<?php
/**
 * Controller class for all JetEngine related API endpoints
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_REST_API' ) ) {

	class Jet_Engine_REST_API {

		private $api_namespace = 'jet-engine/v2';
		private $_endpoints    = false;

		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		}

		/**
		 * Initialize all JetEngine related Rest API endpoints
		 *
		 * @return [type] [description]
		 */
		public function init_endpoints() {

			$this->_endpoints = array();

			require_once jet_engine()->plugin_path( 'includes/base/base-api-endpoint.php' );

			require jet_engine()->plugin_path( 'includes/rest-api/endpoints/search-posts.php' );
			require jet_engine()->plugin_path( 'includes/rest-api/endpoints/add-item.php' );
			require jet_engine()->plugin_path( 'includes/rest-api/endpoints/edit-item.php' );
			require jet_engine()->plugin_path( 'includes/rest-api/endpoints/delete-item.php' );
			require jet_engine()->plugin_path( 'includes/rest-api/endpoints/get-item.php' );
			require jet_engine()->plugin_path( 'includes/rest-api/endpoints/get-items.php' );

			$this->register_endpoint( new Jet_Engine_Rest_Search_Posts() );
			$this->register_endpoint( new Jet_Engine_Rest_Add_Item() );
			$this->register_endpoint( new Jet_Engine_Rest_Edit_Item() );
			$this->register_endpoint( new Jet_Engine_Rest_Delete_Item() );
			$this->register_endpoint( new Jet_Engine_Rest_Get_Item() );
			$this->register_endpoint( new Jet_Engine_Rest_Get_Items() );

			do_action( 'jet-engine/rest-api/init-endpoints', $this );

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
		 * Register JetEngine rest API routes
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

				$route = '/' . $endpoint->get_name() . '/' . $endpoint->get_query_params();

				register_rest_route( $this->api_namespace, $route, $args );

			}

		}

	}

}
