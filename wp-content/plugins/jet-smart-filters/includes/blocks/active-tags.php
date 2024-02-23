<?php
/**
 * Active_Tags Filter
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Block_Active_Tags' ) ) {
	/**
	 * Define Jet_Smart_Filters_Block_Active_Tags class
	 */
	class Jet_Smart_Filters_Block_Active_Tags extends Jet_Smart_Filters_Block_Base {
		/**
		 * Returns block name
		 */
		public function get_name() {

			return 'active-tags';
		}

		public function set_css_scheme() {
			$this->css_scheme = apply_filters(
				'jet-smart-filters/widgets/active-tags/css-scheme',
				[
					'tags'       => '.jet-smart-filters-active-tags',
					'tags-list'  => '.jet-active-tags__list',
					'tags-title' => '.jet-active-tags__title',
					'tag'        => '.jet-active-tag',
					'tag-label'  => '.jet-active-tag__label',
					'tag-value'  => '.jet-active-tag__val',
					'tag-remove' => '.jet-active-tag__remove',
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
					'{{WRAPPER}} ' . $this->css_scheme[ 'tags-title' ] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'       => 'filters_title_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['tags-title'] => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'filters_title_margin',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['tags-title'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
				'separator' => 'after',
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
					'{{WRAPPER}} ' . $this->css_scheme['tags']      => 'flex-direction: {{VALUE}};',
					'{{WRAPPER}} ' . $this->css_scheme['tags-list'] => 'flex-direction: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'row',
					]
				]
			]);

			$this->controls_manager->add_control([
				'id'        => 'filters_space_between_horizontal',
				'type'      => 'range',
				'label'     => esc_html__( 'Horizontal Offset', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['tag'] => 'margin-right: {{VALUE}}{{UNIT}};',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag'] => 'margin-bottom: {{VALUE}}{{UNIT}};',
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
					'{{WRAPPER}} ' . $this->css_scheme['tags-list'] => 'background-color: {{VALUE}};',
				),
				'separator' => 'after',
			]);

			$this->controls_manager->add_control([
				'id'         => 'filters_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['tags-list'] => 'border-style:{{STYLE}};border-width:{{WIDTH}};border-radius:{{RADIUS}};border-color:{{COLOR}};',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'filters_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['tags-list'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
					'{{WRAPPER}} ' . $this->css_scheme['tags-list'] => 'align-items: {{VALUE}};',
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
					'{{WRAPPER}} ' . $this->css_scheme['tags-list'] => 'justify-content: {{VALUE}};',
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
				'separator' => 'before',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['tag'] => 'min-width: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => [
							'value' => 10,
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
					'{{WRAPPER}} ' . $this->css_scheme['tag'] => 'flex-direction: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'row',
					]
				]
			]);

			$this->controls_manager->add_control([
				'id'            => 'filters_item_border',
				'type'          => 'border',
				'label'         => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['tag'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}};',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag'] => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag'] . ':hover' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_hover_border_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['tag'] . ':hover' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			]);

			$this->controls_manager->add_control([
				'id'            => 'tags_clear_item_heading',
				'type'          => 'text',
				'content'       => esc_html__( 'Clear Item', 'jet-smart-filters' ),
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id' => 'tag_clear_style_tabs',
					'separator'  => 'none',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'tag_clear_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'tag_clear_normal_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['tag'] . '--clear ' . $this->css_scheme['tag-value'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'           => 'tag_clear_normal_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['tag'] . '--clear ' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'tag_clear_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'tag_clear_hover_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['tag'] . '--clear' . ':hover ' . $this->css_scheme['tag-value'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'           => 'tag_clear_hover_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['tag'] . '--clear' . ':hover' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'            => 'filters_item_label_heading',
				'type'          => 'text',
				'content'       => esc_html__( 'Label', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'         => 'filters_item_label_typography',
				'type'       => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme[ 'tag-label' ] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag-label'] => 'color: {{VALUE}}',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag'] . ':hover ' . $this->css_scheme['tag-label'] => 'color: {{VALUE}}',
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
					'{{WRAPPER}} ' . $this->css_scheme[ 'tag-value' ] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag-value'] => 'color: {{VALUE}}',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag'] . ':hover ' . $this->css_scheme['tag-value'] => 'color: {{VALUE}}',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag-remove'] => 'font-size: {{VALUE}}{{UNIT}};',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag-remove'] => 'top: {{VALUE}}{{UNIT}};',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag-remove'] => 'right: {{VALUE}}{{UNIT}};',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag-remove'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_remove_normal_bg_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['tag-remove'] => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} ' . $this->css_scheme['tag'] . ':hover ' . $this->css_scheme['tag-remove'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_remove_hover_bg_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['tag'] . ':hover ' . $this->css_scheme['tag-remove'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'           => 'filters_item_remove_hover_border_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['tag'] . ':hover ' . $this->css_scheme['tag-remove'] => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'filters_bfilters_item_remove_borderorder',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['tag-remove'] => 'border-style:{{STYLE}};border-width:{{WIDTH}};border-radius:{{RADIUS}};border-color:{{COLOR}};',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'filters_item_remove_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['tag-remove'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
			$clear_item           = isset( $settings['clear_item'] ) ? filter_var( $settings['clear_item'], FILTER_VALIDATE_BOOLEAN ) : false;
			$clear_label          = ! empty( $settings['clear_item_label'] ) && $clear_item ? $settings['clear_item_label'] : false;

			ob_start();

			printf(
				'<div class="%1$s jet-active-tags jet-filter" data-is-block="jet-smart-filters/%2$s" data-label="%3$s" data-clear-item-label="%4$s" data-content-provider="%5$s" data-apply-type="%6$s" data-query-id="%7$s">',
				$base_class,
				$this->get_name(),
				$settings['tags_label'],
				$clear_label ? $clear_label : false,
				$provider,
				$settings['apply_type'],
				$query_id,
				$additional_providers
			);

			if ( $this->is_editor() ) {
				$active_filters_type = jet_smart_filters()->filter_types->get_filter_types( 'active-filters' );
				$active_filters_type->render_tags_sample( $settings );
			}

			echo '</div>';

			$filter_layout = ob_get_clean();

			return $filter_layout;
		}
	}
}
