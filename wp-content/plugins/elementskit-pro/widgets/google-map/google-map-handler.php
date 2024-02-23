<?php

namespace Elementor;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Google_Map_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

	static function get_name() {
		return 'elementskit-google-map';
	}


	static function get_title() {
		return esc_html__('Google Map', 'elementskit');
	}


	static function get_icon() {
		return ' ekit-widget-icon eicon-google-maps';
	}


	static function get_categories() {
		return ['elementskit'];
	}

	static function get_keywords() {
		return ['ekit', 'google', 'map', 'embed', 'location'];
	}

	static function get_dir() {
		return \ElementsKit::widget_dir() . 'google-map/';
	}


	static function get_url() {
		return \ElementsKit::widget_url() . 'google-map/';
	}
}