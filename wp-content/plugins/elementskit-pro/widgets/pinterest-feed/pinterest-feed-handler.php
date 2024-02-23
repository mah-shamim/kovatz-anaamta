<?php

namespace Elementor;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Pinterest_Feed_Handler extends \ElementsKit_Lite\Core\Handler_Widget {


	public static function get_name() {
		return 'elementskit-pinterest-feed';
	}


	public static function get_title() {
		return esc_html__('Pinterest Feed', 'elementskit');
	}


	public static function get_icon() {
		return 'ekit ekit-widget-icon ekit-pinterest';
	}


	public static function get_categories() {
		return ['elementskit'];
	}

	static function get_keywords() {
		return ['ekit', 'pinterest', 'feed', 'social feed', 'pinterest integration'];
	}

	public static function get_dir() {
		return \ElementsKit::widget_dir() . 'pinterest-feed/';
	}


	public static function get_url() {
		return \ElementsKit::widget_url() . 'pinterest-feed/';
	}


	public function wp_init() {
		new \ElementsKit\Widgets\Pinterest_Feed\Pinterest_Api();
	}


	public static function get_feed_url($user_name, $type = 'home', $board_name = 'feed') {

		if($type === 'board') {

			return 'https://pinterest.com/' . $user_name . '/' . $board_name . '.rss';
		}


		return 'https://pinterest.com/' . $user_name . '/feed.rss';
	}


	public static function get_transient_name($user_name, $board) {

		return '_trans_ekit_pinterest_feed_' . $user_name . '_' . $board;
	}


	public static function get_the_feed($user_name, $type, $board) {

		$user_name = trim($user_name);

		$trans_name  = self::get_transient_name($user_name, $board);
		$trans_value = get_transient($trans_name);

		if(false !== $trans_value) {

			return [
				'success' => true,
				'data'    => $trans_value,
				'msg'     => 'fetched from transient',
			];
		}


		try {

			$url  = self::get_feed_url($user_name, $type, $board);
			$args = '';

			$request = wp_remote_get($url . $args);

			if(!is_wp_error($request) && wp_remote_retrieve_response_code($request) === 200) {

				$body = wp_remote_retrieve_body($request);

				//todo - add checking for html 404 page!!!
				//$str     = str_replace(['<![CDATA[', ']]>', '<br />'], '', $body);
				$str     = $body;
				$xml     = simplexml_load_string($str);
				$channel = $xml->channel;
				$conf    = [];

				$conf['title']         = $channel->title->__toString();
				$conf['link']          = $channel->link->__toString();
				$conf['description']   = $channel->description->__toString();
				$conf['lastBuildDate'] = $channel->lastBuildDate->__toString();

				foreach($channel->item as $item) {

					$tmp = [];
					$tmp['title']       = $item->title->__toString();
					$tmp['link']        = $item->link->__toString();
					$tmp['description'] = $item->description->__toString();
					$tmp['guid']        = $item->guid->__toString();
					$tmp['pubDate']     = $item->pubDate->__toString();

					$conf['item'][] = $tmp;
				}


				/**
				 * We will only cache the api call for 30 minutes
				 * As this will not be called automatically, we are storing it for short time
				 */
				$expiration_time = 86400;


				/**
				 * If every thing goes okay
				 */
				set_transient($trans_name, $conf, $expiration_time);


				return [
					'success' => true,
					'data'    => $conf,
					'msg'     => 'data successfully fetched and cached',
				];
			}

		} catch(\Exception $ex) {

			return [
				'success' => false,
				'data'    => [],
				'msg'     => 'data fetching failed - ' . $ex->getMessage(),
			];
		}


		return [
			'success' => false,
			'data'    => [],
			'msg'     => 'Please fetch the feed first',
		];
	}
}