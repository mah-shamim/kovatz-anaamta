<?php
namespace Jet_Engine\Relations\Rest;

/**
 * Get info about single Relation endpoint
 */

class Get_Relation extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-relation';
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

			jet_engine()->relations->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->relations->get_notices(),
			) );

		}

		$data = jet_engine()->relations->data->get_item_for_edit( $id );

		if ( ! $data ) {

			jet_engine()->relations->add_notice(
				'error',
				__( 'Relation not found', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->relations->get_notices(),
			) );

		}

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $data,
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
