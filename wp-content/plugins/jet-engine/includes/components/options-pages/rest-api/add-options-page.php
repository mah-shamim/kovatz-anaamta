<?php
/**
 * Addoptions page endpoint
 */

class Jet_Engine_Rest_Add_Options_Page extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'add-options-page';
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

		jet_engine()->options_pages->data->set_request( array(
			'name'             => $this->safe_get( $params, 'general_settings', 'name' ),
			'slug'             => $this->safe_get( $params, 'general_settings', 'slug' ),
			'menu_name'        => $this->safe_get( $params, 'general_settings', 'menu_name' ),
			'parent'           => $this->safe_get( $params, 'general_settings', 'parent' ),
			'icon'             => $this->safe_get( $params, 'general_settings', 'icon' ),
			'capability'       => $this->safe_get( $params, 'general_settings', 'capability' ),
			'position'         => $this->safe_get( $params, 'general_settings', 'position' ),
			'storage_type'     => $this->safe_get( $params, 'general_settings', 'storage_type' ),
			'option_prefix'    => $this->safe_get( $params, 'general_settings', 'option_prefix' ),
			'hide_field_names' => $this->safe_get( $params, 'general_settings', 'hide_field_names' ),
			'fields'           => ! empty( $params['fields'] ) ? $params['fields'] : array(),
		) );

		$item_id = jet_engine()->options_pages->data->create_item( false );

		return rest_ensure_response( array(
			'success' => ! empty( $item_id ),
			'item_id' => $item_id,
			'notices' => jet_engine()->options_pages->get_notices(),
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
