<?php

namespace Elementor;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Popup_Modal_Handler extends \ElementsKit_Lite\Core\Handler_Widget {


	public static function get_name() {
		return 'elementskit-popup-modal';
	}

	public static function get_title() {
		return esc_html__('Popup Modal', 'elementskit');
	}

	public static function get_icon() {
		return ' ekit-widget-icon eicon-button';
	}

	public static function get_categories() {
		return ['elementskit'];
	}

	static function get_keywords() {
		return ['ekit', 'popups', 'modal', 'ekit modal'];
	}
    
	public static function get_dir() {
		return \ElementsKit::widget_dir() . 'popup-modal/';
	}

	public static function get_url() {
		return \ElementsKit::widget_url() . 'popup-modal/';
	}
}