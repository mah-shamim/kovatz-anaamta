<?php
/**
 * OceanWP Theme Integration
 */

// Single product wrapper
add_action( 'jet-woo-builder/blank-page/before-content', 'jet_woo_oceanwp_open_site_main_wrap', -999 );
add_action( 'jet-woo-builder/full-width-page/before-content', 'jet_woo_oceanwp_open_site_main_wrap', -999 );
add_action( 'jet-woo-builder/blank-page/after-content', 'jet_woo_oceanwp_close_site_main_wrap', 999 );
add_action( 'jet-woo-builder/full-width-page/after-content', 'jet_woo_oceanwp_close_site_main_wrap', 999 );

// WooCommerce hooks fix
if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
	add_action( 'elementor/widgets/register', 'jet_woo_oceanwp_fix_wc_hooks' );
} else {
	add_action( 'elementor/widgets/widgets_registered', 'jet_woo_oceanwp_fix_wc_hooks' );
}

// Enqueue styles
add_action( 'wp_enqueue_scripts', 'jet_woo_oceanwp_enqueue_styles' );

// Sidebar displaying
add_filter( 'ocean_post_layout_class', 'jet_woo_oceanwp_display_sidebar' );

// Default wrapper removing
add_action( 'wp', 'woocommerce_init' );

/**
 * Remove JetWooBuilder Default template actions
 */
function woocommerce_init() {
	remove_action( 'jet-woo-builder/woocommerce/before-main-content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'jet-woo-builder/woocommerce/after-main-content', 'woocommerce_output_content_wrapper_end', 10  );

	if ( class_exists( 'OceanWP_WooCommerce_Config' ) ) {
		add_action( 'jet-woo-builder/woocommerce/before-main-content', [ 'OceanWP_WooCommerce_Config', 'content_wrapper' ], 10 );
		add_action( 'jet-woo-builder/woocommerce/after-main-content', [ 'OceanWP_WooCommerce_Config', 'content_wrapper_end' ], 10 );
	}

}

/**
 * Display sidebar with jet-woo-builder templates.
 *
 * @param $class
 *
 * @return mixed|string
 */
function jet_woo_oceanwp_display_sidebar( $class ) {
	if ( get_post_type() === 'jet-woo-builder' ) {
		$class = 'full-width';
	}

	return $class;
}

/**
 * Fix WooCommerce hooks for OceanWP theme
 *
 * @return void
 */
function jet_woo_oceanwp_fix_wc_hooks() {
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
}

/**
 * Open .site-main wrapper for products
 *
 * @return void
 */
function jet_woo_oceanwp_open_site_main_wrap() {
	if ( ! is_singular( [ jet_woo_builder_post_type()->slug(), 'product' ] ) ) {
		return;
	}

	echo '<div class="site-main">';
}

/**
 * Close .site-main wrapper for products
 *
 * @return void
 */
function jet_woo_oceanwp_close_site_main_wrap() {
	if ( ! is_singular( [ jet_woo_builder_post_type()->slug(), 'product' ] ) ) {
		return;
	}

	echo '</div>';
}

/**
 * Enqueue OceanWP theme integration stylesheets.
 *
 * @return void
 * @since  1.0.0
 * @access public
 */
function jet_woo_oceanwp_enqueue_styles() {
	wp_enqueue_style(
		'jet-woo-builder-oceanwp',
		jet_woo_builder()->plugin_url( 'includes/compatibility/packages/themes/oceanwp/assets/css/style.css' ),
		false,
		jet_woo_builder()->get_version()
	);
}