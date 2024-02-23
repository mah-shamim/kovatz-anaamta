<?php
/**
 * Booking product add to cart template.
 *
 * @var WC_Product_Booking $product
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;
global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

if ( yith_wcbk()->settings->show_booking_form_to_logged_users_only() && ! is_user_logged_in() ) {
	echo wp_kses_post( apply_filters( 'yith_wcbk_show_booking_form_to_logged_users_only_non_logged_text', '<p>' . __( 'You must be logged in to book this product!', 'yith-booking-for-woocommerce' ) . '</p>' ) );

	if ( apply_filters( 'yith_wcbk_show_booking_form_to_logged_users_only_show_login_form', true ) ) {
		yith_wcbk_print_login_form( false, false );
	}

	return;
}

if ( ! apply_filters( 'yith_wcbk_show_booking_form', true ) ) {
	return;
}

$form_action         = ! $product->is_confirmation_required() ? 'add-to-cart' : 'booking-request-confirmation';
$add_to_cart_classes = array( 'yith-wcbk-add-to-cart-button', 'single_add_to_cart_button', 'button', 'alt' );

if ( $product->is_confirmation_required() ) {
	$add_to_cart_classes[] = 'yith-wcbk-add-to-cart-button--confirmation-required';
}
$add_to_cart_classes = implode( ' ', array_filter( $add_to_cart_classes ) );

?>
<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart" method="post" enctype='multipart/form-data'>

	<input type="hidden" name="<?php echo esc_attr( $form_action ); ?>" value="<?php echo esc_attr( $product->get_id() ); ?>"/>

	<?php
	do_action( 'yith_wcbk_before_booking_form' );

	/**
	 * Hook: yith_wcbk_booking_form_start.
	 *
	 * @hooked yith_wcbk_booking_form_start - 10
	 */
	do_action( 'yith_wcbk_booking_form_start', $product );

	/**
	 * Hook yith_wcbk_booking_form_meta.
	 *
	 * @hooked yith_wcbk_booking_form_meta - 10
	 */
	do_action( 'yith_wcbk_booking_form_meta', $product );

	/**
	 * Hook yith_wcbk_booking_form_fields.
	 *
	 * @hooked yith_wcbk_booking_form_dates - 10
	 * @hooked yith_wcbk_booking_form_persons - 20
	 * @hooked yith_wcbk_booking_form_services - 30
	 */
	do_action( 'yith_wcbk_booking_form_content', $product );

	/**
	 * Hook: yith_wcbk_booking_form_message.
	 *
	 * @hooked yith_wcbk_booking_form_message - 10
	 */
	do_action( 'yith_wcbk_booking_form_message', $product );

	/**
	 * Hook: yith_wcbk_booking_form_end.
	 *
	 * @hooked yith_wcbk_booking_form_end - 10
	 */
	do_action( 'yith_wcbk_booking_form_end', $product );
	?>

	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<?php do_action( 'yith_wcbk_booking_before_add_to_cart_button' ); ?>

	<button type="submit" class="<?php echo esc_attr( $add_to_cart_classes ); ?>"
			disabled><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

	<?php do_action( 'yith_wcbk_booking_after_add_to_cart_button' ); ?>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
