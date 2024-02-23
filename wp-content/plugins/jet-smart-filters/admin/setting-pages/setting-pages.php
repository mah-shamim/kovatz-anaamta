<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Controller class
 */
class Jet_Smart_Filters_Admin_Setting_Pages {

	public $subpage_modules = array();

	// Here initialize our namespace and resource name.
	public function __construct() {

		$this->subpage_modules = apply_filters( 'jet-smart-filters/setting-pages/pages', array(
			'jet-smart-filters-general-settings' => array(
				'class' => 'Jet_Smart_Filters_Admin_Setting_Page_General',
				'args'  => array(),
			),
			'jet-smart-filters-indexer-settings' => array(
				'class' => 'Jet_Smart_Filters_Admin_Setting_Page_Indexer',
				'args'  => array(),
			),
			'jet-smart-filters-url-structure-settings' => array(
				'class' => 'Jet_Smart_Filters_Admin_Setting_Page_URL_Structure',
				'args'  => array(),
			),
			'jet-smart-filters-ajax-request-type' => array(
				'class' => 'Jet_Smart_Filters_Admin_Setting_Page_Ajax_Request_Type',
				'args'  => array(),
			),
			'jet-smart-filters-accessibility-settings' => array(
				'class' => 'Jet_Smart_Filters_Admin_Setting_Page_Accessibility',
				'args'  => array(),
			),
			'jet-smart-filters-provider-preloader' => array(
				'class' => 'Jet_Smart_Filters_Admin_Setting_Page_Provider_Preloader',
				'args'  => array(),
			),
		) );

		add_action( 'init', array( $this, 'register_settings_category' ), 10 );
		add_action( 'init', array( $this, 'init_plugin_subpage_modules' ), 10 );
	}

	public function register_settings_category() {

		\Jet_Dashboard\Dashboard::get_instance()->module_manager->register_module_category( array(
			'name'     => esc_html__( 'JetSmartFilters', 'jet-smart-filters' ),
			'slug'     => 'jet-smart-filters-settings',
			'priority' => 1
		) );
	}

	public function init_plugin_subpage_modules() {

		require jet_smart_filters()->plugin_path( 'admin/setting-pages/pages/base.php' );
		require jet_smart_filters()->plugin_path( 'admin/setting-pages/pages/general.php' );
		require jet_smart_filters()->plugin_path( 'admin/setting-pages/pages/indexer.php' );
		require jet_smart_filters()->plugin_path( 'admin/setting-pages/pages/url-structure.php' );
		require jet_smart_filters()->plugin_path( 'admin/setting-pages/pages/ajax-request-type.php' );
		require jet_smart_filters()->plugin_path( 'admin/setting-pages/pages/accessibility-settings.php' );
		require jet_smart_filters()->plugin_path( 'admin/setting-pages/pages/provider-preloader.php' );

		foreach ( $this->subpage_modules as $subpage => $subpage_data ) {
			\Jet_Dashboard\Dashboard::get_instance()->module_manager->register_subpage_module( $subpage, $subpage_data );
		}
	}
}

