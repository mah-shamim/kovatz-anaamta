<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Listings;

use Jet_Engine\Modules\Rest_API_Listings\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Query {

	public $source;
	public $items = array();

	/**
	 * Constructor for the class
	 */
	public function __construct( $source ) {

		$this->source = $source;

		add_filter(
			'jet-engine/listing/grid/query/' . $this->source,
			array( $this, 'query_items' ), 10, 3
		);

		add_filter(
			'jet-engine/listings/data/object-vars',
			array( $this, 'prepare_object_vars' ), 10
		);

		add_action( 'jet-engine/listings/frontend/reset-data', function( $data ) {
			if ( $this->source === $data->get_listing_source() ) {
				wp_reset_postdata();
			}
		} );

	}

	/**
	 * Prepare appintmnet variables
	 */
	public function prepare_object_vars( $vars ) {

		if ( isset( $vars['is_rest_api_endpoint'] ) ) {

			$new_vars = array();

			foreach ( $vars as $key => $value ) {
				$new_vars[ 'rest_api__' . $key ] = $value;
			}

			$vars = array_merge( $vars, $new_vars );
		}

		return $vars;

	}

	public function prepare_query_args( $query = '' ) {

		if ( empty( $query ) ) {
			return false;
		}

		$query = preg_split( "/\\r\\n|\\r|\\n/", $query );
		$prepared_query = array();

		foreach ( $query as $row ) {

			$row_data = preg_split( "/=/", $row, 2 );

			if ( empty( $row_data ) ) {
				continue;
			}

			$prepared_query[ $row_data[0] ] = isset( $row_data[1] ) ? urlencode( $row_data[1] ) : '';
		}

		return $prepared_query;

	}

	public function query_items( $query, $settings, $widget ) {

		$widget->query_vars['page']    = 1;
		$widget->query_vars['pages']   = 1;
		$widget->query_vars['request'] = false;

		$type = jet_engine()->listings->data->get_listing_post_type();

		if ( ! $type ) {
			return $query;
		}

		$endpoint = Module::instance()->settings->get( $type );

		if ( ! $endpoint ) {
			return $query;
		}

		$page  = 1;
		$query_args = isset( $settings['jet_rest_query'] ) ? $settings['jet_rest_query'] : '';
		$query_args = $this->prepare_query_args( $query_args );

		if ( ! empty( $_REQUEST['action'] ) && 'jet_engine_ajax' === $_REQUEST['action'] && isset( $_REQUEST['query'] ) ) {

			$page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;

			if ( ! empty( $_REQUEST['query'] ) ) {
				$query_args = $_REQUEST['query'];
			}

		}

		if ( ! empty( $_REQUEST['action'] ) && 'jet_smart_filters' === $_REQUEST['action'] ) {
			$page = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
		}

		$filtered_query = apply_filters(
			'jet-engine/listing/grid/posts-query-args',
			array(),
			$widget,
			$settings
		);

		$query_args = $this->do_macros_in_args( $query_args );

		if ( ! empty( $filtered_query['jet_smart_filters'] ) && ! empty( $filtered_query['meta_query'] ) ) {
			foreach ( $filtered_query['meta_query'] as $row ) {
				$args = $this->add_filter_row( $row, $query_args );
			}
		}
/*
		if ( 0 < $limit ) {
			$total = $content_type->db->count( $query_args );
			$widget->query_vars['pages'] = ceil( $total / $limit );

			if ( function_exists( 'jet_smart_filters' ) ) {

				$query_id = ! empty( $settings['_element_id'] ) ? $settings['_element_id'] : false;

				jet_smart_filters()->query->set_props(
					'jet-engine',
					array(
						'found_posts'   => $total,
						'max_num_pages' => $widget->query_vars['pages'],
						'page'          => $page,
					),
					$query_id
				);

			}
		}

		$widget->query_vars['request'] = array(
			'order'  => $order,
			'args'   => $query_args,
			'offset' => $offset,
		);

		if ( 1 < $page ) {
			$offset = $offset + ( $page - 1 ) * $limit;
		}
*/
		$items = Module::instance()->request->set_endpoint( $endpoint )->get_items( $query_args );

		if ( empty( $items ) ) {
			$items = array();
		}

		array_walk( $items, function( &$item ) {
			$item->is_rest_api_endpoint = true;
		} );

		return $items;

	}

	public function do_macros_in_args( $args = array() ) {

		if ( empty( $args ) ) {
			return $args;
		}

		$prepared_args = array();

		foreach ( $args as $key => $arg ) {
			$arg = jet_engine()->listings->macros->do_macros( $arg );
			$prepared_args[ $key ] = $arg;
		}

		return $prepared_args;
	}

	public function add_filter_row( $row, $query ) {

		$row['field']    = $row['key'];
		$row['operator'] = $row['compare'];
		$found           = false;

		unset( $row['key'] );
		unset( $row['compare'] );

		foreach ( $query as $index => $query_row ) {
			if ( $row['field'] === $query_row['field'] ) {
				$query[ $index ] = $row;
				$found = true;
			}
		}

		if ( ! $found ) {
			$query[] = $row;
		}

		return $query;

	}

}
