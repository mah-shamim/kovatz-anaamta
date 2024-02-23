<?php


class mo_linkedin_oidc {

	public $color     = '#007AB9';
	public $scope     = 'openid email profile';
	public $video_url = 'https://www.youtube.com/embed/Qs-PSyy7KVQ';
	public $instructions;
	public function __construct() {
		$this->site_url     = get_option( 'siteurl' );
		$this->instructions = "Go to <a href=\"http://developer.linkedin.com/\" target=\"_blank\">http://developer.linkedin.com/</a> and click on <strong>Create Apps</strong> and sign in with your linkedin account.##Enter the Application Name, Linkedin page URl or name, Privacy Policy URL, And upload app logo.##If you don't have a linked in page click on <a href=\"https://www.linkedin.com/company/setup/new/\" target=\"_blank\">https://www.linkedin.com/company/setup/new/</a> to create a new page.##Check the <b>API Terms of Use</b> and click on create app.##Click on <b>Auth</b> tab and enter <b><code id='11'>" . mo_get_permalink( 'linkedin_oidc' ) . "</code><i style= \"width: 11px;height: 9px;padding-left:2px;padding-top:3px\" class=\"far fa-fw fa-lg fa-copy mo_copy mo_copytooltip\" onclick=\"copyToClipboard(this, '#11', '#shortcode_url_copy')\"><span id=\"shortcode_url_copy\" class=\"mo_copytooltiptext\">Copy to Clipboard</span></i></b> as <strong>Redirect URLs </strong>and click on <strong>Update</strong>##On the same page you will be able to see your <strong>Client ID</strong> and <strong>Client Secret</strong> under the <strong>Application credentials</strong> section. Copy these and Paste them into the fields above. ##Go to the <b>Product tab</b>.##Find <b>Sign In with LinkedIn using OpenID Connect</b> and click on <b>Select</b>. Check the legal agreement check box and Click on <b>Request Access</b>.##Find <b>Share on LinkedIn</b> and click on <b>Select</b> .Check the legal agreement check box and Click on <b>Request Access</b>. This permission required for social sharing.##Wait till Linkedin approves your permission. ##Click on the Save settings button.##Go to Social Login tab to configure the display as well as other login settings";
	}

	function mo_openid_get_app_code() {
		$appslist                = maybe_unserialize( get_option( 'mo_openid_apps_list' ) );
		$social_app_redirect_uri = get_social_app_redirect_uri( 'linkedin_oidc' );
		mo_openid_start_session();
		$_SESSION['appname'] = 'linkedin_oidc';
		$client_id           = $appslist['linkedin_oidc']['clientid'];
		$scope               = $appslist['linkedin_oidc']['scope'];
		$login_dialog_url    = 'https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=' . $client_id . '&redirect_uri=' . $social_app_redirect_uri . '&state=fooobar&scope=' . $scope;
		header( 'Location:' . $login_dialog_url );
		exit;
	}

	function mo_openid_get_access_token() {
		$code                    = mo_openid_validate_code();
		$social_app_redirect_uri = get_social_app_redirect_uri( 'linkedin_oidc' );

		$appslist         = maybe_unserialize( get_option( 'mo_openid_apps_list' ) );
		$client_id        = $appslist['linkedin_oidc']['clientid'];
		$client_secret    = $appslist['linkedin_oidc']['clientsecret'];
		var_dump($client_secret);
		$access_token_uri = 'https://www.linkedin.com/oauth/v2/accessToken';
		$postData         = 'grant_type=authorization_code&code=' . $code . '&redirect_uri=' . $social_app_redirect_uri . '&client_id=' . $client_id . '&client_secret=' . $client_secret;

		$access_token_json_output = mo_openid_get_access_token( $postData, $access_token_uri, 'linkedin_oidc' );

		$access_token = isset( $access_token_json_output['access_token'] ) ? $access_token_json_output['access_token'] : '';
		mo_openid_start_session();

		$get_jwt=$access_token_json_output['id_token'];
        $get_jwt1 = explode('.', $get_jwt);
        $value1=base64_decode($get_jwt1[1]);
        $value2=explode(',',$value1);
        $em1=$value2[6];
        $em2=explode(':',$em1);
        $em3 = str_replace('"', '', $em2[1]);
        $uid1=$value2[4];
        $uid2=explode(':',$uid1);
        $uid3=str_replace('"', '', $uid2[1]);
		$jsonString = implode(',', $value2);
		$profile_json_output = json_decode($jsonString, true);
		// Test Configuration
		if ( is_user_logged_in() && get_option( 'mo_openid_test_configuration' ) == 1 ) {
			mo_openid_app_test_config( $profile_json_output );
		}
		// set all profile details
		// Set User current app
		$name          = $first_name = $last_name = $email = $user_name = $user_url = $user_picture = $social_user_id = '';
		$location_city = $location_country = $about_me = $company_name = $age = $gender = $friend_nos = '';

		$email          = isset( $profile_json_output_email['email'] ) ? $profile_json_output_email['email'] : '';
		$first_name     = isset( $profile_json_output['given_name']) ? $profile_json_output['given_name'] : '';
		$name           = isset( $profile_json_output['name'] ) ? $profile_json_output['name'] : '';
		$last_name      = isset( $profile_json_output['family_name'] ) ? $profile_json_output['family_name'] : '';
		$user_picture   = isset( $profile_json_output['picture'] ) ? $profile_json_output['picture'] : '';
		$social_user_id = isset( $profile_json_output['id'] ) ? $profile_json_output['id'] : '';

		$appuserdetails = array(
			'first_name'       => $first_name,
			'last_name'        => $last_name,
			'email'            => $email,
			'user_name'        => $name,
			'user_url'         => $user_url,
			'user_picture'     => $user_picture,
			'social_user_id'   => $social_user_id,
			'location_city'    => $location_city,
			'location_country' => $location_country,
			'about_me'         => $about_me,
			'company_name'     => $company_name,
			'friend_nos'       => $friend_nos,
			'gender'           => $gender,
			'age'              => $age,
		);
		return $appuserdetails;
	}
}
