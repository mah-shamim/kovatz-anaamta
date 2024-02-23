<?php
/**
 * JetWooBuilder Products List widget loop item price template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-list/item-price.php.
 */

if ( 'yes' !== $this->get_attr( 'show_price' ) ) {
	return;
}

$price = jet_woo_builder_template_functions()->get_product_price();

if ( empty( $price ) ) {
	return;
}
?>

<div class="jet-woo-product-price"><?php echo $price; ?></div>