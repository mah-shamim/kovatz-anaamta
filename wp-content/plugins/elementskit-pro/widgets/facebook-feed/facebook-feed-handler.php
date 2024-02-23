<?php

namespace Elementor;

use ElementsKit_Lite\Libs\Framework\Attr;

class ElementsKit_Widget_Facebook_Feed_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

	public function wp_init() {
		include(self::get_dir() . 'classes/settings.php');

		(new \ElementsKit\Widgets\Facebook_Feed\Facebook_Feed_Api());
	}


	static function get_name() {
		return 'elementskit-facebook-feed';
	}


	static function get_title() {
		return esc_html__('Facebook Feed', 'elementskit');
	}


	static function get_icon() {
		return 'eicon-fb-feed ekit-widget-icon ';
	}


	static function get_categories() {
		return ['elementskit'];
	}

	static function get_keywords() {
		return ['ekit', 'facebook', 'feed', 'social feed', 'fb', 'facebook integration'];
	}

	static function get_dir() {
		return \ElementsKit::widget_dir() . 'facebook-feed/';
	}


	static function get_url() {
		return \ElementsKit::widget_url() . 'facebook-feed/';
	}


	public static function get_data() {

		$data = Attr::instance()->utils->get_option('user_data', []);

		$access = empty($data['fb_feed']['pg_token']) ? '' : $data['fb_feed']['pg_token'];
		$page_id = empty($data['fb_feed']['page_id']) ? '' : $data['fb_feed']['page_id'];


		return [
			'pg_id'  => $page_id,
			'pg_tok' => $access,
		];
	}
}