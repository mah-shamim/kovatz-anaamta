<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * Elementor integrations
 */
class Jet_Smart_Filters_Elementor_Manager {

	public function __construct() {

		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		require jet_smart_filters()->plugin_path( 'includes/elementor/widgets.php' );
		jet_smart_filters()->widgets = new Jet_Smart_Filters_Widgets_Manager();

		$this->init_dynamic_tags();
	}

	public function init_dynamic_tags() {

		$init_action = 'elementor/init';

		// Init a module early on Elementor Data Updater
		if ( is_admin() && ( isset( $_GET['elementor_updater'] ) || isset( $_GET['elementor_pro_updater'] ) ) ) {
			$init_action = 'elementor/documents/register';
		}

		add_action( $init_action, array( $this, 'init_dynamic_tags_module' ) );
	}

	public function init_dynamic_tags_module() {

		require jet_smart_filters()->plugin_path( 'includes/elementor/dynamic-tags/module.php' );
		new Jet_Smart_Filters_Elementor_Dynamic_Tags_Module();
	}
}
