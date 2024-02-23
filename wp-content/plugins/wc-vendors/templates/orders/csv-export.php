<?php
/**
 * Export Orders Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/orders/csv-export.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Emails/HTML
 * @version       2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form method="post" name="export_orders">
	<input type="submit"
			class="btn btn-primary btn-small"
			style="float:right;margin-bottom:10px;"
			name="export_orders"
			value="<?php echo esc_attr_e( 'Export orders', 'wc-vendors' ); ?>"
	>
	<?php wp_nonce_field( 'export_orders', 'export_orders_nonce' ); ?>
</form>
