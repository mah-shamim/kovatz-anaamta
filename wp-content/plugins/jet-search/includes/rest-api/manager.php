<?php
/**
 * Controller class for all JetSearch related API endpoints
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Search_REST_API' ) ) {

	class Jet_Search_REST_API {

		private $api_namespace = 'jet-search/v1';
		private $_endpoints    = false;

		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		}

		/**
		 * Initialize all JetSearch related Rest API endpoints
		 *
		 * @return [type] [description]
		 */
		public function init_endpoints() {

			$this->_endpoints = array();

			require jet_search()->plugin_path( 'includes/rest-api/endpoints/base.php' );
			require jet_search()->plugin_path( 'includes/rest-api/endpoints/search-route.php' );
			require jet_search()->plugin_path( 'includes/rest-api/endpoints/add-suggestion.php' );
			require jet_search()->plugin_path( 'includes/rest-api/endpoints/update-suggestion.php' );
			require jet_search()->plugin_path( 'includes/rest-api/endpoints/delete-suggestion.php' );
			require jet_search()->plugin_path( 'includes/rest-api/endpoints/get-suggestions.php' );
			require jet_search()->plugin_path( 'includes/rest-api/endpoints/form-add-suggestion.php' );

			$this->register_endpoint( new Jet_Search_Rest_Search_Route() );
			$this->register_endpoint( new Jet_Search_Rest_Add_Suggestion() );
			$this->register_endpoint( new Jet_Search_Rest_Update_Suggestion() );
			$this->register_endpoint( new Jet_Search_Rest_Delete_Suggestion() );
			$this->register_endpoint( new Jet_Search_Rest_Get_Suggestions() );
			$this->register_endpoint( new Jet_Search_Rest_Form_Add_Suggestion() );

			do_action( 'jet-search/rest-api/init-endpoints', $this );

		}

		/**
		 * Register new endpoint
		 *
		 * @param  object $endpoint_instance Endpoint instance
		 * @return void
		 */
		public function register_endpoint( $endpoint_instance = null ) {

			if ( $endpoint_instance ) {
				$this->_endpoints[ $endpoint_instance->get_name() ] = $endpoint_instance;
			}

		}

		/**
		 * Returns all registererd API endpoints
		 *
		 * @return [type] [description]
		 */
		public function get_endpoints() {

			if ( false === $this->_endpoints ) {
				$this->init_endpoints();
			}

			return $this->_endpoints;

		}

		/**
		 * Returns route to passed endpoint
		 *
		 * @return [type] [description]
		 */
		public function get_route( $endpoint = '', $full = false ) {

			$path = '/' . $this->api_namespace . '/' . $endpoint . '/';

			if ( ! $full ) {
				return $path;
			} else {
				return get_rest_url( null, $path );
			}

		}

		/**
		 * Register JetSearch rest API routes
		 *
		 * @return [type] [description]
		 */
		public function register_routes() {

			$endpoints = $this->get_endpoints();

			foreach ( $endpoints as $endpoint ) {

				$args = array(
					'methods'             => $endpoint->get_method(),
					'callback'            => array( $endpoint, 'callback' ),
					'permission_callback' => array( $endpoint, 'permission_callback' ),
				);

				if ( ! empty( $endpoint->get_args() ) ) {
					$args['args'] = $endpoint->get_args();
				}

				$route = '/' . $endpoint->get_name() . '/';

				register_rest_route( $this->api_namespace, $route, $args );

			}

		}

	}

}
