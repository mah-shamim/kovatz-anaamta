<?php
/**
 * JetWooBuilder dynamic tags manager class.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Dynamic_Tags_Manager' ) ) {

	class Jet_Woo_Builder_Dynamic_Tags_Manager {

		// A reference to an archive template id.
		private $archive_template = null;

		// A reference to an category template id.
		private $category_template = null;

		function __construct() {

			add_action( 'elementor/init', array( $this, 'init' ) );

			add_filter( 'jet-woo-builder/elementor-views/frontend/archive-item-content', array( $this, 'add_archive_product_card_dynamic_css' ), 10, 3 );
			add_filter( 'jet-woo-builder/integration/jet-popup/the_content', [ $this, 'add_quick_view_popup_item_dynamic_css' ], 10, 2 );

			add_action( 'elementor/element/before_parse_css', array( $this, 'fix_missing_bg_properties' ), 10, 2 );

			// Prevent enqueue default dynamic CSS for archive item templates
			add_action( 'elementor/css-file/post/enqueue', array( $this, 'remove_enqueue_default_dynamic_css' ), 9 );
			add_action( 'elementor/css-file/post/enqueue', array( $this, 'add_enqueue_default_dynamic_css' ), 11 );

			$this->archive_template  = jet_woo_builder_shop_settings()->get( 'archive_template' );
			$this->category_template = jet_woo_builder_shop_settings()->get( 'category_template' );

		}

		/**
		 * Init
		 *
		 * Initialize dynamic tags parts.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @return void
		 */
		public function init() {
			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.0.0', '>=' ) ) {
				require jet_woo_builder()->plugin_path( 'includes/components/elementor-views/dynamic-tags/dynamic-css.php' );
			}
		}

		/**
		 * Add dynamic CSS to the archive product card.
		 *
		 * @param $content
		 * @param $template_id
		 * @param $object
		 *
		 * @return string
		 */
		public function add_archive_product_card_dynamic_css( $content, $template_id, $object ) {

			if ( ! class_exists( 'Elementor\Core\DynamicTags\Dynamic_CSS' ) ) {
				return $content;
			}

			$class          = get_class( $object );
			$default_object = null;

			switch ( $class ) {
				case 'WP_Post':
					$post_id = $object->ID;
					break;

				case 'WP_Term':
					$post_id = $object->term_id;
					break;

				default:
					$post_id = get_the_ID();
					$object  = get_post( $post_id );
					break;
			}

			if ( function_exists( 'jet_engine' ) ) {
				if ( is_a( $object, 'WP_Post' ) ) {
					jet_engine()->listings->data->set_listing( jet_engine()->listings->get_new_doc( array(
						'listing_source'    => 'posts',
						'listing_post_type' => 'product',
						'listing_tax'       => false,
						'is_main'           => true,
					), $post_id ) );
				}

				if ( is_a( $object, 'WP_Term' ) ) {
					$default_object = jet_engine()->listings->data->get_current_object();
					jet_engine()->listings->data->set_current_object( $object );
				}
			}

			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.0.0', '>=' ) ) {
				$css_file = Jet_Woo_Builder_Elementor_Dynamic_CSS::create( $post_id, $template_id );
			} else {
				$css_file = Elementor\Core\DynamicTags\Dynamic_CSS::create( $post_id, $template_id );
			}

			$css = $css_file->get_content();

			if ( function_exists( 'jet_engine' ) ) {
				if ( is_a( $object, 'WP_Post' ) ) {
					jet_engine()->listings->data->reset_listing();
				}

				if ( is_a( $object, 'WP_Term' ) ) {
					jet_engine()->listings->data->set_current_object( $default_object );
				}
			}

			if ( empty( $css ) ) {
				return $content;
			}

			$css = str_replace( '.elementor-' . $post_id, '.jet-woo-builder-archive-item-' . $post_id, $css );
			$css = sprintf( '<style type="text/css">%s</style>', $css );

			return $css . $content;

		}

		/**
		 * Add dynamic CSS to the popup item.
		 *
		 * @param $content
		 * @param $popup_data
		 *
		 * @return string
		 */
		public function add_quick_view_popup_item_dynamic_css( $content, $popup_data ) {

			if ( ! class_exists( 'Elementor\Core\DynamicTags\Dynamic_CSS' ) ) {
				return $content;
			}

			if ( empty( $popup_data['popup_id'] ) ) {
				return $content;
			}

			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.0.0-beta4', '>=' ) ) {
				$css_file = Jet_Woo_Builder_Elementor_Dynamic_CSS::create( $popup_data['templateId'], $popup_data['templateId'] );
			} else {
				$css_file = Elementor\Core\DynamicTags\Dynamic_CSS::create( $popup_data['templateId'], $popup_data['templateId'] );
			}

			$css = $css_file->get_content();

			if ( empty( $css ) ) {
				return $content;
			}

			$css = sprintf( '<style type="text/css">%s</style>', $css );

			return $css . $content;

		}

		/**
		 * Fix missing background properties.
		 *
		 * @param Elementor\Core\Files\CSS\Post $post_css
		 * @param Elementor\Controls_Stack      $element
		 */
		public function fix_missing_bg_properties( $post_css, $element ) {

			if ( wp_doing_ajax() && ! jet_woo_builder()->elementor_views->is_editor_ajax() ) {
				return;
			}

			if ( $post_css instanceof Elementor\Core\DynamicTags\Dynamic_CSS ) {
				return;
			}

			if ( jet_woo_builder_post_type()->slug() !== get_post_type( $post_css->get_post_id() ) ) {
				return;
			}

			$dynamic_settings = $element->get_settings( '__dynamic__' );

			if ( empty( $dynamic_settings ) ) {
				return;
			}

			$all_controls           = $element->get_controls();
			$media_dynamic_settings = array();

			foreach ( $dynamic_settings as $setting => $tag ) {
				if ( ! isset( $all_controls[ $setting ] ) || Elementor\Controls_Manager::MEDIA !== $all_controls[ $setting ]['type'] ) {
					continue;
				}

				$media_dynamic_settings[] = $setting;
			}

			if ( empty( $media_dynamic_settings ) ) {
				return;
			}

			$media_conditions_keys = array_map( function ( $key ) {
				return $key . '[url]!';
			}, $media_dynamic_settings );

			foreach ( $all_controls as $control_id => $control ) {
				if ( empty( $control['selectors'] ) || empty( $control['condition'] ) ) {
					continue;
				}

				foreach ( $control['condition'] as $condition_key => $condition_value ) {
					if ( ! in_array( $condition_key, $media_conditions_keys ) ) {
						continue;
					}

					unset( $control['condition'][ $condition_key ] );

					$element->update_control( $control_id, array(
						'condition' => $control['condition'],
					) );
				}
			}

		}

		/**
		 * Remove action for enqueue default dynamic css.
		 *
		 * @param Elementor\Core\Files\CSS\Post $css_file
		 */
		public function remove_enqueue_default_dynamic_css( $css_file ) {
			$template_id = $css_file->get_post_id();

			if ( $css_file instanceof Elementor\Core\DynamicTags\Dynamic_CSS ) {
				return;
			}

			if ( jet_woo_builder_post_type()->slug() !== get_post_type( $css_file->get_post_id() ) ) {
				return;
			}

			if ( $template_id === $this->archive_template || $template_id === $this->category_template ) {
				$dynamic_tags = Elementor\Plugin::instance()->dynamic_tags;
				remove_action( 'elementor/css-file/post/enqueue', array( $dynamic_tags, 'after_enqueue_post_css' ) );
			}
		}

		/**
		 * Add action for enqueue default dynamic css.
		 *
		 * @param Elementor\Core\Files\CSS\Post $css_file
		 */
		public function add_enqueue_default_dynamic_css( $css_file ) {
			$template_id = $css_file->get_post_id();

			if ( $css_file instanceof Elementor\Core\DynamicTags\Dynamic_CSS ) {
				return;
			}

			if ( jet_woo_builder_post_type()->slug() !== get_post_type( $css_file->get_post_id() ) ) {
				return;
			}

			if ( $template_id === $this->archive_template || $template_id === $this->category_template ) {
				$dynamic_tags = Elementor\Plugin::instance()->dynamic_tags;
				add_action( 'elementor/css-file/post/enqueue', array( $dynamic_tags, 'after_enqueue_post_css' ) );
			}
		}

	}

}