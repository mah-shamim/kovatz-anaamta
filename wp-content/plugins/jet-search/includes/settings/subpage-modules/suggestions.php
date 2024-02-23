<?php
namespace Jet_Search\Settings;

use Jet_Dashboard\Base\Page_Module as Page_Module_Base;
use Jet_Dashboard\Dashboard as Dashboard;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Suggestions extends Page_Module_Base {

	/**
	 * Returns module slug
	 *
	 * @return void
	 */
	public function get_page_slug() {
		return 'jet-search-suggestions-settings';
	}

	/**
	 * [get_subpage_slug description]
	 * @return [type] [description]
	 */
	public function get_parent_slug() {
		return 'settings-page';
	}

	/**
	 * [get_page_name description]
	 * @return [type] [description]
	 */
	public function get_page_name() {
		return esc_html__( 'Suggestions', 'jet-dashboard' );
	}

	/**
	 * [get_category description]
	 * @return [type] [description]
	 */
	public function get_category() {
		return 'jet-search-settings';
	}

	/**
	 * [get_page_link description]
	 * @return [type] [description]
	 */
	public function get_page_link() {
		return Dashboard::get_instance()->get_dashboard_page_url( $this->get_parent_slug(), $this->get_page_slug() );
	}

	/**
	 * Enqueue module-specific assets
	 *
	 * @return void
	 */
	public function enqueue_module_assets() {

		// wp_enqueue_style(
		// 	'jet-search-admin-css',
		// 	jet_search()->plugin_url( 'assets/css/jet-elements-admin.css' ),
		// 	false,
		// 	jet_search()->get_version()
		// );

		wp_enqueue_style(
			'jet-search-suggestions-admin-css',
			jet_search()->plugin_url( 'assets/css/admin/jet-search-suggestions.css' ),
			false,
			jet_search()->get_version()
		);

		wp_enqueue_script(
			'jet-search-admin-script',
			jet_search()->plugin_url( 'assets/js/jet-search-admin-vue-components.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch' ),
			jet_search()->get_version(),
			true
		);

		wp_localize_script(
			'jet-search-admin-script',
			'JetSearchSettingsConfig',
			apply_filters( 'jet-search/admin/settings-page/localized-config', jet_search_settings()->generate_frontend_config_data() )
		);

	}

	/**
	 * License page config
	 *
	 * @param  array  $config  [description]
	 * @param  string $subpage [description]
	 * @return [type]          [description]
	 */
	public function page_config( $config = array(), $page = false, $subpage = false ) {

		$config['pageModule'] = $this->get_parent_slug();
		$config['subPageModule'] = $this->get_page_slug();

		return $config;
	}

	/**
	 * [page_templates description]
	 * @param  array  $templates [description]
	 * @param  string $subpage   [description]
	 * @return [type]            [description]
	 */
	public function page_templates( $templates = array(), $page = false, $subpage = false ) {

		$templates['jet-search-suggestions-settings'] = jet_search()->plugin_path( 'templates/admin-templates/suggestions-settings.php' );
		$templates['jet-search-suggestions-popup'] = jet_search()->plugin_path( 'templates/admin-templates/suggestions-popup.php' );
		$templates['jet-search-suggestions-add-new'] = jet_search()->plugin_path( 'templates/admin-templates/suggestions-add-new.php' );
		$templates['jet-search-suggestions-pagination'] = jet_search()->plugin_path( 'templates/admin-templates/suggestions-pagination.php' );
		$templates['jet-search-suggestions-filter'] = jet_search()->plugin_path( 'templates/admin-templates/suggestions-filter.php' );
		$templates['jet-search-suggestions-config'] = jet_search()->plugin_path( 'templates/admin-templates/suggestions-config.php' );
		return $templates;
	}
}
