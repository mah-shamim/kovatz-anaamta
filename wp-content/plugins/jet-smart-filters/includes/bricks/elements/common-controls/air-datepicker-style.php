<?php

$this->start_jet_control_group( 'section_calendar_styles' );

$this->register_jet_control(
	'calendar_offset_top',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Vertical Offset', 'jet-smart-filters' ),
		'type'     => 'number',
		'units'    => true,
		'default' => '10px',
		'css'     => [
			[
				'property' => 'margin-top',
				'selector' => ".selector-$this->id",
				'id'       => '#datepickers-container',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_offset_left',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Horizontal Offset', 'jet-smart-filters' ),
		'type'     => 'number',
		'units'    => true,
		'default' => '0px',
		'css'     => [
			[
				'property' => 'margin-left',
				'selector' => ".selector-$this->id",
				'id'       => '#datepickers-container',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_width',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Calendar width', 'jet-smart-filters' ),
		'type'     => 'number',
		'units'    => true,
		'default' => '300px',
		'css'     => [
			[
				'property' => 'width',
				'selector' => ".jet-date-period-$this->id",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_body_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_body_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => ".jet-date-period-$this->id",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_body_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => ".jet-date-period-$this->id",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_body_box_shadow',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
		'type'  => 'box-shadow',
		'css'   => [
			[
				'property' => 'box-shadow',
				'selector' => ".jet-date-period-$this->id",
			],
		],
	]
);

$this->end_jet_control_group();

$this->start_jet_control_group( 'section_calendar_header_styles' );

$this->register_jet_control(
	'calendar_header_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--nav",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_margin',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Margin', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'margin',
				'selector' => ".jet-date-period-$this->id .datepicker--nav",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => ".jet-date-period-$this->id .datepicker--nav",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => ".jet-date-period-$this->id .datepicker--nav",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_caption_heading',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Caption', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_header_caption_typography',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
		'type'  => 'typography',
		'css'   => [
			[
				'property' => 'typography',
				'selector' => ".jet-date-period-$this->id .datepicker--nav-title",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_caption_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--nav-title",
			],
		],
	]
);


$this->register_jet_control(
	'calendar_header_caption_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => ".jet-date-period-$this->id .datepicker--nav-title",
			],
		],
	]
);
$this->register_jet_control(
	'calendar_header_caption_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => ".jet-date-period-$this->id .datepicker--nav-title",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_prev_next_heading',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Navigation arrows', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_header_prev_next_size',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Size', 'jet-smart-filters' ),
		'type'     => 'number',
		'units'    => true,
		'css'   => [
			[
				'property' => 'width',
				'selector' => ".jet-date-period-$this->id .datepicker--nav-action svg",
			],
			[
				'property' => 'height',
				'selector' => ".jet-date-period-$this->id .datepicker--nav-action svg",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_prev_next_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--nav-action",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_prev_next_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--nav-action",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_prev_next_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => ".jet-date-period-$this->id .datepicker--nav-action",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_prev_next_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => ".jet-date-period-$this->id .datepicker--nav-action",
			],
		],
	]
);

$this->end_jet_control_group();

$this->start_jet_control_group( 'section_calendar_week_days' );

$this->register_jet_control(
	'calendar_week_days_typography',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
		'type'  => 'typography',
		'css'   => [
			[
				'property' => 'typography',
				'selector' => ".jet-date-period-$this->id .datepicker--day-name",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_week_days_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--day-name",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_week_days_margin',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Margin', 'jet-smart-filters' ),
		'type'    => 'dimensions',
		'css'     => [
			[
				'property' => 'margin',
				'selector' => ".jet-date-period-$this->id .datepicker--day-name",
			],
		],
		'default' => [
			'right' => '-',
			'left'  => '-',
		],
	]
);

$this->register_jet_control(
	'calendar_week_days_padding',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'    => 'dimensions',
		'css'     => [
			[
				'property' => 'padding',
				'selector' => ".jet-date-period-$this->id .datepicker--day-name",
			],
		],
		'default' => [
			'right' => '-',
			'left'  => '-',
		],
	]
);

$this->register_jet_control(
	'calendar_week_days_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => ".jet-date-period-$this->id .datepicker--day-name",
			],
		],
	]
);

$this->end_jet_control_group();

$this->start_jet_control_group( 'section_calendar_days' );

$this->register_jet_control(
	'calendar_days_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border radius', 'jet-smart-filters' ),
		'type'  => 'number',
		'units' => true,
		'css'   => [
			[
				'property' => 'border-radius',
				'selector' => ".jet-date-period-$this->id .datepicker--cell",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_hover_styles',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Hover state', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_days_hover_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-focus-",
			],
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-in-range-.-focus-",
			],
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-week-hover-",
			],
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-range-from-",
			],
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-range-to-",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_hover_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-focus-",
			],
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-in-range-.-focus-",
			],
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-week-hover-",
			],
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-range-from-",
			],
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-range-to-",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_active_styles',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Active state', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_days_active_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-selected-",
			],
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-week-selected-",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_active_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-selected-",
			],
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-week-selected-",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_active_in_range_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'In range color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-in-range-",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_active_in_range_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'In range background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-in-range-",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_current_styles',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Current state', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_days_current_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-current-",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_current_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell.-current-",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_heading',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Days', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_days_typography',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Typography', 'jet-smart-filters' ),
		'type'    => 'typography',
		'exclude' => [
			'color'
		],
		'css'     => [
			[
				'property' => 'typography',
				'selector' => ".jet-date-period-$this->id .datepicker--cell-day",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_weekend_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Weekend color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell-day.-weekend-",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_other_month_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Other month color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => ".jet-date-period-$this->id .datepicker--cell-day.-other-month-",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_days_padding',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'    => 'dimensions',
		'css'     => [
			[
				'property' => 'padding',
				'selector' => ".jet-date-period-$this->id .datepicker--cell-day",
			],
		],
		'default' => [
			'right' => '-',
			'left'  => '-',
		],
	]
);

$this->register_jet_control(
	'calendar_month_heading',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Month', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_month_typography',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Typography', 'jet-smart-filters' ),
		'type'    => 'typography',
		'exclude' => [
			'color'
		],
		'css'     => [
			[
				'property' => 'typography',
				'selector' => ".jet-date-period-$this->id .datepicker--cell-month",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_month_padding',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'    => 'dimensions',
		'css'     => [
			[
				'property' => 'padding',
				'selector' => ".jet-date-period-$this->id .datepicker--cell-month",
			],
		],
		'default' => [
			'right' => '-',
			'left'  => '-',
		],
	]
);

$this->register_jet_control(
	'calendar_year_heading',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Year', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_year_typography',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Typography', 'jet-smart-filters' ),
		'type'    => 'typography',
		'exclude' => [
			'color'
		],
		'css'     => [
			[
				'property' => 'typography',
				'selector' => ".jet-date-period-$this->id .datepicker--cell-year",
			],
		],
	]
);

$this->register_jet_control(
	'calendar_year_padding',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'    => 'dimensions',
		'css'     => [
			[
				'property' => 'padding',
				'selector' => ".jet-date-period-$this->id .datepicker--cell-year",
			],
		],
		'default' => [
			'right' => '-',
			'left'  => '-',
		],
	]
);

$this->end_jet_control_group();