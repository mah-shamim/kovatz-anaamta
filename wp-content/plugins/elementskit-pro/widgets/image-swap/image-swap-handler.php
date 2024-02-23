<?php
namespace Elementor;

class ElementsKit_Widget_Image_Swap_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

    static function get_name() {
        return 'elementskit-image-swap';
    }

    static function get_title() {
        return esc_html__( 'Image Swap', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit ekit-widget-icon ekit-image-swap';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'image', 'swap', 'effect', 'hover effect'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'image-swap/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'image-swap/';
    }

}