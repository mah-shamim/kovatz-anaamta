<?php
/**
 * Class YITH_WCBK_Notifier
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Notifier' ) ) {
	/**
	 * Class YITH_WCBK_Notifier
	 * handle notifications behavior
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Notifier {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Notifier constructor.
		 */
		protected function __construct() {
			add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ) );
			add_filter( 'woocommerce_email_actions', array( $this, 'add_email_actions' ) );
			add_filter( 'woocommerce_email_styles', array( $this, 'email_style' ), 1000 ); // use 1000 as priority to allow support for YITH  Email Templates.
		}

		/**
		 * Add email actions to WooCommerce email actions
		 *
		 * @param array $actions Actions.
		 *
		 * @return array
		 */
		public function add_email_actions( $actions ) {
			foreach ( array_keys( yith_wcbk_get_booking_statuses( true ) ) as $status ) {
				$actions[] = 'yith_wcbk_booking_status_' . $status;
			}

			$actions[] = 'yith_wcbk_new_booking';
			$actions[] = 'yith_wcbk_new_customer_note';

			return $actions;
		}

		/**
		 * Add email classes to WooCommerce
		 *
		 * @param array $emails Emails.
		 *
		 * @return array
		 */
		public function add_email_classes( $emails ) {
			require_once YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email.php';
			$emails['YITH_WCBK_Email_Booking_Status']               = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-booking-status.php';
			$emails['YITH_WCBK_Email_Admin_New_Booking']            = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-admin-new-booking.php';
			$emails['YITH_WCBK_Email_Customer_New_Booking']         = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-new-booking.php';
			$emails['YITH_WCBK_Email_Customer_Confirmed_Booking']   = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-confirmed-booking.php';
			$emails['YITH_WCBK_Email_Customer_Unconfirmed_Booking'] = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-unconfirmed-booking.php';
			$emails['YITH_WCBK_Email_Customer_Cancelled_Booking']   = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-cancelled-booking.php';
			$emails['YITH_WCBK_Email_Customer_Paid_Booking']        = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-paid-booking.php';
			$emails['YITH_WCBK_Email_Customer_Completed_Booking']   = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-completed-booking.php';
			$emails['YITH_WCBK_Email_Customer_Booking_Note']        = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-booking-note.php';

			return $emails;
		}

		/**
		 * Custom email styles.
		 *
		 * @param string $style WooCommerce style.
		 *
		 * @return string
		 */
		public function email_style( $style ) {
			$style .= $this->get_email_style();

			return $style;
		}

		/**
		 * Retrieve the email style for Booking emails.
		 *
		 * @return string
		 */
		private function get_email_style() {
			ob_start();
			include YITH_WCBK_ASSETS_PATH . '/css/emails.css';

			return ob_get_clean();
		}
	}
}
