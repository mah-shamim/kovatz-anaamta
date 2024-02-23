<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Conditions;

class Is_Downloadable extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	public function get_id() {
		return 'is-downloadable';
	}

	public function get_name() {
		return __( 'Product is Downloadable', 'jet-engine' );
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
			return ! $product->is_downloadable();
		} else {
			return $product->is_downloadable();
		}

	}

	public function is_for_fields() {
		return false;
	}

	public function need_value_detect() {
		return false;
	}

}