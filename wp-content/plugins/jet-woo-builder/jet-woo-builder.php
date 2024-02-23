<?php
/**
 * Plugin Name: JetWooBuilder For Elementor
 * Plugin URI:  https://crocoblock.com/plugins/jetwoobuilder/
 * Description: Your perfect asset in creating WooCommerce page templates using loads of special widgets & stylish page layouts.
 * Version:     2.1.10
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: jet-woo-builder
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 *
 * WC tested up to: 7.8
 * WC requires at least: 3.0
 *
 * Elementor tested up to: 3.14
 * Elementor Pro tested up to: 3.14
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// If class `Jet_Woo_Builder` doesn't exists yet.
if ( ! class_exists( 'Jet_Woo_Builder' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	#[AllowDynamicProperties]
	class Jet_Woo_Builder {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		private $version = '2.1.10';

		/**
		 * Require Elementor Version
		 *
		 * @since 1.8.0
		 * @var string Elementor version required to run the plugin.
		 */
		private static $require_elementor_version = '3.0.0';

		/**
		 * Holder for base plugin URL
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_url = null;

		/**
		 * Holder for base plugin path
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_path = null;

		/**
		 * Plugin properties
		 */
		public $module_loader;

		/**
		 * @var Jet_Woo_Builder_Documents
		 */
		public $documents;

		/**
		 * @var Jet_Woo_Builder_Parser
		 */
		public $parser;

		/**
		 * @var Jet_Woo_Builder_Macros
		 */
		public $macros;

		/**
		 * @var Jet_Woo_Builder_Ajax_Handlers
		 */
		public $ajax_handlers;

		/**
		 * @var Jet_Woo_Builder_Export_Import
		 */
		public $export_import;

		/**
		 * @var Jet_Woo_Builder_Components
		 */
		public $components;

		/**
		 * @var Jet_Woo_Builder_Dynamic_Tags_Manager
		 */
		public $dynamic_tags;

		/**
		 * @var Jet_Woo_Builder_Compatibility
		 */
		public $compatibility;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Load modules.
			add_action( 'after_setup_theme', [ $this, 'module_loader' ], -20 );

			// Internationalize the text strings used.
			add_action( 'init', array( $this, 'lang' ), -999 );

			// Load files.
			add_action( 'init', array( $this, 'init' ), -999 );

			// Jet Dashboard Init
			add_action( 'init', array( $this, 'jet_dashboard_init' ), -999 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

		}

		/**
		 * Module loader.
		 *
		 * Load plugin modules.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function module_loader() {

			require $this->plugin_path( 'includes/modules/loader.php' );

			$this->module_loader = new Jet_Woo_Builder_CX_Loader(
				[
					$this->plugin_path( 'includes/modules/interface-builder/cherry-x-interface-builder.php' ),
					$this->plugin_path( 'includes/modules/post-meta/cherry-x-post-meta.php' ),
					$this->plugin_path( 'includes/modules/db-updater/cherry-x-db-updater.php' ),
					$this->plugin_path( 'includes/modules/vue-ui/cherry-x-vue-ui.php' ),
					$this->plugin_path( 'includes/modules/jet-dashboard/jet-dashboard.php' ),
					$this->plugin_path( 'includes/modules/jet-elementor-extension/jet-elementor-extension.php' ),
					$this->plugin_path( 'includes/modules/admin-bar/jet-admin-bar.php' ),
				]
			);

		}

		/**
		 * Returns plugin version
		 *
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Lang.
		 *
		 * Loads the translation files.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function lang() {
			load_plugin_textdomain( 'jet-woo-builder', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Init.
		 *
		 * Manually init required modules.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function init() {

			// Check if Elementor installed and activated.
			if ( ! did_action( 'elementor/loaded' ) ) {
				add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
				return;
			}

			// Check for required Elementor version.
			if ( ! version_compare( ELEMENTOR_VERSION, self::$require_elementor_version, '>=' ) ) {
				add_action( 'admin_notices', [ $this, 'admin_notice_required_elementor_version' ] );
				return;
			}

			if ( ! class_exists( 'WooCommerce' ) ) {
				add_action( 'admin_notices', [ $this, 'admin_notice_missing_woocommerce_plugin' ] );
				return;
			}

			// Declare compatibility with WooCommerce High-Performance Order Storage (HPOS).
			add_action( 'before_woocommerce_init', function () {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
				}
			} );

			$this->load_files();

			jet_woo_builder_assets()->init();
			jet_woo_builder_post_type()->init();
			jet_woo_builder_settings()->init();
			jet_woo_builder_shortcodes()->init();
			jet_woo_builder_shop_settings()->init();

			$this->documents     = new Jet_Woo_Builder_Documents();
			$this->parser        = new Jet_Woo_Builder_Parser();
			$this->macros        = new Jet_Woo_Builder_Macros();
			$this->ajax_handlers = new Jet_Woo_Builder_Ajax_Handlers();
			$this->export_import = new Jet_Woo_Builder_Export_Import();
			$this->components    = new Jet_Woo_Builder_Components();
			$this->compatibility = new Jet_Woo_Builder_Compatibility();
			$this->admin_bar     = Jet_Admin_Bar::get_instance();

			//Init Rest Api
			new \Jet_Woo_Builder\Rest_Api();

			if ( is_admin() ) {
				//Init JetWooBuilder Settings
				new \Jet_Woo_Builder\Settings();

				// Init DB upgrader
				require $this->plugin_path( 'includes/class-jet-woo-builder-db-upgrader.php' );
				jet_woo_builder_db_upgrader()->init();
			}

		}

		/**
		 * Init the JetDashboard module
		 */
		public function jet_dashboard_init() {
			if ( is_admin() ) {
				$jet_dashboard_module_data = $this->module_loader->get_included_module_data( 'jet-dashboard.php' );
				$jet_dashboard             = \Jet_Dashboard\Dashboard::get_instance();

				$jet_dashboard->init(
					[
						'path'           => $jet_dashboard_module_data['path'],
						'url'            => $jet_dashboard_module_data['url'],
						'cx_ui_instance' => [ $this, 'jet_dashboard_ui_instance_init' ],
						'plugin_data'    => [
							'slug'         => 'jet-woo-builder',
							'file'         => 'jet-woo-builder/jet-woo-builder.php',
							'version'      => $this->get_version(),
							'plugin_links' => [
								[
									'label'  => esc_html__( 'Go to settings', 'jet-woo-builder' ),
									'url'    => add_query_arg( [
										'page'    => 'jet-dashboard-settings-page',
										'subpage' => 'jet-woo-builder-general-settings',
									], admin_url( 'admin.php' ) ),
									'target' => '_self',
								],
							],
						],
					]
				);
			}
		}

		/**
		 * Get Vue UI Instance for JetDashboard module
		 *
		 * @return object
		 */
		public function jet_dashboard_ui_instance_init() {

			$cx_ui_module_data = $this->module_loader->get_included_module_data( 'cherry-x-vue-ui.php' );

			return new CX_Vue_UI( $cx_ui_module_data );

		}

		/**
		 * Show required plugins admin notice.
		 *
		 * @since  2.1.6 Refactored.
		 * @access public
		 *
		 * @return void
		 */
		public function admin_notice_missing_main_plugin() {

			/* translators: %s Elementor install/activate URL link. */
			echo '<div class="notice notice-warning is-dismissible"><p>' . sprintf( __( '<strong>JetWooBuilder</strong> requires <a href="%s" target="_blank"><strong>Elementor</strong></a> to be installed and activated.', 'jet-woo-builder' ), admin_url() . 'plugin-install.php?s=elementor&tab=search&type=term' ) . '</p></div>';

			if ( ! class_exists( 'WooCommerce' ) ) {
				$this->admin_notice_missing_woocommerce_plugin();
			}

		}

		/**
		 * Show minimum required Elementor version admin notice.
		 *
		 * @since  1.8.0
		 * @since  2.1.6 Refactored.
		 * @access public
		 *
		 * @return void
		 */
		public function admin_notice_required_elementor_version() {
			/* translators: %s Elementor required version. */
			echo '<div class="notice notice-warning is-dismissible"><p>' . sprintf( __( '<strong>JetWooBuilder</strong> requires <strong>Elementor</strong> version %s or greater.', 'jet-woo-builder' ), self::$require_elementor_version ) . '</p></div>';
		}

		/**
		 * Show missing WooCommerce plugin admin notice.
		 *
		 * @since  2.1.6 Refactored.
		 * @access public
		 *
		 * @return void
		 */
		public function admin_notice_missing_woocommerce_plugin() {
			/* translators: %s WC install/activate URL link. */
			echo '<div class="notice notice-warning is-dismissible"><p>' . sprintf( __( '<strong>JetWooBuilder</strong> requires <a href="%s" target="_blank"><strong>WooCommerce</strong></a> to be installed and activated.', 'jet-woo-builder' ), admin_url() . 'plugin-install.php?s=woocommerce&tab=search&type=term' ) . '</p></div>';
		}

		/**
		 * Check if theme has Elementor
		 *
		 * @return boolean
		 */
		public function has_elementor() {
			return defined( 'ELEMENTOR_VERSION' );
		}

		/**
		 * Returns Elementor instance
		 *
		 * @return object
		 */
		public function elementor() {
			return \Elementor\Plugin::$instance;
		}

		/**
		 * Load required files.
		 */
		public function load_files() {
			require $this->plugin_path( 'includes/class-jet-woo-builder-ajax-handler.php' );
			require $this->plugin_path( 'includes/class-jet-woo-builder-assets.php' );
			require $this->plugin_path( 'includes/class-jet-woo-builder-tools.php' );
			require $this->plugin_path( 'includes/class-jet-woo-builder-post-type.php' );
			require $this->plugin_path( 'includes/class-jet-woo-builder-documents.php' );
			require $this->plugin_path( 'includes/class-jet-woo-builder-parser.php' );
			require $this->plugin_path( 'includes/class-jet-woo-builder-macros.php' );
			require $this->plugin_path( 'includes/class-jet-woo-builder-common-controls.php' );
			require $this->plugin_path( 'includes/class-jet-woo-builder-template-functions.php' );
			require $this->plugin_path( 'includes/export-import.php' );
			require $this->plugin_path( 'includes/compatibility/manager.php' );
			require $this->plugin_path( 'includes/components/manager.php' );
			require $this->plugin_path( 'includes/settings/manager.php' );
			require $this->plugin_path( 'includes/settings/class-jet-woo-builder-settings.php' );
			require $this->plugin_path( 'includes/settings/class-jet-woo-builder-shop-settings.php' );
			require_once $this->plugin_path( 'includes/shortcodes/manager.php' );
			require $this->plugin_path( 'includes/rest-api/rest-api.php' );
			require $this->plugin_path( 'includes/rest-api/endpoints/base.php' );
			require $this->plugin_path( 'includes/rest-api/endpoints/plugin-settings.php' );
		}

		/**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param string $path Path inside plugin dir.
		 *
		 * @return string
		 */
		public function plugin_path( $path = '' ) {

			if ( ! $this->plugin_path ) {
				$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path . $path;

		}

		/**
		 * Returns url to file or dir inside plugin folder
		 *
		 * @param string $path Path inside plugin dir.
		 *
		 * @return string
		 */
		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			return $this->plugin_url . $path;

		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'jet-woo-builder/template-path', 'jet-woo-builder/' );
		}

		/**
		 * Returns path to template file.
		 *
		 * @return string|bool
		 */
		public function get_template( $name = null ) {

			$template = locate_template( $this->template_path() . $name );

			if ( ! $template ) {
				$template = $this->plugin_path( 'templates/' . $name );
			}

			if ( file_exists( $template ) ) {
				return $template;
			} else {
				return false;
			}

		}

		/**
		 * Do some stuff on plugin activation.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function activation() {}

		/**
		 * Do some stuff on plugin activation.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function deactivation() {}

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

}

if ( ! function_exists( 'jet_woo_builder' ) ) {

	/**
	 * Returns instance of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function jet_woo_builder() {
		return Jet_Woo_Builder::get_instance();
	}

}

jet_woo_builder();
