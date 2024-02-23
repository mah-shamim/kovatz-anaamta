<?php
/**
 * JetWooBuilder Products List widget loop item compare button template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-list/item-compare.php.
 */

if ( isset( $settings['show_compare'] ) && 'yes' === $settings['show_compare'] ) {
	do_action( 'jet-woo-builder/templates/jet-woo-products-list/compare-button', $settings );
}