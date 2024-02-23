<?php
/**
 * Jet Dashboard Module
 *
 * Version: 2.1.5.1
 */

namespace Jet_Dashboard;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Dashboard {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * Module directory path.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var srting.
	 */
	protected $path;

	/**
	 * Module directory URL.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var srting.
	 */
	protected $url;

	/**
	 * Module version
	 *
	 * @var string
	 */
	protected $version = '2.1.5.1';

	/**
	 * [$dashboard_slug description]
	 * @var string
	 */
	public $dashboard_slug = 'jet-dashboard';

	/**
	 * [$module_manager description]
	 * @var null
	 */
	public $module_manager = null;

	/**
	 * @var null
	 */
	public $wp_dashboard_manager = null;

	/**
	 * [$data_manager description]
	 * @var null
	 */
	public $data_manager = null;

	/**
	 * [$license_manager description]
	 * @var null
	 */
	public $license_manager = null;

	/**
	 * [$plugin_updater description]
	 * @var null
	 */
	public $plugin_manager = null;

	/**
	 * [$notice_manager description]
	 * @var null
	 */
	public $notice_manager = null;

	/**
	 * [$compat_manager description]
	 * @var null
	 */
	public $compat_manager = null;

	/**
	 * [$subpage description]
	 * @var null
	 */
	private $page = null;

	/**
	 * [$subpage description]
	 * @var null
	 */
	private $subpage = null;

	/**
	 * [$default_args description]
	 * @var [type]
	 */
	public $default_args = array(
		'path'           => '',
		'url'            => '',
		'cx_ui_instance' => false,
		'plugin_data'    => array(
			'slug'         => false,
			'file'         => '',
			'version'      => '',
			'plugin_links' => array()
		),
	);

	/**
	 * [$args description]
	 * @var array
	 */
	public $args = array();

	/**
	 * [$cx_ui_instance description]
	 * @var boolean
	 */
	public $cx_ui_instance = false;

	/**
	 * [$plugin_slug description]
	 * @var boolean
	 */
	public $plugin_data = false;

	/**
	 * [$assets_enqueued description]
	 * @var boolean
	 */
	protected $assets_enqueued = false;

	/**
	 * [$registered_plugins description]
	 * @var array
	 */
	public $registered_plugins = array();

	/**
	 * Jet_Dashboard constructor.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->load_files();

		add_action( 'init', array( $this, 'init_managers' ), -998 );

		add_action( 'admin_menu', array( $this, 'register_page' ), -999 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dashboard_assets' ) );

		add_action( 'admin_head', array( $this, 'modify_item_styles') );
	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {
		/**
		 * Modules
		 */
		require $this->path . 'inc/modules/manager.php';
		require $this->path . 'inc/modules/page-base.php';
		require $this->path . 'inc/modules/welcome/module.php';
		require $this->path . 'inc/modules/welcome/dev-test.php';
		require $this->path . 'inc/modules/license/module.php';
		require $this->path . 'inc/modules/settings/module.php';
		require $this->path . 'inc/modules/upsale/module.php';

		require $this->path . 'inc/utils.php';
		require $this->path . 'inc/wp-dashboard-manager.php';
		require $this->path . 'inc/license-manager.php';
		require $this->path . 'inc/plugin-manager.php';
		require $this->path . 'inc/data-manager.php';
		require $this->path . 'inc/notice-manager.php';

		/**
		 * Compatibility
		 */
		require $this->path . 'inc/compatibility/manager.php';
		require $this->path . 'inc/compatibility/base-theme.php';
		require $this->path . 'inc/compatibility/themes/hello.php';
	}

	/**
	 * [init_managers description]
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function init_managers() {
		$this->module_manager  = new Modules\Manager();
		$this->notice_manager  = new Notice_Manager();
		$this->data_manager    = new Data_Manager();
		$this->license_manager = new License_Manager();
		$this->plugin_manager  = new Plugin_Manager();
		$this->compat_manager  = new Compatibility\Manager();
		$this->wp_dashboard_manager  = new WP_Dashboard_Manager();
	}

	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public function init( $args = [] ) {

		$this->args = wp_parse_args( $args, $this->default_args );

		$this->path = ! empty( $this->args['path'] ) ? $this->args['path'] : false;
		$this->url  = ! empty( $this->args['url'] ) ? $this->args['url'] : false;

		if ( ! $this->path || ! $this->url || ! $this->args['cx_ui_instance'] ) {
			wp_die(
				'Jet_Dashboard not initialized. Module URL, Path, UI instance and plugin data should be passed into constructor',
				'Jet_Dashboard Error'
			);
		}

		$plugin_data = wp_parse_args( $this->args['plugin_data'], $this->default_args['plugin_data'] );

		$this->register_plugin( $this->args['plugin_data']['file'], $plugin_data );
	}

	/**
	 * Register add/edit page
	 *
	 * @return void
	 */
	public function register_page() {

		add_menu_page(
			esc_html__( 'Crocoblock', 'jet-dashboard' ),
			esc_html__( 'Crocoblock', 'jet-dashboard' ),
			'manage_options',
			$this->dashboard_slug,
			function() {
				include $this->get_view( 'common/dashboard' );
			},
			'data:image/svg+xml;base64,' . base64_encode('<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M24 10.0001C26.2095 10.0001 28 8.20905 28 6.00004C28 3.791 26.2095 2 24 2C23.9963 2 23.9929 2.00052 23.9893 2.00052C15.1578 2.00627 8 9.16718 8 18.0001C8 26.8368 15.1636 34 24 34C26.2095 34 28 32.2091 28 30C28 27.7909 26.2095 26 24 26L23.9963 26.0002C19.5798 25.998 15.9999 22.4171 15.9999 18.0001C15.9999 13.5818 19.5817 10.0001 24 10.0001Z" fill="#A0A5AA"/></svg>' ),
			'60.1'
		);

		add_submenu_page(
			$this->dashboard_slug,
			esc_html__( 'Dashboard', 'jet-dashboard' ),
			esc_html__( 'Dashboard', 'jet-dashboard' ),
			'manage_options',
			$this->dashboard_slug
		);

		do_action( 'jet-dashboard/after-page-registration', $this );
	}

	/**
	 * [maybe_modify_subpages_links description]
	 * @return [type] [description]
	 */
	public function maybe_modify_subpages_links() {
		global $submenu;

		$submenu['jet-dashboard'][3][2] = 'admin.php?page=jet-dashboard-license-page&subpage=license-manager';
	}

	/**
	 * [render_dashboard description]
	 * @return [type] [description]
	 */
	public function render_dashboard() {
		include $this->get_view( 'common/dashboard' );
	}

	/**
	 * [get_dashboard_version description]
	 * @return [type] [description]
	 */
	public function get_dashboard_path() {
		return $this->path;
	}

	/**
	 * [get_dashboard_version description]
	 * @return [type] [description]
	 */
	public function get_dashboard_url() {
		return $this->url;
	}

	/**
	 * [get_dashboard_version description]
	 * @return [type] [description]
	 */
	public function get_dashboard_version() {
		return $this->version;
	}

	/**
	 * [get_registered_plugins description]
	 * @return [type] [description]
	 */
	public function get_registered_plugins() {
		return $this->registered_plugins;
	}

	/**
	 * [get_registered_plugins description]
	 * @return [type] [description]
	 */
	public function register_plugin( $plugin_slug = false, $plugin_data = array() ) {

		if ( ! array_key_exists( $plugin_slug, $this->registered_plugins ) ) {
			$this->registered_plugins[ $plugin_slug ] = $plugin_data;
		}

		return false;
	}

	/**
	 * Returns path to view file
	 *
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	public function get_view( $path ) {
		return apply_filters( 'jet-dashboard/get-view', $this->path . 'views/' . $path . '.php' );
	}

	/**
	 * Returns wizard initial subpage
	 *
	 * @return string
	 */
	public function get_initial_page() {
		return 'welcome-page';
	}

	/**
	 * Check if dashboard page is currently displayiing
	 *
	 * @return boolean [description]
	 */
	public function is_dashboard_page() {
		return ( ! empty( $_GET['page'] ) && false !== strpos( $_GET['page'], $this->dashboard_slug ) );
	}

	/**
	 * Returns current subpage slug
	 *
	 * @return string
	 */
	public function get_page() {

		if ( null === $this->page ) {
			$page = isset( $_GET['page'] ) && $this->dashboard_slug !== $_GET['page'] ? esc_attr( $_GET['page'] ) : $this->dashboard_slug . '-' . $this->get_initial_page();
			$this->page = str_replace( $this->dashboard_slug . '-', '', $page );
		}

		return $this->page;
	}

	/**
	 * [get_subpage description]
	 * @return [type] [description]
	 */
	public function get_subpage() {

		if ( null === $this->subpage ) {
			$this->subpage = isset( $_GET['subpage'] ) && $this->is_dashboard_page() ? esc_attr( $_GET['subpage'] ) : false;
		}

		return $this->subpage;
	}

	/**
	 * [get_admin_url description]
	 * @return [type] [description]
	 */
	public function get_dashboard_page_url( $page = null, $subpage = null, $args = array() ) {
		$page = $this->dashboard_slug . '-' . $page;

		$page_args = array(
			'page'    => $page,
			'subpage' => $subpage,
		);

		if ( ! empty( $args ) ) {
			$page_args = array_merge( $page_args, $args );
		}

		return add_query_arg( $page_args, admin_url( 'admin.php' ) );
	}

	/**
	 * [init_ui_instance description]
	 * @param  boolean $ui_callback [description]
	 * @return [type]               [description]
	 */
	public function init_ui_instance( $ui_callback = false ) {

		if ( $ui_callback && is_object( $ui_callback ) && 'CX_Vue_UI' === get_class( $ui_callback ) ) {
			$this->cx_ui_instance = $ui_callback;
		}

		if ( ! $ui_callback || ! is_callable( $ui_callback ) ) {
			return;
		}

		$this->cx_ui_instance = call_user_func( $ui_callback );
	}

	/**
	 * [enqueue_dashboard_assets description]
	 * @param  [type] $hook [description]
	 * @return [type]       [description]
	 */
	public function enqueue_dashboard_assets( $hook ) {

		// Enqueue WP Admin assets
		$this->enqueue_wp_admin_assets();

		if ( ! $this->is_dashboard_page() ) {
			return false;
		}

		if ( $this->assets_enqueued ) {
			return false;
		}

		$this->enqueue_assets();

		$this->assets_enqueued = true;
	}

	/**
	 * @return void
	 */
	public function enqueue_wp_admin_assets() {

		wp_enqueue_style(
			'jet-wp-admin-styles',
			$this->url . 'assets/css/wp-admin-styles.css',
			false,
			$this->version
		);
	}

	/**
	 * [modify_item_styles description]
	 * @return [type] [description]
	 */
	public function modify_item_styles() {
		echo '<style type="text/css">
			#adminmenu #toplevel_page_jet-dashboard a[href*="admin.php?page=jet-dashboard-license-page&subpage=license-manager"] { color: #4aa5f5; }
			#adminmenu #toplevel_page_jet-dashboard a[href="admin.php?page=jet-dashboard-upsale-page"] { color: #F5C546; }
			#adminmenu #toplevel_page_jet-dashboard a[href*="https://account.crocoblock.com/upgrade"] { color: #F5C546; }
			.separator-croco { border-bottom: 1px solid #484a4c; position: relative; }
			.separator-croco::before { display: inline-block; transform: translate(8px, 1px); line-height: 1; }
			.rtl .separator-croco::before { display: inline-block; transform: translate(-8px, 1px); line-height: 1; }
			
			.separator-croco--plugins-before {
				height: 10px !important;
			}
			
			.separator-croco--plugins-before::before {
				content: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAzMiAxMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3QgeD0iMC41IiB5PSIwLjUiIHdpZHRoPSIzMSIgaGVpZ2h0PSI5IiByeD0iMi41IiBmaWxsPSIjMUQyMzI3IiBzdHJva2U9IiM0ODRBNEMiLz4KPHBhdGggZD0iTTMuNDc5NCA3VjIuNjM2MzZINS4wMzQ4QzUuMzc0MjkgMi42MzYzNiA1LjY1NTU0IDIuNjk4MTUgNS44Nzg1NSAyLjgyMTczQzYuMTAxNTYgMi45NDUzMSA2LjI2ODQ3IDMuMTE0MzUgNi4zNzkyNiAzLjMyODg0QzYuNDkwMDYgMy41NDE5IDYuNTQ1NDUgMy43ODE5NiA2LjU0NTQ1IDQuMDQ5MDFDNi41NDU0NSA0LjMxNzQ3IDYuNDg5MzUgNC41NTg5NSA2LjM3NzEzIDQuNzczNDRDNi4yNjYzNCA0Ljk4NjUxIDYuMDk4NzIgNS4xNTU1NCA1Ljg3NDI5IDUuMjgwNTRDNS42NTEyOCA1LjQwNDEyIDUuMzcwNzQgNS40NjU5MSA1LjAzMjY3IDUuNDY1OTFIMy45NjMwN1Y0LjkwNzY3SDQuOTczMDFDNS4xODc1IDQuOTA3NjcgNS4zNjE1MSA0Ljg3MDc0IDUuNDk1MDMgNC43OTY4OEM1LjYyODU1IDQuNzIxNTkgNS43MjY1NiA0LjYxOTMyIDUuNzg5MDYgNC40OTAwNkM1Ljg1MTU2IDQuMzYwOCA1Ljg4MjgxIDQuMjEzNzggNS44ODI4MSA0LjA0OTAxQzUuODgyODEgMy44ODQyMyA1Ljg1MTU2IDMuNzM3OTMgNS43ODkwNiAzLjYxMDA5QzUuNzI2NTYgMy40ODIyNCA1LjYyNzg0IDMuMzgyMSA1LjQ5MjkgMy4zMDk2NkM1LjM1OTM4IDMuMjM3MjIgNS4xODMyNCAzLjIwMDk5IDQuOTY0NDkgMy4yMDA5OUg0LjEzNzc4VjdIMy40Nzk0Wk03LjMxNzI5IDdWMi42MzYzNkg3Ljk3NTY3VjYuNDMzMjRIOS45NTI5NVY3SDcuMzE3MjlaTTEzLjUxMDEgMi42MzYzNkgxNC4xNzA2VjUuNTA2MzlDMTQuMTcwNiA1LjgxMTc5IDE0LjA5ODkgNi4wODIzOSAxMy45NTU0IDYuMzE4MThDMTMuODEyIDYuNTUyNTYgMTMuNjEwMyA2LjczNzIyIDEzLjM1MDMgNi44NzIxNkMxMy4wOTA0IDcuMDA1NjggMTIuNzg1NyA3LjA3MjQ0IDEyLjQzNjMgNy4wNzI0NEMxMi4wODgyIDcuMDcyNDQgMTEuNzg0MyA3LjAwNTY4IDExLjUyNDMgNi44NzIxNkMxMS4yNjQ0IDYuNzM3MjIgMTEuMDYyNyA2LjU1MjU2IDEwLjkxOTIgNi4zMTgxOEMxMC43NzU3IDYuMDgyMzkgMTAuNzA0IDUuODExNzkgMTAuNzA0IDUuNTA2MzlWMi42MzYzNkgxMS4zNjI0VjUuNDUzMTJDMTEuMzYyNCA1LjY1MDU3IDExLjQwNTcgNS44MjU5OSAxMS40OTI0IDUuOTc5NEMxMS41ODA0IDYuMTMyODEgMTEuNzA0NyA2LjI1MzU1IDExLjg2NTIgNi4zNDE2MkMxMi4wMjU3IDYuNDI4MjcgMTIuMjE2MSA2LjQ3MTU5IDEyLjQzNjMgNi40NzE1OUMxMi42NTc4IDYuNDcxNTkgMTIuODQ4OSA2LjQyODI3IDEzLjAwOTQgNi4zNDE2MkMxMy4xNzEzIDYuMjUzNTUgMTMuMjk0OSA2LjEzMjgxIDEzLjM4MDEgNS45Nzk0QzEzLjQ2NjggNS44MjU5OSAxMy41MTAxIDUuNjUwNTcgMTMuNTEwMSA1LjQ1MzEyVjIuNjM2MzZaTTE4LjA4MSA0LjAxNDkxQzE4LjAzOTggMy44ODU2NSAxNy45ODQ0IDMuNzY5ODkgMTcuOTE0OCAzLjY2NzYxQzE3Ljg0NjYgMy41NjM5MiAxNy43NjQ5IDMuNDc1ODUgMTcuNjY5NyAzLjQwMzQxQzE3LjU3NDYgMy4zMjk1NSAxNy40NjU5IDMuMjczNDQgMTcuMzQzOCAzLjIzNTA5QzE3LjIyMyAzLjE5NjczIDE3LjA5MDIgMy4xNzc1NiAxNi45NDUzIDMuMTc3NTZDMTYuNjk5NiAzLjE3NzU2IDE2LjQ3OCAzLjI0MDc3IDE2LjI4MDUgMy4zNjcxOUMxNi4wODMxIDMuNDkzNjEgMTUuOTI2OCAzLjY3ODk4IDE1LjgxMTggMy45MjMzQzE1LjY5ODIgNC4xNjYxOSAxNS42NDEzIDQuNDYzNzggMTUuNjQxMyA0LjgxNjA1QzE1LjY0MTMgNS4xNjk3NCAxNS42OTg5IDUuNDY4NzUgMTUuODEzOSA1LjcxMzA3QzE1LjkyOSA1Ljk1NzM5IDE2LjA4NjYgNi4xNDI3NiAxNi4yODY5IDYuMjY5MThDMTYuNDg3MiA2LjM5NTYgMTYuNzE1MiA2LjQ1ODgxIDE2Ljk3MDkgNi40NTg4MUMxNy4yMDgxIDYuNDU4ODEgMTcuNDE0OCA2LjQxMDUxIDE3LjU5MDkgNi4zMTM5MkMxNy43Njg1IDYuMjE3MzMgMTcuOTA1NSA2LjA4MDk3IDE4LjAwMjEgNS45MDQ4M0MxOC4xMDAxIDUuNzI3MjcgMTguMTQ5MSA1LjUxODQ3IDE4LjE0OTEgNS4yNzg0MUwxOC4zMTk2IDUuMzEwMzdIMTcuMDcxVjQuNzY3MDVIMTguNzg2MlY1LjI2MzQ5QzE4Ljc4NjIgNS42Mjk5NyAxOC43MDgxIDUuOTQ4MTUgMTguNTUxOCA2LjIxODA0QzE4LjM5NyA2LjQ4NjUxIDE4LjE4MjUgNi42OTM4OSAxNy45MDg0IDYuODQwMkMxNy42MzU3IDYuOTg2NTEgMTcuMzIzMiA3LjA1OTY2IDE2Ljk3MDkgNy4wNTk2NkMxNi41NzYgNy4wNTk2NiAxNi4yMjk0IDYuOTY4NzUgMTUuOTMxMSA2Ljc4NjkzQzE1LjYzNDIgNi42MDUxMSAxNS40MDI3IDYuMzQ3MyAxNS4yMzY1IDYuMDEzNDlDMTUuMDcwMyA1LjY3ODI3IDE0Ljk4NzIgNS4yODA1NCAxNC45ODcyIDQuODIwMzFDMTQuOTg3MiA0LjQ3MjMgMTUuMDM1NSA0LjE1OTggMTUuMTMyMSAzLjg4MjgxQzE1LjIyODcgMy42MDU4MiAxNS4zNjQzIDMuMzcwNzQgMTUuNTM5MSAzLjE3NzU2QzE1LjcxNTIgMi45ODI5NSAxNS45MjE5IDIuODM0NTIgMTYuMTU5MSAyLjczMjI0QzE2LjM5NzcgMi42Mjg1NSAxNi42NTg0IDIuNTc2NyAxNi45NDExIDIuNTc2N0MxNy4xNzY4IDIuNTc2NyAxNy4zOTYzIDIuNjExNTEgMTcuNTk5NCAyLjY4MTExQzE3LjgwNCAyLjc1MDcxIDE3Ljk4NTggMi44NDk0MyAxOC4xNDQ5IDIuOTc3MjdDMTguMzA1NCAzLjEwNTExIDE4LjQzODIgMy4yNTcxIDE4LjU0MzMgMy40MzMyNEMxOC42NDg0IDMuNjA3OTUgMTguNzE5NSAzLjgwMTg1IDE4Ljc1NjQgNC4wMTQ5MUgxOC4wODFaTTIwLjI4MDQgMi42MzYzNlY3SDE5LjYyMlYyLjYzNjM2SDIwLjI4MDRaTTI0Ljc2MTIgMi42MzYzNlY3SDI0LjE1NjFMMjEuOTM4IDMuNzk5NzJIMjEuODk3NVY3SDIxLjIzOTJWMi42MzYzNkgyMS44NDg1TDI0LjA2ODcgNS44NDA5MUgyNC4xMDkyVjIuNjM2MzZIMjQuNzYxMlpNMjguMTAxNiAzLjc4MjY3QzI4LjA3ODggMy41ODA5NyAyNy45ODUxIDMuNDI0NzIgMjcuODIwMyAzLjMxMzkyQzI3LjY1NTUgMy4yMDE3IDI3LjQ0ODIgMy4xNDU2IDI3LjE5ODIgMy4xNDU2QzI3LjAxOTIgMy4xNDU2IDI2Ljg2NDMgMy4xNzQwMSAyNi43MzM3IDMuMjMwODJDMjYuNjAzIDMuMjg2MjIgMjYuNTAxNCAzLjM2MjkzIDI2LjQyOSAzLjQ2MDk0QzI2LjM1OCAzLjU1NzUzIDI2LjMyMjQgMy42Njc2MSAyNi4zMjI0IDMuNzkxMTlDMjYuMzIyNCAzLjg5NDg5IDI2LjM0NjYgMy45ODQzNyAyNi4zOTQ5IDQuMDU5NjZDMjYuNDQ0NiA0LjEzNDk0IDI2LjUwOTIgNC4xOTgxNSAyNi41ODg4IDQuMjQ5MjlDMjYuNjY5NyA0LjI5OTAxIDI2Ljc1NjQgNC4zNDA5MSAyNi44NDg3IDQuMzc1QzI2Ljk0MTEgNC40MDc2NyAyNy4wMjk4IDQuNDM0NjYgMjcuMTE1MSA0LjQ1NTk3TDI3LjU0MTIgNC41NjY3NkMyNy42ODA0IDQuNjAwODUgMjcuODIzMiA0LjY0NzAyIDI3Ljk2OTUgNC43MDUyNkMyOC4xMTU4IDQuNzYzNDkgMjguMjUxNCA0Ljg0MDIgMjguMzc2NCA0LjkzNTM3QzI4LjUwMTQgNS4wMzA1NCAyOC42MDIzIDUuMTQ4NDQgMjguNjc5IDUuMjg5MDZDMjguNzU3MSA1LjQyOTY5IDI4Ljc5NjIgNS41OTgwMSAyOC43OTYyIDUuNzk0MDNDMjguNzk2MiA2LjA0MTE5IDI4LjczMjIgNi4yNjA2NSAyOC42MDQ0IDYuNDUyNDFDMjguNDc4IDYuNjQ0MTggMjguMjk0IDYuNzk1NDUgMjguMDUyNiA2LjkwNjI1QzI3LjgxMjUgNy4wMTcwNSAyNy41MjIgNy4wNzI0NCAyNy4xODExIDcuMDcyNDRDMjYuODU0NCA3LjA3MjQ0IDI2LjU3MTcgNy4wMjA2IDI2LjMzMzEgNi45MTY5QzI2LjA5NDUgNi44MTMyMSAyNS45MDc3IDYuNjY2MTkgMjUuNzcyNyA2LjQ3NTg1QzI1LjYzNzggNi4yODQwOSAyNS41NjMyIDYuMDU2ODIgMjUuNTQ5IDUuNzk0MDNIMjYuMjA5NUMyNi4yMjIzIDUuOTUxNyAyNi4yNzM0IDYuMDgzMSAyNi4zNjI5IDYuMTg4MjFDMjYuNDUzOCA2LjI5MTkgMjYuNTY5NiA2LjM2OTMyIDI2LjcxMDIgNi40MjA0NUMyNi44NTIzIDYuNDcwMTcgMjcuMDA3OCA2LjQ5NTAzIDI3LjE3NjggNi40OTUwM0MyNy4zNjI5IDYuNDk1MDMgMjcuNTI4NCA2LjQ2NTkxIDI3LjY3MzMgNi40MDc2N0MyNy44MTk2IDYuMzQ4MDEgMjcuOTM0NyA2LjI2NTYyIDI4LjAxODUgNi4xNjA1MUMyOC4xMDIzIDYuMDUzOTggMjguMTQ0MiA1LjkyOTY5IDI4LjE0NDIgNS43ODc2NEMyOC4xNDQyIDUuNjU4MzggMjguMTA3MiA1LjU1MjU2IDI4LjAzMzQgNS40NzAxN0MyNy45NjA5IDUuMzg3NzggMjcuODYyMiA1LjMxOTYgMjcuNzM3MiA1LjI2NTYyQzI3LjYxMzYgNS4yMTE2NSAyNy40NzM3IDUuMTY0MDYgMjcuMzE3NSA1LjEyMjg3TDI2LjgwMTggNC45ODIyNEMyNi40NTI0IDQuODg3MDcgMjYuMTc1NCA0Ljc0NzE2IDI1Ljk3MDkgNC41NjI1QzI1Ljc2NzggNC4zNzc4NCAyNS42NjYyIDQuMTMzNTIgMjUuNjY2MiAzLjgyOTU1QzI1LjY2NjIgMy41NzgxMiAyNS43MzQ0IDMuMzU4NjYgMjUuODcwNyAzLjE3MTE2QzI2LjAwNzEgMi45ODM2NiAyNi4xOTE4IDIuODM4MDcgMjYuNDI0NyAyLjczNDM4QzI2LjY1NzcgMi42MjkyNiAyNi45MjA1IDIuNTc2NyAyNy4yMTMxIDIuNTc2N0MyNy41MDg1IDIuNTc2NyAyNy43NjkyIDIuNjI4NTUgMjcuOTk1IDIuNzMyMjRDMjguMjIyMyAyLjgzNTk0IDI4LjQwMTMgMi45Nzg2OSAyOC41MzIgMy4xNjA1MUMyOC42NjI2IDMuMzQwOTEgMjguNzMwOCAzLjU0ODMgMjguNzM2NSAzLjc4MjY3SDI4LjEwMTZaIiBmaWxsPSIjQThBQUFEIi8+Cjwvc3ZnPgo=");
			}
			
			.separator-croco--post-type-before {
				height: 10px !important;
			}

			.separator-croco--post-type-before::before {
				content: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDMiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCA0MyAxMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3QgeD0iMC41IiB5PSIwLjUiIHdpZHRoPSI0MiIgaGVpZ2h0PSI5IiByeD0iMi41IiBmaWxsPSIjMUQyMzI3IiBzdHJva2U9IiM0ODRBNEMiLz4KPHBhdGggZD0iTTMuNDc5NCA3VjIuNjM2MzZINS4wMzQ4QzUuMzc0MjkgMi42MzYzNiA1LjY1NTU0IDIuNjk4MTUgNS44Nzg1NSAyLjgyMTczQzYuMTAxNTYgMi45NDUzMSA2LjI2ODQ3IDMuMTE0MzUgNi4zNzkyNiAzLjMyODg0QzYuNDkwMDYgMy41NDE5IDYuNTQ1NDUgMy43ODE5NiA2LjU0NTQ1IDQuMDQ5MDFDNi41NDU0NSA0LjMxNzQ3IDYuNDg5MzUgNC41NTg5NSA2LjM3NzEzIDQuNzczNDRDNi4yNjYzNCA0Ljk4NjUxIDYuMDk4NzIgNS4xNTU1NCA1Ljg3NDI5IDUuMjgwNTRDNS42NTEyOCA1LjQwNDEyIDUuMzcwNzQgNS40NjU5MSA1LjAzMjY3IDUuNDY1OTFIMy45NjMwN1Y0LjkwNzY3SDQuOTczMDFDNS4xODc1IDQuOTA3NjcgNS4zNjE1MSA0Ljg3MDc0IDUuNDk1MDMgNC43OTY4OEM1LjYyODU1IDQuNzIxNTkgNS43MjY1NiA0LjYxOTMyIDUuNzg5MDYgNC40OTAwNkM1Ljg1MTU2IDQuMzYwOCA1Ljg4MjgxIDQuMjEzNzggNS44ODI4MSA0LjA0OTAxQzUuODgyODEgMy44ODQyMyA1Ljg1MTU2IDMuNzM3OTMgNS43ODkwNiAzLjYxMDA5QzUuNzI2NTYgMy40ODIyNCA1LjYyNzg0IDMuMzgyMSA1LjQ5MjkgMy4zMDk2NkM1LjM1OTM4IDMuMjM3MjIgNS4xODMyNCAzLjIwMDk5IDQuOTY0NDkgMy4yMDA5OUg0LjEzNzc4VjdIMy40Nzk0Wk0xMS4xMDc4IDQuODE4MThDMTEuMTA3OCA1LjI4NDA5IDExLjAyMjUgNS42ODQ2NiAxMC44NTIxIDYuMDE5ODlDMTAuNjgxNiA2LjM1MzY5IDEwLjQ0OCA2LjYxMDggMTAuMTUxMSA2Ljc5MTE5QzkuODU1NjUgNi45NzAxNyA5LjUxOTcxIDcuMDU5NjYgOS4xNDMyOSA3LjA1OTY2QzguNzY1NDUgNy4wNTk2NiA4LjQyODA5IDYuOTcwMTcgOC4xMzEyMSA2Ljc5MTE5QzcuODM1NzYgNi42MTA4IDcuNjAyODEgNi4zNTI5OCA3LjQzMjM1IDYuMDE3NzZDNy4yNjE5IDUuNjgyNTMgNy4xNzY2NyA1LjI4MjY3IDcuMTc2NjcgNC44MTgxOEM3LjE3NjY3IDQuMzUyMjcgNy4yNjE5IDMuOTUyNDEgNy40MzIzNSAzLjYxODYxQzcuNjAyODEgMy4yODMzOCA3LjgzNTc2IDMuMDI2MjggOC4xMzEyMSAyLjg0NzNDOC40MjgwOSAyLjY2NjkgOC43NjU0NSAyLjU3NjcgOS4xNDMyOSAyLjU3NjdDOS41MTk3MSAyLjU3NjcgOS44NTU2NSAyLjY2NjkgMTAuMTUxMSAyLjg0NzNDMTAuNDQ4IDMuMDI2MjggMTAuNjgxNiAzLjI4MzM4IDEwLjg1MjEgMy42MTg2MUMxMS4wMjI1IDMuOTUyNDEgMTEuMTA3OCA0LjM1MjI3IDExLjEwNzggNC44MTgxOFpNMTAuNDU1OCA0LjgxODE4QzEwLjQ1NTggNC40NjMwNyAxMC4zOTgzIDQuMTY0MDYgMTAuMjgzMiAzLjkyMTE2QzEwLjE2OTYgMy42NzY4NSAxMC4wMTMzIDMuNDkyMTkgOS44MTQ0NSAzLjM2NzE5QzkuNjE3MDEgMy4yNDA3NyA5LjM5MzI5IDMuMTc3NTYgOS4xNDMyOSAzLjE3NzU2QzguODkxODcgMy4xNzc1NiA4LjY2NzQ0IDMuMjQwNzcgOC40Njk5OSAzLjM2NzE5QzguMjcyNTUgMy40OTIxOSA4LjExNjMgMy42NzY4NSA4LjAwMTI0IDMuOTIxMTZDNy44ODc2MSA0LjE2NDA2IDcuODMwNzkgNC40NjMwNyA3LjgzMDc5IDQuODE4MThDNy44MzA3OSA1LjE3MzMgNy44ODc2MSA1LjQ3MzAxIDguMDAxMjQgNS43MTczM0M4LjExNjMgNS45NjAyMyA4LjI3MjU1IDYuMTQ0ODkgOC40Njk5OSA2LjI3MTMxQzguNjY3NDQgNi4zOTYzMSA4Ljg5MTg3IDYuNDU4ODEgOS4xNDMyOSA2LjQ1ODgxQzkuMzkzMjkgNi40NTg4MSA5LjYxNzAxIDYuMzk2MzEgOS44MTQ0NSA2LjI3MTMxQzEwLjAxMzMgNi4xNDQ4OSAxMC4xNjk2IDUuOTYwMjMgMTAuMjgzMiA1LjcxNzMzQzEwLjM5ODMgNS40NzMwMSAxMC40NTU4IDUuMTczMyAxMC40NTU4IDQuODE4MThaTTE0LjMwODYgMy43ODI2N0MxNC4yODU5IDMuNTgwOTcgMTQuMTkyMSAzLjQyNDcyIDE0LjAyNzMgMy4zMTM5MkMxMy44NjI2IDMuMjAxNyAxMy42NTUyIDMuMTQ1NiAxMy40MDUyIDMuMTQ1NkMxMy4yMjYyIDMuMTQ1NiAxMy4wNzE0IDMuMTc0MDEgMTIuOTQwNyAzLjIzMDgyQzEyLjgxIDMuMjg2MjIgMTIuNzA4NSAzLjM2MjkzIDEyLjYzNiAzLjQ2MDk0QzEyLjU2NSAzLjU1NzUzIDEyLjUyOTUgMy42Njc2MSAxMi41Mjk1IDMuNzkxMTlDMTIuNTI5NSAzLjg5NDg5IDEyLjU1MzYgMy45ODQzNyAxMi42MDE5IDQuMDU5NjZDMTIuNjUxNiA0LjEzNDk0IDEyLjcxNjMgNC4xOTgxNSAxMi43OTU4IDQuMjQ5MjlDMTIuODc2OCA0LjI5OTAxIDEyLjk2MzQgNC4zNDA5MSAxMy4wNTU4IDQuMzc1QzEzLjE0ODEgNC40MDc2NyAxMy4yMzY5IDQuNDM0NjYgMTMuMzIyMSA0LjQ1NTk3TDEzLjc0ODIgNC41NjY3NkMxMy44ODc0IDQuNjAwODUgMTQuMDMwMiA0LjY0NzAyIDE0LjE3NjUgNC43MDUyNkMxNC4zMjI4IDQuNzYzNDkgMTQuNDU4NSA0Ljg0MDIgMTQuNTgzNSA0LjkzNTM3QzE0LjcwODUgNS4wMzA1NCAxNC44MDkzIDUuMTQ4NDQgMTQuODg2IDUuMjg5MDZDMTQuOTY0MSA1LjQyOTY5IDE1LjAwMzIgNS41OTgwMSAxNS4wMDMyIDUuNzk0MDNDMTUuMDAzMiA2LjA0MTE5IDE0LjkzOTMgNi4yNjA2NSAxNC44MTE0IDYuNDUyNDFDMTQuNjg1IDYuNjQ0MTggMTQuNTAxMSA2Ljc5NTQ1IDE0LjI1OTYgNi45MDYyNUMxNC4wMTk1IDcuMDE3MDUgMTMuNzI5IDcuMDcyNDQgMTMuMzg4MSA3LjA3MjQ0QzEzLjA2MTQgNy4wNzI0NCAxMi43Nzg4IDcuMDIwNiAxMi41NDAxIDYuOTE2OUMxMi4zMDE1IDYuODEzMjEgMTIuMTE0NyA2LjY2NjE5IDExLjk3OTggNi40NzU4NUMxMS44NDQ4IDYuMjg0MDkgMTEuNzcwMiA2LjA1NjgyIDExLjc1NiA1Ljc5NDAzSDEyLjQxNjVDMTIuNDI5MyA1Ljk1MTcgMTIuNDgwNSA2LjA4MzEgMTIuNTcgNi4xODgyMUMxMi42NjA5IDYuMjkxOSAxMi43NzY2IDYuMzY5MzIgMTIuOTE3MyA2LjQyMDQ1QzEzLjA1OTMgNi40NzAxNyAxMy4yMTQ4IDYuNDk1MDMgMTMuMzgzOSA2LjQ5NTAzQzEzLjU3IDYuNDk1MDMgMTMuNzM1NCA2LjQ2NTkxIDEzLjg4MDMgNi40MDc2N0MxNC4wMjY2IDYuMzQ4MDEgMTQuMTQxNyA2LjI2NTYyIDE0LjIyNTUgNi4xNjA1MUMxNC4zMDkzIDYuMDUzOTggMTQuMzUxMiA1LjkyOTY5IDE0LjM1MTIgNS43ODc2NEMxNC4zNTEyIDUuNjU4MzggMTQuMzE0MyA1LjU1MjU2IDE0LjI0MDQgNS40NzAxN0MxNC4xNjggNS4zODc3OCAxNC4wNjkyIDUuMzE5NiAxMy45NDQyIDUuMjY1NjJDMTMuODIwNyA1LjIxMTY1IDEzLjY4MDggNS4xNjQwNiAxMy41MjQ1IDUuMTIyODdMMTMuMDA4OSA0Ljk4MjI0QzEyLjY1OTQgNC44ODcwNyAxMi4zODI1IDQuNzQ3MTYgMTIuMTc3OSA0LjU2MjVDMTEuOTc0OCA0LjM3Nzg0IDExLjg3MzIgNC4xMzM1MiAxMS44NzMyIDMuODI5NTVDMTEuODczMiAzLjU3ODEyIDExLjk0MTQgMy4zNTg2NiAxMi4wNzc4IDMuMTcxMTZDMTIuMjE0MSAyLjk4MzY2IDEyLjM5ODggMi44MzgwNyAxMi42MzE3IDIuNzM0MzhDMTIuODY0NyAyLjYyOTI2IDEzLjEyNzUgMi41NzY3IDEzLjQyMDEgMi41NzY3QzEzLjcxNTYgMi41NzY3IDEzLjk3NjIgMi42Mjg1NSAxNC4yMDIxIDIuNzMyMjRDMTQuNDI5MyAyLjgzNTk0IDE0LjYwODMgMi45Nzg2OSAxNC43MzkgMy4xNjA1MUMxNC44Njk3IDMuMzQwOTEgMTQuOTM3OSAzLjU0ODMgMTQuOTQzNSAzLjc4MjY3SDE0LjMwODZaTTE1LjU3NDggMy4yMDMxMlYyLjYzNjM2SDE4Ljk1MTlWMy4yMDMxMkgxNy41OTA0VjdIMTYuOTM0MVYzLjIwMzEySDE1LjU3NDhaTTIxLjA3MDggMy4yMDMxMlYyLjYzNjM2SDI0LjQ0OFYzLjIwMzEySDIzLjA4NjVWN0gyMi40MzAyVjMuMjAzMTJIMjEuMDcwOFpNMjQuODU3NiAyLjYzNjM2SDI1LjYwNTVMMjYuNzQ1NCA0LjYyMDAzSDI2Ljc5MjNMMjcuOTMyMiAyLjYzNjM2SDI4LjY4TDI3LjA5NjkgNS4yODY5M1Y3SDI2LjQ0MDdWNS4yODY5M0wyNC44NTc2IDIuNjM2MzZaTTI5LjMwMTcgN1YyLjYzNjM2SDMwLjg1NzFDMzEuMTk2NiAyLjYzNjM2IDMxLjQ3NzggMi42OTgxNSAzMS43MDA4IDIuODIxNzNDMzEuOTIzOCAyLjk0NTMxIDMyLjA5MDcgMy4xMTQzNSAzMi4yMDE1IDMuMzI4ODRDMzIuMzEyMyAzLjU0MTkgMzIuMzY3NyAzLjc4MTk2IDMyLjM2NzcgNC4wNDkwMUMzMi4zNjc3IDQuMzE3NDcgMzIuMzExNiA0LjU1ODk1IDMyLjE5OTQgNC43NzM0NEMzMi4wODg2IDQuOTg2NTEgMzEuOTIxIDUuMTU1NTQgMzEuNjk2NiA1LjI4MDU0QzMxLjQ3MzUgNS40MDQxMiAzMS4xOTMgNS40NjU5MSAzMC44NTQ5IDUuNDY1OTFIMjkuNzg1M1Y0LjkwNzY3SDMwLjc5NTNDMzEuMDA5OCA0LjkwNzY3IDMxLjE4MzggNC44NzA3NCAzMS4zMTczIDQuNzk2ODhDMzEuNDUwOCA0LjcyMTU5IDMxLjU0ODggNC42MTkzMiAzMS42MTEzIDQuNDkwMDZDMzEuNjczOCA0LjM2MDggMzEuNzA1MSA0LjIxMzc4IDMxLjcwNTEgNC4wNDkwMUMzMS43MDUxIDMuODg0MjMgMzEuNjczOCAzLjczNzkzIDMxLjYxMTMgMy42MTAwOUMzMS41NDg4IDMuNDgyMjQgMzEuNDUwMSAzLjM4MjEgMzEuMzE1MiAzLjMwOTY2QzMxLjE4MTYgMy4yMzcyMiAzMS4wMDU1IDMuMjAwOTkgMzAuNzg2OCAzLjIwMDk5SDI5Ljk2VjdIMjkuMzAxN1pNMzMuMTM5NiA3VjIuNjM2MzZIMzUuODc1NFYzLjIwMzEySDMzLjc5NzlWNC41MzI2N0gzNS43MzI2VjUuMDk3M0gzMy43OTc5VjYuNDMzMjRIMzUuOTAwOVY3SDMzLjEzOTZaTTM5LjEzNDggMy43ODI2N0MzOS4xMTIgMy41ODA5NyAzOS4wMTgzIDMuNDI0NzIgMzguODUzNSAzLjMxMzkyQzM4LjY4ODcgMy4yMDE3IDM4LjQ4MTQgMy4xNDU2IDM4LjIzMTQgMy4xNDU2QzM4LjA1MjQgMy4xNDU2IDM3Ljg5NzUgMy4xNzQwMSAzNy43NjY5IDMuMjMwODJDMzcuNjM2MiAzLjI4NjIyIDM3LjUzNDYgMy4zNjI5MyAzNy40NjIyIDMuNDYwOTRDMzcuMzkxMiAzLjU1NzUzIDM3LjM1NTYgMy42Njc2MSAzNy4zNTU2IDMuNzkxMTlDMzcuMzU1NiAzLjg5NDg5IDM3LjM3OTggMy45ODQzNyAzNy40MjgxIDQuMDU5NjZDMzcuNDc3OCA0LjEzNDk0IDM3LjU0MjQgNC4xOTgxNSAzNy42MjIgNC4yNDkyOUMzNy43MDI5IDQuMjk5MDEgMzcuNzg5NiA0LjM0MDkxIDM3Ljg4MTkgNC4zNzVDMzcuOTc0MyA0LjQwNzY3IDM4LjA2MyA0LjQzNDY2IDM4LjE0ODMgNC40NTU5N0wzOC41NzQ0IDQuNTY2NzZDMzguNzEzNiA0LjYwMDg1IDM4Ljg1NjQgNC42NDcwMiAzOS4wMDI3IDQuNzA1MjZDMzkuMTQ5IDQuNzYzNDkgMzkuMjg0NiA0Ljg0MDIgMzkuNDA5NiA0LjkzNTM3QzM5LjUzNDYgNS4wMzA1NCAzOS42MzU1IDUuMTQ4NDQgMzkuNzEyMiA1LjI4OTA2QzM5Ljc5MDMgNS40Mjk2OSAzOS44Mjk0IDUuNTk4MDEgMzkuODI5NCA1Ljc5NDAzQzM5LjgyOTQgNi4wNDExOSAzOS43NjU0IDYuMjYwNjUgMzkuNjM3NiA2LjQ1MjQxQzM5LjUxMTIgNi42NDQxOCAzOS4zMjcyIDYuNzk1NDUgMzkuMDg1OCA2LjkwNjI1QzM4Ljg0NTcgNy4wMTcwNSAzOC41NTUyIDcuMDcyNDQgMzguMjE0MyA3LjA3MjQ0QzM3Ljg4NzYgNy4wNzI0NCAzNy42MDQ5IDcuMDIwNiAzNy4zNjYzIDYuOTE2OUMzNy4xMjc3IDYuODEzMjEgMzYuOTQwOSA2LjY2NjE5IDM2LjgwNTkgNi40NzU4NUMzNi42NzEgNi4yODQwOSAzNi41OTY0IDYuMDU2ODIgMzYuNTgyMiA1Ljc5NDAzSDM3LjI0MjdDMzcuMjU1NSA1Ljk1MTcgMzcuMzA2NiA2LjA4MzEgMzcuMzk2MSA2LjE4ODIxQzM3LjQ4NyA2LjI5MTkgMzcuNjAyOCA2LjM2OTMyIDM3Ljc0MzQgNi40MjA0NUMzNy44ODU1IDYuNDcwMTcgMzguMDQxIDYuNDk1MDMgMzguMjEgNi40OTUwM0MzOC4zOTYxIDYuNDk1MDMgMzguNTYxNiA2LjQ2NTkxIDM4LjcwNjUgNi40MDc2N0MzOC44NTI4IDYuMzQ4MDEgMzguOTY3OSA2LjI2NTYyIDM5LjA1MTcgNi4xNjA1MUMzOS4xMzU1IDYuMDUzOTggMzkuMTc3NCA1LjkyOTY5IDM5LjE3NzQgNS43ODc2NEMzOS4xNzc0IDUuNjU4MzggMzkuMTQwNCA1LjU1MjU2IDM5LjA2NjYgNS40NzAxN0MzOC45OTQxIDUuMzg3NzggMzguODk1NCA1LjMxOTYgMzguNzcwNCA1LjI2NTYyQzM4LjY0NjggNS4yMTE2NSAzOC41MDY5IDUuMTY0MDYgMzguMzUwNyA1LjEyMjg3TDM3LjgzNSA0Ljk4MjI0QzM3LjQ4NTYgNC44ODcwNyAzNy4yMDg2IDQuNzQ3MTYgMzcuMDA0MSA0LjU2MjVDMzYuODAxIDQuMzc3ODQgMzYuNjk5NCA0LjEzMzUyIDM2LjY5OTQgMy44Mjk1NUMzNi42OTk0IDMuNTc4MTIgMzYuNzY3NiAzLjM1ODY2IDM2LjkwMzkgMy4xNzExNkMzNy4wNDAzIDIuOTgzNjYgMzcuMjI1IDIuODM4MDcgMzcuNDU3OSAyLjczNDM4QzM3LjY5MDkgMi42MjkyNiAzNy45NTM3IDIuNTc2NyAzOC4yNDYzIDIuNTc2N0MzOC41NDE3IDIuNTc2NyAzOC44MDI0IDIuNjI4NTUgMzkuMDI4MiAyLjczMjI0QzM5LjI1NTUgMi44MzU5NCAzOS40MzQ1IDIuOTc4NjkgMzkuNTY1MiAzLjE2MDUxQzM5LjY5NTggMy4zNDA5MSAzOS43NjQgMy41NDgzIDM5Ljc2OTcgMy43ODI2N0gzOS4xMzQ4WiIgZmlsbD0iI0E4QUFBRCIvPgo8L3N2Zz4K");
			}
			
			.separator-croco--cct-before {
				height: 10px !important;
			}
			
			.separator-croco--cct-before::before {
				content: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTkiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAxOSAxMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3QgeD0iMC41IiB5PSIwLjUiIHdpZHRoPSIxOCIgaGVpZ2h0PSI5IiByeD0iMi41IiBmaWxsPSIjMUQyMzI3IiBzdHJva2U9IiM0ODRBNEMiLz4KPHBhdGggZD0iTTcuMTAzNjkgNC4wNTU0SDYuNDM4OTJDNi40MTMzNSAzLjkxMzM1IDYuMzY1NzcgMy43ODgzNSA2LjI5NjE2IDMuNjgwNEM2LjIyNjU2IDMuNTcyNDQgNi4xNDEzNCAzLjQ4MDgyIDYuMDQwNDggMy40MDU1NEM1LjkzOTYzIDMuMzMwMjYgNS44MjY3IDMuMjczNDQgNS43MDE3IDMuMjM1MDlDNS41NzgxMyAzLjE5NjczIDUuNDQ2NzMgMy4xNzc1NiA1LjMwNzUzIDMuMTc3NTZDNS4wNTYxMSAzLjE3NzU2IDQuODMwOTcgMy4yNDA3NyA0LjYzMjEgMy4zNjcxOUM0LjQzNDY2IDMuNDkzNjEgNC4yNzg0MSAzLjY3ODk4IDQuMTYzMzUgMy45MjMzQzQuMDQ5NzIgNC4xNjc2MSAzLjk5MjkgNC40NjU5MSAzLjk5MjkgNC44MTgxOEMzLjk5MjkgNS4xNzMzIDQuMDQ5NzIgNS40NzMwMSA0LjE2MzM1IDUuNzE3MzNDNC4yNzg0MSA1Ljk2MTY1IDQuNDM1MzcgNi4xNDYzMSA0LjYzNDIzIDYuMjcxMzFDNC44MzMxIDYuMzk2MzEgNS4wNTY4MiA2LjQ1ODgxIDUuMzA1NCA2LjQ1ODgxQzUuNDQzMTggNi40NTg4MSA1LjU3Mzg2IDYuNDQwMzQgNS42OTc0NCA2LjQwMzQxQzUuODIyNDQgNi4zNjUwNiA1LjkzNTM3IDYuMzA4OTUgNi4wMzYyMiA2LjIzNTA5QzYuMTM3MDcgNi4xNjEyMiA2LjIyMjMgNi4wNzEwMiA2LjI5MTkgNS45NjQ0OUM2LjM2MjkzIDUuODU2NTMgNi40MTE5MyA1LjczMjk1IDYuNDM4OTIgNS41OTM3NUw3LjEwMzY5IDUuNTk1ODhDNy4wNjgxOCA1LjgxMDM3IDYuOTk5MjkgNi4wMDc4MSA2Ljg5NzAyIDYuMTg4MjFDNi43OTYxNiA2LjM2NzE5IDYuNjY2MTkgNi41MjIwMiA2LjUwNzEgNi42NTI3QzYuMzQ5NDMgNi43ODE5NiA2LjE2OTAzIDYuODgyMSA1Ljk2NTkxIDYuOTUzMTJDNS43NjI3OCA3LjAyNDE1IDUuNTQxMTkgNy4wNTk2NiA1LjMwMTE0IDcuMDU5NjZDNC45MjMzIDcuMDU5NjYgNC41ODY2NSA2Ljk3MDE3IDQuMjkxMTkgNi43OTExOUMzLjk5NTc0IDYuNjEwOCAzLjc2Mjc4IDYuMzUyOTggMy41OTIzMyA2LjAxNzc2QzMuNDIzMyA1LjY4MjUzIDMuMzM4NzggNS4yODI2NyAzLjMzODc4IDQuODE4MThDMy4zMzg3OCA0LjM1MjI3IDMuNDI0MDEgMy45NTI0MSAzLjU5NDQ2IDMuNjE4NjFDMy43NjQ5MSAzLjI4MzM4IDMuOTk3ODcgMy4wMjYyOCA0LjI5MzMyIDIuODQ3M0M0LjU4ODc4IDIuNjY2OSA0LjkyNDcyIDIuNTc2NyA1LjMwMTE0IDIuNTc2N0M1LjUzMjY3IDIuNTc2NyA1Ljc0ODU4IDIuNjEwMDkgNS45NDg4NiAyLjY3Njg1QzYuMTUwNTcgMi43NDIxOSA2LjMzMTY4IDIuODM4NzggNi40OTIxOSAyLjk2NjYyQzYuNjUyNyAzLjA5MzA0IDYuNzg1NTEgMy4yNDc4NyA2Ljg5MDYyIDMuNDMxMTFDNi45OTU3NCAzLjYxMjkzIDcuMDY2NzYgMy44MjEwMiA3LjEwMzY5IDQuMDU1NFpNMTEuNTE1OCA0LjA1NTRIMTAuODUxQzEwLjgyNTUgMy45MTMzNSAxMC43Nzc5IDMuNzg4MzUgMTAuNzA4MyAzLjY4MDRDMTAuNjM4NyAzLjU3MjQ0IDEwLjU1MzQgMy40ODA4MiAxMC40NTI2IDMuNDA1NTRDMTAuMzUxNyAzLjMzMDI2IDEwLjIzODggMy4yNzM0NCAxMC4xMTM4IDMuMjM1MDlDOS45OTAyMyAzLjE5NjczIDkuODU4ODQgMy4xNzc1NiA5LjcxOTY0IDMuMTc3NTZDOS40NjgyMiAzLjE3NzU2IDkuMjQzMDggMy4yNDA3NyA5LjA0NDIxIDMuMzY3MTlDOC44NDY3NyAzLjQ5MzYxIDguNjkwNTIgMy42Nzg5OCA4LjU3NTQ2IDMuOTIzM0M4LjQ2MTgzIDQuMTY3NjEgOC40MDUwMSA0LjQ2NTkxIDguNDA1MDEgNC44MTgxOEM4LjQwNTAxIDUuMTczMyA4LjQ2MTgzIDUuNDczMDEgOC41NzU0NiA1LjcxNzMzQzguNjkwNTIgNS45NjE2NSA4Ljg0NzQ4IDYuMTQ2MzEgOS4wNDYzNCA2LjI3MTMxQzkuMjQ1MjEgNi4zOTYzMSA5LjQ2ODkzIDYuNDU4ODEgOS43MTc1MSA2LjQ1ODgxQzkuODU1MjkgNi40NTg4MSA5Ljk4NTk3IDYuNDQwMzQgMTAuMTA5NiA2LjQwMzQxQzEwLjIzNDYgNi4zNjUwNiAxMC4zNDc1IDYuMzA4OTUgMTAuNDQ4MyA2LjIzNTA5QzEwLjU0OTIgNi4xNjEyMiAxMC42MzQ0IDYuMDcxMDIgMTAuNzA0IDUuOTY0NDlDMTAuNzc1IDUuODU2NTMgMTAuODI0IDUuNzMyOTUgMTAuODUxIDUuNTkzNzVMMTEuNTE1OCA1LjU5NTg4QzExLjQ4MDMgNS44MTAzNyAxMS40MTE0IDYuMDA3ODEgMTEuMzA5MSA2LjE4ODIxQzExLjIwODMgNi4zNjcxOSAxMS4wNzgzIDYuNTIyMDIgMTAuOTE5MiA2LjY1MjdDMTAuNzYxNSA2Ljc4MTk2IDEwLjU4MTEgNi44ODIxIDEwLjM3OCA2Ljk1MzEyQzEwLjE3NDkgNy4wMjQxNSA5Ljk1MzMgNy4wNTk2NiA5LjcxMzI1IDcuMDU5NjZDOS4zMzU0IDcuMDU5NjYgOC45OTg3NiA2Ljk3MDE3IDguNzAzMyA2Ljc5MTE5QzguNDA3ODUgNi42MTA4IDguMTc0ODkgNi4zNTI5OCA4LjAwNDQ0IDYuMDE3NzZDNy44MzU0IDUuNjgyNTMgNy43NTA4OSA1LjI4MjY3IDcuNzUwODkgNC44MTgxOEM3Ljc1MDg5IDQuMzUyMjcgNy44MzYxMiAzLjk1MjQxIDguMDA2NTcgMy42MTg2MUM4LjE3NzAyIDMuMjgzMzggOC40MDk5OCAzLjAyNjI4IDguNzA1NDMgMi44NDczQzkuMDAwODkgMi42NjY5IDkuMzM2ODMgMi41NzY3IDkuNzEzMjUgMi41NzY3QzkuOTQ0NzggMi41NzY3IDEwLjE2MDcgMi42MTAwOSAxMC4zNjEgMi42NzY4NUMxMC41NjI3IDIuNzQyMTkgMTAuNzQzOCAyLjgzODc4IDEwLjkwNDMgMi45NjY2MkMxMS4wNjQ4IDMuMDkzMDQgMTEuMTk3NiAzLjI0Nzg3IDExLjMwMjcgMy40MzExMUMxMS40MDc4IDMuNjEyOTMgMTEuNDc4OSAzLjgyMTAyIDExLjUxNTggNC4wNTU0Wk0xMi4wODg0IDMuMjAzMTJWMi42MzYzNkgxNS40NjU2VjMuMjAzMTJIMTQuMTA0VjdIMTMuNDQ3OFYzLjIwMzEySDEyLjA4ODRaIiBmaWxsPSIjQThBQUFEIi8+Cjwvc3ZnPgo=");
			}
			
			.separator-croco--options-pages-before {
				height: 10px !important;
			}
			
			.separator-croco--options-pages-before::before {
				content: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTQiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCA1NCAxMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3QgeD0iMC41IiB5PSIwLjUiIHdpZHRoPSI1MyIgaGVpZ2h0PSI5IiByeD0iMi41IiBmaWxsPSIjMUQyMzI3IiBzdHJva2U9IiM0ODRBNEMiLz4KPHBhdGggZD0iTTcuMjY5ODkgNC44MTgxOEM3LjI2OTg5IDUuMjg0MDkgNy4xODQ2NiA1LjY4NDY2IDcuMDE0MiA2LjAxOTg5QzYuODQzNzUgNi4zNTM2OSA2LjYxMDA5IDYuNjEwOCA2LjMxMzIxIDYuNzkxMTlDNi4wMTc3NiA2Ljk3MDE3IDUuNjgxODIgNy4wNTk2NiA1LjMwNTQgNy4wNTk2NkM0LjkyNzU2IDcuMDU5NjYgNC41OTAyIDYuOTcwMTcgNC4yOTMzMiA2Ljc5MTE5QzMuOTk3ODcgNi42MTA4IDMuNzY0OTEgNi4zNTI5OCAzLjU5NDQ2IDYuMDE3NzZDMy40MjQwMSA1LjY4MjUzIDMuMzM4NzggNS4yODI2NyAzLjMzODc4IDQuODE4MThDMy4zMzg3OCA0LjM1MjI3IDMuNDI0MDEgMy45NTI0MSAzLjU5NDQ2IDMuNjE4NjFDMy43NjQ5MSAzLjI4MzM4IDMuOTk3ODcgMy4wMjYyOCA0LjI5MzMyIDIuODQ3M0M0LjU5MDIgMi42NjY5IDQuOTI3NTYgMi41NzY3IDUuMzA1NCAyLjU3NjdDNS42ODE4MiAyLjU3NjcgNi4wMTc3NiAyLjY2NjkgNi4zMTMyMSAyLjg0NzNDNi42MTAwOSAzLjAyNjI4IDYuODQzNzUgMy4yODMzOCA3LjAxNDIgMy42MTg2MUM3LjE4NDY2IDMuOTUyNDEgNy4yNjk4OSA0LjM1MjI3IDcuMjY5ODkgNC44MTgxOFpNNi42MTc5IDQuODE4MThDNi42MTc5IDQuNDYzMDcgNi41NjAzNyA0LjE2NDA2IDYuNDQ1MzEgMy45MjExNkM2LjMzMTY4IDMuNjc2ODUgNi4xNzU0MyAzLjQ5MjE5IDUuOTc2NTYgMy4zNjcxOUM1Ljc3OTEyIDMuMjQwNzcgNS41NTU0IDMuMTc3NTYgNS4zMDU0IDMuMTc3NTZDNS4wNTM5OCAzLjE3NzU2IDQuODI5NTUgMy4yNDA3NyA0LjYzMjEgMy4zNjcxOUM0LjQzNDY2IDMuNDkyMTkgNC4yNzg0MSAzLjY3Njg1IDQuMTYzMzUgMy45MjExNkM0LjA0OTcyIDQuMTY0MDYgMy45OTI5IDQuNDYzMDcgMy45OTI5IDQuODE4MThDMy45OTI5IDUuMTczMyA0LjA0OTcyIDUuNDczMDEgNC4xNjMzNSA1LjcxNzMzQzQuMjc4NDEgNS45NjAyMyA0LjQzNDY2IDYuMTQ0ODkgNC42MzIxIDYuMjcxMzFDNC44Mjk1NSA2LjM5NjMxIDUuMDUzOTggNi40NTg4MSA1LjMwNTQgNi40NTg4MUM1LjU1NTQgNi40NTg4MSA1Ljc3OTEyIDYuMzk2MzEgNS45NzY1NiA2LjI3MTMxQzYuMTc1NDMgNi4xNDQ4OSA2LjMzMTY4IDUuOTYwMjMgNi40NDUzMSA1LjcxNzMzQzYuNTYwMzcgNS40NzMwMSA2LjYxNzkgNS4xNzMzIDYuNjE3OSA0LjgxODE4Wk04LjA5MDczIDdWMi42MzYzNkg5LjY0NjEzQzkuOTg1NjIgMi42MzYzNiAxMC4yNjY5IDIuNjk4MTUgMTAuNDg5OSAyLjgyMTczQzEwLjcxMjkgMi45NDUzMSAxMC44Nzk4IDMuMTE0MzUgMTAuOTkwNiAzLjMyODg0QzExLjEwMTQgMy41NDE5IDExLjE1NjggMy43ODE5NiAxMS4xNTY4IDQuMDQ5MDFDMTEuMTU2OCA0LjMxNzQ3IDExLjEwMDcgNC41NTg5NSAxMC45ODg1IDQuNzczNDRDMTAuODc3NyA0Ljk4NjUxIDEwLjcxIDUuMTU1NTQgMTAuNDg1NiA1LjI4MDU0QzEwLjI2MjYgNS40MDQxMiA5Ljk4MjA3IDUuNDY1OTEgOS42NDQgNS40NjU5MUg4LjU3NDRWNC45MDc2N0g5LjU4NDM0QzkuNzk4ODMgNC45MDc2NyA5Ljk3MjgzIDQuODcwNzQgMTAuMTA2NCA0Ljc5Njg4QzEwLjIzOTkgNC43MjE1OSAxMC4zMzc5IDQuNjE5MzIgMTAuNDAwNCA0LjQ5MDA2QzEwLjQ2MjkgNC4zNjA4IDEwLjQ5NDEgNC4yMTM3OCAxMC40OTQxIDQuMDQ5MDFDMTAuNDk0MSAzLjg4NDIzIDEwLjQ2MjkgMy43Mzc5MyAxMC40MDA0IDMuNjEwMDlDMTAuMzM3OSAzLjQ4MjI0IDEwLjIzOTIgMy4zODIxIDEwLjEwNDIgMy4zMDk2NkM5Ljk3MDcgMy4yMzcyMiA5Ljc5NDU3IDMuMjAwOTkgOS41NzU4MiAzLjIwMDk5SDguNzQ5MTFWN0g4LjA5MDczWk0xMS43MTM0IDMuMjAzMTJWMi42MzYzNkgxNS4wOTA2VjMuMjAzMTJIMTMuNzI5VjdIMTMuMDcyOFYzLjIwMzEySDExLjcxMzRaTTE2LjQ5NTIgMi42MzYzNlY3SDE1LjgzNjhWMi42MzYzNkgxNi40OTUyWk0yMS4yNDQ1IDQuODE4MThDMjEuMjQ0NSA1LjI4NDA5IDIxLjE1OTMgNS42ODQ2NiAyMC45ODg4IDYuMDE5ODlDMjAuODE4NCA2LjM1MzY5IDIwLjU4NDcgNi42MTA4IDIwLjI4NzggNi43OTExOUMxOS45OTI0IDYuOTcwMTcgMTkuNjU2NCA3LjA1OTY2IDE5LjI4IDcuMDU5NjZDMTguOTAyMiA3LjA1OTY2IDE4LjU2NDggNi45NzAxNyAxOC4yNjc5IDYuNzkxMTlDMTcuOTcyNSA2LjYxMDggMTcuNzM5NSA2LjM1Mjk4IDE3LjU2OTEgNi4wMTc3NkMxNy4zOTg2IDUuNjgyNTMgMTcuMzEzNCA1LjI4MjY3IDE3LjMxMzQgNC44MTgxOEMxNy4zMTM0IDQuMzUyMjcgMTcuMzk4NiAzLjk1MjQxIDE3LjU2OTEgMy42MTg2MUMxNy43Mzk1IDMuMjgzMzggMTcuOTcyNSAzLjAyNjI4IDE4LjI2NzkgMi44NDczQzE4LjU2NDggMi42NjY5IDE4LjkwMjIgMi41NzY3IDE5LjI4IDIuNTc2N0MxOS42NTY0IDIuNTc2NyAxOS45OTI0IDIuNjY2OSAyMC4yODc4IDIuODQ3M0MyMC41ODQ3IDMuMDI2MjggMjAuODE4NCAzLjI4MzM4IDIwLjk4ODggMy42MTg2MUMyMS4xNTkzIDMuOTUyNDEgMjEuMjQ0NSA0LjM1MjI3IDIxLjI0NDUgNC44MTgxOFpNMjAuNTkyNSA0LjgxODE4QzIwLjU5MjUgNC40NjMwNyAyMC41MzUgNC4xNjQwNiAyMC40MTk5IDMuOTIxMTZDMjAuMzA2MyAzLjY3Njg1IDIwLjE1IDMuNDkyMTkgMTkuOTUxMiAzLjM2NzE5QzE5Ljc1MzcgMy4yNDA3NyAxOS41MyAzLjE3NzU2IDE5LjI4IDMuMTc3NTZDMTkuMDI4NiAzLjE3NzU2IDE4LjgwNDIgMy4yNDA3NyAxOC42MDY3IDMuMzY3MTlDMTguNDA5MyAzLjQ5MjE5IDE4LjI1MyAzLjY3Njg1IDE4LjEzOCAzLjkyMTE2QzE4LjAyNDMgNC4xNjQwNiAxNy45Njc1IDQuNDYzMDcgMTcuOTY3NSA0LjgxODE4QzE3Ljk2NzUgNS4xNzMzIDE4LjAyNDMgNS40NzMwMSAxOC4xMzggNS43MTczM0MxOC4yNTMgNS45NjAyMyAxOC40MDkzIDYuMTQ0ODkgMTguNjA2NyA2LjI3MTMxQzE4LjgwNDIgNi4zOTYzMSAxOS4wMjg2IDYuNDU4ODEgMTkuMjggNi40NTg4MUMxOS41MyA2LjQ1ODgxIDE5Ljc1MzcgNi4zOTYzMSAxOS45NTEyIDYuMjcxMzFDMjAuMTUgNi4xNDQ4OSAyMC4zMDYzIDUuOTYwMjMgMjAuNDE5OSA1LjcxNzMzQzIwLjUzNSA1LjQ3MzAxIDIwLjU5MjUgNS4xNzMzIDIwLjU5MjUgNC44MTgxOFpNMjUuNTg3NCAyLjYzNjM2VjdIMjQuOTgyMkwyMi43NjQyIDMuNzk5NzJIMjIuNzIzN1Y3SDIyLjA2NTNWMi42MzYzNkgyMi42NzQ3TDI0Ljg5NDkgNS44NDA5MUgyNC45MzU0VjIuNjM2MzZIMjUuNTg3NFpNMjguOTI3NyAzLjc4MjY3QzI4LjkwNSAzLjU4MDk3IDI4LjgxMTMgMy40MjQ3MiAyOC42NDY1IDMuMzEzOTJDMjguNDgxNyAzLjIwMTcgMjguMjc0MyAzLjE0NTYgMjguMDI0MyAzLjE0NTZDMjcuODQ1MyAzLjE0NTYgMjcuNjkwNSAzLjE3NDAxIDI3LjU1OTggMy4yMzA4MkMyNy40MjkyIDMuMjg2MjIgMjcuMzI3NiAzLjM2MjkzIDI3LjI1NTEgMy40NjA5NEMyNy4xODQxIDMuNTU3NTMgMjcuMTQ4NiAzLjY2NzYxIDI3LjE0ODYgMy43OTExOUMyNy4xNDg2IDMuODk0ODkgMjcuMTcyOCAzLjk4NDM3IDI3LjIyMTEgNC4wNTk2NkMyNy4yNzA4IDQuMTM0OTQgMjcuMzM1NCA0LjE5ODE1IDI3LjQxNSA0LjI0OTI5QzI3LjQ5NTkgNC4yOTkwMSAyNy41ODI2IDQuMzQwOTEgMjcuNjc0OSA0LjM3NUMyNy43NjcyIDQuNDA3NjcgMjcuODU2IDQuNDM0NjYgMjcuOTQxMiA0LjQ1NTk3TDI4LjM2NzQgNC41NjY3NkMyOC41MDY2IDQuNjAwODUgMjguNjQ5MyA0LjY0NzAyIDI4Ljc5NTYgNC43MDUyNkMyOC45NDE5IDQuNzYzNDkgMjkuMDc3NiA0Ljg0MDIgMjkuMjAyNiA0LjkzNTM3QzI5LjMyNzYgNS4wMzA1NCAyOS40Mjg0IDUuMTQ4NDQgMjkuNTA1MSA1LjI4OTA2QzI5LjU4MzMgNS40Mjk2OSAyOS42MjIzIDUuNTk4MDEgMjkuNjIyMyA1Ljc5NDAzQzI5LjYyMjMgNi4wNDExOSAyOS41NTg0IDYuMjYwNjUgMjkuNDMwNiA2LjQ1MjQxQzI5LjMwNDIgNi42NDQxOCAyOS4xMjAyIDYuNzk1NDUgMjguODc4NyA2LjkwNjI1QzI4LjYzODcgNy4wMTcwNSAyOC4zNDgyIDcuMDcyNDQgMjguMDA3MyA3LjA3MjQ0QzI3LjY4MDYgNy4wNzI0NCAyNy4zOTc5IDcuMDIwNiAyNy4xNTkzIDYuOTE2OUMyNi45MjA2IDYuODEzMjEgMjYuNzMzOCA2LjY2NjE5IDI2LjU5ODkgNi40NzU4NUMyNi40NjQgNi4yODQwOSAyNi4zODk0IDYuMDU2ODIgMjYuMzc1MiA1Ljc5NDAzSDI3LjAzNTdDMjcuMDQ4NSA1Ljk1MTcgMjcuMDk5NiA2LjA4MzEgMjcuMTg5MSA2LjE4ODIxQzI3LjI4IDYuMjkxOSAyNy4zOTU4IDYuMzY5MzIgMjcuNTM2NCA2LjQyMDQ1QzI3LjY3ODQgNi40NzAxNyAyNy44MzQgNi40OTUwMyAyOC4wMDMgNi40OTUwM0MyOC4xODkxIDYuNDk1MDMgMjguMzU0NiA2LjQ2NTkxIDI4LjQ5OTUgNi40MDc2N0MyOC42NDU4IDYuMzQ4MDEgMjguNzYwOCA2LjI2NTYyIDI4Ljg0NDYgNi4xNjA1MUMyOC45Mjg0IDYuMDUzOTggMjguOTcwMyA1LjkyOTY5IDI4Ljk3MDMgNS43ODc2NEMyOC45NzAzIDUuNjU4MzggMjguOTMzNCA1LjU1MjU2IDI4Ljg1OTYgNS40NzAxN0MyOC43ODcxIDUuMzg3NzggMjguNjg4NCA1LjMxOTYgMjguNTYzNCA1LjI2NTYyQzI4LjQzOTggNS4yMTE2NSAyOC4yOTk5IDUuMTY0MDYgMjguMTQzNiA1LjEyMjg3TDI3LjYyOCA0Ljk4MjI0QzI3LjI3ODYgNC44ODcwNyAyNy4wMDE2IDQuNzQ3MTYgMjYuNzk3MSA0LjU2MjVDMjYuNTkzOSA0LjM3Nzg0IDI2LjQ5MjQgNC4xMzM1MiAyNi40OTI0IDMuODI5NTVDMjYuNDkyNCAzLjU3ODEyIDI2LjU2MDUgMy4zNTg2NiAyNi42OTY5IDMuMTcxMTZDMjYuODMzMyAyLjk4MzY2IDI3LjAxNzkgMi44MzgwNyAyNy4yNTA5IDIuNzM0MzhDMjcuNDgzOCAyLjYyOTI2IDI3Ljc0NjYgMi41NzY3IDI4LjAzOTIgMi41NzY3QzI4LjMzNDcgMi41NzY3IDI4LjU5NTMgMi42Mjg1NSAyOC44MjEyIDIuNzMyMjRDMjkuMDQ4NSAyLjgzNTk0IDI5LjIyNzUgMi45Nzg2OSAyOS4zNTgxIDMuMTYwNTFDMjkuNDg4OCAzLjM0MDkxIDI5LjU1NyAzLjU0ODMgMjkuNTYyNyAzLjc4MjY3SDI4LjkyNzdaTTMxLjk5NyA3VjIuNjM2MzZIMzMuNTUyNEMzMy44OTE5IDIuNjM2MzYgMzQuMTczMSAyLjY5ODE1IDM0LjM5NjEgMi44MjE3M0MzNC42MTkxIDIuOTQ1MzEgMzQuNzg2IDMuMTE0MzUgMzQuODk2OCAzLjMyODg0QzM1LjAwNzYgMy41NDE5IDM1LjA2MyAzLjc4MTk2IDM1LjA2MyA0LjA0OTAxQzM1LjA2MyA0LjMxNzQ3IDM1LjAwNjkgNC41NTg5NSAzNC44OTQ3IDQuNzczNDRDMzQuNzgzOSA0Ljk4NjUxIDM0LjYxNjMgNS4xNTU1NCAzNC4zOTE5IDUuMjgwNTRDMzQuMTY4OSA1LjQwNDEyIDMzLjg4ODMgNS40NjU5MSAzMy41NTAyIDUuNDY1OTFIMzIuNDgwNlY0LjkwNzY3SDMzLjQ5MDZDMzMuNzA1MSA0LjkwNzY3IDMzLjg3OTEgNC44NzA3NCAzNC4wMTI2IDQuNzk2ODhDMzQuMTQ2MSA0LjcyMTU5IDM0LjI0NDEgNC42MTkzMiAzNC4zMDY2IDQuNDkwMDZDMzQuMzY5MSA0LjM2MDggMzQuNDAwNCA0LjIxMzc4IDM0LjQwMDQgNC4wNDkwMUMzNC40MDA0IDMuODg0MjMgMzQuMzY5MSAzLjczNzkzIDM0LjMwNjYgMy42MTAwOUMzNC4yNDQxIDMuNDgyMjQgMzQuMTQ1NCAzLjM4MjEgMzQuMDEwNSAzLjMwOTY2QzMzLjg3NyAzLjIzNzIyIDMzLjcwMDggMy4yMDA5OSAzMy40ODIxIDMuMjAwOTlIMzIuNjU1NFY3SDMxLjk5N1pNMzUuNzQwNiA3SDM1LjA0MTdMMzYuNjEyIDIuNjM2MzZIMzcuMzcyN0wzOC45NDMgN0gzOC4yNDQxTDM3LjAxMDUgMy40Mjg5OEgzNi45NzY0TDM1Ljc0MDYgN1pNMzUuODU3OCA1LjI5MTE5SDM4LjEyNDhWNS44NDUxN0gzNS44NTc4VjUuMjkxMTlaTTQyLjMyMTIgNC4wMTQ5MUM0Mi4yOCAzLjg4NTY1IDQyLjIyNDYgMy43Njk4OSA0Mi4xNTUgMy42Njc2MUM0Mi4wODY4IDMuNTYzOTIgNDIuMDA1MSAzLjQ3NTg1IDQxLjkxIDMuNDAzNDFDNDEuODE0OCAzLjMyOTU1IDQxLjcwNjEgMy4yNzM0NCA0MS41ODQgMy4yMzUwOUM0MS40NjMyIDMuMTk2NzMgNDEuMzMwNCAzLjE3NzU2IDQxLjE4NTUgMy4xNzc1NkM0MC45Mzk4IDMuMTc3NTYgNDAuNzE4MiAzLjI0MDc3IDQwLjUyMDggMy4zNjcxOUM0MC4zMjMzIDMuNDkzNjEgNDAuMTY3MSAzLjY3ODk4IDQwLjA1MiAzLjkyMzNDMzkuOTM4NCA0LjE2NjE5IDM5Ljg4MTYgNC40NjM3OCAzOS44ODE2IDQuODE2MDVDMzkuODgxNiA1LjE2OTc0IDM5LjkzOTEgNS40Njg3NSA0MC4wNTQyIDUuNzEzMDdDNDAuMTY5MiA1Ljk1NzM5IDQwLjMyNjkgNi4xNDI3NiA0MC41MjcyIDYuMjY5MThDNDAuNzI3NSA2LjM5NTYgNDAuOTU1NCA2LjQ1ODgxIDQxLjIxMTEgNi40NTg4MUM0MS40NDgzIDYuNDU4ODEgNDEuNjU1IDYuNDEwNTEgNDEuODMxMSA2LjMxMzkyQzQyLjAwODcgNi4yMTczMyA0Mi4xNDU4IDYuMDgwOTcgNDIuMjQyNCA1LjkwNDgzQzQyLjM0MDQgNS43MjcyNyA0Mi4zODk0IDUuNTE4NDcgNDIuMzg5NCA1LjI3ODQxTDQyLjU1OTggNS4zMTAzN0g0MS4zMTEzVjQuNzY3MDVINDMuMDI2NVY1LjI2MzQ5QzQzLjAyNjUgNS42Mjk5NyA0Mi45NDgzIDUuOTQ4MTUgNDIuNzkyMSA2LjIxODA0QzQyLjYzNzMgNi40ODY1MSA0Mi40MjI4IDYuNjkzODkgNDIuMTQ4NiA2Ljg0MDJDNDEuODc1OSA2Ljk4NjUxIDQxLjU2MzQgNy4wNTk2NiA0MS4yMTExIDcuMDU5NjZDNDAuODE2MiA3LjA1OTY2IDQwLjQ2OTYgNi45Njg3NSA0MC4xNzEzIDYuNzg2OTNDMzkuODc0NSA2LjYwNTExIDM5LjY0MjkgNi4zNDczIDM5LjQ3NjcgNi4wMTM0OUMzOS4zMTA1IDUuNjc4MjcgMzkuMjI3NSA1LjI4MDU0IDM5LjIyNzUgNC44MjAzMUMzOS4yMjc1IDQuNDcyMyAzOS4yNzU3IDQuMTU5OCAzOS4zNzIzIDMuODgyODFDMzkuNDY4OSAzLjYwNTgyIDM5LjYwNDYgMy4zNzA3NCAzOS43NzkzIDMuMTc3NTZDMzkuOTU1NCAyLjk4Mjk1IDQwLjE2MjEgMi44MzQ1MiA0MC4zOTkzIDIuNzMyMjRDNDAuNjM4IDIuNjI4NTUgNDAuODk4NiAyLjU3NjcgNDEuMTgxMyAyLjU3NjdDNDEuNDE3MSAyLjU3NjcgNDEuNjM2NSAyLjYxMTUxIDQxLjgzOTcgMi42ODExMUM0Mi4wNDQyIDIuNzUwNzEgNDIuMjI2IDIuODQ5NDMgNDIuMzg1MSAyLjk3NzI3QzQyLjU0NTYgMy4xMDUxMSA0Mi42Nzg0IDMuMjU3MSA0Mi43ODM2IDMuNDMzMjRDNDIuODg4NyAzLjYwNzk1IDQyLjk1OTcgMy44MDE4NSA0Mi45OTY2IDQuMDE0OTFINDIuMzIxMlpNNDMuODYyMiA3VjIuNjM2MzZINDYuNTk4VjMuMjAzMTJINDQuNTIwNlY0LjUzMjY3SDQ2LjQ1NTNWNS4wOTczSDQ0LjUyMDZWNi40MzMyNEg0Ni42MjM2VjdINDMuODYyMlpNNDkuODU3NCAzLjc4MjY3QzQ5LjgzNDcgMy41ODA5NyA0OS43NDA5IDMuNDI0NzIgNDkuNTc2MiAzLjMxMzkyQzQ5LjQxMTQgMy4yMDE3IDQ5LjIwNCAzLjE0NTYgNDguOTU0IDMuMTQ1NkM0OC43NzUgMy4xNDU2IDQ4LjYyMDIgMy4xNzQwMSA0OC40ODk1IDMuMjMwODJDNDguMzU4OCAzLjI4NjIyIDQ4LjI1NzMgMy4zNjI5MyA0OC4xODQ4IDMuNDYwOTRDNDguMTEzOCAzLjU1NzUzIDQ4LjA3ODMgMy42Njc2MSA0OC4wNzgzIDMuNzkxMTlDNDguMDc4MyAzLjg5NDg5IDQ4LjEwMjUgMy45ODQzNyA0OC4xNTA3IDQuMDU5NjZDNDguMjAwNSA0LjEzNDk0IDQ4LjI2NTEgNC4xOTgxNSA0OC4zNDQ2IDQuMjQ5MjlDNDguNDI1NiA0LjI5OTAxIDQ4LjUxMjMgNC4zNDA5MSA0OC42MDQ2IDQuMzc1QzQ4LjY5NjkgNC40MDc2NyA0OC43ODU3IDQuNDM0NjYgNDguODcwOSA0LjQ1NTk3TDQ5LjI5NzEgNC41NjY3NkM0OS40MzYzIDQuNjAwODUgNDkuNTc5IDQuNjQ3MDIgNDkuNzI1MyA0LjcwNTI2QzQ5Ljg3MTYgNC43NjM0OSA1MC4wMDczIDQuODQwMiA1MC4xMzIzIDQuOTM1MzdDNTAuMjU3MyA1LjAzMDU0IDUwLjM1ODEgNS4xNDg0NCA1MC40MzQ4IDUuMjg5MDZDNTAuNTEzIDUuNDI5NjkgNTAuNTUyIDUuNTk4MDEgNTAuNTUyIDUuNzk0MDNDNTAuNTUyIDYuMDQxMTkgNTAuNDg4MSA2LjI2MDY1IDUwLjM2MDMgNi40NTI0MUM1MC4yMzM4IDYuNjQ0MTggNTAuMDQ5OSA2Ljc5NTQ1IDQ5LjgwODQgNi45MDYyNUM0OS41Njg0IDcuMDE3MDUgNDkuMjc3OSA3LjA3MjQ0IDQ4LjkzNyA3LjA3MjQ0QzQ4LjYxMDMgNy4wNzI0NCA0OC4zMjc2IDcuMDIwNiA0OC4wODkgNi45MTY5QzQ3Ljg1MDMgNi44MTMyMSA0Ny42NjM1IDYuNjY2MTkgNDcuNTI4NiA2LjQ3NTg1QzQ3LjM5MzYgNi4yODQwOSA0Ny4zMTkxIDYuMDU2ODIgNDcuMzA0OSA1Ljc5NDAzSDQ3Ljk2NTRDNDcuOTc4MiA1Ljk1MTcgNDguMDI5MyA2LjA4MzEgNDguMTE4OCA2LjE4ODIxQzQ4LjIwOTcgNi4yOTE5IDQ4LjMyNTUgNi4zNjkzMiA0OC40NjYxIDYuNDIwNDVDNDguNjA4MSA2LjQ3MDE3IDQ4Ljc2MzcgNi40OTUwMyA0OC45MzI3IDYuNDk1MDNDNDkuMTE4OCA2LjQ5NTAzIDQ5LjI4NDMgNi40NjU5MSA0OS40MjkyIDYuNDA3NjdDNDkuNTc1NSA2LjM0ODAxIDQ5LjY5MDUgNi4yNjU2MiA0OS43NzQzIDYuMTYwNTFDNDkuODU4MSA2LjA1Mzk4IDQ5LjkgNS45Mjk2OSA0OS45IDUuNzg3NjRDNDkuOSA1LjY1ODM4IDQ5Ljg2MzEgNS41NTI1NiA0OS43ODkyIDUuNDcwMTdDNDkuNzE2OCA1LjM4Nzc4IDQ5LjYxODEgNS4zMTk2IDQ5LjQ5MzEgNS4yNjU2MkM0OS4zNjk1IDUuMjExNjUgNDkuMjI5NiA1LjE2NDA2IDQ5LjA3MzMgNS4xMjI4N0w0OC41NTc3IDQuOTgyMjRDNDguMjA4MyA0Ljg4NzA3IDQ3LjkzMTMgNC43NDcxNiA0Ny43MjY3IDQuNTYyNUM0Ny41MjM2IDQuMzc3ODQgNDcuNDIyMSA0LjEzMzUyIDQ3LjQyMjEgMy44Mjk1NUM0Ny40MjIxIDMuNTc4MTIgNDcuNDkwMiAzLjM1ODY2IDQ3LjYyNjYgMy4xNzExNkM0Ny43NjMgMi45ODM2NiA0Ny45NDc2IDIuODM4MDcgNDguMTgwNiAyLjczNDM4QzQ4LjQxMzUgMi42MjkyNiA0OC42NzYzIDIuNTc2NyA0OC45Njg5IDIuNTc2N0M0OS4yNjQ0IDIuNTc2NyA0OS41MjUgMi42Mjg1NSA0OS43NTA5IDIuNzMyMjRDNDkuOTc4MiAyLjgzNTk0IDUwLjE1NzEgMi45Nzg2OSA1MC4yODc4IDMuMTYwNTFDNTAuNDE4NSAzLjM0MDkxIDUwLjQ4NjcgMy41NDgzIDUwLjQ5MjQgMy43ODI2N0g0OS44NTc0WiIgZmlsbD0iI0E4QUFBRCIvPgo8L3N2Zz4K");
			}
		
		</style>';
	}

	/**
	 * Enqueue builder assets
	 *
	 * @return void
	 */
	public function enqueue_assets() {

		$this->init_ui_instance( $this->args['cx_ui_instance'] );

		$this->cx_ui_instance->enqueue_assets();

		wp_enqueue_script(
			'jet-dashboard-class-script',
			$this->url . 'assets/js/jet-dashboard-class.js',
			array( 'cx-vue-ui' ),
			$this->version,
			true
		);

		do_action( 'jet-dashboard/before-enqueue-assets', $this, $this->get_page() );

		$direction_suffix = is_rtl() ? '-rtl' : '';

		wp_enqueue_style(
			'jet-dashboard-admin-css',
			$this->url . 'assets/css/jet-dashboard-admin' . $direction_suffix . '.css',
			false,
			$this->version
		);

		wp_enqueue_script(
			'jet-dashboard-script',
			$this->url . 'assets/js/jet-dashboard.js',
			array( 'cx-vue-ui' ),
			$this->version,
			true
		);

		do_action( 'jet-dashboard/after-enqueue-assets', $this, $this->get_page() );

		wp_set_script_translations( 'jet-dashboard-script', 'jet-dashboard' );

		wp_localize_script(
			'jet-dashboard-script',
			'JetDashboardConfig',
			apply_filters( 'jet-dashboard/js-page-config',
				array(
					'pageModule'           => false,
					'subPageModule'        => false,
					'themeInfo'            => $this->data_manager->get_theme_info(),
					'licenseList'          => array_values( Utils::get_license_list() ),
					'primaryLicenseData'   => $this->license_manager->get_primary_license_data(),
					'ajaxUrl'              => esc_url( admin_url( 'admin-ajax.php' ) ),
					'nonce'                => wp_create_nonce( $this->dashboard_slug ),
					'pageModuleConfig'     => $this->data_manager->get_dashboard_page_config( $this->get_page(), $this->get_subpage() ),
					'helpCenterConfig'     => $this->data_manager->get_dashboard_config( 'helpCenter' ),
					'avaliableBanners'     => $this->data_manager->get_dashboard_config( 'banners' ),
					'noticeList'           => $this->notice_manager->get_registered_notices( $this->get_page() ),
					'serviceActionOptions' => $this->data_manager->get_service_action_list(),
				),
				$this->get_page(),
				$this->get_subpage()
			)
		);

		add_action( 'admin_footer', array( $this, 'print_vue_templates' ), 0 );
	}

	/**
	 * Print components templates
	 *
	 * @return void
	 */
	public function print_vue_templates() {

		$templates = apply_filters(
			'jet-dashboard/js-page-templates',
			array(
				'alert-list'       => $this->get_view( 'common/alert-list' ),
				'alert-item'       => $this->get_view( 'common/alert-item' ),
				'banner'           => $this->get_view( 'common/banner' ),
				'before-content'   => $this->get_view( 'common/before-content' ),
				'header'           => $this->get_view( 'common/header' ),
				'before-component' => $this->get_view( 'common/before-component' ),
				'inner-component'  => $this->get_view( 'common/inner-component' ),
				'after-component'  => $this->get_view( 'common/after-component' ),
				'before-sidebar'   => $this->get_view( 'common/before-sidebar' ),
				'sidebar'          => $this->get_view( 'common/sidebar' ),
				'after-sidebar'    => $this->get_view( 'common/after-sidebar' ),
			),
			$this->get_page(),
			$this->get_subpage()
		);

		foreach ( $templates as $name => $path ) {

			ob_start();
			include $path;
			$content = ob_get_clean();

			printf(
				'<script type="text/x-template" id="jet-dashboard-%1$s">%2$s</script>',
				$name,
				$content
			);
		}
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

