<?php
/**
 * Custom content types module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Module_Dynamic_Charts class
 */
class Jet_Engine_Module_Dynamic_Charts extends Jet_Engine_External_Module_Base {

	/**
	 * Module ID
	 *
	 * @return string
	 */
	public function module_id() {
		return 'jet-engine-dynamic-charts-module';
	}

	/**
	 * Check if related plugin for current external module is active
	 *
	 * @return boolean [description]
	 */
	public function is_related_plugin_active() {
		return defined( 'JET_ENGINE_DYNAMIC_CHARTS_VERSION' );
	}

	/**
	 * Returns actions allowed after plugin installation
	 *
	 * @return [type] [description]
	 */
	public function get_installed_actions() {
		return array(
			array(
				'id'    => 'charts-builder',
				'label' => __( 'Go to Charts Builder', 'jet-engine' ),
				'style' => 'accent',
				'url'   => admin_url( 'admin.php?page=jet-engine-charts' ),
			),
			array(
				'label' => __( 'Charts Builder Overview', 'jet-engine' ),
				'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-charts-builder-overview/',
			),
		);
	}

	/**
	 * Module name
	 *
	 * @return string
	 */
	public function module_name() {
		return __( 'Dynamic Charts Builder', 'jet-engine' );
	}

	/**
	 * Returns detailed information about current module for the dashboard page
	 * @return [type] [description]
	 */
	public function get_module_description() {
		return '<p>This module is designed to display dynamic data in graph format.</p>';
	}

	/**
	 * Returns information about the related plugin for current module
	 *
	 * @return [type] [description]
	 */
	public function get_related_plugin_data() {
		return array(
			'file' => 'jet-engine-dynamic-charts-module/jet-engine-dynamic-charts-module.php',
			'name' => 'JetEngine - dynamic charts builder',
		);
	}

	/**
	 * Returns array links to the module-related resources
	 * @return array
	 */
	public function get_module_links() {
		return array(
			array(
				'label'    => __( 'Go to Tables Builder', 'jet-engine' ),
				'url'      => admin_url( 'admin.php?page=jet-engine-tables' ),
				'is_local' => true,
			),
			array(
				'label' => __( 'Charts Builder Overview', 'jet-engine' ),
				'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-charts-builder-overview/',
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
