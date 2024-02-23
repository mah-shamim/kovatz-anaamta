<?php
namespace Jet_Engine\Relations\Forms\Jet_Engine_Forms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	public function __construct() {
		$this->init_notification();
		$this->init_preset();
	}

	/**
	 * Initialize JetEngine forms notification
	 *
	 * @return [type] [description]
	 */
	public function init_notification() {
		require_once jet_engine()->relations->component_path( 'forms/jet-engine/notification.php' );
		new Notification();
	}

	/**
	 * Initialize JetEngine forms preset
	 *
	 * @return [type] [description]
	 */
	public function init_preset() {
		require_once jet_engine()->relations->component_path( 'forms/jet-engine/preset.php' );
		new Preset();
	}

}
