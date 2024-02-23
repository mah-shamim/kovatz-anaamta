<?php
namespace Jet_Smart_Filters;

/**
 * API controller class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Controller class
 */
class Rest_Api {
	/**
	 * A reference to an instance of this class.
	 */
	private static $instance = null;

	/**
	 * api namespace
	 */
	public $api_namespace = 'jet-smart-filters-api/v1';

	/**
	 * endpoints
	 */
	private $_endpoints = null;

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	// Here initialize our namespace and resource name.
	public function __construct() {

		$this->load_files();

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function load_files() {

		require jet_smart_filters()->plugin_path( 'includes/rest-api/endpoints/base.php' );
		require jet_smart_filters()->plugin_path( 'includes/rest-api/endpoints/plugin-settings.php' );
		require jet_smart_filters()->plugin_path( 'includes/rest-api/endpoints/filters.php' );
		require jet_smart_filters()->plugin_path( 'includes/rest-api/endpoints/filter.php' );
		require jet_smart_filters()->plugin_path( 'includes/rest-api/endpoints/taxonomy-terms.php' );
		require jet_smart_filters()->plugin_path( 'includes/rest-api/endpoints/posts-list.php' );
		require jet_smart_filters()->plugin_path( 'includes/rest-api/endpoints/admin-mode-switch.php' );
	}

	/**
	 * Initialize all JetEngine related Rest API endpoints
	 */
	public function init_endpoints() {

		$this->_endpoints = array();

		$this->register_endpoint( new Endpoints\Plugin_Settings() );
		$this->register_endpoint( new Endpoints\Filters() );
		$this->register_endpoint( new Endpoints\Filter() );
		$this->register_endpoint( new Endpoints\TaxonomyTerms() );
		$this->register_endpoint( new Endpoints\PostsList() );
		$this->register_endpoint( new Endpoints\AdminModeSwitch() );

		do_action( 'jet-smart-filters/rest/init-endpoints', $this );
	}

	/**
	 * Register new endpoint
	 */
	public function register_endpoint( $endpoint_instance = null ) {

		if ( $endpoint_instance ) {
			$this->_endpoints[ $endpoint_instance->get_name() ] = $endpoint_instance;
		}
	}

	/**
	 * Returns all registererd API endpoints
	 */
	public function get_endpoints() {

		if ( null === $this->_endpoints ) {
			$this->init_endpoints();
		}

		return $this->_endpoints;
	}

	/**
	 * Returns endpoints URLs
	 */
	public function get_endpoints_urls() {

		$result    = array();
		$endpoints = $this->get_endpoints();

		foreach ( $endpoints as $endpoint ) {
			$key = str_replace( '-', '', ucwords( $endpoint->get_name(), '-' ) );
			$result[ $key ] = get_rest_url( null, $this->api_namespace . '/' . $endpoint->get_name() . '/' . $endpoint->get_query_params() , 'rest' );
		}

		return $result;
	}

	/**
	 * Returns route to passed endpoint
	 */
	public function get_route( $endpoint = '', $full = false ) {

		$path = $this->api_namespace . '/' . $endpoint . '/';

		if ( ! $full ) {
			return $path;
		} else {
			return get_rest_url( null, $path );
		}
	}

	/**
	 * Register our routes.
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

