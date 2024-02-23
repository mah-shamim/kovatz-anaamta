<?php
/**
 * Air Datepicker style controls
 */

namespace Elementor;

$this->start_controls_section(
	'section_calendar_styles',
	array(
		'label'      => __( 'Calendar', 'jet-smart-filters' ),
		'tab'        => Controls_Manager::TAB_STYLE,
		'show_label' => false,
	)
);

$this->add_responsive_control(
	'calendar_offset_top',
	array(
		'label'      => esc_html__( 'Vertical Offset', 'jet-smart-filters' ),
		'type'       => Controls_Manager::SLIDER,
		'range'      => array(
			'px' => array(
				'min' => -300,
				'max' => 300,
			),
		),
		'default'    => array(
			'size' => 10
		),
		'selectors'  => array(
			'#datepickers-container .jet-date-period-{{ID}}' => 'margin-top: {{SIZE}}{{UNIT}}',
		),
	)
);

$this->add_responsive_control(
	'calendar_offset_left',
	array(
		'label'      => esc_html__( 'Horizontal Offset', 'jet-smart-filters' ),
		'type'       => Controls_Manager::SLIDER,
		'range'      => array(
			'px' => array(
				'min' => -300,
				'max' => 300,
			),
		),
		'default'    => array(
			'size' => 0
		),
		'selectors'  => array(
			'#datepickers-container .jet-date-period-{{ID}}' => 'margin-left: {{SIZE}}{{UNIT}}',
		),
	)
);

$this->add_responsive_control(
	'calendar_width',
	array(
		'label'      => esc_html__( 'Calendar Width', 'jet-smart-filters' ),
		'type'       => Controls_Manager::SLIDER,
		'range'      => array(
			'px' => array(
				'min' => 100,
				'max' => 1000,
			),
		),
		'default'    => array(
			'size' => 300,
			'unit' => 'px',
		),
		'selectors'  => array(
			'#datepickers-container .jet-date-period-{{ID}}' => 'width: {{SIZE}}{{UNIT}};',
		),
	)
);

$this->add_control(
	'calendar_body_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}}' => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'        => 'calendar_body_border',
		'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
		'placeholder' => '1px',
		'default'     => '1px',
		'selector'    => '#datepickers-container .jet-date-period-{{ID}}',
	)
);

$this->add_control(
	'calendar_body_border_radius',
	array(
		'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'#datepickers-container .jet-date-period-{{ID}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_group_control(
	Group_Control_Box_Shadow::get_type(),
	array(
		'name'     => 'calendar_body_box_shadow',
		'selector' => '#datepickers-container .jet-date-period-{{ID}}',
	)
);

$this->add_responsive_control(
	'calendar_body_padding',
	array(
		'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'#datepickers-container .jet-date-period-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->end_controls_section();

$this->start_controls_section(
	'section_calendar_header_styles',
	array(
		'label'      => __( 'Calendar Header', 'jet-smart-filters' ),
		'tab'        => Controls_Manager::TAB_STYLE,
		'show_label' => false,
	)
);

$this->add_control(
	'calendar_header_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav' => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'        => 'calendar_header_border',
		'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
		'placeholder' => '1px',
		'default'     => '1px',
		'selector'    => '#datepickers-container .jet-date-period-{{ID}} .datepicker--nav',
	)
);

$this->add_control(
	'calendar_header_border_radius',
	array(
		'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_responsive_control(
	'calendar_header_padding',
	array(
		'label'     => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'      => Controls_Manager::DIMENSIONS,
		'separator' => 'before',
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_responsive_control(
	'calendar_header_margin',
	array(
		'label'     => esc_html__( 'Margin', 'jet-smart-filters' ),
		'type'      => Controls_Manager::DIMENSIONS,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_control(
	'calendar_header_caption_heading',
	array(
		'label'     => esc_html__( 'Caption', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'calendar_header_caption_typography',
		'selector' => '#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-title',
	)
);

$this->start_controls_tabs( 'calendar_header_caption_style_tabs' );

$this->start_controls_tab(
	'calendar_header_caption_normal_styles',
	array(
		'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_header_caption_normal_color',
	array(
		'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-title' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_header_caption_normal_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-title' => 'background-color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();

$this->start_controls_tab(
	'calendar_header_caption_hover_styles',
	array(
		'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_header_caption_hover_color',
	array(
		'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-title' . ':hover' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_header_caption_hover_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-title' . ':hover' => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_header_caption_hover_border_color',
	array(
		'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-title' . ':hover' => 'border-color: {{VALUE}}',
		),
		'condition' => array(
			'calendar_header_caption_border_border!' => '',
		)
	)
);

$this->end_controls_tab();

$this->end_controls_tabs();

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'        => 'calendar_header_caption_border',
		'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
		'selector'    => '#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-title',
		'separator'   => 'before'
	)
);

$this->add_control(
	'calendar_header_caption_border_radius',
	array(
		'label'     => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'      => Controls_Manager::DIMENSIONS,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_responsive_control(
	'calendar_header_caption_padding',
	array(
		'label'     => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'      => Controls_Manager::DIMENSIONS,
		'separator' => 'before',
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_control(
	'calendar_header_prev_next_heading',
	array(
		'label'     => esc_html__( 'Navigation Arrows', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_responsive_control(
	'calendar_header_prev_next_size',
	array(
		'label'      => esc_html__( 'Size', 'jet-smart-filters' ),
		'type'       => Controls_Manager::SLIDER,
		'range'      => array(
			'px' => array(
				'min' => 10,
				'max' => 50,
			),
		),
		'selectors'  => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-action svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
		),
	)
);

$this->start_controls_tabs( 'calendar_header_prev_next_style_tabs' );

$this->start_controls_tab(
	'calendar_header_prev_next_normal_styles',
	array(
		'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_header_prev_next_normal_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-action' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_header_prev_next_normal_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-action' => 'background-color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();

$this->start_controls_tab(
	'calendar_header_prev_next_hover_styles',
	array(
		'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_header_prev_next_hover_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-action' . ':hover' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_header_prev_next_hover_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-action' . ':hover' => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_header_prev_next_hover_border_color',
	array(
		'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-action' . ':hover' => 'border-color: {{VALUE}}',
		),
		'condition' => array(
			'calendar_header_prev_next_border_border!' => '',
		)
	)
);

$this->end_controls_tab();

$this->end_controls_tabs();

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'        => 'calendar_header_prev_next_border',
		'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
		'selector'    => '#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-action',
		'separator'   => 'before'
	)
);

$this->add_control(
	'calendar_header_prev_next_border_radius',
	array(
		'label'     => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'      => Controls_Manager::DIMENSIONS,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-action' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_responsive_control(
	'calendar_header_prev_next_padding',
	array(
		'label'     => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'      => Controls_Manager::DIMENSIONS,
		'separator' => 'before',
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--nav-action' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->end_controls_section();

$this->start_controls_section(
	'section_calendar_cell',
	array(
		'label'      => __( 'Calendar Cell', 'jet-smart-filters' ),
		'tab'        => Controls_Manager::TAB_STYLE,
		'show_label' => false,
	)
);

$this->start_controls_tabs( 'calendar_cell_style_tabs' );

$this->start_controls_tab(
	'calendar_cell_default_styles',
	array(
		'label' => esc_html__( 'Default', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_cell_default_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell' => 'color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();

$this->start_controls_tab(
	'calendar_cell_hover_styles',
	array(
		'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_cell_hover_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-focus-' => 'color: {{VALUE}}',
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-in-range-.-focus-' => 'color: {{VALUE}}',
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-week-hover-' => 'color: {{VALUE}}',
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-range-from-' => 'color: {{VALUE}}',
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-range-to-' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_cell_hover_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-focus-' => 'background-color: {{VALUE}}',
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-in-range-.-focus-' => 'background-color: {{VALUE}}',
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-week-hover-' => 'background-color: {{VALUE}}',
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-range-from-' => 'background-color: {{VALUE}}',
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-range-to-' => 'background-color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();

$this->start_controls_tab(
	'calendar_cell_active_styles',
	array(
		'label' => esc_html__( 'Active', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_cell_active_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-selected-' => 'color: {{VALUE}}',
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-week-selected-' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_cell_active_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-selected-' => 'background-color: {{VALUE}}',
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-week-selected-' => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_cell_active_in_range_color',
	array(
		'label'     => esc_html__( 'In Range Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-in-range-' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_cell_active_in_range_background_color',
	array(
		'label'     => esc_html__( 'In Range Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-in-range-' => 'background-color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();

$this->start_controls_tab(
	'calendar_cell_current_styles',
	array(
		'label' => esc_html__( 'Current', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_cell_current_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-current-' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_cell_current_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell.-current-' => 'background-color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();

$this->end_controls_tabs();

$this->add_control(
	'calendar_cell_border_radius',
	array(
		'label'     => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'      => Controls_Manager::DIMENSIONS,
		'separator' => 'before',
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
		),
	)
);

$this->add_control(
	'calendar_days_heading',
	array(
		'label'     => esc_html__( 'Days', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'calendar_days_typography',
		'selector' => '#datepickers-container .jet-date-period-{{ID}} .datepicker--cell-day',
	)
);

$this->add_control(
	'calendar_days_weekend_color',
	array(
		'label'     => esc_html__( 'Weekend Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'separator' => 'before',
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell-day.-weekend-' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_days_other_month_color',
	array(
		'label'     => esc_html__( 'Other Month Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell-day.-other-month-' => 'color: {{VALUE}}',
		),
	)
);

$this->add_responsive_control(
	'calendar_days_cells_padding',
	array(
		'label'              => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'               => Controls_Manager::DIMENSIONS,
		'allowed_dimensions' => array( 'top', 'bottom' ),
		'separator'          => 'before',
		'selectors'          => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell-day' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
		),
	)
);

$this->add_control(
	'calendar_month_heading',
	array(
		'label'     => esc_html__( 'Month', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'calendar_months_typography',
		'selector' => '#datepickers-container .jet-date-period-{{ID}} .datepicker--cell-month',
	)
);


$this->add_responsive_control(
	'calendar_months_cells_padding',
	array(
		'label'              => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'               => Controls_Manager::DIMENSIONS,
		'allowed_dimensions' => array( 'top', 'bottom' ),
		'separator'          => 'before',
		'selectors'          => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell-month' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
		),
	)
);

$this->add_control(
	'calendar_year_heading',
	array(
		'label'     => esc_html__( 'Year', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'calendar_years_typography',
		'selector' => '#datepickers-container .jet-date-period-{{ID}} .datepicker--cell-year',
	)
);


$this->add_responsive_control(
	'calendar_years_cells_padding',
	array(
		'label'              => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'               => Controls_Manager::DIMENSIONS,
		'allowed_dimensions' => array( 'top', 'bottom' ),
		'separator'          => 'before',
		'selectors'          => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--cell-year' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
		),
	)
);

$this->end_controls_section();

$this->start_controls_section(
	'section_calendar_week_days',
	array(
		'label'      => __( 'Calendar Week Days', 'jet-smart-filters' ),
		'tab'        => Controls_Manager::TAB_STYLE,
		'show_label' => false,
	)
);

$this->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'calendar_week_days_typography',
		'selector' => '#datepickers-container .jet-date-period-{{ID}} .datepicker--day-name',
	)
);

$this->add_control(
	'calendar_week_days_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--day-name' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_week_days_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--days-names' => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'      => 'calendar_week_days_border',
		'label'     => esc_html__( 'Header Border', 'jet-smart-filters' ),
		'separator' => 'before',
		'selector'  => '#datepickers-container .jet-date-period-{{ID}} .datepicker--days-names',
	)
);

$this->add_control(
	'calendar_week_days_border_radius',
	array(
		'label'     => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'      => Controls_Manager::DIMENSIONS,
		'selectors' => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--days-names' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_responsive_control(
	'calendar_week_days_cells_padding',
	array(
		'label'              => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'               => Controls_Manager::DIMENSIONS,
		'allowed_dimensions' => array( 'top', 'bottom' ),
		'separator'          => 'before',
		'selectors'          => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--days-names' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
		),
	)
);

$this->add_responsive_control(
	'calendar_week_days_margin',
	array(
		'label'              => esc_html__( 'Margin', 'jet-smart-filters' ),
		'type'               => Controls_Manager::DIMENSIONS,
		'allowed_dimensions' => array('top', 'bottom'),
		'selectors'          => array(
			'#datepickers-container .jet-date-period-{{ID}} .datepicker--days-names' => 'margin: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
		),
	)
);

$this->end_controls_section();
