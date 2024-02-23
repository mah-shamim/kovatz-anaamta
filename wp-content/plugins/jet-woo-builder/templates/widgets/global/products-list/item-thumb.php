<?php
/**
 * JetWooBuilder Products List widget loop item thumbnail template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-list/item-thumb.php.
 */

if ( 'yes' !== $this->get_attr( 'show_image' ) ) {
	return;
}

$size      = $this->get_attr( 'thumb_size' );
$thumbnail = jet_woo_builder_template_functions()->get_product_thumbnail( $size );

if ( empty( $thumbnail ) ) {
	return;
}

$badge_text   = jet_woo_builder()->macros->do_macros( $this->get_attr( 'sale_badge_text' ) );
$sale_badge = jet_woo_builder_template_functions()->get_product_sale_flash( $badge_text, $settings );
$open_link  = '';
$close_link = '';

if ( isset( $settings['is_linked_image'] ) && 'yes' === $settings['is_linked_image'] ) {
	$open_link  = '<a href="' . $permalink . '" ' . $target_attr . '>';
	$close_link = '</a>';
}
?>

<div class="jet-woo-product-thumbnail">
	<?php
	do_action( 'jet-woo-builder/templates/products-list/before-item-thumbnail' );

	echo $open_link . $thumbnail . $close_link;

	if ( ! empty( $sale_badge ) && 'yes' === $this->get_attr( 'show_badges' ) ) {
		echo sprintf( '<div class="jet-woo-product-badges">%s</div>', $sale_badge );
	}

	do_action( 'jet-woo-builder/templates/products-list/after-item-thumbnail' );
	?>
</div>