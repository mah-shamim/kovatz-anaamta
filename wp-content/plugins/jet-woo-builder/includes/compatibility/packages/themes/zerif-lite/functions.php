<?php
/**
 * Zerif Lite Theme Integration
 */

// Single product wrapper
add_action( 'jet-woo-builder/blank-page/before-content', 'jet_woo_zeriflite_open_site_main_wrap', -999 );
add_action( 'jet-woo-builder/full-width-page/before-content', 'jet_woo_zeriflite_open_site_main_wrap', -999 );
add_action( 'jet-woo-builder/blank-page/after-content', 'jet_woo_zeriflite_close_site_main_wrap', 999 );
add_action( 'jet-woo-builder/full-width-page/after-content', 'jet_woo_zeriflite_close_site_main_wrap', 999 );

// Enqueue styles
add_action( 'wp_enqueue_scripts', 'jet_woo_zeriflite_enqueue_styles' );

/**
 * Open .site-main wrapper for products
 *
 * @return void
 */
function jet_woo_zeriflite_open_site_main_wrap() {
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
function jet_woo_zeriflite_close_site_main_wrap() {
	if ( ! is_singular( [ jet_woo_builder_post_type()->slug(), 'product' ] ) ) {
		return;
	}

	echo '</div>';
}

/**
 * Enqueue Zerif Lite theme integration stylesheets.
 *
 * @return void
 * @since  1.0.0
 * @access public
 */
function jet_woo_zeriflite_enqueue_styles() {
	wp_enqueue_style(
		'jet-woo-builder-zeriflite',
		jet_woo_builder()->plugin_url( 'includes/compatibility/packages/themes/zerif-lite/assets/css/style.css' ),
		false,
		jet_woo_builder()->get_version()
	);
}