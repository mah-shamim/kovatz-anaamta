<?php
/**
 * JetWooBuilder Products Grid widget loop item SKU template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/item-sku.php.
 */

if ( 'yes' !== $this->get_attr( 'show_sku' ) ) {
	return;
}

$sku = jet_woo_builder_template_functions()->get_product_sku();

if ( empty( $sku ) ) {
	return;
}
?>

<div class="jet-woo-product-sku"><?php echo $sku; ?></div>