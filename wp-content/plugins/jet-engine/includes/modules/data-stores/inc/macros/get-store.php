<?php
namespace Jet_Engine\Modules\Data_Stores\Macros;

use Jet_Engine\Modules\Data_Stores\Module;

class Get_Store extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'get_store';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Get store', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {
		return array(
			'store' => array(
				'label'   => __( 'Store', 'jet-engine' ),
				'type'    => 'select',
				'options' => Module::instance()->elementor_integration->get_store_options(),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$store = ! empty( $args['store'] ) ? $args['store'] : false;

		if ( ! $store ) {
			return null;
		}

		$store_instance = Module::instance()->stores->get_store( $store );

		if ( ! $store_instance ) {
			return null;
		}

		if ( $store_instance->get_type()->is_front_store() ) {

			$store_items = array(
				'is-front',
				$store_instance->get_type()->type_id(),
				$store_instance->get_slug(),
			);

		} else {
			$store_items = $store_instance->get_store();
		}

		if ( empty( $store_items ) ) {
			$store_items = array( 'not-found' );
		}

		return implode( ',', $store_items );
	}
}