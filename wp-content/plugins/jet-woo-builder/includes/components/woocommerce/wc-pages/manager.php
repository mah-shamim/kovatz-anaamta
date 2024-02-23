<?php
/**
 * WooCommerce pages manager class.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_WC_Pages_Manager' ) ) {

	class Jet_Woo_Builder_WC_Pages_Manager {

		function __construct() {
			add_action( 'jet-woo-builder/components/woocommerce/init', [ $this, 'init' ] );
		}

		/**
		 * Init.
		 *
		 * Initialize WooCommerce pages components.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @return void
		 */
		public function init() {
			if ( 'yes' === jet_woo_builder_shop_settings()->get( 'custom_checkout_page' ) ) {
				require jet_woo_builder()->plugin_path( 'includes/components/woocommerce/wc-pages/checkout-page.php' );
				new Jet_Woo_Builder_Checkout_Page();
			}
		}

	}

}
