<?php
/**
 * Custom content types module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Module_Dynamic_Tables class
 */
class Jet_Engine_Module_Dynamic_Tables extends Jet_Engine_External_Module_Base {

	/**
	 * Module ID
	 *
	 * @return string
	 */
	public function module_id() {
		return 'jet-engine-dynamic-tables-module';
	}

	/**
	 * Check if related plugin for current external module is active
	 *
	 * @return boolean [description]
	 */
	public function is_related_plugin_active() {
		return defined( 'JET_ENGINE_DYNAMIC_TABLES_VERSION' );
	}

	/**
	 * Module name
	 *
	 * @return string
	 */
	public function module_name() {
		return __( 'Dynamic Tables Builder', 'jet-engine' );
	}

	/**
	 * Returns detailed information about current module for the dashboard page
	 * @return [type] [description]
	 */
	public function get_module_description() {
		return '<p>It allows you to create different tables based on dynamic data, be it tables of posts, WooCommerce products, users, terms, comments, etc. You can also build tables based on CCT data or tables with data queried directly from the SQL database. In the table cells, you can either display the data pulled from the database or Listing templates built based on this data.</p><p>Also, the Dynamic Tables module is compatible with JetSmartFilters, so the selected data can be filtered or sorted as you like.</p>';
	}

	/**
	 * Returns information about the related plugin for current module
	 *
	 * @return [type] [description]
	 */
	public function get_related_plugin_data() {
		return array(
			'file' => 'jet-engine-dynamic-tables-module/jet-engine-dynamic-tables-module.php',
			'name' => 'JetEngine - dynamic tables builder',
		);
	}

	/**
	 * Returns actions allowed after plugin installation
	 *
	 * @return [type] [description]
	 */
	public function get_installed_actions() {
		return array(
			array(
				'id'    => 'table-builder',
				'label' => __( 'Go to Tables Builder', 'jet-engine' ),
				'style' => 'accent',
				'url'   => admin_url( 'admin.php?page=jet-engine-tables' ),
			),
			array(
				'label' => 'Tables Builder Overview',
				'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-tables-builder-overview/',
			),
		);
	}

	public function get_video_embed() {
		return 'https://www.youtube.com/embed/X3KK76UZZ3w';
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
				'label'    => __( 'Tables Builder Overview', 'jet-engine' ),
				'url'      => 'https://crocoblock.com/knowledge-base/articles/jetengine-tables-builder-overview/',
			),
			
			array(
				'label'    => 'How to create WooCommerce Dynamic Product Table',
				'url'      => 'https://www.youtube.com/watch?v=X3KK76UZZ3w',
				'is_video' => true,
			),
			array(
				'label'    => 'How to add filters to Dynamic Product Table',
				'url'      => 'https://www.youtube.com/watch?v=2JK0L_56di8',
				'is_video' => true,
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
