<?php
/**
 * Astra Theme Integration.
 */

add_action( 'jet-smart-filters/providers/woocommerce-archive/before-ajax-content', 'jet_woo_astra_compatibility', 1 );
add_action( 'wp_enqueue_scripts', 'jet_woo_astra_enqueue_styles' );

/**
 * Astra theme compatibility
 */
function jet_woo_astra_compatibility() {
	if ( class_exists( 'Astra_Woocommerce' ) ) {
		$astra = new Astra_Woocommerce();

		if ( ! apply_filters( 'astra_woo_shop_product_structure_override', false ) ) {
			$astra->shop_customization();
		}

		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	}
}

/**
 * Disable custom checkout customization.
 */
add_action( 'wp', function () {
	if ( 'yes' === jet_woo_builder_shop_settings()->get( 'custom_checkout_page' ) && 'default' !== jet_woo_builder_shop_settings()->get( 'checkout_template' ) ) {
		remove_action( 'woocommerce_checkout_billing', [ WC()->checkout(), 'checkout_form_shipping' ] );
	}
} );

/**
 * Handle sale badges issue.
 */
if ( ! defined( 'ASTRA_EXT_VER' ) || ( defined( 'ASTRA_EXT_VER' ) && ! Astra_Ext_Extension::is_active( 'woocommerce' ) ) ) {
	add_filter( 'jet-woo-builder/template-functions/product-sale-flash', function ( $html, $product, $settings, $label ) {
		return sprintf( '<div class="jet-woo-product-badge jet-woo-product-badge__sale">%s</div>', $label );
	}, 10, 4 );
}

/**
 * Enqueue Astra integration stylesheets.
 *
 * @since  1.0.0
 * @access public
 *
 * @return void
 */
function jet_woo_astra_enqueue_styles() {
	wp_enqueue_style(
		'jet-woo-builder-astra',
		jet_woo_builder()->plugin_url( 'includes/compatibility/packages/themes/astra/assets/css/style.css' ),
		false,
		jet_woo_builder()->get_version()
	);
}