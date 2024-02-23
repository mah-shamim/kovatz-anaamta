<?php
/**
 * Class: Jet_Smart_Filters_Provider_WooCommerce_Shortcode
 * Name: WooCommerce Shortcode
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Provider_WooCommerce_Shortcode' ) ) {
	/**
	 * Define Jet_Smart_Filters_Provider_WooCommerce_Shortcode class
	 */
	class Jet_Smart_Filters_Provider_WooCommerce_Shortcode extends Jet_Smart_Filters_Provider_Base {
		/**
		 * Watch for default query
		 */
		public function __construct() {

			if ( ! jet_smart_filters()->query->is_ajax_filter() ) {
				add_filter( 'woocommerce_shortcode_products_query', array( $this, 'store_shortcode_query' ), 0, 3 );
			}
		}

		/**
		 * Store default query args
		 */
		public function store_shortcode_query( $args, $attributes, $type ) {

			if ( empty( $attributes['class'] ) ) {
				$query_id = 'default';
			} else {
				$query_id = $attributes['class'];
			}

			$args['suppress_filters']  = false;
			$args['no_found_rows']     = false;
			$args['jet_smart_filters'] = jet_smart_filters()->query->encode_provider_data(
				$this->get_id(),
				$query_id
			);

			jet_smart_filters()->query->store_provider_default_query( $this->get_id(), $args, $query_id );

			if ( isset( $_REQUEST['paged'] ) ) {
				$attributes['page'] = absint( $_REQUEST['paged'] );
			}

			jet_smart_filters()->providers->store_provider_settings( $this->get_id(), array(
				'query_type'     => 'shortcode',
				'shortcode_type' => $type,
				'attributes'     => $attributes,
			), $query_id );

			add_action( 'woocommerce_shortcode_before_' . $type . '_loop', array( $this, 'store_props' ) );

			return $args;
		}

		/**
		 * Get provider name
		 */
		public function get_name() {

			return __( 'WooCommerce Shortcode', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'woocommerce-shortcode';
		}

		/**
		 * Get filtered provider content
		 */
		public function ajax_get_content() {

			if ( ! function_exists( 'wc' ) ) {
				return;
			}

			$this->сlear_store_props();

			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'add_query_args' ), 10, 2 );

			$settings   = $this->sanitize_settings( jet_smart_filters()->query->get_query_settings() );
			$type       = $settings['shortcode_type'];
			$attributes = $settings['attributes'];

			global $post;
			$post = null;

			add_action( 'woocommerce_shortcode_before_' . $type . '_loop', array( $this, 'store_props' ) );

			$shortcode = new WC_Shortcode_Products( $attributes, $type );
			echo $shortcode->get_content();
		}

		/**
		 * Store query ptoperties
		 */
		public function store_props() {

			global $woocommerce_loop;

			jet_smart_filters()->query->set_props(
				$this->get_id(),
				array(
					'found_posts'   => $woocommerce_loop['total'],
					'max_num_pages' => $woocommerce_loop['total_pages'],
					'page'          => $woocommerce_loop['current_page'],
				)
			);
		}

		/**
		 * Сlear store props
		 */
		public function сlear_store_props() {

			jet_smart_filters()->query->set_props(
				$this->get_id(),
				array(
					'found_posts'   => 0,
					'max_num_pages' => 0,
					'page'          => 0
				)
			);
		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_wrapper_selector() {

			return 'body .woocommerce[class*="columns"]';
		}

		/**
		 * Get provider list selector
		 */
		public function get_list_selector() {

			return '.products';
		}

		/**
		 * Get provider list item selector
		 */
		public function get_item_selector() {

			return '.product';
		}

		/**
		 * Action for wrapper selector - 'insert' into it or 'replace'
		 */
		public function get_wrapper_action() {

			return 'replace';
		}

		/**
		 * Set prefix for unique ID selector. Mostly is default '#' sign, but sometimes class '.' sign needed
		 */
		public function id_prefix() {

			return '.';
		}

		/**
		 * Add custom settings for AJAX request
		 */
		public function add_settings( $settings ) {

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

			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'add_query_args' ), 10, 2 );
		}

		/**
		 * Add custom query arguments
		 */
		public function add_query_args( $args = array(), $attributes = array() ) {

			$filter_args = jet_smart_filters()->query->get_query_args();

			if ( ! isset( $filter_args['jet_smart_filters'] ) ) {
				return $args;
			}

			if ( $filter_args['jet_smart_filters'] !== jet_smart_filters()->render->request_provider( 'raw' ) ) {
				return $args;
			}

			if ( ! jet_smart_filters()->query->is_ajax_filter() ) {

				if ( empty( $attributes['class'] ) ) {
					$query_id = 'default';
				} else {
					$query_id = $attributes['class'];
				}

				if ( $query_id !== jet_smart_filters()->render->request_provider( 'query_id' ) ) {
					return $args;
				}
			}

			if ( isset( $filter_args['no_found_rows'] ) ){
				$filter_args['no_found_rows'] = filter_var( $filter_args['no_found_rows'], FILTER_VALIDATE_BOOLEAN );
			}

			if( isset( $filter_args['ignore_sticky_posts'] ) ){
				$filter_args['ignore_sticky_posts'] = filter_var( $filter_args['ignore_sticky_posts'], FILTER_VALIDATE_BOOLEAN );
			}

			return jet_smart_filters()->utils->merge_query_args( $args, $filter_args );
		}
	}
}
