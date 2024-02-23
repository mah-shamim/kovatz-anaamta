<?php
namespace Jet_Dashboard;

use Jet_Dashboard\Dashboard as Dashboard;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Data_Manager class
 */
class Data_Manager {

	/**
	 * [$jet_dashboard_config_url description]
	 * @var string
	 */
	public $jet_dashboard_config_url = 'https://api.crocoblock.com/downloads/config/jet-dashboard/%s.json';

	/**
	 * [$jet_changelog_url description]
	 * @var string
	 */
	public $jet_changelog_url = 'https://crocoblock.com/wp-content/uploads/jet-changelog/%s.json';

	/**
	 * [__construct description]
	 */
	public function __construct() {
		add_action( 'wp_ajax_jet_dashboard_debug_action', array( $this, 'jet_dashboard_debug_action' ) );
	}

	/**
	 * [get_dashboard_config description]
	 * @return [type] [description]
	 */
	public function get_dashboard_config( $key = '' ) {

		$all_config_data = get_site_transient( 'jet-dashboard-all-config-data' );
		//$all_config_data = false;

		if ( empty( $all_config_data ) ) {
			$all_config_data = $this->dashboard_config_remote_query( 'all-config' );

			if ( ! $all_config_data ) {
				return false;
			}

			// Expires in 1 day
			set_site_transient( 'jet-dashboard-all-config-data', $all_config_data, DAY_IN_SECONDS );
		}

		if ( ! empty( $key ) && isset( $all_config_data[ $key ] ) ) {
			return $all_config_data[ $key ];
		}

		return $all_config_data;
	}

	/**
	 * [get_dashboard_page_config description]
	 * @param  boolean $page [description]
	 * @return [type]        [description]
	 */
	public function get_dashboard_page_config( $page = false, $subpage = false ) {

		$dashboard_config = $this->get_dashboard_config();

		if ( ! isset( $dashboard_config['pagesConfig'] ) ) {
			return false;
		}

		$page_config = false;

		if ( is_array( $dashboard_config['pagesConfig'] ) ) {

			if ( $subpage ) {

				foreach( $dashboard_config['pagesConfig'] as $page_data ) {

					if ( $subpage === $page_data['slug'] && $page === $page_data['parent-slug'] ) {
						$page_config = $page_data;

						break;
					}
				}
			}

			if ( ! $page_config ) {
				foreach( $dashboard_config['pagesConfig'] as $page_data ) {
					if ( $page === $page_data['slug'] ) {
						$page_config = $page_data;

						break;
					}
				}
			}
		}

		return $page_config;
	}

	/**
	 * [changelog_remote_query description]
	 * @param  [type] $slug [description]
	 * @return [type]       [description]
	 */
	public function dashboard_config_remote_query( $slug ) {

		$response = wp_remote_get( sprintf( $this->jet_dashboard_config_url, $slug ), array(
			'timeout' => 60,
		) );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != '200' ) {
			return false;
		}

		$response = json_decode( $response['body'], true );

		return $response;
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
	 * [get_theme_info description]
	 * @return [type] [description]
	 */
	public function get_theme_info() {
		$style_parent_theme = wp_get_theme( get_template() );

		return apply_filters( 'jet-dashboard/data-manager/theme-info', array(
			'name'       => $style_parent_theme->get('Name'),
			'theme'      => strtolower( preg_replace('/\s+/', '', $style_parent_theme->get('Name') ) ),
			'version'    => $style_parent_theme->get('Version'),
			'author'     => $style_parent_theme->get('Author'),
			'authorSlug' => strtolower( preg_replace('/\s+/', '', $style_parent_theme->get('Author') ) ),
		) );
	}

	/**
	 * [jet_dashboard_debug_action description]
	 * @return [type] [description]
	 */
	public function get_service_action_list() {
		return array(
			array(
				'label' => 'Check Plugins Update',
				'value' => 'check-plugin-update',
			),
			array(
				'label' => 'Delete License Data',
				'value' => 'delete-license-data',
			),
			array(
				'label' => 'License Expire Check',
				'value' => 'license-expire-check',
			),
			array(
				'label' => 'Modify Tm License Data',
				'value' => 'modify-tm-license-data',
			),
			array(
				'label' => 'Reset Transient Cache',
				'value' => 'reset-transient-cache',
			),
		);
	}

	/**
	 * Proccesing subscribe form ajax
	 *
	 * @return void
	 */
	public function jet_dashboard_debug_action() {

		$data = ( ! empty( $_POST['data'] ) ) ? $_POST['data'] : false;

		if ( ! $data || ! isset( $data['action'] ) ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'code'    => 'server_error',
					'message' => $this->sys_messages['server_error'],
					'data'    => [],
				)
			);
		}

		if ( ! isset( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'jet-dashboard' ) ) {
			wp_send_json( [
				'status'  => 'error',
				'code'    => 'server_error',
				'message' => __( 'Page has expired. Please reload this page.', 'jet-dashboard' ),
				'data'    => [],
			] );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( [
				'status'  => 'error',
				'code'    => 'server_error',
				'message' => __( 'Sorry, you are not allowed to do that on this site.', 'jet-dashboard' ),
				'data'    => [],
			] );
		}

		$license_action = $data['action'];

		switch ( $license_action ) {

			case 'check-plugin-update':
				delete_site_transient( 'update_plugins' );

				wp_send_json(
					array(
						'status'  => 'success',
						'code'    => 'plugin_update_cheking',
						'message' => 'Plugins Update Checked',
						'data'    => [],
					)
				);

			break;

			case 'delete-license-data':
				Utils::set_license_data( 'license-list', [] );

				wp_send_json(
					array(
						'status'  => 'success',
						'code'    => 'license_deleted',
						'message' => 'License data has been deleted',
						'data'    => [],
					)
				);

			break;

			case 'license-expire-check':
				delete_site_transient( 'jet_dashboard_license_expire_check' );

				wp_send_json(
					array(
						'status'  => 'success',
						'code'    => 'transient_deleted',
						'message' => 'License Expire Checked',
						'data'    => [],
					)
				);

			break;

			case 'modify-tm-license-data':
				update_option( 'jet_is_modify_tm_license_data', 'false' );

				wp_send_json(
					array(
						'status'  => 'success',
						'code'    => 'transient_deleted',
						'message' => 'Tm license modified',
						'data'    => [],
					)
				);

			break;

			case 'reset-transient-cache':
				delete_site_transient( 'jet-dashboard-all-config-data' );
				delete_site_transient( 'jet_dashboard_remote_jet_plugin_list' );
				delete_site_transient( 'kava_theme_data' );
				delete_site_transient( 'update_themes' );
				delete_site_transient( 'jet_core_theme_data' );

				wp_send_json(
					array(
						'status'  => 'success',
						'code'    => 'transient_deleted',
						'message' => 'Transient Cache Deleted',
						'data'    => [],
					)
				);

			break;

			default:
				wp_send_json(
					array(
						'status'  => 'error',
						'code'    => 'action_not_found',
						'message' => 'Action Not Found',
						'data'    => [],
					)
				);
			break;
		}

		exit;
	}

}
