<?php
/**
 * JetWooBuilder Products Grid widget loop item tags template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/item-tags.php.
 */

if ( 'yes' !== $this->get_attr( 'show_tag' ) ) {
	return;
}

$tags_count = ! empty( $settings['tags_count'] ) ? $settings['tags_count'] : 0;
$tags       = jet_woo_builder_template_functions()->get_product_terms_list( 'product_tag', $tags_count );

if ( ! $tags ) {
	return;
}
?>

<div class="jet-woo-product-tags">
	<ul><?php echo $tags; ?></ul>
</div>