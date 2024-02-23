<?php
/**
 * JetWooBuilder Products Grid widget loop item title template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/item-title.php.
 */

if ( 'yes' !== $this->get_attr( 'show_title' ) ) {
	return;
}

$full_title = jet_woo_builder_template_functions()->get_product_title();
$title      = jet_woo_builder_tools()->trim_text(
	$full_title,
	$this->get_attr( 'title_length' ),
	$this->get_attr( 'title_trim_type' ),
	'...'
);

if ( empty( $title ) ) {
	return;
}

$title_tag     = jet_woo_builder_tools()->sanitize_html_tag( $this->get_attr( 'title_html_tag' ) );
$title_tooltip = '';

if ( -1 !== $this->get_attr( 'title_length' ) && 'yes' === $this->get_attr( 'title_tooltip' ) ) {
	$title_tooltip = 'title="' . $full_title . '"';
}

$open_wrap  = '<' . $title_tag . ' class="jet-woo-product-title" ' . $title_tooltip . '>';
$close_wrap = '</' . $title_tag . '>';

if ( 'yes' === $this->get_attr( 'add_title_link' ) ) {
	$open_wrap  .= '<a href="' . $permalink . '" ' . $target_attr . '>';
	$close_wrap = '</a>' . $close_wrap;
}

echo $open_wrap . $title . $close_wrap;
