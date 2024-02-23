<?php

namespace ElementsKit\Widgets\Dribble_Feed;

defined('ABSPATH') || exit;

use Elementor\ElementsKit_Widget_Dribble_Feed_Handler;
use ElementsKit_Lite\Core\Handler_Api;


class Dribble_Api extends Handler_Api {

	public function config() {
		$this->prefix = 'widget/dribble';
		$this->param  = "";
	}


	public function post_remove_cache() {

		$data = $this->request->get_params();
		$idd = sanitize_key($data['provider_id']);

		if($idd == 'dribble_feed') {

			$trans_key = ElementsKit_Widget_Dribble_Feed_Handler::$transient_name;

			if(delete_transient($trans_key)) {

				$trans_key = ElementsKit_Widget_Dribble_Feed_Handler::$transient_name_user;

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
}
