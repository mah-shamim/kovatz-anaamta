<?php
/**
 * Class YITH_WCBK_Email_Vendor_New_Booking
 *
 * @author  YITH
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Vendor_New_Booking' ) ) {
	/**
	 * Class YITH_WCBK_Email_Vendor_New_Booking
	 * An email sent to the vendor when a new booking is created
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  1.0.8
	 */
	class YITH_WCBK_Email_Vendor_New_Booking extends YITH_WCBK_Email {
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id          = 'yith_wcbk_vendor_new_booking';
			$this->title       = __( 'New Booking (Vendor)', 'yith-booking-for-woocommerce' );
			$this->description = __( 'This email is sent to the vendor when a booking is created.', 'yith-booking-for-woocommerce' );
			$this->heading     = __( 'New Booking', 'yith-booking-for-woocommerce' );
			$this->subject     = __( 'Booking #{booking_id} created', 'yith-booking-for-woocommerce' );

			$this->custom_message = __( "Hi {customer_name},\n\nGreat news! There is a new booking for the item \"{product_name}\"\n\n{booking_details}", 'yith-booking-for-woocommerce' );

			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/vendor-new-booking.php';
			$this->template_plain = 'emails/plain/vendor-new-booking.php';

			add_action( 'yith_wcbk_new_booking_notification', array( $this, 'trigger' ) );

			parent::__construct();

			$this->recipient = YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' );
		}

		/**
		 * Maybe set booking recipient email.
		 */
		protected function maybe_set_booking_recipient() {
			$this->recipient = false;
			if ( $this->object ) {
				$vendor = yith_get_vendor( $this->object->get_id(), 'product' );
				if ( $vendor->is_valid() ) {
					$vendor_email = $vendor->store_email;

					if ( empty( $vendor_email ) ) {
						$vendor_owner = get_user_by( 'id', absint( $vendor->get_owner() ) );
						$vendor_email = $vendor_owner instanceof WP_User ? $vendor_owner->user_email : false;
					}

					$this->recipient = $vendor_email;
				}
			}
		}
	}
}

return new YITH_WCBK_Email_Vendor_New_Booking();
