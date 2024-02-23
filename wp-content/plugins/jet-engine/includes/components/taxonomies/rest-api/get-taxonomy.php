<?php
/**
 * Get info about single taxonomy endpoint
 */

class Jet_Engine_CPT_Rest_Get_Taxonomy extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-taxonomy';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();
		$id     = isset( $params['id'] ) ? intval( $params['id'] ) : false;

		if ( ! $id ) {

			jet_engine()->taxonomies->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->taxonomies->get_notices(),
			) );

		}

		$item_data = jet_engine()->taxonomies->data->get_item_for_edit( $id );

		if ( ! $item_data ) {

			jet_engine()->taxonomies->add_notice(
				'error',
				__( 'Taxonomy not found', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->taxonomies->get_notices(),
			) );

		}

		if ( empty( $item_data['labels'] ) ) {
			$item_data['labels']['singular_name'] = '';
		}

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $item_data,
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
		return '(?P<id>[\d]+)';
	}

}