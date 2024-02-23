<?php
/**
 * JetWooBuilder Products Grid widget loop item template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/loop-item.php.
 */

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() && ! $hidden_products ) {
	return;
}

$product_id          = $product->get_id();
$classes             = [ 'jet-woo-builder-product' ];
$permalink           = jet_woo_builder_template_functions()->get_product_permalink( $product );
$enable_thumb_effect = filter_var( jet_woo_builder_settings()->get( 'enable_product_thumb_effect' ), FILTER_VALIDATE_BOOLEAN );
$clickable_item      = filter_var( $this->get_attr( 'clickable_item' ), FILTER_VALIDATE_BOOLEAN );
$clickable_data      = '';
$box_classes         = [ 'jet-woo-products__inner-box' ];

if ( $enable_thumb_effect ) {
	$classes[] = 'jet-woo-thumb-with-effect';
}

if ( $carousel_enabled ) {
	$classes[] = 'swiper-slide';
}

if ( $clickable_item ) {
	$box_classes[]  = 'jet-woo-item-overlay-wrap';
	$clickable_data = 'data-url="' . $permalink . '"';

	if ( 'yes' === $this->get_attr( 'open_new_tab' ) ) {
		$clickable_data .= ' data-target="_blank"';
	}
}
?>

<div class="jet-woo-products__item <?php echo implode( ' ', $classes ); ?>" data-product-id="<?php echo $product_id ?>">
	<div class="<?php echo implode( ' ', $box_classes ); ?>" <?php echo $clickable_data; ?> >
		<?php include $this->get_product_preset_template(); ?>
	</div>

	<?php if ( $clickable_item ) : ?>
		<a href="<?php echo $permalink; ?>" class="jet-woo-item-overlay-link" <?php echo $target_attr; ?> ></a>
	<?php endif; ?>
</div>