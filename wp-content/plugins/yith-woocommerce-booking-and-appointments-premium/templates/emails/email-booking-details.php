<?php
/**
 * Booking details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-booking-details.php.
 *
 * @var YITH_WCBK_Booking $booking       The booking.
 * @var string            $email_heading The heading.
 * @var WC_Email          $email         The email.
 * @var bool              $sent_to_admin Is this sent to admin?
 * @var bool              $plain_text    Is this plain?
 *
 * @package YITH\Booking
 */

defined( 'ABSPATH' ) || exit;

$additional_services = $booking->get_service_names( $sent_to_admin, 'additional' );
$included_services   = $booking->get_service_names( $sent_to_admin, 'included' );
$booking_url         = $sent_to_admin ? get_edit_post_link( $booking->get_id() ) : $booking->get_view_booking_url();

do_action( 'yith_wcbk_email_before_booking_table', $booking, $sent_to_admin, $plain_text, $email ); ?>
<div class="booking-details__wrapper">
	<table class="booking-details" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; background: #f1f1f1" border="0">
		<tr>
			<th scope="row" colspan="2" style="text-align:left;"><?php esc_html_e( 'Booking ID', 'yith-booking-for-woocommerce' ); ?></th>
			<td style="text-align:left;"><a href="<?php echo esc_url( $booking_url ); ?>">#<?php echo esc_html( $booking->get_id() ); ?></a></td>
		</tr>
		<?php
		$booking_order_id = apply_filters( 'yith_wcbk_email_booking_details_order_id', $booking->get_order_id(), $booking, $sent_to_admin, $plain_text, $email );
		$the_order        = ! ! $booking_order_id ? wc_get_order( $booking_order_id ) : false;
		?>

		<?php if ( $the_order ) : ?>
			<?php
			if ( ! $sent_to_admin ) {
				$order_link = $the_order->get_view_order_url();
			} else {
				$order_link = esc_url( admin_url( 'post.php?post=' . $booking_order_id . '&action=edit' ) );
			}
			$order_title = _x( '#', 'hash before order number', 'woocommerce' ) . $the_order->get_order_number();
			?>
			<tr>
				<th scope="row" colspan="2" style="text-align:left;"><?php esc_html_e( 'Order', 'yith-booking-for-woocommerce' ); ?></th>
				<td style="text-align:left;">
					<a href="<?php echo esc_url( $order_link ); ?>"><?php echo esc_html( $order_title ); ?></a>
				</td>
			</tr>
		<?php endif ?>

		<?php if ( $booking->get_product() ) : ?>
			<tr>
				<th scope="row" colspan="2" style="text-align:left;"><?php esc_html_e( 'Product', 'yith-booking-for-woocommerce' ); ?></th>
				<td style="text-align:left;">
					<a href="<?php echo esc_url( $booking->get_product()->get_permalink() ); ?>"><?php echo esc_html( $booking->get_product()->get_title() ); ?></a>
				</td>
			</tr>
		<?php endif ?>
		<tr>
			<th scope="row" colspan="2" style="text-align:left;"><?php echo esc_html( yith_wcbk_get_label( 'duration' ) ); ?></th>
			<td style="text-align:left;"><?php echo esc_html( $booking->get_duration_html() ); ?></td>
		</tr>
		<tr>
			<th scope="row" colspan="2" style="text-align:left;"><?php echo esc_html( yith_wcbk_get_label( 'from' ) ); ?></th>
			<td style="text-align:left;"><?php echo esc_html( $booking->get_formatted_from() ); ?></td>
		</tr>
		<tr>
			<th scope="row" colspan="2" style="text-align:left;"><?php echo esc_html( yith_wcbk_get_label( 'to' ) ); ?></th>
			<td style="text-align:left;"><?php echo esc_html( $booking->get_formatted_to() ); ?></td>
		</tr>

		<?php if ( $additional_services ) : ?>
			<tr>
				<th scope="row" colspan="2" style="text-align:left;"><?php echo esc_html( yith_wcbk_get_label( 'additional-services' ) ); ?></th>
				<td style="text-align:left;">
					<?php echo esc_html( yith_wcbk_booking_services_html( $additional_services ) ); ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( $included_services ) : ?>
			<tr>
				<th scope="row" colspan="2" style="text-align:left;"><?php echo esc_html( yith_wcbk_get_label( 'included-services' ) ); ?></th>
				<td style="text-align:left;">
					<?php echo esc_html( yith_wcbk_booking_services_html( $included_services ) ); ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( $booking->has_persons() ) : ?>
			<tr>
				<th scope="row" colspan="2" style="text-align:left;"><?php echo esc_html( yith_wcbk_get_label( 'people' ) ); ?></th>
				<td style="text-align:left;">
					<?php
					echo esc_html( $booking->get_persons() );

					if ( $booking->has_person_types() ) {
						$person_types_info = array();

						foreach ( $booking->get_person_types() as $person_type ) {
							if ( ! $person_type['number'] ) {
								continue;
							}
							$person_type_id     = absint( $person_type['id'] );
							$person_type_title  = yith_wcbk()->person_type_helper->get_person_type_title( $person_type_id );
							$person_type_title  = ! ! $person_type_title ? $person_type_title : $person_type['title'];
							$person_type_number = absint( $person_type['number'] );

							$person_types_info[] = $person_type_title . ': ' . $person_type_number;
						}
						$person_types_info = implode( ', ', $person_types_info );

						echo esc_html( ! ! $person_types_info ? ' (' . $person_types_info . ')' : '' );
					}
					?>
				</td>
			</tr>
		<?php endif; ?>

		<tr class="booking-status-row">
			<th scope="row" colspan="2" style="text-align:left;"><?php esc_html_e( 'Status', 'yith-booking-for-woocommerce' ); ?></th>
			<td class="booking-status booking-status--<?php echo esc_attr( $booking->get_status() ); ?>" style="text-align:left;"><?php echo esc_html( $booking->get_status_text() ); ?></td>
		</tr>
	</table>


	<?php if ( ( $sent_to_admin && $booking->has_status( 'pending-confirm' ) ) || ( ! $sent_to_admin && $booking->has_status( 'confirmed' ) ) ) : ?>
		<div class="booking-actions">
			<?php if ( $sent_to_admin && $booking->has_status( 'pending-confirm' ) ) : ?>
				<?php
				$confirm_url = $booking->get_mark_action_url( 'confirmed', array( 'source' => 'email' ) );
				$reject_url  = $booking->get_mark_action_url( 'unconfirmed', array( 'source' => 'email' ) );
				?>
				<div class="booking-actions__row">
					<a class='booking-button booking-action--confirm' href="<?php echo esc_url( $confirm_url ); ?>"><?php esc_html_e( 'Confirm booking', 'yith-booking-for-woocommerce' ); ?></a>
				</div>
				<div class="booking-actions__row">
					<?php
					echo wp_kses_post(
						sprintf(
						// translators: %s is an action link.
							_x( 'or %s', 'Email action alternative', 'yith-booking-for-woocommerce' ),
							sprintf(
								'<a class="booking-link booking-action--reject" href="%s">%s</a>',
								esc_url( $reject_url ),
								esc_html__( 'Reject booking', 'yith-booking-for-woocommerce' )
							)
						)
					);
					?>
				</div>

			<?php elseif ( ! $sent_to_admin && $booking->has_status( 'confirmed' ) ) : ?>
				<?php
				$pay_url  = $booking->get_confirmed_booking_payment_url();
				$view_url = $booking->get_view_booking_url();
				?>
				<div class="booking-actions__row">
					<a class="booking-button booking-action--pay" href="<?php echo esc_url( $pay_url ); ?>"><?php esc_html_e( 'Pay booking', 'yith-booking-for-woocommerce' ); ?></a>
				</div>
				<div class="booking-actions__row">
					<?php
					echo wp_kses_post(
						sprintf(
						// translators: %s is an action link.
							_x( 'or %s', 'Email action alternative', 'yith-booking-for-woocommerce' ),
							sprintf(
								'<a class="booking-link booking-action--view" href="%s">%s</a>',
								esc_url( $view_url ),
								esc_html__( 'View booking details', 'yith-booking-for-woocommerce' )
							)
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</div>

<?php do_action( 'yith_wcbk_email_after_booking_table', $booking, $sent_to_admin, $plain_text, $email ); ?>
