<?php
namespace Jet_Engine\Modules\Data_Stores\Macros;

use Jet_Engine\Modules\Data_Stores\Module;

class Get_Store_Count extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'store_count';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Store count', 'jet-engine' );
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
			return 'not found';
		}

		return Module::instance()->render->store_count( $store );
	}
}