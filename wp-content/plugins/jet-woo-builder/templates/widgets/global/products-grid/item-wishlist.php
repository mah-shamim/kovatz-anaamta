<?php
/**
 * JetWooBuilder Products Grid widget loop item wishlist button template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/item-wishlist.php.
 */

if ( isset( $settings['show_wishlist'] ) && 'yes' === $settings['show_wishlist'] ) {
	do_action( 'jet-woo-builder/templates/jet-woo-products/wishlist-button', $settings );
}