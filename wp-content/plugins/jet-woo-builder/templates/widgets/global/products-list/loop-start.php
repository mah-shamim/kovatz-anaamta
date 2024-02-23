<?php
/**
 * Products list widget loop start template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-list/loop-start.php.
 */

$settings        = $this->get_settings();
$layout          = $this->get_attr( 'products_layout' );
$hidden_products = filter_var( $this->get_attr( 'hidden_products' ), FILTER_VALIDATE_BOOLEAN );
$target_attr     = 'yes' === $this->get_attr( 'open_new_tab' ) ? 'target="_blank"' : '';

$classes = [
	'jet-woo-products-list',
	$layout ? 'products-layout-' . $layout : '',
];

$attributes = apply_filters( 'jet-woo-builder/templates/jet-woo-products-list/widget-attributes', '', $settings, $query, $this );

printf( '<ul class="%s" %s >', implode( ' ', $classes ), $attributes );
