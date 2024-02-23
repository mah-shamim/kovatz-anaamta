<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Dashboard' ) ) {

	/**
	 * Define Jet_Engine_Dashboard class
	 */
	class Jet_Engine_Dashboard {

		public $builder       = null;
		public $skins_manager = null;
		public $import        = null;
		public $export        = null;
		public $presets       = null;
		private $nonce_action = 'jet-engine-dashboard';

		/**
		 * Constructor for the class
		 */
		function __construct() {
			add_action( 'admin_menu', array( $this, 'register_main_menu_page' ), 10 );
			add_action( 'admin_init', array( $this, 'init_components' ), 99 );
		}

		/**
		 * Register menu page
		 *
		 * @return void
		 */
		public function register_main_menu_page() {

			add_menu_page(
				__( 'JetEngine', 'jet-engine' ),
				__( 'JetEngine', 'jet-engine' ),
				'manage_options',
				jet_engine()->admin_page,
				array( $this, 'render_page' ),
				'data:image/svg+xml;base64,' . base64_encode('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M20 1H4C2.34315 1 1 2.34315 1 4V20C1 21.6569 2.34315 23 4 23H20C21.6569 23 23 21.6569 23 20V4C23 2.34315 21.6569 1 20 1ZM4 0C1.79086 0 0 1.79086 0 4V20C0 22.2091 1.79086 24 4 24H20C22.2091 24 24 22.2091 24 20V4C24 1.79086 22.2091 0 20 0H4Z" fill="black"/><path fill-rule="evenodd" clip-rule="evenodd" d="M21.6293 6.00066C21.9402 5.98148 22.1176 6.38578 21.911 6.64277L20.0722 8.93035C19.8569 9.19824 19.4556 9.02698 19.4598 8.669L19.4708 7.74084C19.4722 7.61923 19.4216 7.50398 19.3343 7.42975L18.6676 6.86321C18.4105 6.6447 18.5378 6.19134 18.8619 6.17135L21.6293 6.00066ZM6.99835 12.008C6.99835 14.1993 5.20706 15.9751 2.99967 15.9751C2.44655 15.9751 2 15.5293 2 14.9827C2 14.4361 2.44655 13.9928 2.99967 13.9928C4.10336 13.9928 4.99901 13.1036 4.99901 12.008V9.03323C4.99901 8.48413 5.44556 8.04082 5.99868 8.04082C6.55179 8.04082 6.99835 8.48413 6.99835 9.03323V12.008ZM17.7765 12.008C17.7765 13.1036 18.6721 13.9928 19.7758 13.9928C20.329 13.9928 20.7755 14.4336 20.7755 14.9827C20.7755 15.5318 20.329 15.9751 19.7758 15.9751C17.5684 15.9751 15.7772 14.1993 15.7772 12.008V9.03323C15.7772 8.48413 16.2237 8.04082 16.7768 8.04082C17.33 8.04082 17.7765 8.48665 17.7765 9.03323V9.92237H18.5707C19.1238 9.92237 19.5729 10.3682 19.5729 10.9173C19.5729 11.4664 19.1238 11.9122 18.5707 11.9122H17.7765V12.008ZM15.2038 10.6176C15.2063 10.6151 15.2088 10.6151 15.2088 10.6151C14.8942 9.79393 14.3056 9.07355 13.4835 8.60001C11.5755 7.50181 9.13979 8.15166 8.04117 10.0508C6.94001 11.9475 7.59462 14.3731 9.50008 15.4688C10.9032 16.2749 12.593 16.1338 13.8261 15.2472L13.8184 15.2371C14.1026 15.0633 14.2904 14.751 14.2904 14.3958C14.2904 13.8492 13.8438 13.4059 13.2932 13.4059C13.0268 13.4059 12.7833 13.5092 12.6057 13.6805C12.0069 14.081 11.2102 14.1439 10.5378 13.7762L14.5644 11.9198C14.7978 11.8493 15.0059 11.6931 15.1353 11.4664C15.2926 11.1969 15.3078 10.8871 15.2038 10.6176ZM12.4864 10.3153C12.6057 10.3833 12.7122 10.4614 12.8112 10.5471L9.49754 12.0709C9.48993 11.7208 9.5762 11.3657 9.76395 11.0407C10.3145 10.0937 11.5324 9.76874 12.4864 10.3153Z" fill="#24292D"/></svg>')
			);

		}

		/**
		 * Initialize dashboard components
		 *
		 * @return [type] [description]
		 */
		public function init_components() {

			if ( ! $this->is_dashboard() && ! wp_doing_ajax() ) {
				return;
			}

			require jet_engine()->plugin_path( 'includes/dashboard/skins-import.php' );
			require jet_engine()->plugin_path( 'includes/dashboard/skins-export.php' );
			require jet_engine()->plugin_path( 'includes/dashboard/presets.php' );
			require jet_engine()->plugin_path( 'includes/dashboard/tab-manager.php' );

			$this->import  = new Jet_Engine_Skins_Import();
			$this->export  = new Jet_Engine_Skins_Export();
			$this->presets = new Jet_Engine_Skins_Presets();

			\Jet_Engine\Dashboard\Tab_Manager::instance();

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		}

		/**
		 * Initialize interface builder
		 *
		 * @return [type] [description]
		 */
		public function enqueue_assets() {

			$module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );
			$ui          = new CX_Vue_UI( $module_data );

			$ui->enqueue_assets();

			wp_register_script(
				'jet-engine-shortcode-generator',
				jet_engine()->plugin_url( 'assets/js/admin/dashboard/shortcode-generator.js' ),
				array( 'cx-vue-ui' ),
				jet_engine()->get_version(),
				true
			);

			wp_register_script(
				'jet-engine-macros-generator',
				jet_engine()->plugin_url( 'assets/js/admin/dashboard/macros-generator.js' ),
				array( 'cx-vue-ui' ),
				jet_engine()->get_version(),
				true
			);

			wp_register_script(
				'jet-engine-dashboard-skins',
				jet_engine()->plugin_url( 'assets/js/admin/dashboard/skins.js' ),
				array( 'cx-vue-ui' ),
				jet_engine()->get_version(),
				true
			);

			wp_localize_script(
				'jet-engine-dashboard-skins',
				'JetEngineExportConfig',
				$this->export->export_config()
			);

			do_action( 'jet-engine/dashboard/assets' );

			wp_enqueue_script(
				'jet-engine-dashboard',
				jet_engine()->plugin_url( 'assets/js/admin/dashboard/main.js' ),
				array( 'cx-vue-ui', 'jet-engine-dashboard-skins', 'jet-engine-shortcode-generator', 'jet-engine-macros-generator' ),
				jet_engine()->get_version(),
				true
			);

			wp_localize_script(
				'jet-engine-dashboard',
				'JetEngineDashboardConfig',
				apply_filters(
					'jet-engine/dashboard/config',
					array(
						'internal_modules'  => jet_engine()->modules->get_all_modules_for_js( true, 'internal' ),
						'external_modules'  => jet_engine()->modules->get_all_modules_for_js( true, 'external' ),
						'active_modules'    => jet_engine()->modules->get_active_modules(),
						'modules_to_update' => jet_engine()->modules->updater->get_pluign_updates(),
						'is_license_active' => jet_engine()->modules->installer->is_license_active(),
						'components_list'   => array(
							array(
								'value' => 'meta_field',
								'label' => __( 'Meta Field', 'jet-engine' ),
							),
							array(
								'value' => 'option',
								'label' => __( 'Option', 'jet-engine' ),
							),
						),
						'messages'          => array(
							'saved'            => __( 'Saved!', 'jet-engine' ),
							'saved_and_reload' => __( 'Saved! One of activated/deactivated modules requires page reloading. Page will be reloaded automatically in few seconds.', 'jet-engine' ),
						),
						'shortode_generator' => jet_engine()->shortcodes->get_generator_config(),
						'macros_generator'   => jet_engine()->listings->macros->get_macros_for_js(),
						'_nonce'             => wp_create_nonce( $this->nonce_action ),
					)
				)
			);

			wp_enqueue_style(
				'jet-engine-dashboard',
				jet_engine()->plugin_url( 'assets/css/admin/dashboard.css' ),
				array(),
				jet_engine()->get_version()
			);

			do_action( 'jet-engine/dashboard/assets-after' );

			add_action( 'admin_footer', array( $this, 'print_dashboard_templates' ) );

		}

		public function get_nonce_action() {
			return $this->nonce_action;
		}

		public function print_dashboard_templates() {

			ob_start();
			include jet_engine()->get_template( 'admin/pages/dashboard/shortcode-generator.php' );
			$content = ob_get_clean();

			printf( '<script type="text/x-template" id="jet-engine-shortcode-generator">%s</script>', $content );

			ob_start();
			include jet_engine()->get_template( 'admin/pages/dashboard/macros-generator.php' );
			$content = ob_get_clean();

			printf( '<script type="text/x-template" id="jet-engine-macros-generator">%s</script>', $content );
		}

		/**
		 * Check if is dashboard page
		 *
		 * @return boolean [description]
		 */
		public function is_dashboard() {
			return ( isset( $_GET['page'] ) && jet_engine()->admin_page === $_GET['page'] );
		}

		/**
		 * Returns dashboard page URL
		 * @return [type] [description]
		 */
		public function dashboard_url( $tab = '' ) {
			
			$url = add_query_arg(
				array( 'page' => jet_engine()->admin_page ),
				esc_url( admin_url( 'admin.php' ) )
			);

			if ( $tab ) {
				$url .= '#' . esc_attr( $tab );
			}

			return $url;
		}

		/**
		 * Render main admin page
		 *
		 * @return void
		 */
		public function render_page() {
			include jet_engine()->get_template( 'admin/pages/dashboard/main.php' );
		}

		/**
		 * Get dashboard setting
		 *
		 * @return [type] [description]
		 */
		public function get_setting( $setting = null, $default = false ) {

		}

	}

}
