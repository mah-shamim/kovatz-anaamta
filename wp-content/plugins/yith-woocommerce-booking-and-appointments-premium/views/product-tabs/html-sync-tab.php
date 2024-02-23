<?php
/**
 * Template options in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 *
 * @package YITH\Booking\Views
 * @author  YITH
 */

defined( 'YITH_WCBK' ) || exit;
?>
<div class="yith-wcbk-product-metabox-options-panel yith-plugin-ui options_group show_if_<?php echo esc_attr( $prod_type ); ?>">
	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Booking Sync', 'yith-booking-for-woocommerce' ); ?></h3>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php
			$key                   = $booking_product ? $booking_product->get_external_calendars_key( 'edit' ) : yith_wcbk_generate_external_calendars_key();
			$export_future_ics_url = add_query_arg(
				array(
					'yith_wcbk_exporter_action' => 'export_future_ics',
					'product_id'                => $post_id,
					'key'                       => $key,
				),
				trailingslashit( home_url() )
			);
			ob_start();
			?>
			<input type="hidden" name="_yith_booking_external_calendars_key" value="<?php echo esc_attr( $key ); ?>"/>
			<?php yith_plugin_fw_copy_to_clipboard( $export_future_ics_url ); ?>
			<a href='<?php echo esc_url( $export_future_ics_url ); ?>' class='yith-wcbk-sync-download-future-ics yith-plugin-fw__button--secondary yith-plugin-fw__button--with-icon'>
				<i class="yith-icon yith-icon-upload"></i>
				<?php esc_html_e( 'Download', 'yith-booking-for-woocommerce' ); ?>
			</a>
			<?php
			$export_future_ics_html = ob_get_clean();

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_sync_export_future_ics_url yith_booking_multi_fields',
					'title'  => __( 'Export Future ICS URL', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'type'  => 'html',
						'value' => $export_future_ics_html,
					),
				)
			);

			ob_start();
			$calendars = $booking_product ? $booking_product->get_external_calendars( 'edit' ) : array();
			yith_wcbk_get_view( 'product-tabs/utility/html-imported-calendars.php', compact( 'calendars' ) );
			$calendars_html = ob_get_clean();
			yith_wcbk_product_metabox_form_field(
				array(
					'title'  => __( 'Import ICS Calendars', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'type'  => 'html',
						'value' => $calendars_html,
					),
				)
			);

			$last_sync = $booking_product ? $booking_product->get_external_calendars_last_sync( 'edit' ) : 0;
			yith_wcbk_product_metabox_form_field(
				array(
					'title'  => __( 'Last Sync', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'type'  => 'html',
						'value' => $last_sync ? yith_wcbk_datetime( $last_sync ) : '',
					),
				)
			);
			?>
		</div>
	</div>
</div>
