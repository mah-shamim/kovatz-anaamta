<?php
/**
 * Date Period Filter
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Block_Date_Period' ) ) {
	/**
	 * Define Jet_Smart_Filters_Block_Date_Period class
	 */
	class Jet_Smart_Filters_Block_Date_Period extends Jet_Smart_Filters_Block_Base {
		/**
		 * Returns block name
		 */
		public function get_name() {

			return 'date-period';
		}

		/* public function get_script_depends() {
			return 'air-datepicker';
		}

		public function get_style_depends() {
			return 'air-datepicker';
		} */

		/* public function get_editor_script_depends() {
			return array( 'jet-smart-filters', 'air-datepicker' );
		}

		public function get_editor_style_depends() {
			return array( 'jet-smart-filters', 'air-datepicker' );
		} */

		public function add_style_manager_options() {

			$css_scheme =  apply_filters(
				'jet-smart-filters/widgets/date-period/css-scheme',
				[
					'date-period-wrapper'   => '.jet-date-period__wrapper',
					'datepicker-button'     => '.jet-date-period__datepicker-button',
					'prev-button'           => '.jet-date-period__prev',
					'next-button'           => '.jet-date-period__next',
					'calendar-wrapper'      => '.ui-datepicker',
					'calendar'              => '.jet-date-period-range',
					'calendar-header'       => '.ui-datepicker-header',
					'calendar-prev-button'  => '.ui-datepicker-prev',
					'calendar-next-button'  => '.ui-datepicker-next',
					'calendar-title'        => '.ui-datepicker-title',
					'calendar-body-header'  => '.ui-datepicker-calendar thead',
					'calendar-body-content' => '.ui-datepicker-calendar tbody',
					'filters-label'         => '.jet-filter-label',
					'apply-filters-button'  => '.apply-filters__button',
				]
			);

//Datepicker Button
			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_datepicker_button_style',
					'title'       => esc_html__( 'Datepicker Button', 'jet-smart-filters' ),
					'initialOpen' => true,
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'datepicker_button_typography',
				'type'       => 'typography',
				'css_selector' => [
					'{{WRAPPER}} ' . $css_scheme['datepicker-button'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id'         => 'datepicker_button_style_tabs',
					'separator'  => 'before',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'datepicker_button_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'datepicker_button_hover_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['datepicker-button'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'datepicker_button_hover_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['datepicker-button'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'datepicker_button_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'datepicker_button_normal_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['datepicker-button'] . ':hover' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'datepicker_button_normal_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['datepicker-button'] . ':hover' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();
			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'datepicker_button_border',
				'type'       => 'border',
				'separator'  => 'before',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $css_scheme['datepicker-button'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'datepicker_button_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $css_scheme['datepicker-button'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_responsive_control([
				'id'        => 'datepicker_button_alignment',
				'type'      => 'choose',
				'label'     => esc_html__( 'Horizontal Alignment', 'jet-smart-filters' ),
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
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $css_scheme['date-period-wrapper'] => 'justify-content: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'flex-start',
					]
				],
			]);
			$this->controls_manager->end_section();

//Prev/Next Buttons
			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'next_prev_buttons_style',
					'title'       => esc_html__( 'Prev/Next Buttons', 'jet-smart-filters' ),
					'initialOpen' => false,
				]
			);

			$this->controls_manager->add_responsive_control([
				'id'        => 'next_prev_buttons_offset',
				'type'      => 'range',
				'label'     => esc_html__( 'Horizontal Offset', 'jet-smart-filters' ),
				'separator' => 'after',
				'css_selector' => [
					'{{WRAPPER}} ' . $css_scheme['prev-button'] => 'margin-right: {{VALUE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['next-button'] => 'margin-left: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => [
							'value' => 5,
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
							'max'  => 50,
						]
					],
				],
			]);

			$this->controls_manager->add_responsive_control([
				'id'        => 'next_prev_buttons_width',
				'type'      => 'range',
				'label'     => esc_html__( 'Width', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $css_scheme['prev-button'] => 'width: {{VALUE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['next-button'] => 'width: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => [
							'value' => 34,
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
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id'         => 'next_prev_buttons_style_tabs',
					'separator'  => 'before',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'next_prev_buttons_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'next_prev_buttons_normal_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['prev-button'] . ', {{WRAPPER}} ' . $css_scheme['next-button'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'next_prev_buttons_normal_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['prev-button'] . ', {{WRAPPER}} ' . $css_scheme['next-button'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'next_prev_buttons_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'next_prev_buttons_hover_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['prev-button'] . ':hover, {{WRAPPER}} ' . $css_scheme['next-button'] . ':hover' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'next_prev_buttons_hover_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['prev-button'] . ':hover, {{WRAPPER}} ' . $css_scheme['next-button'] . ':hover' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'next_prev_buttons_hover_border_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['prev-button'] . ':hover, {{WRAPPER}} ' . $css_scheme['next-button'] . ':hover' => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();
			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'next_prev_buttons_border',
				'type'       => 'border',
				'separator'  => 'before',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $css_scheme['prev-button'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
					'{{WRAPPER}} ' . $css_scheme['next-button'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->end_section();

//Calendar
			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_calendar_styles',
					'title'       => esc_html__( 'Calendar', 'jet-smart-filters' ),
					'initialOpen' => false,
				]
			);

			$this->controls_manager->add_responsive_control([
				'id'           => 'calendar_offset_top',
				'type'         => 'range',
				'label'        => esc_html__( 'Vertical Offset', 'jet-smart-filters' ),
				'css_selector' => [
					'#datepickers-container .datepicker' => 'margin-top: {{VALUE}}{{UNIT}};',
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
					'#datepickers-container .datepicker' => 'margin-left: {{VALUE}}{{UNIT}};',
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

			$this->controls_manager->add_responsive_control([
				'id'           => 'calendar_width',
				'type'         => 'range',
				'label'        => esc_html__( 'Calendar Width', 'jet-smart-filters' ),
				'separator'    => 'before',
				'css_selector' => [
					'#datepickers-container .datepicker' => 'width: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' =>  [
							'value' => 300,
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
							'max'  => 1000,
						]
					],
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'calendar_body_background_color',
				'type'         => 'color-picker',
				'separator'    => 'before',
				'label'        => esc_html__( 'Background', 'jet-smart-filters' ),
				'css_selector' => array(
					'#datepickers-container .datepicker' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_body_border',
				'type'       => 'border',
				'separator'  => 'before',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_body_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->end_section();

//Calendar Header
			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_calendar_header_styles',
					'title'       => esc_html__( 'Calendar Header', 'jet-smart-filters' ),
					'initialOpen' => false,
				]
			);

			$this->controls_manager->add_control([
				'id'           => 'calendar_header_background_color',
				'type'         => 'color-picker',
				'label'        => esc_html__( 'Background', 'jet-smart-filters' ),
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--nav' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_border',
				'type'       => 'border',
				'separator'  => 'before',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--nav' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--nav' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_margin',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--nav' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'            => 'calendar_header_caption_heading',
				'type'          => 'text',
				'separator'     => 'both',
				'content'       => esc_html__( 'Caption', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_caption_typography',
				'type'       => 'typography',
				'css_selector' => [
					'#datepickers-container .datepicker .datepicker--nav-title' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id'         => 'calendar_header_caption_style_tabs',
					'separator'  => 'before',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_header_caption_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_caption_normal_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--nav-title' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_caption_normal_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--nav-title' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_header_caption_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_caption_hover_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--nav-title:hover' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_caption_hover_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--nav-title:hover' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_caption_hover_border_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--nav-title:hover' => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();
			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_caption_border',
				'type'       => 'border',
				'separator'  => 'before',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--nav-title' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_caption_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--nav-title' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'            => 'calendar_header_prev_next_heading',
				'type'          => 'text',
				'separator'     => 'both',
				'content'       => esc_html__( 'Navigation Arrows', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_responsive_control([
				'id'           => 'calendar_header_prev_next_size',
				'label'        => esc_html__( 'Size', 'jet-smart-filters' ),
				'type'      => 'range',
				'css_selector' => [
					'#datepickers-container .datepicker .datepicker--nav-action svg' => 'width: {{VALUE}}{{UNIT}}; height: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 25,
						'unit' => 'px'
					]
				],
				'units' => [
					[
						'value' => 'px',
						'intervals' => [
							'step' => 1,
							'min'  => 10,
							'max'  => 50,
						]
					],
				],
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id'         => 'calendar_header_prev_next_style_tabs',
					'separator'  => 'before',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_header_prev_next_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_prev_next_normal_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--nav-action' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_prev_next_normal_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--nav-action' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_header_prev_next_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_prev_next_hover_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--nav-action:hover' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_prev_next_hover_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--nav-action:hover' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_prev_next_hover_border_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Border Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--nav-action:hover' => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();
			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_prev_next_border',
				'type'       => 'border',
				'separator'  => 'before',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--nav-action' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_header_prev_next_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--nav-action' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);
			$this->controls_manager->end_section();

//Calendar Cell
			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_calendar_cell',
					'title'       => esc_html__( 'Calendar Cell', 'jet-smart-filters' ),
					'initialOpen' => false,
				]
			);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id'         => 'calendar_cell_style_tabs',
				]
			);
//Default
			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_cell_default_styles',
					'title' => esc_html__( 'Default', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_cell_default_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--cell' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

//Hover
			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_cell_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_cell_hover_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--cell.-focus-' => 'color: {{VALUE}}',
					'#datepickers-container .datepicker .datepicker--cell.-in-range-.-focus-' => 'color: {{VALUE}}',
					'#datepickers-container .datepicker .datepicker--cell.-week-hover-' => 'color: {{VALUE}}',
					'#datepickers-container .datepicker .datepicker--cell.-range-from-' => 'color: {{VALUE}}',
					'#datepickers-container .datepicker .datepicker--cell.-range-to-' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_cell_hover_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Hover Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--cell.-focus-' => 'background-color: {{VALUE}}',
					'#datepickers-container .datepicker .datepicker--cell.-in-range-.-focus-' => 'background-color: {{VALUE}}',
					'#datepickers-container .datepicker .datepicker--cell.-week-hover-' => 'background-color: {{VALUE}}',
					'#datepickers-container .datepicker .datepicker--cell.-range-from-' => 'background-color: {{VALUE}}',
					'#datepickers-container .datepicker .datepicker--cell.-range-to-' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

//Active
			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_cell_active_styles',
					'title' => esc_html__( 'Active', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_cell_active_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Active Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--cell.-selected-' => 'color: {{VALUE}}',
					'#datepickers-container .datepicker .datepicker--cell.-week-selected-' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_cell_active_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Active Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--cell.-selected-' => 'background-color: {{VALUE}}',
					'#datepickers-container .datepicker .datepicker--cell.-week-selected-' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_cell_active_in_range_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'In Range Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--cell.-in-range-' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_cell_active_in_range_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'In Range Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--cell.-in-range-' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

//Current
			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'calendar_cell_current_styles',
					'title' => esc_html__( 'Current', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_cell_current_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Current Text Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--cell.-current-' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_cell_current_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Current Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--cell.-current-' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();
			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'calendar_cell_border_radius',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--cell' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			]);
			
//Days
			$this->controls_manager->add_control([
				'id'            => 'calendar_days_heading',
				'type'          => 'text',
				'separator'     => 'both',
				'content'       => esc_html__( 'Days', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_days_typography',
				'type'       => 'typography',
				'separator'  => 'after',
				'css_selector' => [
					'#datepickers-container .datepicker .datepicker--cell-day' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_days_weekend_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Weekend Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker--cell-day.-weekend-' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_days_other_month_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Other Month Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker--cell-day.-other-month-' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_days_cells_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--cell-day' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			]);

//Month
			$this->controls_manager->add_control([
				'id'            => 'calendar_month_heading',
				'type'          => 'text',
				'separator'     => 'both',
				'content'       => esc_html__( 'Month', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_months_typography',
				'type'       => 'typography',
				'separator'  => 'after',
				'css_selector' => [
					'#datepickers-container .datepicker .datepicker--cell-month' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_months_cells_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--cell-month' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			]);

//Year
			$this->controls_manager->add_control([
				'id'            => 'calendar_year_heading',
				'type'          => 'text',
				'separator'     => 'both',
				'content'       => esc_html__( 'Year', 'jet-smart-filters' ),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_year_typography',
				'type'       => 'typography',
				'separator'  => 'after',
				'css_selector' => [
					'#datepickers-container .datepicker .datepicker--cell-year' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_year_cells_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--cell-year' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			]);

			$this->controls_manager->end_section();

//Calendar Week Days
			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'section_calendar_week_days',
					'title'       => esc_html__( 'Calendar Week Days', 'jet-smart-filters' ),
					'initialOpen' => false,
				]
			);

			$this->controls_manager->add_control([
				'id'         => 'calendar_week_days_typography',
				'type'       => 'typography',
				'separator'  => 'after',
				'css_selector' => [
					'#datepickers-container .datepicker .datepicker--day-name' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_week_days_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--day-name' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_week_days_background_color',
				'type'       => 'color-picker',
				'label'      => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'#datepickers-container .datepicker .datepicker--days-names' => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_week_days_border',
				'type'       => 'border',
				'separator'  => 'after',
				'label'       => esc_html__( 'Header Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--days-names' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_week_days_cells_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'separator'  => 'after',
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--days-names' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'calendar_week_days_margin',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'#datepickers-container .datepicker .datepicker--days-names' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			]);

			$this->controls_manager->end_section();

//Label
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
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
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
					'{{WRAPPER}} ' . $css_scheme['filters-label']  => 'text-align: {{VALUE}};',
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
				'separator'    => 'before',
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}}  ' . $css_scheme['filters-label'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'         => 'label_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'label_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'         => 'label_margin',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $css_scheme['filters-label'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->end_section();

//Button
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
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
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
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'filter_apply_button_normal_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'separator'    => 'before',
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'background-color: {{VALUE}}',
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
					'id'    => 'filter_apply_button_hover_styles',
					'title' => esc_html__( 'Hover', 'jet-smart-filters' ),
				]
			);
			$this->controls_manager->add_control([
				'id'       => 'filter_apply_button_hover_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] . ':hover' => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'filter_apply_button_hover_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'separator'    => 'before',
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] . ':hover' => 'background-color: {{VALUE}}',
				),
				'attributes' => [
					'default' => [
						'value' => ''
					],
				],
			]);

			$this->controls_manager->add_control([
				'id'       => 'filter_apply_button_hover_border_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'separator'    => 'before',
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] . ':hover' => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'           => 'filter_apply_button_border',
				'type'         => 'border',
				'label'        => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] =>'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'           => 'filter_apply_button_padding',
				'type'         => 'dimensions',
				'label'        => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'        => array( 'px', '%' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'    => 'before',
			]);

			$this->controls_manager->add_control([
				'id'           => 'filter_apply_button_margin',
				'type'         => 'dimensions',
				'label'        => esc_html__( 'Margin', 'jet-smart-filters' ),
				'units'        => array( 'px', '%' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
					'{{WRAPPER}} ' . $css_scheme['apply-filters-button'] => 'align-self: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'flex-start',
					],
				]
			]);

			$this->controls_manager->end_section();
		}
	}
}
