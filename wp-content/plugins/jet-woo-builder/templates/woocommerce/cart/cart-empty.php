<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/woocommerce/cart/cart-empty.php.
 *
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

$template = apply_filters( 'jet-woo-builder/current-template/template-id', jet_woo_builder()->woocommerce->get_custom_empty_cart_template() );

jet_woo_builder()->admin_bar->register_post_item( $template );
?>

<div class="jet-woo-builder-woocommerce-empty-cart">
	<?php
	$content =  jet_woo_builder_template_functions()->get_woo_builder_content( $template, true );

	if (  apply_filters( 'jet-woo-builder/cart/empty-message', true ) ) {
		do_action( 'woocommerce_cart_is_empty' );
	}

	echo $content;
	?>
</div>
