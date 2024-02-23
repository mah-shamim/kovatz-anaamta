<?php
/**
 * Abstract class for WC Vendor API.
 *
 * @package WCVendors/API
 */
abstract class WCV_API {
    /**
     * API namespace.
     *
     * @var string $wcv_api_namespace API namespace.
     */
    protected $wcv_api_namespace = 'wcv-api/v1';

    /**
     * Setup class.
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register the routes for this class.
     */
    abstract public function register_routes();

    /**
     * Register a single route.
     *
     * @param string $route    Route to register.
     * @param string $callback Callback function.
     * @param string $method   Request method.
     * @param array  $args     Additional arguments.
     */
    protected function register_route( $route, $callback, $method, $args = array() ) {
        if ( empty( $callback ) || ! is_callable( array( $this, $callback ) ) || empty( $method ) ) {
            return;
        }

        register_rest_route(
            $this->wcv_api_namespace,
            $route,
            array(
				array(
					'methods'             => $method,
					'callback'            => array( $this, $callback ),
                    'permission_callback' => array( $this, 'get_api_permissions_check' ),
                    'args'                => $args,
				),
            )
        );
    }

    /**
     * Check permissions for the API.
     *
     * @return bool
     */
    public function get_api_permissions_check() {
        return true;
    }
}
