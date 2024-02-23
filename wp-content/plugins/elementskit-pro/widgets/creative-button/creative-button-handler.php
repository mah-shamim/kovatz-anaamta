<?php
namespace Elementor;

class ElementsKit_Widget_Creative_Button_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-creative-button';
    }

    static function get_title() {
        return esc_html__( 'Creative Button', 'elementskit' );
    }

    static function get_icon() {
        return ' ekit-widget-icon eicon-button';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'call', 'link', 'btn', 'creative', 'button', 'creative button'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'creative-button/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'creative-button/';
    }
}