<?php
/**
 * YITH_WCBK_Booking_Metabox
 * handle meta-boxes for booking object.
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Booking_Metabox' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Metabox
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Booking_Metabox {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Magic getter
		 * to handle deprecations.
		 *
		 * @param string $key The key.
		 *
		 * @return mixed
		 */
		public function __get( $key ) {
			if ( 'booking_post_type' === $key ) {
				yith_wcbk_doing_it_wrong( $key, 'This property was deprecated. Use YITH_WCBK_Post_Types::BOOKING instead.', '3.0.0' );
				$this->$key = YITH_WCBK_Post_Types::BOOKING;

				return $this->$key;
			}

			return null;
		}

		/**
		 * YITH_WCBK_Booking_Metabox constructor.
		 */
		private function __construct() {
			add_action( 'admin_menu', array( $this, 'remove_publish_box' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_booking' ), 10, 3 );
		}

		/**
		 * Remove publish box from edit booking
		 */
		public function remove_publish_box() {
			remove_meta_box( 'submitdiv', YITH_WCBK_Post_Types::BOOKING, 'side' );
		}

		/**
		 * Add meta boxes to edit booking page
		 *
		 * @param string $post_type Post type.
		 */
		public function add_meta_boxes( $post_type ) {
			if ( YITH_WCBK_Post_Types::BOOKING !== $post_type ) {
				return;
			}

			$meta_boxes = $this->get_meta_boxes();

			if ( empty( $meta_boxes ) || ! is_array( $meta_boxes ) ) {
				return;
			}

			foreach ( $meta_boxes as $meta_box ) {
				add_meta_box(
					$meta_box['id'],
					$meta_box['title'],
					array( $this, 'meta_box_print' ),
					YITH_WCBK_Post_Types::BOOKING,
					$meta_box['context'],
					$meta_box['priority']
				);
			}
		}

		/**
		 * Get the meta-boxes
		 *
		 * @return array
		 */
		protected function get_meta_boxes() {
			$meta_boxes = array(
				10 => array(
					'id'       => 'yith-booking-notes',
					'title'    => __( 'Booking notes', 'yith-booking-for-woocommerce' ),
					'context'  => 'side',
					'priority' => 'default',
				),
				20 => array(
					'id'       => 'yith-booking-actions',
					'title'    => __( 'Booking actions', 'yith-booking-for-woocommerce' ),
					'context'  => 'side',
					'priority' => 'high',
				),
				30 => array(
					'id'       => 'yith-booking-data',
					'title'    => __( 'Booking data', 'yith-booking-for-woocommerce' ),
					'context'  => 'normal',
					'priority' => 'high',
				),
			);

			$meta_boxes = apply_filters( 'yith_wcbk_booking_metaboxes_array', $meta_boxes );
			ksort( $meta_boxes );

			return $meta_boxes;
		}

		/**
		 * Print meta_boxes content
		 *
		 * @param WP_Post $post     Post object.
		 * @param array   $meta_box The meta-box array.
		 */
		public function meta_box_print( $post, $meta_box ) {

			if ( ! isset( $meta_box['id'] ) ) {
				return;
			}

			switch ( $meta_box['id'] ) {
				case 'yith-booking-notes':
					$booking = yith_get_booking( $post->ID );
					$notes   = $booking->get_notes();
					include YITH_WCBK_VIEWS_PATH . 'metaboxes/html-booking-meta-notes.php';
					break;
				case 'yith-booking-actions':
					$booking = yith_get_booking( $post->ID );
					include YITH_WCBK_VIEWS_PATH . 'metaboxes/html-booking-meta-actions.php';
					break;
				case 'yith-booking-data':
					$booking = yith_get_booking( $post->ID );
					include YITH_WCBK_VIEWS_PATH . 'metaboxes/html-booking-meta-data.php';
					break;
				default:
					do_action( 'yith_wcbk_booking_' . $meta_box['id'] . '_print', $post );
					break;
			}
		}

		/**
		 * Save meta on save post
		 *
		 * @param int     $post_id The Post ID.
		 * @param WP_Post $post    The Post object.
		 * @param bool    $update  Update flag.
		 */
		public function save_booking( $post_id, $post, $update ) {
			static $synchronized_bookings = array();
			if ( get_post_type( $post_id ) !== YITH_WCBK_Post_Types::BOOKING || ! $update ) {
				return;
			}

			if ( isset( $_REQUEST['yith-wcbk-booking-save-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['yith-wcbk-booking-save-nonce'] ) ), 'save-booking' ) ) {
				$booking = yith_get_booking( $post_id );

				if ( $booking ) {
					if ( isset( $_POST['yith_booking_date'], $_POST['yith_booking_date_hour'], $_POST['yith_booking_date_minute'] ) ) {
						$date      = sanitize_text_field( wp_unslash( $_POST['yith_booking_date'] ) );
						$hour      = absint( $_POST['yith_booking_date_hour'] );
						$minute    = absint( $_POST['yith_booking_date_minute'] );
						$post_date = strtotime( sprintf( '%s %s:%s:00', $date, $hour, $minute ) );
						$post_date = $post_date + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
						$post_date = date_i18n( 'Y-m-d H:i:s', $post_date );
						$booking->set_date_created( $post_date );
					}

					if ( isset( $_POST['yith_booking_status'] ) ) {
						$status = sanitize_text_field( wp_unslash( $_POST['yith_booking_status'] ) );
						$booking->set_status( $status );
					}

					if ( isset( $_POST['yith_booking_order'] ) ) {
						$order_id = absint( $_POST['yith_booking_order'] );
						$booking->set_order_id( $order_id );
					}

					if ( isset( $_POST['yith_booking_user'] ) ) {
						$user_id = absint( $_POST['yith_booking_user'] );
						$booking->set_user_id( $user_id );
					}

					if ( isset( $_POST['yith_booking_from'] ) ) {
						$from = strtotime( sanitize_text_field( wp_unslash( $_POST['yith_booking_from'] ) ) );
						$booking->set_from( $from );
					}

					if ( isset( $_POST['yith_booking_to'] ) ) {
						$to = strtotime( sanitize_text_field( wp_unslash( $_POST['yith_booking_to'] ) ) );
						if ( $booking->is_all_day() ) {
							$to = strtotime( '23:59:59', $to );
						}
						$booking->set_to( $to );
					}

					if ( $booking->has_person_types() ) {
						if ( ! empty( $_POST['yith_booking_person_type'] ) ) {
							$person_types      = $booking->get_person_types( 'edit' );
							$post_person_types = wc_clean( wp_unslash( $_POST['yith_booking_person_type'] ) );

							$total_persons = 0;

							foreach ( $person_types as $key => $person_type ) {
								$person_type_id = $person_type['id'];
								if ( isset( $post_person_types[ $person_type_id ] ) ) {
									$person_types[ $key ]['number'] = absint( $post_person_types[ $person_type_id ] );
								}

								$total_persons += absint( $post_person_types[ $person_type_id ] );
							}
							$booking->set_person_types( $person_types );
						}
					} else {
						if ( isset( $_POST['yith_booking_persons'] ) ) {
							$persons = absint( $_POST['yith_booking_persons'] );
							$booking->set_persons( $persons );
						}
					}

					if ( isset( $_POST['yith_booking_service_quantities'] ) ) {
						$service_quantities = wc_clean( wp_unslash( $_POST['yith_booking_service_quantities'] ) );
						$booking->set_service_quantities( $service_quantities );
					}

					$booking->save();

					if ( ! in_array( $booking->get_id(), $synchronized_bookings, true ) ) {
						do_action( 'yith_wcbk_google_calendar_booking_sync_on_update', $booking->get_id() );
						$synchronized_bookings[] = $booking->get_id();
					}
				}
			}
		}
	}
}

return YITH_WCBK_Booking_Metabox::get_instance();
