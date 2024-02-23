<?php
/**
 * Class YITH_WCBK_Search_Form_Frontend
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Search_Form_Frontend' ) ) {
	/**
	 * Class YITH_WCBK_Search_Form_Frontend
	 * handle Booking Forms in frontend
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Search_Form_Frontend {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Search_Form_Frontend constructor.
		 */
		protected function __construct() {
			add_action( 'yith_wcbk_booking_search_form_print_field', array( $this, 'print_field' ), 10, 3 );

			add_action( 'pre_get_posts', array( $this, 'filter_search_results_in_shop' ) );
			add_filter( 'woocommerce_loop_product_link', array( $this, 'add_booking_data_in_search_result_links' ), 10, 2 );
			add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_booking_data_in_search_result_links' ), 10, 2 );
			add_filter( 'woocommerce_get_price_html', array( $this, 'show_price_based_on_search_params' ), 10, 2 );
		}

		/**
		 * Is searching?
		 *
		 * @return bool
		 * @since 2.1.9
		 */
		public function is_search() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return isset( $_REQUEST['yith-wcbk-booking-search'] ) && 'search-bookings' === $_REQUEST['yith-wcbk-booking-search'];
		}

		/**
		 * Show prices in Shop page based on search parameters
		 *
		 * @param string     $price_html Price HTML.
		 * @param WC_Product $product    The product.
		 *
		 * @return string
		 * @since 2.1.9
		 */
		public function show_price_based_on_search_params( $price_html, $product ) {
			// The price calculation is already made, so here we keep only the filter.

			if ( $this->is_search() && yith_wcbk_is_booking_product( $product ) && 'day' === $product->get_duration_unit() ) {
				$price_html = apply_filters( 'yith_wcbk_get_price_based_on_search_param', $price_html, $product );
			}

			return $price_html;
		}

		/**
		 * Add booking data in product links when showing results in Shop Page
		 *
		 * @param string             $permalink The permalink.
		 * @param WC_Product_Booking $product   The product.
		 *
		 * @return string
		 * @since 2.0.6
		 */
		public function add_booking_data_in_search_result_links( $permalink, $product ) {
			if ( $this->is_search() && yith_wcbk_is_booking_product( $product ) && 'day' === $product->get_duration_unit() ) {
				$booking_request               = $_REQUEST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$booking_request['product_id'] = $product->get_id();
				if ( isset( $booking_request['services'] ) ) {
					$booking_request['booking_services'] = $booking_request['services'];
				}

				if ( isset( $booking_request['duration'] ) ) {
					$booking_request['duration'] = intdiv( $booking_request['duration'], $product->get_duration() );
				}

				$booking_data         = YITH_WCBK_Cart::get_booking_data_from_request( $booking_request );
				$key                  = YITH_WCBK_Search_Form_Helper::RESULT_KEY_IN_BOOKING_DATA;
				$booking_data[ $key ] = true;
				$permalink            = $product->get_permalink_with_data( $booking_data );
			}

			return $permalink;
		}

		/**
		 * Filter search results in shop
		 *
		 * @param WP_Query $query The query.
		 */
		public function filter_search_results_in_shop( $query ) {
			if ( $query->is_main_query() && $this->is_search() ) {
				$product_ids = yith_wcbk()->search_form_helper->search_booking_products( $_REQUEST ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				if ( ! $product_ids ) {
					$product_ids = array( 0 );
				}

				$query->set( 'post__in', $product_ids );
			}
		}

		/**
		 * Print field.
		 *
		 * @param string                $field_name  Field name.
		 * @param array                 $field_data  Field data.
		 * @param YITH_WCBK_Search_Form $search_form Search form.
		 */
		public function print_field( $field_name, $field_data, $search_form ) {
			$template = $field_name;

			if ( ! empty( $field_data['type'] ) ) {
				$template .= '-' . $field_data['type'];
			}

			$template .= '.php';

			$args = array(
				'field_name'  => $field_name,
				'field_data'  => $field_data,
				'search_form' => $search_form,
			);

			wc_get_template( 'booking/search-form/fields/' . $template, $args, '', YITH_WCBK_TEMPLATE_PATH );
		}
	}
}
