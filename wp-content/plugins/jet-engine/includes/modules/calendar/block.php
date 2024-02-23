<?php
/**
 * Calendar block type.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Calendar block type class
 */
class Jet_Listing_Calendar_Block_Type extends \Jet_Engine_Blocks_Views_Type_Base {

	/**
	 * Returns block name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'listing-calendar';
	}

	/**
	 * Return attributes array
	 *
	 * @return array
	 */
	public function get_attributes() {

		$module = jet_engine()->modules->get_module( 'calendar' );

		return apply_filters( 'jet-engine/blocks-views/listing-calendar/attributes', array(
			'lisitng_id' => array(
				'type'    => 'string',
				'default' => '',
			),
			'group_by' => array(
				'type'    => 'string',
				'default' => 'post_date',
				'options' => $module->get_calendar_group_keys( true ),
			),
			'group_by_key' => array(
				'type'    => 'string',
				'default' => '',
			),
			'allow_multiday' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'end_date_key' => array(
				'type'    => 'string',
				'default' => '',
			),
			'use_custom_post_types' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'custom_post_types' => array(
				'type'    => 'array',
				'items'   => array( 'type' => 'string' ),
				'default' => array(),
			),
			'week_days_format' => array(
				'type'    => 'string',
				'default' => 'short',
			),
			'custom_start_from' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'start_from_month' => array(
				'type'    => 'string',
				'default' => date( 'F' ),
			),
			'start_from_year' => array(
				'type'    => 'string',
				'default' => date( 'Y' ),
			),
			'show_posts_nearby_months' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'hide_past_events' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'caption_layout' => array(
				'type'    => 'string',
				'default' => 'layout-1',
			),

			// Custom Query
			'custom_query' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'custom_query_id' => array(
				'type'    => 'string',
				'default' => '',
			),

			// Posts Query
			'posts_query' => array(
				'type'    => 'array',
				'default' => array(),
			),
			'meta_query_relation' => array(
				'type'    => 'string',
				'default' => 'AND',
			),
			'tax_query_relation' => array(
				'type'    => 'string',
				'default' => 'AND',
			),

			// Block Visibility
			'hide_widget_if' => array(
				'type'    => 'string',
				'default' => '',
			),

			// Block ID
			'_block_id' => array(
				'type'    => 'string',
				'default' => '',
			),

			// Element ID
			'_element_id' => array(
				'type'    => 'string',
				'default' => '',
			),
		) );
	}

	/**
	 * Add style block options
	 *
	 * @return void
	 */
	public function add_style_manager_options() {

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_caption_style',
				'title' => esc_html__( 'Caption', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'caption_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'caption_txt_color',
				'label' => esc_html__( 'Label Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption__name' => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'caption_txt_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'both',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption__name' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'caption_padding',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%', 'em' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'caption_margin',
				'label' => esc_html__( 'Margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%', 'em' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'caption_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector'   => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'caption_border_radius',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'caption_gap',
				'label' => esc_html__( 'Gap between caption elements', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-caption__wrap' => 'gap: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_nav_style',
				'title' => esc_html__( 'Navigation Arrows', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_width',
				'label' => esc_html__( 'Width', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'width: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'   => 'nav_height',
				'label' => esc_html__( 'Height', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'height: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_size',
				'label' => esc_html__( 'Arrow Size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'font-size: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id'        => 'tabs_nav_prev_next_style',
				'separator' => 'after',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tab_nav_prev',
				'title' => esc_html__( 'Prev Arrow (Default)', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'nav_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'disable_radius' => true,
				'css_selector'   => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'nav_border_radius',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tab_nav_next',
				'title' => esc_html__( 'Next Arrow', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'nav_border_next',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'disable_radius' => true,
				'css_selector'   => array(
					'{{WRAPPER}} .jet-calendar-nav__link.nav-link-next' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'nav_border_radius_next',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link.nav-link-next' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id' => 'tabs_nav_style',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tab_nav_normal',
				'title' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_color',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_background_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tab_nav_hover',
				'title' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_color_hover',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_background_color_hover',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'nav_border_color_hover',
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_week_style',
				'title' => esc_html__( 'Week Days', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'week_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-header__week-day' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'week_txt_color',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-header__week-day' => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'week_txt_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'both',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-header__week-day' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'week_padding',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%', 'em' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-header__week-day' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'week_border_width',
				'label' => esc_html__( 'Border Width', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%', 'em' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-header__week-day' => 'border-style: solid; border-top-width: {{TOP}}; border-bottom-width: {{BOTTOM}}; border-left-width: {{LEFT}}; border-right-width: 0;',
					'{{WRAPPER}} .jet-calendar-header__week-day:last-child' => 'border-right-width: {{RIGHT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'week_border_color',
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-header__week-day' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'week_border_color_first',
				'label' => esc_html__( 'First Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-header__week-day:first-child' => 'border-left-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'week_border_color_last',
				'label' => esc_html__( 'Last Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-header__week-day:last-child' => 'border-right-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'week_border_radius',
				'label' => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-header__week-day:first-child' => 'border-radius: {{TOP}} 0 0 {{LEFT}};',
					'{{WRAPPER}} .jet-calendar-header__week-day:last-child' => 'border-radius: 0 {{RIGHT}} {{BOTTOM}} 0;',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_day_style',
				'title' => esc_html__( 'Days', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'day_padding',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%', 'em' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-content' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'day_min_height',
				'label' => esc_html__( 'Min height', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 200,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-content' => 'min-height: {{VALUE}}{{UNIT}};',
					'{{WRAPPER}} .jet-calendar-week__day-wrap'    => 'min-height: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'day_events_gap',
				'label' => esc_html__( 'Gap between events', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 20,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-event + .jet-calendar-week__day-event' => 'margin-top: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'day_border_width',
				'label' => esc_html__( 'Border Width', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 20,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week .jet-calendar-week__day' => 'border: {{VALUE}}{{UNIT}} solid; border-right-width: 0; border-bottom-width: 0;',
					'{{WRAPPER}} .jet-calendar-week .jet-calendar-week__day:last-child' => 'border-right-width: {{VALUE}}{{UNIT}};',
					'{{WRAPPER}} tbody .jet-calendar-week:last-child .jet-calendar-week__day' => 'border-bottom-width: {{VALUE}}{{UNIT}};'
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_border_color',
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-grid .jet-calendar-week .jet-calendar-week__day' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'day_border_radius',
				'label' => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%' ),
				'css_selector' => array(
					'{{WRAPPER}} tbody .jet-calendar-week:first-child .jet-calendar-week__day:first-child' => 'border-radius: {{TOP}} 0 0 0;',
					'{{WRAPPER}} tbody .jet-calendar-week:first-child .jet-calendar-week__day:last-child' => 'border-radius: 0 {{RIGHT}} 0 0;',
					'{{WRAPPER}} tbody .jet-calendar-week:last-child .jet-calendar-week__day:first-child' => 'border-radius: 0 0 0 {{BOTTOM}};',
					'{{WRAPPER}} tbody .jet-calendar-week:last-child .jet-calendar-week__day:last-child' => 'border-radius: 0 0 {{LEFT}} 0;',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'day_label_styles',
				'content'   => esc_html__( 'Date Label', 'jet-engine' ),
				'type'      => 'text',
				'separator' => 'before',
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id' => 'tabs_day_label_style',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tabs_day_label_noraml',
				'title' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_label_color',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-date' => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_label_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-date' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'tabs_day_label_has_events',
				'title' => esc_html__( 'Has Events', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_label_color_has_events',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .has-events .jet-calendar-week__day-date' => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_label_bg_color_has_events',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .has-events .jet-calendar-week__day-date' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->add_control(
			array(
				'id'        => 'day_label_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'both',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-date' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'day_label_alignment',
				'label' => esc_html__( 'Date Box Alignment', 'jet-engine' ),
				'type'  => 'choose',
				'options' => array(
					'flex-start' => array(
						'shortcut' => esc_html__( 'Start', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignleft',
					),
					'center' => array(
						'shortcut' => esc_html__( 'Center', 'jet-engine' ),
						'icon'     => 'dashicons-editor-aligncenter',
					),
					'flex-end' => array(
						'shortcut' => esc_html__( 'End', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignright',
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-header' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'      => 'day_label_text_alignment',
				'label'   => esc_html__( 'Date Text Alignment', 'jet-engine' ),
				'type'    => 'choose',
				'options' => array(
					'flex-start' => array(
						'shortcut' => esc_html__( 'Start', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignleft',
					),
					'center' => array(
						'shortcut' => esc_html__( 'Center', 'jet-engine' ),
						'icon'     => 'dashicons-editor-aligncenter',
					),
					'flex-end' => array(
						'shortcut' => esc_html__( 'End', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignright',
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-date' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'day_label_width',
				'label'     => esc_html__( 'Width', 'jet-engine' ),
				'type'      => 'range',
				'separator' => 'before',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 100,
						),
					),
					array(
						'value'     => '%',
						'intervals' => array(
							'step' => 1,
							'min'  => 1,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-date' => 'width: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_label_height',
				'label' => esc_html__( 'Height', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-date' => 'height: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'day_label_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector'   => array(
					'{{WRAPPER}} .jet-calendar-week__day-date' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'day_label_border_radius',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-date' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'day_label_padding',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-date' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'day_label_margin',
				'label' => esc_html__( 'Margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-date' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'current_day_styles',
				'content'   => esc_html__( 'Current Day', 'jet-engine' ),
				'type'      => 'text',
				'separator' => 'before',
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'current_day_bg_color',
				'label' => esc_html__( 'Day Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day.current-day' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'current_day_label_color',
				'label' => esc_html__( 'Day Label Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day.current-day .jet-calendar-week__day-date' => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'current_day_label_bg_color',
				'label' => esc_html__( 'Day Label Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day.current-day .jet-calendar-week__day-date' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'current_day_label_border_color',
				'label' => esc_html__( 'Day Label Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day.current-day .jet-calendar-week__day-date' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'day_disabled_styles',
				'content'   => esc_html__( 'Disabled Days (not in current month)', 'jet-engine' ),
				'type'      => 'text',
				'separator' => 'before',
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_opacity',
				'label' => esc_html__( 'Opacity', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 1,
						),
					),
				),
				'attributes' => array(
					'default' => array(
						'value' => 1,
						'unit'  => 'px',
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day.day-pad' => 'opacity: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_bg_color_disabled',
				'label' => esc_html__( 'Day Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day.day-pad' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_border_color_disabled',
				'label' => esc_html__( 'Day Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day.day-pad' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_label_color_disabled',
				'label' => esc_html__( 'Day Label Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day.day-pad .jet-calendar-week__day-date' => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_label_bg_color_disabled',
				'label' => esc_html__( 'Day Label Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day.day-pad .jet-calendar-week__day-date' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'day_label_border_color_disabled',
				'label' => esc_html__( 'Day Label Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day.day-pad .jet-calendar-week__day-date' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'calendar_mobile_style',
				'title' => esc_html__( 'Mobile', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'mobile_trigger_color',
				'label' => esc_html__( 'Mobile Trigger Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-mobile-trigger' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'mobile_trigger_color_active',
				'label' => esc_html__( 'Active Mobile Trigger Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .calendar-event-active .jet-calendar-week__day-mobile-trigger' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'mobile_trigger_width',
				'label'     => esc_html__( 'Mobile Trigger Width', 'jet-engine' ),
				'type'      => 'range',
				'separator' => 'before',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 100,
						),
					),
					array(
						'value'     => '%',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-mobile-trigger' => 'width: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'mobile_trigger_height',
				'label' => esc_html__( 'Mobile Trigger Height', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 200,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-mobile-trigger' => 'height: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'mobile_trigger_alignment',
				'label' => esc_html__( 'Mobile Trigger Alignment', 'jet-engine' ),
				'type'  => 'choose',
				'options' => array(
					'flex-start' => array(
						'shortcut' => esc_html__( 'Start', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignleft',
					),
					'center' => array(
						'shortcut' => esc_html__( 'Center', 'jet-engine' ),
						'icon'     => 'dashicons-editor-aligncenter',
					),
					'flex-end' => array(
						'shortcut' => esc_html__( 'End', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignright',
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-mobile-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'mobile_trigger_border_radius',
				'label'     => esc_html__( 'Mobile Trigger Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-mobile-trigger' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'mobile_trigger_margin',
				'label' => esc_html__( 'Mobile Trigger Margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-mobile-trigger' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'mobile_event_margin',
				'label' => esc_html__( 'Mobile Event Margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%' ),
				'css_selector' => array(
					'{{WRAPPER}} .jet-calendar-week__day-mobile-event' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->end_section();
	}

	public function render_callback( $attributes = array() ) {
		$render = $this->get_render_instance( $attributes );

		jet_engine()->frontend->frontend_scripts();

		$this->_root['class'][] = 'jet-listing-calendar-block';
		$this->_root['data-element-id'] = $attributes['_block_id'];
		$this->_root['data-is-block'] = $this->get_block_name();

		if ( ! empty( $attributes['className'] ) ) {
			$this->_root['class'][] = $attributes['className'];
		}

		if ( ! empty( $attributes['_element_id'] ) ) {
			$this->_root['id'] = $attributes['_element_id'];
		}

		$result = sprintf(
			'<div %1$s>%2$s</div>',
			$this->get_root_attr_string(),
			$render->get_content()
		);

		return $result;
	}

}
