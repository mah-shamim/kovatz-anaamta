<?php

namespace ElementsKit\Widgets\Behance_Feed;

defined('ABSPATH') || exit;

use ElementsKit_Lite\Core\Handler_Api;
use Elementor\ElementsKit_Widget_Behance_Feed_Handler;


class Behance_Api extends Handler_Api {

	public function __construct() {

		parent::__construct();
	}


	public function config() {
		$this->prefix = 'behance';
		$this->param  = "";
	}


	public function post_del_cache() {

		$data      = $this->request->get_params();
		$user_name = trim($data['username']);

		$trans_usr_key = ElementsKit_Widget_Behance_Feed_Handler::get_behance_feed_user_info_key($user_name);
		$trans_feed_key = ElementsKit_Widget_Behance_Feed_Handler::get_behance_feed_feed_key($user_name);

		delete_transient($trans_usr_key);
		delete_transient($trans_feed_key);

		return [
			'success' => true,
			'msg'     => 'cache successfully deleted',
		];
	}


	public function post_refresh_user() {

		$data      = $this->request->get_params();
		$user_name = trim($data['username']);
		$msg       = '';

		$usr = ElementsKit_Widget_Behance_Feed_Handler::fetch_user_info_feed($user_name, $msg);

		return [
			'success' => true,
			'msg'     => 'fetched from transient',
			'dt'     => $usr,
		];
	}


	public function post_feed() {

		$data      = $this->request->get_params();
		$user_name = trim($data['username']);

		$transient_name  = ElementsKit_Widget_Behance_Feed_Handler::get_transient_name($user_name);
		$transient_value = get_transient($transient_name);

		if(false !== $transient_value) {

			return [
				'success' => true,
				'msg'     => 'fetched from transient',
			];
		}

		try {

			$url     = 'https://www.behance.net/feeds/user';
			$args    = '?username=' . $user_name;
			$request = wp_remote_get($url . $args);

			if(!is_wp_error($request)) {

				$body = wp_remote_retrieve_body($request);

				//todo - add checking for html 404 page!!!
				$str     = str_replace(['<![CDATA[', ']]>', '<br />'], '', $body);
				$xml     = simplexml_load_string($str);
				$channel = $xml->channel;
				$conf    = [];


				$conf['title']       = $channel->title->__toString();
				$conf['link']        = $channel->link->__toString();
				$conf['description'] = $channel->description->__toString();
				$conf['pubDate']     = $channel->pubDate->__toString();

				foreach($channel->item as $item) {

					$tmp = [];

					$tmp['title']   = $item->title->__toString();
					$tmp['link']    = $item->link->__toString();
					$tmp['guid']    = $item->guid->__toString();
					$tmp['pubDate'] = $item->pubDate->__toString();
					$tmp['cover']   = empty($item->description->img->attributes()->src) ? '' : $item->description->img->attributes()->src->__toString();

					$str = str_replace('https://www.behance.net/gallery/', '', $item->link);
					$str = explode('/', $str);

					$tmp['id']   = $str[0];
					$tmp['name'] = $str[1];

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
				set_transient($transient_name, $conf, $expiration_time);

				return [
					'success' => true,
					'msg'     => 'data successfully fetched',
				];
			}

		} catch(\Exception $ex) {

			return [
				'success' => false,
				'msg'     => 'data fetching failed - ' . $ex->getMessage(),
			];
		}

		return [
			'success' => false,
			'msg'     => 'No data retrieved - please give a valid username',
		];
	}
}

