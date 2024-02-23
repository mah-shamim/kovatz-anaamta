<?php
namespace Jet_Dashboard\Modules\Settings;

use Jet_Dashboard\Base\Page_Module as Page_Module_Base;
use Jet_Dashboard\Dashboard as Dashboard;
use Jet_Dashboard\Utils as Utils;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Module extends Page_Module_Base {

	/**
	 * [$default_settings_page description]
	 * @var boolean
	 */
	public $default_subpage_module = false;

	/**
	 * Returns module slug
	 *
	 * @return void
	 */
	public function get_page_slug() {
		return 'settings-page';
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
		return esc_html__( 'Settings', 'jet-dashboard' );
	}

	/**
	 * [get_category description]
	 * @return [type] [description]
	 */
	public function get_category() {
		return false;
	}

	/**
	 * [create description]
	 * @return [type] [description]
	 */
	public function create() {
		add_action( 'admin_menu', array( $this, 'register_page' ), -998 );
	}

	/**
	 * [register_page description]
	 * @return [type] [description]
	 */
	public function register_page() {

		if ( empty( Dashboard::get_instance()->module_manager->get_registered_subpage_modules() ) ) {
			return;
		}

		add_submenu_page(
			Dashboard::get_instance()->dashboard_slug,
			esc_html__( 'JetPlugins Settings', 'jet-dashboard' ),
			esc_html__( 'JetPlugins Settings', 'jet-dashboard' ),
			'manage_options',
			Dashboard::get_instance()->dashboard_slug . '-settings-page',
			function() {
				include Dashboard::get_instance()->get_view( 'common/dashboard' );
			}
		);
	}

	/**
	 * [get_page_link description]
	 * @return [type] [description]
	 */
	public function get_page_link() {
		return Dashboard::get_instance()->get_dashboard_page_url( $this->get_page_slug(), $this->get_parent_slug() );
	}

	public function init() {
		$page_slug = $this->get_page_slug();
		$subpage = Dashboard::get_instance()->get_subpage();

		if ( ! $subpage ) {
			$subpage_module_list = Dashboard::get_instance()->module_manager->get_subpage_module_list( $page_slug );

			$this->default_subpage_module = ! empty( $subpage_module_list ) ? array_values( $subpage_module_list )[0]['page'] : false;
			Dashboard::get_instance()->module_manager->load_subpage_module( $this->default_subpage_module );
		}
	}

	/**
	 * Enqueue module-specific assets
	 *
	 * @return void
	 */
	public function enqueue_module_assets() {
		wp_enqueue_script(
			'jet-dashboard-settings-page',
			Dashboard::get_instance()->get_dashboard_url() . 'assets/js/settings-page.js',
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

		$config['pageModule']       = $this->get_page_slug();
		$config['subPageModule']    = $this->default_subpage_module;
		$config['subpageList']      = Dashboard::get_instance()->module_manager->get_subpage_module_list( $this->get_page_slug() );
		$config['categoryList']     = Dashboard::get_instance()->module_manager->get_subpage_category_list( $this->get_page_slug() );

		if ( ! Dashboard::get_instance()->get_subpage() ) {
			$config['pageModuleConfig'] = Dashboard::get_instance()->data_manager->get_dashboard_page_config( $this->get_page_slug(), $this->default_subpage_module );
		}

		return $config;
	}

	/**
	 * [page_templates description]
	 * @param  array  $templates [description]
	 * @param  string $subpage   [description]
	 * @return [type]            [description]
	 */
	public function page_templates( $templates = array(), $page = false, $subpage = false ) {

		$templates['settings-page'] = Dashboard::get_instance()->get_view( 'settings/main' );
		$templates['plugin-settings-toggle'] = Dashboard::get_instance()->get_view( 'settings/plugin-settings-toggle' );

		return $templates;
	}
}
