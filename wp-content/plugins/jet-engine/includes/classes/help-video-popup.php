<?php
/**
 * Posts search handler class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Engine_Help_Video_Popup {

	private $label;
	private $popup_title;
	private $embed;
	private $more_url;

	public static $style_is_set = false;
	
	private $script_is_set = false;
	private $tempalte_is_set = false;
	private $id = null;

	public function __construct( $args = array() ) {

		$this->label = isset( $args['label'] ) ? $args['label'] : __( 'What is this?', 'jet-engine' );
		$this->popup_title = isset( $args['popup_title'] ) ? $args['popup_title'] : '';
		$this->embed = isset( $args['embed'] ) ? $args['embed'] : false;
		$this->more_url = isset( $args['more_url'] ) ? $args['more_url'] : false;
		$this->id = $this->get_id();

	}

	public function get_id() {
		$chars = '0123456789abcdefghijklmnopqrstuvwxyz';
		$length = 10;
		return substr( str_shuffle( str_repeat( $chars, ceil( $length/strlen( $chars ) ) ) ), 1, $length );

	}

	public function wp_page_popup( $selector = '.page-title-action' ) {

		$this->setup_template();
		$this->setup_default_vars();
		$this->setup_js_var( 'JetListingsVideoPageTrigger', 1 );
		$this->setup_js_var( 'JetListingsVideoTriggerSelector', $selector );
		$this->setup_script();
		$this->setup_styles();

	}

	public function popup_trigger( $is_link = false ) {

		$url = ( $is_link ) ? $this->embed : '#';

		printf(
			'<a href="%3$s" class="jet-listings-video-popup-trigger" target="_blank">%2$s%1$s</a>',
			$this->label, $this->get_trigger_icon(), $url
		);

		if ( ! $is_link ) {
			$this->setup_template();
			$this->setup_default_vars();
			$this->setup_script();
		}
		
		$this->setup_styles();
	}

	public function setup_script() {
		
		if ( ! $this->script_is_set ) {
			add_action( 'admin_footer', array( $this, 'print_script' ), 99 );
			 $this->script_is_set = true;
		}
		
	}

	public function setup_styles() {
		
		if ( ! self::$style_is_set ) {
			add_action( 'admin_footer', array( $this, 'print_style' ), 99 );
			self::$style_is_set = true;
		}
		
	}

	public function setup_template() {
		
		if ( !  $this->tempalte_is_set ) {
			add_action( 'admin_footer', array( $this, 'print_template' ), 99 );
			 $this->tempalte_is_set = true;
		}
		
	}

	public function get_trigger_icon() {
		return '<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"></rect><g><path d="M19 15V5c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h13c1.1 0 2-.9 2-2zM8 14V6l6 4z"></path></g></svg>';
	}

	public function print_script() {
		echo '<script>
		(function( $ ) {
			
			"use strict";
			
			if ( ! window.JetListingsVideoSettings' .  $this->id . ' ) {
				return;
			}

			var $popup = $( "#popup_' .  $this->id . '" );
			var $document = $( document );

			if ( window.JetListingsVideoPageTrigger' .  $this->id . ' ) {
				console.log( $( window.JetListingsVideoTriggerSelector' .  $this->id . ' ) );
				$( window.JetListingsVideoTriggerSelector' .  $this->id . ' ).after( \'<a href="#" class="jet-listings-video-popup-trigger">' . $this->get_trigger_icon() . '\' + window.JetListingsVideoSettings' .  $this->id . '.label + \'</a>\' );
			}

			$document.on( \'click.JetListings\', \'.jet-listings-video-popup-trigger\', function( event ) {
				event.preventDefault();
				$popup.addClass( \'jet-listings-popup-active\' );
				$popup.find( \'iframe\' ).attr( \'src\', window.JetListingsVideoSettings' .  $this->id . '.embed );
			} );

			$document.on( \'click.JetListings\', \'.jet-listings-video-popup__overlay, .jet-listings-video-popup__close\', function() {
				$popup.removeClass( \'jet-listings-popup-active\' );
				$popup.find( \'iframe\' ).attr( \'src\', \'\' );
			} );
		})( jQuery );
		</script>';
	}

	public function print_style() {
		echo '<style>
			.jet-listings-video-popup-trigger {
				display: inline-flex;
				align-items: center;
				margin: 0 0 0 15px;
				text-decoration: none;
				position: relative;
				top: 2px;
			}
			.rtl .jet-listings-video-popup-trigger {
				margin: 0 15px 0 0;
			}
			.jet-listings-video-popup-trigger svg {
				margin: 0 4px 0 0;
			}
			.rtl .jet-listings-video-popup-trigger svg {
				margin: 0 0 0 4px;
			}
			.jet-listings-video-popup-trigger path {
				fill: currentColor;
			}
			.jet-listings-video-popup {
				display: none;
				justify-content: center;
				align-items: center;
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				z-index: 999;
			}
			.jet-listings-video-popup.jet-listings-popup-active {
				display: flex !important;
			}
			.jet-listings-video-popup__overlay {
				position: absolute;
				left: 0;
				right: 0;
				top: 0;
				bottom: 0;
				background: rgba( 0, 0, 0, .8 );
				transition: opacity 200ms linear;
				opacity: 0;
				z-index: 1000;
			}
			.jet-listings-popup-active .jet-listings-video-popup__overlay {
				opacity: 1;
			}
			.jet-listings-video-popup__close {
				position: absolute;
				right: 15px;
				top: 17px;
				width: 24px;
				height: 24px;
				cursor: pointer;
				opacity: .5;
			}
			.rtl .jet-listings-video-popup__close {
				right: auto;
				left: 15px;
			}
			.jet-listings-video-popup__content {
				background: #fff;
				width: 560px;
				padding: 0;
				position: relative;
				transition: opacity 200ms linear;
				opacity: 0;
				z-index: 1001;
			}
			.jet-listings-video-popup__content iframe {
				display: block;
			}
			.jet-listings-popup-active .jet-listings-video-popup__content {
				opacity: 1;
			}
			.jet-listings-video-popup__heading {
				margin: 0;
				padding: 20px;
			}

		</style>';
	}

	public function print_template() {
		echo '<div class="jet-listings-video-popup" id="popup_' .  $this->id . '" style="display: none;">
			<div class="jet-listings-video-popup__overlay"></div>
			<div class="jet-listings-video-popup__content">
			<div class="jet-listings-video-popup__close">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M14.95 6.46L11.41 10l3.54 3.54-1.41 1.41L10 11.42l-3.53 3.53-1.42-1.42L8.58 10 5.05 6.47l1.42-1.42L10 8.58l3.54-3.53z"/></g></svg>
			</div>
			<h3 class="jet-listings-video-popup__heading">' . $this->popup_title . '</h3>
			<iframe width="560" height="315" src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
		</div>';
	}

	public function setup_default_vars() {
		$this->setup_js_var( 'JetListingsVideoSettings', array(
			'label' => $this->label,
			'embed' => $this->embed,
		) );
	}

	public function setup_js_var( $name, $data ) {
		add_action( 'admin_footer', function() use ( $name, $data ) {
			printf( '<script>var %1$s%3$s = %2$s;</script>', $name, json_encode( $data ), $this->id );
		} );
	}

}
