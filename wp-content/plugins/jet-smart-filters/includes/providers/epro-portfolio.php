<?php
/**
 * Class: Jet_Smart_Filters_Provider_EPro_Portfolio
 * Name: Elementor Pro Portfolio
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Provider_EPro_Portfolio' ) ) {
	/**
	 * Define Jet_Smart_Filters_Provider_EPro_Portfolio class
	 */
	class Jet_Smart_Filters_Provider_EPro_Portfolio extends Jet_Smart_Filters_Provider_Base {
		/**
		 * Watch for default query
		 */
		public function __construct() {

			if ( ! jet_smart_filters()->query->is_ajax_filter() ) {
				if ( defined( 'ELEMENTOR_PRO_VERSION' ) && version_compare( ELEMENTOR_PRO_VERSION, '2.5.0', '>=' ) ) {
					add_action(
						'elementor/query/jet-smart-filters',
						array( $this, 'portfolio_store_default_query' ),
						0, 2
					);
				} else {
					add_action(
						'elementor_pro/posts/query/jet-smart-filters',
						array( $this, 'portfolio_store_default_query' ),
						0, 2
					);
				}

				add_action( 'elementor/widget/before_render_content', array( $this, 'store_default_settings' ), 0 );
			}
		}

		/**
		 * Hook apply query function
		 */
		public function hook_apply_query() {

			if ( defined( 'ELEMENTOR_PRO_VERSION' ) && version_compare( ELEMENTOR_PRO_VERSION, '2.5.0', '>=' ) ) {
				add_action( 'elementor/query/jet-smart-filters', array( $this, 'portfolio_add_query_args' ), 0, 2 );
			} else {
				add_action( 'elementor_pro/posts/query/jet-smart-filters', array( $this, 'portfolio_add_query_args' ), 0, 2 );
			}

			add_filter( 'elementor_pro/query_control/get_query_args/current_query', array( $this, 'portfolio_add_current_query_args' ), 10 );
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

			foreach ( $store_settings as $key ) {
				$default_settings[ $key ] = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
			}

			$default_settings['_el_widget_id'] = $widget->get_id();

			jet_smart_filters()->providers->store_provider_settings( $this->get_id(), $default_settings, $query_id );
		}

		/**
		 * Returns Elementor Pro apropriate widget name
		 */
		public function widget_name() {

			return 'portfolio';
		}

		/**
		 * Save default query
		 */
		public function portfolio_store_default_query( $wp_query, $widget ) {
			
			if ( $this->widget_name() !== $widget->get_name() ) {
				return;
			}
			
			$settings = $widget->get_settings();

			if ( ! empty( $settings['_element_id'] ) ) {
				$query_id = $settings['_element_id'];
			} else {
				$query_id = 'default';
			}

			$wp_query->set( 'jet_smart_filters', $this->get_id() . '/' . $query_id );

			jet_smart_filters()->query->store_provider_default_query( $this->get_id(), array(
				'post_type'      => $wp_query->get( 'post_type' ),
				'paged'          => $wp_query->get( 'paged' ),
				'posts_per_page' => $wp_query->get( 'posts_per_page' ),
				'tax_query'      => $wp_query->get( 'tax_query' ),
				'category_name'  => $wp_query->get( 'category_name' ),
			), $query_id );

			$query['jet_smart_filters'] = jet_smart_filters()->query->encode_provider_data(
				$this->get_id(),
				$query_id
			);
		}

		/**
		 * Get provider name
		 */
		public function get_name() {

			return __( 'Elementor Pro Portfolio', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'epro-portfolio';
		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_wrapper_selector() {

			return '.elementor-portfolio';
		}

		/**
		 * Get provider list item selector
		 */
		public function get_item_selector() {

			return '.elementor-portfolio-item';
		}

		/**
		 * If added unique ID this paramter will determine - search selector inside this ID, or is the same element
		 */
		public function in_depth() {

			return true;
		}

		/**
		 * Returns settings to store list
		 */
		public function settings_to_store() {

			return array(
				'posts_per_page',
				'thumbnail_size_size',
				'masonry',
				'item_ratio',
				'show_title',
				'title_tag',
				'show_filter_bar',
				'taxonomy',
				'nothing_found_message',
				'posts_post_type',
				'posts_posts_ids',
				'posts_include_term_ids',
				'posts_include_authors',
				'posts_related_taxonomies',
				'posts_include',
				'posts_exclude',
				'posts_exclude_ids',
				'posts_exclude_term_ids',
				'posts_exclude_authors',
				'posts_avoid_duplicates',
				'posts_authors',
				'posts_category_ids',
				'posts_post_tag_ids',
				'posts_post_format_ids',
				'orderby',
				'order',
				'offset',
				'exclude',
				'exclude_ids',
				'avoid_duplicates',
				'posts_query_id',
				'posts_offset',
				'posts_related_fallback',
				'posts_fallback_ids',
				'posts_select_date',
				'posts_date_before',
				'posts_date_after',
				'posts_orderby',
				'posts_order',
				'posts_ignore_sticky_posts',
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

		/**
		 * Get filtered provider content
		 */
		public function ajax_get_content() {

			$settings  = jet_smart_filters()->query->get_query_settings();
			$settings  = $this->ensure_settings( $settings );
			$widget_id = $settings['_el_widget_id'];

			unset( $settings['_el_widget_id'] );

			$data = array(
				'id'         => $widget_id,
				'elType'     => 'widget',
				'settings'   => $this->sanitize_settings( $settings ),
				'elements'   => array(),
				'widgetType' => $this->widget_name(),
			);

			$this->hook_apply_query();

			$attributes = jet_smart_filters()->query->get_query_settings();
			$widget     = Elementor\Plugin::$instance->elements_manager->create_element_instance( $data );

			if ( ! $widget ) {
				throw new \Exception( 'Widget not found.' );
			}

			ob_start();
			$widget->render_content();
			$content = ob_get_clean();

			if ( $content ) {
				echo $content;
			} else {
				echo '<div class="elementor-widget-container"></div>';
			}
		}

		/**
		 * Pass args from reuest to provider
		 */
		public function apply_filters_in_request() {

			$args = jet_smart_filters()->query->get_query_args();

			if ( ! $args ) {
				return;
			}

			$this->hook_apply_query();
		}

		/**
		 * Add custom query arguments
		 */
		public function portfolio_add_query_args( $wp_query, $widget ) {

			if ( $this->widget_name() !== $widget->get_name() ) {
				return;
			}

			$settings = $widget->get_settings();

			if ( ! empty( $settings['_element_id'] ) ) {
				$query_id = $settings['_element_id'];
			} else {
				$query_id = 'default';
			}

			$wp_query->set( 'jet_smart_filters', $this->get_id() . '/' . $query_id );

			foreach ( jet_smart_filters()->query->get_query_args() as $query_var => $value ) {
				$wp_query->set( $query_var, $value );
			}
		}

		/**
		 * Add current query arguments
		 */
		public function portfolio_add_current_query_args( $query ) {

			foreach ( jet_smart_filters()->query->get_query_args() as $query_var => $value ) {
				$query[ $query_var ] = $value;
			}

			$query['jet_smart_filters'] = $this->get_id() . '/default';

			return $query;
		}
	}
}
