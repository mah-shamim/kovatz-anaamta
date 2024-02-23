<?php
/**
 * Custom content types module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Module_Custom_Content_Types class
 */
class Jet_Engine_Module_Post_Expiration_Period extends Jet_Engine_External_Module_Base {

	/**
	 * Module ID
	 *
	 * @return string
	 */
	public function module_id() {
		return 'jet-engine-post-expiration-period';
	}

	/**
	 * Check if related plugin for current external module is active
	 *
	 * @return boolean [description]
	 */
	public function is_related_plugin_active() {
		return class_exists( 'Jet_Engine_Post_PE' );
	}

	/**
	 * Module name
	 *
	 * @return string
	 */
	public function module_name() {
		return __( 'Post expiration period', 'jet-engine' );
	}

	/**
	 * Returns detailed information about current module for the dashboard page
	 * @return [type] [description]
	 */
	public function get_module_description() {
		return '<p>Allows to set the post`s expiration date for posts which was added via JetEngine or JetFormBuilder form.</p>
			<p>Settings for the post expiration can be set in the Insert/Update Post notification settings.</p>
			<p>This module makes possible to display the new post for a limited time.</p>';
	}

	/**
	 * Returns information about the related plugin for current module
	 *
	 * @return [type] [description]
	 */
	public function get_related_plugin_data() {
		return array(
			'file' => 'jet-engine-post-expiration-period/jet-engine-post-expiration-period.php',
			'name' => 'JetEngine - post expiration period',
		);
	}


	/**
	 * Returns array links to the module-related resources
	 * @return array
	 */
	public function get_module_links() {
		return array(
			array(
				'label' => __( 'Post Expiration Period Overview', 'jet-engine' ),
				'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-devtools-post-expiration-period-add-on/',
			),
		);
	}

	/**
	 * Module init
	 *
	 * @return void
	 */
	public function module_init() {}

}
