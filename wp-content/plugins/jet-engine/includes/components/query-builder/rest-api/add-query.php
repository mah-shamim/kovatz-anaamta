<?php
namespace Jet_Engine\Query_Builder\Rest;

use Jet_Engine\Query_Builder\Manager;

class Add_Query extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'add-query';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();

		Manager::instance()->data->set_request( apply_filters( 'jet-engine/query-builder/edit-query/request', array(
			'name'        => ! empty( $params['general_settings']['name'] ) ? $params['general_settings']['name'] : '',
			'slug'        => ! empty( $params['general_settings']['slug'] ) ? $params['general_settings']['slug'] : '',
			'args'        => ! empty( $params['general_settings'] ) ? $params['general_settings'] : array(),
			'meta_fields' => array(),
		) ) );

		$item_id = Manager::instance()->data->create_item( false );

		if ( $item_id ) {
			do_action( 'jet-engine/query-builder/after-query-update', Manager::instance()->data );
		}

		return rest_ensure_response( array(
			'success' => ! empty( $item_id ),
			'item_id' => $item_id,
			'notices' => Manager::instance()->get_notices(),
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
