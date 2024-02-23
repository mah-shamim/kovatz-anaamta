<?php
/**
 * Add meta box endpoint
 */

class Jet_Engine_Meta_Boxes_Rest_Add extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'add-meta-box';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();

		jet_engine()->meta_boxes->data->set_request( array(
			'args'        => ! empty( $params['general_settings'] ) ? $params['general_settings'] : array(),
			'meta_fields' => ! empty( $params['meta_fields'] ) ? $params['meta_fields'] : array(),
		) );

		$meta_id = jet_engine()->meta_boxes->data->create_item( false );

		return rest_ensure_response( array(
			'success' => ! empty( $meta_id ),
			'item_id' => $meta_id,
			'notices' => jet_engine()->meta_boxes->get_notices(),
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
