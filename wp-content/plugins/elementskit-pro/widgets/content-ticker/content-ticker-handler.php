<?php
namespace Elementor;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Content_Ticker_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

    static function get_name() {
        return 'elementskit-content-ticker';
    }

    static function get_title() {
        return esc_html__( 'Content Ticker', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit ekit-widget-icon ekit-content-ticker';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords(){
        return ['ekit','blog', 'content ticker','news ticker', 'post ticker', 'posts ticker','custom ticker'];
    }

    static function get_help_url(){
        return "https://wpmet.com/doc/content-ticker";
    }

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'content-ticker/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'content-ticker/';
    }
}