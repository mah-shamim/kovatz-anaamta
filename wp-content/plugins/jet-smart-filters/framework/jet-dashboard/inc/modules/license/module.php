<?php
namespace Jet_Dashboard\Modules\License;

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
		return 'license-page';
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
		return esc_html__( 'Plugin Manager', 'jet-dashboard' );
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
		add_action( 'admin_menu', array( $this, 'register_plugins_page' ), -997 );
		add_action( 'admin_menu', array( $this, 'register_license_page' ), 9999 );
	}

	/**
	 * [register_page description]
	 * @return [type] [description]
	 */
	public function register_plugins_page() {

		add_submenu_page(
			Dashboard::get_instance()->dashboard_slug,
			esc_html__( 'Update & Installation', 'jet-dashboard' ),
			esc_html__( 'Update & Installation', 'jet-dashboard' ),
			'manage_options',
			Dashboard::get_instance()->dashboard_slug . '-license-page',
			function() {
				include Dashboard::get_instance()->get_view( 'common/dashboard' );
			}
		);

	}

	/**
	 * [register_license_page description]
	 * @return [type] [description]
	 */
	public function register_license_page() {

		add_submenu_page(
			Dashboard::get_instance()->dashboard_slug,
			esc_html__( 'License', 'jet-dashboard' ),
			esc_html__( 'License', 'jet-dashboard' ),
			'manage_options',
			add_query_arg(
				array(
					'page'    => 'jet-dashboard-license-page',
					'subpage' => 'license-manager'
				),
				admin_url( 'admin.php' )
			)
		);
	}

	/**
	 * [get_page_link description]
	 * @return [type] [description]
	 */
	public function get_page_link() {
		return Dashboard::get_instance()->get_dashboard_page_url( $this->get_page_slug(), $this->get_parent_slug() );
	}

	/**
	 * Enqueue module-specific assets
	 *
	 * @return void
	 */
	public function enqueue_module_assets() {
		wp_enqueue_script(
			'jet-dashboard-license-page',
			Dashboard::get_instance()->get_dashboard_url() . 'assets/js/license-page.js',
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

		$config['pageModule']    = $this->get_page_slug();
		$config['allJetPlugins'] = Dashboard::get_instance()->plugin_manager->get_plugin_data_list();

		return $config;
	}

	/**
	 * [page_templates description]
	 * @param  array  $templates [description]
	 * @param  string $subpage   [description]
	 * @return [type]            [description]
	 */
	public function page_templates( $templates = array(), $page = false, $subpage = false ) {

		$templates['license-page']          = Dashboard::get_instance()->get_view( 'license/main' );
		$templates['license-item']          = Dashboard::get_instance()->get_view( 'license/license-item' );
		$templates['plugin-item-installed'] = Dashboard::get_instance()->get_view( 'license/plugin-item-installed' );
		$templates['plugin-item-avaliable'] = Dashboard::get_instance()->get_view( 'license/plugin-item-avaliable' );
		$templates['plugin-item-more']      = Dashboard::get_instance()->get_view( 'license/plugin-item-more' );
		$templates['responce-info']         = Dashboard::get_instance()->get_view( 'license/responce-info' );

		return $templates;
	}
}
