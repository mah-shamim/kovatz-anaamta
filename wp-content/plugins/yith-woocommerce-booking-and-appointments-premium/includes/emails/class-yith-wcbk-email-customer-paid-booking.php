<?php
/**
 * Class YITH_WCBK_Email_Customer_Paid_Booking
 *
 * @author  YITH
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Customer_Paid_Booking' ) ) {
	/**
	 * Class YITH_WCBK_Email_Customer_Paid_Booking
	 * An email sent to the customer when a new booking is paid
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Email_Customer_Paid_Booking extends YITH_WCBK_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'yith_wcbk_customer_paid_booking';
			$this->title          = __( 'Paid Booking', 'yith-booking-for-woocommerce' );
			$this->description    = __( 'This email is sent to customers when a booking is paid.', 'yith-booking-for-woocommerce' );
			$this->heading        = __( 'Paid Booking', 'yith-booking-for-woocommerce' );
			$this->subject        = __( 'Booking #{booking_id} is now paid', 'yith-booking-for-woocommerce' );
			$this->customer_email = true;

			$this->custom_message = sprintf(
			// translators: %s is the site name.
				__( "Hi {customer_name}, \n\nYour booking {booking_id_link} is now paid!\n\n{booking_details}\n\nRegards,\n%s Staff", 'yith-booking-for-woocommerce' ),
				get_bloginfo( 'name' )
			);

			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/customer-paid-booking.php';
			$this->template_plain = 'emails/plain/customer-paid-booking.php';

			add_action( 'yith_wcbk_booking_status_paid_notification', array( $this, 'trigger' ) );

			parent::__construct();
		}
	}
}

return new YITH_WCBK_Email_Customer_Paid_Booking();
