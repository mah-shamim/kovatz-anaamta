<?php
namespace Elementor;

use ElementsKit_Lite\Libs\Framework\Attr;

class ElementsKit_Widget_Twitter_Feed_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    public function wp_init(){
        include( self::get_dir().'lib/TwitterAPIExchange.php' );
		include(self::get_dir().'classes/settings.php');
    }

    static function get_name() {
        return 'elementskit-twitter-feed';
    }

    static function get_title() {
        return esc_html__( 'Twitter Feed', 'elementskit' );
    }

    static function get_icon() {
        return 'eicon-twitter-feed ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'twitter', 'feed', 'social feed', 'twitter integration'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'twitter-feed/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'twitter-feed/';
    }

	static function get_data(){

		$data = Attr::instance()->utils->get_option('user_data', []);

		$name = (isset($data['twitter']) && !empty($data['twitter']['name']) ) ? $data['twitter']['name'] : '';

		$access_token = (isset($data['twitter']) && !empty($data['twitter']['access_token']) ) ? $data['twitter']['access_token'] : '';

		$access_token_secret = (isset($data['twitter']) && !empty($data['twitter']['access_token_secret']) ) ? $data['twitter']['access_token_secret'] : '';

		$consumer_key = (isset($data['twitter']) && !empty($data['twitter']['consumer_key']) ) ? $data['twitter']['consumer_key'] : '';

		$consumer_secret = (isset($data['twitter']) && !empty($data['twitter']['consumer_secret']) ) ? $data['twitter']['consumer_secret'] : '';

		return [
			'name' => $name,
			'access_token' => $access_token,
			'access_token_secret' => $access_token_secret,
			'consumer_key' => $consumer_key,
			'consumer_secret' => $consumer_secret,
		];
	}


}