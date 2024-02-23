<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Rest;

use Jet_Engine\Modules\Custom_Content_Types\Module;

class Get_Content_Type_Fields extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-content-type-fields';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params  = $request->get_params();
		$type    = isset( $params['type'] ) ? esc_attr( $params['type'] ) : false;
		$listing = isset( $params['listing'] ) ? absint( $params['listing'] ) : false;

		if ( ! $type && $listing ) {
			$type = Module::instance()->manager->get_content_type_by_listing( $listing );
		}

		if ( ! $type ) {

			Module::instance()->manager->add_notice(
				'error',
				__( 'Content type not found. Looks like you selected Listing Item with wrong source.', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => Module::instance()->manager->get_notices(),
			) );

		}

		$content_type = Module::instance()->manager->get_content_types( $type );

		if ( ! $content_type ) {

			Module::instance()->manager->add_notice(
				'error',
				__( 'Content type not found', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => Module::instance()->manager->get_notices(),
			) );

		}

		$fields = $content_type->get_fields_list( 'all', 'blocks' );

		return rest_ensure_response( array(
			'success' => true,
			'fields'  => $fields,
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

		$params  = $request->get_params();
		$type    = isset( $params['type'] ) ? esc_attr( $params['type'] ) : false;
		$listing = isset( $params['listing'] ) ? absint( $params['listing'] ) : false;

		if ( ! $type && $listing ) {
			$type = Module::instance()->manager->get_content_type_by_listing( $listing );
		}

		if ( ! $type ) {
			return current_user_can( 'manage_options' );
		}

		$content_type = Module::instance()->manager->get_content_types( $type );

		if ( ! $content_type ) {
			return current_user_can( 'manage_options' );
		}

		return $content_type->user_has_access();
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array(
			'type' => array(
				'default'  => '',
				'required' => false,
			),
			'listing' => array(
				'default'  => '',
				'required' => false,
			),
		);
	}

	/**
	 * Get query param. Regex with query parameters
	 *
	 * @return string
	 */
	public function get_query_params() {
		return false;
	}

}