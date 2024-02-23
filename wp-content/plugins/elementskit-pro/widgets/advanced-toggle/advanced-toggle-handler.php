<?php
namespace Elementor;

class ElementsKit_Widget_Advanced_Toggle_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-advanced-toggle';
    }

    static function get_title() {
        return esc_html__( 'Advanced Toggle', 'elementskit' );
    }

    static function get_icon() {
        return 'eicon-toggle ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'advanced', 'toggle', 'collapsible', 'switch'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'advanced-toggle/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'advanced-toggle/';
    }

}