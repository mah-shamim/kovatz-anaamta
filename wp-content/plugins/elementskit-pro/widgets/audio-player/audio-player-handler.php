<?php
namespace Elementor;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Audio_Player_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

	static function get_name() {
		return 'elementskit-audio-player';
	}

	static function get_title() {
		return esc_html__( 'Audio Player', 'elementskit' );
	}

	static function get_icon() {
		return 'ekit ekit-audio-player ekit-widget-icon';
	}

	static function get_categories() {
		return [ 'elementskit' ];
	}

	static function get_keywords() {
		return ['ekit', 'audio', 'player', 'embed', 'youtube'];
	}

	static function get_dir() {
		return \ElementsKit::widget_dir() . 'audio-player/';
	}

	static function get_url() {
		return \ElementsKit::widget_url() . 'audio-player/';
	}
}