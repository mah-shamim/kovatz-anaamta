<?php
/**
 * Temporary Loging Without Password Uninstall
 *
 * Delete all temporary user on uninstall of plugin
 *
 * @package Temporary Login Without Password
 *
 * @since 1.0.0
 *
 * @modified 1.6.13
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = maybe_unserialize( get_option( 'tlwp_settings' ) );

$should_delete_data = isset( $settings['delete_data_on_uninstall'] ) ? $settings['delete_data_on_uninstall'] : 0;

if ( 1 == $should_delete_data ) {

	include_once 'includes/class-wp-temporary-login-without-password-common.php';

	// Delete data
	Wp_Temporary_Login_Without_Password_Common::delete_plugin_data_on_uninstall();
}
