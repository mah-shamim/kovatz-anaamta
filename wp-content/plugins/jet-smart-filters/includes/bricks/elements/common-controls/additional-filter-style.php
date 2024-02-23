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

$this->start_jet_control_group( 'search_items_style_section' );

$this->register_jet_control(
	'search_items_width',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Input width', 'jet-smart-filters' ),
		'type'  => 'number',
		'units' => true,
		'css'   => [
			[
				'property' => 'max-width',
				'selector' => $css_items_search_scheme['search'],
			],
		],
	]
);

$this->register_jet_control(
	'search_items_typography',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
		'type'  => 'typography',
		'css'   => [
			[
				'property' => 'typography',
				'selector' => $css_items_search_scheme['search-input'],
			],
		],
	]
);

$this->register_jet_control(
	'search_items_placeholder_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Placeholder color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => $css_items_search_scheme['search-input'] . '::placeholder',
			],
		],
	]
);

$this->register_jet_control(
	'search_items_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => $css_items_search_scheme['search-input'],
			],
		],
	]
);

$this->register_jet_control(
	'search_input_margin',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Margin', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'margin',
				'selector' => $css_items_search_scheme['search-input'],
			],
		],
	]
);

$this->register_jet_control(
	'search_input_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => $css_items_search_scheme['search-input'],
			],
		],
	]
);

$this->register_jet_control(
	'search_items_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => $css_items_search_scheme['search-input'],
			],
		],
	]
);

$this->register_jet_control(
	'search_items_box_shadow',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
		'type'  => 'box-shadow',
		'css'   => [
			[
				'property' => 'box-shadow',
				'selector' => $css_items_search_scheme['search-input'],
			],
		],
	]
);

$this->register_jet_control(
	'search_remove',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Remove button', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'search_remove_size',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Size', 'jet-smart-filters' ),
		'type'  => 'number',
		'units' => true,
		'css'   => [
			[
				'property' => 'font-size',
				'selector' => $css_items_search_scheme['search-clear'],
			],
		],
	]
);

$this->register_jet_control(
	'search_remove_horizontal_offset',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Horizontal offset', 'jet-smart-filters' ),
		'type'  => 'number',
		'units' => true,
		'css'   => [
			[
				'property' => 'right',
				'selector' => $css_items_search_scheme['search-clear'],
			],
		],
	]
);

$this->register_jet_control(
	'search_remove_color',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'color',
				'selector' => $css_items_search_scheme['search-clear'],
			],
		],
	]
);

$this->end_jet_control_group();


/**
 * More Less style controls
 */

$css_items_moreless_scheme = apply_filters(
	'jet-smart-filters/widgets/items-moreless/css-scheme',
	[
		'more-less'        => '.jet-filter-items-moreless',
		'more-less-toggle' => '.jet-filter-items-moreless__toggle',
	]
);

$this->start_jet_control_group( 'more_less_style_section' );

$this->register_jet_control(
	'more_less_button_display',
	[
		'tab'     => 'style',
		'label'   => esc_html__( 'Display', 'jet-smart-filters' ),
		'type'    => 'select',
		'options' => [
			'block'        => esc_html__( 'Block', 'jet-smart-filters' ),
			'inline-block' => esc_html__( 'Inline Block', 'jet-smart-filters' ),
		],
		'default' => 'inline',
		'css'     => [
			[
				'property' => 'display',
				'selector' => $css_items_moreless_scheme['more-less-toggle'],
			],
		],
	]
);

$this->register_jet_control(
	'more_less_button_alignment',
	[
		'tab'      => 'style',
		'label'    => esc_html__( 'Alignment', 'jet-smart-filters' ),
		'type'     => 'text-align',
		'css'      => [
			[
				'property' => 'text-align',
				'selector' => $css_items_moreless_scheme['more-less'],
			],
		],
		'required' => [ 'more_less_button_display', '=', 'inline-block' ],
	]
);

$this->register_jet_control(
	'more_less_button_typography',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
		'type'  => 'typography',
		'css'   => [
			[
				'property' => 'typography',
				'selector' => $css_items_moreless_scheme['more-less-toggle'],
			],
		],
	]
);

$this->register_jet_control(
	'more_less_button_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => $css_items_moreless_scheme['more-less-toggle'],
			],
		],
	]
);

$this->register_jet_control(
	'more_less_button_margin',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Margin', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'margin',
				'selector' => $css_items_moreless_scheme['more-less-toggle'],
			],
		],
	]
);

$this->register_jet_control(
	'more_less_button_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => $css_items_moreless_scheme['more-less-toggle'],
			],
		],
	]
);

$this->register_jet_control(
	'more_less_button_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => $css_items_moreless_scheme['more-less-toggle'],
			],
		],
	]
);

$this->register_jet_control(
	'more_less_button_box_shadow',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
		'type'  => 'box-shadow',
		'css'   => [
			[
				'property' => 'box-shadow',
				'selector' => $css_items_moreless_scheme['more-less-toggle'],
			],
		],
	]
);

$this->end_jet_control_group();


/**
 * Dropdown style controls
 */

$css_items_dropdown_scheme = apply_filters(
	'jet-smart-filters/widgets/items-dropdown/css-scheme',
	[
		'dropdown'              => '.jet-filter-items-dropdown',
		'dropdown-label'        => '.jet-filter-items-dropdown__label',
		'dropdown-body'         => '.jet-filter-items-dropdown__body',
		'dropdown-active-items' => '.jet-filter-items-dropdown__active',
		'dropdown-active-item'  => '.jet-filter-items-dropdown__active__item',
		'dropdown-n-selected'   => '.jet-filter-items-dropdown__n-selected',
	]
);

$this->start_jet_control_group( 'dropdown_style_section' );

$this->register_jet_control(
	'dropdown_width',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Width', 'jet-smart-filters' ),
		'type'  => 'number',
		'units' => true,
		'css'   => [
			[
				'property' => 'max-width',
				'selector' => $css_items_dropdown_scheme['dropdown'],
			],
		],
	]
);

$this->register_jet_control(
	'dropdown_typography',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
		'type'  => 'typography',
		'css'   => [
			[
				'property' => 'typography',
				'selector' => $css_items_dropdown_scheme['dropdown-label'],
			],
		],
	]
);

$this->register_jet_control(
	'dropdown_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => $css_items_dropdown_scheme['dropdown-label'],
			],
		],
	]
);

$this->register_jet_control(
	'dropdown_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => $css_items_dropdown_scheme['dropdown-label'],
			],
		],
	]
);

$this->register_jet_control(
	'dropdown_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => $css_items_dropdown_scheme['dropdown-label'],
			],
		],
	]
);

$this->register_jet_control(
	'dropdown_box_shadow',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
		'type'  => 'box-shadow',
		'css'   => [
			[
				'property' => 'box-shadow',
				'selector' => $css_items_dropdown_scheme['dropdown-label'],
			],
		],
	]
);

// dropdown active items
if ( $this->jet_element_render !== 'radio' ) {

	$this->register_jet_control(
		'dropdown_active_items_heading',
		[
			'tab'   => 'style',
			'type'  => 'separator',
			'label' => esc_html__( 'Active items', 'jet-smart-filters' ),
		]
	);

	$this->register_jet_control(
		'dropdown_active_items_offset',
		[
			'tab'     => 'style',
			'label'   => esc_html__( 'Gap', 'jet-smart-filters' ),
			'type'    => 'number',
			'units'   => true,
			'default' => '8px',
			'css'     => [
				[
					'property' => 'gap',
					'selector' => $css_items_dropdown_scheme['dropdown-active-items'],
				],
				[
					'property' => 'margin',
					'selector' => $css_items_dropdown_scheme['dropdown-active-items'] . ', ' . $css_items_dropdown_scheme['dropdown-active-item'],
					'value'    => '0',
				],
			],
		]
	);

	$this->register_jet_control(
		'dropdown_active_item_typography',
		[
			'tab'   => 'style',
			'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'typography',
					'selector' => $css_items_dropdown_scheme['dropdown-active-item'],
				],
			],
		]
	);

	$this->register_jet_control(
		'dropdown_active_item_bg',
		[
			'tab'   => 'style',
			'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => $css_items_dropdown_scheme['dropdown-active-item'],
				],
			],
		]
	);

	$this->register_jet_control(
		'dropdown_active_item_padding',
		[
			'tab'   => 'style',
			'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
			'type'  => 'dimensions',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => $css_items_dropdown_scheme['dropdown-active-item'],
				],
			],
		]
	);

	$this->register_jet_control(
		'dropdown_active_item_border',
		[
			'tab'   => 'style',
			'label' => esc_html__( 'Border', 'jet-smart-filters' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => $css_items_dropdown_scheme['dropdown-active-item'],
				],
			],
		]
	);

	$this->register_jet_control(
		'dropdown_active_item_box_shadow',
		[
			'tab'   => 'style',
			'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
			'type'  => 'box-shadow',
			'css'   => [
				[
					'property' => 'box-shadow',
					'selector' => $css_items_dropdown_scheme['dropdown-active-item'],
				],
			],
		]
	);

	// N selected
	$this->register_jet_control(
		'dropdown_n_selected_heading',
		[
			'tab'      => 'style',
			'type'     => 'separator',
			'label'    => esc_html__( 'N Selected', 'jet-smart-filters' ),
			'required' => [ 'dropdown_n_selected_enabled', '=', true ],
		]
	);

	$this->register_jet_control(
		'dropdown_n_selected_typography',
		[
			'tab'      => 'style',
			'label'    => esc_html__( 'Typography', 'jet-smart-filters' ),
			'type'     => 'typography',
			'css'      => [
				[
					'property' => 'typography',
					'selector' => $css_items_dropdown_scheme['dropdown-n-selected'],
				],
			],
			'required' => [ 'dropdown_n_selected_enabled', '=', true ],
		]
	);

	$this->register_jet_control(
		'dropdown_n_selected_color',
		[
			'tab'      => 'style',
			'label'    => esc_html__( 'Text Color', 'jet-smart-filters' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'color',
					'selector' => $css_items_dropdown_scheme['dropdown-n-selected'],
				],
			],
			'required' => [ 'dropdown_n_selected_enabled', '=', true ],
		]
	);

	$this->register_jet_control(
		'dropdown_n_selected_margin',
		[
			'tab'      => 'style',
			'label'    => esc_html__( 'Margin', 'jet-smart-filters' ),
			'type'     => 'dimensions',
			'css'      => [
				[
					'property' => 'margin',
					'selector' => $css_items_dropdown_scheme['dropdown-n-selected'],
				],
			],
			'required' => [ 'dropdown_n_selected_enabled', '=', true ],
		]
	);
}

$this->register_jet_control(
	'dropdown_body_heading',
	[
		'tab'   => 'style',
		'type'  => 'separator',
		'label' => esc_html__( 'Dropdown body', 'jet-smart-filters' ),
	]
);

$this->register_jet_control(
	'dropdown_body_offset',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Offset', 'jet-smart-filters' ),
		'type'  => 'number',
		'units' => true,
		'css'   => [
			[
				'property' => 'margin-top',
				'selector' => $css_items_dropdown_scheme['dropdown-body'],
			],
		],
	]
);

$this->register_jet_control(
	'dropdown_body_bg',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
		'type'  => 'color',
		'css'   => [
			[
				'property' => 'background-color',
				'selector' => $css_items_dropdown_scheme['dropdown-body'],
			],
		],
	]
);

$this->register_jet_control(
	'dropdown_body_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => $css_items_dropdown_scheme['dropdown-body'],
			],
		],
	]
);

$this->register_jet_control(
	'dropdown_body_items_padding',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Items padding', 'jet-smart-filters' ),
		'type'  => 'dimensions',
		'css'   => [
			[
				'property' => 'padding',
				'selector' => $css_scheme['list-wrapper'],
			],
		],
	]
);

$this->register_jet_control(
	'dropdown_body_border',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Border', 'jet-smart-filters' ),
		'type'  => 'border',
		'css'   => [
			[
				'property' => 'border',
				'selector' => $css_items_dropdown_scheme['dropdown-body'],
			],
		],
	]
);

$this->register_jet_control(
	'dropdown_body_box_shadow',
	[
		'tab'   => 'style',
		'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
		'type'  => 'box-shadow',
		'css'   => [
			[
				'property' => 'box-shadow',
				'selector' => $css_items_dropdown_scheme['dropdown-body'],
			],
		],
	]
);

$this->end_jet_control_group();