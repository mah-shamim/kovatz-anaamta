<?php
namespace ElementsKit\Widgets\Init;

defined( 'ABSPATH' ) || exit;

class Enqueue_Scripts{

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [$this, 'frontend_js']);
		add_action( 'wp_enqueue_scripts', [$this, 'frontend_css'], 99 );

		add_action( 'elementor/frontend/before_enqueue_scripts', [$this, 'elementor_js'] );
	}

	public function elementor_js() {
		wp_enqueue_script( 'elementskit-elementor-pro', \ElementsKit::widget_url() . 'init/assets/js/elementor.js',array( 'jquery', 'elementor-frontend', 'elementskit-elementor' ), \ElementsKit::version(), true );
	}

	public function frontend_js() {
		if(is_admin()){
			return;
		}
	}

	public function frontend_css() {
		if(!is_admin()){
			wp_enqueue_style( 'ekit-widget-styles-pro', \ElementsKit::widget_url() . 'init/assets/css/widget-styles-pro.css', ['ekit-widget-styles'], \ElementsKit::version() );
		};
	}
}