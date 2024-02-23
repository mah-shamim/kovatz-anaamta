<?php
namespace Jet_Engine\Query_Builder\Rest;

use Jet_Engine\Query_Builder\Manager;

class Get_Query extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-query';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();
		$id     = isset( $params['id'] ) ? esc_attr( $params['id'] ) : false;

		if ( ! $id ) {

			Manager::instance()->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => Manager::instance()->get_notices(),
			) );

		}

		$item = Manager::instance()->data->get_item_for_edit( $id );

		if ( ! $item ) {

			Manager::instance()->add_notice(
				'error',
				__( 'Post type not found', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => Manager::instance()->get_notices(),
			) );

		}

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $item,
		) );

	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'GET';
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
		return '(?P<id>[a-z\-\d]+)';
	}

}
