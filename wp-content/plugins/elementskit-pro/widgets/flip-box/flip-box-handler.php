<?php
namespace Elementor;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Flip_Box_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

    static function get_name() {
        return 'elementskit-flip-box';
    }

    static function get_title() {
        return esc_html__( 'Flip Box', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit ekit-flip-box ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'flip', 'box', 'flipbox', 'flip card', 'card', 'rotate', 'animation', 'content', 'creative'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'flip-box/';
    }
}