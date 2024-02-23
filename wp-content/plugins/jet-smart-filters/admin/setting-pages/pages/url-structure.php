<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

final class Jet_Smart_Filters_Admin_Setting_Page_URL_Structure extends Jet_Smart_Filters_Admin_Setting_Page_Base {

	public function get_page_slug() {

		return 'jet-smart-filters-url-structure-settings';
	}

	public function get_page_name() {

		return esc_html__( 'URL Structure Settings', 'jet-smart-filters' );
	}

	public function enqueue_module_assets() {
		
		parent::enqueue_module_assets();

		jet_smart_filters()->print_x_templates( 'jet-smart-filters-url-aliases-example', 'admin/setting-pages/templates/components/url-aliases-example.php' );
	}

	public function page_templates( $templates = array(), $page = false, $subpage = false ) {

		$templates['jet-smart-filters-url-structure-settings'] = jet_smart_filters()->plugin_path( 'admin/setting-pages/templates/url-structure-settings.php' );

		return $templates;
	}
}
