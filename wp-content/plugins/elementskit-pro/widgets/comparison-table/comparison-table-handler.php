<?php
namespace Elementor;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Comparison_Table_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

	static function get_name() {
		return 'elementskit-comparison-table';
	}

	static function get_title() {
		return esc_html__( 'Comparison Table', 'elementskit' );
	}

	static function get_icon() {
		return 'ekit ekit-flip-box ekit-widget-icon';
	}

	static function get_categories() {
		return [ 'elementskit' ];
	}

	static function get_keywords() {
		return ['ekit', 'comparison', 'list', 'lists'];
	}

	static function get_dir() {
		return \ElementsKit::widget_dir() . 'comparison-table/';
	}

	static function get_url() {
		return \ElementsKit::widget_url() . 'comparison-table/';
	}
}