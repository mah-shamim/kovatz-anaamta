<?php
/**
 * WCV Dependency Checker
 *
 * Checks if a required plugin is enabled
 */

class WCV_Dependencies {

	/**
	 * Current activated plugins.
     *
	 * @var array
	 */
	private static $active_plugins;


	/**
	 * Initiate the class
	 */
	public static function init() {

		self::$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}
	}


	/**
	 * Check if WooCommerce has been activated.
	 *
	 * @return boolean
	 */
	public static function woocommerce_active_check() {
		if ( ! self::$active_plugins ) {
			self::init();
		}

		$woocommerce_dir = WP_PLUGIN_DIR . '/woocommerce';
		if ( ! file_exists( $woocommerce_dir ) ) {
			return false;
		}

		return in_array( 'woocommerce/woocommerce.php', self::$active_plugins, true );
	}
}
