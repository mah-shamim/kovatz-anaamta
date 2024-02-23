<?php
/**
 * Range Filter
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Block_Range' ) ) {
	/**
	 * Define Jet_Smart_Filters_Block_Range class
	 */
	class Jet_Smart_Filters_Block_Range extends Jet_Smart_Filters_Block_Base {
		/**
		 * Returns block name
		 */
		public function get_name() {
			return 'range';
		}

		public function set_css_scheme() {

			$this->css_scheme = apply_filters(
				'jet-smart-filters/widgets/range/css-scheme',
				[
					'slider-track'     => '.jet-range__slider .jet-range__slider__track',
					'slider-range'     => '.jet-range__slider .jet-range__slider__track__range',
					'slider-input'     => '.jet-range__slider .jet-range__slider__input',
					'range-values'     => '.jet-range__slider .jet-range__values',
					'inputs'           => '.jet-range__slider .jet-range__inputs',
					'inputs-container' => '.jet-range__slider .jet-range__inputs__container',
					'input-group'      => '.jet-range__slider .jet-range__inputs__group',
					'input'            => '.jet-range__slider .jet-range__inputs__group input',
					'input-text'       => '.jet-range__slider .jet-range__inputs__group__text',

					'slider'       => '.jet-range__slider',
					'range'        => '.ui-slider-range',
					'range-point'  => '.ui-slider-handle',
					'filter'                => '.jet-filter',
					'filters-label'         => '.jet-filter-label',
					'apply-filters'         => '.apply-filters',
					'apply-filters-button'  => '.apply-filters__button',
				]
			);
		}

		public function add_style_manager_options() {

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_slider_track_style',
					'initialOpen' => false,
					'title'       => esc_html__( 'Slider', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'slider_track_stroke',
				'type'      => 'range',
				'label'     => esc_html__( 'Stroke', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['slider-track'] => 'height: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 4,
						'unit' => 'px'
					]
				],
				'units' => [
					[
						'value' => 'px',
						'intervals' => [
							'step' => 1,
							'min'  => 0,
							'max'  => 20,
						]
					],
				],
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id' => 'item_style_tabs',
					'separator'  => 'both',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'slider_track_default_styles',
					'title' => esc_html__( 'Default', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'slider_track_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['slider-track'] => 'background-color: {{VALUE}}',
				],
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'slider_range_styles',
					'title' => esc_html__( 'Range', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'slider_range_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['slider-range'] => 'color: {{VALUE}}',
				],
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'            => 'slider_track_border',
				'type'          => 'border',
				'label'         => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => [
					'{{WRAPPER}} ' . $this->css_scheme['slider-track'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'range_points_width',
				'type'         => 'range',
				'label'        => esc_html__( 'Points Width', 'jet-smart-filters' ),
				'separator'    => 'before',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-webkit-slider-thumb' => 'width: {{VALUE}}{{UNIT}};',
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-moz-range-thumb'     => 'width: {{VALUE}}{{UNIT}};',
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-ms-thumb'            => 'width: {{VALUE}}{{UNIT}};'
				],
				'attributes' => [
					'default' => [
						'value' => 15,
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

			$this->controls_manager->add_control([
				'id'           => 'range_points_height',
				'type'         => 'range',
				'label'        => esc_html__( 'Points Height', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['slider-input']                            => 'height: {{VALUE}}{{UNIT}};',
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-webkit-slider-thumb' => 'height: {{VALUE}}{{UNIT}};',
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-moz-range-thumb'     => 'height: {{VALUE}}{{UNIT}};',
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-ms-thumb'            => 'height: {{VALUE}}{{UNIT}};'
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
							'max'  => 30,
						]
					],
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'range_points_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-webkit-slider-thumb' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-moz-range-thumb'     => 'background-color: {{VALUE}}',
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-ms-thumb'            => 'background-color: {{VALUE}}'
				]
			]);

			$this->controls_manager->add_control([
				'id'            => 'range_points_border',
				'type'          => 'border',
				'label'         => esc_html__( 'Border', 'jet-smart-filters' ),
				'separator'     => 'before',
				'css_selector'  => [
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-webkit-slider-thumb' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-moz-range-thumb'     => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
					'{{WRAPPER}} ' . $this->css_scheme['slider-input'] . '::-ms-thumb'            => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				],
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_values_style',
					'initialOpen' => false,
					'title'       => esc_html__( 'Values', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'values_typography',
				'type'       => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['range-values'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'values_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['range-values'] => 'color: {{VALUE}}',
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'values_margin',
				'type'         => 'dimensions',
				'label'        => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'        => [ 'px', '%' ],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['range-values'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				],
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'        => 'values_alignment',
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
					'{{WRAPPER}} ' . $this->css_scheme['range-values'] => 'text-align: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'left',
					]
				]
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_inputs_style',
					'initialOpen' => false,
					'title'       => esc_html__( 'Inputs', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'inputs_width',
				'type'         => 'range',
				'label'        => esc_html__( 'Container Width', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['inputs-container'] => 'max-width: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 1000,
						'unit' => 'px'
					]
				],
				'units' => [
					[
						'value' => 'px',
						'intervals' => [
							'step' => 1,
							'min'  => 100,
							'max'  => 1000,
						]
					],
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'inputs_offset',
				'type'         => 'range',
				'label'        => esc_html__( 'Vertical Space Between', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['input-group'] . ':first-child' => 'margin-right: calc({{VALUE}}{{UNIT}}/2);',
					'{{WRAPPER}} ' . $this->css_scheme['input-group'] . ':last-child'  => 'margin-left: calc({{VALUE}}{{UNIT}}/2);',
					'.rtl {{WRAPPER}} ' . $this->css_scheme['input-group'] . ':first-child' => 'margin-left: calc({{VALUE}}{{UNIT}}/2); margin-right: 0;',
					'.rtl {{WRAPPER}} ' . $this->css_scheme['input-group'] . ':last-child'  => 'margin-right: calc({{VALUE}}{{UNIT}}/2); margin-left: 0;',
				],
				'attributes' => [
					'default' => [
						'value' => 20,
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
				'id'           => 'inputs_margin',
				'type'         => 'dimensions',
				'label'        => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'        => [ 'px', '%' ],
				'separator'    => 'before',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['inputs'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				]
			]);

			$this->controls_manager->add_control([
				'id'        => 'inputs_alignment',
				'type'      => 'choose',
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'separator' => 'before',
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
				'return_value' => [
					'left'   => 'margin-left: 0; margin-right: auto;',
					'center' => 'margin-left: auto; margin-right: auto;',
					'right'  => 'margin-left: auto; margin-right: 0;',
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['inputs-container'] => '{{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' =>  'center',
					]
				]
			]);

			$this->controls_manager->add_control([
				'id'      => 'input_heading',
				'type'    => 'text',
				'content' => esc_html__( 'Input', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'           => 'input_typography',
				'type'         => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['input-group'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'input_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['input-group'] => 'color: {{VALUE}};',
				]
			]);

			$this->controls_manager->add_control([
				'id'           => 'input_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['input-group'] => 'background-color: {{VALUE}};',
				]
			]);

			$this->controls_manager->add_control([
				'id'           => 'input_border',
				'type'         => 'border',
				'separator'    => 'before',
				'label'        => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['input-group'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'input_padding',
				'type'         => 'dimensions',
				'label'        => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'        => [ 'px', '%' ],
				'separator'    => 'before',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['input'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				]
			]);

			$this->controls_manager->add_control([
				'id'      => 'input_text_heading',
				'type'    => 'text',
				'content' => esc_html__( 'Prefix/Suffix', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'           => 'input_text_typography',
				'type'         => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['input-text'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'input_text_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['input-text'] => 'color: {{VALUE}};',
				]
			]);

			$this->controls_manager->add_control([
				'id'           => 'input_text_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['input-text'] => 'background-color: {{VALUE}};',
				]
			]);

			$this->controls_manager->add_control([
				'id'           => 'input_text_padding',
				'type'         => 'dimensions',
				'label'        => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'        => [ 'px', '%' ],
				'separator'    => 'before',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['input-text'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				]
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
				]
			]);

			$this->controls_manager->add_control([
				'id'           => 'label_color',
				'type'         => 'color-picker',
				'separator'    => 'before',
				'label'        => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}}  ' . $this->css_scheme['filters-label'] => 'color: {{VALUE}}',
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'label_border',
				'type'         => 'border',
				'label'        => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filters-label'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				],
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'           => 'label_padding',
				'type'         => 'dimensions',
				'label'        => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'        => [ 'px', '%' ],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filters-label'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				],
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'           => 'label_margin',
				'type'         => 'dimensions',
				'label'        => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'        => [ 'px', '%' ],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filters-label'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				],
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
				'id'           => 'filter_apply_button_normal_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] => 'color: {{VALUE}}',
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'filter_apply_button_normal_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'separator'    => 'before',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] => 'background-color: {{VALUE}}',
				],
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
				'id'           => 'filter_apply_button_hover_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] . ':hover' => 'color: {{VALUE}}',
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'filter_apply_button_hover_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'separator'    => 'before',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] . ':hover' => 'background-color: {{VALUE}}',
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'filter_apply_button_hover_border_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'separator'    => 'before',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] . ':hover' => 'border-color: {{VALUE}}',
				],
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'           => 'filter_apply_button_border',
				'type'         => 'border',
				'label'        => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'filter_apply_button_padding',
				'type'         => 'dimensions',
				'label'        => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'        => [ 'px', '%' ],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				],
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'           => 'filter_apply_button_margin',
				'type'         => 'dimensions',
				'label'        => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'        => [ 'px', '%' ],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['apply-filters-button'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				],
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
				]
			]);

			$this->controls_manager->end_section();
		}
	}
}
