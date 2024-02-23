<?php
namespace Elementor;

class ElementsKit_Widget_Coupon_Code_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

    static function get_name() {
        return 'elementskit-coupon-code';
    }

    static function get_title() {
        return esc_html__( 'Coupon Code', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit ekit-widget-icon ekit-coupon-code';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'coupon-code/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'coupon-code/';
    }
}