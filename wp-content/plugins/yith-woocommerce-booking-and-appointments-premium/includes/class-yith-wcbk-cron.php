<?php
/**
 * Cron class.
 * handle Cron processes.
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Cron' ) ) {
	/**
	 * Class YITH_WCBK_Cron
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  2.0.0
	 */
	class YITH_WCBK_Cron {

		/**
		 * Single instance of the class.
		 *
		 * @var YITH_WCBK_Cron
		 */
		private static $instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Cron
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBK_Cron constructor.
		 */
		private function __construct() {
			add_action( 'yith_wcbk_check_reject_pending_confirmation_bookings', array( $this, 'check_reject_pending_confirmation_bookings' ) );
			add_action( 'yith_wcbk_check_complete_paid_bookings', array( $this, 'check_complete_paid_bookings' ) );

			add_action( 'yith_wcbk_test_cron', array( $this, 'test_cron' ) );

			add_action( 'wp_loaded', array( $this, 'schedule_actions' ), 30 );
		}

		/**
		 * Schedule actions through the WooCommerce Action Scheduler.
		 */
		public function schedule_actions() {
			if ( ! WC()->queue()->get_next( 'yith_wcbk_check_reject_pending_confirmation_bookings' ) ) {
				WC()->queue()->schedule_recurring( strtotime( 'tomorrow midnight' ), DAY_IN_SECONDS, 'yith_wcbk_check_reject_pending_confirmation_bookings', array(), 'yith-booking' );
			}

			if ( ! WC()->queue()->get_next( 'yith_wcbk_check_complete_paid_bookings' ) ) {
				WC()->queue()->schedule_recurring( strtotime( 'tomorrow midnight' ), DAY_IN_SECONDS, 'yith_wcbk_check_complete_paid_bookings', array(), 'yith-booking' );
			}
		}

		/**
		 * Set cron
		 *
		 * @deprecated 3.0.0 | use YITH_WCBK_Cron::schedule_actions instead.
		 */
		public function set_cron() {
			$this->schedule_actions();
		}


		/**
		 * Check if reject pending confirmation bookings
		 */
		public function check_reject_pending_confirmation_bookings() {
			// TODO: the check should be made in batches of XX bookings (for example, by updating 20 bookings at time).
			$enabled = yith_wcbk()->settings->get_reject_pending_confirmation_bookings_enabled();
			$after   = yith_wcbk()->settings->get_reject_pending_confirmation_bookings_after();
			if ( $enabled && $after ) {
				$after_day = $after - 1;

				$args = array(
					'post_status' => array( 'bk-pending-confirm' ),
					'date_query'  => array(
						array(
							'before' => gmdate( 'Y-m-d H:i:s', strtotime( "now -$after_day day midnight" ) ),
						),
					),
				);

				$booking_ids = yith_wcbk_get_booking_post_ids( $args );
				$bookings    = array_filter( array_map( 'yith_get_booking', $booking_ids ) );

				if ( ! ! $bookings ) {
					foreach ( $bookings as $booking ) {
						$booking->update_status(
							'unconfirmed',
							sprintf(
							// translators: %s is the number of days.
								__( 'Automatically reject booking after %d day(s) from creating', 'yith-booking-for-woocommerce' ),
								$after
							)
						);
					}
				}
			}
		}

		/**
		 * Check if reject pending confirmation bookings
		 */
		public function check_complete_paid_bookings() {
			// TODO: the check should be made in batches of XX bookings (for example, by updating 20 bookings at time).
			if ( yith_wcbk()->settings->get_complete_paid_bookings_enabled() ) {
				$after     = yith_wcbk()->settings->get_complete_paid_bookings_after();
				$after_day = $after - 1;
				$sign      = $after_day < 0 ? '+' : '-';

				$bookings = yith_wcbk_get_bookings(
					array(
						'status'     => 'paid',
						'return'     => 'bookings',
						'data_query' => array(
							array(
								'key'      => 'to',
								'value'    => strtotime( "now {$sign}{$after_day} day midnight" ),
								'operator' => '<',
							),
						),
					)
				);

				if ( ! ! $bookings ) {
					foreach ( $bookings as $booking ) {
						$booking->update_status(
							'completed',
							sprintf(
							// translators: %s is the number of days.
								__( 'Automatically complete booking after %d day(s) from End Date', 'yith-booking-for-woocommerce' ),
								$after
							)
						);
					}
				}
			}
		}
	}
}
