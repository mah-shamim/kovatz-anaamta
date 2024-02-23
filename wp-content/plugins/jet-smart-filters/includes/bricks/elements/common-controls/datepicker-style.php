<?php
/**
 * Datepicker style controls
 */

$this->start_jet_control_group( 'section_calendar_styles' );

$this->register_jet_control(
	'calendar_offset_top',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Vertical Offset', 'jet-smart-filters' ),
		'type'    => 'number',
		'default' => '10px',
		'css'     => [
			[
				'property' => 'margin-top',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-wrapper'],
			],
		],
	]
);

$this->register_jet_control(
	'calendar_offset_left',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Horizontal Offset', 'jet-smart-filters' ),
		'type'    => 'number',
		'default' => '10px',
		'css'     => [
			[
				'property' => 'margin-left',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-wrapper'],
			],
		],
	]
);

$this->register_jet_control(
	'calendar_width',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Calendar width', 'jet-smart-filters' ),
		'type'    => 'number',
		'default' => '300px',
		'css'     => [
			[
				'property' => 'width',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-wrapper'],
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
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-wrapper'],
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
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-wrapper'],
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
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-wrapper'],
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
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-wrapper'],
			],
		],
	]
);

$this->end_jet_control_group();

$this->start_jet_control_group( 'section_calendar_title' );

$this->register_jet_control(
	'calendar_title_typography',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Typography', 'jet-smart-filters' ),
		'type'    => 'typography',
		'exclude' => [
			'text-align',
		],
		'css'     => [
			[
				'property' => 'typography',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-wrapper'],
			],
		],
	]
);

$this->end_jet_control_group();

$this->start_jet_control_group( 'section_calendar_prev_next' );

$this->register_jet_control(
	'calendar_prev_next_size',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Size', 'jet-smart-filters' ),
		'type'    => 'number',
		'default' => '15px',
		'css'     => [
			[
				'property' => 'border-width',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-prev-button'],
			],
			[
				'property' => 'border-width',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-next-button'],
			],
		],
	]
);

$this->register_jet_control(
	'calendar_prev_next_normal_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'border-left-color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-prev-button'],
			],
			[
				'property' => 'border-right-color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-next-button'],
			],
		],
	]
);

$this->end_jet_control_group();

$this->start_jet_control_group( 'section_calendar_header' );

$this->register_jet_control(
	'calendar_header_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Header background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-header'],
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
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-header'],
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_cells_heading',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Day', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_header_cells_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-header'] . ' > tr > th',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_cells_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-header'] . ' > tr > th',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_cells_content',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Day content', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_header_cells_content_typography',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
		'type'  => 'typography',
		'css'   => [
			[
				'property' => 'typography',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-header'] . ' > tr > th > span',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_cells_content_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-header'] . ' > tr > th > span',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_cells_content_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-header'] . ' > tr > th > span',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_header_cells_content_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-header'] . ' > tr > th > span',
			],
		],
	]
);

$this->end_jet_control_group();

$this->start_jet_control_group( 'section_calendar_content' );

$this->register_jet_control(
	'calendar_content_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Header background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'],
			],
		],
	]
);

$this->register_jet_control(
	'calendar_content_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'],
			],
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_heading',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Day', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_content_cells_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_content',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Day content', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_typography',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
		'type'  => 'typography',
		'css'   => [
			[
				'property' => 'typography',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td > span',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td > span',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td > span',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td > span',
			],
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_current',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Current day', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_current_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td > *',
			]
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_current_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td > *',
			]
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_current_border_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'border-color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td > *',
			]
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_active',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Active day', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_active_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td > *',
			]
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_active_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td > *',
			]
		],
	]
);

$this->register_jet_control(
	'calendar_content_cells_content_active_border_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'border-color',
				'selector' => ".jet-smart-filters-datepicker-$this->id " . $css_scheme['calendar-body-content'] . ' > tr > td > *',
			]
		],
	]
);

$this->end_jet_control_group();