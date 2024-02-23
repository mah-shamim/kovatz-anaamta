<?php
/**
 * Jet_Search_Blocks_Integration class
 *
 * @package   jet-search
 * @author    Zemez
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Search_Blocks_Integration' ) ) {

	/**
	 * Define Jet_Search_Blocks_Integration class
	 */
	class Jet_Search_Blocks_Integration {

		public $rendered = 0;

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   Jet_Search_Blocks_Integration
		 */
		private static $instance = null;

		/**
		 * Initialize integration hooks
		 *
		 * @return void
		 */
		public function init() {
			require_once jet_search()->plugin_path( 'includes/blocks-views/blocks-styles/ajax-search.php' );
			require_once jet_search()->plugin_path( 'includes/blocks-views/blocks-styles/search-suggestions.php' );

			add_action( 'init', array( $this, 'register_block_type' ), 99 );
			add_action( 'init', 'ajax_search_block_add_style', 10 );
			add_action( 'init', 'search_suggestions_block_add_style', 10 );

			add_action( 'enqueue_block_assets',   array( jet_search_assets(), 'enqueue_styles' ), 0 );
			add_action( 'enqueue_block_editor_assets', array( $this, 'register_styles' ), 0 );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_styles' ), 0 );
		}

		/**
		 * Register block type for search
		 *
		 * @return [type] [description]
		 */
		public function register_block_type() {

			wp_register_script(
				'jet-search-block',
				jet_search()->plugin_url( 'assets/js/jet-search-block.js' ),
				array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-block-editor', 'wp-i18n', 'wp-polyfill', 'lodash', 'wp-api-fetch' ),
				jet_search()->get_version() . time(),
				true
			);

			$mimes = get_allowed_mime_types();

			wp_localize_script( 'jet-search-block', 'JetSearchData', array(
				'supportSVG'        => isset( $mimes['svg'] ) ? true : false,
				'taxonomiesList'    => $this->get_taxonomies_list(),
				'postTypesList'     => $this->get_post_types_list(),
				'metaCallbacks'     => \Jet_Search_Tools::allowed_meta_callbacks(),
				'thumbSizes'        => $this->get_thumb_sizes(),
				'placeholderImgUrl' => $this->get_placeholder_img_url(),
				'arrowsType'        => $this->get_arrows_type(),
				'settingsPageLink'  => jet_search_settings()->get_settings_page_link()
			) );

			register_block_type(
				jet_search()->plugin_path( 'includes/blocks-views/blocks/ajax-search-block.json' ),
				array(
					'render_callback' => array( $this, 'search_render_callback' ),
				)
			);

			register_block_type(
				jet_search()->plugin_path( 'includes/blocks-views/blocks/search-suggestions-block.json' ),
				array(
					'render_callback' => array( $this, 'search_suggestions_render_callback' ),
				)
			);
		}

		/**
		 * Register plugin stylesheets.
		 *
		 * @return void
		 */
		public function register_styles() {

			wp_register_style(
				'jquery-chosen',
				jet_search()->plugin_url( 'assets/lib/chosen/chosen.min.css' ),
				false,
				'1.8.7'
			);

			wp_register_style(
				'jet-search',
				jet_search()->plugin_url( 'assets/css/jet-search.css' ),
				array(),
				jet_search()->get_version()
			);

			wp_register_style(
				'jet-search-editor',
				jet_search()->plugin_url( 'assets/css/jet-search-editor.css' ),
				array(),
				jet_search()->get_version()
			);
		}

		/**
		 * Register plugin stylesheets.
		 *
		 * @return void
		 */
		public function enqueue_editor_styles() {
			wp_enqueue_style( 'jet-search-editor' );
		}

		public function get_taxonomies_list() {

			$taxonomies = \Jet_Search_Tools::get_taxonomies( true );

			foreach ( $taxonomies as $value => $label ) {
				$taxonomies_list[] = array(
					'value' => $value,
					'label' => $label,
				);
			}

			return $taxonomies_list;
		}

		public function get_post_types_list() {
			$post_types = \Jet_Search_Tools::get_post_types();

			foreach ( $post_types as $value => $label ) {
				$post_types_list[] = array(
					'value' => $value,
					'label' => $label,
				);
			}

			return $post_types_list;
		}

		public function get_thumb_sizes() {
			$thumb_sizes = \Jet_Search_Tools::get_image_sizes();

			foreach ( $thumb_sizes as $value => $label ) {
				$thumb_sizes_list[] = array(
					'value' => $value,
					'label' => $label,
				);
			}

			return $thumb_sizes_list;
		}

		public function get_placeholder_img_url() {
			return jet_search()->plugin_url( 'assets/images/placeholder.png' );
		}

		public function get_arrows_type() {

			$arrows_type = \Jet_Search_Tools::get_available_prev_arrows_list();

			foreach ( $arrows_type as $value => $label ) {
				$arrows_type_list[] = array(
					'value' => $value,
					'label' => $label,
				);
			}

			return $arrows_type_list;
		}

		public function search_render_callback( $attributes = array() ) {

			$this->rendered++;
			$render = new Jet_Search_Render( $attributes, $this->rendered );

			ob_start();
			$render->render();

			$class_name = ! empty( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';

			return sprintf(
				'<div class="jet-ajax-search-block %2$s" data-is-block="jet-search/ajax-search">%1$s</div>',
				ob_get_clean(),
				$class_name
			);
		}

		public function search_suggestions_render_callback( $attributes = array() ) {

			$this->rendered++;
			$render = new Jet_Search_Suggestions_Render( $attributes, $this->rendered );

			ob_start();
			$render->render();

			$class_name = ! empty( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';

			return sprintf(
				'<div class="jet-search-suggestions-block %2$s" data-is-block="jet-search/search-suggestions">%1$s</div>',
				ob_get_clean(),
				$class_name
			);
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return Jet_Search_Blocks_Integration
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}

/**
 * Returns instance of Jet_Search_Blocks_Integration
 *
 * @return Jet_Search_Blocks_Integration
 */
function jet_search_blocks_integration() {
	return Jet_Search_Blocks_Integration::get_instance();
}
