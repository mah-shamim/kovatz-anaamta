<?php
/**
 * Edit meta box endpoint
 */

class Jet_Engine_Meta_Boxes_Rest_Edit extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'edit-meta-box';
	}

	public function safe_get( $args = array(), $group = '', $key = '', $default = false ) {
		return isset( $args[ $group ][ $key ] ) ? $args[ $group ][ $key ] : $default;
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();

		if ( empty( $params['id'] ) ) {

			jet_engine()->meta_boxes->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->meta_boxes->get_notices(),
			) );

		}

		jet_engine()->meta_boxes->data->set_request( array(
			'id'          => $params['id'],
			'args'        => ! empty( $params['general_settings'] ) ? $params['general_settings'] : array(),
			'meta_fields' => ! empty( $params['meta_fields'] ) ? $params['meta_fields'] : array(),
		) );

		$updated = jet_engine()->meta_boxes->data->edit_item( false );

		return rest_ensure_response( array(
			'success' => $updated,
			'notices' => jet_engine()->cpt->get_notices(),
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

	/**
	 * Get query param. Regex with query parameters
	 *
	 * @return string
	 */
	public function get_query_params() {
		return '(?P<id>[a-z\-\d]+)';
	}

}