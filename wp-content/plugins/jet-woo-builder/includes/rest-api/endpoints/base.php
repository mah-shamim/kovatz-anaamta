<?php

namespace Jet_Woo_Builder\Endpoints;

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
	 *
	 * @return string
	 */
	abstract function get_name();

	/**
	 * API callback
	 *
	 * @param $request
	 *
	 * @return void
	 */
	abstract function callback( $request );

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELETE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'GET';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return string|bool
	 */
	public function permission_callback() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get query param. Regex with query parameters
	 *
	 * Example:
	 *
	 * (?P<id>[\d]+)/(?P<meta_key>[\w-]+)
	 *
	 * @return string
	 */
	public function get_query_params() {
		return '';
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array();
	}

}
