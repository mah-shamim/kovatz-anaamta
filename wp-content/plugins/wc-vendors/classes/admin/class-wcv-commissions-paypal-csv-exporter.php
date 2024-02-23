<?php
/**
 * Handles commission CSV export for PayPal Manual Masspay.
 *
 * @version 2.4.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'WC_CSV_Exporter', false ) ) {
	include_once WC_ABSPATH . 'includes/export/abstract-wc-csv-exporter.php';
}

/**
 * WCV_Commissions_PayPal_Masspay_CSV_Export Class.
 */
class WCV_Commissions_PayPal_Masspay_CSV_Export extends WC_CSV_Exporter {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->column_names = $this->get_default_column_names();
	}


	/**
	 * Return an array of columns to export.
	 *
	 * @since 2.4.3
	 * @return array
	 */
	public function get_default_column_names() {

		// https://developer.paypal.com/docs/payouts/standard/payouts-web/#link-createapaymentfile

		return apply_filters(
			'wcv_commissions_sum_export_columns',
			array(
				'recipient_id' => __( 'Identifier', 'wc-vendors' ),
				'total_due'    => __( 'Total Amount', 'wc-vendors' ),
				'currency'     => __( 'Currency', 'wc-vendors' ),
				'vendor_id'    => sprintf( __( '%s', 'wc-vendors' ), wcv_get_vendor_name() ),
				'note'         => __( 'Note', 'wc-vendors' ),
				'wallet'       => __( 'Wallet (PayPal or Venmo)', 'wc-vendors' ),
			)
		);
	}

	/**
	 * Prepare data for export.
	 *
	 * @since 2.4.3
	 */
	public function prepare_data_to_export() {

		global $wpdb;

		$columns = $this->get_column_names();

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$sum_totals       = WCV_Commission::get_sum_vendor_totals();
		$this->total_rows = count( $sum_totals );
		$this->row_data   = array();

		$paypal_currency = get_option( 'wcvendors_paypal_web_currency', get_woocommerce_currency() );
		$paypal_note     = get_option( 'wcvendors_paypal_payout_note', sprintf( __( 'Commission payout for %s', 'wc-vendors' ), get_bloginfo( 'name' ) ) );

		foreach ( $sum_totals as $status => $totals ) {
			$row = array();
			foreach ( $totals as $vendor_id => $total ) {

				$user         = get_user_by( 'id', $vendor_id );
				$paypal_email = get_user_meta( $vendor_id, 'pv_paypal', 'true' );
				$venmo_id     = get_user_meta( $vendor_id, 'wcv_paypal_masspay_venmo_id', 'true' );
				$wallet       = get_user_meta( $vendor_id, 'wcv_paypal_masspay_wallet', 'true' );
				$recipient_id = ( 'paypal' === $wallet ) ? $paypal_email : $venmo_id; // Yoda Condition.


				foreach ( $columns as $column_id => $column_name ) {

					switch ( $column_id ) {
						case 'recipient_id':
							$value = $recipient_id;
							break;
						case 'total_due':
							$value = wc_format_localized_price( $total );
							break;
						case 'currency':
							$value = $paypal_currency;
							break;
						case 'vendor_id':
							$value = $user->user_login;
							break;
						case 'note':
							$value = $paypal_note;
							break;
						case 'wallet':
							$value = $wallet;
							break;
						default:
							$value = $status;
							break;
					}

					$row[ $column_id ] = $value;
				}

				$this->row_data[] = apply_filters( 'wcvendors_paypal_commissions_export_row_data', $row, $vendor_id, $total, $status );
			}
		}

	}
}
