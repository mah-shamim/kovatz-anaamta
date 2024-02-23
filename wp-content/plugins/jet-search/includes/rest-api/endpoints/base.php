<?php
/**
 * Get searching posts endpoint
 */

abstract class Jet_Search_Rest_Base_Route {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELETE
	 *
	 * @return string
	 */
	abstract public function get_method();

	/**
	 * API callback
	 *
	 * @return void
	 */
	abstract public function callback( $request );

	/**
	 * Check access permissions
	 *
	 * @return [type] [description]
	 */
	public function permission_callback( $request ) {
		return false;
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
