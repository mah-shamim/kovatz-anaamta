<?php
namespace Jet_Dashboard\Modules\Welcome;

use Jet_Dashboard\Base\Page_Module as Page_Module_Base;
use Jet_Dashboard\Dashboard as Dashboard;
use Jet_Dashboard\Utils as Utils;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Module extends Page_Module_Base {

	/**
	 * Returns module slug
	 *
	 * @return void
	 */
	public function get_page_slug() {
		return 'welcome-page';
	}

	/**
	 * [get_subpage_slug description]
	 * @return [type] [description]
	 */
	public function get_parent_slug() {
		return false;
	}

	/**
	 * [get_page_name description]
	 * @return [type] [description]
	 */
	public function get_page_name() {
		return esc_html__( 'Welcome', 'jet-dashboard' );
	}

	/**
	 * [get_category description]
	 * @return [type] [description]
	 */
	public function get_category() {
		return false;
	}

	/**
	 * [get_page_link description]
	 * @return [type] [description]
	 */
	public function get_page_link() {
		return Dashboard::get_instance()->get_dashboard_page_url( $this->get_page_slug(), $this->get_parent_slug() );
	}

	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public function init() {

	}

	/**
	 * Enqueue module-specific assets
	 *
	 * @return void
	 */
	public function enqueue_module_assets() {

		wp_enqueue_script(
			'jet-dashboard-welcome-page',
			Dashboard::get_instance()->get_dashboard_url() . 'assets/js/welcome-page.js',
			array( 'cx-vue-ui' ),
			Dashboard::get_instance()->get_dashboard_version(),
			true
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

		$config['pageModule']        = $this->get_page_slug();
		$config['allJetPlugins']     = Dashboard::get_instance()->plugin_manager->get_plugin_data_list();
		$config['userPlugins']       = Dashboard::get_instance()->plugin_manager->get_user_plugins();
		$config['offersConfig']      = Dashboard::get_instance()->data_manager->get_dashboard_config( 'offers' );
		$config['extrasConfig']      = Dashboard::get_instance()->data_manager->get_dashboard_config( 'extras' );
		$config['generalConfig']     = Dashboard::get_instance()->data_manager->get_dashboard_config( 'general' );
		$config['adminUrl']          = admin_url();
		$config['licensePageUrl']    = Dashboard::get_instance()->get_dashboard_page_url( 'license-page' );
		$config['licenseManagerUrl'] = Dashboard::get_instance()->get_dashboard_page_url( 'license-page', 'license-manager' );
		$config['crocoWizardData']   = Dashboard::get_instance()->plugin_manager->get_user_plugin( 'crocoblock-wizard/crocoblock-wizard.php' );

		return $config;
	}

	/**
	 * Add welcome component template
	 *
	 * @param  array  $templates [description]
	 * @param  string $subpage   [description]
	 * @return [type]            [description]
	 */
	public function page_templates( $templates = array(), $page = false, $subpage = false ) {

		$templates['welcome-page']           = Dashboard::get_instance()->get_view( 'welcome/main' );
		$templates['plugin-item-registered'] = Dashboard::get_instance()->get_view( 'welcome/plugin-item-registered' );
		$templates['plugin-item-more']       = Dashboard::get_instance()->get_view( 'license/plugin-item-more' );
		$templates['offers-item']            = Dashboard::get_instance()->get_view( 'welcome/offers-item' );
		$templates['extras-item']            = Dashboard::get_instance()->get_view( 'welcome/extras-item' );

		return $templates;
	}
}
