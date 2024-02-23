<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Macros;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

class Purchased_Products extends \Jet_Engine_Base_Macros {

	/**
	 * Returns macros tag.
	 *
	 * @return string
	 */
	public function macros_tag() {
		return 'wc_get_purchased_products';
	}

	/**
	 * Returns macros name.
	 *
	 * @return string
	 */
	public function macros_name() {
		return __( 'WC Purchased Products', 'jet-engine' );
	}

	/**
	 * Callback function to return macros value.
	 *
	 * @return string
	 */
	public function macros_callback( $args = [] ) {

		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return '';
		}

		$orders = wc_get_orders( [
			'customer_id' => $user_id,
			'limit'     => -1,
			'status' => array_keys( wc_get_is_paid_statuses() ),
		] );

		if ( ! $orders ) {
			return '';
		}

		$products = [];

		foreach ( $orders as $order ) {
			if ( ! in_array( $order->get_status(), wc_get_is_paid_statuses() ) ) {
				continue;
			}

			$items = $order->get_items();

			foreach ( $items as $item ) {
				$products[] = $item->get_product_id();
			}

		}

		return implode( ',', array_unique( $products ) );

	}

}
