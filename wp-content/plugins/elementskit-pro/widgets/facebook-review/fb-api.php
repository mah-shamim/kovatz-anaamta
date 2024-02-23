<?php

namespace ElementsKit\Widgets\Facebook_Review;

defined('ABSPATH') || exit;


class Fb_API {

	private static $fb_base_url = 'https://www.facebook.com/';
	private static $fb_graph_url = 'https://graph.facebook.com/';
	private static $fb_api_version = 'v8.0';
	public static $ok_fbf_info_cache = 'ekit_fb_page_info_cache_';


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
		$args    .= '&fields=overall_star_rating,id,rating_count,name,page_token,access_token,engagement,picture';

		return $url_acc . $args;
	}
}