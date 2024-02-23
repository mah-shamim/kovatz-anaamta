<?php
/**
 * Admin View: Step Two
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<form method="post">
<?php wp_nonce_field( 'wcv-setup', 'wcv-setup', true, true ); ?>
	<p class="store-setup">
		<?php
		// Translators: %s is replaced with the vendor name.
		printf( esc_html__( 'Enable and disable capabilites of the %s', 'wc-vendors' ), wcv_get_vendor_name( false, false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
    </p>

	<table class="wcv-setup-table">
		<thead>
			<tr>
				<td class="table-desc"><strong><?php esc_html_e( 'Products', 'wc-vendors' ); ?></strong></td>
				<td class="table-check"></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="table-desc">
					<?php
					// Translators: %s is replaced with the vendor name.
					printf( esc_html__( 'Allow %s to add/edit products', 'wc-vendors' ), wcv_get_vendor_name( false, false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</td>
				<td class="table-check">
					<input type="checkbox" style="float: right; font-size: 4em;" id="wcv_capability_products_enabled" name="wcv_capability_products_enabled" value="yes" <?php checked( $products_enabled, 'yes' ); ?> />
				</td>
			</tr>
			<tr>
				<td class="table-desc">
					<?php
					// Translators: %s is replaced with the vendor name.
					printf( esc_html__( 'Allow %s to edit published (live) products', 'wc-vendors' ), wcv_get_vendor_name( false, false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</td>
				<td class="table-check">
					<input type="checkbox" style="float: right; font-size: 4em;" id="wcv_capability_products_edit" name="wcv_capability_products_edit" value="yes" <?php checked( $live_products, 'yes' ); ?> />
				</td>
			</tr>
			<tr>
				<td class="table-desc">
					<?php
					// Translators: %s is replaced with the vendor name.
					printf( esc_html__( 'Allow %s to publish products without requiring approval.', 'wc-vendors' ), wcv_get_vendor_name( false, false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</td>
				<td class="table-check">
					<input type="checkbox" style="float: right; font-size: 4em;" id="wcv_capability_products_live" name="wcv_capability_products_live" value="yes" <?php checked( $products_approval, 'yes' ); ?> />
				</td>
			</tr>
		</tbody>
	</table>

	<table class="wcv-setup-table">
		<thead>
		<tr>
			<td class="table-desc"><strong><?php esc_html_e( 'Orders', 'wc-vendors' ); ?></strong></td>
			<td class="table-check"></td>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td class="table-desc">
				<?php
				// Translators: %s is replaced with the vendor name.
				printf( esc_html__( 'Allow %s to view orders', 'wc-vendors' ), wcv_get_vendor_name( false, false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>
			</td>
			<td class="table-check">
				<input type="checkbox" style="float: right; font-size: 4em;" id="wcv_capability_orders_enabled" name="wcv_capability_orders_enabled" value="yes" <?php checked( $orders_enabled, 'yes' ); ?> />
			</td>
		</tr>
		<tr>
			<td class="table-desc">
				<?php
				// Translators: %s is replaced with the vendor name.
				printf( esc_html__( 'Allow %s to export their orders to a CSV file', 'wc-vendors' ), wcv_get_vendor_name( false, false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>
			</td>
			<td class="table-check">
				<input type="checkbox" style="float: right; font-size: 4em;" id="wcv_capability_orders_export" name="wcv_capability_orders_export" value="yes" <?php checked( $export_orders, 'yes' ); ?> />
			</td>
		</tr>
		<tr>
			<td class="table-desc">
				<?php
				// Translators: %s is replaced with the vendor name.
				printf( esc_html__( 'Allow %s to view order notes', 'wc-vendors' ), wcv_get_vendor_name( false, false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>
			</td>
			<td class="table-check">
				<input type="checkbox" style="float: right; font-size: 4em;" id="wcv_capability_order_read_notes" name="wcv_capability_order_read_notes" value="yes" <?php checked( $view_order_notes, 'yes' ); ?> />
			</td>
		</tr>
		<tr>
			<td class="table-desc">
				<?php
				// Translators: %s is replaced with the vendor name.
				printf( esc_html__( 'Allow %s to add order notes.', 'wc-vendors' ), wcv_get_vendor_name( false, false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
            </td>
			<td class="table-check">
				<input type="checkbox" style="float: right; font-size: 4em;" id="wcv_capability_order_update_notes" name="wcv_capability_order_update_notes" value="yes" <?php checked( $view_order_notes, 'yes' ); ?> />
			</td>
		</tr>
		</tbody>
	</table>

	<p class="wcv-setup-actions step">
		<button type="submit" class="button button-next" value="<?php esc_attr_e( 'Next', 'wc-vendors' ); ?>" name="save_step"><?php esc_html_e( 'Next', 'wc-vendors' ); ?></button>
	</p>
</form>
