<?php
namespace Jet_Engine\Relations\Forms\Jet_Form_Builder_Forms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	public function __construct() {
		require_once jet_engine()->relations->component_path( 'forms/jet-form-builder/actions-manager.php' );
		require_once jet_engine()->relations->component_path( 'forms/jet-form-builder/preset.php' );

		new Actions_Manager();
		new Preset();
	}


}
