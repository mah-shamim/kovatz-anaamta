<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Macros;

class Products_In_Cart extends \Jet_Engine_Base_Macros {

	/**
	 * Returns macros tag
	 *
	 * @return string
	 */
	public function macros_tag() {
		return 'wc_get_products_in_cart';
	}

	/**
	 * Returns macros name
	 *
	 * @return string
	 */
	public function macros_name() {
		return __( 'WC Products In Cart', 'jet-engine' );
	}

	/**
	 * Callback function to return macros value
	 *
	 * @return string
	 */
	public function macros_callback( $args = array() ) {

		if ( ! function_exists( 'WC' ) ) {
			return false;
		}

		if ( ! WC()->cart ) {
			wc_load_cart();
		}

		$products = WC()->cart->get_cart();
		$result   = [];

		if ( ! empty( $products ) ) {
			foreach ( $products as $product ) {
				$result[] = $product['product_id'];
			}
		}

		return $result;

	}

}
