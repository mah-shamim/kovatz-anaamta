<?php
namespace Jet_Engine\Query_Builder\Queries;

use Jet_Engine\Query_Builder\Manager;

class SQL_Query extends Base_Query {

	public $current_query = null;

	/**
	 * Returns queries items
	 *
	 * @return [type] [description]
	 */
	public function _get_items() {

		$sql    = $this->build_sql_query();
		$result = $this->wpdb()->get_results( $sql );

		$cast_to_class = ! empty( $this->query['cast_object_to'] ) ? $this->query['cast_object_to'] : false;

		if ( $cast_to_class && ( class_exists( $cast_to_class ) || function_exists( $cast_to_class ) ) ) {
			$result = array_map( function( $item ) use ( $cast_to_class ) {
				
				if ( class_exists( $cast_to_class ) ) {
					return new $cast_to_class( $item );
				} elseif ( function_exists( $cast_to_class ) ) {
					return call_user_func( $cast_to_class, $item );
				} else {
					return $item;
				}
				
			}, $result );
		} else {
			$start_index = $this->get_start_item_index_on_page() - 1;

			$result = array_map( function( $item, $index ) use ( $start_index ) {
				$item->sql_query_item_id = $this->id . '-' . ( $start_index + $index );
				return $item;
			}, $result, array_keys( $result ) );
		}

		return $result;

	}

	public function get_current_items_page() {

		if ( empty( $this->final_query['_page'] ) ) {
			return 1;
		} else {
			return absint( $this->final_query['_page'] );
		}

	}

	/**
	 * Returns total found items count
	 *
	 * @return [type] [description]
	 */
	public function get_items_total_count() {

		$cached = $this->get_cached_data( 'count' );

		if ( false !== $cached ) {
			return $cached;
		}

		$this->setup_query();

		$sql = $this->build_sql_query( true );

		if ( 'nosql' === $sql ) {
			$result = count( $this->get_items() );
		} else {
			$result = $this->wpdb()->get_var( $sql );
		}

		$this->update_query_cache( $result, 'count' );

		return $result;

	}

	/**
	 * Returns count of the items visible per single listing grid loop/page
	 * @return [type] [description]
	 */
	public function get_items_per_page() {

		$this->setup_query();
		$limit = 0;

		if ( ! empty( $this->final_query['limit_per_page'] ) ) {
			$limit = absint( $this->final_query['limit_per_page'] );
		} elseif ( ! empty( $this->final_query['limit'] ) ) {
			$limit = absint( $this->final_query['limit'] );
		}

		return $limit;
	}

	/**
	 * Returns queried items count per page
	 *
	 * @return [type] [description]
	 */
	public function get_items_page_count() {
		$result   = $this->get_items_total_count();
		$per_page = $this->get_items_per_page();

		if ( $per_page < $result ) {

			$page  = $this->get_current_items_page();
			$pages = $this->get_items_pages_count();

			if ( $page < $pages ) {
				$result = $per_page;
			} elseif ( $page == $pages ) {
				$offset = ( $page - 1 ) * $per_page;
				$result = $result - $offset;
			}

		}

		return $result;
	}

	public function set_filtered_prop( $prop = '', $value = null ) {

		switch ( $prop ) {

			case '_page':

				$page = absint( $value );

				if ( 0 < $page ) {
					$this->final_query['_page']  = $page;
				}

				break;

			case 'orderby':
			case 'order':
			case 'meta_key':

				$key = $prop;

				if ( 'orderby' === $prop ) {
					$key = 'type';
					$value = ( in_array( $value, array( 'meta_key', 'meta_value' ) ) ) ? 'CHAR' : 'DECIMAL';
				} elseif ( 'meta_key' === $prop ) {
					$key = 'orderby';
				}

				$this->set_filtered_order( $key, $value );
				break;

			case 'meta_query':

				foreach ( $value as $row ) {

					$this->update_where_row( $this->prepare_where_row( $row ) );

				}

				break;
		}

	}

	/**
	 * Prepare where arguments row.
	 *
	 * @param  array $row
	 * @return array
	 */
	public function prepare_where_row( $row ) {

		if ( ! empty( $row['relation'] ) ) {

			$prepared_row = array(
				'relation' => $row['relation'],
			);

			unset( $row['relation'] );

			foreach ( $row as $inner_row ) {
				$prepared_row[] = $this->prepare_where_row( $inner_row );
			}

		} else {
			$prepared_row = array(
				'column'  => ! empty( $row['key'] ) ? $row['key'] : false,
				'compare' => ! empty( $row['compare'] ) ? $row['compare'] : '=',
				'value'   => ! empty( $row['value'] ) ? $row['value'] : '',
				'type'    => ! empty( $row['type'] ) ? $row['type'] : false,
			);
		}

		return $prepared_row;
	}

	public function set_filtered_order( $key, $value ) {

		if ( empty( $this->final_query['orderby'] ) ) {
			$this->final_query['orderby'] = array();
		}

		if ( ! isset( $this->final_query['orderby']['custom'] ) ) {
			$this->final_query['orderby'] = array_merge( array( 'custom' => array() ), $this->final_query['orderby'] );
		}

		$this->final_query['orderby']['custom'][ $key ] = $value;

	}

	public function update_where_row( $row ) {

		if ( empty( $this->final_query['where'] ) ) {
			$this->final_query['where'] = array();
		}

		foreach ( $this->final_query['where'] as $index => $existing_row ) {
			if ( isset( $existing_row['column'] )
				 && isset( $row['column'] )
				 && $existing_row['column'] === $row['column']
				 && $existing_row['compare'] === $row['compare']
			) {
				$this->final_query['where'][ $index ] = $row;
				return;
			}
		}

		$this->final_query['where'][] = $row;

	}

	/**
	 * Returns queried items pages count
	 *
	 * @return [type] [description]
	 */
	public function get_items_pages_count() {

		$per_page = $this->get_items_per_page();
		$total    = $this->get_items_total_count();

		if ( ! $per_page || ! $total ) {
			return 1;
		} else {
			return ceil( $total / $per_page );
		}

	}

	public function wpdb() {
		global $wpdb;
		return $wpdb;
	}

	public function get_var( $column = null, $function = null, $decimal_count = 0 ) {

		$this->setup_query();
		$sql = $this->build_sql_query();

		$quote = '';

		if ( $this->is_grouped() ) {
			$quote = '`';
		}

		if ( $function ) {
			if ( 'COUNT' === $function ) {
				$select = sprintf( '%1$s( %3$s%2$s%3$s )', $function, $column, $quote );
			} else {
				$select = sprintf( '%1$s( CAST( %4$s%2$s%4$s AS DECIMAL( 10, %3$s ) ) )', $function, $column, $decimal_count, $quote );
			}
		} else {
			$select = sprintf( '%2$s%1$s%2$s', $column, $quote );
		}

		$advanced_query = $this->get_advanced_query();

		if ( $advanced_query ) {
			$sql = rtrim( $sql, ';' );
			$sql = 'SELECT ' . $select . ' FROM ( ' . $sql . ' ) AS advanced_query_result;';
			return round( $this->wpdb()->get_var( $sql ), $decimal_count );
		}

		if ( $this->is_grouped() ) {
			$sql = $this->wrap_grouped_query( $select, $sql );
		} else {
			$sql = preg_replace( '/SELECT (.+?) FROM/', 'SELECT ' . $select . ' FROM', $sql );
		}

		return round( $this->wpdb()->get_var( $sql ), $decimal_count );

	}

	public function sanitize_sql( $query ) {

		/**
		 * ensure query is not stacked
		 * temporary disabled because can return false positive
		 *
		 * $query = explode( ';', $query );
		 * $query = $query[0];
		 */

		// Remove the / * * / style comments
		$query = preg_replace( '%(/\*)(.*?)(\*/)%s',"",$query );
		// Remove the — style comments
		$query = preg_replace( '%(–).*%',"",$query );

		$query = stripslashes( $query );

		return $query;

	}

	public function is_query_safe( $query ) {

		$query = trim( $query );

		// Should start from SELECT word
		if ( 0 !== strpos( $query, 'SELECT' ) ) {
			return false;
		}

		// Should not contain any dangerous SQL commands
		$disallowed = array(
			'DROP',
			'TRUNCATE',
			'DELETE',
			'COMMIT',
			'GRANT ALL',
			'CREATE',
			'REPLACE',
			'INSERT',
			'ALTER',
			'ADD ',
			'UPDATE',
		);

		foreach ( $disallowed as $command ) {
			if ( false !== strpos( $query, $command ) ) {
				return false;
			}
		}

		return true;

	}

	public function get_advanced_query( $is_count = false ) {

		if ( empty( $this->final_query['advanced_mode'] ) ) {
			return false;
		}

		if ( $is_count ) {
			$query = ! empty( $this->final_query['count_query'] ) ? $this->final_query['count_query'] : false;

			if ( ! $query ) {
				return 'nosql';
			}

		} else {
			$query = $this->final_query['manual_query'];
		}

		if ( ! $query ) {
			return false;
		}

		$query = $this->sanitize_sql( $query );

		if ( ! $this->is_query_safe( $query ) ) {
			return false;
		}

		$query = str_replace( '{prefix}', $this->wpdb()->prefix, $query );

		return $this->apply_macros( $query );

	}

	public function build_sql_query( $is_count = false ) {

		// Return advanced query early if set
		$advanced_query = $this->get_advanced_query( $is_count );

		if ( $advanced_query ) {
			return $advanced_query;
		}

		$prefix = $this->wpdb()->prefix;

		$select_query = "SELECT ";

		if ( $is_count && ! $this->is_grouped() && empty( $this->final_query['limit'] ) ) {
			$select_query .= " COUNT(*) ";
		} else {

			$implode = array();

			if ( ! empty( $this->final_query['include_columns'] ) ) {
				foreach ( $this->final_query['include_columns'] as $col ) {
					$implode[] = $col . " AS '" . $col . "'";
				}
			}

			if ( ! empty( $this->final_query['include_calc'] ) && ! empty( $this->final_query['calc_cols'] ) ) {
				foreach ( $this->final_query['calc_cols'] as $col ) {
					if ( 'custom' === $col['function'] ) {
						$custom_col      = ! empty( $col['custom_col'] ) ? $col['custom_col'] : '%1$s';
						$prepared_col    = str_replace( '%1$s', $col['column'], $custom_col );
						$prepared_col    = jet_engine()->listings->macros->do_macros( $prepared_col );
						$prepared_col_as = sprintf( '%1$s(%2$s)', $col['function'], $col['column'] );
					} else {
						$prepared_col    = sprintf( '%1$s(%2$s)', $col['function'], $col['column'] );
						$prepared_col_as = $prepared_col;
					}
					
					$implode[] = $prepared_col . " AS '" . $prepared_col_as . "'";
				}
			}

			if ( ! empty( $implode ) ) {
				$select_query .= implode( ', ', $implode ) . " ";
			} else {
				$select_query .= "* ";
			}

		}

		if ( null === $this->current_query ) {

			$raw_table      = $this->final_query['table'];
			$prefixed_table = $prefix . $raw_table;
			$current_query  = "";

			$tables = array(
				$raw_table => 1
			);

			$current_query .= "FROM $prefixed_table AS $raw_table ";

			if ( ! empty( $this->final_query['use_join'] ) && ! empty( $this->final_query['join_tables'] ) ) {
				foreach ( $this->final_query['join_tables'] as $table ) {

					$type           = $table['type'];
					$raw_join_table = $table['table'];
					$join_table     = $prefix . $table['table'];

					if ( ! empty( $tables[ $raw_join_table ] ) ) {
						$tables[ $raw_join_table ] = $tables[ $raw_join_table ] + 1;
						$as_table = $raw_join_table . $tables[ $raw_join_table ];
					} else {
						$tables[ $raw_join_table ] = 1;
						$as_table = $raw_join_table;
					}

					$base_col    = $table['on_base'];
					$current_col = $table['on_current'];

					if ( false === strpos( $base_col, '.' ) ) {
						$base_col = $raw_table . '.' . $base_col;
					}

					$current_query .= "$type $join_table AS $as_table ON $base_col = $as_table.$current_col ";

				}
			}

			if ( ! empty( $this->final_query['where'] ) ) {

				$where = array();

				foreach ( $this->final_query['where'] as $row ) {
					$where[] = $row;
				}

				$where_relation = 'AND';

				if ( ! empty( $this->final_query['where_relation'] ) && count( $where ) > 1 ) {
					$where_relation = strtoupper( $this->final_query['where_relation'] );
				}

				$current_query .= $this->add_where_args( $where, $where_relation );
			}

			if ( ! empty( $this->final_query['group_results'] ) && ! empty( $this->final_query['group_by'] ) ) {
				$current_query .= " GROUP BY " . $this->final_query['group_by'];
			}

			if ( ! empty( $this->final_query['orderby'] ) ) {

				$orderby        = array();
				$current_query .= " ";

				foreach ( $this->final_query['orderby'] as $row ) {

					if ( empty( $row['orderby'] ) ) {
						continue;
					}

					$row['column'] = $row['orderby'];
					$orderby[] = $row;
				}

				$current_query .= $this->add_order_args( $orderby );
			}

			$this->current_query = $current_query;

		}

		$limit_offset = "";

		if ( ! $is_count ) {
			$limit = $this->get_items_per_page();
		} else {
			$limit = ! empty( $this->final_query['limit'] ) ? absint( $this->final_query['limit'] ) : 0;
		}

		if ( $limit ) {
			$limit_offset .= " LIMIT";
			$offset = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;

			if ( ! $is_count && ! empty( $this->final_query['_page'] ) ) {
				$page    = absint( $this->final_query['_page'] );
				$pages   = $this->get_items_pages_count();
				$_offset = ( $page - 1 ) * $this->get_items_per_page();
				$offset  = $offset + $_offset;

				// Fixed the following issue:
				// The last page has an incorrect number of posts if the `Total Query Limit` number
				// is not a multiple of the `Per Page Limit` number.
				if ( $page == $pages ) {
					$limit = $this->get_items_total_count() - $_offset;
				}
			}

			if ( $offset ) {
				$limit_offset .= " $offset, $limit";
			} else {
				$limit_offset .= " $limit";
			}
		}

		$result = apply_filters(
			'jet-engine/query-builder/build-query/result',
			$select_query . $this->current_query . $limit_offset . ";",
			$this,
			$is_count
		);

		if ( $is_count && ( $this->is_grouped() || ! empty( $this->final_query['limit'] ) ) ) {
			$result = $this->wrap_grouped_query( 'COUNT(*)', $result );
		}

		return $result;

	}

	public function wrap_grouped_query( $select, $query ) {
		$query = rtrim( $query, ';' );
		return "SELECT $select FROM ( $query ) AS grouped;";
	}

	public function is_grouped() {
		return ( ! empty( $this->final_query['group_results'] ) && ! empty( $this->final_query['group_by'] ) );
	}

	/**
	 * Add ordering arguments
	 */
	public function add_order_args( $args = array() ) {

		$query = '';
		$glue  = '';

		foreach ( $args as $arg ) {

			// Sanitize SQL `column name` string to prevent SQL injection.
			// See: https://github.com/Crocoblock/issues-tracker/issues/5251
			$column = \Jet_Engine_Tools::sanitize_sql_orderby( $arg['column'] );
			$type   = ! empty( $arg['type'] ) ? $arg['type'] : 'CHAR';
			$order  = ! empty( $arg['order'] ) ? strtoupper( $arg['order'] ) : 'DESC';
			$order  = in_array( $order, array( 'ASC', 'DESC' ) ) ? $order : 'DESC';

			if ( ! $column ) {
				continue;
			}

			$query .= $glue;

			switch ( $type ) {
				case 'NUMERIC':
				case 'DECIMAL':
					$query .= "CAST( $column as DECIMAL )";
					break;

				case 'CHAR':
					$query .= $column;
					break;

				default:
					$query .= "CAST( $column as $type )";
					break;
			}

			$query .= " ";
			$query .= $order;

			$glue = ", ";
		}

		if ( $query ) {
			$query  = "ORDER BY " . $query;
		}

		return $query;
	}

	/**
	 * Add nested query arguments
	 *
	 * @param  [type]  $key    [description]
	 * @param  [type]  $value  [description]
	 * @param  boolean $format [description]
	 * @return [type]          [description]
	 */
	public function get_sub_query( $key = null, $value = null, $format = false ) {

		$query = '';
		$glue  = '';

		if ( ! $format ) {

			if ( false !== strpos( $key, '!' ) ) {
				$format = '%1$s != \'%2$s\'';
				$key    = ltrim( $key, '!' );
			} else {
				$format = '%1$s = \'%2$s\'';
			}

		}

		foreach ( $value as $child ) {
			$query .= $glue;
			$query .= sprintf( $format, esc_sql( $key ), esc_sql( $child ) );
			$glue   = ' OR ';
		}

		return $query;

	}

	/**
	 * Add where arguments to query
	 *
	 * @param array  $args [description]
	 * @param string $rel  [description]
	 */
	public function add_where_args( $args = array(), $rel = 'AND', $add_where_string = true ) {

		$query      = '';
		$multi_args = false;

		if ( ! empty( $args ) ) {

			if ( $add_where_string ) {
				$query .= ' WHERE ';
			}

			$glue = '';

			if ( count( $args ) > 1 ) {
				$multi_args = true;
			}

			foreach ( $args as $key => $arg ) {

				if ( is_array( $arg ) && isset( $arg['relation'] ) ) {
					$relation = $arg['relation'];

					unset( $arg['relation'] );

					$clause = $this->add_where_args( $arg, $relation, false );

					if ( $clause ) {
						$clause = '( ' . $clause . ' )';
					}

				} else {

					if ( is_array( $arg ) && isset( $arg['column'] ) ) {
						$column  = ! empty( $arg['column'] ) ? $arg['column'] : false;
						$compare = ! empty( $arg['compare'] ) ? $arg['compare'] : '=';
						$value   = ! empty( $arg['value'] ) ? $arg['value'] : '';
						$type    = ! empty( $arg['type'] ) ? $arg['type'] : false;
					} else {
						$column  = $key;
						$compare = '=';
						$value   = $arg;
						$type    = false;
					}

					$clause = $this->prepare_where_clause( $column, $compare, $value, $type );
				}

				if ( $clause ) {
					$query .= $glue;
					$query .= $clause;
					$glue   = ' ' . $rel . ' ';
				}

			}

		}

		return $query;

	}

	/**
	 * Adjust SQL value string according queried argument type
	 *
	 * @param  string  $value [description]
	 * @param  boolean $type  [description]
	 * @return [type]         [description]
	 */
	public function adjust_value_by_type( $value = '', $type = false ) {

		if ( is_array( $value ) ) {
			return false;
		}

		if ( false !== strpos( strtolower( $type ), 'decimal' ) ) {
			$type = 'float';
		}

		switch ( $type ) {
			case 'integer':
				$value = absint( $value );
				break;
			case 'float':
				$value = floatval( $value );
				break;
			case 'timestamp':
				if ( ! \Jet_Engine_Tools::is_valid_timestamp( $value ) ) {
					$value = strtotime( $value );
				}
				$value = absint( $value );
				break;
			case 'date':
				$value = strtotime( $value );
				$value = sprintf( "'%s'", date( $value, 'Y-m-d H:i:s' ) );
				break;
			default:
				$value = sprintf( "'%s'", esc_sql( $value ) );
				break;
		}

		return $value;

	}

	/**
	 * Check if $haystack start from $needle
	 *
	 * @param  [type] $haystack [description]
	 * @param  [type] $needle   [description]
	 * @return [type]           [description]
	 */
	public function starts_with( $haystack, $needle ) {
		$length = strlen( $needle );
		return substr( $haystack, 0, $length ) === $needle;
	}

	/**
	 * Check if $haystack ends with $needle
	 *
	 * @param  [type] $haystack [description]
	 * @param  [type] $needle   [description]
	 * @return [type]           [description]
	 */
	public function ends_with( $haystack, $needle ) {

		$length = strlen( $needle );

		if ( ! $length ) {
			return true;
		}

		return substr( $haystack, -$length ) === $needle;
	}

	/**
	 * Prepare string to use in like or not like operator
	 *
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function prepare_value_for_like_operator( $value ) {
		if ( $this->starts_with( $value, '%' ) || $this->ends_with( $value, '%' ) ) {
			return sprintf( "'%s'", $value );
		} else {
			return sprintf( "'%%%s%%'", esc_sql( $value ) );
		}
	}

	/**
	 * Prepare single query clause by arguments
	 *
	 * @return [type] [description]
	 */
	public function prepare_where_clause( $column = false, $compare = '=', $value = '', $type = false ) {

		if ( ! $column ) {
			return '';
		}

		$format = '%1$s %3$s %2$s';

		$array_operators = array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' );

		if ( ! is_array( $value ) && in_array( $compare, $array_operators ) && false !== strpos( $value, ',' ) ) {
			$value = explode( ',', $value );
			$value = array_map( 'trim', $value );
		}

		if ( is_array( $value ) ) {
			switch ( $compare ) {

				case 'IN':
				case 'NOT IN':

					array_walk( $value, function( &$item ) use ( $type ) {
						$item = $this->adjust_value_by_type( $item, $type );
					} );

					$value = sprintf( '( %s )', implode( ', ', $value ) );

					break;

				case 'BETWEEN':
				case 'NOT BETWEEN':

					$from = isset( $value[0] ) ? $value[0] : 0;
					$to   = isset( $value[1] ) ? $value[1] : $from;

					$from = $this->adjust_value_by_type( $from, $type );
					$to   = $this->adjust_value_by_type( $to, $type );

					$value = sprintf( '%1$s AND %2$s', $from, $to );

					break;

				default:
					$format = '(%2$s)';
					$args   = array();

					foreach ( $value as $val ) {
						$args[] = array(
							'column'  => $column,
							'compare' => $compare,
							'type'    => $type,
							'value'   => $val,
						);
					}

					$value = $this->add_where_args( $args, 'OR', false );
					break;

			}
		} else {

			if ( in_array( $compare, array( 'LIKE', 'NOT LIKE' ) ) ) {
				$value = $this->prepare_value_for_like_operator( $value );
			} else {
				$value = $this->adjust_value_by_type( $value, $type );
			}

			if ( in_array( $compare, array( 'IN', 'BETWEEN' ) ) ) {
				$compare = '=';
			} elseif ( in_array( $compare, array( 'NOT IN', 'NOT BETWEEN' ) ) ) {
				$compare = '!=';
			}

		}

		$result = sprintf( $format, esc_sql( $column ), $value, $compare );

		return $result;

	}

	/**
	 * Get fields list are available for the current instance of this query
	 *
	 * @return [type] [description]
	 */
	public function get_instance_fields() {

		$cols = array();

		if ( ! empty( $this->query['include_columns'] ) && empty( $this->final_query['advanced_mode'] ) ) {
			$cols = $this->query['include_columns'];
		} elseif ( ! empty( $this->query['default_columns'] ) ) {
			$cols = $this->query['default_columns'];
		}

		if ( ! empty( $this->query['include_calc'] ) && ! empty( $this->query['calc_cols'] ) ) {
			foreach ( $this->query['calc_cols'] as $col ) {
				$cols[] = sprintf( '%1$s(%2$s)', $col['function'], $col['column'] );
			}
		}

		$result = array();

		if ( ! empty( $cols ) ) {
			foreach ( $cols as $col ) {
				$result[ $col ] = $col;
			}
		}

		return $result;
	}

	public function get_args_to_dynamic() {
		return array(
			'manual_query',
			'count_query',
		);
	}

	public function reset_query() {
		$this->current_query = null;
	}

	public function before_preview_body() {
		print_r( $this->wpdb()->last_query . "\n\n" );

		if ( $this->wpdb()->last_error ) {
			print_r( esc_html__( 'ERROR:' ) . "\n" );
			print_r( $this->wpdb()->last_error . "\n\n" );
		}
	}

}
