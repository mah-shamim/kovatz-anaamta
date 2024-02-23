<?php
/**
 * Class: Jet_Smart_Filters_Provider_Jet_Engine_Calendar
 * Name: JetEngine Calendar
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Provider_Jet_Engine_Calendar' ) ) {
	/**
	 * Define Jet_Smart_Filters_Provider_Jet_Engine_Calendar class
	 */
	class Jet_Smart_Filters_Provider_Jet_Engine_Calendar extends Jet_Smart_Filters_Provider_Base {
		/**
		 * Watch for default query
		 */
		public function __construct() {

			if ( ! jet_smart_filters()->query->is_ajax_filter() && ! $this->is_month_request() ) {
				add_filter('jet-engine/listing/grid/posts-query-args', array( $this, 'store_default_query' ), 0, 2 );
			}

			if ( $this->is_month_request() ) {
				add_filter( 'jet-engine/listing/grid/custom-settings', array( $this, 'add_month_settings' ), 10, 2 );
				add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 10, 2 );
			}
		}

		/**
		 * Check if get month request is processed
		 */
		public function is_month_request() {

			if ( isset( $_REQUEST['action'] ) && 'jet_engine_calendar_get_month' === $_REQUEST['action'] ) {
				return true;
			}

			if ( isset( $_REQUEST['jet_engine_action'] ) && 'jet_engine_calendar_get_month' === $_REQUEST['jet_engine_action'] ) {
				return true;
			}

			return false;
		}

		/**
		 * Add widget settings
		 */
		public function add_month_settings( $settings, $widget ) {

			if ( 'jet-listing-calendar' !== $widget->get_name() ) {
				return $settings;
			}

			if ( ! empty( $_REQUEST['settings'] ) ) {
				return $_REQUEST['settings'];
			} else {
				return $settings;
			}
		}

		/**
		 * Store default query args
		 */
		public function store_default_query( $args, $widget ) {

			if ( 'jet-listing-calendar' !== $widget->get_name() ) {
				return $args;
			}

			$settings = $widget->get_settings();

			if ( empty( $settings['_element_id'] ) ) {
				$query_id = false;
			} else {
				$query_id = $settings['_element_id'];
			}

			jet_smart_filters()->query->store_provider_default_query( $this->get_id(), $args, $query_id );

			if ( is_callable( array( $widget, 'get_required_settings' ) ) ) {
				$provider_settings = call_user_func( array( $widget, 'get_required_settings' ) );
			} else {
				$provider_settings = array(
					'lisitng_id'          => isset( $settings['lisitng_id'] ) ? $settings['lisitng_id'] : false,
					'group_by'            => isset( $settings['group_by'] ) ? $settings['group_by'] : false,
					'group_by_key'        => isset( $settings['group_by_key'] ) ? $settings['group_by_key'] : false,
					'allow_multiday'      => isset( $settings['allow_multiday'] ) ? $settings['allow_multiday'] : false,
					'end_date_key'        => isset( $settings['end_date_key'] ) ? $settings['end_date_key'] : false,
					'custom_start_from'   => isset( $settings['custom_start_from'] ) ? $settings['custom_start_from'] : false,
					'week_days_format'    => isset( $settings['end_date_key'] ) ? $settings['end_date_key'] : false,
					'start_from_month'    => isset( $settings['start_from_month'] ) ? $settings['start_from_month'] : date( 'F' ),
					'start_from_year'     => isset( $settings['start_from_year'] ) ? $settings['start_from_year'] : date( 'Y' ),
					'posts_query'         => isset( $settings['posts_query'] ) ? $settings['posts_query'] : array(),
					'meta_query_relation' => isset( $settings['meta_query_relation'] ) ? $settings['meta_query_relation'] : false,
					'tax_query_relation'  => isset( $settings['tax_query_relation'] ) ? $settings['tax_query_relation'] : false,
					'hide_widget_if'      => isset( $settings['hide_widget_if'] ) ? $settings['hide_widget_if'] : false,
					'caption_layout'      => isset( $settings['caption_layout'] ) ? $settings['caption_layout'] : 'layout-1',
				);
			}

			jet_smart_filters()->providers->store_provider_settings( $this->get_id(), $provider_settings, $query_id );

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

			return __( 'JetEngine Calendar', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'jet-engine-calendar';
		}

		/**
		 * Get filtered provider content
		 */
		public function ajax_get_content() {

			if ( ! function_exists( 'jet_engine' ) ) {
				return;
			}

			add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 10, 2 );
			add_filter( 'jet-engine/listing/grid/custom-settings', array( $this, 'add_settings' ), 10, 2 );

			$attrs  = $this->sanitize_settings( jet_smart_filters()->query->get_query_settings() );
			$render = jet_engine()->listings->get_render_instance( 'listing-calendar', $attrs );

			$render->render();

		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_wrapper_selector() {
			return '.jet-listing-calendar';
		}

		/**
		 * Action for wrapper selector - 'insert' into it or 'replace'
		 */
		public function get_wrapper_action() {
			return 'replace';
		}

		/**
		 * If added unique ID this parameter will determine - search selector inside this ID, or is the same element
		 */
		public function in_depth() {
			return true;
		}

		/**
		 * Add custom settings for AJAX request
		 */
		public function add_settings( $settings, $widget ) {

			if ( 'jet-listing-calendar' !== $widget->get_name() ) {
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
		 * Add custom query arguments
		 */
		public function add_query_args( $args, $widget ) {

			if ( 'jet-listing-calendar' !== $widget->get_name() ) {
				return $args;
			}

			if ( ! jet_smart_filters()->query->is_ajax_filter() && ! $this->is_month_request() ) {

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

			if ( $this->is_month_request() ) {
				jet_smart_filters()->query->get_query_from_request( isset( $_REQUEST['query'] ) ? $_REQUEST : array() );
			}

			return array_merge( $args, jet_smart_filters()->query->get_query_args() );
		}
	}
}
