<?php
/**
 * Custom content types module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Module_Custom_Visibility_Conditions class
 */
class Jet_Engine_Module_Custom_Visibility_Conditions extends Jet_Engine_External_Module_Base {

	/**
	 * Module ID
	 *
	 * @return string
	 */
	public function module_id() {
		return 'jet-engine-custom-visibility-conditions';
	}

	/**
	 * Check if related plugin for current external module is active
	 *
	 * @return boolean [description]
	 */
	public function is_related_plugin_active() {
		return function_exists( 'jet_engine_cvc' );
	}

	/**
	 * Module name
	 *
	 * @return string
	 */
	public function module_name() {
		return __( 'Custom visibility conditions', 'jet-engine' );
	}

	/**
	 * Returns detailed information about current module for the dashboard page
	 * @return [type] [description]
	 */
	public function get_module_description() {
		return '<p>Requires <b><i>Dynamic Visibility for Widgets and Sections</i></b> module.</p>
			<p>Adds custom conditions for the Dynamic Visibility module.</p>
			<p>Make sections/columns/widgets visible/invisible to the author of the post only using the "Is post by current user" condition.</p>
			<p>Show/hide the sections/columns/widgets depending on the status of the post using the "Post Status is" condition.</p>';
	}

	/**
	 * Returns information about the related plugin for current module
	 *
	 * @return [type] [description]
	 */
	public function get_related_plugin_data() {
		return array(
			'file' => 'jet-engine-custom-visibility-conditions/jet-engine-custom-visibility-conditions.php',
			'name' => 'JetEngine - custom visibility conditions',
		);
	}


	/**
	 * Returns array links to the module-related resources
	 * @return array
	 */
	public function get_module_links() {
		return array(
			array(
				'label' => __( 'Custom Visibility Conditions Overview', 'jet-engine' ),
				'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-devtools-custom-visibility-conditions-add-on/',
			)
		);
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
