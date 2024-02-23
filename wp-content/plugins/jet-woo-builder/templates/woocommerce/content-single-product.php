<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/woocommerce/content-single-product.php.
 *
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( jet_woo_builder()->elementor_views->in_elementor() ) {
	if ( is_product() || ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) {
		$format  = '<h5>%s</h5>';
		$message = __( 'JetWooBuilder Template is enabled, however, it can&rsquo;t be displayed in shortcode when you&rsquo;re on Elementor editor page.', 'jet-woo-builder' );

		printf( $format, $message );

		return;
	}
}

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}

$template = apply_filters( 'jet-woo-builder/current-template/template-id', jet_woo_builder()->woocommerce->get_custom_single_template() );

jet_woo_builder()->admin_bar->register_post_item( $template );
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?> >
	<?php echo jet_woo_builder_template_functions()->get_woo_builder_content( $template ); ?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
