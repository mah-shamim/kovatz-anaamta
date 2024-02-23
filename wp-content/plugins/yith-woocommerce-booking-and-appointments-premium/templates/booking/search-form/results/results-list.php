<?php
/**
 * Booking Search Form Results List Template
 * Shows list of booking search form results
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/results/results-list.php.
 *
 * @var WP_Query $products        WP Query for products.
 * @var array    $booking_request Booking request.
 * @var int      $current_page    Current page number.
 * @var array    $product_ids     Product IDs.
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

if ( $products->have_posts() ) {

	while ( $products->have_posts() ) {
		$products->the_post();
		/**
		 * The booking product.
		 *
		 * @var WC_Product_Booking $product
		 */
		global $product;

		$booking_request['add-to-cart'] = $product->get_id();
		$booking_data                   = YITH_WCBK_Cart::get_booking_data_from_request( $booking_request );

		$booking_data[ YITH_WCBK_Search_Form_Helper::RESULT_KEY_IN_BOOKING_DATA ] = true;

		if ( isset( $booking_data['duration'] ) ) {
			$booking_data['duration'] = intdiv( $booking_data['duration'], $product->get_duration() );
		}

		$booking_data = $product->parse_booking_data_args( $booking_data );
		$the_price    = '';

		if ( ! empty( $booking_request['from'] ) && ! empty( $booking_request['to'] ) && 'day' === $product->get_duration_unit() ) {
			$the_price = $product->calculate_price( $booking_data );
			$product->set_price( $the_price );
		} else {
			unset( $booking_data['from'] );
			unset( $booking_data['to'] );
			unset( $booking_data['duration'] );
		}

		wc_get_template( 'booking/search-form/results/single.php', compact( 'product', 'booking_data', 'the_price' ), '', YITH_WCBK_TEMPLATE_PATH );
	}

	wp_reset_postdata();
}
