<?php
/**
 * Performance manager module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Module_Performance class
 */
class Jet_Engine_Module_Performance extends Jet_Engine_Module_Base {

	public $instance = null;

	/**
	 * Module ID
	 *
	 * @return string
	 */
	public function module_id() {
		return 'performance';
	}

	/**
	 * Module name
	 *
	 * @return string
	 */
	public function module_name() {
		return __( 'Performance', 'jet-engine' );
	}

	/**
	 * Returns detailed information about current module for the dashboard page
	 * @return [type] [description]
	 */
	public function get_module_details() {
		return 'Built-in performance manager module';
	}

	/**
	 * Module init
	 *
	 * @return void
	 */
	public function module_init() {
		$this->create_instance();
	}

	/**
	 * Create module instance
	 *
	 * @return [type] [description]
	 */
	public function create_instance() {
		require_once jet_engine()->plugin_path( 'includes/modules/performance/inc/module.php' );
		$this->instance = \Jet_Engine\Modules\Performance\Module::instance();
	}

}
