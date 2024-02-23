<?php
/**
 * Delete JetEngine component/module item endpoint
 */
class Jet_Engine_Rest_Delete_Item extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'delete-item';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();
		$instance = ! empty( $params['instance'] ) ? $params['instance'] : false;

		if ( ! $instance ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Item instance should be specified in request to correctly attach callbacks', 'jet-engine' ),
			) );
		}

		$res = apply_filters( 'jet-engine/rest-api/delete-item/' . $instance, false, $params, $this );

		if ( ! $res ) {
			return rest_ensure_response( array(
				'success' => false,
				'message' => __( 'Callback not attached properly or success was not thrown during callback', 'jet-engine' ),
			) );
		}

		return $res;

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
