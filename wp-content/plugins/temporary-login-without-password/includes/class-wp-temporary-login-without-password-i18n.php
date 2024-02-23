<?php
/**
 * Localiztion
 *
 * @package Temporary Login Without Password
 */

/**
 * Define the internationalization functionality
 *
 * Class Wp_Temporary_Login_Without_Password_I18n
 */
class Wp_Temporary_Login_Without_Password_I18n {

	/**
	 * Load plugin text domain
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'temporary-login-without-password', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

}
