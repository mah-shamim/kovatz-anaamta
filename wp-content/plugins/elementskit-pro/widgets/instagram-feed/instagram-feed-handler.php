<?php

namespace Elementor;

use ElementsKit\Libs\Framework\Attr;


class ElementsKit_Widget_Instagram_Feed_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

	protected static $transient_name = 'ekit_instagram_cached_data';

	public function wp_init() {

		(new \ElementsKit\Widgets\Instagram_Feed\Instagram_Feed_Api());
	}

	static function get_name() {
		return 'elementskit-instagram-feed';
	}

	static function get_title() {
		return esc_html__('Instagram Feed', 'elementskit');
	}

	static function get_icon() {
		return 'ekit ekit-instagram ekit-widget-icon ';
	}

	static function get_categories() {
		return ['elementskit'];
	}

	static function get_keywords() {
		return ['ekit', 'instagram', 'feed', 'social feed', 'instagram integration'];
	}

	static function get_dir() {
		return \ElementsKit::widget_dir() . 'instagram-feed/';
	}

	static function get_url() {
		return \ElementsKit::widget_url() . 'instagram-feed/';
	}


	public static function get_data() {

		$data = Attr::instance()->utils->get_option('user_data', []);

		$user_id = (isset($data['instragram']) && !empty($data['instragram']['user_id'])) ? $data['instragram']['user_id'] : '';

		$username = (isset($data['instragram']) && !empty($data['instragram']['username'])) ? $data['instragram']['username'] : '';

		$token = empty($data['instragram']['token']) ? '' : $data['instragram']['token'];

		return [
			'user_id'  => $user_id,
			'token'    => $token,
			'username' => $username,
		];
	}

	public static function get_user_info() {

		$data = Attr::instance()->utils->get_option('user_data', []);
		$token = $data['instragram']['token'];
		$user_id = $data['instragram']['user_id'];

		$trans_key = self::get_transient_key_for_user($user_id);

		$cache_data = get_transient($trans_key);

		if(false === $cache_data) {

			$feed = self::call_api_for_user_details($user_id, $token);

			set_transient($trans_key, $feed, 86400 * 2); // set expire time to 48 hours

			return $feed;
		}

		return $cache_data;
	}

	public static function get_transient_key($uid) {

		return 'ekit_instagram_cached_data_' . md5($uid);
	}

	public static function get_transient_key_for_user($uid) {

		return 'ekit_insta_cache_user_info__' . md5($uid);
	}

	public static function get_instagram_feed_from_API() {

		return self::get_cached_data();
	}

	static function get_cached_data() {

		$util = Attr::instance()->utils;
		$data = $util->get_option('user_data', []);

		if(empty($data['instragram']['user_id'])) {

			$ret = new \stdClass();
			$msg = new \stdClass();

			$msg->message = __('User id is not set yet! Please go to settings and set the user id.', 'elementskit');
			$ret->error = $msg;

			return $ret;
		}

		if(empty($data['instragram']['token'])) {

			$ret = new \stdClass();
			$msg = new \stdClass();

			$msg->message = __('Access token is not set yet! Please go to settings and set the access token.', 'elementskit');
			$ret->error = $msg;

			return $ret;
		}

		// Now check the long live token and decide if we need to refresh the token
		$token_expire = empty($data['instragram']['token_expire']) ? 0 : intval($data['instragram']['token_expire']);
		$five_days = 432000;
		$ten_days = 864000;
		$the_token = $data['instragram']['token'];


		if(!empty($data['instragram']['token_generated']) && !empty($token_expire)) {

			$now = time();
			$gen = strtotime($data['instragram']['token_generated']);
			$time_passed = $now - $gen;
			$time_remains = $token_expire - $ten_days - $time_passed;
			
			if($time_remains <= 0) {

				// we will refresh the access token

				$refresh_url = 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token='.$the_token;

				$response = wp_remote_get($refresh_url);

				if($response['response']['code'] === 200) {

					$output = json_decode($response['body']);

					if(!empty($output->access_token)) {

						$data['instragram']['token_generated'] = date('Y-m-d');
						$data['instragram']['token_expire'] = $output->expires_in;
						$data['instragram']['old_token'] = $the_token;
						$data['instragram']['token'] = $output->access_token;
						$the_token = $output->access_token;
						$util->save_option('user_data', $data);
						
					}
				}		
			}
		}


		$user_id = $data['instragram']['user_id'];
		$trans_key = self::get_transient_key($user_id);

		$cache_data = get_transient($trans_key);

		if(false === $cache_data) {

			$feed = self::call_api($the_token);

			set_transient($trans_key, $feed, 86400); // set expire time to 24 hours

			return $feed;
		}

		return $cache_data;
	}

	public static function call_api($access_token) {

		$url      = 'https://graph.instagram.com/me/media?fields=id,media_type,media_url,username,caption,timestamp&access_token=' . $access_token;
		$response = wp_remote_get($url);
		
		if(200 === wp_remote_retrieve_response_code($response)) {
			$response_body = wp_remote_retrieve_body($response);
			$data          = json_decode($response_body);
			return $data->data;
		}
		return [];
	}

	public static function call_api_for_user_details($uid, $token) {

		$url = 'https://graph.instagram.com/v12.0/' . $uid . '?fields=username,profile_picture&access_token=' . $token;

		$response = wp_remote_get($url);
		$body = json_decode($response['body']);
		if(!isset($body)) {
			return $response['body'];
		}
		return $body;
	}
}
