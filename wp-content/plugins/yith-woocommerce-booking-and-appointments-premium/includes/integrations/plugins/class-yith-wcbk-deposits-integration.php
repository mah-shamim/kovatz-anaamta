<?php
/**
 * Class YITH_WCBK_Deposits_Integration
 * Deposits integration
 *
 * @author  YITH
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Deposits_Integration
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.1
 */
class YITH_WCBK_Deposits_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Init
	 */
	protected function init() {
		if ( $this->is_enabled() ) {
			add_filter( 'yith_wcdp_is_deposit_enabled_on_product', array( $this, 'disable_deposit_on_bookings_requiring_confirmation' ), 10, 2 );
			add_action( 'yith_wcdp_booking_add_to_cart', array( $this, 'add_deposit_to_booking' ) );
			add_filter( 'yith_wcbk_product_form_get_booking_data', array( $this, 'add_deposit_price_to_booking_data' ), 10, 2 );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_action( 'woocommerce_order_status_cancelled', array( $this, 'set_booking_as_cancelled_when_balance_is_cancelled' ), 10, 2 );

			add_filter( 'yith_wcbk_get_bookings_by_order_args', array( $this, 'filter_get_bookings_by_order_args' ), 10, 3 );
			add_filter( 'yith_wcbk_orders_set_booking_as_paid', array( $this, 'disable_setting_booking_as_paid_for_deposit_or_balance_orders' ), 10, 3 );
		}
	}

	/**
	 * Filter args of YITH_WCBK_Booking_Helper::get_bookings_by_order to retrieve the correct bookings related to balance orders.
	 *
	 * @param array     $args          Arguments to be filtered.
	 * @param int       $order_id      Order ID.
	 * @param int|false $order_item_id Order item ID.
	 *
	 * @return array
	 * @since 3.0
	 */
	public function filter_get_bookings_by_order_args( $args, $order_id, $order_item_id = false ) {
		if ( ! $order_item_id ) {
			$order = wc_get_order( $order_id );
			if ( $order && $order->is_created_via( 'yith_wcdp_balance_order' ) ) {
				$args['order_id'] = $order->get_parent_id();

				$items    = $order->get_items();
				$item_ids = array_filter(
					array_map(
						function ( $item ) {
							return absint( $item->get_meta( '_deposit_item_id' ) );
						},
						$items
					)
				);

				/**
				 * If Deposits plugin creates a unique order for all items, there is no '_deposit_item_id' meta set in order items.
				 * So, in this case, we should take all the bookings of the deposit order, instead of retrieving only the ones related to specific item IDs.
				 */
				if ( $item_ids ) {
					$args['data_query'] = array(
						array(
							'key'     => '_order_item_id',
							'value'   => $items,
							'compare' => 'IN',
						),
					);
				}
			}
		}

		return $args;
	}

	/**
	 * Disable setting booking as paid for deposit/balance orders.
	 *
	 * @param bool     $set_flag Set flag.
	 * @param int      $order_id Order ID.
	 * @param WC_Order $order    The order.
	 *
	 * @return bool
	 * @since 3.0
	 */
	public function disable_setting_booking_as_paid_for_deposit_or_balance_orders( $set_flag, $order_id, $order ) {
		$set_booking_as_paid_when = get_option( 'yith-wcbk-set-booking-paid-for-deposits', 'deposit' );
		if ( 'balance' === $set_booking_as_paid_when && $order->get_meta( '_has_deposit' ) ) {
			$set_flag = false;
		} elseif ( 'deposit' === $set_booking_as_paid_when && $order->get_meta( '_has_full_payment' ) ) {
			$set_flag = false;
		}

		return $set_flag;
	}

	/**
	 * Disable deposits on booking products that requires confirmation
	 *
	 * @param bool $enabled    Deposit enabled flag.
	 * @param int  $product_id Product ID.
	 *
	 * @return bool
	 * @since 2.1
	 */
	public function disable_deposit_on_bookings_requiring_confirmation( $enabled, $product_id ) {
		/**
		 * Booking product.
		 *
		 * @var WC_Product_Booking $product
		 */
		$product = wc_get_product( $product_id );
		if ( $product && yith_wcbk_is_booking_product( $product ) && $product->is_confirmation_required() ) {
			$enabled = false;
		}

		return $enabled;
	}

	/**
	 * Add deposit price to booking data.
	 *
	 * @param array              $booking_data Booking data.
	 * @param WC_Product_Booking $product      The booking product.
	 *
	 * @return array
	 */
	public function add_deposit_price_to_booking_data( $booking_data, $product ) {
		$price              = $product->calculate_price( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$deposit_price      = YITH_WCDP_Premium()->get_deposit( $product->get_id(), $price );
		$deposit_price_html = wc_price( $deposit_price );

		$booking_data['deposit_price'] = $deposit_price_html;

		return $booking_data;
	}

	/**
	 * Add Deposits to Booking Products
	 *
	 * @param WC_Product_Booking $product Booking product.
	 */
	public function add_deposit_to_booking( $product ) {
		if ( ! $product->is_confirmation_required() ) {
			add_action( 'woocommerce_before_add_to_cart_button', array( YITH_WCDP_Frontend_Premium(), 'print_single_add_deposit_to_cart_template' ) );
		}
	}

	/**
	 * Returns post parent of a Balance order
	 * If order is not a balance order, it will return false
	 *
	 * @param int|WC_Order $order_id Order or order ID.
	 *
	 * @return int|bool If order is a balance order, and has post parent, returns parent ID; false otherwise
	 */
	public function get_parent_order_id( $order_id ) {
		$order = wc_get_order( $order_id );

		return $order && $order->get_meta( '_has_full_payment' ) ? $order->get_parent_id() : false;

	}

	/**
	 * Set Booking as cancelled when the balance is cancelled
	 *
	 * @param int      $order_id Order id.
	 * @param WC_Order $order    The order.
	 *
	 * @since 2.1.4
	 */
	public function set_booking_as_cancelled_when_balance_is_cancelled( $order_id, $order ) {
		$parent_order_id = $this->get_parent_order_id( $order_id );
		$bookings        = $parent_order_id ? yith_wcbk_booking_helper()->get_bookings_by_order( $parent_order_id ) : false;
		if ( ! ! $bookings ) {
			$order_number = $order ? $order->get_order_number() : $order_id;
			foreach ( $bookings as $booking ) {
				if ( $booking instanceof YITH_WCBK_Booking ) {
					$order_link      = sprintf(
						'<a href="%s">#%s</a>',
						admin_url( 'post.php?post=' . $order_id . '&action=edit' ),
						$order_number
					);
					$additional_note = sprintf(
					// translators: %s is the order link.
						__( 'Reason: balance order %s has been cancelled.', 'yith-booking-for-woocommerce' ),
						$order_link
					);
					$booking->update_status( 'cancelled', $additional_note );
				}
			}
		}
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'yith-wcbk-integration-deposits-booking-form', YITH_WCBK_ASSETS_URL . '/js/integrations/deposits/deposits-booking-form' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );

		wp_enqueue_script( 'yith-wcbk-integration-deposits-booking-form' );

	}
}
