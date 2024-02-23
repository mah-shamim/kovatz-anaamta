<?php
namespace Elementor;

use Elementor\Controls_Manager;

defined('ABSPATH') || die();

class ElementsKit_Wrapper_Link {

	public  function __construct() {
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'register_wrapper_section' ], 1 );
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'register_wrapper_section' ], 1 );
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_wrapper_section' ], 7 );
		add_action( 'elementor/frontend/before_render', [ $this, 'before_section_render' ], 1 );
	}

	public function register_wrapper_section($element) {

		$element->start_controls_section(
			'elementskit_wrapper_link_section',
			[
				'label' => __( 'Elementskit Wrapper Link', 'elementskit' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'elementskit_wrapper_link_enable',
			[
				'label' => esc_html__( 'Enable Link', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$element->add_control(
			'elementskit_wrapper_link',
			[
				'label' => __( 'Link', 'elementskit' ),
				'type'  => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'options' => [ 'url', 'is_external', 'nofollow' ],
				'condition' => [
					'elementskit_wrapper_link_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	public function before_section_render($element) {
		$wrapper_settings = $element->get_settings_for_display( 'elementskit_wrapper_link' );

		if ( ! empty( $wrapper_settings['url'] ) &&  $wrapper_settings ) {
			$element->add_render_attribute(
				'_wrapper',
				[
					'data-wrapper-link' => wp_json_encode( $wrapper_settings ),
					'style' => 'cursor: pointer'
				]
			);
		}
	}
}
