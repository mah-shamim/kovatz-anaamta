<?php

/**
 * Class Wp_Temporary_Login_Without_Password_Common
 */
class Wp_Temporary_Login_Without_Password_Common {

	/**
	 * Create a ranadom username for the temporary user
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public static function create_username( $data ) {

		$first_name = isset( $data['user_first_name'] ) ? $data['user_first_name'] : '';
		$last_name  = isset( $data['user_last_name'] ) ? $data['user_last_name'] : '';
		$email      = isset( $data['user_email'] ) ? $data['user_email'] : '';

		$name = '';
		if ( ! empty( $first_name ) || ! empty( $last_name ) ) {
			$name = str_replace( array( '.', '+' ), '', strtolower( trim( $first_name . $last_name ) ) );
		} else {
			if ( ! empty( $email ) ) {
				$explode = explode( '@', $email );
				$name    = str_replace( array( '.', '+' ), '', $explode[0] );
			}
		}

		if ( username_exists( $name ) ) {
			$name = $name . substr( uniqid( '', true ), - 6 );
		}

		$username = sanitize_user( $name, true );

		/**
		 * We are generating WordPress username from First Name & Last Name fields.
		 * When First Name or Last Name comes with non latin words, generated username
		 * is non latin and sanitize_user function discrad it and user is not being
		 * generated.
		 *
		 * To avoid this, if this situation occurs, we are generating random username
		 * for this user.
		 */
		if ( empty( $username ) ) {
			$username = self::random_username();
		}

		return sanitize_user( $username, true );
	}

	/**
	 * Create a new user
	 *
	 * @param array $data
	 *
	 * @return array|int|WP_Error
	 */
	public static function create_new_user( $data ) {

		if ( false === Wp_Temporary_Login_Without_Password_Common::can_manage_wtlwp() ) {
			return 0;
		}

		$result = array(
			'error' => true
		);

		$expiry_option = ! empty( $data['expiry'] ) ? $data['expiry'] : 'day';
		$date          = ! empty( $data['custom_date'] ) ? $data['custom_date'] : '';

		$password    = Wp_Temporary_Login_Without_Password_Common::generate_password();
		$username    = Wp_Temporary_Login_Without_Password_Common::create_username( $data );
		$first_name  = isset( $data['user_first_name'] ) ? sanitize_text_field( $data['user_first_name'] ) : '';
		$last_name   = isset( $data['user_last_name'] ) ? sanitize_text_field( $data['user_last_name'] ) : '';
		$email       = isset( $data['user_email'] ) ? sanitize_email( $data['user_email'] ) : '';
		$role        = ! empty( $data['role'] ) ? $data['role'] : 'subscriber';
		$redirect_to = ! empty( $data['redirect_to'] ) ? sanitize_text_field( $data['redirect_to'] ) : 'wp_dashboard';
		$user_args   = array(
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'user_login' => $username,
			'user_pass'  => $password,
			'user_email' => sanitize_email( $email ),
			'role'       => $role,
		);

		$user_id = wp_insert_user( $user_args );

		if ( is_wp_error( $user_id ) ) {
			$code = $user_id->get_error_code();

			$result['errcode'] = $code;
			$result['message'] = $user_id->get_error_message( $code );

		} else {

			if ( is_multisite() && ! empty( $data['super_admin'] ) && 'on' === $data['super_admin'] ) {

				// Grant super admin access to this temporary users
				grant_super_admin( $user_id );

				// Now, add this user to all sites
				$sites = get_sites( array( 'deleted' => '0' ) );

				if ( ! empty( $sites ) && count( $sites ) > 0 ) {
					foreach ( $sites as $site ) {
						// If user is not already member of blog? Add into this blog
						if ( ! is_user_member_of_blog( $user_id, $site->blog_id ) ) {
							add_user_to_blog( $site->blog_id, $user_id, 'administrator' );
						}
					}
				}
			}

			update_user_meta( $user_id, '_wtlwp_user', true );
			update_user_meta( $user_id, '_wtlwp_created', Wp_Temporary_Login_Without_Password_Common::get_current_gmt_timestamp() );
			update_user_meta( $user_id, '_wtlwp_expire', Wp_Temporary_Login_Without_Password_Common::get_user_expire_time( $expiry_option, $date ) );
			update_user_meta( $user_id, '_wtlwp_token', Wp_Temporary_Login_Without_Password_Common::generate_wtlwp_token( $user_id ) );
			update_user_meta( $user_id, '_wtlwp_redirect_to', $redirect_to );

			update_user_meta( $user_id, 'show_welcome_panel', 0 );

			//set locale
			$locale = ! empty( $data['locale'] ) ? $data['locale'] : 'en_US';
			update_user_meta( $user_id, 'locale', $locale );

			$result['error']   = false;
			$result['user_id'] = $user_id;
		}

		return $result;

	}

	/**
	 * update user
	 *
	 * @param array $data
	 *
	 * @return array|int|WP_Error
	 */
	public static function update_user( $user_id = 0, $data = array() ) {

		if ( false === Wp_Temporary_Login_Without_Password_Common::can_manage_wtlwp() || ( 0 === $user_id ) ) {
			return 0;
		}

		$expiry_option = ! empty( $data['expiry'] ) ? $data['expiry'] : 'day';
		$date          = ! empty( $data['custom_date'] ) ? $data['custom_date'] : '';

		$first_name  = isset( $data['user_first_name'] ) ? sanitize_text_field( $data['user_first_name'] ) : '';
		$last_name   = isset( $data['user_last_name'] ) ? sanitize_text_field( $data['user_last_name'] ) : '';
		$redirect_to = isset( $data['redirect_to'] ) ? sanitize_text_field( $data['redirect_to'] ) : '';
		$role        = ! empty( $data['role'] ) ? $data['role'] : 'subscriber';
		$user_args   = array(
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'role'       => $role,
			'ID'         => $user_id //require for update_user
		);

		$user_id = wp_update_user( $user_args );

		if ( is_wp_error( $user_id ) ) {
			$code = $user_id->get_error_code();

			return array(
				'error'   => true,
				'errcode' => $code,
				'message' => $user_id->get_error_message( $code ),
			);
		}


		if ( is_multisite() && ! empty( $data['super_admin'] ) && 'on' === $data['super_admin'] ) {
			grant_super_admin( $user_id );
		}

		update_user_meta( $user_id, '_wtlwp_updated', Wp_Temporary_Login_Without_Password_Common::get_current_gmt_timestamp() );
		update_user_meta( $user_id, '_wtlwp_expire', Wp_Temporary_Login_Without_Password_Common::get_user_expire_time( $expiry_option, $date ) );
		update_user_meta( $user_id, '_wtlwp_redirect_to', $redirect_to );

		//set locale
		$locale = ! empty( $data['locale'] ) ? $data['locale'] : 'en_US';
		update_user_meta( $user_id, 'locale', $locale );

		return $user_id;

	}


	/**
	 * get the expiry duration
	 *
	 * @param string $key
	 *
	 * @return boolean|array
	 * @since 1.0.0
	 *
	 * @updated: 1.5.11
	 *
	 */
	public static function get_expiry_options() {

		$expiry_options = array(
			'hour'                 => array( 'group' => 'from_now', 'group_name' => __( 'From Now', 'temporary-login-without-password' ), 'label' => __( 'One Hour', 'temporary-login-without-password' ), 'timestamp' => HOUR_IN_SECONDS, 'order' => 5 ),
			'3_hours'              => array( 'group' => 'from_now', 'group_name' => __( 'From Now', 'temporary-login-without-password' ), 'label' => __( 'Three Hours', 'temporary-login-without-password' ), 'timestamp' => HOUR_IN_SECONDS * 3, 'order' => 10 ),
			'day'                  => array( 'group' => 'from_now', 'group_name' => __( 'From Now', 'temporary-login-without-password' ), 'label' => __( 'One Day', 'temporary-login-without-password' ), 'timestamp' => DAY_IN_SECONDS, 'order' => 15 ),
			'3_days'               => array( 'group' => 'from_now', 'group_name' => __( 'From Now', 'temporary-login-without-password' ), 'label' => __( 'Three Days', 'temporary-login-without-password' ), 'timestamp' => DAY_IN_SECONDS * 3, 'order' => 20 ),
			'week'                 => array( 'group' => 'from_now', 'group_name' => __( 'From Now', 'temporary-login-without-password' ), 'label' => __( 'One Week', 'temporary-login-without-password' ), 'timestamp' => WEEK_IN_SECONDS, 'order' => 25 ),
			'month'                => array( 'group' => 'from_now', 'group_name' => __( 'From Now', 'temporary-login-without-password' ), 'label' => __( 'One Month', 'temporary-login-without-password' ), 'timestamp' => MONTH_IN_SECONDS, 'order' => 30 ),
			'hour_after_access'    => array( 'group' => 'after_access', 'group_name' => __( 'After Access', 'temporary-login-without-password' ), 'label' => __( 'One Hour', 'temporary-login-without-password' ), 'expiry_label' => __( '1 hour after access', 'temporary-login-without-password' ), 'timestamp' => HOUR_IN_SECONDS, 'order' => 6 ),
			'3_hours_after_access' => array( 'group' => 'after_access', 'group_name' => __( 'After Access', 'temporary-login-without-password' ), 'label' => __( 'Three Hours', 'temporary-login-without-password' ), 'expiry_label' => __( '3 hours after access', 'temporary-login-without-password' ), 'timestamp' => HOUR_IN_SECONDS * 3, 'order' => 11 ),
			'day_after_access'     => array( 'group' => 'after_access', 'group_name' => __( 'After Access', 'temporary-login-without-password' ), 'label' => __( 'One Day', 'temporary-login-without-password' ), 'expiry_label' => __( '1 day after access', 'temporary-login-without-password' ), 'timestamp' => DAY_IN_SECONDS, 'order' => 16 ),
			'3_days_after_access'  => array( 'group' => 'after_access', 'group_name' => __( 'After Access', 'temporary-login-without-password' ), 'label' => __( 'Three Days', 'temporary-login-without-password' ), 'expiry_label' => __( '3 days after access', 'temporary-login-without-password' ), 'timestamp' => DAY_IN_SECONDS * 3, 'order' => 21 ),
			'week_after_access'    => array( 'group' => 'after_access', 'group_name' => __( 'After Access', 'temporary-login-without-password' ), 'label' => __( 'One Week', 'temporary-login-without-password' ), 'expiry_label' => __( '1 week after access', 'temporary-login-without-password' ), 'timestamp' => WEEK_IN_SECONDS, 'order' => 26 ),
			'month_after_access'   => array( 'group' => 'after_access', 'group_name' => __( 'After Access', 'temporary-login-without-password' ), 'label' => __( 'One Month', 'temporary-login-without-password' ), 'expiry_label' => __( '1 month after access', 'temporary-login-without-password' ), 'timestamp' => MONTH_IN_SECONDS, 'order' => 31 ),
			'custom_date'          => array( 'group' => 'custom', 'group_name' => __( 'Custom', 'temporary-login-without-password' ), 'label' => __( 'Custom Date', 'temporary-login-without-password' ), 'timestamp' => 0, 'order' => 35 ),
		);

		// Now, one can add their own options.
		$expiry_options = apply_filters( 'tlwp_expiry_options', $expiry_options );

		// Get Order options to sort $expiry_options array by it's array
		foreach ( $expiry_options as $key => $options ) {
			$expiry_options[ $key ]['order']        = ! empty( $options['order'] ) ? $options['order'] : 100;
			$expiry_options[ $key ]['group']        = ! empty( $options['group'] ) ? $options['group'] : __( 'from_now', '' );
			$expiry_options[ $key ]['group_name']   = ! empty( $options['group_name'] ) ? $options['group_name'] : __( 'From Now', '' );
			$expiry_options[ $key ]['expiry_label'] = ! empty( $options['expiry_label'] ) ? $options['expiry_label'] : '';

			$orders[ $key ] = ! empty( $options['order'] ) ? $options['order'] : 100;
		}

		// Sort $expiry_options array by it's order value
		array_multisort( $orders, SORT_ASC, $expiry_options );

		return $expiry_options;
	}

	/**
	 * Get Expire duration dropdown
	 *
	 * @param string $selected
	 *
	 * @update: 1.5.11
	 */
	static function get_expiry_duration_html( $selected = '', $excluded = array() ) {

		$r = '';

		$expiry_options = self::get_expiry_options();

		if ( is_array( $expiry_options ) && count( $expiry_options ) > 0 ) {

			$grouped_expiry_options = $groups = array();
			foreach ( $expiry_options as $key => $option ) {

				// We don't need to add option into dropdown if it's excluded
				if ( ! empty( $excluded ) && in_array( $key, $excluded ) ) {
					continue;
				}

				$groups[ $option['group'] ] = $option['group_name'];

				$grouped_expiry_options[ $option['group'] ][ $key ] = $option;
			}

			foreach ( $grouped_expiry_options as $group => $options ) {

				$r .= "\n\t<optgroup label='$groups[$group]'>";

				foreach ( $options as $key => $option ) {

					$label = ! empty( $option['label'] ) ? $option['label'] : '';

					$r .= "\n\t<option ";

					if ( $selected === $key ) {
						$r .= "selected='selected' ";
					}

					$r .= "value='" . esc_attr( $key ) . "'>$label</option>";
				}

				$r .= "</optgroup>";

			}

		}

		echo $r;

	}

	/**
	 * Generate new password for user
	 *
	 * @param int $length
	 * @param bool $special_chars
	 * @param false $extra_special_chars
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 *
	 * @modified 1.6.15 changed function definition
	 */
	public static function generate_password( $length = 15, $special_chars = true, $extra_special_chars = false ) {
		/**
		 * This is the exact same function definition as wp_generate_password() without
		 * allowing people to filter it.
		 */
		$length = absint( $length );

		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if ( $special_chars ) {
			$chars .= '!@#$%^&*()';
		}
		if ( $extra_special_chars ) {
			$chars .= '-_ []{}<>~`+=,.;:/?|';
		}

		$password = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$password .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
		}

		return $password;
	}

	/**
	 * Get the expiration time based on string
	 *
	 * @param string $expiry_option
	 * @param string $date
	 *
	 * @return false|float|int
	 * @since 1.0.0
	 *
	 */
	public static function get_user_expire_time( $expiry_option = 'day', $date = '' ) {

		$expiry_options = self::get_expiry_options();

		$expiry_option = in_array( $expiry_option, array_keys( $expiry_options ) ) ? $expiry_option : 'day';

		if ( 'custom_date' === $expiry_option ) {

			// For the custom_date option we need to simply expire login at particular date
			// So, we don't need to do addition in the current timestamp
			$current_timestamp = 0;
			$timestamp         = strtotime( $date );
		} elseif ( strpos( $expiry_option, '_after_access' ) > 0 ) {
			return $expiry_option;
		} else {

			// We need current gmt timestamp and from now we need to expire temporary login
			// after specified time. So, we need to add into current timestamp
			$current_timestamp = self::get_current_gmt_timestamp();
			$timestamp         = $expiry_options[ $expiry_option ]['timestamp'];
		}

		return $current_timestamp + floatval( $timestamp );

	}

	/**
	 * Get current GMT date time
	 *
	 * @return false|int
	 * @since 1.0
	 *
	 */
	public static function get_current_gmt_timestamp() {
		return strtotime( gmdate( 'Y-m-d H:i:s', time() ) );

	}

	/**
	 * Get Temporary Logins
	 *
	 * @param string $role
	 *
	 * @return array|bool
	 * @since 1.0
	 *
	 */
	public static function get_temporary_logins( $role = '' ) {

		$args = array(
			'fields'     => 'all',
			'meta_key'   => '_wtlwp_expire',
			'order'      => 'DESC',
			'orderby'    => 'meta_value',
			'meta_query' => array(
				0 => array(
					'key'   => '_wtlwp_user',
					'value' => 1,
				),
			),
		);

		if ( ! empty( $role ) ) {
			$args['role'] = $role;
		}

		$users = new WP_User_Query( $args );

		$users_data = $users->get_results();

		return $users_data;

	}

	/**
	 * Format time string
	 *
	 * @param int $stamp
	 * @param string $type
	 *
	 * @return false|string
	 * @since 1.0
	 *
	 */
	public static function format_date_display( $stamp = 0, $type = 'date_format' ) {

		$type_format = 'date_format';
		if ( 'date_format' === $type ) {
			$type_format = get_option( 'date_format' );
		} elseif ( 'time_format' === $type ) {
			$type_format = get_option( 'time_format' );
		}

		$timezone = get_option( 'timezone_string' );

		if ( empty( $timezone ) ) {
			return date( $type_format, $stamp );
		}

		$date = new DateTime( '@' . $stamp );

		$date->setTimezone( new DateTimeZone( $timezone ) );

		return $date->format( $type_format );

	}

	/**
	 * Get Redirection link
	 *
	 * @param array $result
	 *
	 * @return bool|string
	 * @since 1.0
	 *
	 */
	public static function get_redirect_link( $result = array() ) {

		if ( empty( $result ) ) {
			return false;
		}

		$base_url = menu_page_url( 'wp-temporary-login-without-password', false );

		if ( empty( $base_url ) ) {
			return false;
		}

		$query_string = '';
		if ( ! empty( $result['status'] ) ) {
			if ( 'success' === $result['status'] ) {
				$query_string .= '&wtlwp_success=1';
			} elseif ( 'error' === $result['status'] ) {
				$query_string .= '&wtlwp_error=1';
			}
		}

		if ( ! empty( $result['message'] ) ) {
			$query_string .= '&wtlwp_message=' . $result['message'];
		}

		if ( ! empty( $result['tab'] ) ) {
			$query_string .= '&tab=' . $result['tab'];
		}

		$redirect_link = $base_url . $query_string;

		return $redirect_link;

	}

	/**
	 * Can user have permission to manage temporary logins?
	 *
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public static function can_manage_wtlwp( $user_id = 0 ) {

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		if ( empty( $user_id ) ) {
			return false;
		}

		// Don't give manage temporary users permission to temporary user
		$check = get_user_meta( $user_id, '_wtlwp_user', true );

		return ! empty( $check ) ? false : true;

	}

	/**
	 * Check if temporary login expired
	 *
	 * @param int $user_id
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public static function is_login_expired( $user_id = 0 ) {

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		if ( empty( $user_id ) ) {
			return false;
		}

		$expire = get_user_meta( $user_id, '_wtlwp_expire', true );

		return ! empty( $expire ) && is_numeric( $expire ) && self::get_current_gmt_timestamp() >= floatval( $expire ) ? true : false;

	}

	/**
	 * Generate Temporary Login Token
	 *
	 * @param $user_id
	 *
	 * @return false|string
	 *
	 * @since 1.0.0
	 *
	 * @modified 1.6.15 Improved security
	 */
	public static function generate_wtlwp_token( $user_id ) {
		$byte_length = 64;

		if ( function_exists( 'random_bytes' ) ) {
			try {
				return bin2hex( random_bytes( $byte_length ) ); // phpcs:ignore
			} catch ( \Exception $e ) {
			}
		}

		if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
			$crypto_strong = false;

			$bytes = openssl_random_pseudo_bytes( $byte_length, $crypto_strong );
			if ( true === $crypto_strong ) {
				return bin2hex( $bytes );
			}
		}

		// Fallback
		$str  = $user_id . microtime() . uniqid( '', true );
		$salt = substr( md5( $str ), 0, 32 );

		return hash( "sha256", $str . $salt );
	}

	/**
	 * Get valid temporary user based on token
	 *
	 * @param string $token
	 * @param string $fields
	 *
	 * @return array|bool
	 * @since 1.0
	 *
	 */
	public static function get_valid_user_based_on_wtlwp_token( $token = '', $fields = 'all' ) {
		if ( empty( $token ) ) {
			return false;
		}

		$args = array(
			'fields'     => $fields,
			'meta_key'   => '_wtlwp_expire',
			'order'      => 'DESC',
			'orderby'    => 'meta_value',
			'meta_query' => array(
				0 => array(
					'key'     => '_wtlwp_token',
					'value'   => sanitize_text_field( $token ),
					'compare' => '=',
				),
			),
		);

		$users = new WP_User_Query( $args );

		$users_data = $users->get_results();
		if ( empty( $users_data ) ) {
			return false;
		}

		foreach ( $users_data as $key => $user ) {
			$expire = get_user_meta( $user->ID, '_wtlwp_expire', true );

			if ( is_string( $expire ) && strpos( $expire, '_after_access' ) ) {
				$expiry_options = self::get_expiry_options();
				$timestamp      = ! empty( $expiry_options[ $expire ] ) ? $expiry_options[ $expire ]['timestamp'] : 0;
				$expire         = self::get_current_gmt_timestamp() + $timestamp;
				update_user_meta( $user->ID, '_wtlwp_expire', $expire );
			} elseif ( $expire <= self::get_current_gmt_timestamp() ) {
				unset( $users_data[ $key ] );
			}
		}

		return $users_data;

	}

	/**
	 * Checks whether user is valid temporary user
	 *
	 * @param int $user_id
	 * @param bool $check_expiry
	 *
	 * @return bool
	 */
	public static function is_valid_temporary_login( $user_id = 0, $check_expiry = true ) {

		if ( empty( $user_id ) ) {
			return false;
		}

		$check = get_user_meta( $user_id, '_wtlwp_user', true );

		if ( ! empty( $check ) && $check_expiry ) {
			$check = ! ( self::is_login_expired( $user_id ) );
		}

		return ! empty( $check ) ? true : false;

	}

	/**
	 * Get temporary login manage url
	 *
	 * @param $user_id
	 * @param string $action
	 *
	 * @return string
	 * @since 1.0
	 *
	 */
	public static function get_manage_login_url( $user_id, $action = '' ) {

		if ( empty( $user_id ) || empty( $action ) ) {
			return '';
		}

		$base_url = menu_page_url( 'wp-temporary-login-without-password', false );
		$args     = array();

		$valid_actions = array( 'disable', 'enable', 'delete', 'update' );
		if ( in_array( $action, $valid_actions ) ) {
			$args = array(
				'wtlwp_action' => $action,
				'user_id'      => $user_id,
			);
		}

		$manage_login_url = '';
		if ( ! empty( $args ) ) {
			$base_url         = add_query_arg( $args, trailingslashit( $base_url ) );
			$manage_login_url = wp_nonce_url( $base_url, 'manage-temporary-login_' . $user_id, 'manage-temporary-login' );
		}

		return $manage_login_url;

	}

	/**
	 * Get temporary login url
	 *
	 * @param $user_id
	 *
	 * @return string
	 * @since 1.0
	 *
	 */
	public static function get_login_url( $user_id ) {

		if ( empty( $user_id ) ) {
			return '';
		}

		$is_valid_temporary_login = self::is_valid_temporary_login( $user_id, false );
		if ( ! $is_valid_temporary_login ) {
			return '';
		}

		$wtlwp_token = get_user_meta( $user_id, '_wtlwp_token', true );
		if ( empty( $wtlwp_token ) ) {
			return '';
		}

		$login_url = add_query_arg( 'wtlwp_token', $wtlwp_token, trailingslashit( admin_url() ) );

		// Make it compatible with iThemes Security plugin with Custom URL Login enabled
		$login_url = apply_filters( 'itsec_notify_admin_page_url', $login_url );

		return apply_filters( 'tlwp_login_link', $login_url, $user_id );

	}

	/**
	 * Manage temporary logins
	 *
	 * @param int $user_id
	 * @param string $action
	 *
	 * @return bool
	 * @since 1.0
	 *
	 */
	public static function manage_login( $user_id = 0, $action = '' ) {

		if ( empty( $user_id ) || empty( $action ) ) {
			return false;
		}

		$is_valid_temporary_login = self::is_valid_temporary_login( $user_id, false );
		if ( ! $is_valid_temporary_login ) {
			return false;
		}

		$manage_login = false;
		if ( 'disable' === $action ) {
			$manage_login = update_user_meta( $user_id, '_wtlwp_expire', self::get_current_gmt_timestamp() );
		} elseif ( 'enable' === $action ) {
			$manage_login = update_user_meta( $user_id, '_wtlwp_expire', self::get_user_expire_time() );
		}

		if ( $manage_login ) {
			return true;
		}

		return false;

	}

	/**
	 * Get the redable time elapsed string
	 *
	 * @param int $time
	 * @param bool $ago
	 *
	 * @return string
	 * @since 1.0
	 *
	 */
	public static function time_elapsed_string( $time, $ago = false ) {

		if ( is_numeric( $time ) ) {

			if ( $ago ) {
				$etime = self::get_current_gmt_timestamp() - $time;
			} else {
				$etime = $time - self::get_current_gmt_timestamp();
			}

			if ( $etime < 1 ) {
				return __( 'Expired', 'temporary-login-without-password' );
			}

			$a = array(
				// 365 * 24 * 60 * 60 => 'year',
				// 30 * 24 * 60 * 60 => 'month',
				24 * 60 * 60 => 'day',
				60 * 60      => 'hour',
				60           => 'minute',
				1            => 'second',
			);

			$a_plural = array(
				'year'   => 'years',
				'month'  => 'months',
				'day'    => 'days',
				'hour'   => 'hours',
				'minute' => 'minutes',
				'second' => 'seconds',
			);

			foreach ( $a as $secs => $str ) {
				$d = $etime / $secs;

				if ( $d >= 1 ) {
					$r = round( $d );

					$time_string = ( $r > 1 ) ? $a_plural[ $str ] : $str;

					if ( $ago ) {
						return __( sprintf( '%d %s ago', $r, $time_string ), 'temporary-login-without-password' );
					} else {
						return __( sprintf( '%d %s remaining', $r, $time_string ), 'temporary-login-without-password' );
					}
				}
			}

			return __( 'Expired', 'temporary-login-without-password' );
		} else {

			$expiry_options = Wp_Temporary_Login_Without_Password_Common::get_expiry_options();

			return ! empty( $expiry_options[ $time ] ) ? $expiry_options[ $time ]['expiry_label'] : '';
		}

	}

	/**
	 * Get all pages which needs to be blocked for temporary users
	 *
	 * @return array
	 * @since 1.0
	 *
	 */
	public static function get_blocked_pages() {
		$blocked_pages = array( 'user-new.php', 'user-edit.php', 'profile.php' );
		$blocked_pages = apply_filters( 'wtlwp_restricted_pages_for_temporary_users', $blocked_pages );

		return $blocked_pages;

	}

	/**
	 * Delete all temporary logins
	 *
	 * @since 1.0
	 */
	public static function delete_temporary_logins() {

		$temporary_logins = Wp_Temporary_Login_Without_Password_Common::get_temporary_logins();

		if ( count( $temporary_logins ) > 0 ) {
			foreach ( $temporary_logins as $user ) {
				if ( $user instanceof WP_User ) {
					$user_id = $user->ID;

					wp_delete_user( $user_id ); // Delete User

					// delete user from Multisite network too!
					if ( is_multisite() ) {

						// If it's a super admin, we can't directly delete user from network site.
						// We need to revoke super admin access first and then delete user
						if ( is_super_admin( $user_id ) ) {
							revoke_super_admin( $user_id );
						}

						wpmu_delete_user( $user_id );
					}
				}
			}
		}

	}

	/**
	 * Print out option html elements for multi role selectors.
	 *
	 * @param string $selected Slug for the role that should be already selected.
	 *
	 * @since 1.5.2
	 *
	 */
	public static function tlwp_multi_select_dropdown_roles( $selected_roles = array() ) {
		$r = '';

		$editable_roles = array_reverse( get_editable_roles() );

		foreach ( $editable_roles as $role => $details ) {
			$name = translate_user_role( $details['name'] );
			// preselect specified role
			if ( count( $selected_roles ) > 0 && in_array( $role, $selected_roles ) ) {
				$r .= "\n\t<option selected='selected' value='" . esc_attr( $role ) . "'>$name</option>";
			} else {
				$r .= "\n\t<option value='" . esc_attr( $role ) . "'>$name</option>";
			}
		}

		echo $r;
	}

	/**
	 * Get temporary_user details.
	 *
	 * @param int $user_id
	 *
	 * @return array
	 * @since 1.5.3
	 *
	 */
	public static function get_temporary_logins_data( $user_id = 0 ) {

		$user_data = array();
		if ( $user_id ) {

			$is_tlwp_user = get_user_meta( $user_id, '_wtlwp_user', true );

			if ( $is_tlwp_user ) {

				$temporary_user_info = get_userdata( $user_id );

				$email      = $temporary_user_info->user_email;
				$first_name = $temporary_user_info->first_name;
				$last_name  = $temporary_user_info->last_name;
				$role       = array_shift( $temporary_user_info->roles );

				$created_on  = get_user_meta( $user_id, '_wtlwp_created', true );
				$expire_on   = get_user_meta( $user_id, '_wtlwp_expire', true );
				$wtlwp_token = get_user_meta( $user_id, '_wtlwp_token', true );
				$redirect_to = get_user_meta( $user_id, '_wtlwp_redirect_to', true );
				$user_locale = get_user_meta( $user_id, 'locale', true );

				$user_data = array(
					'is_tlwp_user' => $is_tlwp_user,
					'email'        => $email,
					'first_name'   => $first_name,
					'last_name'    => $last_name,
					'created_on'   => $created_on,
					'expire_on'    => $expire_on,
					'wtlwp_token'  => $wtlwp_token,
					'role'         => $role,
					'locale'       => $user_locale,
					'redirect_to'  => $redirect_to
				);
			}

		}

		return $user_data;

	}

	/**
	 * Print out option html elements for role selectors.
	 *
	 * @param string $selected Slug for the role that should be already selected.
	 *
	 * @since 1.5.2
	 *
	 */
	public static function tlwp_dropdown_roles( $visible_roles = array(), $selected = '' ) {
		$r = '';

		$editable_roles = array_reverse( get_editable_roles() );

		$visible_roles = ! empty( $visible_roles ) ? $visible_roles : array_keys( $editable_roles );

		/**
		 * NOTE: When edit tmeporary user - there may be a case where $selected role is not available in viisible roles
		 *  If so, add $selected role into $visible_roles array
		 */
		if ( ! in_array( $selected, $visible_roles ) ) {
			$visible_roles[] = $selected;
		}

		foreach ( $editable_roles as $role => $details ) {

			if ( in_array( $role, $visible_roles ) ) {
				$name = translate_user_role( $details['name'] );
				// preselect specified role
				if ( $selected == $role ) {
					$r .= "\n\t<option selected='selected' value='" . esc_attr( $role ) . "'>$name</option>";
				} else {
					$r .= "\n\t<option value='" . esc_attr( $role ) . "'>$name</option>";
				}
			}
		}

		echo $r;
	}

	/**
	 * Generate mailto link to send temporary login link directly into email
	 *
	 * @param $email
	 * @param $temporary_login_link
	 *
	 * @return string Generated mail to link
	 * @since 1.5.7
	 *
	 */
	public static function generate_mailto_link( $email, $temporary_login_link ) {

		$temporary_login_link = urlencode( $temporary_login_link );
		$double_line_break    = '%0D%0A%0D%0A';    // as per RFC2368
		$mailto_greeting      = __( 'Hello,', 'temporary-login-without-password' );
		$mailto_instruction   = __( 'Click the following link to log into the system:', 'temporary-login-without-password' );
		$mailto_subject       = __( 'Temporary Login Link', 'temporary-login-without-password' );
		$mailto_body          = "$mailto_greeting $double_line_break $mailto_instruction $double_line_break $temporary_login_link $double_line_break";

		return __( sprintf( "mailto:%s?subject=%s&body=%s", $email, $mailto_subject, $mailto_body ), 'temporary-login-without-password' );
	}

	/**
	 * Render Quick Feedback Widget
	 *
	 * @param $params
	 *
	 */
	public static function render_feedback_widget( $params ) {
		global $tlwp_feedback;

		$feedback = $tlwp_feedback;

		$default_params = array(
			'set_transient' => true,
			'force'         => false
		);

		$params = wp_parse_args( $params, $default_params );

		if ( ! empty( $params['event'] ) ) {

			$event = $feedback->event_prefix . $params['event'];
			$force = ! empty( $params['force'] ) ? $params['force'] : false;

			$can_show = false;

			if ( $force ) {
				$can_show = true;
			} else {
				if ( ! $feedback->is_event_transient_set( $event ) ) {
					$can_show = true;

					$feedback_data = $feedback->get_event_feedback_data( $feedback->plugin_abbr, $event );
					if ( count( $feedback_data ) > 0 ) {
						$feedback_data          = array_reverse( $feedback_data );
						$last_feedback_given_on = $feedback_data[0]['created_on'];
						if ( strtotime( $last_feedback_given_on ) > strtotime( '-45 days' ) ) {
							$can_show = false;
						}
					}
				}
			}

			if ( $can_show ) {
				if ( 'star' === $params['type'] ) {
					$feedback->render_stars( $params );
				} elseif ( 'emoji' === $params['type'] ) {
					$feedback->render_emoji( $params );
				}
			}
		}

	}

	/**
	 * Get TLWP meta info
	 *
	 * @return array
	 *
	 * @since 1.6.15
	 */
	public static function get_tlwp_meta_info() {
		return array();
	}

	/**
	 * Check whether TLWP admin page?
	 *
	 * @return bool
	 *
	 * @since 1.5.24
	 */
	public static function is_tlwp_admin_page() {

		$pages = array(
			'users_page_wp-temporary-login-without-password',
			'users_page_wp-temporary-login-without-password-network'
		);

		if ( ! function_exists( 'get_current_screen' ) ) {
			require_once ABSPATH . '/wp-admin/includes/screen.php';
		}
		$screen = get_current_screen();

		if ( in_array( $screen->id, $pages ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check Whether current user is temporary user
	 *
	 * @return bool
	 *
	 * @since 1.6.2
	 */
	public static function is_current_user_valid_temporary_user() {
		$current_user_id = get_current_user_id();

		return self::is_valid_temporary_login( $current_user_id );
	}

	/**
	 * Get pages
	 *
	 * @param string $selected
	 *
	 * @since 1.6.9
	 */
	public static function tlwp_dropdown_redirect_to( $selected = '' ) {

		$pages = (array) get_pages();

		array_unshift( $pages, array( 'ID' => 'home_page', 'post_title' => __( 'Website Home Page', 'temporary-login-without-password' ) ) );
		array_unshift( $pages, array( 'ID' => 'system_default', 'post_title' => __( 'System Default', 'temporary-login-without-password' ) ) );
		array_unshift( $pages, array( 'ID' => 'wp_dashboard', 'post_title' => __( 'Dashboard', 'temporary-login-without-password' ) ) );

		$r = '';
		if ( count( $pages ) > 0 ) {

			$r .= "<optgroup label='" . __( 'Pages', 'temporary-login-without-password' ) . "'>";
			foreach ( $pages as $page ) {
				$page = (array) $page;
				// preselect specified role
				if ( $selected == $page['ID'] ) {
					$r .= "\n\t<option selected='selected' value='" . esc_attr( $page['ID'] ) . "'>" . $page['post_title'] . "</option>";
				} else {
					$r .= "\n\t<option value='" . esc_attr( $page['ID'] ) . "'>" . $page['post_title'] . "</option>";
				}
			}
			$r .= "</optgroup>";
		}

		echo $r;
	}

	/**
	 * Get the Request URI
	 *
	 * @return mixed|string|string[]
	 *
	 * @since 1.6.9
	 */
	public static function get_request_uri() {

		/**
		 * There is an issue with WordPress which installed in sub directory
		 *
		 * e.g WordPress installed at https://wpm.stg/wpmsub
		 *
		 * So, when we are preparing redirect url from current request, we are getting
		 * https://wpm.stg/wpmsub/wpmsub url. Which leads to 404 Not Found.
		 *
		 * So, We need to remove extra "wpmsub" from url.
		 *
		 * If it's Multi site installation, we don't need to remove.
		 * We only need to remove it for WordPress sub directory installation.
		 */

		// Get current request
		$request_uri = $_SERVER['REQUEST_URI'];

		if ( ! is_multisite() ) {
			$component = parse_url( get_site_url(), PHP_URL_PATH );

			if ( ! empty( $component ) ) {

				$component = trim( $component );
				/**
				 * Someone may have subdirectory name as 'wp'.
				 *
				 * So, in this scenario, $component would be '/wp' and request uri will
				 * be /wp/wp-admin/?...
				 *
				 * We want to replace only subdirectory. So, if we do str_replace($component, '', $request_uri)
				 * it will result '-admin/?...' which is wrong.
				 *
				 * So, instead of replacing only '/wp', we will replace '/wp/' (end slash) which
				 * results in 'wp-admin/?...'.
				 *
				 * So, we are adding '/' to $component
				 */
				$component   .= '/';
				$request_uri = str_replace( $component, '', $request_uri );
			}
		}

		return $request_uri;
	}

	/**
	 * Get utm tracking url
	 *
	 * @param array $utm_args
	 *
	 * @return mixed|string
	 *
	 * @since 1.6.11
	 */
	public static function get_utm_tracking_url( $utm_args = array() ) {

		$url          = ! empty( $utm_args['url'] ) ? $utm_args['url'] : 'https://storeapps.org/';
		$utm_source   = ! empty( $utm_args['utm_source'] ) ? $utm_args['utm_source'] : 'in_app';
		$utm_medium   = ! empty( $utm_args['utm_medium'] ) ? $utm_args['utm_medium'] : 'tlwp';
		$utm_campaign = ! empty( $utm_args['utm_campaign'] ) ? $utm_args['utm_campaign'] : 'sa_upsell';

		if ( ! empty( $utm_source ) ) {
			$url = add_query_arg( 'utm_source', $utm_source, $url );
		}

		if ( ! empty( $utm_medium ) ) {
			$url = add_query_arg( 'utm_medium', $utm_medium, $url );
		}

		if ( ! empty( $utm_campaign ) ) {
			$url = add_query_arg( 'utm_campaign', $utm_campaign, $url );
		}

		return $url;

	}

	/**
	 * Delete plugin data
	 *
	 * @since 1.6.13
	 */
	public static function delete_plugin_data_on_uninstall() {

		// Delete all temporary login
		self::delete_temporary_logins();

		/**
		 * Delete option if it's there
		 * We do backup when we deactivate the plugin
		 * So, remove this option too when we actually delete the plugin.
		 */
		delete_option( 'temporary_logins_data' );
		delete_option( 'tlwp_plugin_activation_time' );
		delete_option( 'tlwp_plugin_version' );
		delete_option( 'tlwp_settings' );

	}

	/**
	 * Generate username
	 *
	 * @param int $length
	 *
	 * @return string
	 *
	 * @since 1.6.14
	 */
	public static function random_username( $length = 10 ) {
		$characters      = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$random_username = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$random_username .= $characters[ rand( 0, strlen( $characters ) ) ];
		}

		return sanitize_user( strtolower( $random_username ), true );
	}
}
