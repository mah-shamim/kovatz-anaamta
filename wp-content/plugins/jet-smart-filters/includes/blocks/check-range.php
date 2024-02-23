<?php
/**
 * Check Range Filter
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Block_Check_Range' ) ) {
	/**
	 * Define Jet_Smart_Filters_Block_Check_Range class
	 */
	class Jet_Smart_Filters_Block_Check_Range extends Jet_Smart_Filters_Block_Base {
		/**
		 * Returns block name
		 *
		 * @return string
		 */
		public function get_name() {

			return 'check-range';
		}

		public function set_css_scheme() {

			$this->css_scheme =  apply_filters(
			'jet-smart-filters/widgets/check-range/css-scheme',
				array(
					'item'                  => '.jet-checkboxes-list__row',
					'button'                => '.jet-checkboxes-list__button',
					'disable_button'        => '.jet-filter-row-disable .jet-checkboxes-list__button',
					'label'                 => '.jet-checkboxes-list__label',
					'checkbox'              => '.jet-checkboxes-list__decorator',
					'disable_checkbox'      => '.jet-filter-row-disable .jet-checkboxes-list__decorator',
					'checkbox-checked-icon' => '.jet-checkboxes-list__checked-icon',
					'list-item'             => '.jet-checkboxes-list__row',
					'list-wrapper'          => '.jet-checkboxes-list-wrapper',
					'list-children'         => '.jet-list-tree__children',
					'filter'               => '.jet-filter',
					'filters-label'        => '.jet-filter-label',
					'apply-filters'        => '.apply-filters',
					'apply-filters-button' => '.apply-filters__button',
					'counter'              => '.jet-filters-counter',
				)
			);
		}

		public function add_style_manager_options() {

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'items_style',
					'initialOpen' => true,
					'title'       => esc_html__( 'Items', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'filters_position',
				'type'      => 'choose',
				'label'     => esc_html__( 'Filters Position', 'jet-smart-filters' ),
				'separator' => 'after',
				'options'   =>[
					'inline-block'    => [
						'shortcut' => esc_html__( 'Line', 'jet-smart-filters' ),
						'icon'  => 'dashicons-ellipsis',
					],
					'block' => [
						'shortcut' => esc_html__( 'Column', 'jet-smart-filters' ),
						'icon'  => 'dashicons-menu-alt',
					],
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['list-item']     => 'display: {{VALUE}};',
					'{{WRAPPER}} ' . $this->css_scheme['list-children'] => 'display: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'block',
					]
				]
			]);

			$this->controls_manager->add_control([
				'id'        => 'horizontal_layout_description',
				'type'      => 'range',
				'label'     => esc_html__( 'Horizontal Offset', 'jet-smart-filters' ),
				'help'     => esc_html__( 'Horizontal Offset control works only with Line Filters Position', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['list-item']     => 'margin-right: calc({{VALUE}}{{UNIT}}/2); margin-left: calc({{VALUE}}{{UNIT}}/2);',
					'{{WRAPPER}} ' . $this->css_scheme['list-children'] => 'margin-right: calc({{VALUE}}{{UNIT}}/2); margin-left: calc({{VALUE}}{{UNIT}}/2);',
					'{{WRAPPER}} ' . $this->css_scheme['list-wrapper']  => 'margin-left: calc(-{{VALUE}}{{UNIT}}/2); margin-right: calc(-{{VALUE}}{{UNIT}}/2);',
				],
				'attributes' => [
					'default' => [
						'value' => 0,
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
				'condition' => [
					'filters_position' => 'inline-block',
				],
			]);

			$this->controls_manager->add_control([
				'id'        => 'filters_list_alignment',
				'type'      => 'choose',
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'separator' => 'after',
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
					'{{WRAPPER}} ' . $this->css_scheme['list-wrapper'] => 'text-align: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' =>  'left',
					]
				]
			]);

			$this->controls_manager->add_control([
				'id'        => 'items_space_between',
				'type'      => 'range',
				'label'     => esc_html__( 'Space Between', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['item'] . ':not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};'
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
							'max'  => 50,
						]
					],
				],
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'item_style',
					'title'       => esc_html__( 'Item', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'item_typography',
				'type'       => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['label'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'         => 'show_decorator',
				'type'       => 'toggle',
				'separator'  => 'before',
				'label'      => esc_html__( 'Show Checkbox', 'jet-smart-filters' ),
				'attributes' => [
					'default' => [
						'value' => true,
					]
				],
				'return_value' => [
					'true'  => 'inline-block',
					'false' => 'none',
				],
				'css_selector' => [
					'{{WRAPPER}} .jet-checkboxes-list__item ' . $this->css_scheme['checkbox'] => 'display: {{VALUE}};',
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
					'id'    => 'item_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'       => 'item_normal_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['button'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'item_normal_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['button'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'item_checked_styles',
					'title' => esc_html__( 'Checked', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'       => 'item_checked_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'{{WRAPPER}} .jet-checkboxes-list__input:checked ~ ' . $this->css_scheme['button'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'item_checked_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'{{WRAPPER}} .jet-checkboxes-list__input:checked ~ ' . $this->css_scheme['button'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'item_checked_border_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-checkboxes-list__input:checked ~ ' . $this->css_scheme['button'] => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'item_disable_styles',
					'title' => esc_html__( 'Disable', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'       => 'item_disable_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Disable Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'attributes' => [
					'default' => [
						'value' => ''
					],
				],
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['disable_button'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'item_disable_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Disable Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'attributes' => [
					'default' => [
						'value' => ''
					],
				],
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['disable_button'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'item_disable_border_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Disable Border Color', 'jet-smart-filters' ),
				'attributes' => [
					'default' => [
						'value' => ''
					],
				],
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['disable_button'] => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'item_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['button'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'  => 'after',
			]);

			$this->controls_manager->add_control([
				'id'         => 'item_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} .jet-checkboxes-list__item ' . $this->css_scheme['button'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_checkbox_style',
					'initialOpen' => false,
					'title'       => esc_html__( 'Checkbox', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'checkbox_size',
				'type'      => 'range',
				'label'     => esc_html__( 'Size', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'{{WRAPPER}} .jet-checkboxes-list__item ' . $this->css_scheme['checkbox'] => 'width: {{VALUE}}{{UNIT}}; height: {{VALUE}}{{UNIT}}; min-width: {{VALUE}}{{UNIT}}; min-height: {{VALUE}}{{UNIT}};',
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
				'id'           => 'checkbox_top_offset',
				'type'         => 'range',
				'label'        => esc_html__( 'Offset Top', 'jet-smart-filters' ),
				'separator'    => 'none',
				'css_selector' => [
					'{{WRAPPER}} .jet-checkboxes-list__item ' . $this->css_scheme['checkbox'] => 'margin-top: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 3,
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
				'id'        => 'checkbox_label_offset',
				'type'      => 'range',
				'label'     => esc_html__( 'Offset Right', 'jet-smart-filters' ),
				'separator'    => 'none',
				'css_selector' => [
					'{{WRAPPER}} .jet-checkboxes-list__item ' . $this->css_scheme['label'] => 'margin-left: {{VALUE}}{{UNIT}};',
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
							'max'  => 30,
						]
					],
				],
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id' => 'checkbox_style_tabs',
					'separator'  => 'both',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'checkbox_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'       => 'checkbox_normal_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-checkboxes-list__item ' . $this->css_scheme['checkbox'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'checkbox_checked_styles',
					'title' => esc_html__( 'Checked', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'       => 'checkbox_checked_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'{{WRAPPER}} .jet-checkboxes-list__item .jet-checkboxes-list__input:checked ~ ' . $this->css_scheme['button'] . ' ' . $this->css_scheme['checkbox'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'checkbox_checked_border_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-checkboxes-list__item .jet-checkboxes-list__input:checked ~ ' . $this->css_scheme['button'] . ' ' . $this->css_scheme['checkbox'] => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'checkbox_disable_styles',
					'title' => esc_html__( 'Disable', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'       => 'checkbox_disable_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Disable Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['disable_checkbox'] => 'background-color: {{VALUE}}',
				),
				'attributes' => [
					'default' => [
						'value' => ''
					],
				],
			]);

			$this->controls_manager->add_control([
				'id'       => 'checkbox_disable_border_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Disable Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['disable_checkbox'] => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'checkbox_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} .jet-checkboxes-list__item ' . $this->css_scheme['checkbox'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'checkbox_icon_style',
					'initialOpen' => false,
					'title'       => esc_html__( 'Checkbox Icon', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'checked_icon_size',
				'type'      => 'range',
				'label'     => esc_html__( 'Size', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'{{WRAPPER}} .jet-checkboxes-list__item ' . $this->css_scheme['checkbox-checked-icon'] => 'font-size: {{VALUE}}{{UNIT}};',
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
				'id'       => 'checked_icon_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-checkboxes-list__item ' . $this->css_scheme['checkbox-checked-icon'] => 'color: {{VALUE}}',
				),
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
				'id'       => 'label_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
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
				]
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'counter_style',
					'title'       => esc_html__( 'Counter', 'jet-smart-filters' ),
					'condition' => [
						'show_counter' => true,
						'apply_indexer' => true,
					],
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'counter_typography',
				'type'       => 'typography',
				'separator'    => 'before',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['counter'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'       => 'counter_color',
				'type'     => 'color-picker',
				'separator'    => 'before',
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['counter'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'counter_background_color',
				'type'     => 'color-picker',
				'separator'    => 'before',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['counter'] => 'background-color: {{VALUE}}',
				),
				'attributes' => [
					'default' => [
						'value' => ''
					],
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'counter_padding',
				'type'         => 'dimensions',
				'label'        => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'        => array( 'px', '%' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['counter'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'counter_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['counter'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->end_section();

			// Include Additional Filter Settings Style
			include jet_smart_filters()->plugin_path( 'includes/blocks/common-controls/additional-filter-style.php' );
		}
	}
}
