<?php
/**
 * JetWooBuilder Products Grid widget loop start template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/loop-start.php.
 */

$settings         = $this->get_settings();
$equal            = $this->get_attr( 'equal_height_cols' );
$target_attr      = 'yes' === $this->get_attr( 'open_new_tab' ) ? 'target="_blank"' : '';
$hover_on_touch   = filter_var( $this->get_attr( 'hover_on_touch' ), FILTER_VALIDATE_BOOLEAN );
$hidden_products  = filter_var( $this->get_attr( 'hidden_products' ), FILTER_VALIDATE_BOOLEAN );
$carousel_enabled = isset( $settings['carousel_enabled'] ) ? filter_var( $settings['carousel_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;

$classes = [
	'jet-woo-products',
	'jet-woo-products--' . $this->get_attr( 'presets' ),
	$carousel_enabled ? 'swiper-wrapper' : 'col-row',
	jet_woo_builder_tools()->gap_classes( $this->get_attr( 'columns_gap' ), $this->get_attr( 'rows_gap' ) ),
	$equal ? 'jet-equal-cols' : '',
];

$attributes = apply_filters( 'jet-woo-builder/templates/jet-woo-products/widget-attributes', '', $settings, $query, $this );

printf( '<div class="%s" data-mobile-hover="%s" %s>', implode( ' ', $classes ), $hover_on_touch, $attributes );