<?php
/**
 * Search items style controls
 */

$css_items_search_scheme = apply_filters(
	'jet-smart-filters/widgets/items-search/css-scheme',
	[
		'search'       => '.jet-filter-items-search',
		'search-input' => '.jet-filter-items-search__input',
		'search-clear' => '.jet-filter-items-search__clear',
	]
);

$this->controls_manager->start_section(
	'style_controls',
	[
		'id'          => 'search_items_style_section',
		'initialOpen' => false,
		'title'       => esc_html__( 'Search Items', 'jet-smart-filters' ),
		'condition' => [
			'search_enabled' => true,
		],
	]
);

$this->controls_manager->add_control([
	'id'        => 'search_items_width',
	'type'      => 'range',
	'label'     => esc_html__( 'Input Width', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => [
		'{{WRAPPER}} ' . $css_items_search_scheme['search'] => 'max-width: {{VALUE}}{{UNIT}};',
	],
	'attributes' => [
		'default' => [
			'value' => [
				'value' => 100,
				'unit' => '%'
			]
		]
	],
	'units' => [
		[
			'value' => '%',
			'intervals' => [
				'step' => 1,
				'min'  => 0,
				'max'  => 100,
			]
		],
		[
			'value' => 'px',
			'intervals' => [
				'step' => 1,
				'min'  => 0,
				'max'  => 500,
			]
		],
	],
]);

$this->controls_manager->add_control([
	'id'         => 'search_items_typography',
	'type'       => 'typography',
	'attributes' => [],
	'separator'  => 'after',
	'css_selector' => [
		'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
	],
]);

$this->controls_manager->add_control([
	'id'        => 'search_items_color',
	'type'      => 'color-picker',
	'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_search_scheme['search-input']                             => 'color: {{VALUE}}',
		'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] . '::placeholder'           => 'color: {{VALUE}}',
		'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] . ':-ms-input-placeholder'  => 'color: {{VALUE}}',
		'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] . '::-ms-input-placeholder' => 'color: {{VALUE}}',
	),
]);

$this->controls_manager->add_control([
	'id'        => 'search_items_background_color',
	'type'      => 'color-picker',
	'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] => 'background-color: {{VALUE}}',
	),
]);

$this->controls_manager->add_control([
	'id'        => 'search_input_border',
	'type'      => 'border',
	'label'     => esc_html__( 'Border', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector'  => array(
		'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] => 'border-style:{{STYLE}};border-width:{{WIDTH}};border-radius:{{RADIUS}};border-color:{{COLOR}};',
	),
]);

$this->controls_manager->add_control([
	'id'         => 'search_input_padding',
	'type'       => 'dimensions',
	'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
	'units'      => array( 'px', '%' ),
	'css_selector'  => array(
		'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
	),
	'separator'  => 'after',
]);

$this->controls_manager->add_control([
	'id'         => 'search_input_margin',
	'type'       => 'dimensions',
	'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
	'units'      => array( 'px', '%' ),
	'css_selector'  => array(
		'{{WRAPPER}} ' . $css_items_search_scheme['search'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
	),
	'separator'  => 'after',
]);

$this->controls_manager->add_control([
	'id'        => 'search_remove_size',
	'type'      => 'range',
	'label'     => esc_html__( 'Remove Size', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => [
		'{{WRAPPER}} ' . $css_items_search_scheme['search-clear'] => 'font-size: {{VALUE}}{{UNIT}};',
	],
	'attributes' => [
		'default' => [
			'value' => 16,
			'unit'  => 'px'
		]
	],
	'units' => [
		[
			'value' => 'px',
			'intervals' => [
				'step' => 1,
				'min'  => 0,
				'max'  => 50,
			]
		],
	],
]);

$this->controls_manager->add_control([
	'id'        => 'search_remove_right_offset',
	'type'      => 'range',
	'label'     => esc_html__( 'Remove Right Offset', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => [
		'{{WRAPPER}} ' . $css_items_search_scheme['search-clear'] => 'right: {{VALUE}}{{UNIT}};',
	],
	'attributes' => [
		'default' => [
			'value' => 0,
			'unit'  => 'px'
		]
	],
	'units' => [
		[
			'value' => 'px',
			'intervals' => [
				'step' => 1,
				'min'  => 0,
				'max'  => 30,
			]
		],
	],
]);

$this->controls_manager->start_tabs(
	'style_controls',
	[
		'id' => 'search_remove_style_tabs',
	]
);

$this->controls_manager->start_tab(
	'style_controls',
	[
		'id'    => 'search_remove_normal_styles',
		'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
	]
);

$this->controls_manager->add_control([
	'id'       => 'search_remove_normal_color',
	'type'     => 'color-picker',
	'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_search_scheme['search-clear'] => 'color: {{VALUE}}',
	),
	'attributes' => [
		'default' => [
			'value' => ''
		],
	],
]);

$this->controls_manager->end_tab();

$this->controls_manager->start_tab(
	'style_controls',
	[
		'id'    => 'search_remove_hover_styles',
		'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
	]
);

$this->controls_manager->add_control([
	'id'       => 'search_remove_hover_color',
	'type'     => 'color-picker',
	'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_search_scheme['search-clear'] . ':hover' => 'color: {{VALUE}}',
	),
	'attributes' => [
		'default' => [
			'value' => ''
		],
	],
]);

$this->controls_manager->end_tab();

$this->controls_manager->end_tabs();

$this->controls_manager->end_section();


/**
 * More Less style controls
 */
$css_items_moreless_scheme = apply_filters(
	'jet-smart-filters/widgets/items-moreless/css-scheme',
	array(
		'more-less'        => '.jet-filter-items-moreless',
		'more-less-toggle' => '.jet-filter-items-moreless__toggle',
	)
);

$this->controls_manager->start_section(
	'style_controls',
	[
		'id'          => 'more_less_style_section',
		'initialOpen' => false,
		'title'       => esc_html__( 'More Less Toggle', 'jet-smart-filters' ),
		'condition' => [
			'moreless_enabled' => true,
		],
	]
);

$this->controls_manager->add_control([
	'id'         => 'more_less_button_typography',
	'type'       => 'typography',
	'attributes' => [],
	'separator'  => 'after',
	'css_selector' => [
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
	],
]);

$this->controls_manager->start_tabs(
	'style_controls',
	[
		'id' => 'more_less_button_style_tabs',
	]
);

$this->controls_manager->start_tab(
	'style_controls',
	[
		'id'    => 'more_less_button_normal_styles',
		'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
	]
);

$this->controls_manager->add_control([
	'id'       => 'more_less_button_normal_color',
	'type'     => 'color-picker',
	'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] => 'color: {{VALUE}}',
	),
	'attributes' => [
		'default' => [
			'value' => ''
		],
	],
]);

$this->controls_manager->add_control([
	'id'        => 'more_less_button_normal_background_color',
	'type'      => 'color-picker',
	'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] => 'background-color: {{VALUE}}',
	),
	'attributes' => [
		'default' => [
			'value' => ''
		],
	],
]);

$this->controls_manager->end_tab();

$this->controls_manager->start_tab(
	'style_controls',
	[
		'id'    => 'more_less_button_hover_styles',
		'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
	]
);

$this->controls_manager->add_control([
	'id'       => 'more_less_button_hover_color',
	'type'     => 'color-picker',
	'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] . ':hover' => 'color: {{VALUE}}',
	),
	'attributes' => [
		'default' => [
			'value' => ''
		],
	],
]);

$this->controls_manager->add_control([
	'id'       => 'more_less_button_hover_background_color',
	'type'     => 'color-picker',
	'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] . ':hover' => 'background-color: {{VALUE}}',
	),
	'attributes' => [
		'default' => [
			'value' => ''
		],
	],
]);

$this->controls_manager->add_control([
	'id'        => 'more_less_button_hover_border_color',
	'type'      => 'color-picker',
	'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] . ':hover' => 'border-color: {{VALUE}}',
	),
	'attributes' => [
		'default' => [
			'value' => ''
		],
	],
]);

$this->controls_manager->end_tab();

$this->controls_manager->end_tabs();

$this->controls_manager->add_control([
	'id'        => 'more_less_button_border',
	'type'      => 'border',
	'label'     => esc_html__( 'Border', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector'  => array(
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] => 'border-style:{{STYLE}};border-width:{{WIDTH}};border-radius:{{RADIUS}};border-color:{{COLOR}};',
	),
]);

$this->controls_manager->add_control([
	'id'         => 'more_less_button_padding',
	'type'       => 'dimensions',
	'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
	'units'      => array( 'px', '%' ),
	'css_selector'  => array(
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
	),
	'separator'  => 'after',
]);

$this->controls_manager->add_control([
	'id'        => 'more_less_background_color',
	'type'      => 'color-picker',
	'label'     => esc_html__( 'Container Background Color', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less'] => 'background-color: {{VALUE}}',
	),
	'attributes' => [
		'default' => [
			'value' => ''
		],
	],
]);

$this->controls_manager->add_control([
	'id'        => 'more_less_border',
	'type'      => 'border',
	'label'     => esc_html__( 'Container Border', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector'  => array(
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less'] => 'border-style:{{STYLE}};border-width:{{WIDTH}};border-radius:{{RADIUS}};border-color:{{COLOR}};',
	),
]);

$this->controls_manager->add_control([
	'id'         => 'more_less_padding',
	'type'       => 'dimensions',
	'label'      => esc_html__( 'Container Padding', 'jet-smart-filters' ),
	'units'      => array( 'px', '%' ),
	'css_selector'  => array(
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
	),
	'separator'  => 'after',
]);

$this->controls_manager->add_control([
	'id'        => 'more_less_button_alignment',
	'type'      => 'choose',
	'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
	'options'   =>[
		'left'    => [
			'shortcut' => esc_html__( 'Top', 'jet-smart-filters' ),
			'icon'  => 'dashicons-editor-alignleft',
		],
		'center'    => [
			'shortcut' => esc_html__( 'Center', 'jet-smart-filters' ),
			'icon'  => 'dashicons-editor-aligncenter',
		],
		'right'    => [
			'shortcut' => esc_html__( 'Bottom', 'jet-smart-filters' ),
			'icon'  => 'dashicons-editor-alignright',
		],
	],
	'css_selector' => [
		'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less'] => 'text-align: {{VALUE}};',
	],
	'attributes' => [
		'default' => [
			'value' => 'flex-start',
		]
	]
]);

$this->controls_manager->end_section();


/**
 * Dropdown style controls
 */
$css_items_dropdown_scheme = apply_filters(
	'jet-smart-filters/widgets/items-dropdown/css-scheme',
	array(
		'dropdown'              => '.jet-filter-items-dropdown',
		'dropdown-label'        => '.jet-filter-items-dropdown__label',
		'dropdown-body'         => '.jet-filter-items-dropdown__body',
		'dropdown-active-items' => '.jet-filter-items-dropdown__active',
		'dropdown-active-item'  => '.jet-filter-items-dropdown__active__item',
		'dropdown-n-selected'   => '.jet-filter-items-dropdown__n-selected',
	)
);

$this->controls_manager->start_section(
	'style_controls',
	[
		'id'          => 'dropdown_style_section',
		'initialOpen' => false,
		'title'       => esc_html__( 'Dropdown', 'jet-smart-filters' ),
		'condition' => [
			'dropdown_enabled' => true,
		],
	]
);

$this->controls_manager->add_control([
	'id'        => 'dropdown_width',
	'type'      => 'range',
	'label'     => esc_html__( 'Input Width', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => [
		'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown'] => 'max-width: {{VALUE}}{{UNIT}};',
	],
	'attributes' => [
		'default' => [
			'value' => [
				'value' => 100,
				'unit' => '%'
			]
		]
	],
	'units' => [
		[
			'value' => '%',
			'intervals' => [
				'step' => 1,
				'min'  => 10,
				'max'  => 100,
			]
		],
		[
			'value' => 'px',
			'intervals' => [
				'step' => 1,
				'min'  => 50,
				'max'  => 500,
			]
		],
	],
]);

$this->controls_manager->add_control([
	'id'         => 'dropdown_label_typography',
	'type'       => 'typography',
	'attributes' => [],
	'separator'  => 'after',
	'css_selector' => [
		'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
	],
]);

$this->controls_manager->add_control([
	'id'        => 'dropdown_label_color',
	'type'      => 'color-picker',
	'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'] => 'color: {{VALUE}};',
	),
	'attributes' => [
		'default' => [
			'value' => ''
		],
	],
]);

$this->controls_manager->add_control([
	'id'        => 'dropdown_label_background_color',
	'type'      => 'color-picker',
	'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'] => 'background-color: {{VALUE}};',
	),
	'attributes' => [
		'default' => [
			'value' => ''
		],
	],
]);

$this->controls_manager->add_control([
	'id'        => 'dropdown_label_border',
	'type'      => 'border',
	'label'     => esc_html__( 'Border', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector'  => array(
		'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'] => 'border-style:{{STYLE}};border-width:{{WIDTH}};border-radius:{{RADIUS}};border-color:{{COLOR}};',
	),
]);

$this->controls_manager->add_control([
	'id'         => 'dropdown_label_padding',
	'type'       => 'dimensions',
	'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
	'units'      => array( 'px', '%' ),
	'css_selector'  => array(
		'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
	),
	'separator'  => 'after',
]);

// dropdown active items
if ( $this->get_name() !== 'radio' ) {

	$this->controls_manager->add_control([
		'id'        => 'dropdown_active_items_offset',
		'type'      => 'range',
		'label'     => esc_html__( 'Active Item Offset', 'jet-smart-filters' ),
		'separator' => 'after',
		'css_selector' => [
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-items'] => 'margin: -{{VALUE}}{{UNIT}};',
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] => 'margin: {{VALUE}}{{UNIT}};',
		],
		'attributes' => [
			'default' => [
				'value' => 3,
				'unit'  => 'px'
			]
		],
		'units' => [
			[
				'value' => 'px',
				'intervals' => [
					'step' => 1,
					'min'  => 0,
					'max'  => 40,
				]
			],
		],
	]);

	$this->controls_manager->add_control([
		'id'         => 'dropdown_active_item_typography',
		'type'       => 'typography',
		'label'      => esc_html__( 'Active Item Typography', 'jet-smart-filters' ),
		'attributes' => [],
		'separator'  => 'after',
		'css_selector' => [
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
		],
	]);

	$this->controls_manager->start_tabs(
		'style_controls',
		[
			'id' => 'dropdown_active_item_style_tabs',
		]
	);

	$this->controls_manager->start_tab(
		'style_controls',
		[
			'id'    => 'dropdown_active_item_normal_styles',
			'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
		]
	);

	$this->controls_manager->add_control([
		'id'       => 'dropdown_active_item_normal_color',
		'type'     => 'color-picker',
		'label'     => esc_html__( 'Active Item Color', 'jet-smart-filters' ),
		'css_selector' => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] => 'color: {{VALUE}}',
		),
		'attributes' => [
			'default' => [
				'value' => ''
			],
		],
	]);

	$this->controls_manager->add_control([
		'id'        => 'dropdown_active_item_normal_background_color',
		'type'      => 'color-picker',
		'label'     => esc_html__( 'Active Item Background Color', 'jet-smart-filters' ),
		'separator' => 'after',
		'css_selector' => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] => 'background-color: {{VALUE}}',
		),
		'attributes' => [
			'default' => [
				'value' => ''
			],
		],
	]);

	$this->controls_manager->end_tab();

	$this->controls_manager->start_tab(
		'style_controls',
		[
			'id'    => 'dropdown_active_item_hover_styles',
			'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
		]
	);

	$this->controls_manager->add_control([
		'id'       => 'dropdown_active_item_hover_color',
		'type'     => 'color-picker',
		'label'     => esc_html__( 'Active Item Color', 'jet-smart-filters' ),
		'css_selector' => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] . ':hover' => 'color: {{VALUE}}',
		),
		'attributes' => [
			'default' => [
				'value' => ''
			],
		],
	]);

	$this->controls_manager->add_control([
		'id'       => 'dropdown_active_item_hover_background_color',
		'type'     => 'color-picker',
		'label'     => esc_html__( 'Active Item Background Color', 'jet-smart-filters' ),
		'css_selector' => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] . ':hover' => 'background-color: {{VALUE}}',
		),
		'attributes' => [
			'default' => [
				'value' => ''
			],
		],
	]);

	$this->controls_manager->add_control([
		'id'        => 'dropdown_active_item_hover_border_color',
		'type'      => 'color-picker',
		'label'     => esc_html__( 'Active Item Border Color', 'jet-smart-filters' ),
		'separator' => 'after',
		'css_selector' => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] . ':hover' => 'border-color: {{VALUE}}',
		),
		'attributes' => [
			'default' => [
				'value' => ''
			],
		],
	]);

	$this->controls_manager->end_tab();

	$this->controls_manager->end_tabs();

	$this->controls_manager->add_control([
		'id'        => 'dropdown_active_item_border',
		'type'      => 'border',
		'label'     => esc_html__( 'Active Item Border', 'jet-smart-filters' ),
		'separator' => 'after',
		'css_selector'  => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] => 'border-style:{{STYLE}};border-width:{{WIDTH}};border-radius:{{RADIUS}};border-color:{{COLOR}};',
		),
	]);

	$this->controls_manager->add_control([
		'id'         => 'dropdown_active_item_padding',
		'type'       => 'dimensions',
		'label'      => esc_html__( 'Active Item Padding', 'jet-smart-filters' ),
		'units'      => array( 'px', '%' ),
		'css_selector'  => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
		),
		'separator'  => 'after',
	]);

	// N selected
	$this->controls_manager->add_control([
		'id'         => 'dropdown_n_selected_typography',
		'type'       => 'typography',
		'label'      => esc_html__( 'N Selected Typography', 'jet-smart-filters' ),
		'attributes' => [],
		'separator'  => 'after',
		'css_selector' => [
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-n-selected'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
		],
		'condition' => [
			'dropdown_n_selected_enabled' => true,
		],
	]);

	$this->controls_manager->add_control([
		'id'        => 'dropdown_n_selected_color',
		'type'      => 'color-picker',
		'label'     => esc_html__( 'N Selected Color', 'jet-smart-filters' ),
		'separator' => 'after',
		'css_selector' => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-n-selected'] => 'color: {{VALUE}};',
		),
		'attributes' => [
			'default' => [
				'value' => ''
			],
		],
		'condition' => [
			'dropdown_n_selected_enabled' => true,
		],
	]);

	$this->controls_manager->add_control([
		'id'         => 'dropdown_n_selected_margin',
		'type'       => 'dimensions',
		'label'      => esc_html__( 'N Selected Margin', 'jet-smart-filters' ),
		'units'      => array( 'px', '%' ),
		'css_selector'  => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-n-selected'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
		),
		'separator'  => 'after',
		'condition'  => [
			'dropdown_n_selected_enabled' => true,
		],
	]);
}

$this->controls_manager->add_control([
	'id'        => 'dropdown_body_offset',
	'type'      => 'range',
	'label'     => esc_html__( 'Dropdown Body Offset', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => [
		'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-body'] => 'margin-top: {{VALUE}}{{UNIT}};',
	],
	'attributes' => [
		'default' => [
			'value' => 5,
			'unit'  => 'px'
		]
	],
	'units' => [
		[
			'value' => 'px',
			'intervals' => [
				'step' => 1,
				'min'  => 0,
				'max'  => 100,
			]
		],
	],
]);

$this->controls_manager->add_control([
	'id'        => 'search_input_border',
	'type'      => 'border',
	'label'     => esc_html__( 'Dropdown Body Border', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector'  => array(
		'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-body'] => 'border-style:{{STYLE}};border-width:{{WIDTH}};border-radius:{{RADIUS}};border-color:{{COLOR}};',
	),
]);

$this->controls_manager->add_control([
	'id'        => 'dropdown_body_background_color',
	'type'      => 'color-picker',
	'label'     => esc_html__( 'Dropdown Body Background Color', 'jet-smart-filters' ),
	'separator' => 'after',
	'css_selector' => array(
		'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-body'] => 'background-color: {{VALUE}};',
	),
	'attributes' => [
		'default' => [
			'value' => ''
		],
	],
]);

$this->controls_manager->add_control([
	'id'         => 'dropdown_body_items_padding',
	'type'       => 'dimensions',
	'label'      => esc_html__( 'Dropdown Body Items Padding', 'jet-smart-filters' ),
	'units'      => array( 'px', '%' ),
	'css_selector'  => array(
		'{{WRAPPER}} ' . $this->css_scheme['list-wrapper'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
	),
]);

$this->controls_manager->end_section();