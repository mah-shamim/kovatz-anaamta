<?php
/**
 * Booking Actions Metabox
 *
 * @var YITH_WCBK_Booking $booking
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$booking_type_object = get_post_type_object( $post->post_type );
?>
<div class="yith-wcbk-booking-actions-metabox-content">
	<p style="text-align: center">
		<a href="<?php echo esc_url( $booking->get_pdf_url( 'customer' ) ); ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--small yith-wcbk-admin-button--outline" target="_blank"><?php esc_html_e( 'Customer PDF', 'yith-booking-for-woocommerce' ); ?></a>
		<a href="<?php echo esc_url( $booking->get_pdf_url( 'admin' ) ); ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--small yith-wcbk-admin-button--outline" target="_blank"><?php esc_html_e( 'Admin PDF', 'yith-booking-for-woocommerce' ); ?></a>
	</p>

	<?php if ( yith_wcbk()->google_calendar_sync->is_enabled() ) : ?>
		<div class="yith-wcbk-booking-actions-metabox-google-calendar">
			<?php
			$sync_status = 'not-sync';
			$date        = '';
			$label       = __( 'not synchronized', 'yith-booking-for-woocommerce' );
			if ( $booking->get_google_calendar_last_update() ) {
				$sync_status = 'sync';
				$date        = date_i18n( wc_date_format() . ' ' . wc_time_format(), $booking->get_google_calendar_last_update() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
				$label       = __( 'synchronized', 'yith-booking-for-woocommerce' );
			}

			$tip            = $label . ( $date ? '<br />' . $date : '' );
			$icon_url       = YITH_WCBK_ASSETS_URL . '/images/google-calendar.svg';
			$force_sync_url = yith_wcbk()->google_calendar_sync->get_action_url( 'sync-booking', array( 'booking_id' => $booking->get_id() ) );

			echo "<div class='yith-wcbk-google-calendar-sync-icon__container'>";
			echo '<img class="yith-wcbk-google-calendar-sync-icon" src="' . esc_attr( $icon_url ) . '" />';
			echo '<span class="yith-wcbk-google-calendar-sync-status ' . esc_attr( $sync_status ) . ' yith-icon yith-icon-update tips" data-tip="' . esc_attr( $tip ) . '"></span>';
			echo '</div>';
			echo "<div class='yith-wcbk-google-calendar-sync-force__container'>";
			echo '<a class="yith-wcbk-google-calendar-sync-force" href="' . esc_url( $force_sync_url ) . '">' . esc_html__( 'force sync', 'yith-booking-for-woocommerce' ) . '</a>';
			echo '</div>';
			?>
		</div>
	<?php endif; ?>
</div>

<div class="yith-wcbk-booking-actions-metabox-footer">
	<input type="submit" class="yith-wcbk-admin-button tips" name="save"
			value="<?php esc_attr_e( 'Save Booking', 'yith-booking-for-woocommerce' ); ?>"
			data-tip="<?php esc_attr_e( 'Save/update the booking', 'yith-booking-for-woocommerce' ); ?>"/>
</div>
