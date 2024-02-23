<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Conditions;

class Is_Purchased extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	public function get_id() {
		return 'is-purchased';
	}

	public function get_name() {
		return __( 'Product is Purchased', 'jet-engine' );
	}

	public function get_group() {
		return 'woocommerce';
	}

	public function check( $args = [] ) {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		global $product;

		$product_id = ! empty( $args['condition_settings']['product_id'] ) ? jet_engine()->listings->macros->do_macros( $args['condition_settings']['product_id'] ) : false;
		$product    = wc_get_product( $product_id );
		$type       = ! empty( $args['type'] ) ? $args['type'] : 'show';

		if ( empty( $product ) ) {
			if ( 'hide' === $type ) {
				return empty( $product );
			} else {
				return ! empty( $product );
			}
		}

		$is_purchased = wc_customer_bought_product( '', get_current_user_id(), $product->get_id() );

		if ( 'hide' === $type ) {
			return ! $is_purchased;
		} else {
			return $is_purchased;
		}

	}

	public function get_custom_controls() {
		return [
			'product_id' => [
				'type'        => 'text',
				'label'       => __( 'Product ID', 'jet-engine' ),
				'description' => __( 'Leave this field blank if you want to check the current product.', 'jet-engine' ),
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						'jet_engine_macros',
					],
				],
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