<?php
/*
Plugin Name: User Role Editor
Plugin URI: https://www.role-editor.com
Description: Change/add/delete WordPress user roles and capabilities.
Version: 4.64.2
Author: Vladimir Garagulya
Author URI: https://www.role-editor.com
Text Domain: user-role-editor
Domain Path: /lang/
*/

/*
Copyright 2010-2024  Vladimir Garagulya  (email: support@role-editor.com)
*/

if ( ! function_exists( 'get_option' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    die;  // Silence is golden, direct call is prohibited
}

if ( defined( 'URE_VERSION' ) ) { 
    if ( is_admin() && ( !defined('DOING_AJAX') || !DOING_AJAX ) ) {
        if ( !class_exists('URE_Admin_Notice') ) {
            require_once( plugin_dir_path( __FILE__ ) .'includes/classes/admin-notice.php' );
        }
        new URE_Admin_Notice('warning',  "It seems that other copy of User Role Editor is active. Check if it's deactivated before activate this one.");
    }
    return;
}

define( 'URE_VERSION', '4.64.2' );
define( 'URE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'URE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'URE_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );
define( 'URE_PLUGIN_FILE', basename( __FILE__ ) );
define( 'URE_PLUGIN_FULL_PATH', __FILE__ );

require_once( URE_PLUGIN_DIR .'includes/classes/admin-notice.php' );
require_once( URE_PLUGIN_DIR.'includes/classes/base-lib.php' );
require_once( URE_PLUGIN_DIR.'includes/classes/lib.php' );

// check PHP version
$ure_required_php_version = '7.3';
$exit_msg = 'User Role Editor requires PHP '. $ure_required_php_version .' or newer. '. 
            '<a href="https://www.php.net/supported-versions.php">Please update!</a>';
if ( !URE_Lib::check_version( PHP_VERSION, $ure_required_php_version, $exit_msg, __FILE__ ) ) {
    return;
}

// check WP version
$ure_required_wp_version = '4.4';
$exit_msg = 'User Role Editor requires WordPress '. $ure_required_wp_version .' or newer. '. 
            '<a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>';
if ( !URE_Lib::check_version( get_bloginfo( 'version' ), $ure_required_wp_version, $exit_msg, __FILE__ ) ) {
    return;
}

require_once( URE_PLUGIN_DIR .'includes/loader.php' );

// Uninstall action
register_uninstall_hook( URE_PLUGIN_FULL_PATH, array('User_Role_Editor', 'uninstall') );

$GLOBALS['user_role_editor'] = User_Role_Editor::get_instance();
