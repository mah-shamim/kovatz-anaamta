<?php

namespace Elementor;

class ElementsKit_Extend_Sticky{

    public function __construct() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'register_controls' ], 6 );
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_controls' ], 6 );
		
		// Flexbox Container support
		add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ) );
	}

	public function register_controls( Controls_Stack $element ) {
		$element->start_controls_section(
			'section_scroll_effect',
			[
				'label' => esc_html__( 'ElementsKit Sticky', 'elementskit' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'ekit_sticky',
			[
				'label' => esc_html__( 'Sticky', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'elementskit' ),
					'top' 				=> esc_html__( 'Top', 'elementskit' ),
					'bottom' 			=> esc_html__( 'Bottom', 'elementskit' ),
					'column' 			=> esc_html__( 'Column', 'elementskit' ),
					'show_on_scroll_up' => esc_html__( 'Show on Scroll Up', 'elementskit' ),
				],
				'prefix_class'	=> 'ekit-sticky--',
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'ekit_sticky_until',
			[
				'label' => esc_html__( 'Sticky Until', 'elementskit' ),
				'description' => esc_html__( 'Section id without starting hash, example "section1".', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'condition' => [
					'ekit_sticky!' => ['', 'column'],
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'ekit_sticky_offset',
			[
				'label' => esc_html__( 'Sticky Offset', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'required' => true,
				'condition' => [
					'ekit_sticky!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'ekit_sticky_color',
			[
				'label' => esc_html__( 'Sticky Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'ekit_sticky!' => ['', 'column'],
				],
				'selectors' => [
					'{{WRAPPER}}.ekit-sticky--effects' => 'background-color: {{VALUE}}',
				],
			]
		);

		$element->add_control(
			'ekit_sticky_on',
			[
				'label' => esc_html__( 'Sticky On', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'desktop_tablet_mobile' => esc_html__( 'All Devices', 'elementskit' ),
					'desktop' => esc_html__( 'Desktop Only', 'elementskit' ),
					'desktop_tablet' => esc_html__( 'Desktop & Tablet', 'elementskit' ),
				],
				'default' => 'desktop_tablet_mobile',
				'render_type' => 'none',
				'frontend_available' => true,
				'condition' => [
					'ekit_sticky!' => '',
				],
			]
		);

		$element->add_control(
			'ekit_sticky_effect_offset',
			[
				'label' => esc_html__( 'Add "ekit-sticky--effects" Class Offset', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'required' => true,
				'condition' => [
					'ekit_sticky!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->end_controls_section();
	}
}
