<?php
/**
 * Datepicker style controls
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
			'.jet-smart-filters-datepicker-{{ID}}' . $css_scheme['calendar-wrapper'] => 'margin-top: {{SIZE}}{{UNIT}};',
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
			'.jet-smart-filters-datepicker-{{ID}}' . $css_scheme['calendar-wrapper'] => 'margin-left: {{SIZE}}{{UNIT}}',
		),
	)
);

$this->add_responsive_control(
	'calendar_width',
	array(
		'label'      => esc_html__( 'Calendar Width', 'jet-smart-filters' ),
		'type'       => Controls_Manager::SLIDER,
		'size_units' => array(
			'px',
		),
		'range'      => array(
			'px' => array(
				'min' => 0,
				'max' => 1000,
			),
		),
		'default'    => array(
			'size' => 300,
			'unit' => 'px',
		),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}}' . $css_scheme['calendar-wrapper'] => 'width: {{SIZE}}{{UNIT}};',
		),
	)
);

$this->add_control(
	'calendar_body_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}}' . $css_scheme['calendar-wrapper'] => 'background-color: {{VALUE}}',
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
		'selector'    => '.jet-smart-filters-datepicker-{{ID}}' . $css_scheme['calendar-wrapper'],
	)
);

$this->add_control(
	'calendar_body_border_radius',
	array(
		'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}}' . $css_scheme['calendar-wrapper'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_group_control(
	Group_Control_Box_Shadow::get_type(),
	array(
		'name'     => 'calendar_body_box_shadow',
		'selector' => '.jet-smart-filters-datepicker-{{ID}}' . $css_scheme['calendar-wrapper'],
	)
);

$this->add_responsive_control(
	'calendar_body_padding',
	array(
		'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}}' . $css_scheme['calendar-wrapper'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->end_controls_section();

$this->start_controls_section(
	'section_calendar_title',
	array(
		'label'      => __( 'Calendar Caption', 'jet-smart-filters' ),
		'tab'        => Controls_Manager::TAB_STYLE,
		'show_label' => false,
	)
);

$this->add_control(
	'calendar_title_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-title'] => 'color: {{VALUE}}',
		),
	)
);

$this->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'calendar_title_typography',
		'selector' => '.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-title'],
	)
);

$this->end_controls_section();

$this->start_controls_section(
	'section_calendar_prev_next',
	array(
		'label'      => __( 'Calendar Navigation Arrows', 'jet-smart-filters' ),
		'tab'        => Controls_Manager::TAB_STYLE,
		'show_label' => false,
	)
);

$this->add_responsive_control(
	'calendar_prev_next_size',
	array(
		'label'      => esc_html__( 'Size', 'jet-smart-filters' ),
		'type'       => Controls_Manager::SLIDER,
		'size_units' => array(
			'px',
		),
		'range'      => array(
			'px' => array(
				'min' => 0,
				'max' => 30,
			),
		),
		'default'    => array(
			'size' => 15,
			'unit' => 'px',
		),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $css_scheme['calendar-prev-button'] . '> span' => 'border-width: calc({{SIZE}}{{UNIT}} / 2) calc({{SIZE}}{{UNIT}} / 2) calc({{SIZE}}{{UNIT}} / 2) 0;',
			'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $css_scheme['calendar-next-button'] . '> span' => 'border-width: calc({{SIZE}}{{UNIT}} / 2) 0 calc({{SIZE}}{{UNIT}} / 2) calc({{SIZE}}{{UNIT}} / 2);',
		),
	)
);

$this->add_control(
	'calendar_prev_next_normal_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $css_scheme['calendar-next-button'] . '> span' => 'border-left-color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $css_scheme['calendar-prev-button'] . '> span' => 'border-right-color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_prev_next_hover_color',
	array(
		'label'     => esc_html__( 'Hover Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $css_scheme['calendar-next-button'] . ':hover > span' => 'border-left-color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $css_scheme['calendar-prev-button'] . ':hover > span' => 'border-right-color: {{VALUE}}',
		),
	)
);

$this->end_controls_section();

$this->start_controls_section(
	'section_calendar_header',
	array(
		'label'      => __( 'Calendar Week Days', 'jet-smart-filters' ),
		'tab'        => Controls_Manager::TAB_STYLE,
		'show_label' => false,
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'        => 'calendar_header_border',
		'label'       => esc_html__( 'Header Border', 'jet-smart-filters' ),
		'placeholder' => '1px',
		'default'     => '1px',
		'selector'    => '.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'],
	)
);

$this->add_control(
	'calendar_header_background_color',
	array(
		'label'     => esc_html__( 'Header Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'] => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_header_cells_heading',
	array(
		'label'     => esc_html__( 'Day', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_responsive_control(
	'calendar_header_cells_padding',
	array(
		'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'] . ' > tr > th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'        => 'calendar_header_cells_border',
		'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
		'placeholder' => '1px',
		'default'     => '1px',
		'selector'    => '.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'] . ' > tr > th',
	)
);

$this->add_control(
	'calendar_header_cells_first_border_width',
	array(
		'label'      => esc_html__( 'First Item Border Width', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'] . ' > tr > th:first-child' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
		'condition'  => array(
			'calendar_header_cells_border_border!' => ''
		)
	)
);

$this->add_control(
	'calendar_header_cells_last_border_width',
	array(
		'label'      => esc_html__( 'Last Item Border Width', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'] . ' > tr > th:last-child' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
		'condition'  => array(
			'calendar_header_cells_border_border!' => ''
		)
	)
);

$this->add_control(
	'calendar_header_cells_content',
	array(
		'label'     => esc_html__( 'Day Content', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'calendar_header_cells_content_typography',
		'selector' => '.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'] . ' > tr > th > span',
	)
);

$this->add_control(
	'calendar_header_cells_content_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'] . ' > tr > th > span' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_header_cells_content_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'] . ' > tr > th > span' => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'        => 'calendar_header_cells_content_border',
		'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
		'placeholder' => '1px',
		'default'     => '1px',
		'selector'    => '.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'] . ' > tr > th > span',
	)
);

$this->add_control(
	'calendar_header_cells_content_border_radius',
	array(
		'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'] . ' > tr > th > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_responsive_control(
	'calendar_header_cells_content_padding',
	array(
		'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-header'] . ' > tr > th > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->end_controls_section();


$this->start_controls_section(
	'section_calendar_content',
	array(
		'label'      => __( 'Calendar Days', 'jet-smart-filters' ),
		'tab'        => Controls_Manager::TAB_STYLE,
		'show_label' => false,
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'        => 'calendar_content_border',
		'label'       => esc_html__( 'Body Border', 'jet-smart-filters' ),
		'placeholder' => '1px',
		'default'     => '1px',
		'selector'    => '.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'],
	)
);

$this->add_control(
	'calendar_content_background_color',
	array(
		'label'     => esc_html__( 'Body Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_content_cells_heading',
	array(
		'label'     => esc_html__( 'Day', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_responsive_control(
	'calendar_content_cells_padding',
	array(
		'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'        => 'calendar_content_cells_border',
		'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
		'placeholder' => '1px',
		'default'     => '1px',
		'selector'    => '.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td',
	)
);

$this->add_control(
	'calendar_content_cells_first_border_width',
	array(
		'label'      => esc_html__( 'First Item Border Width', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td:first-child' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
		'condition'  => array(
			'calendar_content_cells_border_border!' => ''
		)
	)
);

$this->add_control(
	'calendar_content_cells_last_border_width',
	array(
		'label'      => esc_html__( 'Last Item Border Width', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td:last-child' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
		'condition'  => array(
			'calendar_content_cells_border_border!' => ''
		)
	)
);

$this->add_control(
	'calendar_content_cells_content',
	array(
		'label'     => esc_html__( 'Day Content', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'calendar_content_cells_content_typography',
		'selector' => '.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > span,' . '.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a',
	)
);

$this->start_controls_tabs( 'calendar_content_cells_content_style_tabs' );
$this->start_controls_tab(
	'calendar_content_cells_content_default_styles',
	array(
		'label' => esc_html__( 'Default', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_content_cells_content_default_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > span' => 'color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a'    => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_content_cells_content_default_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > span' => 'background-color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a'    => 'background-color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();
$this->start_controls_tab(
	'calendar_content_cells_content_hover_styles',
	array(
		'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_content_cells_content_hover_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a:hover' => 'color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a:hover' => 'color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}}.jet-date-period-week-type .ui-datepicker-calendar tbody > tr:hover > td > a' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_content_cells_content_hover_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a:hover' => 'background-color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a:hover' => 'background-color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}}.jet-date-period-week-type .ui-datepicker-calendar tbody > tr:hover > td > a' => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_content_cells_content_hover_border_color',
	array(
		'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a:hover' => 'border-color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a:hover' => 'border-color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}}.jet-date-period-week-type .ui-datepicker-calendar tbody > tr:hover > td > a' => 'border-color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();
$this->start_controls_tab(
	'calendar_content_cells_content_active_styles',
	array(
		'label' => esc_html__( 'Active', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_content_cells_content_active_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a.ui-state-active' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_content_cells_content_active_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a.ui-state-active' => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_content_cells_content_active_border_color',
	array(
		'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a.ui-state-active' => 'border-color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();
$this->start_controls_tab(
	'calendar_content_cells_content_current_styles',
	array(
		'label' => esc_html__( 'Current', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'calendar_content_cells_content_current_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a'    => 'color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > span' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_content_cells_content_current_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a'    => 'background-color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > span' => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'calendar_content_cells_content_current_border_color',
	array(
		'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a'    => 'border-color: {{VALUE}}',
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > span' => 'border-color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();
$this->end_controls_tabs();

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'        => 'calendar_content_cells_content_border',
		'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
		'placeholder' => '1px',
		'default'     => '1px',
		'selector'    => '.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > span,' . '.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a',
	)
);

$this->add_control(
	'calendar_content_cells_content_border_radius',
	array(
		'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_responsive_control(
	'calendar_content_cells_content_padding',
	array(
		'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			'.jet-smart-filters-datepicker-{{ID}} ' . $css_scheme['calendar-body-content'] . ' > tr > td > a'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->end_controls_section();