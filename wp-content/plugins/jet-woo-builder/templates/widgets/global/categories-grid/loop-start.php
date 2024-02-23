<?php
/**
 * JetWooBuilder Categories Grid widget loop start template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/categories-grid/loop-start.php.
 */

$settings         = $this->get_settings();
$equal            = $this->get_attr( 'equal_height_cols' );
$carousel_enabled = filter_var( $settings['carousel_enabled'], FILTER_VALIDATE_BOOLEAN );
$hover_on_touch   = filter_var( $this->get_attr( 'hover_on_touch' ), FILTER_VALIDATE_BOOLEAN );
$target_attr      = 'yes' === $this->get_attr( 'open_new_tab' ) ? 'target="_blank"' : '';

$classes = [
	'jet-woo-categories',
	'jet-woo-categories--' . $this->get_attr( 'presets' ),
	$carousel_enabled ? 'swiper-wrapper' : 'col-row',
	jet_woo_builder_tools()->gap_classes( $this->get_attr( 'columns_gap' ), $this->get_attr( 'rows_gap' ) ),
	$equal ? 'jet-equal-cols' : '',
];

printf( '<div class="%s" data-mobile-hover="%s">', implode( ' ', $classes ), $hover_on_touch );
