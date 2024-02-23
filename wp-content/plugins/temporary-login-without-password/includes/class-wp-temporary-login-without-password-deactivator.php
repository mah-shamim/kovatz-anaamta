<?php
/**
 * Deactivator Class
 *
 * @package Temporary Login Without Password
 */

/**
 * Class Wp_Temporary_Login_Without_Password_Deactivator
 *
 * @package Temporary Login Without Password
 */
class Wp_Temporary_Login_Without_Password_Deactivator {

	/**
	 * Deactivate Plugin.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {

		/**
		 * Steps
		 *
		 * - Get all temporary users data
		 * - Set role to '' (null) for temporary users
		 * - Backup all temporary users data into temporary_logins_data option
		 */
		$temporary_logins = Wp_Temporary_Login_Without_Password_Common::get_temporary_logins();

		$temporary_logins_data = array();
		if ( count( $temporary_logins ) > 0 ) {
			foreach ( $temporary_logins as $user ) {
				if ( $user instanceof WP_User ) {
					$temporary_logins_data[ $user->ID ] = $user->roles[0];
					wp_update_user( array(
						'ID'   => $user->ID,
						'role' => '',
					) );  //Downgrade role to none. So, user won't be able to login.
				}
			}
		}

		$add = 'yes';

		// Backup temporary users's data into temporary_logins_data option for future use.
		update_option( 'temporary_logins_data', $temporary_logins_data, $add );
	}

}
