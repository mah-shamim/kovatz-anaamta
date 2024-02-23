<?php
namespace Elementor;

defined('ABSPATH') || exit;
class ElementsKit_Widget_Price_Menu_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

    static function get_name() {
        return 'elementskit-price-menu';
    }

    static function get_title() {
        return esc_html__( 'Price Menu', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit ekit-widget-icon ekit-price-menu';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static public function get_keywords() {
		return ['ekit', 'pricing', 'menu', 'list', 'image'];
	}
    
	static function get_dir() {
		return \ElementsKit::widget_dir() . 'price-menu/';
	}

    static function get_url() {
        return \ElementsKit::widget_url() . 'price-menu/';
    }
}