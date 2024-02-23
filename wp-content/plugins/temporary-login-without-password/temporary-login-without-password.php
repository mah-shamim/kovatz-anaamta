<?php
/**
 * Plugin Name:       Temporary Login Without Password
 * Plugin URI:        http://www.storeapps.org/create-secure-login-without-password-for-wordpress/
 * Description:       Create a temporary login link with any role using which one can access to your sytem without username and password for limited period of time.
 * Version:           1.8.3
 * Author:            StoreApps
 * Author URI:        https://www.storeapps.org/
 * Requires at least: 3.0.1
 * Tested up to:      6.4.1
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       temporary-login-without-password
 * Domain Path:       /languages/
 * Copyright (c)      2016-2023 StoreApps. All right reserved
 *
 * @package Temporary Login Without Password
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define constants
 */
define( 'WTLWP_FEEDBACK_VERSION', '1.2.8' );
define( 'WTLWP_TRACKER_VERSION', '1.2.5' );
define( 'WTLWP_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'WTLWP_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );
define( 'WTLWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'WTLWP_PLUGIN_FILE' ) ) {
    define( 'WTLWP_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'WTLWP_PLUGIN_VERSION' ) ) {
    define( 'WTLWP_PLUGIN_VERSION', get_wtlwp_plugin_version() );
}

/**
 * Function to return plugin data.
 *
 * @since 1.8.0
 */
function get_wtlwp_plugin_data() {
    if ( ! function_exists( 'get_plugin_data' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    return get_plugin_data( WTLWP_PLUGIN_FILE );
}

/**
 * Function to return current plugin version.
 *
 * @since 1.8.0
 */
function get_wtlwp_plugin_version() {
    $plugin_data    = get_wtlwp_plugin_data();
    $plugin_version = $plugin_data['Version'];

    return $plugin_version;
}

/**
 * Deactivate Temporary Login Without Password
 *
 * @since 1.0
 */
function wp_deactivate_temporary_login_without_password() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-temporary-login-without-password-deactivator.php';
	Wp_Temporary_Login_Without_Password_Deactivator::deactivate();
}

/**
 * Activate Temporary Login Without Password
 *
 * @since 1.0
 */
function wp_activate_temporary_login_without_password() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-temporary-login-without-password-activator.php';
	Wp_Temporary_Login_Without_Password_Activator::activate();
	add_option( 'tlwp_do_activation_redirect', true );
}

register_deactivation_hook( __FILE__, 'wp_deactivate_temporary_login_without_password' );
register_activation_hook( __FILE__, 'wp_activate_temporary_login_without_password' );

add_action( 'admin_init', 'tlwp_redirect' );

if ( ! function_exists( 'tlwp_redirect' ) ) {

	function tlwp_redirect() {

		// Check if it is multisite and the current user is in the network administrative interface. e.g. `/wp-admin/network/`
		if( is_multisite() && is_network_admin() ) {
			return;
		}

		if ( get_option( 'tlwp_do_activation_redirect', false ) ) {
			delete_option( 'tlwp_do_activation_redirect' );
			wp_redirect( 'users.php?page=wp-temporary-login-without-password' );
		}
	}
}
// Include main class file.
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-temporary-login-without-password.php';

/**
 * Initialize
 *
 * @since 1.0
 */
function run_wp_temporary_login_without_password() {
	$plugin = new Wp_Temporary_Login_Without_Password();
	$plugin->run();
}

run_wp_temporary_login_without_password();
