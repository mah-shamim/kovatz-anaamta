<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Conditions;

class Has_Enough_Stock extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	public function get_id() {
		return 'has-enough-stock';
	}

	public function get_name() {
		return __( 'Product has Enough Stock', 'jet-engine' );
	}

	public function get_group() {
		return 'woocommerce';
	}

	public function check( $args = [] ) {

		global $product;

		$product  = wc_get_product();
		$type     = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$quantity = ! empty( $args['condition_settings']['quantity'] ) ? $args['condition_settings']['quantity'] : null;

		if ( empty( $product ) ) {
			if ( 'hide' === $type ) {
				return empty( $product );
			} else {
				return ! empty( $product );
			}
		}

		if ( 'hide' === $type ) {
			return ! $product->has_enough_stock( $quantity );
		} else {
			return $product->has_enough_stock( $quantity );
		}

	}

	public function get_custom_controls() {
		return [
			'quantity' => [
				'label' => __( 'Quantity', 'jet-engine' ),
				'type'  => 'number',
			],
		];
	}

	public function is_for_fields() {
		return false;
	}

	public function need_value_detect() {
		return false;
	}

}