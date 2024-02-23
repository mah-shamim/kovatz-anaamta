<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Shortcodes' ) ) {

	class Jet_Woo_Builder_Shortcodes {

		/**
		 * A reference to an instance of this class.
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Shortcodes holder.
		 *
		 * @var array
		 */
		private $shortcodes = [];

		public function init() {
			add_action( 'init', [ $this, 'register_shortcodes' ], 30 );
		}

		/**
		 * Register shortcodes.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function register_shortcodes() {

			require_once jet_woo_builder()->plugin_path( 'includes/shortcodes/traits/products-shortcode.php' );
			require_once jet_woo_builder()->plugin_path( 'includes/shortcodes/base.php' );

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/shortcodes/' ) . '*.php' ) as $file ) {
				$this->register_shortcode( $file );
			}

		}

		/**
		 * Register shortcode.
		 *
		 * Call shortcode instance from passed file.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @param string $file File name.
		 *
		 * @return void
		 */
		public function register_shortcode( $file ) {

			$base  = 'jet-woo-' . basename( str_replace( '.php', '', $file ) );
			$class = ucwords( str_replace( '-', ' ', $base ) );
			$class = str_replace( ' ', '_', $class );

			require_once $file;

			if ( ! class_exists( $class ) ) {
				return;
			}

			$shortcode = new $class;

			$this->shortcodes[ $shortcode->get_tag() ] = $shortcode;

		}

		/**
		 * Get shortcode.
		 *
		 * Returns shortcode class instance by tag.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @param string $tag Shortcode tag name.
		 *
		 * @return bool|mixed
		 */
		public function get_shortcode( $tag ) {
			return $this->shortcodes[ $tag ] ?? false;
		}

		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;

		}

	}

}

function jet_woo_builder_shortcodes() {
	return Jet_Woo_Builder_Shortcodes::get_instance();
}