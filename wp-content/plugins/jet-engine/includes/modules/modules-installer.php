<?php
/**
 * Modules manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Modules_Installer' ) ) {

	/**
	 * Define Jet_Engine_Modules_Installer class
	 */
	class Jet_Engine_Modules_Installer {

		public $api_host = 'https://api.crocoblock.com/';

		/**
		 * Constructor for the class
		 */
		public function __construct() {
			add_action( 'wp_ajax_jet_engine_install_module', array( $this, 'process_module_installation' ) );
			add_action( 'wp_ajax_jet_engine_uninstall_module', array( $this, 'process_module_uninstall' ) );
		}

		/**
		 * Returns plugin package URL
		 *
		 * @param  [type] $slug [description]
		 * @return [type]       [description]
		 */
		public function get_plugin_url( $slug = null, $license = null ) {

			if ( ! $license ) {
				$license = \Jet_Dashboard\Utils::get_plugin_license_key( 'jet-engine/jet-engine.php' );
			}

			return add_query_arg(
				array(
					'action'   => 'get_jetengine_module_update',
					'license'  => $license,
					'site_url' => \Jet_Dashboard\Utils::get_site_url(),
					'module'   => $slug,
				),
				$this->api_host
			);
		}

		/**
		 * Check if license is currently active
		 *
		 * @return boolean [description]
		 */
		public function is_license_active() {
			$license = \Jet_Dashboard\Utils::get_plugin_license_key( 'jet-engine/jet-engine.php' );
			return ! empty( $license );
		}

		/**
		 * Process external module deactivation
		 *
		 * @return [type] [description]
		 */
		public function process_module_uninstall() {

			$nonce_action = jet_engine()->dashboard->get_nonce_action();

			if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], $nonce_action ) ) {
				wp_send_json_error( array(
					'message' => __( 'Nonce validation failed', 'jet-engine' ),
				) );
			}

			if ( ! current_user_can( 'install_plugins' ) ) {
				wp_send_json_error( array( 'message' => __( '<b>Error:</b> Access denied', 'jet-engine' ) ) );
			}

			$module = ! empty( $_REQUEST['module'] ) ? $_REQUEST['module'] : false;
			$slug   = esc_attr( $module['value'] );

			if ( ! $module ) {
				wp_send_json_error( array( 'message' => __( '<b>Error:</b> Module info not found in the request', 'jet-engine' ) ) );
			}

			$plugin_file = ! empty( $module['plugin_data']['file'] ) ? $module['plugin_data']['file'] : false;

			if ( ! $plugin_file ) {
				wp_send_json_error( array( 'message' => __( '<b>Error:</b> Plugin data not found in the request', 'jet-engine' ) ) );
			}

			deactivate_plugins( $plugin_file );
			jet_engine()->modules->deactivate_module( $slug );

			wp_send_json_success( array( 'message' => __( 'Done!', 'jet-engine' ) ) );

		}

		/**
		 * Process module installation
		 *
		 * @return [type] [description]
		 */
		public function process_module_installation() {

			$nonce_action = jet_engine()->dashboard->get_nonce_action();

			if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], $nonce_action ) ) {
				wp_send_json_error( array(
					'message' => __( 'Nonce validation failed', 'jet-engine' ),
				) );
			}

			if ( ! current_user_can( 'install_plugins' ) ) {
				wp_send_json_error( array( 'message' => __( '<b>Error:</b> Access denied', 'jet-engine' ) ) );
			}

			$module = ! empty( $_REQUEST['module'] ) ? $_REQUEST['module'] : false;

			if ( ! $module ) {
				wp_send_json_error( array( 'message' => __( '<b>Error:</b> Module info not found in the request', 'jet-engine' ) ) );
			}

			$slug        = $module['value'];
			$plugin_url  = $this->get_plugin_url( $slug );
			$plugin_file = ! empty( $module['plugin_data']['file'] ) ? $module['plugin_data']['file'] : false;

			if ( ! $plugin_file ) {
				wp_send_json_error( array( 'message' => __( '<b>Error:</b> Plugin data not found in the request', 'jet-engine' ) ) );
			}

			add_filter( 'http_request_args', array( $this, 'allow_unsafe_urls' ) );

			$result   = $this->install_module( $plugin_url );
			$activate = null;

			if ( ! is_plugin_active( $plugin_file ) ) {
				$activate = activate_plugin( $plugin_file );
			}

			if ( ! $result['success'] && is_wp_error( $activate ) ) {
				wp_send_json_error( array( 'message' => __( '<b>Error:</b> ', 'jet-engine' ) . $result['message'] . ' <br><b>' . __( 'Please make sure your license key is activated and valid!', 'jet-engine' ) . ' <a href="' . admin_url( 'admin.php?page=jet-dashboard-license-page&subpage=license-manager' ) . '" target="_blank">' . __( 'Check it here', 'jet-engine' ) . '</a></b>' ) );
			}

			if ( is_wp_error( $activate ) ) {
				wp_send_json_error( array( 'message' => __( '<b>Error:</b> ', 'jet-engine' ) . $activate->get_error_message() . ' <br><b>' . __( 'Please make sure your license key is activated and valid!', 'jet-engine' ) . ' <a href="' . admin_url( 'admin.php?page=jet-dashboard-license-page&subpage=license-manager' ) . '" target="_blank">' . __( 'Check it here', 'jet-engine' ) . '</a></b>' ) );
			}

			jet_engine()->modules->activate_module( $slug );

			$module_instanse = jet_engine()->modules->get_module( $slug );

			$message = __( 'Module successfully installed and activated', 'jet-engine' );
			$actions = array(
				array(
					'id'    => 'close',
					'label' => __( 'Close', 'jet-engine' ),
					'style' => 'default',
				),
			);

			$custom_actions = $module_instanse->get_installed_actions();
			$custom_message = $module_instanse->get_installed_message();

			if ( ! empty( $custom_actions ) ) {
				$actions = array_merge( $actions, $custom_actions );
			}

			if ( $custom_message ) {
				$message = $custom_message;
			}

			wp_send_json_success( array( 'message' => $message, 'actions' => $actions ) );

		}

		/**
		 * Process module installation
		 * @param  [type] $url [description]
		 * @return [type]      [description]
		 */
		public function install_module( $url ) {

			include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
			//include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

			$skin     = new \WP_Ajax_Upgrader_Skin();
			$upgrader = new \Plugin_Upgrader( $skin );
			$result   = $upgrader->install( $url );

			if ( is_wp_error( $result ) ) {

				return array(
					'success' => false,
					'message' => $result->get_error_message(),
				);

			} elseif ( is_wp_error( $skin->result ) ) {

				return array(
					'success' => false,
					'message' => $skin->result->get_error_message(),
				);

			} elseif ( $skin->get_errors()->get_error_code() ) {

				return array(
					'success' => false,
					'message' => $skin->get_error_messages(),
				);

			} elseif ( is_null( $result ) ) {

				global $wp_filesystem;

				$message = 'Unable to connect to the filesystem. Please confirm your credentials.';

				// Pass through the error from WP_Filesystem if one was raised.
				if ( $wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
					$message = esc_html( $wp_filesystem->errors->get_error_message() );
				}

				return array(
					'success' => false,
					'message' => $message,
				);
			}

			return array(
				'success' => true,
			);

		}

		/**
		 * [allow_unsafe_urls description]
		 * @param  [type] $args [description]
		 * @return [type]       [description]
		 */
		public function allow_unsafe_urls( $args ) {
			$args['reject_unsafe_urls'] = false;
			return $args;
		}

	}

}
