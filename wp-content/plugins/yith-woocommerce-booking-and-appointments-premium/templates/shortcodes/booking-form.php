<?php
/**
 * Booking form shortcode Template
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

global $product, $post;
?>
<div class="woocommerce">
	<div id="product-<?php echo esc_attr( $product->get_id() ); ?>" class="product type-product yith-wcbk-shortcode-booking-form">
		<div class="yith_wcbk_booking_form_shortcode_summary">
			<?php
			/**
			 * Hook: yith_wcbk_booking_form_shortcode_before_add_to_cart_form.
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 */
			do_action( 'yith_wcbk_booking_form_shortcode_before_add_to_cart_form' );

			do_action( 'yith_wcbk_booking_add_to_cart_form' );

			/**
			 * Hook: yith_wcbk_booking_form_shortcode_after_add_to_cart_form.
			 *
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 */
			do_action( 'yith_wcbk_booking_form_shortcode_after_add_to_cart_form' );

			?>
		</div><!-- .summary -->
	</div>
</div>
