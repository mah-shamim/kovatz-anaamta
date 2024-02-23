<?php
namespace Elementor;


class ElementsKit_Widget_Video_Gallery_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-video-gallery';
    }

    static function get_title() {
        return esc_html__( 'Video Gallery', 'elementskit' );
    }

    static function get_icon() {
        return 'eicon-youtube ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'video', 'player', 'embed', 'youtube', 'vimeo', 'dailymotion' ];
	}
    
    static function get_dir() {
        return \ElementsKit::widget_dir() . 'video-gallery/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'video-gallery';
    }
}