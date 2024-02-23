<?php
namespace Elementor;

defined('ABSPATH') || exit;
class ElementsKit_Widget_Stylish_List_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

    static function get_name() {
        return 'elementskit-stylish-list';
    }

    static function get_title() {
        return esc_html__( 'Stylish List', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit ekit-stylish-list ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_dir() {
		return \ElementsKit::widget_dir() . 'stylish-list/';
	}

    static function get_url() {
        return \ElementsKit::widget_url() . 'stylish-list/';
    }
}