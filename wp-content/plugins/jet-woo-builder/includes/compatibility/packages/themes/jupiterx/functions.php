<?php
/**
 * JupiterX Theme Integration
 */

// Enqueue styles
add_action( 'wp_enqueue_scripts', 'jet_woo_jupiterx_enqueue_styles' );

// Set navigation direction
add_filter( 'jet-woo-builder/jet-single-image-template/navigation-direction', 'set_jupiterx_single_image_nav_direction' );

/**
 * Set JupiterX theme product gallery navigation orientation thought Single Image widget control
 *
 * @param $direction
 *
 * @return mixed
 */
function set_jupiterx_single_image_nav_direction( $direction ) {
	set_theme_mod( 'jupiterx_product_page_image_gallery_orientation', $direction );

	return $direction;
}

/**
 * Enqueue JupiterX theme integration stylesheets
 *
 * @return void
 * @since  1.7.5
 * @access public
 */
function jet_woo_jupiterx_enqueue_styles() {
	wp_enqueue_style(
		'jet-woo-builder-jupiterx',
		jet_woo_builder()->plugin_url( 'includes/compatibility/packages/themes/jupiterx/assets/css/style.css' ),
		false,
		jet_woo_builder()->get_version()
	);
}
