<?php
namespace Elementor;

class ElementsKit_Widget_Chart_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-chart';
    }

    static function get_title() {
        return esc_html__( 'Chart', 'elementskit' );
    }

    static function get_icon() {
        return 'eicon-shape ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'charts', 'bar charts', 'line charts', 'radar charts', 'pie charts', 'doughnut charts'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'chart/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'chart/';
    }

    public function scripts(){
       wp_register_script( 'chart-kit-js', self::get_url() . 'assets/js/chart.js', array( 'jquery' ), false, true );
    }
}
