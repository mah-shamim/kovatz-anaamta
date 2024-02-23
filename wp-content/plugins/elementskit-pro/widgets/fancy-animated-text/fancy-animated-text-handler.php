<?php
namespace Elementor;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Fancy_Animated_Text_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

    static function get_name() {
        return 'elementskit-fancy-animated-text';
    }

    static function get_title() {
        return esc_html__( 'Fancy Animated Text', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit ekit-widget-icon ekit-fancy-heading';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'fancy heading', 'fancy animated text', 'animated text', 'advanced text', 'title', 'animation heading'];
	}

    static function get_help_url(){
        return "https://wpmet.com/doc/fancy-animated-text/";
    }

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'fancy-animated-text/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'fancy-animated-text/';
    }
}