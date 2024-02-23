<?php
/**
 * Class YITH_WCBK_AJAX
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_AJAX' ) ) {
	/**
	 * Class YITH_WCBK_AJAX
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_AJAX {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Is testing?
		 *
		 * @var bool
		 */
		public $testing = false;

		/**
		 * YITH_WCBK_AJAX constructor.
		 */
		private function __construct() {
			$ajax_actions = array(
				'json_search_order',
				'json_search_booking_products',
				'get_product_booking_form',
				'search_booking_products',
				'search_booking_products_paged',
				'add_booking_note',
				'delete_booking_note',
				'get_booking_data',
				'get_booking_available_times',
				'get_product_not_available_dates',
			);

			foreach ( $ajax_actions as $ajax_action ) {
				add_action( 'wp_ajax_yith_wcbk_' . $ajax_action, array( $this, $ajax_action ) );
				add_action( 'wp_ajax_nopriv_yith_wcbk_' . $ajax_action, array( $this, $ajax_action ) );
			}
		}

		/**
		 * Start Booking AJAX call
		 *
		 * @param string $context The context (admin or frontend).
		 */
		private function ajax_start( $context = 'admin' ) {
			error_reporting( 0 ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting

			! defined( 'YITH_WCBK_DOING_AJAX' ) && define( 'YITH_WCBK_DOING_AJAX', true );
			if ( 'admin' === $context ) {
				! defined( 'YITH_WCBK_DOING_AJAX_ADMIN' ) && define( 'YITH_WCBK_DOING_AJAX_ADMIN', true );
			} elseif ( 'frontend' === $context ) {
				! defined( 'YITH_WCBK_DOING_AJAX_FRONTEND' ) && define( 'YITH_WCBK_DOING_AJAX_FRONTEND', true );
			}
		}

		/**
		 * Add booking note via ajax.
		 */
		public function add_booking_note() {
			$this->ajax_start();

			check_ajax_referer( 'add-booking-note', 'security' );

			if ( ! current_user_can( 'edit_' . YITH_WCBK_Post_Types::BOOKING . 's' ) ) {
				wp_die( - 1 );
			}

			$post_id   = absint( $_POST['post_id'] ?? 0 );
			$note      = sanitize_textarea_field( wp_unslash( $_POST['note'] ?? '' ) );
			$note_type = sanitize_text_field( wp_unslash( $_POST['note_type'] ?? 'admin' ) );
			$booking   = yith_get_booking( $post_id );

			$note_classes = 'note ' . $note_type;

			if ( $booking && $note ) {
				$note_id = $booking->add_note( $note_type, $note );

				echo '<li rel="' . esc_attr( $note_id ) . '" class="' . esc_attr( $note_classes ) . '">';
				echo '<div class="note_content">';
				echo wp_kses_post( wpautop( wptexturize( $note ) ) );
				echo '</div><p class="meta"><a href="#" class="delete-booking-note">' . esc_html__( 'Delete note', 'yith-booking-for-woocommerce' ) . '</a></p>';
				echo '</li>';
			}
			wp_die();
		}

		/**
		 * Delete booking note via ajax.
		 */
		public function delete_booking_note() {
			$this->ajax_start();

			check_ajax_referer( 'delete-booking-note', 'security' );

			if ( ! current_user_can( 'edit_' . YITH_WCBK_Post_Types::BOOKING . 's' ) ) {
				wp_die( - 1 );
			}

			$note_id = absint( $_POST['note_id'] ?? 0 );

			if ( $note_id ) {
				yith_wcbk_delete_booking_note( $note_id );
			}
			wp_die();
		}

		/**
		 * Order Search
		 */
		public function json_search_order() {
			$this->ajax_start();

			global $wpdb;
			ob_start();

			check_ajax_referer( 'search-orders', 'security' );

			$term = wc_clean( wp_unslash( $_GET['term'] ?? '' ) );

			if ( empty( $term ) ) {
				die();
			}

			$found_orders = array();

			$term = apply_filters( 'yith_wcbk_json_search_order_number', $term );

			$query_orders = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ID, post_title FROM {$wpdb->posts} AS posts
					WHERE posts.post_type = 'shop_order'
					AND posts.ID LIKE %s",
					'%' . $wpdb->esc_like( $term ) . '%'
				)
			);

			if ( $query_orders ) {
				foreach ( $query_orders as $item ) {
					$order_number              = apply_filters( 'yith_wcbk_order_number', '#' . $item->ID, $item->ID );
					$found_orders[ $item->ID ] = $order_number . ' &ndash; ' . esc_html( $item->post_title );
				}
			}

			return $this->send_json( $found_orders );
		}

		/**
		 * Booking Products Search
		 */
		public function json_search_booking_products() {
			$this->ajax_start();

			ob_start();
			check_ajax_referer( 'search-bookings', 'security' );

			$term    = wc_clean( wp_unslash( $_REQUEST['term']['term'] ?? $_REQUEST['term'] ?? '' ) );
			$exclude = array();

			if ( empty( $term ) ) {
				die();
			}

			if ( ! empty( $_REQUEST['exclude'] ) ) {
				$exclude = array_map( 'intval', explode( ',', wc_clean( wp_unslash( $_REQUEST['exclude'] ) ) ) );
			}

			$found_products = array();
			$booking_term   = get_term_by( 'slug', 'booking', 'product_type' );
			if ( $booking_term ) {
				$posts_in = array_unique( (array) get_objects_in_term( $booking_term->term_id, 'product_type' ) );
				if ( count( $posts_in ) > 0 ) {
					$args = array(
						'post_type'        => 'product',
						'post_status'      => 'publish',
						'numberposts'      => - 1,
						'orderby'          => 'title',
						'order'            => 'asc',
						'post_parent'      => 0,
						'suppress_filters' => 0,
						'include'          => $posts_in,
						's'                => $term,
						'fields'           => 'ids',
						'exclude'          => $exclude,
					);

					$args  = apply_filters( 'yith_wcbk_json_search_booking_products_args', $args );
					$posts = get_posts( $args );

					if ( ! empty( $posts ) ) {
						foreach ( $posts as $post ) {
							$product = wc_get_product( $post );

							if ( ! current_user_can( 'read_product', $post ) ) {
								continue;
							}

							$found_products[ $post ] = rawurldecode( $product->get_formatted_name() );
						}
					}
				}
			}

			$found_products = apply_filters( 'yith_wcbk_json_search_found_booking_products', $found_products );

			return $this->send_json( $found_products );
		}

		/**
		 * Get the product booking form
		 */
		public function get_product_booking_form() {
			$this->ajax_start();

			check_ajax_referer( 'yith-wcbk-get-booking-form', 'security' );

			if ( isset( $_POST['product_id'] ) ) {
				$product = wc_get_product( absint( $_POST['product_id'] ) );
				$args    = array(
					'show_price'      => (bool) $_POST['show_price'] ?? true,
					'additional_data' => wc_clean( wp_unslash( $_POST['additional_data'] ?? array() ) ),
				);
				do_action( 'yith_wcbk_booking_form', $product, $args );
			}
			die();
		}

		/**
		 * Search Forms: search booking products
		 */
		public function search_booking_products() {
			$this->ajax_start( 'frontend' );

			check_ajax_referer( 'search-booking-products', 'security' );

			if ( isset( $_REQUEST['yith-wcbk-booking-search'] ) && 'search-bookings' === $_REQUEST['yith-wcbk-booking-search'] ) {
				$this->set_in_search_form_const();

				$from         = wc_clean( wp_unslash( $_REQUEST['from'] ?? '' ) );
				$to           = wc_clean( wp_unslash( $_REQUEST['to'] ?? '' ) );
				$persons      = wc_clean( wp_unslash( $_REQUEST['persons'] ?? 1 ) );
				$person_types = wc_clean( wp_unslash( $_REQUEST['person_types'] ?? array() ) );
				$services     = wc_clean( wp_unslash( $_REQUEST['services'] ?? array() ) );

				if ( ! ! $person_types && is_array( $person_types ) ) {
					$persons = array_sum( array_values( $person_types ) );
				}

				$product_ids = yith_wcbk()->search_form_helper->search_booking_products( $_REQUEST );

				if ( ! $product_ids ) {
					$no_bookings_available_text = __( 'No booking available for this search', 'yith-booking-for-woocommerce' );
					echo wp_kses_post( apply_filters( 'yith_wcbk_search_booking_products_no_bookings_available_text', $no_bookings_available_text ) );
					do_action( 'yith_wcbk_search_booking_products_no_bookings_available_after' );
					die();
				}

				$current_page = 1;

				$args     = array(
					'post_type'           => 'product',
					'ignore_sticky_posts' => 1,
					'no_found_rows'       => 1,
					'posts_per_page'      => apply_filters( 'yith_wcbk_ajax_search_booking_products_posts_per_page', 12 ),
					'paged'               => $current_page,
					'post__in'            => $product_ids,
					'orderby'             => 'post__in',
					'meta_query'          => WC()->query->get_meta_query(),
				);
				$args     = apply_filters( 'yith_wcbk_ajax_search_booking_products_query_args', $args, $product_ids );
				$products = new WP_Query( $args );

				$booking_request = array(
					'from'             => $from,
					'to'               => $to,
					'persons'          => $persons,
					'person_types'     => $person_types,
					'booking_services' => $services,
				);

				wc_get_template( 'booking/search-form/results/results.php', compact( 'booking_request', 'products', 'product_ids', 'current_page' ), '', YITH_WCBK_TEMPLATE_PATH );
			}

			die();
		}

		/**
		 * Search Forms: search booking products paged
		 */
		public function search_booking_products_paged() {
			$this->ajax_start( 'frontend' );

			check_ajax_referer( 'search-booking-products-paged', 'security' );

			if ( ! empty( $_REQUEST['product_ids'] ) && ! empty( $_REQUEST['booking_request'] ) && ! empty( $_REQUEST['page'] ) ) {
				$this->set_in_search_form_const();

				$product_ids     = wc_clean( wp_unslash( $_REQUEST['product_ids'] ) );
				$booking_request = wc_clean( wp_unslash( $_REQUEST['booking_request'] ) );
				$current_page    = absint( $_REQUEST['page'] );

				$args = array(
					'post_type'           => 'product',
					'ignore_sticky_posts' => 1,
					'no_found_rows'       => 1,
					'posts_per_page'      => apply_filters( 'yith_wcbk_ajax_search_booking_products_posts_per_page', 12 ),
					'paged'               => $current_page,
					'post__in'            => $product_ids,
					'meta_query'          => WC()->query->get_meta_query(),
				);
				$args = apply_filters( 'yith_wcbk_ajax_search_booking_products_query_args', $args, $product_ids );

				$products = new WP_Query( $args );

				wc_get_template( 'booking/search-form/results/results-list.php', compact( 'products', 'booking_request' ), '', YITH_WCBK_TEMPLATE_PATH );
			}

			die();
		}

		/**
		 * Define Search Form Results const
		 */
		public function set_in_search_form_const() {
			if ( ! defined( 'YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS' ) ) {
				define( 'YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS', true );
			}
		}

		/**
		 * Get booking data as Availability and price
		 *
		 * @param array|false $request The request.
		 *
		 * @return array
		 */
		public function get_booking_data( $request = false ) {
			$this->ajax_start( 'frontend' );

			check_ajax_referer( 'get-booking-data', 'security' );

			$booking_data = false;
			$request      = ! ! $request && is_array( $request ) ? $request : wc_clean( wp_unslash( $_POST ) );
			$request      = apply_filters( 'yith_wcbk_ajax_booking_data_request', $request );

			// The minimum number of persons is 1, also in case of bookings without people (to allow correct price calculation).
			$request['persons'] = max( 1, $request['persons'] ?? 1 );

			if ( empty( $request['product_id'] ) || empty( $request['from'] ) || ( empty( $request['duration'] ) && empty( $request['to'] ) ) ) {
				$booking_data = array( 'error' => _x( 'Required POST variable not set', 'Error', 'yith-booking-for-woocommerce' ) );
			} else {
				$date_helper = yith_wcbk_date_helper();
				$product_id  = absint( $request['product_id'] );
				/**
				 * The booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product = wc_get_product( $product_id );

				if ( $product ) {
					$from = strtotime( $request['from'] );
					if ( isset( $request['to'] ) ) {
						$to = strtotime( $request['to'] );
					} else {
						$duration = absint( $request['duration'] ) * $product->get_duration();
						if ( $product->is_full_day() ) {
							$duration --;
						}
						$to = $date_helper->get_time_sum( $from, $duration, $product->get_duration_unit() );
					}

					$is_available_args = YITH_WCBK_Cart::get_booking_data_from_request( $request );
					$is_available_args = apply_filters(
						'yith_wcbk_product_form_get_booking_data_available_args',
						$is_available_args,
						$product,
						$request
					);

					$is_available_args['return'] = 'array';

					$availability = $product->is_available( $is_available_args );
					$is_available = $availability['available'];

					$bookable_args = array(
						'product'               => $product,
						'bookable'              => $is_available,
						'from'                  => $from,
						'to'                    => $to,
						'non_available_reasons' => $availability['non_available_reasons'],
					);
					ob_start();
					wc_get_template( 'single-product/add-to-cart/bookable.php', $bookable_args, '', YITH_WCBK_TEMPLATE_PATH );
					$message = ob_get_clean();

					if ( $is_available ) {
						$show_totals = yith_wcbk()->settings->show_totals();
						$totals      = $product->calculate_totals( $request, $show_totals );
						$price       = $product->calculate_price_from_totals( $totals );
						$price       = apply_filters( 'yith_wcbk_booking_product_calculated_price', $price, $request, $product );
						$price_html  = $product->get_calculated_price_html( $price );
						if ( $show_totals ) {
							ob_start();
							wc_get_template( 'single-product/add-to-cart/booking-form/totals-list.php', compact( 'totals', 'price_html', 'product' ), '', YITH_WCBK_TEMPLATE_PATH );
							$totals_html = ob_get_clean();
						} else {
							$totals_html = '';
						}
					} else {
						$totals      = array();
						$totals_html = '';
						$price       = false;
						$price_html  = apply_filters( 'yith_wcbk_product_form_not_bookable_price_html', yith_wcbk_get_label( 'not-bookable' ), $request, $bookable_args );
					}

					$booking_data = array(
						'is_available' => $is_available,
						'totals'       => $totals,
						'totals_html'  => $totals_html,
						'price'        => $price_html,
						'raw_price'    => $price,
						'message'      => $message,
					);

					$booking_data = apply_filters( 'yith_wcbk_product_form_get_booking_data', $booking_data, $product, $bookable_args, $request );
				}
			}

			if ( ! $booking_data ) {
				$booking_data = array( 'error' => _x( 'Product not found', 'Error', 'yith-booking-for-woocommerce' ) );
			}

			return $this->send_json( $booking_data );
		}


		/**
		 * Get booking available times
		 *
		 * @param array|false $request The request.
		 *
		 * @return array|bool
		 * @since 2.0.0
		 */
		public function get_booking_available_times( $request = '' ) {
			check_ajax_referer( 'get-available-times', 'security' );

			$data    = false;
			$request = ! ! $request && is_array( $request ) ? $request : wc_clean( wp_unslash( $_POST ) );
			$request = apply_filters( 'yith_wcbk_ajax_booking_available_times_request', $request );

			if ( empty( $request['product_id'] ) || empty( $request['from'] ) || empty( $request['duration'] ) ) {
				$data = array( 'error' => _x( 'Required POST variable not set', 'Error', 'yith-booking-for-woocommerce' ) );
			} else {

				$product_id = $request['product_id'];
				/**
				 * The booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product = wc_get_product( $product_id );

				if ( $product && yith_wcbk_is_booking_product( $product ) ) {
					$time_data      = $product->create_availability_time_array( $request['from'], $request['duration'] );
					$time_data_html = '<option value="">' . __( 'Select Time', 'yith-booking-for-woocommerce' ) . '</option>';

					$default_start_time = $product->get_default_start_time();
					$first              = true;

					foreach ( $time_data as $time ) {
						$formatted_time = date_i18n( yith_wcbk()->settings->get_time_picker_format(), strtotime( $time ) );
						$formatted_time = apply_filters( 'yith_wcbk_ajax_booking_available_times_formatted_time', $formatted_time, $time, $product );
						$selected       = 'first-available' === $default_start_time ? selected( $first, true, false ) : '';
						$first          = false;

						$time_data_html .= "<option value='$time' $selected>$formatted_time</option>";
					}

					$data = array(
						'time_data'      => $time_data,
						'time_data_html' => $time_data_html,
					);

					if ( ! $time_data ) {
						$data['time_data_html'] = '<option value="">' . __( 'No time available', 'yith-booking-for-woocommerce' ) . '</option>';
					}
				}
			}

			if ( false === $data ) {
				$data = array( 'error' => _x( 'Product not found', 'Error', 'yith-booking-for-woocommerce' ) );
			}

			return $this->send_json( $data );
		}

		/**
		 * Get non-available dates for product.
		 *
		 * @param array|false $request The request.
		 *
		 * @return array|bool
		 */
		public function get_product_not_available_dates( $request = false ) {
			check_ajax_referer( 'get-product-non-available-dates', 'security' );

			$data    = false;
			$request = ! ! $request && is_array( $request ) ? $request : wc_clean( wp_unslash( $_POST ) );
			$request = apply_filters( 'yith_wcbk_ajax_product_not_available_dates_request', $request );

			if ( empty( $request['product_id'] ) ) {

				$data = array( 'error' => _x( 'Required POST variable not set', 'Error', 'yith-booking-for-woocommerce' ) );

			} else {
				$product_id = $request['product_id'];

				/**
				 * The booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product = wc_get_product( $product_id );

				if ( $product && yith_wcbk_is_booking_product( $product ) ) {
					$date_info_args = array();
					if ( ! empty( $request['month_to_load'] ) && ! empty( $request['year_to_load'] ) ) {
						$month_to_load  = $request['month_to_load'];
						$year_to_load   = $request['year_to_load'];
						$start          = "$year_to_load-$month_to_load-01";
						$date_info_args = array( 'start' => $start );
					}
					$date_info = yith_wcbk_get_booking_form_date_info( $product, $date_info_args );

					$data = array(
						'not_available_dates' => $product->get_not_available_dates( $date_info['current_year'], $date_info['current_month'], $date_info['next_year'], $date_info['next_month'], 'day' ),
						'year_to_load'        => $date_info['next_year'],
						'month_to_load'       => $date_info['next_month'],
					);
				}
			}

			if ( false === $data ) {
				$data = array( 'error' => _x( 'Product not found', 'Error', 'yith-booking-for-woocommerce' ) );
			}

			return $this->send_json( $data );
		}


		/**
		 * Send JSON or return if testing.
		 *
		 * @param array $data The data.
		 *
		 * @return array|bool
		 */
		public function send_json( $data ) {
			if ( $this->testing ) {
				return $data;
			} else {
				wp_send_json( $data );

				return false;
			}
		}
	}
}
