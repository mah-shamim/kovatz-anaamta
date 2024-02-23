<?php

namespace Jet_Engine\Modules\Data_Stores\Twig_Views;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * Constructor for the class
	 */
	function __construct() {
		add_action( 'jet-engine/timber-views/register-functions', [ $this, 'register_button_funciton' ] );
	}

	public function register_button_funciton( $registry ) {
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/twig-views/button.php' );
		$registry->register_function( new Button_Function() );
	}

}
