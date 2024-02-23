<?php

namespace Elementor;

use Elementor\Group_Control_Border;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Jet_Smart_Filters_Active_Filters_Widget extends Widget_Base {

	public function get_name() {

		return 'jet-smart-filters-active';
	}

	public function get_title() {

		return __( 'Active Filters', 'jet-smart-filters' );
	}

	public function get_icon() {

		return 'jet-smart-filters-icon-active-filters';
	}

	public function get_html_wrapper_class() {

		return 'elementor-widget-' . $this->get_name() . ' hide-widget';
	}

	public function get_help_url() {

		return jet_smart_filters()->widgets->prepare_help_url(
			'https://crocoblock.com/knowledge-base/articles/jetsmartfilters-how-to-enable-visitors-to-disable-active-filters/',
			$this->get_name()
		);
	}

	public function get_categories() {

		return array( jet_smart_filters()->widgets->get_category() );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/active-filters/css-scheme',
			array(
				'filters'       => '.jet-smart-filters-active',
				'filters-list'  => '.jet-active-filters__list',
				'filters-title' => '.jet-active-filters__title',
				'filter'        => '.jet-active-filter',
				'filter-label'  => '.jet-active-filter__label',
				'filter-value'  => '.jet-active-filter__val',
				'filter-remove' => '.jet-active-filter__remove',
			)
		);

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'content_provider',
			array(
				'label'   => __( 'Show active filters for:', 'jet-smart-filters' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => jet_smart_filters()->data->content_providers(),
			)
		);

		$this->add_control(
			'apply_type',
			array(
				'label'   => __( 'Apply type', 'jet-smart-filters' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ajax',
				'options' => array(
					'ajax'   => __( 'AJAX', 'jet-smart-filters' ),
					'reload' => __( 'Page reload', 'jet-smart-filters' ),
				),
			)
		);

		$this->add_control(
			'filters_label',
			array(
				'label'   => esc_html__( 'Label', 'jet-smart-filters' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Active filters:', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'query_id',
			array(
				'label'       => esc_html__( 'Query ID', 'jet-smart-filters' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'jet-smart-filters' ),
			)
		);

		// Include Additional Providers Settings
		include jet_smart_filters()->plugin_path( 'includes/widgets/common-controls/additional-providers.php' );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filters_title_style',
			array(
				'label'      => esc_html__( 'Title', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filters_title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['filters-title'] ,
			)
		);

		$this->add_control(
			'filters_title_color',
			array(
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filters-title'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'filters_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filters-title'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filters_styles',
			array(
				'label'      => __( 'Filters', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'filters_position',
			array(
				'label'       => esc_html__( 'Filters Position', 'jet-smart-filters' ),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'label_block' => false,
				'default'     => 'row',
				'options'     => array(
					'row'    => array(
						'title' => esc_html__( 'Line', 'jet-smart-filters' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => esc_html__( 'Column', 'jet-smart-filters' ),
						'icon'  => 'eicon-menu-bar',
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['filters'] => 'flex-direction: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['filters-list'] => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'filters_space_between_horizontal',
			array(
				'label'      => esc_html__( 'Horizontal Offset', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'filters_position' => 'row'
				)
			)
		);

		$this->add_responsive_control(
			'filters_space_between_vertical',
			array(
				'label'      => esc_html__( 'Vertical Offset', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'filters_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filters-list'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'filters_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['filters-list'],
			)
		);

		$this->add_control(
			'filters_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filters-list'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'filters_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['filters-list'],
			)
		);

		$this->add_responsive_control(
			'filters_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filters-list'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filters_alignment_column',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filters-list'] => 'align-items: {{VALUE}};',
				),
				'condition' => array(
					'filters_position' => 'column'
				)
			)
		);

		$this->add_responsive_control(
			'filters_alignment_line',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filters-list'] => 'justify-content: {{VALUE}};',
				),
				'condition' => array(
					'filters_position' => 'row'
				)
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filters_item',
			array(
				'label'      => __( 'Items', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'filter_min_width',
			array(
				'label'      => esc_html__( 'Minimal Width', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', '%',
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 500,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_position',
			array(
				'label'       => esc_html__( 'Filter Content Position', 'jet-smart-filters' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'row'    => array(
						'title' => esc_html__( 'Line', 'jet-smart-filters' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => esc_html__( 'Column', 'jet-smart-filters' ),
						'icon'  => 'eicon-menu-bar',
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_item_content_space_between_h',
			array(
				'label'      => esc_html__( 'Space Between Content', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] . ' .jet-active-filter__label + .jet-active-filter__val' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'filter_position' => 'row'
				)
			)
		);

		$this->add_responsive_control(
			'filter_item_content_space_between_v',
			array(
				'label'      => esc_html__( 'Space Between Content', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] . ' .jet-active-filter__label + .jet-active-filter__val' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'filter_position' => 'column'
				)
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filters_item_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['filter'],
			)
		);

		$this->start_controls_tabs( 'filters_item_style_tabs' );

		$this->start_controls_tab(
			'filters_item_normal_styles',
			array(
				'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'filters_item_normal_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filters_item_normal_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filters_item_hover_styles',
			array(
				'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'filters_item_value_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] . ':hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filters_item_hover_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] . ':hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filters_item_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] . ':hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'filters_item_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['filter'],
			)
		);

		$this->add_control(
			'filters_item_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'filters_item_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['filter'],
			)
		);

		$this->add_responsive_control(
			'filters_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'filters_item_label_heading',
			array(
				'label'     => esc_html__( 'Label', 'jet-smart-filters' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filters_item_label_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['filter-label'],
			)
		);

		$this->start_controls_tabs( 'filters_item_label_style_tabs' );

		$this->start_controls_tab(
			'filters_item_label_normal_styles',
			array(
				'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'filters_item_label_normal_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter-label'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filters_item_label_hover_styles',
			array(
				'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'filters_item_label_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] . ':hover ' . $css_scheme['filter-label'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'filters_item_label_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-label'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'filters_item_remove_heading',
			array(
				'label'     => esc_html__( 'Remove', 'jet-smart-filters' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'filters_item_remove_size',
			array(
				'label'      => esc_html__( 'Size', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 12,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-remove'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filters_item_remove_offset_top',
			array(
				'label'      => esc_html__( 'Offset Top', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-remove'] => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filters_item_remove_offset_right',
			array(
				'label'      => esc_html__( 'Offset Right', 'jet-smart-filters' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-remove'] => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'filters_item_remove_style_tabs' );

		$this->start_controls_tab(
			'filters_item_remove_normal_styles',
			array(
				'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'filters_item_remove_normal_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter-remove'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filters_item_remove_normal_bg_color',
			array(
				'label' => esc_html__( 'Background', 'jet-smart-filters' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter-remove'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filters_item_remove_hover_styles',
			array(
				'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'filters_item_remove_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] . ':hover ' . $css_scheme['filter-remove'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filters_item_remove_hover_bg_color',
			array(
				'label' => esc_html__( 'Background', 'jet-smart-filters' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] . ':hover ' . $css_scheme['filter-remove'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filters_item_remove_hover_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['filter'] . ':hover ' . $css_scheme['filter-remove'] => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'filters_item_remove_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['filter-remove'],
			)
		);

		$this->add_control(
			'filters_item_remove_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-remove'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filters_item_remove_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['filter-remove'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Returns CSS selector for nested element
	 */
	public function css_selector( $el = null ) {

		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	protected function render() {

		jet_smart_filters()->set_filters_used();

		$base_class           = $this->get_name();
		$settings             = $this->get_settings();
		$provider             = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';
		$query_id             = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );

		printf(
			'<div class="%1$s jet-active-filters jet-filter" data-label="%6$s" data-content-provider="%2$s" data-additional-providers="%3$s" data-apply-type="%4$s" data-query-id="%5$s">',
			$base_class,
			$provider,
			$additional_providers,
			$settings['apply_type'],
			$query_id,
			$settings['filters_label']
		);

		if ( Plugin::instance()->editor->is_edit_mode() ) {
			$active_filters_type = jet_smart_filters()->filter_types->get_filter_types( 'active-filters' );
			$active_filters_type->render_filters_sample( $settings );
		}

		echo '</div>';
	}
}
