<?php
/**
 * Class YITH_WCBK_Email_Customer_New_Booking
 *
 * @author  YITH
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Customer_New_Booking' ) ) {
	/**
	 * Class YITH_WCBK_Email_Customer_New_Booking
	 * An email sent to the customer when a new booking is created
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Email_Customer_New_Booking extends YITH_WCBK_Email {
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'yith_wcbk_customer_new_booking';
			$this->title          = __( 'New Booking', 'yith-booking-for-woocommerce' );
			$this->description    = __( 'This email is sent to the customer when a booking is created.', 'yith-booking-for-woocommerce' );
			$this->heading        = __( 'New Booking', 'yith-booking-for-woocommerce' );
			$this->subject        = __( 'Booking #{booking_id} created', 'yith-booking-for-woocommerce' );
			$this->customer_email = true;

			$this->custom_message = sprintf(
			// translators: %s is the site name.
				__( "Hi {customer_name}, \n\nGreat news! Your booking {booking_id_link} has been created and it's now <strong>{status}<strong>\n\n{booking_details}\n\nRegards,\n%s Staff", 'yith-booking-for-woocommerce' ),
				get_bloginfo( 'name' )
			);

			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/customer-new-booking.php';
			$this->template_plain = 'emails/plain/customer-new-booking.php';

			add_action( 'yith_wcbk_new_booking_notification', array( $this, 'trigger' ) );

			parent::__construct();
		}
	}
}

return new YITH_WCBK_Email_Customer_New_Booking();
