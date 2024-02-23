<?php
namespace Elementor;


class ElementsKit_Widget_table_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-table';
    }

    static function get_title() {
        return esc_html__( 'Table', 'elementskit' );
    }

    static function get_icon() {
        return 'eicon-table ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

	static function get_keywords() {
		return ['ekit', 'table', 'data table', 'export table', 'CSV', 'comparison table', 'grid'];
	}

    static function get_dir() {
        return \ElementsKit::widget_dir() . 'table/';
    }

    static function get_url() {
        return \ElementsKit::widget_url() . 'table/';
    }
}