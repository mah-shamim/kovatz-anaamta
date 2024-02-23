<?php

namespace ElementsKit\Widgets\Facebook_Review;

use ElementsKit\Libs\Framework\Attr;
use ElementsKit_Lite\Core\Handler_Api;

defined('ABSPATH') || exit;

class Facebook_Review_Api extends Handler_Api {

	public function config() {

		$this->prefix = 'widget/fb-pg-review';
	}


	public function post_remove_cache() {

		$data = $this->request->get_params();
		$idd = sanitize_key($data['provider_id']);

		if($idd == 'fb_page_reviews') {

			$data = Attr::instance()->utils->get_option('user_data', []);

			if(empty($data['fbp_review']['pg_id'])) {

				return [
					'success' => false,
					'msg'     => __('page id not found!', 'elementskit'),
				];
			}

			if(empty($data['fbp_review']['pg_token'])) {

				return [
					'success' => false,
					'msg'     => __('page access token not found!', 'elementskit'),
				];
			}

			$trans_key = self::get_fbf_overall_trans_key($data['fbp_review']['pg_id'], $data['fbp_review']['pg_token']);

			if(delete_transient($trans_key)) {

				$trans_key = self::get_fbp_review_trans_key($data['fbp_review']['pg_id'], $data['fbp_review']['pg_token']);

				delete_transient($trans_key);

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

	public static function get_fbp_review_trans_key($page_id, $token) {

		$md = md5($page_id . $token);

		return '_trans_ekit_fbp_review_review_' . $md;
	}

	public static function get_fbf_overall_trans_key($page_id, $token) {

		$md = md5($page_id . $token);

		return '_trans_ekit_fbp_review_pg_info_' . $md;
	}
}
