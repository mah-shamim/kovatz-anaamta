<?php
namespace Elementor;


class ElementsKit_Widget_Gallery_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name()
    {
        return 'elementskit-gallery';
    }

    static function get_title()
    {
        return esc_html__( 'Gallery', 'elementskit' );
    }

    static function get_icon()
    {
        return 'ekit ekit-image-gallery ekit-widget-icon ';
    }

    static function get_categories()
    {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'gallery', 'image', 'grid gallery', 'masonry gallery', 'lightbox'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'gallery/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'gallery/';
    }
}