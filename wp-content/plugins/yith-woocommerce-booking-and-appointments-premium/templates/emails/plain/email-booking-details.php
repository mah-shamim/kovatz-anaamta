<?php
/**
 * Booking details - plain.
 *
 * @var YITH_WCBK_Booking $booking        The booking.
 * @var string            $email_heading  The heading.
 * @var WC_Email          $email          The email.
 * @var bool              $sent_to_admin  Is this sent to admin?
 * @var bool              $plain_text     Is this plain?
 * @var string            $custom_message The email message including booking details through {booking_details} placeholder.
 *
 * @package YITH\Booking\Templates\Emails
 */

defined( 'YITH_WCBK' ) || exit;

$booking_order_id    = apply_filters( 'yith_wcbk_email_booking_details_order_id', $booking->get_order_id(), $booking, $sent_to_admin, $plain_text, $email );
$the_order           = ! ! $booking_order_id ? wc_get_order( $booking_order_id ) : false;
$additional_services = $booking->get_service_names( $sent_to_admin, 'additional' );
$included_services   = $booking->get_service_names( $sent_to_admin, 'included' );

do_action( 'yith_wcbk_email_before_booking_table', $booking, $sent_to_admin, $plain_text, $email );

echo "-----------------------------------------\n";

echo esc_html( wp_strip_all_tags( __( 'Booking ID', 'yith-booking-for-woocommerce' ) . ': #' . $booking->get_id() ) ) . "\n";

if ( $the_order ) {
	echo esc_html( wp_strip_all_tags( __( 'Order', 'yith-booking-for-woocommerce' ) . ': #' . $the_order->get_order_number() ) ) . "\n";
}

if ( $booking->get_product() ) {
	echo esc_html( wp_strip_all_tags( __( 'Product', 'yith-booking-for-woocommerce' ) . ': ' . $booking->get_product()->get_title() ) ) . "\n";
}

echo esc_html( wp_strip_all_tags( yith_wcbk_get_label( 'duration' ) . ': ' . $booking->get_duration_html() ) ) . "\n";
echo esc_html( wp_strip_all_tags( yith_wcbk_get_label( 'from' ) . ': ' . $booking->get_formatted_from() ) ) . "\n";
echo esc_html( wp_strip_all_tags( yith_wcbk_get_label( 'to' ) . ': ' . $booking->get_formatted_to() ) ) . "\n";

if ( $additional_services ) {
	echo esc_html( wp_strip_all_tags( yith_wcbk_get_label( 'additional-services' ) . ': ' . yith_wcbk_booking_services_html( $additional_services ) ) ) . "\n";
}

if ( $included_services ) {
	echo esc_html( wp_strip_all_tags( yith_wcbk_get_label( 'included-services' ) . ': ' . yith_wcbk_booking_services_html( $included_services ) ) ) . "\n";
}

if ( $booking->has_persons() ) {
	$person_types_info = '';
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
	}
	$people_info = $booking->get_persons();

	$people_info .= ! ! $person_types_info ? ' (' . $person_types_info . ')' : '';

	echo esc_html( wp_strip_all_tags( yith_wcbk_get_label( 'people' ) . ': ' . $people_info ) ) . "\n";

}

echo esc_html( wp_strip_all_tags( __( 'Status', 'yith-booking-for-woocommerce' ) . ': ' . $booking->get_status_text() ) ) . "\n";

echo "-----------------------------------------\n\n";

do_action( 'yith_wcbk_email_after_booking_table', $booking, $sent_to_admin, $plain_text, $email );
