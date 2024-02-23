<?php

namespace Elementor;

defined('ABSPATH') || exit;

use ElementsKit_Lite\Libs\Framework\Attr;

class ElementsKit_Widget_Dribble_Feed_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

	public static $transient_name = '__trans_ekit_dribble_feeds';
	public static $transient_name_user = '__trans_ekit_dribble_user';

	public static function get_name() {
		return 'elementskit-dribble-feed';
	}


	public static function get_title() {
		return esc_html__('Dribbble Feed', 'elementskit');
	}


	public static function get_icon() {
		return 'ekit ekit-widget-icon ekit-dribbble';
	}


	public static function get_categories() {
		return ['elementskit'];
	}

	static function get_keywords() {
		return ['ekit', 'dribbble', 'feed', 'social feed', 'dribbble integration'];
	}

	public static function get_dir() {
		return \ElementsKit::widget_dir() . 'dribble-feed/';
	}


	public static function get_url() {
		return \ElementsKit::widget_url() . 'dribble-feed/';
	}

	public function wp_init() {

		new \ElementsKit\Widgets\Dribble_Feed\Dribble_Api();
	}

	public static function get_config() {
		$data = Attr::instance()->utils->get_option('user_data', []);

		return empty($data['dribble']) ? [] : $data['dribble'];
	}


	public static function get_feed($token) {

		$transient_name  = self::$transient_name;
		$transient_value = get_transient($transient_name);

		if(false !== $transient_value) {

			return $transient_value;
		}

		$show_item = '&page=1&per_page=50';
		$args = '?access_token='.$token;
		$request   = wp_remote_get('https://api.dribbble.com/v2/user/shots' . $args . $show_item);

		if(is_wp_error($request)) {

			return false;
		}

		$body   = wp_remote_retrieve_body($request);
		$data   = json_decode($body);
		$result = $data;

		$expiration_time = 86400;//in second
		set_transient($transient_name, $result, $expiration_time);

		return $result;
	}

	public static function get_user_info($token) {

		$transient_name  = self::$transient_name_user;
		$transient_value = get_transient($transient_name);

		if(false !== $transient_value) {

			return $transient_value;
		}

		$args = '?access_token='.$token;
		$request   = wp_remote_get('https://api.dribbble.com/v2/user' . $args);

		if(is_wp_error($request)) {

			return false;
		}

		$body   = wp_remote_retrieve_body($request);
		$data   = json_decode($body, true);
		$result = $data;

		$expiration_time = 86400;//in second
		set_transient($transient_name, $result, $expiration_time);

		return $result;
	}

	public static function reset_cache() {
		delete_transient(self::$transient_name);
		delete_transient(self::$transient_name_user);
	}
}