<?php

namespace Elementor;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Behance_Feed_Handler extends \ElementsKit_Lite\Core\Handler_Widget {


	static function get_name() {
		return 'elementskit-behance-feed';
	}


	static function get_title() {
		return esc_html__('Behance Feed', 'elementskit');
	}


	static function get_icon() {
		return 'ekit ekit-widget-icon ekit-behance';
	}


	static function get_categories() {
		return ['elementskit'];
	}

	static function get_keywords(){
		return ['ekit', 'behance', 'feed', 'social feed', 'behance integration'];
	}

	static function get_dir() {
		return \ElementsKit::widget_dir() . 'behance-feed/';
	}


	static function get_url() {
		return \ElementsKit::widget_url() . 'behance-feed/';
	}

	public function wp_init() {

		new \ElementsKit\Widgets\Behance_Feed\Behance_Api();
	}


	public static function get_behance_feed_user_info_key($user_name) {


		return '_trans_ekit_bf_user_' . $user_name;
	}


	public static function get_behance_feed_feed_key($user_name) {


		return '_trans_ekit_bf_feed_' . $user_name;
	}


	public static function get_the_feed($user_name) {

		$user_name = trim($user_name);

		$trans_key_usr  = self::get_behance_feed_user_info_key($user_name);
		$trans_key_feed = self::get_behance_feed_feed_key($user_name);

		$trans_usr = get_transient($trans_key_usr);

		if(false !== $trans_usr) {

			$user_info = $trans_usr;
			$msg       = 'user info fetched from cache';

		} else {

			$msg       = 'user info fetched from server and cached it';
			$user_info = self::fetch_user_info_feed($user_name, $msg);
		}

		$trans_feed = get_transient($trans_key_feed);

		if(false !== $trans_feed) {

			$feed = $trans_feed;
			$msg2 = 'feed fetched from cache';

		} else {

			$feed = self::fetch_all_feed($user_name, $msg);
			$msg2 = 'feed fetched from server and cached the call';
		}

		$dt['user'] = empty($user_info->user) ? [] : $user_info->user;
		$dt['feed'] = $feed;

		return [
			'success' => true,
			'data'    => $dt,
			'msg1'    => $msg,
			'msg2'    => $msg2,
		];
	}


	public static function fetch_user_info_feed($user_name, &$msg) {

		$base = 'https://token.wpmet.com/feed/behance.php';
		$args = '?action=get_bf_user&user_name=' . $user_name;

		$trans_key_usr = self::get_behance_feed_user_info_key($user_name);

		try {

			$request = wp_remote_get($base . $args);

			if(is_wp_error($request)) {

				$msg = 'API call failed ! - ' . $request->get_error_message();

				return [];
			}

			$body = wp_remote_retrieve_body($request);
			$dt   = json_decode($body);

			if(!empty($dt->result) && $dt->result == true) {

				$exp_time = 86400 * 2; //for two days

				set_transient($trans_key_usr, $dt->res, $exp_time);

				return $dt->res;
			}

			$msg = $dt->msg;

			return $dt;

		} catch(\Exception $ex) {

			$msg = 'Could not retrieve the access token - ' . $ex->getMessage();

		}

		return [];
	}


	public static function fetch_all_feed($user_name, &$msg) {

		$base = 'https://token.wpmet.com/feed/behance.php';
		$args = '?action=get_bf_feed&user_name=' . $user_name;

		$trans_key_feed = self::get_behance_feed_feed_key($user_name);


		try {

			$request = wp_remote_get($base . $args);

			if(is_wp_error($request)) {

				$msg = 'API call failed ! - ' . $request->get_error_message();

				return [];
			}

			$body = wp_remote_retrieve_body($request);
			$dt   = json_decode($body);


			if(!empty($dt->result) && $dt->result == true) {

				$exp_time = 86400 * 2; //for two days

				set_transient($trans_key_feed, $dt->res, $exp_time);

				return $dt->res;
			}

			$msg = $dt->msg;


		} catch(\Exception $ex) {

			$msg = 'Could not retrieve the access token - ' . $ex->getMessage();
		}

		return [];
	}
}