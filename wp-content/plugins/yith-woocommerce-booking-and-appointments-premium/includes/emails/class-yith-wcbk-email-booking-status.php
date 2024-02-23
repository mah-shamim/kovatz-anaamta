<?php
/**
 * Class YITH_WCBK_Email_Booking_Status
 *
 * @author  YITH
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Booking_Status' ) ) {
	/**
	 * Class YITH_WCBK_Email_Booking_Status
	 * An email sent to the admin when a new booking changes status
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Email_Booking_Status extends YITH_WCBK_Email {
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id          = 'yith_wcbk_booking_status';
			$this->title       = __( 'Booking status', 'yith-booking-for-woocommerce' );
			$this->description = __( 'This email is sent to the administrator when a booking\'s status changes.', 'yith-booking-for-woocommerce' );
			$this->heading     = __( 'Booking status changed', 'yith-booking-for-woocommerce' );
			$this->subject     = __( 'Booking #{booking_id} is now {status}', 'yith-booking-for-woocommerce' );
			$this->reply_to    = '';

			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/admin-booking-status.php';
			$this->template_plain = 'emails/plain/admin-booking-status.php';

			$this->custom_message = sprintf(
			// translators: %s is the site name.
				__( "Hi Admin,\n\nThe booking {booking_id_link} is now <strong>{status}</strong>!\n\n{booking_details}\n\nRegards,\n%s Staff", 'yith-booking-for-woocommerce' ),
				get_bloginfo( 'name' )
			);

			$statuses = $this->get_option( 'status' );
			$statuses = is_array( $statuses ) ? $statuses : array();
			foreach ( $statuses as $status ) {
				add_action( 'yith_wcbk_booking_status_' . $status . '_notification', array( $this, 'trigger' ) );
			}

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
					// translators: %s is the default recipient.
						esc_html__( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'yith-booking-for-woocommerce' ),
						'<code>' . esc_html( get_option( 'admin_email' ) ) . '</code>'
					),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true,
				)
			);

			yith_wcbk_array_add_before(
				$this->form_fields,
				'email_type',
				'status',
				array(
					'title'       => __( 'Send email for these statuses', 'yith-booking-for-woocommerce' ),
					'type'        => 'multiselect',
					'description' => __( 'Choose on which status(es) this email notification should be sent.', 'yith-booking-for-woocommerce' ),
					'default'     => array( 'unpaid', 'cancelled_by_user', 'pending-confirm' ),
					'class'       => 'email_type wc-enhanced-select',
					'options'     => yith_wcbk_get_booking_statuses( true ),
					'desc_tip'    => true,
				)
			);
		}
	}
}

return new YITH_WCBK_Email_Booking_Status();
