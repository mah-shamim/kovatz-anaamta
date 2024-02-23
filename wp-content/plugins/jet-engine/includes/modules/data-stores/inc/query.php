<?php
namespace Jet_Engine\Modules\Data_Stores;

class Query {

	public function __construct() {
		add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 10, 3 );
		add_filter( 'jet-engine/listing/grid/query-args',       array( $this, 'add_front_store_query_args' ), 10, 4 );

		add_action( 'jet-engine/query-builder/query/after-query-setup', array( $this, 'setup_front_store_prop' ) );
	}

	public function add_query_args( $args, $render, $settings ) {

		if ( jet_engine()->listings->is_listing_ajax() && ! empty( $_REQUEST['query'] ) ) {
			remove_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 10 );

			if ( ! empty( $_REQUEST['query']['is_front_store'] ) ) {
				$args = $_REQUEST['query'];

				add_filter( 'jet-engine/listing/grid/add-query-data', array( $this, 'add_query_data_trigger' ) );
				unset( $args['is_front_store'] );
			}

		} elseif ( ! empty( $settings['posts_query'] ) ) {

			$store = false;

			foreach ( $settings['posts_query'] as $query_item ) {
				if ( ! empty( $query_item['posts_from_data_store'] ) ) {
					$store = $query_item['posts_from_data_store'];
				}
			}

			if ( $store ) {

				$store_instance = Module::instance()->stores->get_store( $store );

				if ( $store_instance ) {
					if( $store_instance->get_type()->is_front_store() ) {
						$args['post__in'] = array(
							'is-front',
							$store_instance->get_type()->type_id(),
							$store_instance->get_slug(),
						);
					} else {
						$store_posts = $store_instance->get_store();

						if ( empty( $store_posts ) ) {
							$args['post__in'] = array( 'no-posts' );
						} else  {
							$args['post__in'] = $store_instance->get_store();
						}
					}
				}

				add_filter( 'jet-engine/listing/grid/add-query-data', array( $this, 'add_query_data_trigger' ) );
			}
		}

		return $args;

	}

	public function add_front_store_query_args( $args, $widget, $settings, $query ) {

		if ( jet_engine()->listings->is_listing_ajax() && ! empty( $_REQUEST['query'] ) ) {

			remove_filter( 'jet-engine/listing/grid/query-args', array( $this, 'add_front_store_query_args' ), 10 );

			$use_load_more = ! empty( $settings['use_load_more'] ) ? $settings['use_load_more'] : false;
			$use_load_more = filter_var( $use_load_more, FILTER_VALIDATE_BOOLEAN );

			if ( $use_load_more && ! empty( $_REQUEST['query']['post__in'] ) ) {
				$args['filtered_query']['post__in'] = $_REQUEST['query']['post__in'];
			}

			if ( ! empty( $_REQUEST['query']['is_front_store'] ) ) {
				add_filter( 'jet-engine/listing/grid/add-query-data', array( $this, 'add_query_data_trigger' ) );
				unset( $_REQUEST['query']['is_front_store'] );
			}

			return $args;
		}

		if ( empty( $query->current_wp_query ) ) {
			return $args;
		}

		$check_fields = array(
			'post__in',
		);

		$has_front_store = false;

		foreach ( $check_fields as $check_field ) {

			$value = $query->current_wp_query->get( $check_field );

			if ( empty( $value ) || ! is_array( $value ) ) {
				continue;
			}

			if ( isset( $value[0] ) && 'is-front' !== $value[0] ) {
				continue;
			}

			$args[ $check_field ] = $value;

			$has_front_store = true;
		}

		if ( $has_front_store ) {
			add_filter( 'jet-engine/listing/grid/add-query-data', array( $this, 'add_query_data_trigger' ) );
		}

		return $args;
	}

	public function setup_front_store_prop( $query ) {

		if ( ! jet_engine()->listings->is_listing_ajax() ) {
			return;
		}

		if ( empty( $_REQUEST['query'] ) ) {
			return;
		}

		if ( empty( $_REQUEST['query']['is_front_store'] ) ) {
			return;
		}

		if ( ! empty( $_REQUEST['query']['post__in'] ) ) {
			$query->final_query['post__in'] = $_REQUEST['query']['post__in'];
		}

	}

	public function add_query_data_trigger( $res ) {
		$res = true;
		remove_filter( 'jet-engine/listing/grid/add-query-data', array( $this, 'add_query_data_trigger' ) );
		return $res;
	}

}
