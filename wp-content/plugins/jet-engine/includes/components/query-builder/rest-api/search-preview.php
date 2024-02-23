<?php
namespace Jet_Engine\Query_Builder\Rest;

use Jet_Engine\Query_Builder\Manager;

class Search_Preview extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'search-query-preview';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		if ( ! class_exists( '\Jet_Engine_Posts_Search_Handler' ) ) {
			require jet_engine()->plugin_path( 'includes/classes/posts-search.php' );
		}

		$search_handler = new \Jet_Engine_Posts_Search_Handler();
		$params         = $request->get_params();
		$query          = isset( $params['_s'] ) ? esc_attr( $params['_s'] ) : false;

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $search_handler->search_posts( array(
				'q' => $query,
			) ),
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
		return null;
	}

	/**
	 * Get query param. Regex with query parameters
	 *
	 * @return string
	 */
	public function get_args() {
		return array(
			'_s' => array(
				'default'  => '',
				'required' => false,
			),
		);
	}

}
