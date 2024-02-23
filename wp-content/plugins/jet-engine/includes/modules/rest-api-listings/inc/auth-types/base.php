<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Auth_Types;

abstract class Base {

	public function __construct() {

		add_action( 'jet-engine/rest-api-listings/settings/auth-controls', array( $this, 'register_controls' ) );
		add_action( 'jet-engine/rest-api-listings/form/auth-controls', array( $this, 'register_form_controls' ) );
		add_filter( 'jet-engine/rest-api-listings/data/args', array( $this, 'register_args' ) );

		$this->init();

	}

	/**
	 * Return auth type ID
	 *
	 * @return [type] [description]
	 */
	abstract public function get_id();

	/**
	 * Return auth type name
	 *
	 * @return [type] [description]
	 */
	abstract public function get_name();

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function init() {
	}

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function register_controls() {
	}

	/**
	 * Register form-related controls
	 *
	 * @return [type] [description]
	 */
	public function register_form_controls() {
	}

	/**
	 * Initialize authorization
	 *
	 * @return [type] [description]
	 */
	public function register_args( $args = array() ) {
	}

	public function is_current_type_endpoint( $endpoint ) {

		$auth = isset( $endpoint['authorization'] ) ? $endpoint['authorization'] : false;
		$auth = filter_var( $auth, FILTER_VALIDATE_BOOLEAN );

		if ( ! $auth || empty( $endpoint['auth_type'] ) ) {
			return false;
		} else {
			return $endpoint['auth_type'] === $this->get_id();
		}

	}

}
