<?php

namespace Jet_Engine\Modules\Data_Stores\Bricks_Views;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {
	/**
	 * Elementor Frontend instance
	 *
	 * @var null
	 */
	public $frontend = null;

	/**
	 * Constructor for the class
	 */
	function __construct() {

		if ( ! $this->has_bricks() ) {
			return;
		}

		add_action( 'jet-engine/bricks-views/register-elements', array( $this, 'register_elements' ), 11 );
	}

	public function module_path( $relative_path = '' ) {
		return jet_engine()->plugin_path( 'includes/modules/data-stores/inc/bricks-views/' . $relative_path );
	}

	public function register_elements() {

		\Bricks\Elements::register_element( $this->module_path( 'button.php' ) );

	}

	public function has_bricks() {
		return defined( 'BRICKS_VERSION' );
	}
}