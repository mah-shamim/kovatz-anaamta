<?php
/**
 * JetWooBuilder Categories Grid widget loop item title template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/categories-grid/item-title.php.
 */

if ( 'yes' !== $this->get_attr( 'show_title' ) ) {
	return;
}

$title         = jet_woo_builder_tools()->trim_text(
	$category->name,
	$this->get_attr( 'title_length' ),
	$this->get_attr( 'title_trim_type' ),
	'...'
);
$title_tag     = jet_woo_builder_tools()->sanitize_html_tag( $this->get_attr( 'title_html_tag' ) );
$title_tooltip = '';

if ( -1 !== $this->get_attr( 'title_length' ) && 'yes' === $this->get_attr( 'title_tooltip' ) ) {
	$title_tooltip = 'title="' . $category->name . '"';
}

echo '<' . $title_tag . ' class="jet-woo-category-title" ' .$title_tooltip . '>';
echo '<a href="' . $permalink . '" class="jet-woo-category-title__link" ' . $target_attr . '>' . $title . '</a>';
echo '</' . $title_tag . '>';
