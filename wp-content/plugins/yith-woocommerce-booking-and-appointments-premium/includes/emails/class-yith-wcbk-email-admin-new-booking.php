<?php
/**
 * Class YITH_WCBK_Email_Admin_New_Booking
 *
 * @author  YITH
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Admin_New_Booking' ) ) {
	/**
	 * Class YITH_WCBK_Email_Admin_New_Booking
	 * An email sent to the admin when a booking is created
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  1.0.8
	 */
	class YITH_WCBK_Email_Admin_New_Booking extends YITH_WCBK_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id          = 'yith_wcbk_admin_new_booking';
			$this->title       = __( 'New Booking (Admin)', 'yith-booking-for-woocommerce' );
			$this->description = __( 'This email is sent to the admin when a booking is created.', 'yith-booking-for-woocommerce' );
			$this->heading     = __( 'New Booking', 'yith-booking-for-woocommerce' );
			$this->subject     = __( 'Booking #{booking_id} created', 'yith-booking-for-woocommerce' );

			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/admin-new-booking.php';
			$this->template_plain = 'emails/plain/admin-new-booking.php';

			$this->custom_message = sprintf(
			// translators: %s is the site name.
				__( "Hi Admin,\n\nGreat news! There is a new booking for the item \"{product_name}\"\n\n{booking_details}\n\nRegards,\n%s Staff", 'yith-booking-for-woocommerce' ),
				get_bloginfo( 'name' )
			);

			add_action( 'yith_wcbk_new_booking_notification', array( $this, 'trigger' ) );

			parent::__construct();

			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
		}

		/**
		 * Initialize Form fields.
		 */
		public function init_form_fields() {
			parent::init_form_fields();
			yith_wcbk_array_add_after(
				$this->form_fields,
				'enabled',
				'recipient',
				array(
					'title'       => __( 'Recipient(s)', 'yith-booking-for-woocommerce' ),
					'type'        => 'text',
					'description' => sprintf(
					// translators: %s is the default value.
						esc_html__( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'yith-booking-for-woocommerce' ),
						'<code>' . esc_html( get_option( 'admin_email' ) ) . '</code>'
					),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true,
				)
			);
		}
	}
}

return new YITH_WCBK_Email_Admin_New_Booking();
