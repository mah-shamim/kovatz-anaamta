<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Auth_Types;

use Jet_Engine\Modules\Rest_API_Listings\Module;

class Manager {

	private $_types = array();

	public function __construct() {
		$this->register_types();
	}

	/**
	 * Register auth types
	 *
	 * @return [type] [description]
	 */
	public function register_types() {

		require_once Module::instance()->module_path( 'auth-types/base.php' );
		require_once Module::instance()->module_path( 'auth-types/application-password.php' );
		require_once Module::instance()->module_path( 'auth-types/bearer-token.php' );
		require_once Module::instance()->module_path( 'auth-types/custom-header.php' );
		require_once Module::instance()->module_path( 'auth-types/rapidapi.php' );

		$this->register_type( new Application_Password() );
		$this->register_type( new Bearer_Token() );
		$this->register_type( new Custom_Header() );
		$this->register_type( new RapidAPI() );

		do_action( 'jet-engine/rest-api-listings/register-auth-types', $this );

	}

	/**
	 * Register single type
	 * @return [type] [description]
	 */
	public function register_type( $instance ) {
		$this->_types[ $instance->get_id() ] = $instance;
	}

	/**
	 * Returns types list as array of arrays with 'value' and 'label' keys
	 * @return [type] [description]
	 */
	public function get_types_for_js() {

		$result = array();

		foreach ( $this->_types as $type ) {
			$result[] = array(
				'value' => $type->get_id(),
				'label' => $type->get_name(),
			);
		}

		return $result;

	}

}
