<?php

namespace Elementor;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Breadcrumb_Handler extends \ElementsKit_Lite\Core\Handler_Widget {


	public static function get_name() {
		return 'elementskit-breadcrumb';
	}


	public static function get_title() {
		return esc_html__('Breadcrumb', 'elementskit');
	}


	public static function get_icon() {
		return ' ekit-widget-icon eicon-button';
	}


	public static function get_categories() {
		return ['elementskit'];
	}

	static function get_keywords(){
		return ['ekit', 'breadcrumbs', 'crumbs', 'list'];
	}

	public static function get_dir() {
		return \ElementsKit::widget_dir() . 'breadcrumb/';
	}


	public static function get_url() {
		return \ElementsKit::widget_url() . 'breadcrumb/';
	}
}