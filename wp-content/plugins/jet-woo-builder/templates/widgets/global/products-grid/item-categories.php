<?php
/**
 * JetWooBuilder Products Grid widget loop item categories template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/item-categories.php.
 */

if ( 'yes' !== $this->get_attr( 'show_cat' ) ) {
	return;
}

$cat_count  = ! empty( $settings['categories_count'] ) ? $settings['categories_count'] : 0;
$categories = jet_woo_builder_template_functions()->get_product_terms_list( 'product_cat', $cat_count );

if ( ! $categories ) {
	return;
}
?>

<div class="jet-woo-product-categories">
	<ul><?php echo $categories; ?></ul>
</div>