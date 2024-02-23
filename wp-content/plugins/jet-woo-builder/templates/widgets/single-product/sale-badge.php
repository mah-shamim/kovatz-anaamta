<?php
/**
 * JetWooBuilder Single Sale Badge widget template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/single-product/sale-badge.php.
 */

global $post, $product;

if ( ! is_a( $product, 'WC_Product' ) ) {
	return;
}

$settings = $this->get_settings_for_display();

$badge_text = jet_woo_builder()->macros->do_macros( $settings['single_badge_text'] );
$badge      = sprintf( '<span class="onsale">%s</span>', esc_html__( $badge_text, 'jet-woo-builder' ) );
$badge      = apply_filters( 'jet-woo-builder/templates/single-product/sale-badge', $badge, $product, $settings );
$on_sale    = apply_filters( 'jet-woo-builder/templates/single-product/sale-badge/on-sale', $product->is_on_sale(), $product, $settings );

if ( $on_sale ) {
	echo apply_filters( 'woocommerce_sale_flash', $badge, $post, $product );
}
