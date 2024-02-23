<?php
/**
 * Admin booking cancelled email, plain text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/emails/plain/admin-booking-cancelled.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @package WooCommerce_Bookings
 * @version 1.8.0
 * @since   1.7.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '= ' . esc_html( $email_heading ) . " =\n\n";

echo esc_html( __( 'The following booking has been cancelled by the customer. The details of the cancelled booking can be found below.', 'woocommerce-bookings' ) ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/* translators: 1: booking product title */
echo esc_html( __( 'Booked: %s', 'woocommerce-bookings' ) );
wc_get_template( 'order/admin/booking-display.php', array( 'booking_ids' => array( $booking->get_id() ) ), 'woocommerce-bookings', WC_BOOKINGS_TEMPLATE_PATH );
echo "\n";

/* translators: 1: booking id */
echo esc_html( sprintf( __( 'Booking ID: %s', 'woocommerce-bookings' ), $booking->get_id() ) ) . "\n";

$resource = $booking->get_resource();

if ( $booking->has_resources() && $resource ) {
	/* translators: 1: booking title */
	echo esc_html( sprintf( __( 'Booking Type: %s', 'woocommerce-bookings' ), $resource->post_title ) ) . "\n";
}

/* translators: 1: booking start date */
echo esc_html( sprintf( __( 'Booking Start Date: %s', 'woocommerce-bookings' ), $booking->get_start_date( null, null, wc_should_convert_timezone( $booking ) ) ) ) . "\n";
/* translators: 1: booking end date */
echo esc_html( sprintf( __( 'Booking End Date: %s', 'woocommerce-bookings' ), $booking->get_end_date( null, null, wc_should_convert_timezone( $booking ) ) ) ) . "\n";

if ( wc_should_convert_timezone( $booking ) ) {
	/* translators: 1: time zone */
	echo esc_html( sprintf( __( 'Time Zone: %s', 'woocommerce-bookings' ), str_replace( '_', ' ', $booking->get_local_timezone() ) ) );
}

if ( $booking->has_persons() ) {
	foreach ( $booking->get_persons() as $bid => $qty ) {
		if ( 0 === $qty ) {
			continue;
		}

		$person_type = ( 0 < $bid ) ? get_the_title( $bid ) : __( 'Person(s)', 'woocommerce-bookings' );
		/* translators: 1: person type 2: quantity */
		echo esc_html( sprintf( __( '%1$s: %2$d', 'woocommerce-bookings' ), $person_type, $qty ) ) . "\n";
	}
}

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

$edit_booking_url  = admin_url( 'post.php?post=' . $booking->get_id() . '&action=edit' );
$edit_booking_link = sprintf(
	'<a href="%1$s">%2$s</a>',
	esc_url( $edit_booking_url ),
	__( 'Edit booking', 'woocommerce-bookings' )
);

/* translators: 1: a href to booking */
echo wp_kses_post( sprintf( __( 'You can view and edit this booking in the dashboard here: %s', 'woocommerce-bookings' ), $edit_booking_link ) );

/**
 * Allows users to filter text in email footer
 *
 * @since 1.0.0
 */
echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
