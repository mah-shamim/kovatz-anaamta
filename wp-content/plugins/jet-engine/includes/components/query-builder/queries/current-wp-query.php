<?php
namespace Jet_Engine\Query_Builder\Queries;

use Jet_Engine\Query_Builder\Manager;
use Jet_Engine\Query_Builder\Helpers\Posts_Per_Page_Manager;

class Current_WP_Query extends Posts_Query {

	public $current_wp_query = null;

	public function __construct( $args = array() ) {

		parent::__construct( $args );

		if ( ! empty( $this->query['posts_per_page'] ) ) {
			Posts_Per_Page_Manager::instance()->add_items( $this->query['posts_per_page'] );
			unset( $this->query['posts_per_page'] );
		}

	}

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
	 * Rewrite setup_query from parent to ensure all query varaibles is set
	 * 
	 * @return void
	 */
	public function setup_query() {
		
		parent::setup_query();
		$current_query = $this->get_current_wp_query();

		if ( $current_query ) {
			$query_args = $current_query->query;

			if ( empty( $query_args ) ) {
				$query_args = array();
			}

			// ensure we have set obvious post_type for the query
			if ( empty( $query_args['post_type'] ) ) {
				
				if ( $current_query->is_home()
					|| $current_query->is_category()
					|| $current_query->is_tag() ) {
					$query_args['post_type'] = 'post';
				} elseif ( $current_query->is_tax() && ! empty( $current_query->query_vars['taxonomy'] ) ) {
					if ( $tax = get_taxonomy( $current_query->query_vars['taxonomy'] ) ) {
						$query_args['post_type'] = $tax->object_type;
					}
				}
				
			}
			
			$this->final_query = array_merge( $this->final_query, $query_args );
		}

	}

	/**
	 * Returns WP Query object for current query
	 * Warning! Never use $this->setup_query() inside this method to avoid infinity looping
	 * 
	 * @return WP_Query
	 */
	public function get_current_wp_query() {

		// maybe re-query filtered data
		if ( $this->current_wp_query && false === $this->current_wp_query->posts ) {
			$this->requery_posts();
		}

		if ( null !== $this->current_wp_query ) {
			return $this->current_wp_query;
		}

		global $wp_query;
		$this->current_wp_query = $wp_query;

		return $this->current_wp_query;

	}

	public function requery_posts() {
		
		global $wp_query;
		
		foreach ( $this->final_query as $key => $value ) {
			switch ( $key ) {
				case 'meta_query':
				case 'tax_query':

					$current = $wp_query->get( $key );

					if ( ! empty( $current ) ) {
						$value = array_merge( $value, $current );
					}

					$wp_query->set( $key, $value );
					break;

				default:

					$wp_query->set( $key, $value );
					break;
			}
		}

		$wp_query->get_posts();
		$this->current_wp_query = null;

	}

	public function get_current_items_page() {

		$query = $this->get_current_wp_query();
		$page  = ! empty( $query->query_vars['paged'] ) ? $query->query_vars['paged'] : 1;

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

		$this->get_current_wp_query()->posts = false;

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

}
