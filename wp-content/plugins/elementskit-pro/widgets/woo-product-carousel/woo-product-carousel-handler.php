<?php
namespace Elementor;


class ElementsKit_Widget_Woo_Product_Carousel_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-woo-product-carousel';
    }

    static function get_title() {
        return esc_html__( 'Woo Product Carousel', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit-widget-icon eicon-carousel';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'woo product', 'woo', 'woocommerce', 'product', 'product carousel', 'carousel', 'sale'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'woo-product-carousel/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'woo-product-carousel/';
    }
}