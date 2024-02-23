<?php
/**
 * JetWooBuilder Products Grid widget loop item tags template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/item-compare.php.
 */

if ( isset( $settings['show_compare'] ) && 'yes' === $settings['show_compare'] ) {
	do_action( 'jet-woo-builder/templates/jet-woo-products/compare-button', $settings );
}
