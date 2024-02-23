<?php
/**
 * Jet Smart Filters Admin class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Admin' ) ) {
	/**
	 * Define Jet_Smart_Filters_Admin class
	 */
	class Jet_Smart_Filters_Admin {
		/**
		 * Components
		 */
		public $data;
		public $multilingual_support;

		/**
		 * Constructor for the class
		 */
		public function __construct() {
			// Init components
			require jet_smart_filters()->plugin_path( 'admin/includes/data.php' );
			add_action( 'init', function() {
				$this->data = new Jet_Smart_Filters_Admin_Data();
			}, 999 );

			// Init Setting Pages
			require_once jet_smart_filters()->plugin_path( 'admin/setting-pages/setting-pages.php' );
			new Jet_Smart_Filters_Admin_Setting_Pages();

			// Register plugin menu page
			add_action( 'admin_menu', array( $this, 'register_plugin_page' ) );

			if ( ! $this->is_filters_page() ) {
				return;
			}

			// Init Multilingual Support
			require_once jet_smart_filters()->plugin_path( 'admin/includes/multilingual-support.php' );
			$this->multilingual_support = new Jet_Smart_Filters_Admin_Multilingual_Support();

			// Register dynamic query data
			require_once jet_smart_filters()->plugin_path( 'admin/includes/dynamic-query/registration.php' );
			new Jet_Smart_Filters_Admin_Dynamic_Query_Registration();

			// Register and enqueue admin assets
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

			// Localize admin app data
			add_action( 'admin_enqueue_scripts', array( $this, 'localize_data' ) );

			// Remove all notices
			add_action('in_admin_header', array( $this, 'remove_all_notices' ), 99);
		}

		/**
		 * Check if is filters page
		 */
		public function is_filters_page() {

			return ( isset( $_REQUEST['page'] ) && jet_smart_filters()->post_type->slug() === $_REQUEST['page'] );
		}

		/**
		 * Register plugin menu page
		 */
		public function register_plugin_page() {
			/**
			 * Register Smart Filters page
			 */
			add_menu_page(
				esc_html__( 'Smart Filters', 'jet-smart-filters' ),
				esc_html__( 'Smart Filters', 'jet-smart-filters' ),
				'manage_options',
				jet_smart_filters()->post_type->slug(),
				array( $this, 'render_plugin_page' ),
				'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2E3YWFhZCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTIwIDFINEMyLjM0MzE1IDEgMSAyLjM0MzE1IDEgNFYyMEMxIDIxLjY1NjkgMi4zNDMxNSAyMyA0IDIzSDIwQzIxLjY1NjkgMjMgMjMgMjEuNjU2OSAyMyAyMFY0QzIzIDIuMzQzMTUgMjEuNjU2OSAxIDIwIDFaTTQgMEMxLjc5MDg2IDAgMCAxLjc5MDg2IDAgNFYyMEMwIDIyLjIwOTEgMS43OTA4NiAyNCA0IDI0SDIwQzIyLjIwOTEgMjQgMjQgMjIuMjA5MSAyNCAyMFY0QzI0IDEuNzkwODYgMjIuMjA5MSAwIDIwIDBINFoiIGZpbGw9IiNhN2FhYWQiLz48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTIxLjYyOTMgNi4wMDA2NkMyMS45NDAyIDUuOTgxNDggMjIuMTE3NiA2LjM4NTc4IDIxLjkxMSA2LjY0Mjc3TDIwLjA3MjIgOC45MzAzNUMxOS44NTY5IDkuMTk4MjQgMTkuNDU1NiA5LjAyNjk4IDE5LjQ1OTggOC42NjlMMTkuNDcwOCA3Ljc0MDg0QzE5LjQ3MjIgNy42MTkyMyAxOS40MjE2IDcuNTAzOTggMTkuMzM0MyA3LjQyOTc1TDE4LjY2NzYgNi44NjMyMUMxOC40MTA1IDYuNjQ0NyAxOC41Mzc4IDYuMTkxMzQgMTguODYxOSA2LjE3MTM1TDIxLjYyOTMgNi4wMDA2NlpNNi45OTgzNSAxMi4wMDhDNi45OTgzNSAxNC4xOTkzIDUuMjA3MDYgMTUuOTc1MSAyLjk5OTY3IDE1Ljk3NTFDMi40NDY1NSAxNS45NzUxIDIgMTUuNTI5MyAyIDE0Ljk4MjdDMiAxNC40MzYxIDIuNDQ2NTUgMTMuOTkyOCAyLjk5OTY3IDEzLjk5MjhDNC4xMDMzNiAxMy45OTI4IDQuOTk5MDEgMTMuMTAzNiA0Ljk5OTAxIDEyLjAwOFY5LjAzMzIzQzQuOTk5MDEgOC40ODQxMyA1LjQ0NTU2IDguMDQwODIgNS45OTg2OCA4LjA0MDgyQzYuNTUxNzkgOC4wNDA4MiA2Ljk5ODM1IDguNDg0MTMgNi45OTgzNSA5LjAzMzIzVjEyLjAwOFpNMTcuNzc2NSAxMi4wMDhDMTcuNzc2NSAxMy4xMDM2IDE4LjY3MjEgMTMuOTkyOCAxOS43NzU4IDEzLjk5MjhDMjAuMzI5IDEzLjk5MjggMjAuNzc1NSAxNC40MzM2IDIwLjc3NTUgMTQuOTgyN0MyMC43NzU1IDE1LjUzMTggMjAuMzI5IDE1Ljk3NTEgMTkuNzc1OCAxNS45NzUxQzE3LjU2ODQgMTUuOTc1MSAxNS43NzcyIDE0LjE5OTMgMTUuNzc3MiAxMi4wMDhWOS4wMzMyM0MxNS43NzcyIDguNDg0MTMgMTYuMjIzNyA4LjA0MDgyIDE2Ljc3NjggOC4wNDA4MkMxNy4zMyA4LjA0MDgyIDE3Ljc3NjUgOC40ODY2NSAxNy43NzY1IDkuMDMzMjNWOS45MjIzN0gxOC41NzA3QzE5LjEyMzggOS45MjIzNyAxOS41NzI5IDEwLjM2ODIgMTkuNTcyOSAxMC45MTczQzE5LjU3MjkgMTEuNDY2NCAxOS4xMjM4IDExLjkxMjIgMTguNTcwNyAxMS45MTIySDE3Ljc3NjVWMTIuMDA4Wk0xNS4yMDM4IDEwLjYxNzZDMTUuMjA2MyAxMC42MTUxIDE1LjIwODggMTAuNjE1MSAxNS4yMDg4IDEwLjYxNTFDMTQuODk0MiA5Ljc5MzkzIDE0LjMwNTYgOS4wNzM1NSAxMy40ODM1IDguNjAwMDFDMTEuNTc1NSA3LjUwMTgxIDkuMTM5NzkgOC4xNTE2NiA4LjA0MTE3IDEwLjA1MDhDNi45NDAwMSAxMS45NDc1IDcuNTk0NjIgMTQuMzczMSA5LjUwMDA4IDE1LjQ2ODhDMTAuOTAzMiAxNi4yNzQ5IDEyLjU5MyAxNi4xMzM4IDEzLjgyNjEgMTUuMjQ3MkwxMy44MTg0IDE1LjIzNzFDMTQuMTAyNiAxNS4wNjMzIDE0LjI5MDQgMTQuNzUxIDE0LjI5MDQgMTQuMzk1OEMxNC4yOTA0IDEzLjg0OTIgMTMuODQzOCAxMy40MDU5IDEzLjI5MzIgMTMuNDA1OUMxMy4wMjY4IDEzLjQwNTkgMTIuNzgzMyAxMy41MDkyIDEyLjYwNTcgMTMuNjgwNUMxMi4wMDY5IDE0LjA4MSAxMS4yMTAyIDE0LjE0MzkgMTAuNTM3OCAxMy43NzYyTDE0LjU2NDQgMTEuOTE5OEMxNC43OTc4IDExLjg0OTMgMTUuMDA1OSAxMS42OTMxIDE1LjEzNTMgMTEuNDY2NEMxNS4yOTI2IDExLjE5NjkgMTUuMzA3OCAxMC44ODcxIDE1LjIwMzggMTAuNjE3NlpNMTIuNDg2NCAxMC4zMTUzQzEyLjYwNTcgMTAuMzgzMyAxMi43MTIyIDEwLjQ2MTQgMTIuODExMiAxMC41NDcxTDkuNDk3NTQgMTIuMDcwOUM5LjQ4OTkzIDExLjcyMDggOS41NzYyIDExLjM2NTcgOS43NjM5NSAxMS4wNDA3QzEwLjMxNDUgMTAuMDkzNyAxMS41MzI0IDkuNzY4NzQgMTIuNDg2NCAxMC4zMTUzWiIgZmlsbD0iI2E3YWFhZCIvPjwvc3ZnPg==',
				101
			);

			/**
			 * All filters submenu page
			 */
			add_submenu_page(
				jet_smart_filters()->post_type->slug(),
				esc_html__( 'All Filters', 'jet-dashboard' ),
				esc_html__( 'All Filters', 'jet-dashboard' ),
				'manage_options',
				admin_url( 'admin.php' ) . '?page=' . jet_smart_filters()->post_type->slug() . '#/'
			);

			/**
			 * Add new filter submenu page
			 */
			add_submenu_page(
				jet_smart_filters()->post_type->slug(),
				esc_html__( 'Add New', 'jet-dashboard' ),
				esc_html__( 'Add New', 'jet-dashboard' ),
				'manage_options',
				admin_url( 'admin.php' ) . '?page=' . jet_smart_filters()->post_type->slug() . '#/new'
			);

			/**
			 * Settings submenu page
			 */
			add_submenu_page(
				jet_smart_filters()->post_type->slug(),
				esc_html__( 'Settings', 'jet-dashboard' ),
				esc_html__( 'Settings', 'jet-dashboard' ),
				'manage_options',
				add_query_arg(
					array(
						'page' => 'jet-dashboard-settings-page',
						'subpage' => 'jet-smart-filters-general-settings'
					),
					admin_url( 'admin.php' )
				)
			);

			// Remove Smart Filters link
			global $submenu;

			if ( isset( $submenu[jet_smart_filters()->post_type->slug()] ) ) {
				array_shift( $submenu[jet_smart_filters()->post_type->slug()] );
			}
		}

		/**
		 * Render plugin page callback
		 */
		public function render_plugin_page() {

			$app_template = jet_smart_filters()->plugin_path( 'admin/templates/admin-app.php' );

			if ( ! file_exists( $app_template ) ) {
				return;
			}

			include $app_template;
		}

		/**
		 * Enqueue admin assets only on gallery page
		 */
		public function enqueue_admin_assets() {

			wp_enqueue_media();

			wp_enqueue_script(
				'jet-smart-filters-admin-app',
				jet_smart_filters()->plugin_url( 'admin/assets/js/jsf-admin-app.js' ),
				array(),
				jet_smart_filters()->get_version(),
				true
			);
		}

		/**
		 * Localize data
		 */
		public function localize_data() {

			wp_localize_script( 'jet-smart-filters-admin-app', 'JetSmartFiltersAdminData',
				apply_filters( 'jet-smart-filters/admin/localized-data',
					array(
						'urls' => array(
							'admin'     => get_admin_url(),
							'ajaxurl'   => admin_url( 'admin-ajax.php' ),
							'endpoints' => jet_smart_filters()->rest_api->get_endpoints_urls(),
						),
						'nonce'           => wp_create_nonce( 'wp_rest' ),
						'plugin_settings' => $this->data->plugin_settings(),
						'filter_settings' => $this->data->settings(),
						'filter_types'    => $this->data->types(),
						'filter_sources'  => $this->data->sources(),
						'sort_by_list'    => $this->data->sort_by_list(),
						'help_block'      => $this->data->help_block_data()
					)
				)
			);
		}

		/**
		 * Remove all notices
		 */
		public function remove_all_notices() {

			remove_all_actions( 'user_admin_notices' );
			remove_all_actions( 'admin_notices' );
		}
	}
}