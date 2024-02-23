<?php

namespace Elementor;

use ElementsKit_Lite\Libs\Framework\Attr;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Facebook_Review_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

	private static $fb_base_url = 'https://www.facebook.com/';
	private static $fb_graph_url = 'https://graph.facebook.com/';
	private static $fb_api_version = 'v8.0';
	public static $ok_fbf_info_cache = 'ekit_fb_page_info_cache_';


	public function wp_init() {

		(new \ElementsKit\Widgets\Facebook_Review\Facebook_Review_Api());
	}

	static function get_name() {
		return 'elementskit-facebook-review';
	}


	static function get_title() {
		return esc_html__('Facebook review', 'elementskit');
	}


	static function get_icon() {
		return ' ekit-widget-icon eicon-button';
	}


	static function get_categories() {
		return ['elementskit'];
	}

	static function get_keywords() {
		return ['ekit', 'review', 'fb', 'facebook', 'social review'];
	}

	static function get_dir() {
		return \ElementsKit::widget_dir() . 'facebook-review/';
	}


	static function get_url() {
		return \ElementsKit::widget_url() . 'facebook-review/';
	}


	/**
	 * Get user's profile image link
	 *
	 *
	 * @param $user_id - facebook user id
	 * @param $token - facebook page token
	 *
	 * @return string
	 */
	public static function get_user_profile_image_url($user_id, $token) {

		return self::$fb_graph_url . self::$fb_api_version . '/' . $user_id . '/picture/?access_token=' . $token;
	}


	public static function get_fbp_review_url($page_id, $pg_token) {

		$url  = self::$fb_graph_url . self::$fb_api_version . '/' . $page_id . '/ratings';
		$args = '?access_token=' . $pg_token;
		$args .= '&fields=recommendation_type,created_time,review_text,reviewer,rating,has_rating,has_review';

		return $url . $args;
	}


	public static function get_fbp_info_url($acc_tok) {

		$url_acc = self::$fb_graph_url . self::$fb_api_version . '/me/accounts';
		$args    = '?access_token=' . $acc_tok;
		$args    .= '&fields=overall_star_rating,id,rating_count,name,page_token,access_token,engagement,picture';

		return $url_acc . $args;
	}


	public static function get_fbp_page_info_url($page_id, $page_access_tok) {

		$url_acc = self::$fb_graph_url . self::$fb_api_version . '/' . $page_id;
		$args    = '?access_token=' . $page_access_tok;

		$fld[] = 'followers_count';
		$fld[] = 'fan_count';
		$fld[] = 'rating_count';
		$fld[] = 'overall_star_rating';

		$args    .= '&fields=id,name,page_token,access_token,engagement,picture,'. implode(',', $fld);

		return $url_acc . $args;
	}


	public static function get_fbp_review_trans_key($page_id, $token) {

		$md = md5($page_id . $token);

		return '_trans_ekit_fbp_review_review_' . $md;
	}


	public static function get_fbf_overall_trans_key($page_id, $token) {

		$md = md5($page_id . $token);

		return '_trans_ekit_fbp_review_pg_info_' . $md;
	}


	public static function get_fb_page_overall_rating($page_id, $pg_acc_token) {

		$trans_key = self::get_fbf_overall_trans_key($page_id, $pg_acc_token);

		$trans_val = get_transient($trans_key);

		if(false !== $trans_val) {

			return array(
				'success' => true,
				'msg'     => 'Fetched from cached api call',
				'dt'      => $trans_val,
			);
		}

		try {

			$url     = self::get_fbp_page_info_url($page_id, $pg_acc_token);
			$request = wp_remote_get($url);

			if(is_wp_error($request)) {

				return array(
					'success' => false,
					'msg'     => __('API call failed to retrieve the facebook page info.', 'elementskit') . ' - ' . $request->get_error_message(),
				);
			}

			$body  = wp_remote_retrieve_body($request);
			$datum = json_decode($body);


			if(empty($datum->name)) {

				return $datum;
			}

			$extracted['rating']  = $datum->overall_star_rating;
			$extracted['count']   = $datum->rating_count;
			$extracted['pg_name'] = $datum->name;
			$extracted['pg_id']   = $datum->id;
			$extracted['pgt']     = $datum->page_token;
			$extracted['follower']     = $datum->followers_count;
			$extracted['fan']     = $datum->fan_count;
			$extracted['likes']   = empty($datum->engagement->count) ? 0 : intval($datum->engagement->count);
			$extracted['picture'] = empty($datum->picture->data->url) ? '' : $datum->picture->data->url;

			$expire = 86400 * 2;

			set_transient($trans_key, $extracted, $expire);

		} catch(\Exception $ex) {

			return array(
				'success' => false,
				'msg'     => __('API call failed to retrieve the facebook page info.', 'elementskit') . ' - ' . $ex->getMessage(),
			);
		}


		return array(
			'success' => true,
			'msg'     => 'Fetched and cached the calls',
			'dt'      => $extracted,
		);
	}


	public static function get_fb_reviews($pg_id, $pg_token) {

		$trans_name      = self::get_fbp_review_trans_key($pg_id, $pg_token);
		$transient_value = get_transient($trans_name);
		$result          = [];

		if(false !== $transient_value) {

			return $transient_value;
		}

		try {

			$url     = self::get_fbp_review_url($pg_id, $pg_token);
			$request = wp_remote_get($url);

			if(!is_wp_error($request)) {

				$body   = wp_remote_retrieve_body($request);
				$result = json_decode($body);

				$expiration_time = 86400;//in second
				set_transient($trans_name, $result, $expiration_time);
			}

		} catch(\Exception $ex) {

			$result = [];
		}

		return $result;
	}


	public static function get_data() {

		$data = Attr::instance()->utils->get_option('user_data', []);

		$pg_token = empty($data['fbp_review']['pg_token']) ? '' : $data['fbp_review']['pg_token'];
		$page_id  = empty($data['fbp_review']['pg_id']) ? '' : $data['fbp_review']['pg_id'];


		return [
			'pg_id'  => $page_id,
			'pg_tok' => $pg_token,
		];
	}
}