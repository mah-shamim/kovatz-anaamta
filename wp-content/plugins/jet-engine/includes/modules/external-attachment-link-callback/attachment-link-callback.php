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
class Jet_Engine_Module_Attachment_Link_Callback extends Jet_Engine_External_Module_Base {

	/**
	 * Module ID
	 *
	 * @return string
	 */
	public function module_id() {
		return 'jet-engine-attachment-link-callback';
	}

	/**
	 * Check if related plugin for current external module is active
	 *
	 * @return boolean [description]
	 */
	public function is_related_plugin_active() {
		return function_exists( 'jet_engine_add_attachment_link_callback' );
	}

	/**
	 * Module name
	 *
	 * @return string
	 */
	public function module_name() {
		return __( 'Attachment file link by ID', 'jet-engine' );
	}

	/**
	 * Returns detailed information about current module for the dashboard page
	 * @return [type] [description]
	 */
	public function get_module_description() {
		return '<p>A callback to output the media file link through its ID.</p>
			<p>This module adds a new callback to the Dynamic Field widget.</p>
			<p>Allows to display download links for .pdf, .zip, and other file formats using the Dynamic Field widget and use it to output such links to the archive and single page templates.</p>';
	}

	/**
	 * Returns information about the related plugin for current module
	 *
	 * @return [type] [description]
	 */
	public function get_related_plugin_data() {
		return array(
			'file' => 'jet-engine-attachment-link-callback/jet-engine-attachment-link-callback.php',
			'name' => 'JetEngine - get attachment file link by ID',
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

}
