<?php
/**
 * Date Range Filter
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Block_Date_Range' ) ) {
	/**
	 * Define Jet_Smart_Filters_Block_Date_Range class
	 */
	class Jet_Smart_Filters_Block_Date_Range extends Jet_Smart_Filters_Block_Base {
		/**
		 * Returns block name
		 *
		 * @return string
		 */
		public function get_name() {
			return 'date-range';
		}

		public function set_css_scheme() {

			$this->css_scheme =  apply_filters(
				'jet-smart-filters/widgets/date-range/css-scheme',
				[
					'filter-wrapper'            => '.jet-smart-filters-date-range',
					'filter-content'            => '.jet-smart-filters-date-range .jet-date-range',
					'filters-label'             => '.jet-filter-label',
					'inputs'                    => '.jet-date-range__inputs',
					'input'                     => '.jet-date-range__inputs > input',
					'apply-filters-button'      => '.jet-date-range__submit',
					'apply-filters-button-icon' => '.jet-date-range__submit > i',
					'calendar-wrapper'          => '.ui-datepicker',
					'calendar'                  => '.ui-datepicker-calendar',
					'calendar-header'           => '.ui-datepicker-header',
					'calendar-prev-button'      => '.ui-datepicker-prev',
					'calendar-next-button'      => '.ui-datepicker-next',
					'calendar-title'            => '.ui-datepicker-title',
					'calendar-body-header'      => '.ui-datepicker-calendar thead',
					'calendar-body-content'     => '.ui-datepicker-calendar tbody',
				]
			);
		}

		public function add_style_manager_options() {

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_date_range_content_style',
					'initialOpen' => false,
					'title'       => esc_html__( 'Content', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'content_position',
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
				'return_value' => [
					'inline-block' => 'flex-direction:row;',
					'block'        => 'flex-direction:column;',
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter-content'] => 'display:flex; {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'block',
					]
				],
			]);

			$this->controls_manager->add_control([
				'id'        => 'content_line_horizontal_alignment',
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
					'space-between'    => [
						'shortcut' => esc_html__( 'Stretch', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-justify',
					],
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter-content'] => 'justify-content: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'flex-start',
					]
				],
				'condition' => [
					'content_position' => 'inline-block',
				],
			]);

			$this->controls_manager->add_control([
				'id'        => 'content_block_vertical_alignment',
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
					'stretch'    => [
						'shortcut' => esc_html__( 'Stretch', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-justify',
					],
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filter-content'] => 'align-items: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'flex-start',
					]
				],
				'condition' => [
					'content_position' => 'block',
				],
			]);

			$this->controls_manager->add_control([
				'id'            => 'content_input',
				'type'          => 'text',
				'separator'     => 'both',
				'content'       => esc_html__( 'Input', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'        => 'content_date_range_input_width',
				'type'      => 'range',
				'label'     => esc_html__( 'Inputs Width', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['inputs'] => 'max-width: {{VALUE}}{{UNIT}}; width:100%;',
				],
				'attributes' => [
					'default' => [
						'value' =>[
							'value' => 15,
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
				'id'         => 'input_typography',
				'type'       => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['input'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'       => 'input_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['input'] => 'color: {{VALUE}};',
					'{{WRAPPER}} ' . $this->css_scheme['input'] . '::placeholder' => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			]);

			$this->controls_manager->add_control([
				'id'       => 'input_background',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['input'] => 'background-color: {{VALUE}};',
				),
				'separator' => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'input_border',
				'type'       => 'border',
				'separator'  => 'before',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['input'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'input_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['input'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'input_margin',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['input'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_calendar_styles',
					'initialOpen' => false,
					'title'       => esc_html__( 'Calendar', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'calendar_offset_top',
				'type'      => 'range',
				'label'     => esc_html__( 'Vertical Offset', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'.jet-smart-filters-datepicker-{{ID}}' . $this->css_scheme['calendar-wrapper'] => 'margin-top: {{VALUE}}{{UNIT}};',
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
							'min'  => -300,
							'max'  => 300,
						]
					],
				],
			]);

			$this->controls_manager->add_responsive_control([
				'id'           => 'calendar_offset_left',
				'type'         => 'range',
				'label'        => esc_html__( 'Horizontal Offset', 'jet-smart-filters' ),
				'css_selector' => [
					'.jet-smart-filters-datepicker-{{ID}}' . $this->css_scheme['calendar-wrapper'] => 'margin-left: {{VALUE}}{{UNIT}};',
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
							'min'  => -300,
							'max'  => 300,
						]
					],
				],
			]);

			$this->controls_manager->add_control([
				'id'        => 'calendar_width',
				'type'      => 'range',
				'label'     => esc_html__( 'Calendar Width', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'.jet-smart-filters-datepicker-{{ID}}' . $this->css_scheme['calendar-wrapper'] => 'width: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 300,
						'unit' => 'px'
					]
				],
				'units' => [
					[
						'value' => 'px',
						'intervals' => [
							'step' => 1,
							'min'  => 0,
							'max'  => 1000,
						]
					],
				],
			]);

			$this->controls_manager->add_control([
				'id'       => 'calendar_body_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}}' . $this->css_scheme['calendar-wrapper'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_body_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}}' . $this->css_scheme['calendar-wrapper'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_body_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}}' . $this->css_scheme['calendar-wrapper'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_calendar_title',
					'initialOpen' => false,
					'title'       => esc_html__( 'Calendar Caption', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_title_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-title'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_title_typography',
				'type'       => 'typography',
				'css_selector' => [
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-title'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_calendar_prev_next',
					'initialOpen' => false,
					'title'       => esc_html__( 'Calendar Navigation Arrows', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'calendar_prev_next_size',
				'type'      => 'range',
				'label'     => esc_html__( 'Size', 'jet-smart-filters' ),
				'css_selector' => [
					'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $this->css_scheme['calendar-prev-button'] . '> span' => 'border-width: calc({{VALUE}}{{UNIT}} / 2) calc({{VALUE}}{{UNIT}} / 2) calc({{VALUE}}{{UNIT}} / 2) 0;',
					'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $this->css_scheme['calendar-next-button'] . '> span' => 'border-width: calc({{VALUE}}{{UNIT}} / 2) 0 calc({{VALUE}}{{UNIT}} / 2) calc({{VALUE}}{{UNIT}} / 2);',
				],
				'separator'    => 'after',
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
				'id'       => 'calendar_prev_next_normal_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'separator'    => 'after',
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $this->css_scheme['calendar-next-button'] . '> span' => 'border-left-color: {{VALUE}}',
					'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $this->css_scheme['calendar-prev-button'] . '> span' => 'border-right-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'calendar_prev_next_hover_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Hover Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $this->css_scheme['calendar-next-button'] . ':hover > span' => 'border-left-color: {{VALUE}}',
					'.jet-smart-filters-datepicker-{{ID}}.ui-datepicker ' . $this->css_scheme['calendar-prev-button'] . ':hover > span' => 'border-right-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_calendar_header',
					'initialOpen' => false,
					'title'       => esc_html__( 'Calendar Week Days', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Header Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-header'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
				'disable_radius' => true,
				'separator'    => 'after',
			]);

			$this->controls_manager->add_control([
				'id'       => 'calendar_header_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Header Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-header'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'            => 'calendar_header_cells_heading',
				'type'          => 'text',
				'content'       => esc_html__( 'Day', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_cells_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-header'] . ' > tr > th' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_cells_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-header'] . ' > tr > th' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
				'disable_radius' => true,
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'            => 'calendar_header_cells_content',
				'type'          => 'text',
				'separator'     => 'both',
				'content'       => esc_html__( 'Day Content', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'           => 'calendar_header_cells_content_typography',
				'type'         => 'typography',
				'separator'     => 'after',
				'css_selector' => [
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-header'] . ' > tr > th > span' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'       => 'calendar_header_cells_content_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'separator'     => 'after',
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-header'] . ' > tr > th > span' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'calendar_header_cells_content_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-header'] . ' > tr > th > span' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_cells_content_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-header'] . ' > tr > th > span' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_cells_content_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-header'] . ' > tr > th > span' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_calendar_content',
					'initialOpen' => false,
					'title'       => esc_html__( 'Calendar Days', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
				'separator'    => 'after',
			]);

			$this->controls_manager->add_control([
				'id'       => 'calendar_content_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Body Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'            => 'calendar_content_cells_heading',
				'type'          => 'text',
				'separator'     => 'both',
				'content'       => esc_html__( 'Day', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td' =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_first_border_width',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'First Item Border Width', 'jet-smart-filters' ),
				'units'      => array( 'px' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td:first-child' => 'border-width: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_last_border_width',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Last Item Border Width', 'jet-smart-filters' ),
				'units'      => array( 'px' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td:last-child' => 'border-width: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'            => 'calendar_content_cells_content',
				'type'          => 'text',
				'separator'     => 'both',
				'content'       => esc_html__( 'Day Content', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_typography',
				'type'       => 'typography',
				'css_selector' => [
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > span,' . '.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > a' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id' => 'calendar_content_cells_content_style_tabs',
					'separator'  => 'both',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_content_cells_content_default_styles',
					'title' => esc_html__( 'Default', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_default_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > span' => 'color: {{VALUE}}',
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > a'    => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_default_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > span' => 'background-color: {{VALUE}}',
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > a'    => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_content_cells_content_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_hover_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > a:hover' => 'color: {{VALUE}}',
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a:hover' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_hover_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > a:hover' => 'background-color: {{VALUE}}',
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a:hover' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_hover_border_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > a:hover' => 'border-color: {{VALUE}}',
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a:hover' => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();


			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_content_cells_content_active_styles',
					'title' => esc_html__( 'Active', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_active_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Active Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > a.ui-state-active' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_active_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Active Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > a.ui-state-active' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_active_border_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Active Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > a.ui-state-active' => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_content_cells_content_current_styles',
					'title' => esc_html__( 'Current', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_current_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Current Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a'    => 'color: {{VALUE}}',
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > span' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_current_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Current Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a'    => 'background-color: {{VALUE}}',
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > span' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_current_border_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Current Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > a'    => 'border-color: {{VALUE}}',
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td.ui-datepicker-today > span' => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();
			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > span,' . '.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > a',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_content_cells_content_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px' ),
				'css_selector'  => array(
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.jet-smart-filters-datepicker-{{ID}} ' . $this->css_scheme['calendar-body-content'] . ' > tr > td > a'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'    => 'before',
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
				'id'       => 'label_color',
				'type'     => 'color-picker',
				'separator'    => 'before',
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

			$this->controls_manager->add_control([
				'id'        => 'label_alignment',
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
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['filters-label'] => 'text-align: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' =>  'left',
					]
				]
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'button_style',
					'initialOpen' => false,
					'title'       => esc_html__( 'Button', 'jet-smart-filters' ),
					'condition' => [
						'hide_apply_button' => false,
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
				],
			]);

			$this->controls_manager->end_section();
		}

		/**
		 * Return callback
		 */
		public function render_callback( $settings = array() ) {
			
			jet_smart_filters()->set_filters_used();

			if ( empty( $settings['filter_id'] ) ) {
				return $this->is_editor() ? __( 'Please select a filter', 'jet-smart-filters' ) : false;
			}

			if ( empty( $settings['content_provider'] ) || $settings['content_provider'] === 'not-selected' ) {
				return $this->is_editor() ? __( 'Please select a provider', 'jet-smart-filters' ) : false;
			}

			if ( 'ajax' === $settings['apply_type'] ) {
				$apply_type = 'ajax-reload';
			} else {
				$apply_type = $settings['apply_type'];
			}

			$filter_id            = apply_filters( 'jet-smart-filters/render_filter_template/filter_id', $settings['filter_id'] );
			$base_class           = 'jet-smart-filters-' . $this->get_name();
			$provider             = $settings['content_provider'];
			$query_id             = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
			$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );
			$show_label           = $settings['show_label'];
			$hide_button          = $settings['hide_apply_button'];
			$apply_button_text    = $settings['apply_button_text'];
			$filter_template_args = array(
				'filter_id'        => $filter_id,
				'content_provider' => $provider,
				'query_id'         => $query_id,
				'apply_type'       => $apply_type,
				'hide_button'      => $hide_button,
				'button_text'      => $apply_button_text,
			);

			if ( isset( $settings['blockID'] ) ) {
				$filter_template_args['block_id'] = $settings['blockID'];
			}

			jet_smart_filters()->admin_bar_register_item( $filter_id );

			ob_start();

			printf(
				'<div class="%1$s jet-filter" data-is-block="jet-smart-filters/%2$s">',
				$base_class,
				$this->get_name()
			);

			include jet_smart_filters()->get_template( 'common/filter-label.php' );

			jet_smart_filters()->filter_types->render_filter_template( $this->get_name(), array(
				'block_id'             => isset( $settings['blockID'] ) ? $settings['blockID'] : false,
				'filter_id'            => $filter_id,
				'content_provider'     => $provider,
				'query_id'             => $query_id,
				'additional_providers' => $additional_providers,
				'apply_type'           => $apply_type,
				'hide_button'          => $hide_button,
				'button_text'          => $apply_button_text,
			) );

			echo '</div>';

			$filter_layout = ob_get_clean();

			return $filter_layout;
		}
	}
}
