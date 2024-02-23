<?php
namespace Jet_Engine\Query_Builder\Queries;

use Jet_Engine\Query_Builder\Manager;

class Posts_Query extends Base_Query {

	use Traits\Meta_Query_Trait;
	use Traits\Tax_Query_Trait;
	use Traits\Date_Query_Trait;

	public $current_wp_query = null;

	/**
	 * Returns queries items
	 *
	 * @return [type] [description]
	 */
	public function _get_items() {

		$current_query = $this->get_current_wp_query();

		$result = array();

		if ( $current_query ) {
			$result = $current_query->posts;
		}

		return $result;

	}

	/**
	 * Returns current query arguments
	 *
	 * @return array
	 */
	public function get_query_args() {

		if ( null === $this->final_query ) {
			$this->setup_query();
		}

		$args = $this->final_query;

		if ( empty( $args['post_type'] ) ) {
			$args['post_type'] = 'any';
		}

		if ( ! empty( $args['meta_query'] ) ) {
			$args['meta_query'] = $this->prepare_meta_query_args( $args );
		}

		if ( ! empty( $args['tax_query'] ) ) {

			$raw = $args['tax_query'];
			$args['tax_query'] = array();

			$custom_tax_query = array();

			if ( ! empty( $args['tax_query_relation'] ) ) {
				$args['tax_query']['relation'] = $args['tax_query_relation'];
			}

			foreach ( $raw as $query_row ) {

				// 'exclude_children' => true  is replaced to 'include_children' => false
				// 'exclude_children' => false is replaced to 'include_children' => true
				if ( isset( $query_row['exclude_children'] ) ) {
					$query_row['include_children'] = ! $query_row['exclude_children'];
					unset( $query_row['exclude_children'] );
				}

				if ( empty( $query_row['operator'] ) || in_array( $query_row['operator'], array( 'IN', 'NOT IN' ) ) ) {
					if ( ! empty( $query_row['terms'] ) && ! is_array( $query_row['terms'] ) ) {
						$query_row['terms'] = $this->explode_string( $query_row['terms'] );
					}
				}

				if ( empty( $query_row['terms'] )
					&& ( empty( $query_row['operator'] ) || ! in_array( $query_row['operator'], array( 'NOT EXISTS', 'EXISTS' ) ) )
				) {
					continue;
				}

				if ( ! empty( $query_row['custom'] ) ) {
					unset( $query_row['custom'] );
					$custom_tax_query[] = $query_row;
					continue;
				}

				$args['tax_query'][] = $query_row;
			}

			if ( ! empty( $custom_tax_query ) ) {

				if ( ! empty( $args['tax_query_relation'] ) && 'or' === $args['tax_query_relation'] ) {
					$args['tax_query'] = array_merge( array( $args['tax_query'] ), $custom_tax_query );
				} else {
					$args['tax_query'] = array_merge( $args['tax_query'], $custom_tax_query );
				}

			}

		}

		if ( ! empty( $args['date_query'] ) ) {
			$args['date_query'] = $this->prepare_date_query_args( $args );
		}

		if ( ! empty( $args['orderby'] ) ) {

			$raw = $args['orderby'];
			$args['orderby'] = array();

			foreach ( $raw as $query_row ) {

				if ( empty( $query_row ) ) {
					continue;
				}

				if ( empty( $query_row['orderby'] ) ) {
					continue;
				}

				$order = isset( $query_row['order'] ) ? $query_row['order'] : '';

				if ( 'meta_clause' !== $query_row['orderby'] && isset( $args['orderby'][ $query_row['orderby'] ] ) ) {
					continue;
				}

				switch ( $query_row['orderby'] ) {
					case 'meta_clause':

						$clause_name = ! empty( $query_row['order_meta_clause'] ) ? $query_row['order_meta_clause'] : false;

						if ( $clause_name && isset( $args['orderby'][ $clause_name ] ) ) {
							break;
						}

						if ( $clause_name ) {
							$args['orderby'][ $clause_name ] = $order;
						}

						break;

					case 'meta_value_num':
					case 'meta_value':
						$args['orderby'][ $query_row['orderby'] ] = $order;

						if ( isset( $query_row['meta_key'] ) ) {
							$args['meta_key'] = $query_row['meta_key'];
						}

						break;

					case 'rand':

						$rand = sprintf( 'RAND(%s)', rand() );
						$args['orderby'][ $rand ] = $order;

						break;

					default:
						$args['orderby'][ $query_row['orderby'] ] = $order;
						break;
				}

			}

		} elseif ( isset( $args['orderby'] ) ) {
			unset( $args['orderby'] );
		}

		if ( empty( $args['offset'] ) ) {
			unset( $args['offset'] );
		}

		if ( isset( $args['comment_count_value'] ) && '' !== $args['comment_count_value'] ) {

			$value = absint( $args['comment_count_value'] );
			unset( $args['comment_count_value'] );

			if ( ! empty( $args['comment_count_compare'] ) ) {
				$args['comment_count'] = array(
					'value'   => $value,
					'compare' => $args['comment_count_compare'],
				);
			} else {
				$args['comment_count'] = $value;
			}

		}

		return apply_filters( 'jet-engine/query-builder/types/posts-query/args', $args, $this );

	}

	/**
	 * Returns WP Query object for current query
	 *
	 * @return WP_Query
	 */
	public function get_current_wp_query() {

		if ( null !== $this->current_wp_query ) {
			return $this->current_wp_query;
		}

		$this->current_wp_query = new \WP_Query( $this->get_query_args() );
		$this->current_wp_query = apply_filters( 'jet-engine/query-builder/types/posts-query/wp-query', $this->current_wp_query, $this );

		return $this->current_wp_query;

	}

	public function get_current_items_page() {

		$query = $this->get_current_wp_query();
		$page  = ! empty( $this->final_query['paged'] ) ? $this->final_query['paged'] : false;

		if ( ! $page && ! empty( $this->final_query['page'] ) ) {
			$page = $this->final_query['page'];
		}

		if ( ! $page && ! empty( $this->final_query['page'] ) ) {
			$page = $this->final_query['page'];
		}

		if ( ! $page && ! empty( $query->query_var['paged'] ) ) {
			$page = $query->query_var['paged'];
		}

		if ( ! $page ) {
			$page = 1;
		}

		return $page;

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

		$query = $this->get_current_wp_query();

		$this->update_query_cache( $query->found_posts, 'count' );

		return $query->found_posts;

	}

	/**
	 * Returns count of the items visible per single listing grid loop/page
	 * @return [type] [description]
	 */
	public function get_items_per_page() {
		$query = $this->get_current_wp_query();
		return $query->query_vars['posts_per_page'];
	}

	/**
	 * Returns queried items count per page
	 *
	 * @return [type] [description]
	 */
	public function get_items_page_count() {
		$query = $this->get_current_wp_query();
		return $query->post_count;
	}

	/**
	 * Returns queried items pages count
	 *
	 * @return [type] [description]
	 */
	public function get_items_pages_count() {
		$query = $this->get_current_wp_query();
		return $query->max_num_pages;
	}

	public function set_filtered_prop( $prop = '', $value = null ) {

		switch ( $prop ) {

			case '_page':
				$this->final_query['paged'] = $value;
				$this->final_query['page']  = $value;
				break;

			case 'orderby':
			case 'order':
			case 'meta_key':
				$this->set_filtered_order( $prop, $value );
				break;

			case 'meta_query':
				$this->replace_meta_query_row( $value );
				break;

			case 'tax_query':
				$this->replace_tax_query_row( $value );
				break;

			case 'post__in':

				if ( ! empty( $this->final_query['post__in'] ) ) {
					$this->final_query['post__in'] = array_intersect( $this->final_query['post__in'], $value );

					if ( empty( $this->final_query['post__in'] ) ) {
						$this->final_query['post__in'] = array( PHP_INT_MAX );
					}

				} else {
					$this->final_query['post__in'] = $value;
				}

				break;

			/*
			 * This code not needed anymore, because the `post__not_in` prop need merged.
			case 'post__not_in':

				if ( ! empty( $this->final_query['post__not_in'] ) ) {
					$this->final_query['post__not_in'] = array_intersect( $this->final_query['post__not_in'], $value );

					if ( empty( $this->final_query['post__not_in'] ) ) {
						$this->final_query['post__not_in'] = array( PHP_INT_MAX );
					}

				} else {
					$this->final_query['post__not_in'] = $value;
				}

				break;
			*/

			case 'post_type':
				$this->final_query['post_type'] = $value;
				break;

			case 'suppress_filters':
				$this->final_query['suppress_filters'] = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
				break;

			default:
				$this->merge_default_props( $prop, $value );
				break;
		}

	}

	public function set_filtered_order( $key, $value ) {

		if ( empty( $this->final_query['orderby'] ) ) {
			$this->final_query['orderby'] = array();
		}

		if ( ! isset( $this->final_query['orderby']['custom'] ) ) {
			$this->final_query['orderby'] = array( 'custom' => array() ) + $this->final_query['orderby'];
		}

		if ( 'orderby' === $key && is_array( $value ) ) {
			$prepared = array();
			$index    = 0;

			foreach ( $value as $orderby => $order ) {
				$prepared[ 'custom_' . $index ] = array(
					'orderby' => $orderby,
					'order'   => $order,
				);

				$index++;
			}

			$this->final_query['orderby'] = $prepared + $this->final_query['orderby'];

		} else {
			$this->final_query['orderby']['custom'][ $key ] = $value;
		}

	}

	/**
	 * Adds date range query arguments to given query parameters.
	 * Required to allow ech query to ensure compatibility with Dynamic Calendar
	 * 
	 * @param array $args [description]
	 */
	public function add_date_range_args( $args = array(), $dates_range = array(), $settings = array() ) {

		$group_by = $settings['group_by'];

		switch ( $group_by ) {

			case 'post_date':
			case 'post_mod':

				if ( 'post_date' === $group_by ) {
					$db_column = 'post_date';
				} else {
					$db_column = 'post_modified';
				}

				if ( isset( $args['date_query'] ) ) {
					$date_query = $args['date_query'];
				} else {
					$date_query = array();
				}

				$date_query = array_merge( $date_query, array(
					array(
						'column'    => $db_column,
						'after'     => date( 'Y-m-d', $dates_range['start'] ),
						'before'    => date( 'Y-m-d', $dates_range['end'] ),
						'inclusive' => true,
					),
				) );

				$args['date_query'] = $date_query;

				break;

			case 'meta_date':

				$args['meta_query'] = $this->get_dates_range_meta_query( $args, $dates_range, $settings );

				break;

		}

		$args['posts_per_page'] = -1;
		$args['ignore_sticky_posts'] = true;

		return $args;

	}

	/**
	 * Array of arguments where string should be exploded into array
	 *
	 * @return [type] [description]
	 */
	public function get_args_to_explode() {
		return array(
			'post_parent__in',
			'post_parent__not_in',
			'post__in',
			'post__not_in',
			'post_name__in',
			'author__in',
			'author__not_in',
		);
	}

	public function reset_query() {
		$this->current_wp_query = null;
	}

}
