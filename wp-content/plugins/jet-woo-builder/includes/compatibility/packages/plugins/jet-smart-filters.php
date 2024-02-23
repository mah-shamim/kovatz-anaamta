<?php
/**
 * JetSmartFilters compatibility package.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Smart_Filters_Package' ) ) {
	class Jet_Woo_Builder_Smart_Filters_Package {

		public function __construct() {
			add_filter( 'jet-woo-builder/shortcodes/jet-woo-products/not-found-message', [ $this, 'modify_products_message' ] );
			add_filter( 'jet-woo-builder/shortcodes/jet-woo-products-list/not-found-message', [ $this, 'modify_products_message' ] );
		}

		/**
		 * Modify products message.
		 *
		 * Return products not found message after filtration.
		 *
		 * @since  2.1.0
		 * @access public
		 *
		 * @param string $message Not found message.
		 *
		 * @return string
		 */
		public function modify_products_message( $message ) {
			$message = str_replace( '\\', '', $message );
			return do_shortcode( $message );
		}

	}
}

new Jet_Woo_Builder_Smart_Filters_Package();
