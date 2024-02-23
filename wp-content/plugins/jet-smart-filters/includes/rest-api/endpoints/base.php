<?php
namespace Jet_Smart_Filters\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Endpoint_Base class
 */
abstract class Base {
	/**
	 * Returns route name
	 */
	abstract function get_name();

	/**
	 * API callback
	 */
	abstract function callback( $request );

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 */
	public function get_method() {

		return 'POST';
	}

	/**
	 * Check user access to current end-popint
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get query param. Regex with query parameters
	 *
	 * Example:
	 * (?P<id>[\d]+)/(?P<meta_key>[\w-]+)
	 */
	public function get_query_params() {

		return '';
	}

	/**
	 * Returns arguments config
	 *
	 * Example:
	 * 	array(
	 * 		array(
	 * 			'type' => array(
	 * 			'default'  => '',
	 * 			'required' => false,
	 * 		),
	 * 	)
	 */
	public function get_args() {

		return array();
	}
}
