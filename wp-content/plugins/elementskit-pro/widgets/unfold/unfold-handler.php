<?php
namespace Elementor;

class ElementsKit_Widget_Unfold_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-unfold';
    }

    static function get_title() {
        return esc_html__( 'Unfold', 'elementskit' );
    }

    static function get_icon() {
        return ' ekit-widget-icon eicon-button';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'unfold', 'fold'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'unfold/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'unfold/';
    }
}