<?php
namespace Elementor;


class ElementsKit_Widget_Team_Slider_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-team-slider';
    }

    static function get_title() {
        return esc_html__( 'Team Carousel Slider', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit ekit-team-carousel-slider ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'team-slider/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'team-slider/';
    }


}