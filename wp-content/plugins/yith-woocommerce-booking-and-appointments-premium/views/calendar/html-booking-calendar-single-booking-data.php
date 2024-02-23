<?php
/**
 * Single booking data for Month Calendar page html
 *
 * @var YITH_WCBK_Booking|YITH_WCBK_Booking_External $booking
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$booking_edit_link   = $booking->get_edit_link();
$booking_name_format = get_option( 'yith-wcbk-booking-name-format-in-calendar', '#{id} {user_name}' );

?>
<div class="yith-wcbk-booking-calendar-single-booking-data-wrapper">

	<?php do_action( 'yith_wcbk_calendar_single_booking_data_before', $booking ); ?>

	<div class="yith-wcbk-booking-calendar-single-booking-data-actions__container">
		<?php if ( $booking_edit_link ) : ?>
			<a href="<?php echo esc_url( $booking_edit_link ); ?>" target="_blank">
				<span class="dashicons dashicons-edit yith-wcbk-booking-calendar-single-booking-data-action yith-wcbk-booking-calendar-single-booking-data-action-edit"></span>
			</a>
		<?php endif; ?>
		<span class="dashicons dashicons-no-alt yith-wcbk-booking-calendar-single-booking-data-action yith-wcbk-booking-calendar-single-booking-data-action-close"></span>
	</div>

	<div class="yith-wcbk-booking-calendar-single-booking-data-title__container">
		<div class="yith-wcbk-booking-calendar-single-booking-data-title"><?php echo wp_kses_post( apply_filters( 'yith_wcbk_calendar_single_booking_data_booking_title', $booking->get_formatted_name( $booking_name_format ), $booking ) ); ?></div>
	</div>

	<div class="yith-wcbk-booking-calendar-single-booking-data-table__container">

		<table class="yith-wcbk-booking-calendar-single-booking-data-table">
			<?php do_action( 'yith_wcbk_calendar_single_booking_data_table_before', $booking ); ?>
			<tr>
				<th><?php esc_html_e( 'Status', 'yith-booking-for-woocommerce' ); ?></th>
				<td><?php echo esc_html( $booking->get_status_text() ); ?> </td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Product', 'yith-booking-for-woocommerce' ); ?></th>
				<td>
					<?php
					if ( $booking->get_product() ) {
						$product_link  = admin_url( 'post.php?post=' . $booking->get_product_id() . '&action=edit' );
						$product_title = $booking->get_product()->get_title();
						$_title        = "<a href='{$product_link}'>{$product_title}</a>";
					} else {
						// translators: %s is the ID of the product.
						$_title = sprintf( __( 'Deleted Product #%s', 'yith-booking-for-woocommerce' ), $booking->get_product_id() );
					}
					echo wp_kses_post( $_title );
					?>
				</td>
			</tr>
			<?php if ( ! $booking->is_external() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Duration', 'yith-booking-for-woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( $booking->get_duration_html() ); ?></td>
				</tr>
			<?php endif; ?>
			<tr>
				<th><?php esc_html_e( 'From', 'yith-booking-for-woocommerce' ); ?></th>
				<td><?php echo esc_html( $booking->get_formatted_from() ); ?> </td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'To', 'yith-booking-for-woocommerce' ); ?></th>
				<td><?php echo esc_html( $booking->get_formatted_to() ); ?> </td>
			</tr>
			<?php if ( ! $booking->is_external() ) : ?>
				<?php
				if ( $booking->get_order_id() ) {
					?>
					<tr>
						<th><?php esc_html_e( 'Order', 'yith-booking-for-woocommerce' ); ?></th>
						<td>
							<?php
							yith_wcbk_admin_order_info_html(
								$booking,
								array(
									'show_email'  => false,
									'show_status' => false,
								)
							);
							?>
						</td>
					</tr>
					<?php
				} elseif ( $booking->get_user() ) {
					?>
					<tr>
						<th><?php esc_html_e( 'User', 'yith-booking-for-woocommerce' ); ?></th>
						<td>
							<?php yith_wcbk_admin_user_info_html( $booking ); ?>
						</td>
					</tr>
					<?php
				}
				?>
				<?php if ( $booking->has_persons() ) : ?>
					<tr>
						<th><?php esc_html_e( 'People', 'yith-booking-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $booking->get_persons() ); ?> </td>
					</tr>
				<?php endif ?>

				<?php
				$services = $booking->get_service_names();
				if ( ! ! $services ) {
					$services_html = implode( ', ', $services );
					?>
					<tr>
						<th><?php esc_html_e( 'Services', 'yith-booking-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $services_html ); ?> </td>
					</tr>
					<?php
				}
				?>
			<?php else : ?>
				<?php
				$external_extra_data = array(
					'summary'       => __( 'Summary', 'yith-booking-for-woocommerce' ),
					'description'   => __( 'Description', 'yith-booking-for-woocommerce' ),
					'location'      => __( 'Location', 'yith-booking-for-woocommerce' ),
					'uid'           => __( 'UID', 'yith-booking-for-woocommerce' ),
					'calendar_name' => __( 'Calendar Name', 'yith-booking-for-woocommerce' ),
					'source'        => __( 'Source', 'yith-booking-for-woocommerce' ),
				);

				foreach ( $external_extra_data as $key => $label ) {
					$getter = "get_{$key}";
					$value  = $booking->$getter();

					switch ( $key ) {
						case 'description':
							$value = nl2br( $value );
							break;
						case 'source':
							$value = yith_wcbk_booking_external_sources()->get_name_from_string( $value );
							break;
					}

					if ( ! ! $value ) {
						echo '<tr><th>' . esc_html( $label ) . '</th><td>' . esc_html( $value ) . '</td></tr>';
					}
				}

				?>
			<?php endif; ?>

			<?php do_action( 'yith_wcbk_calendar_single_booking_data_table_after', $booking ); ?>

		</table>
	</div>

	<?php do_action( 'yith_wcbk_calendar_single_booking_data_after', $booking ); ?>
</div>
