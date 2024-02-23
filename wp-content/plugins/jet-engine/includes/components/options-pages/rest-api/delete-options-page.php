<?php
/**
 * Delete options page endpoint
 */

class Jet_Engine_Rest_Delete_Options_Page extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'delete-options-page';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();

		$action = $params['action'];
		$id     = $params['id'];

		if ( ! $id ) {

			jet_engine()->options_pages->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->options_pages->get_notices(),
			) );

		}

		$item_data = jet_engine()->options_pages->data->get_item_for_edit( $id );

		if ( ! $item_data || ! isset( $item_data['general_settings']['slug'] ) ) {

			jet_engine()->options_pages->add_notice(
				'error',
				__( 'Item data not found', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->options_pages->get_notices(),
			) );

		}

		$slug = $item_data['general_settings']['slug'];

		$storage_type  = $item_data['general_settings']['storage_type'];
		$option_prefix = $item_data['general_settings']['option_prefix'];

		if ( 'delete' === $action ) {

			if ( 'separate' === $storage_type ) {

				$fields = $item_data['fields'];
				$fields = array_filter( $fields, function ( $field ) {

					if ( empty( $field['object_type'] ) ) {
						return false;
					}

					if ( 'field' !== $field['object_type'] ) {
						return false;
					}

					if ( empty( $field['type'] ) ) {
						return false;
					}

					if ( 'html' === $field['type'] ) {
						return false;
					}

					return true;
				} );

				$option_keys = wp_list_pluck( $fields, 'name' );

				foreach ( $option_keys as $key ) {

					if ( ! empty( $option_prefix ) ) {
						$key = $slug . '_' . $key;
					}

					delete_option( $key );
				}

			} else {
				delete_option( $slug );
			}
		}

		jet_engine()->options_pages->data->set_request( array( 'id' => $id ) );

		if ( jet_engine()->options_pages->data->delete_item( false ) ) {
			return rest_ensure_response( array(
				'success' => true,
			) );
		} else {
			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->options_pages->get_notices(),
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
		return '(?P<id>[\d]+)';
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array(
			'action' => array(
				'default'  => 'none',
				'required' => true,
			),
		);
	}

}