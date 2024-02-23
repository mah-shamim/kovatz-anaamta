<?php

namespace ElementsKit\Widgets\Instagram_Feed;

use Elementor\ElementsKit_Widget_Instagram_Feed_Handler;
use ElementsKit\Libs\Framework\Attr;
use ElementsKit_Lite\Core\Handler_Api;

defined('ABSPATH') || exit;

class Instagram_Feed_Api extends Handler_Api {

	public function config() {

		$this->prefix = 'widget/instagram-feed';
	}


	public function post_remove_cache() {

		$data = $this->request->get_params();
		$idd = sanitize_key($data['provider_id']);

		if($idd == 'instagram_feed') {

			$data = Attr::instance()->utils->get_option('user_data', []);

			if(empty($data['instragram']['user_id'])) {

				return [
					'success' => false,
					'msg'     => __('No user id found!', 'elementskit'),
				];
			}

			$trans_key = ElementsKit_Widget_Instagram_Feed_Handler::get_transient_key($data['instragram']['user_id']);

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
}
