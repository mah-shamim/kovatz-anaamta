<?php
/**
 * Class: Jet_Smart_Filters_Provider_Jet_Engine
 * Name: JetEngine
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Provider_Jet_Engine' ) ) {
	/**
	 * Define Jet_Smart_Filters_Provider_Jet_Engine class
	 */
	class Jet_Smart_Filters_Provider_Jet_Engine extends Jet_Smart_Filters_Provider_Base {
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
		 */
		public function store_default_query( $args, $widget ) {

			if ( 'jet-listing-grid' !== $widget->get_name() ) {
				return $args;
			}

			$settings = $widget->get_settings();

			if ( empty( $settings['_element_id'] ) ) {
				$query_id = false;
			} else {
				$query_id = esc_attr( $settings['_element_id'] );
			}

			global $wp_query;

			if ( $wp_query && $wp_query->get( 'wc_query' ) ) {
				$args['wc_query'] = $wp_query->get( 'wc_query' );
			}

			$is_archive_template = isset( $settings['is_archive_template'] ) ? filter_var( $settings['is_archive_template'], FILTER_VALIDATE_BOOLEAN ) : false;

			if ( $is_archive_template ) {
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

			if ( is_callable( array( $widget, 'get_required_settings' ) ) ) {
				$provider_settings = call_user_func( array( $widget, 'get_required_settings' ) );

				if ( isset( $provider_settings['is_archive_template'] ) ) {
					unset( $provider_settings['is_archive_template'] );
				}
			} else {
				$provider_settings = apply_filters(
					'jet-smart-filters/providers/jet-engine/stored-settings',
					array(
						'lisitng_id'           => absint( $settings['lisitng_id'] ),
						'columns'              => ! empty( $settings['columns'] ) ? $settings['columns'] : 3,
						'columns_tablet'       => ! empty( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : false,
						'columns_mobile'       => ! empty( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : false,
						'not_found_message'    => ! empty( $settings['not_found_message'] ) ? $settings['not_found_message'] : '',
						'equal_columns_height' => ! empty( $settings['equal_columns_height'] ) ? $settings['equal_columns_height'] : '',
						'carousel_enabled'     => ! empty( $settings['carousel_enabled'] ) ? $settings['carousel_enabled'] : '',
						'slides_to_scroll'     => ! empty( $settings['slides_to_scroll'] ) ? $settings['slides_to_scroll'] : '',
						'arrows'               => ! empty( $settings['arrows'] ) ? $settings['arrows'] : '',
						'arrow_icon'           => ! empty( $settings['arrow_icon'] ) ? $settings['arrow_icon'] : '',
						'dots'                 => ! empty( $settings['dots'] ) ? $settings['dots'] : '',
						'autoplay'             => ! empty( $settings['autoplay'] ) ? $settings['autoplay'] : '',
						'autoplay_speed'       => ! empty( $settings['autoplay_speed'] ) ? $settings['autoplay_speed'] : '',
						'infinite'             => ! empty( $settings['infinite'] ) ? $settings['infinite'] : '',
						'effect'               => ! empty( $settings['effect'] ) ? $settings['effect'] : '',
						'speed'                => ! empty( $settings['speed'] ) ? $settings['speed'] : '',
						'is_masonry'           => ! empty( $settings['is_masonry'] ) ? $settings['is_masonry'] : '',
					),
					$settings
				);
			}

			jet_smart_filters()->providers->store_provider_settings(
				$this->get_id(),
				$provider_settings,
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
		 */
		public function get_name() {
			return __( 'JetEngine', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {
			return 'jet-engine';
		}

		/**
		 * Get filtered provider content
		 */
		public function ajax_get_content() {

			if ( ! function_exists( 'jet_engine' ) ) {
				return;
			}

			add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 10, 2 );

			if ( jet_engine()->has_elementor() ) {
				Elementor\Plugin::instance()->frontend->start_excerpt_flag( null );
			}

			$attrs  = isset( $_REQUEST['settings'] ) ? $this->sanitize_settings( $_REQUEST['settings'] ) : array();
			$render = jet_engine()->listings->get_render_instance( 'listing-grid', $attrs );

			$render->render();

		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_wrapper_selector() {

			return apply_filters( 
				'jet-smart-filters/providers/jet-engine/selector',
				'.jet-listing-grid.jet-listing'
			);
		}

		/**
		 * Get provider list selector
		 */
		public function get_list_selector() {

			return '.jet-listing-grid__items';
		}

		/**
		 * Get provider list item selector
		 */
		/* public function get_item_selector() {

			return '.jet-listing-grid__item';
		} */

		/**
		 * Action for wrapper selector - 'insert' into it or 'replace'
		 */
		public function get_wrapper_action() {

			return 'replace';
		}

		/**
		 * If added unique ID this paramter will determine - search selector inside this ID, or is the same element
		 */
		public function in_depth() {

			return true;
		}

		/**
		 * Pass args from reuest to provider
		 */
		public function apply_filters_in_request() {

			$args = jet_smart_filters()->query->get_query_args();

			if ( ! $args ) {
				return;
			}

			add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 20, 2 );
		}

		/**
		 * Updates the arguments based on the offset parameter
		 */
		public function query_maybe_has_offset( $args ) {

			if ( isset( $args['offset'] ) ){
				add_filter( 'found_posts', array( $this, 'adjust_offset_pagination' ), 1, 2 );

				if ( isset( $args['paged'] ) ) {
					$args['offset'] = $args['offset'] + ( ( $args['paged'] - 1 ) * $args['posts_per_page'] );
				}
			}

			return $args;
		}

		/**
		 * Adjusts page number shift
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
		 */
		public function add_query_args( $args, $widget ) {

			if ( 'jet-listing-grid' !== $widget->get_name() ) {
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

				// Replace global wp_query if is archive template
				$is_archive_template = isset( $settings['is_archive_template'] ) ? $settings['is_archive_template'] : false;
				$is_archive_template = filter_var( $is_archive_template, FILTER_VALIDATE_BOOLEAN );

				if ( $is_archive_template ) {
					global $wp_query;

					$archive_query_vars = array_merge( $wp_query->query_vars, jet_smart_filters()->query->get_query_args() );
					$archive_query_vars = $this->query_maybe_has_offset( $archive_query_vars );

					$wp_query = new WP_Query( $archive_query_vars );

					return $archive_query_vars;
				}
			}

			if ( jet_smart_filters()->query->is_ajax_filter() ) {
				remove_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 10, 2 );
			}

			$query_args = jet_smart_filters()->utils->merge_query_args( $args, jet_smart_filters()->query->get_query_args() );
			$query_args = $this->query_maybe_has_offset( $query_args );

			return $query_args;
		}
	}
}
