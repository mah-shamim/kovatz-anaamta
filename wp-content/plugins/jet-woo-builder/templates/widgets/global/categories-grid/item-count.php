<?php
/**
 * JetWooBuilder Categories Grid widget loop item count template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/categories-grid/item-count.php.
 */

if ( 'yes' !== $this->get_attr( 'show_count' ) ) {
	return;
}

printf(
	'<span class="jet-woo-category-count">%2$s %1$s %3$s</span>',
	$category->count,
	esc_html__( $this->get_attr( 'count_before_text' ), 'jet-woo-builder' ),
	esc_html__( $this->get_attr( 'count_after_text' ), 'jet-woo-builder' )
);
