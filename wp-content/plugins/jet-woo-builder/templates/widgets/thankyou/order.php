<?php
/**
 * Thank You Page Order widget Template.
 */

defined( 'ABSPATH' ) || exit;

$order = jet_woo_builder_template_functions()->get_current_received_order();

if ( ! $order ) {
	return;
}

$settings = $this->get_settings_for_display();

$order_heading   = isset( $settings['thankyou_order_table_order_heading'] ) && ! empty( $settings['thankyou_order_table_order_heading'] ) ? $settings['thankyou_order_table_order_heading'] : 'Order number:';
$date_heading    = isset( $settings['thankyou_order_table_date_heading'] ) && ! empty( $settings['thankyou_order_table_date_heading'] ) ? $settings['thankyou_order_table_date_heading'] : 'Date:';
$email_heading   = isset( $settings['thankyou_order_table_email_heading'] ) && ! empty( $settings['thankyou_order_table_email_heading'] ) ? $settings['thankyou_order_table_email_heading'] : 'Email:';
$total_heading   = isset( $settings['thankyou_order_table_total_heading'] ) && ! empty( $settings['thankyou_order_table_total_heading'] ) ? $settings['thankyou_order_table_total_heading'] : 'Total:';
$payment_heading = isset( $settings['thankyou_order_table_payment_method_heading'] ) && ! empty( $settings['thankyou_order_table_payment_method_heading'] ) ? $settings['thankyou_order_table_payment_method_heading'] : 'Payment method:';


if ( $order->has_status( 'failed' ) ) : ?>

	<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
		<?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?>
	</p>

	<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
		<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
		<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
		<?php endif; ?>
	</p>

<?php else : ?>

	<?php wc_get_template( 'checkout/order-received.php', [ 'order' => $order ] ); ?>

	<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

		<li class="woocommerce-order-overview__order order">
			<?php esc_html_e( $order_heading, 'jet-woo-builder' ); ?>
			<strong><?php echo $order->get_order_number(); ?></strong>
		</li>

		<li class="woocommerce-order-overview__date date">
			<?php esc_html_e( $date_heading, 'jet-woo-builder' ); ?>
			<strong><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
		</li>

		<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
			<li class="woocommerce-order-overview__email email">
				<?php esc_html_e( $email_heading, 'jet-woo-builder' ); ?>
				<strong><?php echo $order->get_billing_email(); ?></strong>
			</li>
		<?php endif; ?>

		<li class="woocommerce-order-overview__total total">
			<?php esc_html_e( $total_heading, 'jet-woo-builder' ); ?>
			<strong><?php echo $order->get_formatted_order_total(); ?></strong>
		</li>

		<?php if ( $order->get_payment_method_title() ) : ?>
			<li class="woocommerce-order-overview__payment-method method">
				<?php esc_html_e( $payment_heading, 'jet-woo-builder' ); ?>
				<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
			</li>
		<?php endif; ?>

	</ul>

<?php endif; ?>

<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
