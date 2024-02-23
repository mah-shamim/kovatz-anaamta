<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Conditions;

class Is_Type extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	public function get_id() {
		return 'is-type';
	}

	public function get_name() {
		return __( 'Product is Type', 'jet-engine' );
	}

	public function get_group() {
		return 'woocommerce';
	}

	public function check( $args = [] ) {

		global $product;

		$product      = wc_get_product();
		$type         = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$product_type = ! empty( $args['condition_settings']['product_type'] ) ? $args['condition_settings']['product_type'] : '';

		if ( empty( $product ) ) {
			if ( 'hide' === $type ) {
				return empty( $product );
			} else {
				return ! empty( $product );
			}
		}

		if ( 'hide' === $type ) {
			return ! $product->is_type( $product_type );
		} else {
			return $product->is_type( $product_type );
		}

	}

	public function get_custom_controls() {
		return [
			'product_type' => [
				'label'       => __( 'Product Type', 'jet-engine' ),
				'label_block' => true,
				'type'        => 'select2',
				'multiple'    => true,
				'options'     => wc_get_product_types(),
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