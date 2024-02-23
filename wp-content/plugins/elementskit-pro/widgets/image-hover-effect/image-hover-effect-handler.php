<?php
namespace Elementor;
class ElementsKit_Widget_Image_Hover_Effect_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

    static function get_name() {
        return 'elementskit-image-hover-effect';
    }

    static function get_title() {
        return esc_html__( 'Image Hover Effect', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit ekit-widget-icon ekit-image-hover-effect';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'image', 'hover', 'effect', 'promo', 'box', 'promo box', 'advertise', 'adds', 'animated box', 'interactive box'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'image-hover-effect/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'image-hover-effect/';
    }
}