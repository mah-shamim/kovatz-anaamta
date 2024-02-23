<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Conditions;

class Is_Sold_Individually extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	public function get_id() {
		return 'is-sold-individually';
	}

	public function get_name() {
		return __( 'Product is Sold Individually', 'jet-engine' );
	}

	public function get_group() {
		return 'woocommerce';
	}

	public function check( $args = [] ) {

		global $product;

		$product = wc_get_product();
		$type    = ! empty( $args['type'] ) ? $args['type'] : 'show';

		if ( empty( $product ) ) {
			if ( 'hide' === $type ) {
				return empty( $product );
			} else {
				return ! empty( $product );
			}
		}

		if ( 'hide' === $type ) {
			return ! $product->is_sold_individually();
		} else {
			return $product->is_sold_individually();
		}

	}

	public function is_for_fields() {
		return false;
	}

	public function need_value_detect() {
		return false;
	}

}