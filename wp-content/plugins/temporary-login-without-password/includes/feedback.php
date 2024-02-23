<?php

/**
 * Get additional system & plugin specific information for feedback
 *
 */
if ( ! function_exists( 'tlwp_get_additional_info' ) ) {

	/**
	 * Get TLWP specific information
	 *
	 * @param $additional_info
	 * @param bool $system_info
	 *
	 * @return mixed
	 *
	 * @since 1.5.17
	 */
	function tlwp_get_additional_info( $additional_info, $system_info = false ) {
		global $tlwp_tracker;

		$additional_info['version'] = WTLWP_PLUGIN_VERSION;

		if ( $system_info ) {

			$additional_info['active_plugins']   = implode( ', ', $tlwp_tracker::get_active_plugins() );
			$additional_info['inactive_plugins'] = implode( ', ', $tlwp_tracker::get_inactive_plugins() );
			$additional_info['current_theme']    = $tlwp_tracker::get_current_theme_info();
			$additional_info['wp_info']          = $tlwp_tracker::get_wp_info();
			$additional_info['server_info']      = $tlwp_tracker::get_server_info();

			// TLWP Specific information
			//$additional_info['plugin_meta_info'] = Wp_Temporary_Login_Without_Password_Common::get_tlwp_meta_info();
		}

		return $additional_info;

	}

}

add_filter( 'tlwp_additional_feedback_meta_info', 'tlwp_get_additional_info', 10, 2 );

if ( ! function_exists( 'tlwp_can_ask_user_for_review' ) ) {
	/**
	 * Can we ask user for 5 star review?
	 *
	 * @return bool
	 *
	 * @since 1.5.22
	 */
	function tlwp_can_ask_user_for_review( $enable, $review_data ) {

		if ( $enable ) {

			$current_user_id = get_current_user_id();

			// Don't show 5 star review notice to temporary user
			if ( ! empty( $current_user_id ) && Wp_Temporary_Login_Without_Password_Common::is_valid_temporary_login( $current_user_id ) ) {
				return false;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			$temporary_logins = Wp_Temporary_Login_Without_Password_Common::get_temporary_logins();
			$total_logins     = count( $temporary_logins );

			// Is user fall in love with our plugin in 60 days after they said no for the review?
			// But, make sure we are asking user only after 60 days.
			// We are good people. Respect the user decision.
			if ( $total_logins < 1 ) {
				return false;
			}
		}

		return $enable;

	}
}

add_filter( 'tlwp_can_ask_user_for_review', 'tlwp_can_ask_user_for_review', 10, 2 );


if ( ! function_exists( 'tlwp_review_message_data' ) ) {
	/**
	 * Filter 5 star review data
	 *
	 * @param $review_data
	 *
	 * @return mixed
	 *
	 * @since 1.5.22
	 */
	function tlwp_review_message_data( $review_data ) {

		$icon_url = WTLWP_PLUGIN_URL . 'admin/assets/images/icon-64.png';

		$review_data['icon_url'] = $icon_url;

		return $review_data;
	}
}

add_filter( 'tlwp_review_message_data', 'tlwp_review_message_data', 10 );

if ( ! function_exists('tlwp_can_load_sweetalert_js') ) {
	/**
	 * Can load sweetalert js
	 *
	 * @param bool $load
	 *
	 * @return bool
	 *
	 * @since 1.5.24
	 */
	function tlwp_can_load_sweetalert_js( $load = false ) {

		if ( Wp_Temporary_Login_Without_Password_Common::is_tlwp_admin_page() ) {
			return true;
		}

		return $load;
	}
}

add_filter( 'tlwp_can_load_sweetalert_js', 'tlwp_can_load_sweetalert_js', 10, 1 );

if ( ! function_exists('tlwp_can_load_sweetalert_css') ) {
	/**
	 * Can load sweetalert css
	 *
	 * @param bool $load
	 *
	 * @return bool
	 *
	 * @since 1.7.3
	 */
	function tlwp_can_load_sweetalert_css( $load = false ) {

		if ( Wp_Temporary_Login_Without_Password_Common::is_tlwp_admin_page() ) {
			return true;
		}

		return $load;
	}
}

add_filter( 'tlwp_can_load_sweetalert_css', 'tlwp_can_load_sweetalert_css', 10, 1 );

if ( ! function_exists( 'tlwp_add_escape_allowed_tags') ) {
	/**
	 * Add HTML tags to be excluded while escaping
	 *
	 * @return array $allowedtags
	 */
	function tlwp_add_escape_allowed_tags() {
		$context_allowed_tags = wp_kses_allowed_html( 'post' );
		$custom_allowed_tags  = array(
			'div'      => array(
				'x-data' => true,
				'x-show' => true,
			),
			'select'   => array(
				'class'    => true,
				'name'     => true,
				'id'       => true,
				'style'    => true,
				'title'    => true,
				'role'     => true,
				'data-*'   => true,
				'tab-*'    => true,
				'multiple' => true,
				'aria-*'   => true,
				'disabled' => true,
				'required' => 'required',
			),
			'optgroup' => array(
				'label' => true,
			),
			'option'   => array(
				'class'    => true,
				'value'    => true,
				'selected' => true,
				'name'     => true,
				'id'       => true,
				'style'    => true,
				'title'    => true,
				'data-*'   => true,
			),
			'input'    => array(
				'class'          => true,
				'name'           => true,
				'type'           => true,
				'value'          => true,
				'id'             => true,
				'checked'        => true,
				'disabled'       => true,
				'selected'       => true,
				'style'          => true,
				'required'       => 'required',
				'min'            => true,
				'max'            => true,
				'maxlength'      => true,
				'size'           => true,
				'placeholder'    => true,
				'autocomplete'   => true,
				'autocapitalize' => true,
				'autocorrect'    => true,
				'tabindex'       => true,
				'role'           => true,
				'aria-*'         => true,
				'data-*'         => true,
			),
			'label'    => array(
				'class' => true,
				'name'  => true,
				'type'  => true,
				'value' => true,
				'id'    => true,
				'for'   => true,
				'style' => true,
			),
			'form'     => array(
				'class'  => true,
				'name'   => true,
				'value'  => true,
				'id'     => true,
				'style'  => true,
				'action' => true,
				'method' => true,
				'data-*' => true,
			),
			'svg'      => array(
				'width'    => true,
				'height'   => true,
				'viewbox'  => true,
				'xmlns'    => true,
				'class'    => true,
				'stroke-*' => true,
				'fill'     => true,
				'stroke'   => true,
			),
			'path'     => array(
				'd'               => true,
				'fill'            => true,
				'class'           => true,
				'fill-*'          => true,
				'clip-*'          => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'stroke-width'    => true,
				'fill-rule'       => true,
			),

			'main'     => array(
				'align'    => true,
				'dir'      => true,
				'lang'     => true,
				'xml:lang' => true,
				'aria-*'   => true,
				'class'    => true,
				'id'       => true,
				'style'    => true,
				'title'    => true,
				'role'     => true,
				'data-*'   => true,
			),
			'textarea' => array(
				'id' => true,
				'autocomplete' => true,
				'required'	   => 'required',
				'placeholder'  => true,
				'class'		   => true,
			),
			'style'    => array(),
			'link'     => array(
				'rel'   => true,
				'id'    => true,
				'href'  => true,
				'media' => true,
			),
			'a'        => array(
				'x-on:click' => true,
			),
			'polygon'  => array(
				'class'  => true,
				'points' => true,
			),
		);

		$allowedtags = array_merge_recursive( $context_allowed_tags, $custom_allowed_tags );

		return $allowedtags;
	}
}

add_filter( 'tlwp_escape_allowed_tags', 'tlwp_add_escape_allowed_tags' );

if ( ! function_exists( 'tlwp_show_feature_survey' ) ) {
	function tlwp_show_feature_survey() {

		if ( ! Wp_Temporary_Login_Without_Password_Common::is_tlwp_admin_page() ) {
			return;
		}

		$can_ask_user_for_review = false;
		
		$temporary_logins        = Wp_Temporary_Login_Without_Password_Common::get_temporary_logins();
		$temporary_logins_exists = count( $temporary_logins ) > 0;

		if ( $temporary_logins_exists ) {
			$can_ask_user_for_review = true;
		} else {
			$plugin_activation_time  = get_option( 'tlwp_plugin_activation_time', 0 );
			$feedback_wait_period    = 10 * DAY_IN_SECONDS;
			$feedback_time           = $plugin_activation_time + $feedback_wait_period;
			$current_time            = time();
			$can_ask_user_for_review = $current_time > $feedback_time;
		}

		if ( ! $can_ask_user_for_review ) {
			return;
		}

		global $tlwp_feedback;

		$survey_title     = __( '📣 Quick survey: Tell us what feature you want in the Temporary Login plugin?', 'temporary-login-without-password'  );
		$survey_slug      = 'tlwp-feature-survey';
		$survey_questions = array(
			'generate_bulk_logins_via_csv'       => __( 'Generation of bulk login links when users are uploaded via CSV', 'temporary-login-without-password' ), 
			'create_temp_login_on_user_register' => __( 'Create a temporary login link whenever a user registers', 'temporary-login-without-password' ), 
			'notify_admin_on_temp_user_login'    => __( 'Send an email notification to the admin when a temporary user logs in.', 'temporary-login-without-password' ),
			'log_temp_user_activity'             => __( 'Log every activity performed by a temporary login user', 'temporary-login-without-password' ),
			'limit_login_for_temp_user'          => __( 'Limit the number of times a temporary user can log in to your site', 'temporary-login-without-password' ),
			'other'                              => __( 'Other - Mention the exact feature', 'temporary-login-without-password' ),
		);

		$survey_fields = array();
		foreach ( $survey_questions as $question_slug => $question_text ) {
			$survey_fields[] = array(
				'type' => 'radio',
				'name' => 'feature',
				'label' => $question_text,
				'value' => $question_slug,
			);
		}

		// Store default values in field_name => default_value format.
		$default_values = array(
			'feature' => 'generate_bulk_logins_via_csv',
		);

		$feedback_data = array(
			'event'          => 'feature_survey',
			'title'          => $survey_title,
			'slug'           => $survey_slug,
			'logo_img_url'   => WTLWP_PLUGIN_URL . 'admin/assets/images/icon-64.png',
			'fields'         => $survey_fields,
			'default_values' => $default_values,
			'type'	         => 'poll',
			'system_info'    => false,
		);
		
		$tlwp_feedback->render_feedback_widget( $feedback_data );
	}
}

add_action( 'admin_notices', 'tlwp_show_feature_survey' );


