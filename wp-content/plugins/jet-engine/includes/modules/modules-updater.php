<?php
/**
 * Modules manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Modules_Updater' ) ) {

	/**
	 * Define Jet_Engine_Modules_Updater class
	 */
	class Jet_Engine_Modules_Updater {

		protected $api_url        = 'https://account.crocoblock.com/wp-json/croco/v1/engine-modules/';
		protected $plugins        = array();
		protected $update_plugins = false;

		/**
		 * Init class parameters.
		 *
		 * @since  1.0.0
		 * @param  array $attr Input attributes array.
		 * @return void
		 */
		public function __construct( $attr = array() ) {

			/**
			 * Need for test update - wp_clean_update_cache();
			 */
			//wp_clean_update_cache();

			add_action( 'pre_set_site_transient_update_plugins', array( $this, 'get_update' ) );
			add_action( 'wp_ajax_jet_engine_update_module', array( $this, 'process_update' ) );

		}

		public function process_update() {

			$nonce_action = jet_engine()->dashboard->get_nonce_action();

			if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], $nonce_action ) ) {
				wp_send_json_error( array(
					'message' => __( 'Nonce validation failed', 'jet-engine' ),
				) );
			}

			if ( ! current_user_can( 'update_plugins' ) ) {
				wp_send_json_error( array( 'message' => 'You can`t update plugins' ) );
			}

			$plugin_file = ! empty( $_REQUEST['file'] ) ? $_REQUEST['file'] : false;

			if ( ! $plugin_file ) {
				wp_send_json_error( array( 'message' => 'Plugin file not found in the request' ) );
			}

			$plugin = plugin_basename( sanitize_text_field( wp_unslash( $plugin_file ) ) );
			$slug   = dirname( $plugin );

			$status = array(
				'update'     => 'plugin',
				'slug'       => $slug,
				'oldVersion' => '',
				'newVersion' => '',
			);

			if ( ! current_user_can( 'update_plugins' ) || 0 !== validate_file( $plugin ) ) {
				wp_send_json_error( array(
					'message' => 'Sorry, you are not allowed to update plugins for this site.',
				) );
			}

			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );

			include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

			wp_update_plugins();

			$skin     = new \WP_Ajax_Upgrader_Skin();
			$upgrader = new \Plugin_Upgrader( $skin );
			$result   = $upgrader->bulk_upgrade( array( $plugin ) );

			$upgrade_messages = [];

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$upgrade_messages = $skin->get_upgrade_messages();
			}

			if ( is_wp_error( $skin->result ) ) {

				wp_send_json_error( array(
					'message' => $skin->result->get_error_message(),
					'debug'   => $upgrade_messages,
				) );

			} elseif ( $skin->get_errors()->get_error_code() ) {

				wp_send_json_error( array(
					'message' => $skin->get_error_messages(),
					'debug'   => $upgrade_messages,
				) );

			} elseif ( is_array( $result ) && ! empty( $result[ $plugin ] ) ) {

				$plugin_update_data = current( $result );

				/*
				 * If the `update_plugins` site transient is empty (e.g. when you update
				 * two plugins in quick succession before the transient repopulates),
				 * this may be the return.
				 *
				 * Preferably something can be done to ensure `update_plugins` isn't empty.
				 * For now, surface some sort of error here.
				 */
				if ( true === $plugin_update_data ) {
					wp_send_json_error( array(
						'message' => 'Plugin update failed.',
						'debug'   => $upgrade_messages,
					) );
				}

				$plugin_data = get_plugins( '/' . $result[ $plugin ]['destination_name'] );
				$plugin_data = reset( $plugin_data );

				wp_send_json_success( array(
					'message' => 'The plugin has been updated',
				) );

			} elseif ( false === $result ) {

				global $wp_filesystem;

				$errorMessage = 'Unable to connect to the filesystem. Please confirm your credentials.';

				// Pass through the error from WP_Filesystem if one was raised.
				if ( $wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
					$errorMessage = esc_html( $wp_filesystem->errors->get_error_message() );
				}

				wp_send_json_error( array(
					'message' => $errorMessage,
				) );

			}

			wp_send_json_error( array(
				'message' => 'Plugin update failed.',
			) );

		}

		/**
		 * Returns allowed plugin updates
		 *
		 * @return [type] [description]
		 */
		public function get_pluign_updates() {

			if ( ! $this->update_plugins ) {
				$this->update_plugins = get_site_transient( 'update_plugins' );
			}

			if ( empty( $this->plugins ) || ! $this->update_plugins || empty( $this->update_plugins->response ) ) {
				return array();
			}

			$to_update = array();

			foreach ( $this->plugins as $plugin ) {

				if ( ! isset( $this->update_plugins->response[ $plugin['file'] ] ) ) {
					continue;
				}

				$new_version = $this->update_plugins->response[ $plugin['file'] ]->new_version;

				if ( version_compare( $new_version, $plugin['version'], '>' ) ) {
					$to_update[ $plugin['file'] ] = array(
						'version' => $new_version,
						'slug'    => $plugin['slug'],
					);
				}

			}

			return $to_update;

		}

		/**
		 * Register module plugin for updater
		 *
		 * @param  array  $plugin [description]
		 * @return [type]         [description]
		 */
		public function register_plugin( $plugin = array() ) {
			$this->plugins[ $plugin['slug'] ] = $plugin;
			add_filter( 'in_plugin_update_message-' . $plugin['file'], array( $this, 'in_plugin_update_message' ), 10, 2 );
		}

		/**
		 * [plugin_row_meta description]
		 * @param  [type] $plugin_meta [description]
		 * @param  [type] $plugin_file [description]
		 * @param  [type] $plugin_data [description]
		 * @return [type]              [description]
		 */
		public function in_plugin_update_message( $plugin_data, $response ) {

			if ( ! $response->package ) {
				echo sprintf( '&nbsp;<strong><a class="" href="%1$s">%2$s</a><strong>', \Jet_Dashboard\Dashboard::get_instance()->get_dashboard_page_url( 'license-page', 'license-manager' ), 'Activate your JetEngine license for automatic updates.' );
			}
		}

		/**
		 * Check if update are avaliable.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		protected function check_update( $slug, $version ) {

			$response = $this->remote_query( $slug );

			if ( ! $response ) {
				return array( 'version' => false );
			}

			if ( version_compare( $version, $response->version, '<' ) ) {
				return array(
					'version' => $response->version,
					'package' => $response->package,
				);
			}

			return array( 'version' => false );

		}

		/**
		 * Remote request to updater API.
		 *
		 * @since  1.0.0
		 * @return array|bool
		 */
		protected function remote_query( $slug ) {

			$response = wp_remote_get( $this->api_url . $slug );

			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != '200' ) {
				return false;
			}

			$response = json_decode( $response['body'] );

			return $response;

		}

		/**
		 * Process update.
		 *
		 * @since  1.0.0
		 * @param  object $data Update data.
		 * @return object
		 */
		public function get_update( $data ) {

			if ( ! is_object( $data ) ) {
				$data = new \stdClass();
			}

			foreach ( $this->plugins as $plugin ) {

				$new_update = $this->check_update( $plugin['slug'], $plugin['version'] );

				if ( $new_update['version'] ) {

					$update = new stdClass();

					$package = false;
					$license = \Jet_Dashboard\Utils::get_plugin_license_key( 'jet-engine/jet-engine.php' );

					if ( $license ) {
						$package = jet_engine()->modules->installer->get_plugin_url( $plugin['slug'] );
					}

					$update->slug        = $plugin['slug'];
					$update->plugin      = $plugin['file'];
					$update->new_version = $new_update['version'];
					$update->url         = false;
					$update->package     = $package;

					$data->response[ $plugin['file'] ] = $update;

				}

			}

			return $data;

		}

	}

}
