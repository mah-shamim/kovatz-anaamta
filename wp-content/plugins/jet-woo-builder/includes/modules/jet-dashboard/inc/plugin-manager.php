<?php
namespace Jet_Dashboard;

use Jet_Dashboard\Dashboard as Dashboard;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define plugin updater class.
 *
 * @since 1.0.0
 */
class Plugin_Manager {

	/**
	 * [$jet_banners_url description]
	 * @var string
	 */
	public $jet_banners_url = 'https://account.crocoblock.com/free-download/images/jetbanners/';

	/**
	 * [$jet_changelog_url description]
	 * @var string
	 */
	public $jet_changelog_url = 'https://crocoblock.com/wp-content/uploads/jet-changelog/%s.json';

	/**
	 * [$remote_plugin_data description]
	 * @var boolean
	 */
	public $remote_plugin_data = false;

	/**
	 * [$user_plugins description]
	 * @var boolean
	 */
	public $user_plugins = false;

	/**
	 * [$update_plugins description]
	 * @var boolean
	 */
	public $update_plugins = false;

	/**
	 * [$registered_plugins description]
	 * @var boolean
	 */
	public $registered_plugins = false;

	/**
	 * [$registered_plugins description]
	 * @var array
	 */
	public $registered_plugins_data = array();

	/**
	 * Init class parameters.
	 *
	 * @since  1.0.0
	 * @param  array $attr Input attributes array.
	 * @return void
	 */
	public function __construct() {

		$registered_plugins = Dashboard::get_instance()->get_registered_plugins();

		if ( ! empty( $registered_plugins ) ) {
			foreach ( $registered_plugins as $plugin_file => $plugin_data ) {
				add_filter( 'in_plugin_update_message-' . $plugin_file , array( $this, 'in_plugin_update_message' ), 10, 2 );
			}
		}

		/**
		 * Need for test update - set_site_transient( 'update_plugins', null );
		 */
		add_action( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

		add_action( 'admin_init', array( $this, 'generate_register_plugin_data' ) );

		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );

		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

		add_filter( 'http_request_args', array( $this, 'allow_unsafe_urls' ) );

		add_action( 'activated_plugin', array( $this, 'activate_plugin_handle' ), 10, 2 );

		add_action( 'wp_ajax_jet_dashboard_plugin_action', array( $this, 'plugin_action' ) );

		add_action( 'wp_ajax_jet_dashboard_wizard_plugin_action', array( $this, 'wizard_plugin_action' ) );

	}

	/**
	 * [admin_init description]
	 * @return [type] [description]
	 */
	public function generate_register_plugin_data() {

		$registered_plugins = Dashboard::get_instance()->get_registered_plugins();

		if ( ! empty( $registered_plugins ) ) {
			foreach ( $registered_plugins as $plugin_file => $plugin_data ) {
				$plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file );

				$this->registered_plugins_data[ $plugin_file ] = array(
					'name'       => $plugin_info['Name'],
					'author'     => $plugin_info['Author'],
					'plugin_url' => $plugin_info['PluginURI'],
					'requires'   => '5.2',
					'tested'     => '',
					'banners'    => array(
						'high' => sprintf( 'https://account.crocoblock.com/free-download/images/jetbanners/%s.png', $plugin_data['slug'] ),
						'low'  => sprintf( 'https://account.crocoblock.com/free-download/images/jetbanners/%s.png', $plugin_data['slug'] ),
					),
					'version'       => false,
					'changelog'     => false,
					'slug'          => $plugin_data['slug'],
					'transient_key' => $plugin_data['slug'] . '_plugin_info_data'
				);
			}
		}
	}

	/**
	 * [plugins_api_filter description]
	 * @param  [type] $_data   [description]
	 * @param  string $_action [description]
	 * @param  [type] $_args   [description]
	 * @return [type]          [description]
	 */
	public function plugins_api_filter( $_data, $_action = '', $_args = null ) {

		if ( 'plugin_information' !== $_action ) {
			return $_data;
		}

		if ( ! isset( $_args->slug ) ) {
			return $_data;
		}

		$registered_plugin_data = false;

		foreach ( $this->registered_plugins_data as $plugin_file => $plugin_data ) {

			if ( $plugin_data['slug'] === $_args->slug ) {
				$registered_plugin_data = $plugin_data;

				break;
			}
		}

		if ( ! $registered_plugin_data ) {
			return $_data;
		}

		$plugin_api_data = get_site_transient( $registered_plugin_data['transient_key'] );

		if ( empty( $plugin_api_data ) ) {
			$changelog_remote_response = Dashboard::get_instance()->data_manager->changelog_remote_query( $registered_plugin_data['slug'] );

			if ( ! $changelog_remote_response ) {
				return $_data;
			}

			$plugin_api_data = new \stdClass();

			$plugin_api_data->name     = $registered_plugin_data['name'];
			$plugin_api_data->slug     = $registered_plugin_data['slug'];
			$plugin_api_data->author   = $registered_plugin_data['author'];
			$plugin_api_data->homepage = $registered_plugin_data['plugin_url'];
			$plugin_api_data->requires = $registered_plugin_data['requires'];
			$plugin_api_data->tested   = $registered_plugin_data['tested'];
			$plugin_api_data->banners  = $registered_plugin_data['banners'];
			$plugin_api_data->version  = $changelog_remote_response->current_version;
			$plugin_api_data->sections = array(
				'changelog' => $changelog_remote_response->changelog,
			);

			// Expires in 1 day
			set_site_transient( $registered_plugin_data['transient_key'], $plugin_api_data, DAY_IN_SECONDS );
		}

		$_data = $plugin_api_data;

		return $_data;
	}

	/**
	 * [changelog_remote_query description]
	 * @param  [type] $slug [description]
	 * @return [type]       [description]
	 */
	public function changelog_remote_query( $slug ) {

		$response = wp_remote_get( sprintf( $this->jet_changelog_url, $slug ) );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != '200' ) {
			return false;
		}

		$response = json_decode( $response['body'] );

		return $response;
	}

	/**
	 * [plugin_row_meta description]
	 * @param  [type] $plugin_meta [description]
	 * @param  [type] $plugin_file [description]
	 * @param  [type] $plugin_data [description]
	 * @return [type]              [description]
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data ) {

		if ( array_key_exists( $plugin_file, $this->registered_plugins_data ) && empty( $plugin_data['update'] ) ) {

			$plugin_meta['view-details'] = sprintf( '<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
				esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $this->registered_plugins_data[ $plugin_file ]['slug'] . '&TB_iframe=true&width=600&height=550' ) ),
				esc_attr( sprintf( __( 'More information about %s', 'jet-tricks' ), $this->registered_plugins_data[ $plugin_file ]['name'] ) ),
				esc_attr( $this->registered_plugins_data[ $plugin_file ]['name'] ),
				'View details'
			);
		}

		return $plugin_meta;
	}

	/**
	 * [activate_plugin_handle description]
	 * @param  [type] $plugin       [description]
	 * @param  [type] $network_wide [description]
	 * @return [type]               [description]
	 */
	public function activate_plugin_handle( $plugin, $network_wide ) {

		$jet_plugin_list = $this->get_remote_jet_plugin_list();

		$is_jet_plugin = array_search( $plugin, array_column( $jet_plugin_list, 'slug' ) );

		if ( ! $is_jet_plugin ) {
			return false;
		}

		$query_url = add_query_arg(
			array(
				'action'   => 'plugin_activate_action',
				'license'  => Utils::get_plugin_license_key( $plugin ),
				'plugin'   => $plugin,
				'site_url' => urlencode( Utils::get_site_url() ),
			),
			Utils::get_api_url()
		);

		wp_remote_post( $query_url, array(
			'timeout'  => 30,
			//'blocking' => false
		) );

		return false;
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
			echo sprintf( '&nbsp;<strong><a class="" href="%1$s">%2$s</a><strong>', Dashboard::get_instance()->get_dashboard_page_url( 'license-page', 'license-manager' ), 'Activate your license for automatic updates.' );
		}
	}

	/**
	 * Process update.
	 *
	 * @since  1.0.0
	 * @param  object $data Update data.
	 * @return object
	 */
	public function check_update( $data ) {

		if ( empty( $data ) ) {
			return false;
		}

		delete_site_transient( 'jet_dashboard_remote_jet_plugin_list' );

		$registered_plugins = Dashboard::get_instance()->get_registered_plugins();

		foreach ( $registered_plugins as $plugin_slug => $plugin_data ) {

			$new_update_version = $this->check_new_update_version( $plugin_data );

			if ( $new_update_version ) {

				// Delete plugin api transient data
				if ( ! empty( $this->registered_plugins_data ) && isset( $this->registered_plugins_data[ $plugin_data['file'] ] ) ) {
					delete_site_transient( $this->registered_plugins_data[ $plugin_data['file'] ]['transient_key'] );
				}

				$update = new \stdClass();

				$update->slug        = $plugin_data['slug'];
				$update->plugin      = $plugin_data['file'];
				$update->new_version = $new_update_version;
				$update->url         = false;
				$update->package     = Utils::package_url( $plugin_data['file'] );

				$data->response[ $plugin_data['file'] ] = $update;
			}
		}

		return $data;
	}

	/**
	 * [check_update description]
	 * @return [type] [description]
	 */
	public function check_new_update_version( $plugin_data = false ) {

		$remote_plugin_data = $this->get_remote_jet_plugin_list();

		if ( ! $remote_plugin_data || ! is_array( $remote_plugin_data ) ) {
			return false;
		}

		$new_version = '1.0.0';

		foreach ( $remote_plugin_data as $key => $plugin ) {

			if ( $plugin_data['file'] === $plugin['slug'] ) {
				$new_version = $plugin['version'];

				break;
			}
		}

		if ( version_compare( $plugin_data['version'], $new_version, '<' ) ) {
			return $new_version;
		}

		return false;
	}

	/**
	 * Remote request to updater API.
	 *
	 * @since  1.0.0
	 * @return array|bool
	 */
	public function get_remote_jet_plugin_list() {

		$remote_jet_plugin_list = get_site_transient( 'jet_dashboard_remote_jet_plugin_list' );

		if ( $remote_jet_plugin_list ) {
			return $remote_jet_plugin_list;
		}

		$query_url = add_query_arg(
			array(
				'action' => 'get_plugins_data',
			),
			Utils::get_api_url()
		);

		$response = wp_remote_get( $query_url, array(
			'timeout' => 30,
		) );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != '200' ) {
			return false;
		}

		$response = json_decode( $response['body'], true );

		if ( 'error' === $response['status'] ) {
			return false;
		}

		if ( ! isset( $response['data'] ) ) {
			return false;
		}

		$remote_jet_plugin_list = $response['data'];

		set_site_transient( 'jet_dashboard_remote_jet_plugin_list', $remote_jet_plugin_list, HOUR_IN_SECONDS * 24 );

		return $remote_jet_plugin_list;
	}

	/**
	 * [get_plugin_list description]
	 * @return [type] [description]
	 */
	public function get_plugin_data_list() {

		$jet_plugin_list    = $this->get_remote_jet_plugin_list();
		$user_plugin_list   = $this->get_user_plugins();
		$registered_plugins = Dashboard::get_instance()->get_registered_plugins();

		$plugins_list = [];

		if ( ! empty( $jet_plugin_list ) ) {
			foreach ( $jet_plugin_list as $key => $plugin_data ) {

				$plugin_slug = $plugin_data['slug'];

				if ( array_key_exists( $plugin_slug, $user_plugin_list ) ) {
					$plugin_data = wp_parse_args( $plugin_data, $user_plugin_list[ $plugin_slug ] );
				} else {
					$plugin_data = wp_parse_args( $plugin_data, array(
						'version'         => $plugin_data['version'],
						'currentVersion'  => $plugin_data['version'],
						'updateAvaliable' => false,
						'isActivated'     => false,
						'isInstalled'     => false,
					) );
				}

				$plugin_data['licenseControl'] = array_key_exists( $plugin_slug, $registered_plugins ) ? true : false;
				$plugin_data['usefulLinks'] = array_key_exists( $plugin_slug, $registered_plugins ) ? $registered_plugins[ $plugin_slug ]['plugin_links'] : array();
				$plugins_list[ $plugin_data['slug'] ] = $plugin_data;
			}
		}

		return $plugins_list;
	}

	/**
	 * [get_plugin_data description]
	 * @return [type] [description]
	 */
	public function get_user_plugins() {

		if ( ! $this->user_plugins ) {

			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$this->user_plugins = get_plugins();
		}

		$plugin_list = array();

		if ( $this->user_plugins ) {

			foreach ( $this->user_plugins as $plugin_file => $plugin_data ) {
				$current_version = $plugin_data['Version'];
				$latest_version = $this->get_latest_version( $plugin_file );

				$plugin_list[ $plugin_file ] = array(
					'version'         => $latest_version,
					'currentVersion'  => $current_version,
					'updateAvaliable' => version_compare( $latest_version, $current_version, '>' ),
					'isActivated'     => is_plugin_active( $plugin_file ),
					'isInstalled'     => true,
				);

			}
		}

		return $plugin_list;
	}

	/**
	 * [is_plugin_installed description]
	 * @param  [type]  $plugin_file [description]
	 * @param  boolean $plugin_url  [description]
	 * @return boolean              [description]
	 */
	public function get_user_plugin( $plugin_file = false ) {

		if ( ! $plugin_file ) {
			return false;
		}

		$user_plugins = $this->get_user_plugins();

		if ( isset( $user_plugins[ $plugin_file ] ) ) {
			return $user_plugins[ $plugin_file ];
		}

		return false;
	}

	/**
	 * [get_installed_plugin_data description]
	 * @param  [type] $plugin [description]
	 * @return [type]         [description]
	 */
	public function get_installed_plugin_data( $plugin_file ) {

		if ( ! $this->user_plugins ) {

			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$this->user_plugins = get_plugins();
		}

		$plugin_data = $this->user_plugins[ $plugin_file ];

		$current_version = $plugin_data['Version'];

		$latest_version = $this->get_latest_version( $plugin_file );

		return array(
			'version'         => $latest_version,
			'currentVersion'  => $current_version,
			'updateAvaliable' => version_compare( $latest_version, $current_version, '>' ),
			'isActivated'     => is_plugin_active( $plugin_file ),
			'isInstalled'     => true,
		);
	}

	/**
	 * Get latest version for passed plugin
	 *
	 * @param  [type] $remote_plugin_data [description]
	 * @return [type]                     [description]
	 */
	public function get_latest_version( $plugin_file ) {

		if ( ! $this->update_plugins ) {
			$this->update_plugins = get_site_transient( 'update_plugins' );
		}

		$no_update = isset( $this->update_plugins->no_update ) ? $this->update_plugins->no_update : false;
		$to_update = isset( $this->update_plugins->response ) ? $this->update_plugins->response : false;

		if ( $to_update && ! empty( $to_update ) && array_key_exists( $plugin_file, $to_update ) ) {
			$version = $to_update[ $plugin_file ]->new_version;
		} elseif ( ! empty( $no_update ) && array_key_exists( $plugin_file, $no_update ) ) {
			$version = $no_update[ $plugin_file ]->new_version;
		} elseif ( array_key_exists( $plugin_file, $this->user_plugins ) ) {
			$version = $this->user_plugins[ $plugin_file ]['Version'];
		} else {
			$version = '1.0.0';
		}

		return $version;
	}

	/**
	 * [install_plugin description]
	 * @param  [type]  $plugin     [description]
	 * @param  boolean $plugin_url [description]
	 * @return [type]              [description]
	 */
	public function plugin_action() {
		$data = ( ! empty( $_POST['data'] ) ) ? $_POST['data'] : false;

		if ( ! $data ) {
			wp_send_json(
				array(
					'status' => 'error',
					'message' => $this->sys_messages['server_error']
				)
			);
		}

		// Nonce checking here. The capability checking is in the appropriate methods below
		if ( ! isset( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'jet-dashboard' ) ) {
			wp_send_json( [
				'status'  => 'error',
				'message' => __( 'Page has expired. Please reload this page.', 'jet-dashboard' ),
			] );
		}

		$action  = $data['action'];
		$plugin  = $data['plugin'];
		$version = isset( $data['version'] ) ? $data['version'] : false;

		switch ( $action ) {

			case 'activate':
				$this->activate_plugin( $plugin );
			break;

			case 'deactivate':
				$this->deactivate_plugin( $plugin );
			break;

			case 'install':
				$this->install_plugin( $plugin );
			break;

			case 'update':
				$this->update_plugin( $plugin );
			break;

			case 'rollback':
				$this->rollback_plugin( $plugin, $version );
			break;
		}

		wp_send_json(
			array(
				'status'  => 'success',
				'message' => 'Success',
				'data'    => [],
			)
		);
	}

	/**
	 * [install_plugin description]
	 * @param  [type]  $plugin     [description]
	 * @param  boolean $plugin_url [description]
	 * @return [type]              [description]
	 */
	public function wizard_plugin_action() {
		$data = ( ! empty( $_POST['data'] ) ) ? $_POST['data'] : false;

		if ( ! $data ) {
			wp_send_json(
				array(
					'status' => 'error',
					'message' => $this->sys_messages['server_error']
				)
			);
		}

		// Nonce checking here. The capability checking is in the appropriate methods below
		if ( ! isset( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'jet-dashboard' ) ) {
			wp_send_json( [
				'type' => 'error',
				'title' => __( 'Error', 'jet-dashboard' ),
				'desc'  => __( 'Server error. Stop cheating!!!', 'jet-dashboard' ),
			] );
		}

		$action  = $data['action'];
		$plugin  = $data['plugin'];

		switch ( $action ) {

			case 'install':
				$this->install_plugin( $plugin, 'https://account.crocoblock.com/free-download/crocoblock-wizard.zip' );
			break;

			case 'activate':
				$this->activate_plugin( $plugin );
			break;
		}

		wp_send_json(
			array(
				'status'  => 'error',
				'message' => 'Server Error',
				'data'    => [],
			)
		);
	}

	/**
	 * Performs plugin activation
	 *
	 * @param  [type] $plugin [description]
	 * @return [type]         [description]
	 */
	public function activate_plugin( $plugin_file ) {

		$status = array();

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Sorry, you are not allowed to install plugins on this site.'
				)
			);
		}

		if ( ! $plugin_file ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Plugin slug is required'
				)
			);
		}

		$activate = null;

		if ( ! is_plugin_active( $plugin_file ) ) {
			$activate = activate_plugin( $plugin_file );
		}

		if ( is_wp_error( $activate ) ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $activate->get_error_message(),
				)
			);
		}

		wp_send_json(
			array(
				'status'  => 'success',
				'message' => 'The plugin has been activated',
				'data'    => $this->get_installed_plugin_data( $plugin_file ),
			)
		);
	}

	/**
	 * Performs plugin activation
	 *
	 * @param  [type] $plugin [description]
	 * @return [type]         [description]
	 */
	public function deactivate_plugin( $plugin_file ) {

		$status = array();

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Sorry, you are not allowed to install plugins on this site.'
				)
			);
		}

		if ( ! $plugin_file ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Plugin slug is required'
				)
			);
		}

		$deactivate_handler = null;

		if ( is_plugin_active( $plugin_file ) ) {
			$deactivate_handler = deactivate_plugins( $plugin_file );
		}

		if ( is_wp_error( $deactivate_handler ) ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $deactivate_handler->get_error_message(),
				)
			);
		}

		wp_send_json(
			array(
				'status'  => 'success',
				'message' => 'The plugin has been deactivated',
				'data'    => $this->get_installed_plugin_data( $plugin_file ),
			)
		);
	}

	/**
	 * Perform plugin installtion by passed plugin slug and plugin package URL (optional)
	 *
	 * @param  [type]  $plugin     [description]
	 * @param  boolean $plugin_url [description]
	 * @return [type]              [description]
	 */
	public function install_plugin( $plugin_file, $plugin_url = false ) {

		$status = array();

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Sorry, you are not allowed to install plugins on this site.'
				)
			);
		}

		if ( ! $plugin_file ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Plugin slug is required'
				)
			);
		}

		if ( ! $plugin_url ) {
			$package = Utils::package_url( $plugin_file );
		} else {
			$package = $plugin_url;
		}

		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		//include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		$skin     = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $package );

		if ( is_wp_error( $result ) ) {
			$status['errorCode']    = $result->get_error_code();
			$status['errorMessage'] = $result->get_error_message();

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $result->get_error_message(),
					'data'    => [],
				)
			);
		} elseif ( is_wp_error( $skin->result ) ) {
			$status['errorCode']    = $skin->result->get_error_code();
			$status['errorMessage'] = $skin->result->get_error_message();

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $skin->result->get_error_message(),
					'data'    => [],
				)
			);
		} elseif ( $skin->get_errors()->get_error_code() ) {
			$status['errorMessage'] = $skin->get_error_messages();

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $skin->get_error_messages(),
					'data'    => [],
				)
			);
		} elseif ( is_null( $result ) ) {
			global $wp_filesystem;

			$status['errorMessage'] = 'Unable to connect to the filesystem. Please confirm your credentials.';

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $status['errorMessage'],
					'data'    => [],
				)
			);
		}

		wp_send_json(
			array(
				'status'  => 'success',
				'message' => 'The plugin has been Installed',
				'data'    => $this->get_installed_plugin_data( $plugin_file ),
			)
		);
	}

	/**
	 * [update_plugin description]
	 * @param  [type] $plugin_slug [description]
	 * @return [type]              [description]
	 */
	public function update_plugin( $plugin_file ) {

		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Sorry, you are not allowed to update plugins on this site.'
				)
			);
		}

		if ( ! $plugin_file ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Plugin slug is required'
				)
			);
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

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Sorry, you are not allowed to update plugins for this site.',
				)
			);
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

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $skin->result->get_error_message(),
					'debug'   => $upgrade_messages,
				)
			);
		} elseif ( $skin->get_errors()->get_error_code() ) {

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $skin->get_error_messages(),
					'debug'   => $upgrade_messages,
				)
			);
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
				wp_send_json(
					array(
						'status'  => 'error',
						'message' => 'Plugin update failed.',
						'debug'   => $upgrade_messages,
					)
				);
			}

			$plugin_data = get_plugins( '/' . $result[ $plugin ]['destination_name'] );
			$plugin_data = reset( $plugin_data );

			wp_send_json(
				array(
					'status'  => 'success',
					'message' => 'The plugin has been updated',
					'data'    => $this->get_installed_plugin_data( $plugin_file ),
				)
			);

		} elseif ( false === $result ) {
			global $wp_filesystem;

			$errorMessage = 'Unable to connect to the filesystem. Please confirm your credentials.';

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$errorMessage = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $errorMessage,
				)
			);
		}

		wp_send_json(
			array(
				'status'  => 'error',
				'message' => 'Plugin update failed.',
			)
		);
	}

	/**
	 * [update_plugin_to_version description]
	 * @param  boolean $plugin_file [description]
	 * @param  boolean $version     [description]
	 * @return [type]               [description]
	 */
	public function rollback_plugin( $plugin_file = false, $version = false ) {
		$status = array();

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Sorry, you are not allowed to install plugins on this site.'
				)
			);
		}

		if ( ! $plugin_file ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => 'Plugin slug is required'
				)
			);
		}

		$package = Utils::package_url( $plugin_file );

		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		$skin            = new \WP_Ajax_Upgrader_Skin();
		$plugin_upgrader = new \Plugin_Upgrader( $skin );
		$plugin_upgrader->init();
		$plugin_upgrader->upgrade_strings();

		// Get the URL to the zip file.
		$package = Utils::package_url( $plugin_file, $version );

		add_filter( 'upgrader_pre_install', array( $plugin_upgrader, 'active_before' ), 10, 2 );
		add_filter( 'upgrader_clear_destination', array( $plugin_upgrader, 'delete_old_plugin' ), 10, 4 );
		add_filter( 'upgrader_post_install', array( $plugin_upgrader, 'active_after' ), 10, 2 );
		add_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9, 0 );

		$result = $plugin_upgrader->run(
			array(
				'package'           => $package,
				'destination'       => WP_PLUGIN_DIR,
				'clear_destination' => true,
				'clear_working'     => true,
				'hook_extra'        => array(
					'plugin' => $plugin_file,
					'type'   => 'plugin',
					'action' => 'update',
				),
			)
		);

		remove_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9 );
		remove_filter( 'upgrader_pre_install', array( $plugin_upgrader, 'active_before' ) );
		remove_filter( 'upgrader_clear_destination', array( $plugin_upgrader, 'delete_old_plugin' ) );
		remove_filter( 'upgrader_post_install', array( $plugin_upgrader, 'active_after' ) );

		// Force refresh of plugin update information.
		wp_clean_plugins_cache( true );

		$upgrade_messages = [];

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$upgrade_messages = $skin->get_upgrade_messages();
		}

		if ( is_wp_error( $skin->result ) ) {

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $skin->result->get_error_message(),
					'debug'   => $upgrade_messages,
				)
			);
		} elseif ( $skin->get_errors()->get_error_code() ) {

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $skin->get_error_messages(),
					'debug'   => $upgrade_messages,
				)
			);
		} elseif ( $result ) {

			wp_send_json(
				array(
					'status'  => 'success',
					'message' => 'Plugin version has been changed',
					'data'    => $this->get_installed_plugin_data( $plugin_file ),
				)
			);

		} elseif ( false === $result ) {
			global $wp_filesystem;

			$errorMessage = 'Unable to connect to the filesystem. Please confirm your credentials.';

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$errorMessage = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $errorMessage,
				)
			);
		}

		wp_send_json(
			array(
				'status'  => 'error',
				'message' => 'Plugin update failed.',
			)
		);
	}

	/**
	 * [allow_unsafe_urls description]
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function allow_unsafe_urls( $args ) {

		if ( isset( $_REQUEST['action'] ) && 'jet_dashboard_plugin_action' === $_REQUEST['action'] ) {
			$args['reject_unsafe_urls'] = false;
		}

		return $args;
	}
}
