<?php
/**
 * Commission Payout Method Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/dashboard/settings/commission-payout-method.php
 *
 * @version 2.4.9
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$commission_payout_method = get_user_meta( $user_id, 'wcv_commission_payout_method', true );
?>

<p>
    <b><?php esc_html_e( 'Commission Payout Method', 'wc-vendors' ); ?></b><br/>
    <?php esc_html_e( 'Choose how you want your commission payout.', 'wc-vendors' ); ?><br/>
    <select name="wcv_commission_payout_method" id="wcv_commission_payout_method" style="width: 25em;">
        <option value="paypal" <?php selected( $commission_payout_method, 'paypal' ); ?>><?php esc_html_e( 'PayPal', 'wc-vendors' ); ?></option>
        <option value="bank" <?php selected( $commission_payout_method, 'bank' ); ?>><?php esc_html_e( 'Bank Transfer', 'wc-vendors' ); ?></option>
    </select>
</p>
