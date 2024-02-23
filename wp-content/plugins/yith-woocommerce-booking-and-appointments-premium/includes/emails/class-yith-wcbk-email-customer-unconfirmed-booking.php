<?php
/**
 * Class YITH_WCBK_Email_Customer_Unconfirmed_Booking
 *
 * @author  YITH
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Customer_Unconfirmed_Booking' ) ) {
	/**
	 * Class YITH_WCBK_Email_Customer_Unconfirmed_Booking
	 * An email sent to the customer when a new booking is not confirmed
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Email_Customer_Unconfirmed_Booking extends YITH_WCBK_Email {
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'yith_wcbk_customer_unconfirmed_booking';
			$this->title          = __( 'Rejected Booking', 'yith-booking-for-woocommerce' );
			$this->description    = __( 'This email is sent to the customer when a booking is not confirmed.', 'yith-booking-for-woocommerce' );
			$this->heading        = __( 'Rejected Booking', 'yith-booking-for-woocommerce' );
			$this->subject        = __( 'Booking #{booking_id} was rejected', 'yith-booking-for-woocommerce' );
			$this->customer_email = true;

			$this->custom_message = sprintf(
			// translators: %s is the site name.
				__( "Hi {customer_name}, \n\nYour booking {booking_id_link} was rejected!\n\n{booking_details}\n\nRegards,\n%s Staff", 'yith-booking-for-woocommerce' ),
				get_bloginfo( 'name' )
			);

			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/customer-unconfirmed-booking.php';
			$this->template_plain = 'emails/plain/customer-unconfirmed-booking.php';

			add_action( 'yith_wcbk_booking_status_unconfirmed_notification', array( $this, 'trigger' ) );

			parent::__construct();
		}

	}
}

return new YITH_WCBK_Email_Customer_Unconfirmed_Booking();
