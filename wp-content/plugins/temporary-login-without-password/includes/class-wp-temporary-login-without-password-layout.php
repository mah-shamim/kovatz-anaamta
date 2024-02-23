<?php
/**
 * Plugin Layout Class
 *
 * @package Temporary Login Without Password
 */

/**
 * Manage Plugin Layout.
 *
 * Class Wp_Temporary_Login_Without_Password_Layout
 */
class Wp_Temporary_Login_Without_Password_Layout {

	/**
	 * Create footer headings.
	 *
	 * @return string
	 * @since 1.0
	 *
	 */
	public static function prepare_header_footer_row() {

		$row = '<tr class="bg-gray-100 text-sm text-left leading-4 text-gray-500 tracking-wider border-b border-t border-gray-200">';

		$row .= '<th class="p-4 font-medium" colspan="2">' . __( 'Users', 'temporary-login-without-password' ) . '</th>';
		$row .= '<th class="p-4 font-medium">' . __( 'Role', 'temporary-login-without-password' ) . '</th>';
		$row .= '<th class="p-4 font-medium">' . __( 'Last Logged In', 'temporary-login-without-password' ) . '</th>';
		$row .= '<th class="p-4 font-medium">' . __( 'Count', 'temporary-login-without-password' ) . '</th>';
		$row .= '<th class="p-4 font-medium">' . __( 'Expiry', 'temporary-login-without-password' ) . '</th>';
		$row .= '<th class="p-4 font-medium">' . __( 'Actions', 'temporary-login-without-password' ) . '</th></tr>';

		return $row;
	}

	/**
	 * Prepare empty user row.
	 *
	 * @return string
	 * @since 1.0
	 *
	 */
	public static function prepare_empty_user_row() {

		$row = '';

		$row .= '<tr class="tempadmin-single-user-row tempadmin-empty-users-row standard">';
		$row .= '<td colspan="6 pl-4 py-2 bg-white border-b border-gray-200 text-sm leading-5 text-gray-600">';
		$row .= '<span class="description">' . __( 'You have not created any temporary logins yet.', 'temporary-login-without-password' ) . '</span>';
		$row .= '</td>';
		$row .= '</tr>';

		return $row;
	}

	/**
	 * Prepare single user row
	 *
	 * @param WP_User|int $user WP_User object.
	 *
	 * @return string
	 * @since 1.0
	 *
	 */
	public static function prepare_single_user_row( $user = OBJECT ) {
		global $wpdb;
		if ( is_numeric( $user ) && ! is_object( $user ) ) {
			$user = get_user_by( 'id', $user );
		}

		$expire          = get_user_meta( $user->ID, '_wtlwp_expire', true ); // phpcs:ignore
		$last_login_time = get_user_meta( $user->ID, '_wtlwp_last_login', true ); // phpcs:ignore
		$total_count     = get_user_meta( $user->ID, '_wtlwp_login_count', true ); // phpcs:ignore

		// If we don't have earlier data and
		// if $last_login_time is not empty which means user already logged in at least one time in past
		if ( empty( $total_count ) ) {
			$total_count = 0;
			if ( ! empty( $last_login_time ) ) {
				$total_count = 1;
			}
		}

		$last_login_str = __( 'Not yet logged in', 'temporary-login-without-password' );
		if ( ! empty( $last_login_time ) ) {
			$last_login_str = Wp_Temporary_Login_Without_Password_Common::time_elapsed_string( $last_login_time, true );
		}

		$wtlwp_status = 'Active';
		if ( Wp_Temporary_Login_Without_Password_Common::is_login_expired( $user->ID ) ) {
			$wtlwp_status = 'Expired';
		}

		if ( is_multisite() && is_super_admin( $user->ID ) ) {
			$user_role = __( 'Super Admin', 'temporary-login-without-password' );
		} else {
			$capabilities = $user->{$wpdb->prefix . 'capabilities'};
			$wp_roles     = new WP_Roles();
			$user_role    = '';
			foreach ( $wp_roles->role_names as $role => $name ) {
				if ( array_key_exists( $role, $capabilities ) ) {
					$user_role = $name;
				}
			}
		}

		$user_details = '<div><span>';
		if ( ( esc_attr( $user->first_name ) ) ) {
			$user_details .= '<span>' . esc_attr( $user->first_name ) . '</span>';
		}

		if ( ( esc_attr( $user->last_name ) ) ) {
			$user_details .= '<span> ' . esc_attr( $user->last_name ) . '</span>';
		}

		$user_details .= "  (<span class='wtlwp-user-login'>" . esc_attr( $user->user_login ) . ')</span><br />';

		if ( ( esc_attr( $user->user_email ) ) ) {
			$user_details .= '<p class="inline-block pt-1 font-medium text-gray-500">' . esc_attr( $user->user_email ) . '</p> <br />';
		}

		$user_details .= '</span></div>';

		$row = '';

		$row .= '<tr id="single-user-' . absint( $user->ID ) . '" class="tempadmin-single-user-row pl-4 py-3 border-b border-gray-200 text-sm leading-5 text-gray-600 bg-white">';
		$row .= '<td class="email column-details pl-4 py-2 border-b border-gray-200 text-sm leading-5 text-gray-600" colspan="2">' . $user_details . '</td>';
		$row .= '<td class="wtlwp-token column-role pl-4 py-2 border-b border-gray-200 text-sm leading-5 text-gray-600">' . esc_attr( $user_role ) . '</td>';
		$row .= '<td class="wtlwp-token column-last-login pl-4 py-2 border-b border-gray-200 text-sm leading-5 text-gray-600">' . esc_attr( $last_login_str ) . '</td>';
		$row .= '<td class="wtlwp-token column-total-count pl-4 py-2 border-b border-gray-200 text-sm leading-5 text-gray-600">' . esc_attr( $total_count ) . '</td>';

		$row .= '<td class="expired column-expired wtlwp-status-' . strtolower( $wtlwp_status ) . ' pl-4 py-2 border-b border-gray-200 text-sm leading-5 text-gray-600">';
		if ( ! empty( $expire ) ) {
			$row .= Wp_Temporary_Login_Without_Password_Common::time_elapsed_string( $expire );
		}
		$row .= '</td>';
		$row .= '<td class="wtlwp-token column-email pl-4 py-2 border-b border-gray-200 text-sm leading-5 text-gray-600">' . self::prepare_row_actions( $user, $wtlwp_status ) . '</td>';
		$row .= '</tr>';

		return $row;
	}

	/**
	 * Prepare user actions row.
	 *
	 * @param WP_User $user WP_User object.
	 * @param string $wtlwp_status Current wtlwp_status.
	 *
	 * @return string
	 * @since 1.0
	 *
	 */
	public static function prepare_row_actions( $user, $wtlwp_status ) {

		$is_active = ( 'active' === strtolower( $wtlwp_status ) ) ? true : false;
		$user_id   = $user->ID;
		$email     = $user->user_email;

		$delete_login_url     = Wp_Temporary_Login_Without_Password_Common::get_manage_login_url( $user_id, 'delete' );
		$update_login_url     = add_query_arg(
			array(
				'page'    => 'wp-temporary-login-without-password',
				'user_id' => $user_id,
				'action'  => 'update',
			), admin_url( 'users.php' )
		);

		$disable_login_url    = Wp_Temporary_Login_Without_Password_Common::get_manage_login_url( $user_id, 'disable' );
		$enable_login_url     = Wp_Temporary_Login_Without_Password_Common::get_manage_login_url( $user_id, 'enable' );
		$temporary_login_link = Wp_Temporary_Login_Without_Password_Common::get_login_url( $user_id );
		$mail_to_link         = Wp_Temporary_Login_Without_Password_Common::generate_mailto_link( $email, $temporary_login_link );

		$action_row = '<div class="actions">';

		if ( $is_active ) {
			$action_row .= "<span class='disable mr-1'><a title='" . __( 'Disable', 'temporary-login-without-password' ) . "' class='text-green-600 hover:text-green-600' href='{$disable_login_url}'><span class='dashicons dashicons-unlock'></span></a></span>";
		} else {
			$action_row .= "<span class='enable mr-1'><a title='" . __( 'Reactivate for one day', 'temporary-login-without-password' ) . "' class='text-gray-600 hover:text-gray-600' href='{$enable_login_url}'><span class='dashicons dashicons-lock'></a></span></span>";
		}

		$action_row .= "<span class='delete tlwp-delete mr-1'><a title='" . __( 'Delete', 'temporary-login-without-password' ) . "' class='text-red-600 hover:text-red-600' href='{$delete_login_url}'><span class='dashicons dashicons-no'></span></a></span>";
		$action_row .= "<span class='edit mr-1'><a title='" . __( 'Edit', 'temporary-login-without-password' ) . "' href='{$update_login_url}'><span class='dashicons dashicons-edit'></span></a></span>";

		// Shows these link only if temporary login active.
		if ( $is_active ) {
			$action_row .= "<span class='email mr-1'><a title='" . __( 'Email login link', 'temporary-login-without-password' ) . "' class='text-yellow-600 hover:text-yellow-600' href='{$mail_to_link}'><span class='dashicons dashicons-email'></span></a></span>";
			$action_row .= "<span class='copy'><span id='text-{$user_id}' class='text-indigo-600 dashicons dashicons-admin-links wtlwp-copy-to-clipboard' title='" . __( 'Copy login link', 'temporary-login-without-password' ) . "' data-clipboard-text='{$temporary_login_link}'></span></span>";
			$action_row .= "<span id='copied-text-{$user_id}' class='copied-text-message'></span>";
		}

		$action_row .= '</div>';

		return $action_row;
	}

}
