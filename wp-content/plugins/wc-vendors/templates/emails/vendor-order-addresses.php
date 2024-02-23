<?php
/**
 * Vendor Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/vendor-order-addresses.php.
 *
 * @author  Jamie Madden, WC Vendors
 * @package WCVendors/Templates/Emails
 * @version 2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text_align = is_rtl() ? 'right' : 'left';
?>
<table id="addresses" cellspacing="0" cellpadding="0"
        style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;" border="0">
	<tr>
		<td style="text-align:<?php echo esc_attr( $text_align ); ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;"
		    valign="top" width="50%">
			<?php if ( $show_billing_address || $show_customer_billing_name || $show_customer_phone || $show_customer_email ) : ?>
				<h2><?php esc_html_e( 'Billing address', 'wc-vendors' ); ?></h2>

				<address class="address">
					<?php if ( $show_customer_billing_name ) : ?>
						<?php echo esc_html( $customer_billing_name ); ?><br/>
					<?php endif; ?>
					<?php if ( $show_billing_address ) : ?>
						<?php echo ( $address = $order->get_formatted_billing_address() ) ? wp_kses( $address, array( 'br' => array() ) ) : esc_html__( 'N/A', 'wc-vendors' ); ?>
					<?php endif; ?>
					<?php if ( $show_customer_phone ) : ?>
						<?php if ( $order->get_billing_phone() ) : ?>
							<br/><?php echo wc_make_phone_clickable( esc_html( $order->get_billing_phone() ) ); //phpcs:ignore ?>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( $show_customer_email ) : ?>
						<?php if ( $order->get_billing_email() ) : ?>
							<p><?php echo esc_html( $order->get_billing_email() ); ?></p>
						<?php endif; ?>
					<?php endif; ?>
				</address>
			<?php endif; ?>
		</td>
		<?php
        if ( $show_shipping_address || $show_customer_shipping_name ) :
			$shipping = $order->get_formatted_shipping_address();
		?>
			<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $shipping ) : ?>
				<td style="text-align:<?php echo esc_attr( $text_align ); ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding:0;"
				    valign="top" width="50%">
					<h2><?php esc_html_e( 'Shipping address', 'wc-vendors' ); ?></h2>

					<address class="address">
						<?php if ( $show_customer_shipping_name ) : ?>
							<?php echo wp_kses_post( $customer_shipping_name ); ?><br/>
						<?php endif; ?>
						<?php if ( $show_shipping_address ) : ?>
							<?php echo wp_kses_post( $shipping, array( 'br' => array() ) ); ?>
							<br /><?php echo wc_make_phone_clickable( $order->get_shipping_phone() ); //phpcs:ignore ?>
						<?php endif; ?>
					</address>
				</td>
			<?php endif; ?>
		<?php endif; ?>
	</tr>
</table>
