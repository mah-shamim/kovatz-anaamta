<?php
/**
 * Class: Jet_Smart_Filters_Provider_EPro_Products
 * Name: Elementor Pro Products
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Provider_EPro_Products' ) ) {
	/**
	 * Define Jet_Smart_Filters_Provider_EPro_Products class
	 */
	class Jet_Smart_Filters_Provider_EPro_Products extends Jet_Smart_Filters_Provider_Base {

		public $current_query_id = 'default';

		/**
		 * Watch for default query
		 */
		public function __construct() {

			if ( ! jet_smart_filters()->query->is_ajax_filter() ) {
				add_filter( 'woocommerce_shortcode_products_query', array( $this, 'store_shortcode_query' ), 0, 3 );
				add_action( 'woocommerce_product_query', array( $this, 'store_archive_query' ) );
				add_action( 'pre_get_posts', array( $this, 'store_search_query' ) );
				add_action( 'elementor/widget/before_render_content', array( $this, 'store_default_settings' ), 0 );
			}
		}

		/**
		 * Store default query args
		 */
		public function store_shortcode_query( $args, $attributes, $type ) {

			$query_id = $this->current_query_id;
			
			$args['suppress_filters']  = false;
			$args['post_type']         = 'product';
			$args['no_found_rows']     = false;
			$args['jet_smart_filters'] = jet_smart_filters()->query->encode_provider_data(
				$this->get_id(),
				$query_id
			);

			jet_smart_filters()->query->store_provider_default_query( $this->get_id(), $args, $query_id, true );

			/* if ( isset( $_REQUEST['product-page'] ) ) {
				$attributes['page'] = absint( $_REQUEST['product-page'] );
			}

			jet_smart_filters()->providers->store_provider_settings( $this->get_id(), array(
				'query_type'     => 'shortcode',
				'shortcode_type' => $type,
				'attributes'     => $attributes,
			), $query_id );

			add_action( "woocommerce_shortcode_before_{$type}_loop", array( $this, 'store_props' ) ); */

			return $args;
		}

		public function store_archive_query( $query ) {

			if ( ! $query->get( 'wc_query' ) ) {
				return;
			}

			$default_query = $this->get_default_query( $query );

			jet_smart_filters()->query->store_provider_default_query( $this->get_id(), $default_query );
			$query->set( 'jet_smart_filters', $this->get_id() );
		}

		public function store_search_query( $query ) {

			if ( ! is_search() || ! $query->is_search ) {
				return;
			}

			$default_query = $this->get_default_query( $query );

			add_action( 'woocommerce_shortcode_before_current_query_loop', function() use ( $default_query, $query ) {

				jet_smart_filters()->query->store_provider_default_query( $this->get_id(), $default_query );
				$query->set( 'jet_smart_filters', $this->get_id() );
			} );
		}

		public function get_default_query( $query ) {

			$default_query = array(
				//'post_type'       => $query->get( 'post_type' ),
				'post_status'       => 'publish',
				'wc_query'          => $query->get( 'wc_query' ),
				'orderby'           => $query->get( 'orderby' ),
				'order'             => $query->get( 'order' ),
				'paged'             => $query->get( 'paged' ),
				'posts_per_page'    => $query->get( 'posts_per_page' ),
				'tax_query'         => $query->get( 'tax_query' ),
				'jet_smart_filters' => $this->get_id(),
			);

			if ( ! empty( $query->queried_object ) ) {
				$default_query['taxonomy'] = $query->queried_object->taxonomy;
				$default_query['term']     = $query->queried_object->slug;
			}

			if ( ! empty( $query->query ) ) {
				foreach ( $query->query as $q_key => $q_value ) {
					$default_query[ $q_key ] = $q_value;
				}
			}

			if ( ! empty( $query->get( 's' ) ) ) {
				$default_query['s'] = $query->get( 's' );
			}

			switch ( $default_query['orderby'] ) {
				case 'price' :
					$default_query['meta_key'] = '_price';
					$default_query['orderby']  = 'meta_value_num';
					break;
				case 'rating':
					$default_query['meta_key'] = '_wc_average_rating';
					$default_query['orderby']  = 'meta_value_num';
					$default_query['order']    = 'DESC';
					break;
			}

			return $default_query;
		}

		/**
		 * Save default widget settings
		 */
		public function store_default_settings( $widget ) {

			if ( $this->widget_name() !== $widget->get_name() ) {
				return;
			}

			$settings         = $widget->get_settings();
			$store_settings   = $this->settings_to_store();
			$default_settings = array();

			if ( ! empty( $settings['_element_id'] ) ) {
				$query_id = $settings['_element_id'];
			} else {
				$query_id = 'default';
			}

			$this->current_query_id = $query_id;

			foreach ( $store_settings as $key ) {
				$default_settings[ $key ] = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
			}

			$default_settings['_el_widget_id'] = $widget->get_id();

			jet_smart_filters()->providers->store_provider_settings( $this->get_id(), $default_settings, $query_id );

			add_action( 'woocommerce_shortcode_before_products_loop', array( $this, 'store_props' ) );
			add_action( 'woocommerce_shortcode_before_current_query_loop', array( $this, 'store_props' ) );
		}

		/**
		 * Get provider name
		 */
		public function get_name() {

			return __( 'Elementor Pro Products', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'epro-products';
		}

		/**
		 * Returns Elementor Pro apropriate widget name
		 */
		public function widget_name() {

			return 'woocommerce-products';
		}

		/**
		 * Returns settings to store list
		 */
		public function settings_to_store() {

			return array(
				'rows',
				'columns',
				'columns_tablet',
				'columns_mobile',
				'paginate',
				'show_result_count',
				'allow_order',
				'query_post_type',
				'query_posts_ids',
				'query_product_cat_ids',
				'query_product_tag_ids',
				'orderby',
				'order',
				'exclude',
				'exclude_ids',
				'avoid_duplicates',
				'query_include',
				'query_include_term_ids',
				'query_include_authors',
				'query_exclude',
				'query_exclude_ids',
				'query_exclude_term_ids',
				'query_select_date',
				'query_date_before',
				'query_date_after',
				'query_orderby',
				'query_order',
				'products_class',
				'_element_id'
			);
		}

		/**
		 * Ensure all settings are passed
		 */
		public function ensure_settings( $settings ) {

			foreach ( $this->settings_to_store() as $setting ) {
				if ( ! isset( $settings[ $setting ] ) ) {
					if ( false !== strpos( $setting, '_meta_data' ) ) {
						$settings[ $setting ] = array();
					} else {
						$settings[ $setting ] = false;
					}
				}
			}

			return $settings;
		}

		public function ajax_get_content() {

			if ( ! function_exists( 'wc' ) ) {
				return;
			}

			$this->сlear_store_props();

			$settings  = $this->ensure_settings( jet_smart_filters()->query->get_query_settings() );
			$widget_id = $settings['_el_widget_id'];
			unset( $settings['_el_widget_id'] );

			if ( ! empty( $settings['_element_id'] ) ) {
				$this->current_query_id = $settings['_element_id'];
			}

			$data = array(
				'id'         => $widget_id,
				'elType'     => 'widget',
				'settings'   => $this->sanitize_settings( $settings ),
				'elements'   => array(),
				'widgetType' => $this->widget_name(),
			);

			do_action( 'jet-smart-filters/providers/epro-products/before-ajax-content' );

			add_action( 'woocommerce_shortcode_before_products_loop', array( $this, 'store_props' ) );
			add_action( 'woocommerce_shortcode_before_current_query_loop', array( $this, 'store_props' ) );

			$widget = Elementor\Plugin::$instance->elements_manager->create_element_instance( $data );

			if ( ! $widget ) {
				throw new \Exception( 'Widget not found.' );
			}

			if ( isset( $settings['query_post_type'] ) && $settings['query_post_type'] === 'current_query' ) {
				// archive query
				global $wp_query;
				$wp_query = new WP_Query( jet_smart_filters()->query->get_query_args() );
			} else {
				// shortcode query
				add_filter( 'woocommerce_shortcode_products_query', array( $this, 'add_query_args' ), 10, 2 );
			}

			ob_start();
			$widget->render_content();
			$content = ob_get_clean();

			if ( $content ) {
				echo $content;
			} else {
				echo '<div class="elementor-widget-container"></div>';
			}

			do_action( 'jet-smart-filters/providers/epro-products/after-ajax-content' );
		}

		/**
		 * Store query ptoperties
		 */
		public function store_props() {

			remove_action( 'woocommerce_shortcode_before_products_loop', array( $this, 'store_props' ) );
			remove_action( 'woocommerce_shortcode_before_current_query_loop', array( $this, 'store_props' ) );

			global $woocommerce_loop;

			$query_id = $this->current_query_id;

			jet_smart_filters()->query->set_props(
				$this->get_id(),
				array(
					'found_posts'   => $woocommerce_loop['total'],
					'max_num_pages' => $woocommerce_loop['total_pages'],
					'page'          => $woocommerce_loop['current_page'],
				),
				$query_id
			);
		}

		/**
		 * Сlear store props
		 */
		public function сlear_store_props() {

			$query_id = $this->current_query_id;

			jet_smart_filters()->query->set_props(
				$this->get_id(),
				array(
					'found_posts'   => 0,
					'max_num_pages' => 0,
					'page'          => 0
				),
				$query_id
			);
		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_wrapper_selector() {

			return '.elementor-widget-woocommerce-products .elementor-widget-container';
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

			return '#';
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
			add_filter( 'pre_get_posts', array( $this, 'add_archive_query_args' ), 10 );
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

				$query_id = $this->current_query_id;

				if ( $query_id !== jet_smart_filters()->render->request_provider( 'query_id' ) ) {
					return $args;
				}
			}

			if ( isset( $filter_args['no_found_rows'] ) ) {
				$filter_args['no_found_rows'] = filter_var( $filter_args['no_found_rows'], FILTER_VALIDATE_BOOLEAN );
			}

			if ( isset( $filter_args['ignore_sticky_posts'] ) ) {
				$filter_args['ignore_sticky_posts'] = filter_var( $filter_args['ignore_sticky_posts'], FILTER_VALIDATE_BOOLEAN );
			}

			return $this->merge_query( $filter_args, $args );
		}

		public function add_archive_query_args( $query ) {

			if ( $query->get('jsf') !== $this->get_id() && ! $query->get( 'wc_query' ) && ! $query->is_search ) {
				return;
			}

			$jet_query_args = jet_smart_filters()->query->get_query_args();

			foreach ( $jet_query_args as $query_var => $value ) {
				$query->set( $query_var, $value );
			}

			foreach ( ['orderby', 'order'] as $value ) {
				if ( array_key_exists( $value, $jet_query_args ) ) {
					WC()->query->remove_ordering_args();
					break;
				}
			}
		}
	}
}
