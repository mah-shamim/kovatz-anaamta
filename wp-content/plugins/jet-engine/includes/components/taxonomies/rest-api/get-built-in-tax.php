<?php
/**
 * Add/Update post type endpoint
 */

class Jet_Engine_CPT_Rest_Get_BI_Tax extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-built-in-tax';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params    = $request->get_params();
		$tax = isset( $params['tax'] ) ? esc_attr( $params['tax'] ) : false;

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

		$tax_data = jet_engine()->taxonomies->data->get_default_built_in_item( $tax );

		if ( ! $tax_data ) {

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->taxonomies->get_notices(),
			) );

		}

		$settings = ! empty( $tax_data['advanced_settings'] ) ? $tax_data['advanced_settings'] : array();

		if ( isset( $settings['rewrite'] ) && is_array( $settings['rewrite'] ) ) {
			$tax_data['advanced_settings']['rewrite']              = true;
			$tax_data['advanced_settings']['rewrite_slug']         = $settings['rewrite']['slug'];
			$tax_data['advanced_settings']['rewrite_hierarchical'] = isset( $settings['rewrite']['hierarchical'] ) ? $settings['rewrite']['hierarchical'] : false;
			$tax_data['advanced_settings']['with_front']           = isset( $settings['rewrite']['with_front'] ) ? $settings['rewrite']['with_front'] : true;
		}

		$stored = jet_engine()->taxonomies->data->get_built_in_item_from_db( $tax );

		if ( ! empty( $stored ) ) {

			// Ensure post type name
			if ( ! empty( $stored['general_settings'] ) && empty( $stored['general_settings']['name'] ) ) {
				$stored['general_settings']['name'] = $tax_data['general_settings']['name'];
			}

			foreach ( $tax_data as $key => $value ) {

				if ( ! empty( $stored[ $key ] ) ) {
					$tax_data[ $key ] = array_merge( $value, array_filter( $stored[ $key ] ) );
				}
			}

			$tax_data['general_settings']['id'] = $stored['general_settings']['id'];
		}

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $tax_data,
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
		return '(?P<tax>[a-z\-\_\d]+)';
	}

}