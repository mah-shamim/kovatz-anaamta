<?php
/**
 * JetWooBuilder Products Grid widget loop item stock status template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/item-stock-status.php.
 */

if ( 'yes' !== $this->get_attr( 'show_stock_status' ) ) {
	return;
}

$on_backorder = esc_html__( $this->get_attr( 'on_backorder_status_text' ), 'jet-woo-builder' );
$in_stock     = esc_html__( $this->get_attr( 'in_stock_status_text' ), 'jet-woo-builder' );
$out_of_stock = esc_html__( $this->get_attr( 'out_of_stock_status_text' ), 'jet-woo-builder' );
$stock_status = jet_woo_builder_template_functions()->get_custom_product_stock_status( $in_stock, $on_backorder,  $out_of_stock );

if ( empty( $stock_status ) ) {
	return;
}
?>

<div class="jet-woo-product-stock-status"><?php echo $stock_status; ?></div>
