<?php

if ( ! class_exists( 'Jet_Woo_Builder_Shortcode_Base' ) ) {
	#[AllowDynamicProperties]
	abstract class Jet_Woo_Builder_Shortcode_Base {

		/**
		 * Information about shortcode.
		 *
		 * @var array
		 */
		public $info = [];

		/**
		 * Shortcode settings.
		 *
		 * @var array
		 */
		public $settings = [];

		/**
		 * Shortcode attributes.
		 *
		 * @var array
		 */
		public $atts = [];

		public function __construct() {
			add_shortcode( $this->get_tag(), [ $this, 'do_shortcode' ] );
		}

		/**
		 * Returns shortcode tag. Should be rewritten in shortcode class.
		 *
		 * @return string
		 */
		public function get_tag() {}

		/**
		 * Returns shortcode name. Should be rewritten in shortcode class.
		 *
		 * @return string
		 */
		public function get_name() {}

		/**
		 * This function should be rewritten in shortcode class with attributes array.
		 *
		 * @return array
		 */
		public function get_atts() {
			return [];
		}

		/**
		 * Get attr.
		 *
		 * Retrieve single shortcode attribute.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @param string $name Attribute key.
		 *
		 * @return false|mixed
		 */
		public function get_attr( $name = '' ) {

			if ( isset( $this->atts[ $name ] ) ) {
				return $this->atts[ $name ];
			}

			$allowed = $this->get_atts();

			if ( isset( $allowed[ $name ] ) && isset( $allowed[ $name ]['default'] ) ) {
				return $allowed[ $name ]['default'];
			} else {
				return false;
			}

		}

		/**
		 * Hidden atts.
		 *
		 * Return hidden atts list.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return array
		 */
		public function hidden_atts() {
			return [
				'_element_id' => '',
			];
		}

		/**
		 * Get widget settings
		 *
		 * @return array
		 */
		public function get_settings() {
			return $this->settings;
		}

		/**
		 * Set widget settings.
		 *
		 * @param array $settings
		 *
		 * @return array
		 */
		public function set_settings( $settings = [] ) {

			$defaults = $this->default_settings();
			$settings = wp_parse_args( $settings, $defaults );

			foreach ( $defaults as $key => $default_value ) {
				if ( null === $settings[ $key ] ) {
					$settings[ $key ] = $default_value;
				}
			}

			return $this->settings = $settings;

		}

		/**
		 * Returns widget default settings.
		 *
		 * @return array
		 */
		public function default_settings() {
			return [];
		}

		/**
		 * Returns required widget settings.
		 *
		 * @return array
		 */
		public function get_required_settings() {

			$required = [];
			$settings = $this->get_settings();
			$default  = $this->default_settings();

			foreach ( $default as $key => $value ) {
				if ( isset( $settings[ $key ] ) ) {
					$required[ $key ] = $settings[ $key ];
				}
			}

			return $required;

		}

		/**
		 * This is main shortcode callback and it should be rewritten in shortcode class
		 *
		 * @param null $content [description]
		 *
		 * @return void
		 */
		public function _shortcode( $content = null ) {}

		/**
		 * Return default shortcode attributes
		 *
		 * @return array
		 */
		public function default_atts() {

			$result = [];

			foreach ( $this->get_atts() as $attr => $data ) {
				$result[ $attr ] = $data['default'] ?? false;
			}

			foreach ( $this->hidden_atts() as $attr => $default ) {
				$result[ $attr ] = $default;
			}

			return $result;

		}

		/**
		 * Shortcode callback
		 *
		 * @param array $atts
		 * @param null  $content
		 *
		 * @return string
		 */
		public function do_shortcode( $atts = [], $content = null ) {

			$atts              = shortcode_atts( $this->default_atts(), $atts, $this->get_tag() );
			$this->css_classes = [];

			if ( null !== $content ) {
				$content = do_shortcode( $content );
			}

			$this->atts = $atts;

			return $this->_shortcode( $content );

		}

		/**
		 * Get templates.
		 *
		 * @param string $name
		 *
		 * @return string
		 */
		public function get_template( $name = null ) {

			$template = jet_woo_builder()->get_template( $this->get_tag() . '/global/' . $name . '.php' );

			if ( ! $template ) {
				switch ( $this->get_tag() ) {
					case 'jet-woo-products':
						$widget = 'products-grid';

						break;
					case 'jet-woo-products-list':
						$widget = 'products-list';

						break;
					case 'jet-woo-categories':
						$widget = 'categories-grid';

						break;
					default:
						$widget = null;

						break;
				}

				$template = jet_woo_builder()->get_template( 'widgets/global/' . $widget . '/' . $name . '.php' );
			}

			return $template;

		}

	}
}
