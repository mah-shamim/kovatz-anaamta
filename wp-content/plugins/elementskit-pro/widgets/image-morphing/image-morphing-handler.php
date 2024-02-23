<?php
namespace Elementor;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Image_Morphing_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

	static function get_name() {
		return 'elementskit-image-morphing';
	}

	static function get_title() {
		return esc_html__( 'Image Morphing', 'elementskit' );
	}

	static function get_icon() {
		return 'ekit ekit-image-morphing ekit-widget-icon';
	}

	static function get_categories() {
		return [ 'elementskit' ];
	}

	static function get_keywords() {
		return ['ekit', 'morphing', 'image', 'blob', 'animation', 'svg', 'mask'];
	}

	static function get_dir() {
		return \ElementsKit::widget_dir() . 'image-morphing/';
	}

	static function get_url() {
		return \ElementsKit::widget_url() . 'image-morphing/';
	}
}