<?php

namespace Elementor;

use Elementor\Group_Control_Border;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Jet_Smart_Filters_Range_Widget extends Jet_Smart_Filters_Base_Widget {

	public function get_name() {

		return 'jet-smart-filters-range';
	}

	public function get_title() {

		return __( 'Range Filter', 'jet-smart-filters' );
	}

	public function get_icon() {

		return 'jet-smart-filters-icon-range-filter';
	}

	public function get_help_url() {

		return jet_smart_filters()->widgets->prepare_help_url(
			'https://crocoblock.com/knowledge-base/articles/jetsmartfilters-how-to-create-a-price-range-filter-for-woocommerce-products/',
			$this->get_name()
		);
	}

	public function register_filter_style_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/range/css-scheme',
			array(
				'slider-track'     => '.jet-range__slider__track',
				'slider-range'     => '.jet-range__slider__track__range',
				'slider-input'     => '.jet-range__slider__input',
				'range-values'     => '.jet-range__values',
				'inputs'           => '.jet-range__inputs',
				'inputs-container' => '.jet-range__inputs__container',
				'input-group'      => '.jet-range__inputs__group',
				'input'            => '.jet-range__inputs__group input',
				'input-text'       => '.jet-range__inputs__group__text'
			)
		);

		$this->start_controls_section(
			'section_slider_style',
			array(
				'label'      => esc_html__( 'Slider', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'slider_stroke',
			array(
				'label'      => esc_html__( 'Stroke', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px'
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'default' => array(
					'size' => 4,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['slider-track'] => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'slider_style_tabs' );

		$this->start_controls_tab(
			'slider_default_styles',
			array(
				'label' => esc_html__( 'Default', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'slider_background_color',
			array(
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['slider-track'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'slider_range_styles',
			array(
				'label' => esc_html__( 'Range', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'slider_range_background_color',
			array(
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['slider-range'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'slider_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['slider-track'],
			)
		);

		$this->add_control(
			'slider_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['slider-track'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				),
			)
		);

		$this->add_control(
			'range_points_heading',
			array(
				'label'     => esc_html__( 'Range Points', 'jet-smart-filters' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'range_points_width',
			array(
				'label'      => esc_html__( 'Points Width', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px'
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default' => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-webkit-slider-thumb' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-moz-range-thumb'     => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-ms-thumb'            => 'width: {{SIZE}}{{UNIT}};'
				),
			)
		);

		$this->add_responsive_control(
			'range_points_height',
			array(
				'label'      => esc_html__( 'Points Height', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px'
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default' => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['slider-input']                            => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-webkit-slider-thumb' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-moz-range-thumb'     => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-ms-thumb'            => 'height: {{SIZE}}{{UNIT}};'
				),
			)
		);

		$this->add_control(
			'range_points_background_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-webkit-slider-thumb' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-moz-range-thumb'     => 'background-color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-ms-thumb'            => 'background-color: {{VALUE}}'
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'range_points_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-webkit-slider-thumb',
			)
		);

		$this->add_control(
			'range_points_border_pseudo_classes',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default'   => 'style',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-moz-range-thumb' => 'border-style: {{range_points_border_border.VALUE}}; border-width: {{range_points_border_width.TOP}}{{range_points_border_width.UNIT}} {{range_points_border_width.RIGHT}}{{range_points_border_width.UNIT}} {{range_points_border_width.BOTTOM}}{{range_points_border_width.UNIT}} {{range_points_border_width.LEFT}}{{range_points_border_width.UNIT}}; border-color: {{range_points_border_color.VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-ms-thumb'        => 'border-style: {{range_points_border_border.VALUE}}; border-width: {{range_points_border_width.TOP}}{{range_points_border_width.UNIT}} {{range_points_border_width.RIGHT}}{{range_points_border_width.UNIT}} {{range_points_border_width.BOTTOM}}{{range_points_border_width.UNIT}} {{range_points_border_width.LEFT}}{{range_points_border_width.UNIT}}; border-color: {{range_points_border_color.VALUE}}',
				),
				'condition' => array(
					'range_points_border_border!' => '',
				),
			) 
		);

		$this->add_control(
			'range_points_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-webkit-slider-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-moz-range-thumb'     => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-ms-thumb'            => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'range_points_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-webkit-slider-thumb',
			)
		);

		$this->add_control(
			'range_points_shadow_pseudo_classes',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default'   => 'style',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-moz-range-thumb' => 'box-shadow: {{range_points_shadow_box_shadow.HORIZONTAL}}px {{range_points_shadow_box_shadow.VERTICAL}}px {{range_points_shadow_box_shadow.BLUR}}px {{range_points_shadow_box_shadow.SPREAD}}px {{range_points_shadow_box_shadow.COLOR}} {{range_points_shadow_box_shadow_position.VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['slider-input'] . '::-ms-thumb'        => 'box-shadow: {{range_points_shadow_box_shadow.HORIZONTAL}}px {{range_points_shadow_box_shadow.VERTICAL}}px {{range_points_shadow_box_shadow.BLUR}}px {{range_points_shadow_box_shadow.SPREAD}}px {{range_points_shadow_box_shadow.COLOR}} {{range_points_shadow_box_shadow_position.VALUE}};',
				),
				'condition' => array(
					'range_points_shadow_box_shadow_type' => 'yes',
				),
			) 
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_values_style',
			array(
				'label'      => esc_html__( 'Values', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'values_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['range-values'],
			)
		);

		$this->add_control(
			'values_color',
			array(
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['range-values'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'values_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['range-values'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'values_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'toggle'  => false,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['range-values'] => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_inputs_style',
			array(
				'label'      => esc_html__( 'Inputs', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'inputs_width',
			array(
				'label'      => esc_html__( 'Container Width', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', '%',
				),
				'range'      => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['inputs-container'] => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'inputs_offset',
			array(
				'label'      => esc_html__( 'Inputs Offset', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input-group'] . ':first-child' => 'margin-right: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} ' . $css_scheme['input-group'] . ':last-child'  => 'margin-left: calc({{SIZE}}{{UNIT}}/2);',
					'.rtl {{WRAPPER}} ' . $css_scheme['input-group'] . ':first-child' => 'margin-left: calc({{SIZE}}{{UNIT}}/2); margin-right: 0;',
					'.rtl {{WRAPPER}} ' . $css_scheme['input-group'] . ':last-child'  => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: 0;',
				),
			)
		);

		$this->add_responsive_control(
			'inputs_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['inputs'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'inputs_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'toggle'    => false,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'posts-grid-builder' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'posts-grid-builder' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'posts-grid-builder' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left'   => 'margin-left: 0; margin-right: auto;',
					'center' => 'margin-left: auto; margin-right: auto;',
					'right'  => 'margin-left: auto; margin-right: 0;',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inputs-container'] => '{{VALUE}}',
				)
			)
		);

		$this->add_control(
			'input_heading',
			array(
				'label'     => esc_html__( 'Input', 'jet-smart-filters' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'input_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['input-group']
			)
		);

		$this->add_control(
			'input_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['input-group'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'input_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['input-group'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'input_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['input-group'],
				'separator'   => 'before'
			)
		);

		$this->add_control(
			'input_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input-group'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'input_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['input-group'],
			)
		);

		$this->add_responsive_control(
			'input_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before'
			)
		);

		$this->add_control(
			'input_text_heading',
			array(
				'label'     => esc_html__( 'Prefix/Suffix', 'jet-smart-filters' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'input_text_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['input-text']
			)
		);

		$this->add_control(
			'input_text_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['input-text'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'input_text_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['input-text'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'input_text_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['input-text'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before'
			)
		);

		$this->end_controls_section();
	}

	public function base_controls_section_filter_group( $css_scheme ) {

		$this->start_controls_section(
			'section_group_filters_style',
			array(
				'label'      => esc_html__( 'Grouped Filters', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'group_filters_content_position',
			array(
				'label'   => esc_html__( 'Position', 'jet-smart-filters' ),
				'type'    => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'line' => array(
						'title' => esc_html__( 'Line', 'jet-smart-filters' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => esc_html__( 'Columns', 'jet-smart-filters' ),
						'icon'  => 'eicon-menu-bar',
					),
				),
				'selectors_dictionary' => array(
					'line'      => 'display:flex; flex-direction:row; justify-content: space-between;',
					'column'    => 'display:flex; flex-direction:column;',
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-filters-group' => '{{VALUE}}',
				),
				'prefix_class' => 'jet-smart-filter-group-position-',
			)
		);

		$this->add_responsive_control(
			'group_filters_width',
			array(
				'label'      => esc_html__( 'Width', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'%',
					'px',
				),
				'range'      => array(
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'px' => array(
						'min' => 50,
						'max' => 400,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-filters-group ' . $css_scheme['filter'] => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'group_filters_vertical_offset',
			array(
				'label'      => esc_html__( 'Vertical Space Between', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-filters-group ' . $css_scheme['filter'] . '+' . $css_scheme['filter'] => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jet-select[data-hierarchical="1"] + .jet-select[data-hierarchical="1"]' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}
}
