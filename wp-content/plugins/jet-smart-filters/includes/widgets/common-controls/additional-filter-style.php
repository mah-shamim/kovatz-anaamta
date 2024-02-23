<?php
namespace Elementor;

use Elementor\Core\Schemes\Typography as Scheme_Typography;

/**
 * Search items style controls
 */
$css_items_search_scheme = apply_filters(
	'jet-smart-filters/widgets/items-search/css-scheme',
	array(
		'search'       => '.jet-filter-items-search',
		'search-input' => '.jet-filter-items-search__input',
		'search-clear' => '.jet-filter-items-search__clear',
	)
);

$this->start_controls_section(
	'search_items_style_section',
	[
		'label'      => esc_html__( 'Search Items', 'jet-smart-filters' ),
		'tab'        => Controls_Manager::TAB_STYLE,
		'condition' => array(
			'search_enabled' => 'yes'
		)
	]
);

$this->add_responsive_control(
	'search_items_width',
	array(
		'label'      => esc_html__( 'Input Width', 'jet-smart-filters' ),
		'type'       => Controls_Manager::SLIDER,
		'size_units' => array(
			'px',
			'%',
		),
		'range'      => array(
			'px' => array(
				'min' => 0,
				'max' => 500,
			),
			'%'  => array(
				'min' => 0,
				'max' => 100,
			),
		),
		'default'    => array(
			'size' => 100,
			'unit' => '%',
		),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_search_scheme['search'] => 'max-width: {{SIZE}}{{UNIT}};',
		),
		'separator'  => 'after'
	)
);

$this->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'search_items_typography',
		'selector' => '{{WRAPPER}} ' . $css_items_search_scheme['search-input']
	)
);

$this->add_control(
	'search_items_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_search_scheme['search-input']                             => 'color: {{VALUE}}',
			'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] . '::placeholder'           => 'color: {{VALUE}}',
			'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] . ':-ms-input-placeholder'  => 'color: {{VALUE}}',
			'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] . '::-ms-input-placeholder' => 'color: {{VALUE}}',

		),
	)
);

$this->add_control(
	'search_items_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'        => 'search_input_border',
		'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
		'placeholder' => '1px',
		'default'     => '1px',
		'selector'    => '{{WRAPPER}} ' . $css_items_search_scheme['search-input'],
		'separator'   => 'before'
	)
);

$this->add_control(
	'search_input_border_radius',
	array(
		'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_group_control(
	Group_Control_Box_Shadow::get_type(),
	array(
		'name'     => 'search_input_box_shadow',
		'selector' => '{{WRAPPER}} ' . $css_items_search_scheme['search-input'],
	)
);

$this->add_responsive_control(
	'search_input_padding',
	array(
		'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_search_scheme['search-input'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
		'separator'  => 'before'
	)
);

$this->add_responsive_control(
	'search_input_margin',
	array(
		'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_search_scheme['search'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
		'separator'  => 'after'
	)
);

$this->add_control(
	'search_remove',
	array(
		'label' => __( 'Remove', 'jet-smart-filters' ),
		'type' => Controls_Manager::HEADING,
	)
);

$this->add_responsive_control(
	'search_remove_size',
	array(
		'label'      => esc_html__( 'Size', 'jet-smart-filters' ),
		'type'       => Controls_Manager::SLIDER,
		'range'      => array(
			'px' => array(
				'min' => 0,
				'max' => 50,
			),
		),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_search_scheme['search-clear'] => 'font-size: {{SIZE}}{{UNIT}};',
		),
	)
);

$this->add_responsive_control(
	'search_remove_horizontal_offset',
	array(
		'label'     => esc_html__( 'Horizontal offset', 'jet-smart-filters' ),
		'type'      => Controls_Manager::SLIDER,
		'range'     => array(
			'px' => array(
				'min' => 0,
				'max' => 30,
			),
		),
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_search_scheme['search-clear']      => 'right: {{SIZE}}{{UNIT}};',
			'.rtl {{WRAPPER}} ' . $css_items_search_scheme['search-clear'] => 'right: initial; left: {{SIZE}}{{UNIT}};',
		),
	)
);

$this->start_controls_tabs( 'search_remove_style_tabs' );

$this->start_controls_tab(
	'search_remove_normal_styles',
	array(
		'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'search_remove_normal_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_search_scheme['search-clear'] => 'color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();

$this->start_controls_tab(
	'search_remove_hover_styles',
	array(
		'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'search_remove_hover_color',
	array(
		'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_search_scheme['search-clear'] . ':hover' => 'color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();

$this->end_controls_tabs();

$this->end_controls_section();

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

$this->start_controls_section(
	'more_less_style_section',
	[
		'label'     => esc_html__( 'More Less Toggle', 'jet-smart-filters' ),
		'tab'       => Controls_Manager::TAB_STYLE,
		'condition' => array(
			'moreless_enabled' => 'yes'
		)
	]
);

$this->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'more_less_button_typography',
		'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
		'selector' => '{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'],
	)
);

$this->start_controls_tabs( 'more_less_button_style_tabs' );

$this->start_controls_tab(
	'more_less_button_normal_styles',
	array(
		'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'more_less_button_normal_color',
	array(
		'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'more_less_button_normal_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] => 'background-color: {{VALUE}}',
		),
	)
);

$this->end_controls_tab();

$this->start_controls_tab(
	'more_less_button_hover_styles',
	array(
		'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
	)
);

$this->add_control(
	'more_less_button_hover_color',
	array(
		'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] . ':hover' => 'color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'more_less_button_hover_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] . ':hover' => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_control(
	'more_less_button_hover_border_color',
	array(
		'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] . ':hover' => 'border-color: {{VALUE}}',
		)
	)
);

$this->end_controls_tab();

$this->end_controls_tabs();

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'     => 'more_less_button_border',
		'label'    => esc_html__( 'Button Border', 'jet-smart-filters' ),
		'selector' => '{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'],
	)
);

$this->add_control(
	'more_less_button_border_radius',
	array(
		'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_group_control(
	Group_Control_Box_Shadow::get_type(),
	array(
		'name'     => 'more_less_button_shadow',
		'selector' => '{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'],
	)
);

$this->add_responsive_control(
	'more_less_button_padding',
	array(
		'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less-toggle'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		)
	)
);

$this->add_control(
	'more_less_heading',
	array(
		'label'     => esc_html__( 'Holder', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_control(
	'more_less_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less'] => 'background-color: {{VALUE}}',
		),
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'     => 'more_less_border',
		'selector' => '{{WRAPPER}} ' . $css_items_moreless_scheme['more-less'],
	)
);

$this->add_responsive_control(
	'more_less_padding',
	array(
		'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		)
	)
);

$this->add_responsive_control(
	'more_less_button_alignment',
	array(
		'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
		'type'      => Controls_Manager::CHOOSE,
		'toggle'    => false,
		'default'   => 'left',
		'options'   => array(
			'left'   => array(
				'title' => esc_html__( 'Left', 'jet-smart-filters' ),
				'icon'  => 'eicon-text-align-left',
			),
			'center' => array(
				'title' => esc_html__( 'Center', 'jet-smart-filters' ),
				'icon'  => 'eicon-text-align-center',
			),
			'right'  => array(
				'title' => esc_html__( 'Right', 'jet-smart-filters' ),
				'icon'  => 'eicon-text-align-right',
			),
		),
		'separator' => 'before',
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_moreless_scheme['more-less'] => 'text-align: {{VALUE}};',
		)
	)
);

$this->end_controls_section();

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

$this->start_controls_section(
	'dropdown_style_section',
	[
		'label'     => esc_html__( 'Dropdown', 'jet-smart-filters' ),
		'tab'       => Controls_Manager::TAB_STYLE,
		'condition' => array(
			'dropdown_enabled' => 'yes'
		)
	]
);

$this->add_responsive_control(
	'dropdown_width',
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
				'max' => 500,
			),
		),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown'] => 'max-width: {{SIZE}}{{UNIT}}',
		),
	)
);

$this->add_control(
	'dropdown_label_heading',
	array(
		'label'     => esc_html__( 'Label', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'dropdown_label_typography',
		'selector' => '{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'],
	)
);

$this->add_control(
	'dropdown_label_color',
	array(
		'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'default'   => '',
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'] => 'color: {{VALUE}};',
		),
	)
);

$this->add_control(
	'dropdown_label_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'] => 'background-color: {{VALUE}};',
		),
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'      => 'dropdown_label_border',
		'default'   => '1px',
		'selector'  => '{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'],
	)
);

$this->add_control(
	'dropdown_label_border_radius',
	array(
		'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_group_control(
	Group_Control_Box_Shadow::get_type(),
	array(
		'name'     => 'dropdown_label_box_shadow',
		'selector' => '{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'],
	)
);

$this->add_responsive_control(
	'dropdown_label_padding',
	array(
		'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', 'em', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-label'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		)
	)
);

// dropdown active items
if ( $this->get_name() !== 'jet-smart-filters-radio' ) {

	$this->add_control(
		'dropdown_active_items_heading',
		array(
			'label'     => esc_html__( 'Active Items', 'jet-smart-filters' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		)
	);

	$this->add_responsive_control(
		'dropdown_active_items_offset',
		array(
			'label'     => esc_html__( 'Offset', 'jet-smart-filters' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => array(
				'px' => array(
					'min' => 0,
					'max' => 40,
				),
			),
			'selectors' => array(
				'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-items'] => 'margin: -{{SIZE}}{{UNIT}};',
				'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] => 'margin: {{SIZE}}{{UNIT}};',
			)
		)
	);

	$this->add_group_control(
		Group_Control_Typography::get_type(),
		array(
			'name'     => 'dropdown_active_item_typography',
			'selector' => '{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'],
		)
	);

	$this->start_controls_tabs( 'dropdown_active_item_style_tabs' );

	$this->start_controls_tab(
		'dropdown_active_item_normal_styles',
		array(
			'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
		)
	);

	$this->add_control(
		'dropdown_active_item_normal_color',
		array(
			'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] => 'color: {{VALUE}}',
			),
		)
	);

	$this->add_control(
		'dropdown_active_item_normal_background_color',
		array(
			'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] => 'background-color: {{VALUE}}',
			),
		)
	);

	$this->end_controls_tab();

	$this->start_controls_tab(
		'dropdown_active_item_hover_styles',
		array(
			'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
		)
	);

	$this->add_control(
		'dropdown_active_item_hover_color',
		array(
			'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] . ':hover' => 'color: {{VALUE}}',
			),
		)
	);

	$this->add_control(
		'dropdown_active_item_hover_background_color',
		array(
			'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] . ':hover' => 'background-color: {{VALUE}}',
			),
		)
	);

	$this->add_control(
		'dropdown_active_item_hover_border_color',
		array(
			'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] . ':hover' => 'border-color: {{VALUE}}',
			),
		)
	);

	$this->end_controls_tab();

	$this->end_controls_tabs();

	$this->add_group_control(
		Group_Control_Border::get_type(),
		array(
			'name'     => 'dropdown_active_item_border',
			'label'    => esc_html__( 'Border', 'jet-smart-filters' ),
			'default'  => '1px',
			'selector' => '{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'],
		)
	);

	$this->add_control(
		'dropdown_active_item_border_radius',
		array(
			'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		)
	);

	$this->add_responsive_control(
		'dropdown_active_item_padding',
		array(
			'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-active-item'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		)
	);

	// N selected
	$this->add_control(
		'dropdown_n_selected_heading',
		array(
			'label'     => esc_html__( 'N Selected', 'jet-smart-filters' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => array(
				'dropdown_n_selected_enabled' => 'yes'
			)
		)
	);

	$this->add_group_control(
		Group_Control_Typography::get_type(),
		array(
			'name'      => 'dropdown_n_selected_typography',
			'selector'  => '{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-n-selected'],
			'condition' => array(
				'dropdown_n_selected_enabled' => 'yes'
			)
		)
	);

	$this->add_control(
		'dropdown_n_selected_color',
		array(
			'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-n-selected'] => 'color: {{VALUE}}',
			),
			'condition' => array(
				'dropdown_n_selected_enabled' => 'yes'
			)
		)
	);

	$this->add_responsive_control(
		'dropdown_n_selected_margin',
		array(
			'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-n-selected'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
			'condition' => array(
				'dropdown_n_selected_enabled' => 'yes'
			)
		)
	);
}

$this->add_control(
	'dropdown_body_heading',
	array(
		'label'     => esc_html__( 'Dropdown Body', 'jet-smart-filters' ),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'before',
	)
);

$this->add_responsive_control(
	'dropdown_body_offset',
	array(
		'label'     => esc_html__( 'Offset', 'jet-smart-filters' ),
		'type'      => Controls_Manager::SLIDER,
		'range'     => array(
			'px' => array(
				'min' => 0,
				'max' => 100,
			),
		),
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-body'] => 'margin-top: {{SIZE}}{{UNIT}};'
		)
	)
);

$this->add_group_control(
	Group_Control_Border::get_type(),
	array(
		'name'     => 'dropdown_body_border',
		'default'  => '1px',
		'selector' => '{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-body'],
	)
);

$this->add_control(
	'dropdown_body_border_radius',
	array(
		'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-body'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
	)
);

$this->add_control(
	'dropdown_body_background_color',
	array(
		'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} ' . $css_items_dropdown_scheme['dropdown-body'] => 'background-color: {{VALUE}};',
		),
	)
);

$this->add_responsive_control(
	'dropdown_body_items_padding',
	array(
		'label'      => esc_html__( 'Items Padding', 'jet-smart-filters' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px', 'em', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} ' . $css_scheme['list-wrapper'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		)
	)
);

$this->end_controls_section();