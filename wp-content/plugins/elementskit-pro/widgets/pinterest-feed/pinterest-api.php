<?php

namespace ElementsKit\Widgets\Pinterest_Feed;

defined('ABSPATH') || exit;

use ElementsKit_Lite\Core\Handler_Api;
use Elementor\ElementsKit_Widget_Pinterest_Feed_Handler;


class Pinterest_Api extends Handler_Api {

	public function __construct() {

		parent::__construct();

	}


	public function config() {
		$this->prefix = 'pinterest';
		$this->param  = "";
	}


	public function post_feed() {

		$data      = $this->request->get_params();
		$type      = trim($data['type']);
		$user_name = trim($data['username']);

		$transient_name  = ElementsKit_Widget_Pinterest_Feed_Handler::get_transient_name($user_name, $type);
		$transient_value = get_transient($transient_name);

		if(false !== $transient_value) {

			return [
				'success' => true,
				'msg'     => 'fetched from transient',
			];
		}


		try {

			$url  = ElementsKit_Widget_Pinterest_Feed_Handler::get_feed_url($user_name, $type, $data['board']);
			$args = '';

			$request = wp_remote_get($url . $args);

			if(!is_wp_error($request)) {


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

