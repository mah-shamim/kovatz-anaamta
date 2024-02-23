<?php
/**
 * JetWooBuilder Products Grid widget loop item thumbnail template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/item-thumb.php.
 */

$size         = $this->get_attr( 'thumb_size' );
$thumb_effect = 'yes' === $this->get_attr( 'enable_thumb_effect' );
$thumbnail    = jet_woo_builder_template_functions()->get_product_thumbnail( $size, $thumb_effect );

if ( ! $thumbnail ) {
	return;
}

$badge_text   = jet_woo_builder()->macros->do_macros( $this->get_attr( 'sale_badge_text' ) );
$sale_badge = jet_woo_builder_template_functions()->get_product_sale_flash( $badge_text, $settings );
$open_link  = '';
$close_link = '';

if ( 'yes' === $this->get_attr( 'add_thumb_link' ) ) {
	$open_link  = '<a href="' . $permalink . '" ' . $target_attr . '>';
	$close_link = '</a>';
}
?>

<div class="jet-woo-product-thumbnail">
	<?php do_action( 'jet-woo-builder/templates/products/before-item-thumbnail' ) ?>

	<?php echo $open_link . $thumbnail . $close_link; ?>

	<div class="jet-woo-product-img-overlay"></div>

	<?php
	if ( ! empty( $sale_badge ) && 'yes' === $this->get_attr( 'show_badges' ) ) {
		echo sprintf( '<div class="jet-woo-product-badges">%s</div>', $sale_badge );
	}
	?>

	<?php do_action( 'jet-woo-builder/templates/products/after-item-thumbnail' ) ?>
</div>