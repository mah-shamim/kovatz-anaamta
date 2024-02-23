<?php
/**
 * Taxonomy API: WP_Tax_Query class
 *
 * @package WordPress
 * @subpackage Taxonomy
 * @since 4.4.0
 */

/**
 * Core class used to implement taxonomy queries for the Taxonomy API.
 *
 * Used for generating SQL clauses that filter a primary query according to object
 * taxonomy terms.
 *
 * WP_Tax_Query is a helper that allows primary query classes, such as WP_Query, to filter
 * their results by object metadata, by generating `JOIN` and `WHERE` subclauses to be
 * attached to the primary SQL query string.
 *
 * @since 3.1.0
 */
class Jet_Search_Tax_Query extends WP_Tax_Query {

	/**
	 * Generate SQL clauses to be appended to a main query.
	 *
	 * Called by the public WP_Tax_Query::get_sql(), this method
	 * is abstracted out to maintain parity with the other Query classes.
	 *
	 * @since 4.1.0
	 *
	 * @return string[] {
	 *     Array containing JOIN and WHERE SQL clauses to append to the main query.
	 *
	 *     @type string $join  SQL fragment to append to the main JOIN clause.
	 *     @type string $where SQL fragment to append to the main WHERE clause.
	 * }
	 */

	public $terms_ids = '';
	public $include_terms_ids = '';
	public $exclude_terms_ids = '';
	public $exclude_posts_ids = '';

	/**
	 * Standard response when the query should not return any rows.
	 *
	 * @since 3.2.0
	 * @var string
	 */
	private static $no_results = array(
		'join'  => array( '' ),
		'where' => array( '0 = 1' ),
	);

	protected function get_sql_clauses() {
		/*
		 * $queries are passed by reference to get_sql_for_query() for recursion.
		 * To keep $this->queries unaltered, pass a copy.
		 */
		$queries = $this->queries;
		$sql     = $this->get_sql_for_query( $queries );

		if ( ! empty( $sql['where'] ) && '' === $this->terms_ids && '' === $this->exclude_terms_ids && '' === $this->exclude_posts_ids ) {
			$sql['where'] = ' OR (' . $sql['where'] . ' AND (' . $this->primary_table . '.post_status = \'publish\'))';
		} else {
			$terms_ids         = '' === $this->terms_ids ? '' : $this->terms_ids;
			$exclude_terms_ids = '' === $this->exclude_terms_ids ? '' : $this->exclude_terms_ids;
			$exclude_posts_ids = '' === $this->exclude_posts_ids ? '' : $this->exclude_posts_ids;

			$sql['where'] = ' OR ( ( ' . $sql['where'] . $terms_ids . $exclude_terms_ids . $exclude_posts_ids . ' ) AND (' . $this->primary_table . '.post_status = \'publish\'))';
		}

		return $sql;
	}

	/**
	 * Generate SQL JOIN and WHERE clauses for a "first-order" query clause.
	 *
	 * @since 4.1.0
	 *
	 * @global wpdb $wpdb The WordPress database abstraction object.
	 *
	 * @param array $clause       Query clause (passed by reference).
	 * @param array $parent_query Parent query array.
	 * @return string[] {
	 *     Array containing JOIN and WHERE SQL clauses to append to a first-order query.
	 *
	 *     @type string $join  SQL fragment to append to the main JOIN clause.
	 *     @type string $where SQL fragment to append to the main WHERE clause.
	 * }
	 */
	public function get_sql_for_clause( &$clause, $parent_query ) {
		global $wpdb;

		$sql = array(
			'where' => array(),
			'join'  => array(),
		);

		$join  = '';
		$where = '';

		if ( ! isset( $clause['req'] ) && ! isset( $clause['_tax_type'] ) ) {
			$clause['terms'] = array_unique( (array) $clause['terms'] );

			if ( is_taxonomy_hierarchical( $clause['taxonomy'] ) && $clause['include_children'] ) {

				$this->transform_query( $clause, 'term_id' );

				$children = array();

				foreach ( $clause['terms'] as $term ) {
					$children   = array_merge( $children, get_term_children( $term, $clause['taxonomy'] ) );
					$children[] = $term;
				}

				$clause['terms'] = $children;

			} else {
				$this->transform_query( $clause, 'term_taxonomy_id' );
			}

			if ( is_wp_error( $clause ) ) {
				return self::$no_results;
			}

			$terms    = $clause['terms'];
			$operator = strtoupper( $clause['operator'] );

			if ( empty( $terms ) ) {
				return self::$no_results;
			}
		} else {
			$terms    = $clause['terms'];
			$operator = strtoupper( $clause['operator'] );
		}

		$terms = array_unique( $terms );
		$terms = implode( ',', $terms );

		/*
		 * Before creating another table join, see if this clause has a
		 * sibling with an existing join that can be shared.
		 */
		$alias = 'ajt';
		//$i     = count( $this->table_aliases );
		$i     = rand( 0, 999 );
		$alias = $i ? $alias . $i : $alias;

		// Store the alias as part of a flat array to build future iterators.
		$this->table_aliases[] = $alias;

		// Store the alias with this clause, so later siblings can use it.
		$clause['alias'] = $alias;

		$join .= " LEFT JOIN $wpdb->term_relationships";
		$join .= " AS $alias";
		$join .= " ON ($this->primary_table.$this->primary_id_column = $alias.object_id)";

		$tax_type = isset( $clause['_tax_type'] ) ? $clause['_tax_type'] : '';

		if ( '' != $tax_type ) {
			$ids = implode(', ', $clause['_ids']);

			switch ($tax_type) {
				case 'exclude_terms':
					$this->exclude_terms_ids = " AND
					(
						$wpdb->posts.ID NOT IN (
									SELECT object_id
									FROM $wpdb->term_relationships
									WHERE term_taxonomy_id IN ($ids)
						)
					) ";
					break;
				case 'include_terms':
					$this->include_terms_ids = " AND $wpdb->term_relationships.term_taxonomy_id IN ($ids)";
					break;
				case 'exclude_posts':
					$this->exclude_posts_ids = " AND $wpdb->posts.ID NOT IN ($ids)";
					break;
			}
		} else if ( isset( $clause['req'] ) ) {
			$this->terms_ids = " AND $alias.term_taxonomy_id $operator ($terms)";
		} else {
			$where = "$alias.term_taxonomy_id $operator ($terms)";
		}

		$sql['join'][]  = $join;
		$sql['where'][] = $where;

		return $sql;
	}

	/**
	 * Transforms a single query, from one field to another.
	 *
	 * Operates on the `$query` object by reference. In the case of error,
	 * `$query` is converted to a WP_Error object.
	 *
	 * @since 3.2.0
	 *
	 * @global wpdb $wpdb The WordPress database abstraction object.
	 *
	 * @param array  $query           The single query. Passed by reference.
	 * @param string $resulting_field The resulting field. Accepts 'slug', 'name', 'term_taxonomy_id',
	 *                                or 'term_id'. Default 'term_id'.
	 */
	public function transform_query( &$query, $resulting_field ) {

		if ( empty( $query['terms'] ) ) {
			return;
		}

		if ( $query['field'] == $resulting_field ) {
			return;
		}

		$resulting_field = sanitize_key( $resulting_field );

		// Empty 'terms' always results in a null transformation.
		$terms = array_filter( $query['terms'] );
		if ( empty( $terms ) ) {
			$query['terms'] = array();
			$query['field'] = $resulting_field;
			return;
		}

		/**
		 * Incorrect number of terms displayed, the 'number' argument has been removed.
		 *
		 * @since  3.1.3
		 *
		 */

		$args = array(
			'get'                    => 'all',
			//'number'                 => 0,
			'taxonomy'               => $query['taxonomy'],
			'update_term_meta_cache' => false,
		);

		$args['search'] = is_array( $terms ) ? $terms[0] : $terms;

		// if ( ! is_taxonomy_hierarchical( $query['taxonomy'] ) ) {
		// 	$args['number'] = count( $terms );
		// }

		$term_query = new WP_Term_Query();

		$term_list  = $term_query->query( $args );

		if ( is_wp_error( $term_list ) ) {
			$query = $term_list;
			return;
		}

		if ( 'AND' === $query['operator'] && count( $term_list ) < count( $query['terms'] ) ) {
			$query = new WP_Error( 'inexistent_terms', __( 'Inexistent terms.' ) );
			return;
		}

		$query['terms'] = wp_list_pluck( $term_list, $resulting_field );
		$query['field'] = $resulting_field;
	}
}
