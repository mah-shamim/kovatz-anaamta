<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Rest;

use Jet_Engine\Modules\Custom_Content_Types\Module;

class Delete_Content_Type extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'delete-content-type';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();
		$id     = $params['id'];

		if ( ! $id ) {

			Module::instance()->manager->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => Module::instance()->manager->get_notices(),
			) );

		}

		Module::instance()->manager->data->set_request( array( 'id' => $id ) );

		if ( Module::instance()->manager->data->delete_item( false ) ) {
			return rest_ensure_response( array(
				'success' => true,
			) );
		} else {
			return rest_ensure_response( array(
				'success' => false,
				'notices' => Module::instance()->manager->get_notices(),
			) );
		}

	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELETE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'DELETE';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get query param. Regex with query parameters
	 *
	 * @return string
	 */
	public function get_query_params() {
		return '(?P<id>[a-z\-\_\d]+)';
	}

}