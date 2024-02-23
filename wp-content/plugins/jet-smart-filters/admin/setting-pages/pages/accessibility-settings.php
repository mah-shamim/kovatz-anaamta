<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

final class Jet_Smart_Filters_Admin_Setting_Page_Accessibility extends Jet_Smart_Filters_Admin_Setting_Page_Base {

	public function get_page_slug() {

		return 'jet-smart-filters-accessibility-settings';
	}

	public function get_page_name() {

		return esc_html__( 'Accessibility Settings', 'jet-smart-filters' );
	}

	public function page_templates( $templates = array(), $page = false, $subpage = false ) {

		$templates['jet-smart-filters-accessibility-settings'] = jet_smart_filters()->plugin_path( 'admin/setting-pages/templates/accessibility-settings.php' );

		return $templates;
	}
}
