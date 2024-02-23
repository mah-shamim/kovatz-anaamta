<?php

namespace Jet_Engine\Modules\Profile_Builder\Bricks_Views;

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
		return jet_engine()->plugin_path( 'includes/modules/profile-builder/inc/bricks-views/elements/' . $relative_path );
	}

	public function register_elements() {

		$element_files = array(
			$this->module_path( 'profile-content.php' ),
			$this->module_path( 'profile-menu.php' ),
		);

		foreach ( $element_files as $file ) {
			\Bricks\Elements::register_element( $file );
		}

	}

	public function has_bricks() {
		return defined( 'BRICKS_VERSION' );
	}
}