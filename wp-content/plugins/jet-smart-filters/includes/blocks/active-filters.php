<?php
/**
 * Active_Filters Filter
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Block_Active_Filters' ) ) {
	/**
	 * Define Jet_Smart_Filters_Block_Active_Filters class
	 */
	class Jet_Smart_Filters_Block_Active_Filters extends Jet_Smart_Filters_Block_Base {
		/**
		 * Returns block name
		 */
		public function get_name() {

			return 'active';
		}

		public function set_css_scheme() {

			$this->css_scheme = apply_filters(
				'jet-smart-filters/widgets/active-filters/css-scheme',
				[
					'filters'       => '.jet-smart-filters-active',
					'filters-list'  => '.jet-smart-filters-active .jet-active-filters__list',
					'filters-title' => '.jet-smart-filters-active .jet-active-filters__title',
					'filter'        => '.jet-smart-filters-active .jet-active-filter',
					'filter-label'  => '.jet-smart-filters-active .jet-active-filter__label',
					'filter-value'  => '.jet-smart-filters-active .jet-active-filter__val',
					'filter-remove' => '.jet-smart-filters-active .jet-active-filter__remove',
				]
			);
		}

		public function add_style_manager_options() {

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_filters_title_style',
					'initialOpen' => true,
					'title'       => esc_html__( 'Title', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'filters_title_typography',
				'type'       => 'typography',
				'attributes' => [],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme[ 'filters-title' ] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'       => 'filters_title_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filters-title'] => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'filters_title_margin',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['filters-title'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_filters_styles',
					'initialOpen' => false,
					'title'       => esc_html__( 'Filters', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'filters_position',
				'type'      => 'choose',
				'label'     => esc_html__( 'Filters Position', 'jet-smart-filters' ),
				'separator' => 'both',
				'options'   =>[
					'row'    => [
						'shortcut' => esc_html__( 'Line', 'jet-smart-filters' ),
						'icon'  => 'dashicons-ellipsis',
					],
					'column' => [
						'shortcut' => esc_html__( 'Column', 'jet-smart-filters' ),
						'icon'  => 'dashicons-menu-alt',
					],
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filters']      => 'flex-direction: {{VALUE}};',
					'{{WRAPPER}} ' . $this->css_scheme['filters-list'] => 'flex-direction: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'row',
					],
				]
			]);

			$this->controls_manager->add_control([
				'id'        => 'filters_space_between_horizontal',
				'type'      => 'range',
				'label'     => esc_html__( 'Horizontal Offset', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter'] => 'margin-right: {{VALUE}}{{UNIT}};',
				],
				'condition' => [
					'filters_position' => 'row',
				],
				'attributes' => [
					'default' => [
						'value' => 5,
						'unit' => 'px'
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
				'id'        => 'filters_space_between_vertical',
				'type'      => 'range',
				'label'     => esc_html__( 'Vertical Offset', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter'] => 'margin-bottom: {{VALUE}}{{UNIT}};',
				],
				'condition' => [
					'filters_position' => 'column',
				],
				'attributes' => [
					'default' => [
						'value' => 5,
						'unit' => 'px'
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
				'id'       => 'filters_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filters-list'] => 'background-color: {{VALUE}};',
				),
				'separator' => 'after',
			]);

			$this->controls_manager->add_control([
				'id'         => 'filters_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['filters-list'] => 'border-style:{{STYLE}};border-width:{{WIDTH}};border-radius:{{RADIUS}};border-color:{{COLOR}};',
				),
				'separator' => 'after',
			]);

			$this->controls_manager->add_control([
				'id'         => 'filters_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['filters-list'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'  => 'after',
			]);

			$this->controls_manager->add_control([
				'id'        => 'filters_alignment_column',
				'type'      => 'choose',
				'label'     => esc_html__( 'Vertical Alignment', 'jet-smart-filters' ),
				'separator' => 'after',
				'options'   =>[
					'flex-start'    => [
						'shortcut' => esc_html__( 'Top', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-alignleft',
					],
					'center'    => [
						'shortcut' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-aligncenter',
					],
					'flex-end'    => [
						'shortcut' => esc_html__( 'Bottom', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-alignright',
					],
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filters-list'] => 'align-items: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'flex-start',
					]
				]
			]);

			$this->controls_manager->add_control([
				'id'        => 'filters_alignment_line',
				'type'      => 'choose',
				'label'     => esc_html__( 'Horizontal Alignment', 'jet-smart-filters' ),
				'options'   =>[
					'flex-start'    => [
						'shortcut' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-alignleft',
					],
					'center'    => [
						'shortcut' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-aligncenter',
					],
					'flex-end'    => [
						'shortcut' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-alignright',
					],
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filters-list'] => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'filters_position' => 'row',
				],
				'attributes' => [
					'default' => [
						'value' => 'flex-start',
					]
				]
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_filters_item',
					'initialOpen' => false,
					'title'       => esc_html__( 'Items', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'group_filters_width',
				'type'      => 'range',
				'label'     => esc_html__( 'Minimal Width', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter'] => 'min-width: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 10,
						'unit' => 'px'
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
				'id'        => 'filter_position',
				'type'      => 'choose',
				'label'     => esc_html__( 'Filter Content Position', 'jet-smart-filters' ),
				'separator' => 'both',
				'options'   =>[
					'row'    => [
						'shortcut' => esc_html__( 'Line', 'jet-smart-filters' ),
						'icon'  => 'dashicons-ellipsis',
					],
					'column' => [
						'shortcut' => esc_html__( 'Column', 'jet-smart-filters' ),
						'icon'  => 'dashicons-menu-alt',
					],
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter'] => 'flex-direction: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'row',
					]
				]
			]);

			$this->controls_manager->add_control([
				'id'        => 'filter_item_content_space_between_h',
				'type'      => 'range',
				'label'     => esc_html__( 'Horizontal Space Between Content', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter'] . ' .jet-active-filter__label + .jet-active-filter__val' => 'margin-left: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 5,
						'unit' => 'px'
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
				'id'        => 'filter_item_content_space_between_v',
				'type'      => 'range',
				'label'     => esc_html__( 'Vertical Space Between Content', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter'] . ' .jet-active-filter__label + .jet-active-filter__val' => 'margin-top: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 5,
						'unit' => 'px'
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
				'id'            => 'filters_item_border',
				'type'          => 'border',
				'label'         => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}};',
				),
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id' => 'filters_item_style_tabs',
					'separator'  => 'both',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'filters_item_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_normal_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'filters_item_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_hover_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter'] . ':hover' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_hover_border_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter'] . ':hover' => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'            => 'filters_item_padding',
				'type'          => 'dimensions',
				'label'         => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'         => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			]);

			$this->controls_manager->add_control([
				'id'            => 'filters_item_label_heading',
				'type'          => 'text',
				'content'       => esc_html__( 'Label', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'         => 'filters_item_label_typography',
				'type'       => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme[ 'filter-label' ] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id' => 'filters_item_label_style_tabs',
					'separator'  => 'both',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'filters_item_label_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_label_normal_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter-label'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'filters_item_label_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_label_hover_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter'] . ':hover ' . $this->css_scheme['filter-label'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'            => 'filters_item_value_heading',
				'type'          => 'text',
				'separator'     => 'after',
				'content'       => esc_html__( 'Value', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'         => 'filters_item_value_typography',
				'type'       => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme[ 'filter-value' ] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id' => 'filters_item_value_style_tabs',
					'separator' => 'both',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'filters_item_value_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_value_normal_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter-value'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'filters_item_value_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_value_hover_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter'] . ':hover ' . $this->css_scheme['filter-value'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'            => 'filters_item_remove_heading',
				'type'          => 'text',
				'separator'    => 'after',
				'content'       => esc_html__( 'Remove', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_remove_size',
				'type'         => 'range',
				'label'        => esc_html__( 'Size', 'jet-smart-filters' ),
				'separator'    => 'after',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter-remove'] => 'font-size: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 12,
						'unit' => 'px'
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
				'id'           => 'filters_item_remove_offset_top',
				'type'         => 'range',
				'label'        => esc_html__( 'Offset Top', 'jet-smart-filters' ),
				'separator'    => 'after',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter-remove'] => 'top: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 15,
						'unit' => 'px'
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
				'id'           => 'filters_item_remove_offset_right',
				'type'         => 'range',
				'label'        => esc_html__( 'Offset Right', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter-remove'] => 'right: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 15,
						'unit' => 'px'
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

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id' => 'filters_item_remove_style_tabs',
					'separator' => 'both',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'filters_item_remove_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_remove_normal_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter-remove'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_remove_normal_bg_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter-remove'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'filters_item_remove_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_remove_hover_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter'] . ':hover ' . $this->css_scheme['filter-remove'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_remove_hover_bg_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter'] . ':hover ' . $this->css_scheme['filter-remove'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_remove_hover_border_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter'] . ':hover ' . $this->css_scheme['filter-remove'] => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'filters_bfilters_item_remove_borderorder',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter-remove'] => 'border-style:{{STYLE}};border-width:{{WIDTH}};border-radius:{{RADIUS}};border-color:{{COLOR}};',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'filters_item_remove_padding',
				'type'       => 'dimensions',
				'separator'  => 'before',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['filter-remove'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			]);

			$this->controls_manager->end_section();
		}

		/**
		 * Return callback
		 */
		public function render_callback( $settings = array() ) {

			jet_smart_filters()->set_filters_used();

			if ( empty( $settings['content_provider'] ) || $settings['content_provider'] === 'not-selected' ) {
				return $this->is_editor() ? __( 'Please select a provider', 'jet-smart-filters' ) : false;
			}

			$base_class           = 'jet-smart-filters-' . $this->get_name();
			$provider             = $settings['content_provider'];
			$query_id             = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
			$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );

			ob_start();

			printf(
				'<div class="%1$s jet-active-filters jet-filter" data-is-block="jet-smart-filters/%2$s" data-label="%3$s" data-content-provider="%4$s" data-apply-type="%5$s" data-query-id="%6$s">',
				$base_class,
				$this->get_name(),
				$settings['filters_label'],
				$provider,
				$settings['apply_type'],
				$query_id,
				$additional_providers,
			);

			if ( $this->is_editor() ) {
				$active_filters_type = jet_smart_filters()->filter_types->get_filter_types( 'active-filters' );
				$active_filters_type->render_filters_sample( $settings );
			}

			echo '</div>';

			$filter_layout = ob_get_clean();

			return $filter_layout;
		}
	}
}
