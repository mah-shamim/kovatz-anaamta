<?php
/**
 * Filters manager class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Smart_Filters_Referrer_Manager class
 */
class Jet_Smart_Filters_Referrer_Manager {

	/**
	 * Available types:
	 * front - send request to the initial page with ?jsf_ajax=1
	 * ajax - send request to admin-ajax.php with referrer data in the request
	 */
	public $referrer_type = 'default';

	/**
	 * Query varaibale key to define front referrer request
	 */
	public $front_query_key = 'jsf_ajax';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$this->referrer_type = jet_smart_filters()->settings->get( 'ajax_request_types' );

		if ( 'default' === $this->referrer_type || ! $this->referrer_type ) {

			if ( jet_smart_filters()->query->is_ajax_filter() ) {
				$this->define_filters_request_constant();
			}

			return;
		}

		add_filter( 'jet-smart-filters/filters/localized-data', array( $this, 'set_referrer_settings' ) );

		if ( 'referrer' === $this->referrer_type ) {
			add_action( 'jet-smart-filters/render/ajax/before', array( $this, 'setup_ajax_referrer' ) );
		}

		if ( 'self' === $this->referrer_type && ! empty( $_GET[ $this->front_query_key ] ) ) {
			add_action( 'parse_request', array( $this, 'setup_front_referrer' ) );
		}

	}

	public function define_filters_request_constant() {
		define( 'JET_SMART_FILTERS_DOING_REQUEST', true );
	}

	public function set_referrer_settings( $data ) {

		if ( 'referrer' === $this->referrer_type ) {
			$data['referrer_data'] = $this->get_referrer_data();
		}

		if ( 'self' === $this->referrer_type ) {
			$data['referrer_url'] = $this->get_referrer_url();
		}

		return $data;
	}

	/**
	 * Setup front referrer
	 */
	public function setup_front_referrer( $wp ) {

		$this->define_filters_request_constant();

		$wp->query_posts();
		$wp->register_globals();

		if ( apply_filters( 'jet-smart-filters/referrer/front/define-ajax', false ) ) {
			define( 'DOING_AJAX', true );
		}
		
		jet_smart_filters()->query->set_is_ajax_filter();

		do_action( 'jet-smart-filters/referrer/request' );

		do_action( 'wp_ajax_jet_smart_filters' );
		do_action( 'wp_ajax_nopriv_jet_smart_filters' );

		die();
	}

	/**
	 * Setup data by referrer URL string
	 */
	public function setup_ajax_referrer() {

		if ( empty( $_REQUEST['referrer'] ) ) {
			return;
		}

		$referrer = $_REQUEST['referrer'];

		if ( ! is_array( $referrer ) ) {
			return;
		}

		$this->define_filters_request_constant();

		global $wp;

		$map = array(
			'uri'  => 'REQUEST_URI',
			'info' => 'PATH_INFO',
			'self' => 'PHP_SELF',
		);

		$temp = array();

		$uri      = ! empty( $referrer['uri'] ) ? $referrer['uri'] : false;
		$uri_data = explode( '?', $uri );

		if ( ! empty( $uri_data[1] ) ) {
			parse_str( $uri_data[1], $request );

			foreach ( $request as $key => $value ) {
				$_GET[ $key ]     = $value;
				$_REQUEST[ $key ] = $value;
			}
		}

		foreach ( $map as $request_key => $server_key ) {
			if ( isset( $referrer[ $request_key ] ) ) {
				$temp[ $server_key ] = isset( $_SERVER[ $server_key ] ) ? $_SERVER[ $server_key ] : '';
				$_SERVER[ $server_key ] = $referrer[ $request_key ];
			}
		}

		global $current_screen;

		$current_screen = WP_Screen::get( 'front' );

		$wp->parse_request();
		$wp->query_posts();
		$wp->register_globals();

		foreach ( $temp as $key => $value) {
			$_SERVER[ $key ] = $value;
		}

		do_action( 'jet-smart-filters/referrer/request' );
	}

	/**
	 * Returns referrer URL
	 */
	public function get_referrer_data() {

		return array(
			'uri'  => ! empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : null,
			'info' => ! empty( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : null,
			'self' => ! empty( $_SERVER['PHP_SELF'] ) ? $_SERVER['PHP_SELF'] : null,
		);
	}

	/**
	 * Returns referrer URL
	 */
	public function get_referrer_url() {

		return add_query_arg( array( $this->front_query_key => 1 ) );
	}
}
