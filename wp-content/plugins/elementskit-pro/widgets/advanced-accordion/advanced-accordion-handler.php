<?php
namespace Elementor;

class ElementsKit_Widget_Advanced_Accordion_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-advance-accordion';
    }

    static function get_title() {
        return esc_html__( 'Advanced Accordion', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit  ekit-widget-icon ekit-accordion';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'accordion', 'advanced accordion', 'advanced', 'faq', 'faq schema'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'advance-accordion/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'advance-accordion/';
    }
}