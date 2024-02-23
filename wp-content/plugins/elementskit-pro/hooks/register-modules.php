<?php
namespace ElementsKit\Hooks;

defined( 'ABSPATH' ) || exit;

class Register_Modules {
	use \ElementsKit\Traits\Singleton;

	public function __construct() {
		add_filter( 'elementskit/modules/list', [$this, 'get_list'] );
	}


	public function get_list($list) {


		return array_merge($list, [
			'parallax' => [
				'slug'              => 'parallax',
				'package'           => 'pro',
				'title'             => 'Parallax Effects',
				'base_class_name'   => '\ElementsKit\Modules\Parallax\Init',
			],
			'sticky-content' => [
				'slug'              => 'sticky-content',
				'package'           => 'pro',
				'title'             => 'Sticky Content',
				'base_class_name'   => '\ElementsKit\Modules\Sticky_Content\Init',
			],
			'header-footer' => [
				'slug'              => 'header-footer',
				'package'           => 'pro',
				'title'             => 'Header Footer Builder',
				'base_class_name'   => '\ElementsKit\Modules\Header_Footer\Init',
			],
			'facebook-messenger' => [
				'slug'              => 'facebook-messenger',
				'package'           => 'pro',
				'title'             => 'Facebook Messenger',
				'base_class_name'   => '\ElementsKit\Modules\Facebook_Messenger\Init',
			],
			'conditional-content' => [
				'slug'              => 'conditional-content',
				'package'           => 'pro',
				'title'             => 'Conditional Content',
				'base_class_name'   => '\ElementsKit\Modules\Conditional_Content\Init',
			],
			'copy-paste-cross-domain' => [
				'slug'              => 'copy-paste-cross-domain',
				'package'           => 'pro',
				'title'             => 'Cross-Domain Copy Paste',
				'base_class_name'   => '\ElementsKit\Modules\Copy_Paste_Cross_Domain\Init',
			],
			'advanced-tooltip' => [
				'slug'              => 'advanced-tooltip',
				'package'           => 'pro',
				'title'             => 'Advanced Tooltip',
				'base_class_name'   => '\ElementsKit\Modules\Advanced_Tooltip\Init',
			],
			'pro-form-reset-button' => [
				'slug'              => 'pro-form-reset-button',
				'package'           => 'pro',
				'title'             => 'Reset Button For Elementor Pro Form',
				'base_class_name'   => '\ElementsKit\Modules\Pro_Form_Reset_Button\Init',
			],
			'google_sheet_for_elementor_pro_form' => [
				'slug'              => 'google-sheet-for-elementor-pro-form',
				'package'           => 'pro',
				'title'             => 'Google Sheet For Elementor Pro Form',
				'base_class_name'   => '\ElementsKit\Modules\Google_Sheet_Elementor_Pro_Form\Init',
			],
			'masking' => [
				'slug'              => 'masking',
				'package'           => 'pro',
				'title'             => 'Masking',
				'base_class_name'   => '\ElementsKit\Modules\Masking\Init',
			],
			'particles' => [
				'slug'              => 'particles',
				'package'           => 'pro',
				'title'             => 'Particles',
				'base_class_name'   => '\ElementsKit\Modules\Particles\Init',
			],
			'wrapper-link' => [
				'slug'              => 'wrapper-link',
				'package'           => 'pro',
				'title'             => 'Wrapper Link',
				'base_class_name'   => '\ElementsKit\Modules\Wrapper_Link\Init',
			],
		]);
	}
}