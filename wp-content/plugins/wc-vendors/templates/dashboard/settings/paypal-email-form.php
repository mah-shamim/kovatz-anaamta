<?php
/**
 * Shop Name Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/dashboard/settings/paypal-email-form.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Emails/HTML
 * @version       2.0.0
 * @version       2.4.3 - Added support for PayPal Masspay web
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="pv_paypal_container show-if" data-control="wcv_commission_payout_method" data-control-value="paypal">
	<p>
		<b><?php esc_html_e( 'PayPal Payout Method', 'wc-vendors' ); ?></b><br/>
		<?php esc_html_e( 'Choose how you want your commission payout via PayPal.', 'wc-vendors' ); ?><br/>
		<?php $wcv_paypal_wallet = get_user_meta( $user_id, 'wcv_paypal_masspay_wallet', true ); ?>
		<?php echo wcv_paypal_masspay_walet_select( $wcv_paypal_wallet ) // phpcs:ignore ?>
		
	</p>
	<p class="show-if" data-control="wcv_paypal_masspay_wallet" data-control-value="paypal">
		<b><?php esc_html_e( 'PayPal Address', 'wc-vendors' ); ?></b><br/>
		<?php esc_html_e( 'Your PayPal address can be used to manually send you your commission.', 'wc-vendors' ); ?><br/>

		<input type="email" name="pv_paypal" id="pv_paypal" placeholder="some@email.com"
				value="<?php echo esc_attr( get_user_meta( $user_id, 'pv_paypal', true ) ); ?>"/>
	</p>
	<p class="show-if" data-control="wcv_paypal_masspay_wallet" data-control-value="venmo">
		<b><?php esc_html_e( 'Venmo ID', 'wc-vendors' ); ?></b><br/>
		<?php esc_html_e( 'Provide your Venmo ID or Phone number for your commission payout.', 'wc-vendors' ); ?><br/>

		<input type="text" name="wcv_paypal_masspay_venmo_id" id="wcv_paypal_masspay_venmo_id" placeholder="some@email.com"
				value="<?php echo esc_attr( get_user_meta( $user_id, 'wcv_paypal_masspay_venmo_id', true ) ); ?>"/>
	</p>
</div>
