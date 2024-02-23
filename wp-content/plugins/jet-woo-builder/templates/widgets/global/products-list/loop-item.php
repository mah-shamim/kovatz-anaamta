<?php
/**
 * Products list widget loop item template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-list/loop-item.php.
 */

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() && ! $hidden_products ) {
	return;
}

$product_id     = $product->get_id();
$permalink      = jet_woo_builder_template_functions()->get_product_permalink( $product );
$clickable_item = filter_var( $this->get_attr( 'clickable_item' ), FILTER_VALIDATE_BOOLEAN );
$clickable_data = '';
$classes        = [ 'jet-woo-products-list__inner-box' ];

if ( $clickable_item ) {
	$classes[]      = 'jet-woo-item-overlay-wrap';
	$clickable_data = 'data-url="' . $permalink . '"';

	if ( 'yes' === $this->get_attr( 'open_new_tab' ) ) {
		$clickable_data .= ' data-target="_blank"';
	}
}
?>

<li class="jet-woo-products-list__item jet-woo-builder-product" data-product-id="<?php echo $product_id ?>">
	<div class="<?php echo implode( ' ', $classes ); ?>" <?php echo $clickable_data; ?> >
		<div class="jet-woo-products-list__item-img">
			<?php include $this->get_template( 'item-thumb' ); ?>
		</div>

		<div class="jet-woo-products-list__item-content">
			<?php
			include $this->get_template( 'item-categories' );
			include $this->get_template( 'item-sku' );
			include $this->get_template( 'item-title' );
			include $this->get_template( 'item-price' );
			include $this->get_template( 'item-stock-status' );
			include $this->get_template( 'item-button' );
			include $this->get_template( 'item-rating' );
			include $this->get_template( 'item-compare' );
			include $this->get_template( 'item-wishlist' );
			include $this->get_template( 'item-quick-view' );
			?>
		</div>
	</div>

	<?php if ( $clickable_item ) : ?>
		<a href="<?php echo $permalink; ?>" class="jet-woo-item-overlay-link" <?php echo $target_attr; ?> ></a>
	<?php endif; ?>
</li>