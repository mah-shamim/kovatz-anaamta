<?php
/**
 * Class YITH_WCBK_Admin_Notices
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Admin_Notices' ) ) {
	/**
	 * YITH_WCBK_Admin class.
	 *
	 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since   3.0.0
	 */
	class YITH_WCBK_Admin_Notices {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Notices.
		 *
		 * @var array
		 */
		private $notices = array();

		/**
		 * Constructor
		 */
		private function __construct() {
			if ( current_user_can( 'manage_woocommerce' ) ) {
				add_action( 'admin_notices', array( $this, 'print_notices' ), 999 );
			}
		}

		/**
		 * Get core notices.
		 *
		 * @return array[]
		 */
		private function get_core_notices() {
			return array(
				'product-prices-sync-running'     => array(
					'message' =>
						sprintf(
							'<strong>%s</strong><br />%s <a href="%s">%s &rarr;</a>',
							sprintf(
							// translators: %s is the plugin name.
								esc_html__( '%s is updating bookable product prices in the background.', 'yith-booking-for-woocommerce' ),
								YITH_WCBK_PLUGIN_NAME
							),
							esc_html__( 'It will take a few minutes and this notice will disappear when completed.', 'yith-booking-for-woocommerce' ),
							esc_url( admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=yith_wcbk_sync_booking_product_prices&status=pending' ) ),
							esc_html__( 'View progress', 'yith-booking-for-woocommerce' )
						),
					'type'    => 'info',
				),
				'price-sync-no-bookings'          => array(
					'message' => __( 'You don\'t have any bookable product in your store.', 'yith-booking-for-woocommerce' ),
					'type'    => 'warning',
				),
				'generating-booking-lookup-table' => array(
					'message' =>
						sprintf(
							'<strong>%s</strong><br />%s <a href="%s">%s &rarr;</a>',
							sprintf(
							// translators: %s is the plugin name.
								esc_html__( '%s is updating booking data in background.', 'yith-booking-for-woocommerce' ),
								YITH_WCBK_PLUGIN_NAME
							),
							esc_html__( 'It will take a few minutes and this notice will disappear when complete.', 'yith-booking-for-woocommerce' ),
							esc_url( admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=yith_wcbk_update_booking_lookup_tables&status=pending' ) ),
							esc_html__( 'View progress', 'yith-booking-for-woocommerce' )
						),
					'type'    => 'info',
				),
			);
		}

		/**
		 * Retrieve notices.
		 *
		 * @param string $key Notice key.
		 *
		 * @return array|false
		 */
		private function get_notice( $key ) {
			$core_notices = $this->get_core_notices();

			return array_key_exists( $key, $core_notices ) ? $core_notices[ $key ] : false;
		}

		/**
		 * Retrieve notices.
		 *
		 * @return array
		 */
		public function get_notices() {
			return $this->notices;
		}

		/**
		 * Add notice
		 *
		 * @param string $key Notice key.
		 */
		public function add_notice( $key ) {
			$this->notices[] = $key;
			$this->notices   = array_unique( $this->notices );
		}

		/**
		 * Populate core notices.
		 */
		private function populate_core_notices() {
			if ( yith_wcbk_update_product_lookup_tables_is_running() ) {
				$this->add_notice( 'generating-booking-lookup-table' );
			}
		}

		/**
		 * Print notices
		 */
		public function print_notices() {
			$screen              = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$screen_id           = $screen ? $screen->id : '';
			$panel_page          = yith_wcbk()->admin ? yith_wcbk()->admin->get_panel_page() : false;
			$is_panel            = $panel_page && strpos( $screen->id, 'page_' . $panel_page ) > 0;
			$excluded_screen_ids = array( 'product' );

			if ( ( in_array( $screen_id, yith_wcbk_booking_admin_screen_ids(), true ) || $is_panel ) && ! in_array( $screen_id, $excluded_screen_ids, true ) ) {

				$this->populate_core_notices();
				$notices = $this->get_notices();

				foreach ( $notices as $notice_key ) {
					$notice = $this->get_notice( $notice_key );
					if ( $notice && ! empty( $notice['message'] ) ) {
						$message     = $notice['message'];
						$type        = $notice['type'] ?? 'info';
						$dismissible = $notice['dismissible'] ?? true;
						$key         = $notice['key'] ?? $notice_key;
						yith_wcbk_print_notice( $message, $type, $dismissible, $key );
					}
				}
			}
		}
	}
}
