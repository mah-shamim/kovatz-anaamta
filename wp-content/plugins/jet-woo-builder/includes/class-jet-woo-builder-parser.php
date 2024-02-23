<?php
/**
 * JetWooBuilder parser Class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Parser' ) ) {

	/**
	 * Define Jet_Woo_Builder_Parser class
	 */
	class Jet_Woo_Builder_Parser {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		public  $processed_documents      = array();
		private $jet_engine_object        = null;
		private $admin_bar_edit_documents = null;

		/**
		 * Macros regular expression
		 *
		 * @return string
		 */
		public function macros_regex() {
			return '/\%\%([a-z-_]+)(\:\:(.*?))?\%\%/';
		}

		/**
		 * Returns template content by template ID
		 *
		 * @param null $template_id
		 * @param bool $with_css
		 * @param null $object
		 *
		 * @return mixed
		 */
		public function get_template_content( $template_id = null, $with_css = false, $object = null ) {

			global $wp_query;

			$default_object = $wp_query->queried_object;
			$class          = get_class( $object );

			switch ( $class ) {
				case 'WP_Post':
					$wp_query->queried_object = get_post( $object->ID );
					break;
				case 'WP_Term':
					$wp_query->queried_object = $object;
					break;
				default:
					$wp_query->queried_object = get_post( $object->get_id() );
					break;
			}

			$this->set_jet_engine_object( $object );

			$render_method = apply_filters(
				'jet-woo-builder/get-template-content/render-method',
				jet_woo_builder_shop_settings()->get( 'widgets_render_method', 'macros' )
			);

			if ( filter_var( jet_woo_builder_settings()->get( 'enable_inline_templates_styles' ), FILTER_VALIDATE_BOOLEAN ) ) {
				$with_css = true;
			}

			$with_css = apply_filters( 'jet-woo-builder/get-template-content/inline-styles', $with_css, $template_id );

			if ( 'elementor' === $render_method ) {
				$content = $this->render_elementor_content( $template_id, $with_css );
			} else {
				$builder_content = get_post_meta( $template_id, '_jet_woo_builder_content', true );

				if ( ! $builder_content ) {
					return null;
				}

				$this->set_elementor_data( $template_id );

				$content = $this->parse_content( $builder_content );

				$this->reset_jet_engine_object();
			}

			$wp_query->queried_object = $default_object;

			return $content;

		}

		/**
		 * Set JetEngine object
		 *
		 * @param $object
		 */
		public function set_jet_engine_object( $object ) {

			if ( ! function_exists( 'jet_engine' ) ) {
				return;
			}

			global $post;

			if ( is_a( $object, 'WC_Product' ) ) {
				$post_id = $object->get_id();
				$post    = get_post( $post_id );
			}

			if ( null === $this->jet_engine_object ) {
				$this->jet_engine_object = jet_engine()->listings->data->get_current_object();
			}

			jet_engine()->listings->data->set_current_object( $post );

		}

		/**
		 * Reset JetEngine object
		 */
		public function reset_jet_engine_object() {

			if ( ! function_exists( 'jet_engine' ) ) {
				return;
			}

			jet_engine()->listings->data->set_current_object( $this->jet_engine_object );

		}

		/**
		 * Render elementor content.
		 *
		 * Render content with usual Elementor method
		 *
		 * @since  1.7.0
		 * @since  2.1.7 get_builder_content_for_display() replaced with get_builder_content(), because caused errors
		 * in the editor after Elementor 3.15.0 update. It was related clearing controls stack before sending it to
		 * editor config.
		 * @access public
		 *
		 * @param string|int $template_id The template ID.
		 * @param boolean    $with_css    Optional. Whether to retrieve the content with CSS
		 *                                or not. Default is false.
		 *
		 * @return string The post content.
		 */
		public function render_elementor_content( $template_id, $with_css ) {
			return Elementor\Plugin::instance()->frontend->get_builder_content( $template_id, $with_css );
		}

		/**
		 * Returns parsed content
		 *
		 * @param $content
		 *
		 * @return string|string[]|null
		 */
		public function parse_content( $content ) {
			return preg_replace_callback( $this->macros_regex(), array( $this, 'replace_callback' ), $content );
		}

		/**
		 * Returns replaced parsed content
		 *
		 * @param $matches
		 *
		 * @return false|mixed|string
		 */
		public function replace_callback( $matches ) {

			if ( empty( $matches[1] ) ) {
				return $matches[0];
			}

			$widget = $this->get_widget_from_macros( $matches[1] );

			if ( ! $widget ) {
				return $matches[0];
			}

			if ( ! is_callable( array( $widget, 'render_callback' ) ) ) {
				return $matches[0];
			}

			$settings = array();

			if ( ! empty( $matches[3] ) ) {
				$settings = $this->get_parsed_settings( $matches[3] );
			}

			$args = apply_filters( 'jet-woo-builder/render-callback/custom-args', array() );

			ob_start();

			call_user_func( array( $widget, 'render_callback' ), $settings, $args );

			return ob_get_clean();

		}

		/**
		 * Get settings array from string
		 *
		 * @param $settings_string
		 *
		 * @return array
		 */
		public function get_parsed_settings( $settings_string ) {

			$settings_string = str_replace( '::', '', $settings_string );
			$raw             = explode( '&&', $settings_string );

			if ( empty( $raw ) ) {
				return array();
			}

			$settings = array();

			foreach ( $raw as $setting ) {
				$setting                 = explode( '="', $setting );
				$settings[ $setting[0] ] = rtrim( $setting[1], '"' );
			}

			return $settings;

		}

		/**
		 * Render macros string
		 *
		 * @param string $macros
		 * @param array  $settings
		 *
		 * @return string
		 */
		public function get_macros_string( $macros = '', $settings = array() ) {

			$settings_string = '';
			$sep             = '';

			if ( ! empty( $settings ) ) {

				foreach ( $settings as $key => $value ) {

					$settings_string .= '::';

					if ( is_array( $value ) ) {
						$value = implode( '|', $value );
					}

					$settings_string .= sprintf( '%3$s%1$s="%2$s"', $key, $value, $sep );

					$sep = '&&';
				}

			}

			return sprintf( '%%%%%1$s%2$s%%%%', $macros, $settings_string );

		}

		/**
		 * Get widget class name from macros
		 *
		 * @return string
		 */
		public function get_widget_from_macros( $macros ) {

			$class_name = str_replace( array( '-', '_' ), ' ', $macros );
			$class_name = ucwords( $class_name );
			$class_name = str_replace( ' ', '_', $class_name );
			$class_name = 'Elementor\\' . $class_name;

			if ( ! class_exists( $class_name ) ) {
				$file = glob( jet_woo_builder()->plugin_path( 'includes/widgets/' ) . 'archive-*/' . $macros . '.php' );

				if ( empty( $file ) ) {
					return null;
				}

				if ( ! file_exists( $file[0] ) ) {
					return null;
				} else {
					require $file[0];
				}
			}

			return $class_name;

		}

		/**
		 * Set Elementor data
		 *
		 * @param $post_id
		 *
		 * @return null
		 */
		public function set_elementor_data( $post_id ) {

			if ( in_array( $post_id, $this->processed_documents ) ) {
				return null;
			}

			$document = Elementor\Plugin::$instance->documents->get_doc_for_frontend( $post_id );

			// Change the current post, so widgets can use `documents->get_current`.
			Elementor\Plugin::$instance->documents->switch_to_document( $document );

			if ( $document->is_editable_by_current_user() ) {
				$this->admin_bar_edit_documents[ $document->get_main_id() ] = $document;
			}

			if ( $document->is_autosave() ) {
				$css_file = new Elementor\Core\Files\CSS\Post_Preview( $document->get_post()->ID );
			} else {
				$css_file = new Elementor\Core\Files\CSS\Post( $post_id );
			}

			$css_meta = $css_file->get_meta();

			if ( 'inline' === $css_meta['status'] ) {
				printf( '<style id="elementor-post-%1$s">%2$s</style>', $css_file->get_post_id(), $css_meta['css'] ); // XSS ok.
			} else {
				$css_file->enqueue();
			}

			$this->maybe_print_css_directly( $css_file );

			Elementor\Plugin::$instance->documents->restore_document();

			$this->processed_documents[] = $post_id;

		}

		/**
		 * Try print css directly
		 *
		 * @param $css_file
		 */
		public function maybe_print_css_directly( $css_file ) {

			$plugin = Elementor\Plugin::instance();

			if ( $plugin->editor->is_edit_mode() ) {
				printf( '<link rel="stylesheet" type="text/css" href="%s">', $css_file->get_url() );
			}

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
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
 * Returns instance of Jet_Woo_Builder_Parser
 *
 * @return object
 */
function class_name() {
	return Jet_Woo_Builder_Parser::get_instance();
}
