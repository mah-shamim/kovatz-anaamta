<?php

namespace ElementsKit\Widgets\Facebook_Feed;

use ElementsKit\Libs\Framework\Attr;
use ElementsKit_Lite\Core\Handler_Api;

defined('ABSPATH') || exit;

class Facebook_Feed_Api extends Handler_Api {

	public function config() {

		$this->prefix = 'widget/fb-feed';
	}


	public function post_remove_cache() {

		$data = $this->request->get_params();
		$idd = sanitize_key($data['provider_id']);

		if($idd == 'fb_page_feed') {

			$data = Attr::instance()->utils->get_option('user_data', []);

			if(empty($data['fb_feed']['page_id'])) {

				return [
					'success' => false,
					'msg'     => __('page id not found!', 'elementskit'),
				];
			}

			if(empty($data['fb_feed']['pg_token'])) {

				return [
					'success' => false,
					'msg'     => __('page access token not found!', 'elementskit'),
				];
			}

			$trans_key = self::get_fb_pg_post_call_transient_key($data['fb_feed']['page_id'], $data['fb_feed']['pg_token']);

			if(delete_transient($trans_key)) {

				return [
					'success' => true,
					'msg'     => __('Successfully cleaned', 'elementskit'),
				];
			}

			return [
				'success' => false,
				'msg'     => __('Cache not found!', 'elementskit'),
			];
		}

		return [
			'success' => false,
			'msg'     => __('Unknown provider key', 'elementskit'),
		];
	}


	public static function get_fb_pg_post_call_transient_key($page_id, $tok) {

		$md = md5($page_id . $tok);

		return 'ekit_fbf_pg_post_call_' . $md;
	}
}
