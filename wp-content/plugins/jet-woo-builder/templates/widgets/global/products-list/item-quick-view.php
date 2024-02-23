<?php
/**
 * JetWooBuilder Products List widget loop item quick view button template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-list/item-quick-view.php.
 */


if ( isset( $settings['show_quickview'] ) && 'yes' === $settings['show_quickview'] ) {
	do_action( 'jet-woo-builder/templates/jet-woo-products-list/quickview-button', $settings );
}
