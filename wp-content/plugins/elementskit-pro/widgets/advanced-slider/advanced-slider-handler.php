<?php
namespace Elementor;

class ElementsKit_Widget_Advanced_Slider_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

	static function get_name() {
		return 'elementskit-advanced-slider';
	}

	static function get_title() {
		return esc_html__( 'Advanced Slider', 'elementskit' );
	}

	static function get_icon() {
		return 'ekit ekit-widget-icon ekit-advanced-slider';
	}

	static function get_categories() {
		return [ 'elementskit' ];
	}

	static function get_keywords() {
		return ['ekit', 'advanced', 'slider', 'carousel', 'hero slider', 'image slider', 'slideshow', 'image carousel', 'vertical slider', 'horizontal slider'];
	}

	static function get_dir() {
		return \ElementsKit::widget_dir() . 'advanced-slider/';
	}

	static function get_url() {
		return \ElementsKit::widget_url() . 'advanced-slider/';
	}
}