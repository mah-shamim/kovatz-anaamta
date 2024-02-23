<?php

namespace Elementor;

use ElementsKit_Lite\Core\Handler_Widget;
use ElementsKit_Lite\Libs\Framework\Attr;

class ElementsKit_Widget_Yelp_Handler extends Handler_Widget
{

    static function get_name()
    {
        return 'elementskit-yelp';
    }

    static function get_title()
    {
        return esc_html__('Yelp', 'elementskit');
    }

    static function get_icon()
    {
        return 'ekit-widget-icon eicon-favorite';
    }

    static function get_categories()
    {
        return ['elementskit'];
    }

	static function get_keywords() {
		return ['ekit', 'review', 'yelp', 'yelp review', 'social review'];
	}

    static function get_dir()
    {
        return \ElementsKit::widget_dir() . 'yelp/';
    }

    static function get_url()
    {
        return \ElementsKit::widget_url() . 'yelp/';
    }

    static function get_data() {

        $transient_name = 'ekit_yelp_feeds';
        $transient_value = get_transient($transient_name);

        $user_data = Attr::instance()->utils->get_option('user_data', []);
        $page = (!isset($user_data['yelp']['page'])) ? '' : ($user_data['yelp']['page']);
        $api = 'https://token.wpmet.com/providers/yelp.php?page=' . $page;
        $request = wp_remote_get($api);

        if (is_wp_error($request)) {
            return false;
        }

        $body = wp_remote_retrieve_body($request);
        $result = json_decode($body);
        $expiration_time = 86400;//in second
        set_transient($transient_name, $result, $expiration_time);
        return $result;

    }
}
