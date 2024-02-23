<?php
/**
 * Plugin Name: JetSearch
 * Plugin URI:  https://crocoblock.com/plugins/jetsearch/
 * Description: The best tool for adding complex search functionality to pages built with Elementor or Blocks editor
 * Version:     3.2.3
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: jet-search
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 *
 * @package jet-search
 * @author  Zemez
 * @license GPL-2.0+
 * @copyright  2018, Zemez
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// If class `Jet_Search` doesn't exists yet.
if ( ! class_exists( 'Jet_Search' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	class Jet_Search {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    Jet_Search
		 */
		private static $instance = null;

		/**
		 * Plugin version.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $version = '3.2.3';

		/**
		 * Holder for base plugin URL.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_url = null;

		/**
		 * Holder for base plugin path.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_path = null;

		/**
		 * Framework component
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    object
		 */
		public $module_loader = null;

		/**
		 * @var Jet_Search_DB
		 */
		public $db;
		public $rest_api = null;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Load the CX Loader.
			add_action( 'after_setup_theme', array( $this, 'module_loader' ), -20 );

			// Internationalize the text strings used.
			add_action( 'init', array( $this, 'lang' ), -999 );

			// Init required modules.
			add_action( 'init', array( $this, 'init' ), -999 );

			// Jet Dashboard Init
			add_action( 'init', array( $this, 'jet_dashboard_init' ), -999 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__,   array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Load the theme modules.
		 *
		 * @since  1.0.0
		 */
		public function module_loader() {
			require $this->plugin_path( 'includes/modules/loader.php' );

			$this->module_loader = new Jet_Search_CX_Loader(
				array(
					$this->plugin_path( 'includes/modules/vue-ui/cherry-x-vue-ui.php' ),
					$this->plugin_path( 'includes/modules/jet-dashboard/jet-dashboard.php' ),
				)
			);
		}

		/**
		 * [jet_dashboard_init description]
		 * @return [type] [description]
		 */
		public function jet_dashboard_init() {

			if ( is_admin() ) {

				$cx_ui_module_data         = $this->module_loader->get_included_module_data( 'cherry-x-vue-ui.php' );
				$jet_dashboard_module_data = $this->module_loader->get_included_module_data( 'jet-dashboard.php' );

				$jet_dashboard = \Jet_Dashboard\Dashboard::get_instance();

				$jet_dashboard->init( array(
					'path'           => $jet_dashboard_module_data['path'],
					'url'            => $jet_dashboard_module_data['url'],
					'cx_ui_instance' => array( $this, 'jet_dashboard_ui_instance_init' ),
					'plugin_data'    => array(
						'slug'    => 'jet-search',
						'file'    => 'jet-search/jet-search.php',
						'version' => $this->get_version(),
					),
				) );
			}
		}

		/**
		 * [jet_dashboard_ui_instance_init description]
		 * @return [type] [description]
		 */
		public function jet_dashboard_ui_instance_init() {
			$cx_ui_module_data = $this->module_loader->get_included_module_data( 'cherry-x-vue-ui.php' );

			return new CX_Vue_UI( $cx_ui_module_data );
		}

		/**
		 * Returns plugin version.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Manually init required modules.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function init() {

			$this->load_files();

			if ( $this->has_elementor() ) {
				require $this->plugin_path( 'includes/elementor-views/integration.php' );
				jet_search_integration()->init();
			}

			$this->db       = new Jet_Search_DB();
			$this->rest_api = new Jet_Search_REST_API();

			jet_search_blocks_integration()->init();
			jet_search_bricks_integration()->init();
			jet_search_ajax_handlers()->init();
			jet_search_assets()->init();
			jet_search_compatibility()->init();

			if ( is_admin() ) {
				//Init Settings Manager
				new \Jet_Search\Settings();
			}
		}

		/**
		 * Check if Elementor installed and activated.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return boolean
		 */
		public function has_elementor() {
			return defined( 'ELEMENTOR_VERSION' );
		}

		/**
		 * Returns elementor instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return \Elementor\Plugin
		 */
		public function elementor() {
			return \Elementor\Plugin::instance();
		}

		/**
		 * Load required files.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function load_files() {

			require $this->plugin_path( 'includes/renders/ajax-search.php' );
			require $this->plugin_path( 'includes/renders/search-suggestions.php' );
			require $this->plugin_path( 'includes/blocks-views/integration.php' );
			require $this->plugin_path( 'includes/assets.php' );
			require $this->plugin_path( 'includes/ajax-handlers.php' );
			require $this->plugin_path( 'includes/tools.php' );
			require $this->plugin_path( 'includes/compatibility.php' );
			require $this->plugin_path( 'includes/template-functions.php' );
			require $this->plugin_path( 'includes/class-jet-search-settings.php' );
			require $this->plugin_path( 'includes/settings/manager.php' );
			require $this->plugin_path( 'includes/core/db.php' );
			require $this->plugin_path( 'includes/rest-api/manager.php' );
			require $this->plugin_path( 'includes/token-manager.php' );

			// Bricks Integration
			require $this->plugin_path( 'includes/bricks-views/integration.php' );
		}

		/**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @since  1.0.0
		 * @return string
		 */
		public function plugin_path( $path = null ) {

			if ( ! $this->plugin_path ) {
				$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path . $path;
		}
		/**
		 * Returns url to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @since  1.0.0
		 * @return string
		 */
		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			return $this->plugin_url . $path;
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function lang() {
			load_plugin_textdomain( 'jet-search', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Get the template path.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'jet-search/template-path', 'jet-search/' );
		}

		/**
		 * Returns path to template file.
		 *
		 * @param  null|string $name Template name.
		 * @since  1.0.0
		 * @return string|bool
		 */
		public function get_template( $name = null ) {

			$template = apply_filters(
				'jet-search/get-locate-template',
				locate_template( $this->template_path() . $name ),
				$name
			);

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
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function activation() {
			require $this->plugin_path( 'includes/core/db.php' );
			Jet_Search_DB::create_all_tables();
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function deactivation() {
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return Jet_Search
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

if ( ! function_exists( 'jet_search' ) ) {

	/**
	 * Returns instance of the plugin class.
	 *
	 * @since  1.0.0
	 * @return Jet_Search
	 */
	function jet_search() {
		return Jet_Search::get_instance();
	}
}

jet_search();
