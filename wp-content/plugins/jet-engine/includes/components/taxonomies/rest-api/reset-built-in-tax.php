<?php
/**
 * Reset built-in post type to defaults
 */

class Jet_Engine_CPT_Rest_Reset_BI_Tax extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'reset-built-in-tax';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();
		$tax    = isset( $params['tax'] ) ? esc_attr( $params['tax'] ) : false;

		if ( ! $tax ) {

			jet_engine()->taxonomies->add_notice(
				'error',
				__( 'Tax name not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->taxonomies->get_notices(),
			) );

		}
		
		$result = jet_engine()->taxonomies->data->reset_built_in_item( $tax );

		if ( ! $result ) {

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->taxonomies->get_notices(),
			) );

		} else {
			return rest_ensure_response( array(
				'success' => true,
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
		return '(?P<tax>[a-z\-\_\d]+)';
	}

}