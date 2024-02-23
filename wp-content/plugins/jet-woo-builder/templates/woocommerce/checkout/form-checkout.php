<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/woocommerce/checkout/form-checkout.php.
 *
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$template     = apply_filters( 'jet-woo-builder/current-template/template-id', jet_woo_builder()->woocommerce->get_custom_checkout_template() );
$top_template = apply_filters( 'jet-woo-builder/current-template/template-id', jet_woo_builder()->woocommerce->get_custom_top_checkout_template() );

jet_woo_builder()->admin_bar->register_post_item( $template );
jet_woo_builder()->admin_bar->register_post_item( $top_template );

if ( $top_template ) {
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form' );
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form' );

	echo jet_woo_builder_template_functions()->get_woo_builder_content( $top_template );
}

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'jet-woo-builder' ) ) );
	return;
}

?>

<div class="jet-woo-builder-woocommerce-checkout">
	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
		<?php echo jet_woo_builder_template_functions()->get_woo_builder_content( $template ); ?>
	</form>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
