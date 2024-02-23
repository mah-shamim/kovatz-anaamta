<?php
namespace Jet_Engine\Query_Builder\Rest;

use Jet_Engine\Query_Builder\Manager;

class Edit_Query extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'edit-query';
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

			Manager::instance()->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => Module::instance()->manager->get_notices(),
			) );

		}

		Manager::instance()->data->set_request( apply_filters( 'jet-engine/query-builder/edit-query/request', array(
			'id'          => $params['id'],
			'name'        => ! empty( $params['general_settings']['name'] ) ? $params['general_settings']['name'] : '',
			'slug'        => ! empty( $params['general_settings']['slug'] ) ? $params['general_settings']['slug'] : '',
			'args'        => ! empty( $params['general_settings'] ) ? $params['general_settings'] : array(),
			'meta_fields' => ! empty( $params['meta_fields'] ) ? $params['meta_fields'] : array(),
		) ) );

		$updated = Manager::instance()->data->edit_item( false );

		if ( $updated ) {
			do_action( 'jet-engine/query-builder/after-query-update', Manager::instance()->data );
		}

		return rest_ensure_response( array(
			'success' => $updated,
			'notices' => Manager::instance()->get_notices(),
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
