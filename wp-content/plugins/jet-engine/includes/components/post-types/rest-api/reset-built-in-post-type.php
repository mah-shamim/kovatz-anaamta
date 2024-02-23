<?php
/**
 * Reset built-in post type to defaults
 */

class Jet_Engine_CPT_Rest_Reset_BI_Post_Type extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'reset-built-in-post-type';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params    = $request->get_params();
		$post_type = isset( $params['post_type'] ) ? esc_attr( $params['post_type'] ) : false;

		if ( ! $post_type ) {

			jet_engine()->cpt->add_notice(
				'error',
				__( 'Post type name not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->cpt->get_notices(),
			) );

		}

		$result = jet_engine()->cpt->data->reset_built_in_post_type( $post_type );

		if ( ! $result ) {

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->cpt->get_notices(),
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
		return '(?P<post_type>[a-z\-\_\d]+)';
	}

}