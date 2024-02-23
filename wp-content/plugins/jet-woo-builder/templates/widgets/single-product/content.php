<?php
/**
 * JetWooBuilder Single Content widget template.
 */

global $product;

if ( ! is_a( $product, 'WC_Product' ) ) {
	return;
}

$product_id   = $product->get_id();
$content_mode = get_post_meta( $product_id, '_elementor_edit_mode', true );
$content      = '';

if ( 'builder' === $content_mode && class_exists( 'Elementor\Plugin' ) ) {
	$elementor    = Elementor\Plugin::instance();
	$is_edit_mode = $elementor->editor->is_edit_mode();
	$content      = $elementor->frontend->get_builder_content( $product_id, $is_edit_mode );
} else {
	$content = get_the_content();
	$content = apply_filters( 'the_content', $content );
	$content = str_replace( ']]>', ']]&gt;', $content );
}

printf( '<div class="jet-single-content">%s</div>', do_shortcode( $content ) );
