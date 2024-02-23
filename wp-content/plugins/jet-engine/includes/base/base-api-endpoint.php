<?php
/**
 * Base class for REST API endpoint
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Base_API_Endpoint' ) ) {

	/**
	 * Define Jet_Engine_Base_API_Endpoint class
	 */
	abstract class Jet_Engine_Base_API_Endpoint {

		/**
		 * Returns route name
		 *
		 * @return string
		 */
		abstract public function get_name();

		/**
		 * API callback
		 *
		 * @return void
		 */
		abstract public function callback( $request );

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
			return true;
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
		 * Example:
		 *
		 * 	array(
		 * 		array(
		 * 			'type' => array(
		 * 			'default'  => '',
		 * 			'required' => false,
		 * 		),
		 * 	)
		 *
		 * @return array
		 */
		public function get_args() {
			return array();
		}

	}

}