<?php
/**
 * Select Filter
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Block_Select' ) ) {
	/**
	 * Define Jet_Smart_Filters_Block_Select class
	 */
	class Jet_Smart_Filters_Block_Select extends Jet_Smart_Filters_Block_Base {
		/**
		 * Returns block name
		 */
		public function get_name() {

			return 'select';
		}

		public function set_css_scheme() {

			$this->css_scheme = apply_filters(
				'jet-smart-filters/widgets/select/css-scheme',
				[
					'filter'               => '.jet-select',
					'select'               => '.jet-select__control',
					'filters-label'        => '.jet-filter-label',
					'apply-filters'        => '.apply-filters',
					'apply-filters-button' => '.apply-filters__button',
				]
			);
		}

		public function add_style_manager_options() {

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_content_style',
					'initialOpen' => true,
					'title'       => esc_html__( 'Content', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'content_position',
				'type'      => 'choose',
				'label'     => esc_html__( 'Position', 'jet-smart-filters' ),
				'separator' => 'after',
				'options'   =>[
					'line'    => [
						'shortcut' => esc_html__( 'Line', 'jet-smart-filters' ),
						'icon'  => 'dashicons-ellipsis',
					],
					'column' => [
						'shortcut' => esc_html__( 'Column', 'jet-smart-filters' ),
						'icon'  => 'dashicons-menu-alt',
					],
				],
				'return_value' => [
					'line'   => 'display:flex;',
					'column' => 'display:block;',
				],
				'css_selector' => [
					'{{WRAPPER}} .jet-smart-filters-select.jet-filter' => '{{VALUE}}',
					'{{WRAPPER}} .jet-smart-filters-hierarchy.jet-filter ' . $this->css_scheme['filter'] => '{{VALUE}}',
				],
				'attributes' => [
					'default' => [
						'value' => 'column',
					]
				],
			]);

			$this->controls_manager->add_control([
				'id'        => 'select_width',
				'type'      => 'range',
				'label'     => esc_html__( 'Select Width', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} .jet-smart-filters-select.jet-filter ' . $this->css_scheme['filter'] => 'width: {{VALUE}}{{UNIT}}',
					'{{WRAPPER}} .jet-smart-filters-hierarchy.jet-filter ' . $this->css_scheme['select'] => 'width: {{VALUE}}{{UNIT}}',
				],
				'attributes' => [
					'default' => [
						'value' => [
							'value' => 50,
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
				],
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_hierarchical_style',
					'initialOpen' => true,
					'title'       => esc_html__( 'Hierarchical', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'hierarchical_position',
				'type'      => 'choose',
				'label'     => esc_html__( 'Hierarchical Position', 'jet-smart-filters' ),
				'separator' => 'after',
				'options'   =>[
					'line'    => [
						'shortcut' => esc_html__( 'Line', 'jet-smart-filters' ),
						'icon'  => 'dashicons-ellipsis',
					],
					'column' => [
						'shortcut' => esc_html__( 'Column', 'jet-smart-filters' ),
						'icon'  => 'dashicons-menu-alt',
					],
				],
				'return_value' => [
					'line'   => 'display:flex;',
					'column' => 'display:block;',
				],
				'css_selector' => [
					'{{WRAPPER}} .jet-smart-filters-hierarchy.jet-filter .jet-filters-group' => '{{VALUE}}',
				],
				'attributes' => [
					'default' => [
						'value' => 'column',
					]
				],
			]);

			$this->controls_manager->add_control([
				'id'        => 'hierarchical_vertical_offset',
				'type'      => 'range',
				'label'     => esc_html__( 'Vertical Space Between', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} .jet-smart-filters-hierarchy.jet-filter .jet-filters-group ' . $this->css_scheme['filter'] . ' + ' . $this->css_scheme['filter'] => 'margin-top: {{VALUE}}{{UNIT}}',
				],
				'attributes' => [
					'default' => [
						'value' => [
							'value' => 10,
							'unit' => 'px'
						]
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
				'condition' => [
					'hierarchical_position' => 'column',
				],
			]);

			$this->controls_manager->add_control([
				'id'        => 'hierarchical_item_width',
				'type'      => 'range',
				'label'     => esc_html__( 'Item Width', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'{{WRAPPER}} .jet-smart-filters-hierarchy.jet-filter ' . $this->css_scheme['filter'] => 'width: {{VALUE}}{{UNIT}}',
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
							'min'  => 1,
							'max'  => 100,
						]
					],
				],
				'condition' => [
					'hierarchical_position' => 'line',
				],
			]);

			$this->controls_manager->add_control([
				'id'        => 'hierarchical_horizontal_offset',
				'type'      => 'range',
				'label'     => esc_html__( 'Horizontal Space Between', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} .jet-smart-filters-hierarchy.jet-filter .jet-filters-group' => 'margin-left: calc(-{{VALUE}}{{UNIT}}/2); margin-right: calc(-{{VALUE}}{{UNIT}}/2)',
					'{{WRAPPER}} .jet-smart-filters-hierarchy.jet-filter .jet-filters-group ' . $this->css_scheme['filter'] => 'margin-left: calc({{VALUE}}{{UNIT}}/2); margin-right: calc({{VALUE}}{{UNIT}}/2)',
				],
				'attributes' => [
					'default' => [
						'value' => [
							'value' => 10,
							'unit' => 'px'
						]
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
				'condition' => [
					'hierarchical_position' => 'line',
				],
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_select_style',
					'title'       => esc_html__( 'Select', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'select_typography',
				'type'       => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['select'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'       => 'select_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['select'] => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			]);

			$this->controls_manager->add_control([
				'id'       => 'select_disabled_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Disabled Text Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['select'] . ' option:disabled' => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			]);

			$this->controls_manager->add_control([
				'id'       => 'select_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['select'] => 'background-color: {{VALUE}};',
				),
				'separator' => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'select_border',
				'type'       => 'border',
				'separator'  => 'before',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['select'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'select_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['select'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'        => 'select_alignment',
				'type'      => 'choose',
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'separator'    => 'before',
				'options'   =>[
					'left'    => [
						'shortcut' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-alignleft',
					],
					'center'    => [
						'shortcut' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-aligncenter',
					],
					'right'    => [
						'shortcut' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-alignright',
					],
				],
				'return_value' => array(
					'left'   => 'margin-left: 0; margin-right: auto;',
					'center' => 'margin-left: auto; margin-right: auto;',
					'right'  => 'margin-left: auto; margin-right: 0;',
				),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['select'] => '{{VALUE}}',
				],
				'attributes' => [
					'default' => [
						'value' => 'left',
					]
				],
			]);

			$this->controls_manager->add_control([
				'id'         => 'reset_appearance',
				'type'       => 'toggle',
				'label'      => esc_html__( 'Reset Field Appearance', 'jet-smart-filters' ),
				'help'       => esc_html__( 'Check this option to reset field appearance CSS value. This will make field appearance the same for all browsers', 'jet-smart-filters' ),
				'return_value' => [
					'true'  => '-webkit-appearance: none; appearance: none;',
					'false' => '',
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['select'] => '{{VALUE}}',
				],
				'separator' => 'before',
				'attributes' => [
					'default' => [
						'value' => false,
					]
				],
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'label_style',
					'initialOpen' => false,
					'title'       => esc_html__( 'Label', 'jet-smart-filters' ),
					'condition' => [
						'show_label' => true,
					],
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'label_typography',
				'type'       => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filters-label'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'        => 'label_alignment',
				'type'      => 'choose',
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'separator'    => 'before',
				'options'   =>[
					'left'    => [
						'shortcut' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-alignleft',
					],
					'center'    => [
						'shortcut' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-aligncenter',
					],
					'right'    => [
						'shortcut' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-alignright',
					],
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filters-label']  => 'text-align: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'left',
					]
				],
			]);

			$this->controls_manager->add_control([
				'id'       => 'label_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'separator'    => 'before',
				'attributes' => [
					'default' => '',
				],
				'css_selector' => array(
					'{{WRAPPER}}  ' . $this->css_scheme['filters-label'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'label_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['filters-label'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'label_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['filters-label'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'label_margin',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['filters-label'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'button_style',
					'initialOpen' => false,
					'title'       => esc_html__( 'Button', 'jet-smart-filters' ),
					'condition' => [
						'apply_button' => true,
					]
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'filter_apply_button_typography',
				'type'       => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id' => 'filter_apply_button_style_tabs',
					'separator'  => 'both',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'filter_apply_button_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'       => 'filter_apply_button_normal_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'filter_apply_button_normal_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'separator'    => 'before',
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'filter_apply_button_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);
			$this->controls_manager->add_control([
				'id'       => 'filter_apply_button_hover_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] . ':hover' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'filter_apply_button_hover_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'separator'    => 'before',
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] . ':hover' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'filter_apply_button_hover_border_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'separator'    => 'before',
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] . ':hover' => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'filter_apply_button_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'filter_apply_button_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'filter_apply_button_margin',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'        => 'filter_apply_button_alignment',
				'type'      => 'choose',
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'separator'    => 'before',
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
					'stretch'    => [
						'shortcut' => esc_html__( 'Stretch', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-justify',
					],
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] => 'align-self: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'flex-start',
					]
				],
			]);

			$this->controls_manager->end_section();
		}
	}
}
