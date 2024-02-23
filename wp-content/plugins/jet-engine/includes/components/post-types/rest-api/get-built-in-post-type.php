<?php
/**
 * Add/Update post type endpoint
 */

class Jet_Engine_CPT_Rest_Get_BI_Post_Type extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-built-in-post-type';
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

		$post_type_data = jet_engine()->cpt->data->get_default_built_in_post_type( $post_type );

		if ( ! $post_type_data ) {

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->cpt->get_notices(),
			) );

		}

		$settings = ! empty( $post_type_data['advanced_settings'] ) ? $post_type_data['advanced_settings'] : array();

		if ( isset( $settings['rewrite'] ) && is_array( $settings['rewrite'] ) ) {
			$post_type_data['advanced_settings']['rewrite_slug'] = $settings['rewrite']['slug'];
			$post_type_data['advanced_settings']['rewrite']      = true;
		}

		$stored = jet_engine()->cpt->data->get_built_in_post_type_from_db( $post_type );

		if ( ! empty( $stored ) ) {

			// Ensure post type name
			if ( ! empty( $stored['general_settings'] ) && empty( $stored['general_settings']['name'] ) ) {
				$stored['general_settings']['name'] = $post_type_data['general_settings']['name'];
			}

			foreach ( $post_type_data as $key => $value ) {

				if ( ! empty( $stored[ $key ] ) ) {
					$post_type_data[ $key ] = array_merge( $value, $stored[ $key ] );
				}
			}

			$post_type_data['general_settings']['id'] = $stored['general_settings']['id'];
		}

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $post_type_data,
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
		return '(?P<post_type>[a-z\-\_\d]+)';
	}

}