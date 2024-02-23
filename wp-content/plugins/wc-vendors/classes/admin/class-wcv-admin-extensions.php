<?php
/**
 * WC Vendors Extensions Page
 *
 * @author   WC Vendors
 * @category Admin
 * @package  WCVendors/Admin
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Admin_Addons Class.
 */
class WCVendors_Admin_Extensions {

	/**
	 * Ouput the extensions page
	 */
	public static function output() {
		$plugin_installer = ( new WCV_Plugin_Installer() )->get_instance();
		$plugin_installer->set_exclude_plugins( array( 'wc-vendors-pro', 'woocommerce' ) );
		include_once __DIR__ . '/views/html-admin-page-extensions.php';
	}
}
