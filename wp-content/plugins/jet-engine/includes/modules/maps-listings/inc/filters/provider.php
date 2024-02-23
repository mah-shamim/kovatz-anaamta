<?php
namespace Jet_Engine\Modules\Maps_Listings\Filters;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Filters_Provider class
 */
class Provider extends \Jet_Smart_Filters_Provider_Base {

	/**
	 * Watch for default query
	 */
	public function __construct() {

		if ( ! jet_smart_filters()->query->is_ajax_filter() ) {
			add_filter('jet-engine/listing/grid/posts-query-args', array( $this, 'store_default_query' ), 0, 2 );
		}

	}

	/**
	 * Store default query args
	 *
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function store_default_query( $args, $widget ) {

		if ( 'jet-engine-maps-listing' !== $widget->get_name() ) {
			return $args;
		}

		$settings = $widget->get_settings();

		if ( empty( $settings['_element_id'] ) ) {
			$query_id = false;
		} else {
			$query_id = $settings['_element_id'];
		}

		if ( isset( $settings['is_archive_template'] ) && 'yes' === $settings['is_archive_template'] ){
			jet_smart_filters()->query->set_props(
				$this->get_id(),
				array(
					'found_posts'   => $args['found_posts'],
					'max_num_pages' => $args['max_num_pages'],
					'page'          => $args['paged'],
				),
				$query_id
			);
		}

		add_filter( 'found_posts', array( $this, 'adjust_offset_pagination' ), 1, 2 );

		jet_smart_filters()->query->store_provider_default_query( $this->get_id(), $args, $query_id );

		jet_smart_filters()->providers->store_provider_settings(
			$this->get_id(),
			$widget->get_required_settings(),
			$query_id
		);

		$args['suppress_filters']  = false;
		$args['jet_smart_filters'] = jet_smart_filters()->query->encode_provider_data(
			$this->get_id(),
			$query_id
		);

		return $args;
	}

	/**
	 * Get provider name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'JetEngine Maps', 'jet-engine' );
	}

	/**
	 * Get provider ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'jet-engine-maps';
	}

	/**
	 * If added unique ID this paramter will determine - search selector inside this ID, or is the same element
	 *
	 * @return bool
	 */
	public function in_depth() {
		return true;
	}

	/**
	 * Check if this providers requires custom renderer on the front-end
	 *
	 * @return [type] [description]
	 */
	public function custom_render() {
		return true;
	}

	/**
	 * Get filtered provider content
	 *
	 * @return string
	 */
	public function ajax_get_content() {

		if ( ! function_exists( 'jet_engine' ) ) {
			return;
		}

		add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 10, 2 );

		add_filter( 'jet-smart-filters/render/ajax/data', array( $this, 'add_new_markers_to_response' ), 9 );

	}

	/**
	 * Adds markers to reposnse
	 *
	 * @param [type] $response [description]
	 */
	public function add_new_markers_to_response( $response ) {

		$settings = jet_smart_filters()->query->get_query_settings();
		$instance = jet_engine()->listings->get_render_instance( 'maps-listing', $settings );

		jet_engine()->listings->data->set_listing_by_id( $settings['lisitng_id'] );

		$instance->setup_listing_props();

		$query       = $instance->get_query( $settings );
		$map_markers = $instance->get_map_markers( $query, $settings, false );

		$response['markers']    = $map_markers;
		$response['pagination'] = jet_smart_filters()->query->get_current_query_props();

		return $response;

	}

	/**
	 * Get provider wrapper selector
	 *
	 * @return string
	 */
	public function get_wrapper_selector() {
		return '.jet-map-listing';
	}

	/**
	 * Add custom settings for AJAX request
	 */
	public function add_settings( $settings, $widget ) {

		if ( 'jet-engine-maps-listing' !== $widget->get_name() ) {
			return $settings;
		}

		return jet_smart_filters()->query->get_query_settings();
	}

	/**
	 * Pass args from reuest to provider
	 */
	public function apply_filters_in_request() {

		$args = jet_smart_filters()->query->get_query_args();

		if ( ! $args ) {
			return;
		}

		add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 10, 2 );

	}

	/**
	 * Updates the arguments based on the offset parameter
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	public function query_maybe_has_offset( $args ){

		if ( isset( $args['offset'] ) ){

			add_filter( 'found_posts', array( $this, 'adjust_offset_pagination' ), 1, 2 );

			if( isset( $args['paged'] ) ){
				$args['offset'] = $args['offset'] + ( ( $args['paged'] - 1 ) * $args['posts_per_page'] );
			}

		}

		return $args;

	}

	/**
	 * Adjusts page number shift
	 *
	 * @param $found_posts
	 * @param $query
	 *
	 * @return mixed
	 */
	function adjust_offset_pagination( $found_posts, $query ) {
		$found_posts = (int) $found_posts;
		$offset      = (int) $query->get( 'offset' );

		if ( $query->get( 'jet_smart_filters' ) && ! empty( $offset ) ){

			$paged = $query->get( 'paged' );
			$posts_per_page = $query->get( 'posts_per_page' );

			if ( 0 < $paged ){
				$offset = $offset - ( ( $paged - 1 ) * $posts_per_page );
			}

			return $found_posts - $offset;

		}

		return $found_posts;

	}

	/**
	 * Add custom query arguments
	 *
	 * @param array $args [description]
	 */
	public function add_query_args( $args, $widget ) {

		if ( 'jet-engine-maps-listing' !== $widget->get_name() ) {
			return $args;
		}

		if ( ! jet_smart_filters()->query->is_ajax_filter() ) {

			$settings = $widget->get_settings();

			if ( empty( $settings['_element_id'] ) ) {
				$query_id = 'default';
			} else {
				$query_id = $settings['_element_id'];
			}

			$request_query_id = jet_smart_filters()->query->get_current_provider( 'query_id' );

			if ( $query_id !== $request_query_id ) {
				return $args;
			}

		}

		$query_args = array_merge( $args, jet_smart_filters()->query->get_query_args() );
		$query_args = $this->query_maybe_has_offset( $query_args );

		return $query_args;

	}

}
