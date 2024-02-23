<?php
/**
 * JetWooBuilder Products Grid widget loop item price template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/products-grid/item-rating.php.
 */

if ( 'yes' !== $this->get_attr( 'show_rating' ) ) {
	return;
}

$show_empty = filter_var( $this->get_attr( 'show_rating_empty' ), FILTER_VALIDATE_BOOLEAN );
$rating     = jet_woo_builder_template_functions()->get_product_rating( $show_empty );
$classes    = [ 'jet-woo-product-rating' ];

if ( $show_empty && empty( $product->get_average_rating() ) ) {
	$classes[] = 'empty';
}

if ( empty( $rating ) ) {
	return;
}
?>

<div class="<?php echo implode( ' ', $classes ); ?>"><?php echo $rating; ?></div>