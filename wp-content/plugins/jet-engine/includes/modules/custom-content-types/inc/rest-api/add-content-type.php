<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Rest;

use Jet_Engine\Modules\Custom_Content_Types\Module;

class Add_Content_Type extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'add-content-type';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();

		Module::instance()->manager->data->set_request( array(
			'name'        => ! empty( $params['general_settings']['name'] ) ? $params['general_settings']['name'] : '',
			'slug'        => ! empty( $params['general_settings']['slug'] ) ? $params['general_settings']['slug'] : '',
			'args'        => ! empty( $params['general_settings'] ) ? $params['general_settings'] : array(),
			'meta_fields' => ! empty( $params['meta_fields'] ) ? $params['meta_fields'] : array(),
		) );

		$item_id = Module::instance()->manager->data->create_item( false );

		return rest_ensure_response( array(
			'success' => ! empty( $item_id ),
			'item_id' => $item_id,
			'notices' => Module::instance()->manager->get_notices(),
		) );

	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'POST';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

}
