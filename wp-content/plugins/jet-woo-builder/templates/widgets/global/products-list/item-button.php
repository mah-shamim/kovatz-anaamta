<?php
/**
 * JetWooBuilder Products List widget loop item add to cart button template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-list/item-button.php.
 */

if ( 'yes' !== $this->get_attr( 'show_button' ) ) {
	return;
}

$classes         = [ 'jet-woo-product-button' ];
$enable_quantity = 'yes' === $this->get_attr( 'show_quantity' );

if ( 'yes' === $this->get_attr( 'button_use_ajax_style' ) ) {
	$classes[] = 'is--default';
}
?>

<div class="<?php echo implode( ' ', $classes ); ?>">
	<?php jet_woo_builder_template_functions()->get_product_add_to_cart_button( [], $enable_quantity ); ?>
</div>