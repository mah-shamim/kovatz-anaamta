<?php
/**
 * Jet_Engine_Module_Trim_Callback module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Module_Trim_Callback class
 */
class Jet_Engine_Module_Trim_Callback extends Jet_Engine_External_Module_Base {

	/**
	 * Module ID
	 *
	 * @return string
	 */
	public function module_id() {
		return 'jet-engine-trim-callback';
	}

	/**
	 * Check if related plugin for current external module is active
	 *
	 * @return boolean [description]
	 */
	public function is_related_plugin_active() {
		return function_exists( 'jet_engine_trim_add_callback' );
	}

	/**
	 * Module name
	 *
	 * @return string
	 */
	public function module_name() {
		return __( 'Trim string callback', 'jet-engine' );
	}

	/**
	 * Returns detailed information about current module for the dashboard page
	 * @return [type] [description]
	 */
	public function get_module_description() {
		return '<p>Output the string trimmed by the desired number of characters.</p>
			<p>This module adds a new callback to the Dynamic Field widget.</p>
			<p>Display the pieces of texts from meta fields, options, etc., trimmed by the chosen string length value (by default, this can be done for post excerpt only, not for meta fields).</p>';
	}

	/**
	 * Returns information about the related plugin for current module
	 *
	 * @return [type] [description]
	 */
	public function get_related_plugin_data() {
		return array(
			'file' => 'jet-engine-trim-callback/jet-engine-trim-callback.php',
			'name' => 'JetEngine - trim string callback',
		);
	}


	/**
	 * Returns array links to the module-related resources
	 * @return array
	 */
	public function get_module_links() {
		return array();
	}

	/**
	 * Module init
	 *
	 * @return void
	 */
	public function module_init() {}

	/**
	 * Is module supports blocks view
	 *
	 * @return [type] [description]
	 */
	public function support_blocks() {
		return true;
	}

}
