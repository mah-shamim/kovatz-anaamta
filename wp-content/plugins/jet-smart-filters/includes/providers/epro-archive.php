<?php
/**
 * Class: Jet_Smart_Filters_Provider_EPro_Archive
 * Name: Elementor Pro Archive
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Provider_EPro_Archive' ) ) {
	/**
	 * Define Jet_Smart_Filters_Provider_EPro_Archive class
	 */
	class Jet_Smart_Filters_Provider_EPro_Archive extends Jet_Smart_Filters_Provider_Base {
		/**
		 * Watch for default query
		 */
		public function __construct() {

			if ( ! jet_smart_filters()->query->is_ajax_filter() ) {
				add_filter(
					'elementor/theme/posts_archive/query_posts/query_vars',
					array( $this, 'store_default_query' ),
					0, 2
				);

				add_action( 'elementor/widget/before_render_content', array( $this, 'store_default_settings' ), 0 );
			}
		}

		/**
		 * Hook apply query function
		 */
		public function hook_apply_query() {

			add_filter( 'elementor/theme/posts_archive/query_posts/query_vars', array( $this, 'add_query_args' ), 10 );
		}

		/**
		 * Returns Elementor Pro apropriate widget name
		 */
		public function widget_name() {

			return 'archive-posts';
		}

		/**
		 * Returns settings to store list
		 */
		public function settings_to_store() {

			return array(
				'_skin',
				'archive_custom_skin_template',
				'archive_classic_show_excerpt',
				'archive_classic_meta_separator',
				'archive_classic_read_more_text',
				'archive_cards_show_excerpt',
				'archive_cards_meta_separator',
				'archive_cards_read_more_text',
				'pagination_type',
				'pagination_numbers_shorten',
				'pagination_page_limit',
				'pagination_prev_label',
				'pagination_next_label',
				'nothing_found_message',
				'archive_classic_columns',
				'archive_classic_columns_tablet',
				'archive_classic_columns_mobile',
				'archive_classic_thumbnail',
				'archive_classic_masonry',
				'archive_classic_thumbnail_size_size',
				'archive_classic_item_ratio',
				'archive_classic_item_ratio_tablet',
				'archive_classic_item_ratio_mobile',
				'archive_classic_image_width',
				'archive_classic_image_width_tablet',
				'archive_classic_image_width_mobile',
				'archive_classic_show_title',
				'archive_classic_title_tag',
				'archive_classic_excerpt_length',
				'archive_classic_meta_data',
				'archive_classic_show_read_more',
				'archive_cards_columns',
				'archive_cards_columns_tablet',
				'archive_cards_columns_mobile',
				'archive_cards_thumbnail',
				'archive_cards_masonry',
				'archive_cards_thumbnail_size_size',
				'archive_cards_item_ratio',
				'archive_cards_item_ratio_tablet',
				'archive_cards_item_ratio_mobile',
				'archive_cards_show_title',
				'archive_cards_title_tag',
				'archive_cards_excerpt_length',
				'archive_cards_meta_data',
				'archive_cards_show_read_more',
				'archive_cards_show_badge',
				'archive_cards_badge_taxonomy',
				'archive_cards_show_avatar',
			);
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
		 * Save default query
		 */
		public function store_default_query( $query ) {

			$default_query = array(
				//'post_type'    => get_post_type(), - removed to filter taxonomy with multiple post types
				'paged'          => ! empty( $query['paged'] ) ? $query['paged'] : 1,
				'posts_per_page' => ! empty( $query['posts_per_page'] ) ? $query['posts_per_page'] : 10,
				'post_status'    => 'publish'
			);

			if ( ! empty( $query['post_type'] ) ) {
				$default_query['post_type'] = $query['post_type'];
			}

			if ( ! empty( $query['category_name'] ) ) {
				$default_query['category_name'] = $query['category_name'];
			}

			if ( ! empty( $query['tag'] ) ) {
				$default_query['tag'] = $query['tag'];
			}

			if ( ! empty( $query['taxonomy'] ) && ! empty( $query['term'] ) ) {
				$default_query['taxonomy'] = $query['taxonomy'];
				$default_query['term'] = $query['term'];
			}

			jet_smart_filters()->query->store_provider_default_query( $this->get_id(), $default_query );

			$query['jet_smart_filters'] = jet_smart_filters()->query->encode_provider_data( $this->get_id() );

			return $query;
		}

		/**
		 * Get provider name
		 */
		public function get_name() {

			return __( 'Elementor Pro Archive', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'epro-archive';
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
		 * Get provider wrapper selector
		 */
		public function get_wrapper_selector() {

			return '.elementor-widget-archive-posts .elementor-widget-container';
		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_list_selector() {

			return '.elementor-posts';
		}

		/**
		 * Get provider list item selector
		 */
		public function get_item_selector() {

			return '.elementor-post';
		}

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

			return false;
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
		public function add_query_args( $query ) {

			foreach ( jet_smart_filters()->query->get_query_args() as $query_var => $value ) {
				$query[ $query_var ] = $value;
			}

			$query['jet_smart_filters'] = $this->get_id() . '/default';

			return $query;
		}
	}
}
