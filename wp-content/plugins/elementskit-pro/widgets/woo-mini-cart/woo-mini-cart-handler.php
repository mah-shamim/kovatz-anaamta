<?php
namespace Elementor;


class ElementsKit_Widget_Woo_Mini_Cart_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    public function wp_init(){
        add_filter( 'woocommerce_add_to_cart_fragments', array($this, 'ekit_cart_count_total_fragments'), 10, 1 );
    }

    public function ekit_cart_count_total_fragments( $fragments ) {
        
        $fragments['.ekit-cart-items-count'] = '<span class="ekit-cart-items-count">' . '<span class="ekit-cart-content-count">'. WC()->cart->get_cart_contents_count() . '</span><span class="ekit-cart-content-separator"> - </span>' .  WC()->cart->get_cart_total(). '</span>';

        $fragments['.ekit-cart-count'] = '<span class="ekit-cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';

        return $fragments;
    }


    static function get_name() {
        return 'elementskit-woo-mini-cart';
    }

    static function get_title() {
        return esc_html__( 'Woo Mini Cart', 'elementskit' );
    }

    static function get_icon() {
        return 'ekit-widget-icon eicon-product-add-to-cart';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'woo', 'woo', 'woocommerce', 'mini', 'cart'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'woo-mini-cart/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'woo-mini-cart/';
    }
}