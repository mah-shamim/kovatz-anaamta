<?php
/**
 * Vendor bank details template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/dashboard/settings/bank-details.php
 *
 * @author        WC Vendors
 * @version       2.4.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="show-if" data-control="wcv_commission_payout_method" data-control-value="bank">
<strong><?php esc_html_e( 'Bank Details', 'wc-vendors' ); ?></strong><br/>
<?php esc_html_e( 'Please enter your bank details below.', 'wc-vendors' ); ?><br/>
<table>
    <tr>
        <td>
            <p class="form-row notes">
                <label for="wcv_bank_account_name"><?php esc_html_e( 'Account Name', 'wc-vendors' ); ?></label>
                <input
                    type="text"
                    name="wcv_bank_account_name"
                    id="wcv_bank_account_name"
                    value="<?php echo esc_attr( get_user_meta( $user_id, 'wcv_bank_account_name', true ) ); ?>"
                />
            </p>
        </td>
        <td>
            <p class="form-row notes">
                <label for="wcv_bank_account_number"><?php esc_html_e( 'Account Number', 'wc-vendors' ); ?></label>
                <input
                    type="text"
                    name="wcv_bank_account_number"
                    id="wcv_bank_account_number"
                    value="<?php echo esc_attr( get_user_meta( $user_id, 'wcv_bank_account_number', true ) ); ?>"
                />
            </p>
        </td>
        <td><p class="form-row notes">
            <label for="wcv_bank_name">
                <?php esc_html_e( 'Bank Name', 'wc-vendors' ); ?></label>
                <input
                    type="text"
                    name="wcv_bank_name"
                    id="wcv_bank_name"
                    value="<?php echo esc_attr( get_user_meta( $user_id, 'wcv_bank_name', true ) ); ?>"
                />
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p class="form-row notes">
                <label for="wcv_bank_routing_number"><?php esc_html_e( 'Routing Number', 'wc-vendors' ); ?></label>
                <input
                    type="text"
                    name="wcv_bank_routing_number"
                    id="wcv_bank_routing_number"
                    value="<?php echo esc_attr( get_user_meta( $user_id, 'wcv_bank_routing_number', true ) ); ?>"
                />
            </p>
        </td>
        <td>
            <p class="form-row notes">
                <label for="wcv_bank_iban"><?php esc_html_e( 'IBAN', 'wc-vendors' ); ?></label>
                <input
                    type="text"
                    name="wcv_bank_iban"
                    id="wcv_bank_iban"
                    value="<?php echo esc_attr( get_user_meta( $user_id, 'wcv_bank_iban', true ) ); ?>"
                />
            </p>
        </td>
        <td>
            <p class="form-row notes">
                <label for="wcv_bank_bic_swift"><?php esc_html_e( 'BIC / Swift', 'wc-vendors' ); ?></label>
                <input
                    type="text"
                    name="wcv_bank_bic_swift"
                    id="wcv_bank_bic_swift"
                    value="<?php echo esc_attr( get_user_meta( $user_id, 'wcv_bank_bic_swift', true ) ); ?>"
                />
            </p>
        </td>
    </tr>
</table>
</div>
