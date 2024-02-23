<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Elementor_Views;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Integration {

	public function __construct() {
		add_action( 'jet-engine/listings/dynamic-link/source-controls', [ $this, 'register_dynamic_link_controls' ] );
		add_action( 'elementor/element/jet-listing-dynamic-link/section_link_style/after_section_end', [ $this, 'register_dynamic_link_style_controls' ] );
	}

	/**
	 * Register links controls.
	 *
	 * Register add to cart source custom controls.
	 *
	 * @since  3.0.8
	 * @access public
	 *
	 * @param object $widget Dynamic link widget instance.
	 *
	 * @return void
	 */
	public function register_dynamic_link_controls( $widget ) {

		$widget->add_control(
			'dynamic_link_enable_quantity_input',
			[
				'label'       => __( 'Enable quantity input', 'jet-engine' ),
				'type'        => 'switcher',
				'description' => __( 'Display quantity input fields for simple products next to add to cart buttons.', 'jet-engine' ),
				'condition'   => [
					'dynamic_link_source' => 'add_to_cart',
				],
			]
		);

		$widget->add_control(
			'dynamic_link_add_to_cart_quantity',
			[
				'label'     => __( 'Quantity', 'elementor-pro' ),
				'type'      => 'number',
				'default'   => 1,
				'condition' => [
					'dynamic_link_source' => 'add_to_cart',
				],
			]
		);

	}

	/**
	 * Register dynamic links style controls.
	 *
	 * Register add to cart source quantity input styles.
	 *
	 * @since  3.0.8
	 * @access public
	 *
	 * @param object $widget Dynamic link widget instance.
	 *
	 * @return void
	 */
	public function register_dynamic_link_style_controls( $widget ) {

		$widget->start_controls_section(
			'dynamic_link_quantity_input_styles',
			[
				'label'     => __( 'Quantity Input', 'jet-engine' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'dynamic_link_enable_quantity_input!' => '',
				],
			]
		);

		$widget->add_responsive_control(
			'dynamic_link_quantity_input_display_type',
			[
				'label'     => __( 'Display Type', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'row',
				'options'   => [
					'column' => __( 'Block', 'jet-engine' ),
					'row'    => __( 'Inline', 'jet-engine' ),
				],
				'selectors' => [
					$widget->css_selector( ' .cart' ) => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$widget->add_responsive_control(
			'dynamic_link_quantity_input_width',
			[
				'label'      => __( 'Width', 'jet-engine' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 70,
				],
				'range'      => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 50,
						'max' => 1000,
					],
				],
				'selectors'  => [
					$widget->css_selector( ' .quantity' ) => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'dynamic_link_quantity_input_typography',
				'selector' => $widget->css_selector( ' .quantity .qty' ),
			]
		);

		$widget->start_controls_tabs( 'dynamic_link_quantity_input_tabs' );

		$widget->start_controls_tab(
			'dynamic_link_quantity_input_tab_normal',
			[
				'label' => __( 'Normal', 'jet-engine' ),
			]
		);

		$widget->add_control(
			'dynamic_link_quantity_input_color',
			[
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					$widget->css_selector( ' .quantity .qty' ) => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'dynamic_link_quantity_input_background',
				'label'    => __( 'Background', 'jet-engine' ),
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => $widget->css_selector( ' .quantity .qty' ),
			]
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'dynamic_link_quantity_input_tab_focus',
			[
				'label' => __( 'Focus', 'jet-engine' ),
			]
		);

		$widget->add_control(
			'dynamic_link_quantity_input_color_focus',
			[
				'label'     => __( 'Color', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					$widget->css_selector( ' .quantity .qty:focus' ) => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'dynamic_link_quantity_input_background_focus',
				'label'    => __( 'Background', 'jet-engine' ),
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => $widget->css_selector( ' .quantity .qty:focus' ),
			]
		);

		$widget->add_control(
			'dynamic_link_quantity_input_border_color_focus',
			[
				'label'     => __( 'Border Color', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					$widget->css_selector( ' .quantity .qty:focus' ) => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'dynamic_link_quantity_input_border_border!' => '',
				],
			]
		);

		$widget->end_controls_tab();

		$widget->end_controls_tabs();

		$widget->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'dynamic_link_quantity_input_border',
				'selector'  => $widget->css_selector( ' .quantity .qty' ),
				'separator' => 'before',
			]
		);

		$widget->add_control(
			'dynamic_link_quantity_input_border_radius',
			[
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$widget->css_selector( ' .quantity .qty' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dynamic_link_quantity_input_box_shadow',
				'selector' => $widget->css_selector( ' .quantity .qty' ),
			]
		);

		$widget->add_responsive_control(
			'dynamic_link_quantity_input_margin',
			[
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$widget->css_selector( ' .quantity' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$widget->add_responsive_control(
			'dynamic_link_quantity_input_padding',
			[
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$widget->css_selector( ' .quantity .qty' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$widget->end_controls_section();

	}

}