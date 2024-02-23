<?php
namespace Elementor;

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Elementor\Jet_Listing_Calendar_Widget' ) ) {

	class Jet_Listing_Calendar_Widget extends Jet_Listing_Grid_Widget {

		public $is_first        = false;
		public $data            = false;
		public $first_day       = false;
		public $last_day        = false;
		public $multiday_events = array();
		public $posts_cache     = array();
		public $start_from      = false;

		public $prev_month_posts = array();
		public $next_month_posts = array();

		public function get_name() {
			return 'jet-listing-calendar';
		}

		public function get_title() {
			return __( 'Calendar', 'jet-engine' );
		}

		public function get_icon() {
			return 'jet-engine-icon-listing-calendar';
		}

		public function get_categories() {
			return array( 'jet-listing-elements' );
		}

		public function get_help_url() {
			return 'https://crocoblock.com/knowledge-base/articles/jetengine-calendar-listing-functionality-how-to-add-a-dynamic-calendar/?utm_source=jetengine&utm_medium=listing-calendar&utm_campaign=need-help';
		}

		protected function register_controls() {

			$this->register_general_settings();
			$this->register_query_settings();
			$this->register_visibility_settings();
			$this->register_style_settings();

		}

		public function register_general_settings() {

			$module = jet_engine()->modules->get_module( 'calendar' );

			$this->start_controls_section(
				'section_general',
				array(
					'label' => __( 'General', 'jet-engine' ),
				)
			);

			$this->add_control(
				'lisitng_id',
				array(
					'label'      => __( 'Listing', 'jet-engine' ),
					'type'       => 'jet-query',
					'query_type' => 'post',
					'query'      => array(
						'post_type' => jet_engine()->post_type->slug(),
					),
					'edit_button' => array(
						'active' => true,
						'label'  => __( 'Edit Listing', 'jet-engine' ),
					),
				)
			);

			$this->add_control(
				'query_notice',
				array(
					'type'            => \Elementor\Controls_Manager::RAW_HTML,
					'raw'             => __( '<b>Please note:</b> For non-posts listings (users, terms, CCT etc.) set Query with Custom Query settings', 'jet-engine' ),
					'content_classes' => 'elementor-descriptor',
				)
			);

			$this->add_control(
				'group_by',
				array(
					'label'   => __( 'Group posts by', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'post_date',
					'options' => $module->get_calendar_group_keys(),
				)
			);

			$this->add_control(
				'group_by_key',
				array(
					'label'       => esc_html__( 'Meta field name', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'Could be meta field or item peroperty field (depende on query used). This field must contain date to group items by. Works only if "Save as timestamp" option for this field is active', 'jet-engine' ),
					'condition'   => array(
						'group_by' => 'meta_date'
					),
				)
			);

			$this->add_control(
				'allow_multiday',
				array(
					'label'        => __( 'Allow multi-day events', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => '',
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'group_by' => 'meta_date'
					),
				)
			);

			$this->add_control(
				'end_date_key',
				array(
					'label'       => esc_html__( 'End date field name', 'jet-engine' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
					'description' => __( 'If you used "Advanced Datetime" meta field type you can leave this field empty. This field must contain date when events ends. Works only if "Save as timestamp" option for meta field is active.', 'jet-engine' ),
					'condition'   => array(
						'group_by'       => 'meta_date',
						'allow_multiday' => 'yes',
					),
				)
			);

			$this->add_control(
				'use_custom_post_types',
				array(
					'label'        => __( 'Use Custom Post Types', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'custom_post_types',
				array(
					'label'       => esc_html__( 'Post Types', 'jet-engine' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => jet_engine()->listings->get_post_types_for_options(),
					'condition'   => array(
						'use_custom_post_types' => 'yes',
					),
				)
			);

			$this->add_control(
				'week_days_format',
				array(
					'label'   => __( 'Week days format', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'short',
					'options' => array(
						'full'    => __( 'Full', 'jet-engine' ),
						'short'   => __( 'Short', 'jet-engine' ),
						'initial' => __( 'Initial letter', 'jet-engine' ),
					),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'custom_start_from',
				array(
					'label'        => __( 'Start from custom month', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => '',
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'start_from_month',
				array(
					'label'     => __( 'Start from month', 'jet-engine' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => date( 'F' ),
					'options'   => $this->get_months(),
					'condition' => array(
						'custom_start_from' => 'yes',
					),
				)
			);

			$this->add_control(
				'start_from_year',
				array(
					'label'     => __( 'Start from year', 'jet-engine' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => date( 'Y' ),
					'condition' => array(
						'custom_start_from' => 'yes',
					),
				)
			);

			$this->add_control(
				'show_posts_nearby_months',
				array(
					'label'        => __( 'Show posts from the nearby months', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'hide_past_events',
				array(
					'label'        => __( 'Hide past events', 'jet-engine' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-engine' ),
					'label_off'    => __( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'legacy_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => jet_engine()->listings->legacy->get_notice(),
				)
			);

			$this->end_controls_section();

		}

		/**
		 * Returns available months list
		 *
		 * @return [type] [description]
		 */
		public function get_months() {
			return array(
				'January'   => __( 'January', 'jet-engine' ),
				'February'  => __( 'February', 'jet-engine' ),
				'March'     => __( 'March', 'jet-engine' ),
				'April'     => __( 'April', 'jet-engine' ),
				'May'       => __( 'May', 'jet-engine' ),
				'June'      => __( 'June', 'jet-engine' ),
				'July'      => __( 'July', 'jet-engine' ),
				'August'    => __( 'August', 'jet-engine' ),
				'September' => __( 'September', 'jet-engine' ),
				'October'   => __( 'October', 'jet-engine' ),
				'November'  => __( 'November', 'jet-engine' ),
				'December'  => __( 'December', 'jet-engine' ),
			);
		}

		/**
		 * Register style settings
		 * @return [type] [description]
		 */
		public function register_style_settings() {

			$this->start_controls_section(
				'section_caption_style',
				array(
					'label'      => esc_html__( 'Caption', 'jet-engine' ),
					'tab'        => Controls_Manager::TAB_STYLE,
					'show_label' => false,
				)
			);

			$this->add_control(
				'caption_layout',
				array(
					'label'   => __( 'Layout', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'layout-1',
					'options' => array(
						'layout-1' => __( 'Layout 1', 'jet-engine' ),
						'layout-2' => __( 'Layout 2', 'jet-engine' ),
						'layout-3' => __( 'Layout 3', 'jet-engine' ),
						'layout-4' => __( 'Layout 4', 'jet-engine' ),
					),
				)
			);

			$this->add_control(
				'caption_bg_color',
				array(
					'label' => esc_html__( 'Background Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-caption' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'caption_txt_color',
				array(
					'label'  => esc_html__( 'Label Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-caption__name' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'caption_txt_typography',
					'selector' => '{{WRAPPER}} .jet-calendar-caption__name',
				)
			);

			$this->add_responsive_control(
				'caption_padding',
				array(
					'label'      => esc_html__( 'Padding', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'caption_margin',
				array(
					'label'      => esc_html__( 'Margin', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-caption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'caption_border',
					'label'          => esc_html__( 'Border', 'jet-engine' ),
					'placeholder'    => '1px',
					'selector'       => '{{WRAPPER}} .jet-calendar-caption',
				)
			);

			$this->add_responsive_control(
				'caption_border_radius',
				array(
					'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'caption_gap',
				array(
					'label' => esc_html__( 'Gap between caption elements', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-caption__wrap' => 'gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_nav_style',
				array(
					'label'      => esc_html__( 'Navigation Arrows', 'jet-engine' ),
					'tab'        => Controls_Manager::TAB_STYLE,
					'show_label' => false,
				)
			);

			$this->add_control(
				'nav_width',
				array(
					'label' => esc_html__( 'Width', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 10,
							'max' => 100,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-nav__link' => 'width: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'nav_height',
				array(
					'label' => esc_html__( 'Height', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 10,
							'max' => 100,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-nav__link' => 'height: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'nav_size',
				array(
					'label' => esc_html__( 'Arrow Size', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 10,
							'max' => 100,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-nav__link' => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs( 'tabs_nav_prev_next_style' );

			$this->start_controls_tab(
				'tab_nav_prev',
				array(
					'label' => esc_html__( 'Prev Arrow (Default)', 'jet-engine' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'nav_border',
					'label'          => esc_html__( 'Border', 'jet-engine' ),
					'placeholder'    => '1px',
					'selector'       => '{{WRAPPER}} .jet-calendar-nav__link',
				)
			);

			$this->add_responsive_control(
				'nav_border_radius',
				array(
					'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-nav__link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_nav_next',
				array(
					'label' => esc_html__( 'Next Arrow', 'jet-engine' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'nav_border_next',
					'label'          => esc_html__( 'Border', 'jet-engine' ),
					'placeholder'    => '1px',
					'selector'       => '{{WRAPPER}} .jet-calendar-nav__link.nav-link-next',
				)
			);

			$this->add_responsive_control(
				'nav_border_radius_next',
				array(
					'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-nav__link.nav-link-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->start_controls_tabs( 'tabs_nav_style' );

			$this->start_controls_tab(
				'tab_nav_normal',
				array(
					'label' => esc_html__( 'Normal', 'jet-engine' ),
				)
			);

			$this->add_control(
				'nav_color',
				array(
					'label'     => esc_html__( 'Text Color', 'jet-engine' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-nav__link' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'nav_background_color',
				array(
					'label'  => esc_html__( 'Background Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-nav__link' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_nav_hover',
				array(
					'label' => esc_html__( 'Hover', 'jet-engine' ),
				)
			);

			$this->add_control(
				'nav_color_hover',
				array(
					'label' => esc_html__( 'Text Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'nav_background_color_hover',
				array(
					'label' => esc_html__( 'Background Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'nav_border_color_hover',
				array(
					'label' => esc_html__( 'Border Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'condition' => array(
						'nav_border_border!' => '',
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-nav__link:hover' => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			$this->start_controls_section(
				'section_week_style',
				array(
					'label'      => esc_html__( 'Week Days', 'jet-engine' ),
					'tab'        => Controls_Manager::TAB_STYLE,
					'show_label' => false,
				)
			);

			$this->add_control(
				'week_bg_color',
				array(
					'label' => esc_html__( 'Background Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-header__week-day' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'week_txt_color',
				array(
					'label'  => esc_html__( 'Text Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-header__week-day' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'week_txt_typography',
					'selector' => '{{WRAPPER}} .jet-calendar-header__week-day',
				)
			);

			$this->add_responsive_control(
				'week_padding',
				array(
					'label'      => esc_html__( 'Padding', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-header__week-day' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'week_border_width',
				array(
					'label'      => esc_html__( 'Border Width', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-header__week-day' => 'border-style: solid; border-top-width: {{TOP}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width: {{LEFT}}{{UNIT}}; border-right-width: 0;',
						'{{WRAPPER}} .jet-calendar-header__week-day:last-child' => 'border-right-width: {{RIGHT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'week_border_color',
				array(
					'label'  => esc_html__( 'Border Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-header__week-day' => 'border-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'week_border_color_first',
				array(
					'label'  => esc_html__( 'First Border Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-header__week-day:first-child' => 'border-left-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'week_border_color_last',
				array(
					'label'  => esc_html__( 'Last Border Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-header__week-day:last-child' => 'border-right-color: {{VALUE}}',
					),
				)
			);

			$this->add_responsive_control(
				'week_border_radius',
				array(
					'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-header__week-day:first-child' => 'border-radius: {{TOP}}{{UNIT}} 0 0 {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .jet-calendar-header__week-day:last-child' => 'border-radius: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0;',
					),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_day_style',
				array(
					'label'      => esc_html__( 'Days', 'jet-engine' ),
					'tab'        => Controls_Manager::TAB_STYLE,
					'show_label' => false,
				)
			);

			$this->add_control(
				'day_bg_color',
				array(
					'label' => esc_html__( 'Background Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_responsive_control(
				'day_padding',
				array(
					'label'      => esc_html__( 'Padding', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-week__day-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'day_min_height',
				array(
					'label' => esc_html__( 'Min height', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 10,
							'max' => 200,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day-content' => 'min-height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .jet-calendar-week__day-wrap'    => 'min-height: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'day_events_gap',
				array(
					'label' => esc_html__( 'Gap between events', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 20,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day-event + .jet-calendar-week__day-event' => 'margin-top: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'day_border_width',
				array(
					'label' => esc_html__( 'Border Width', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 20,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week .jet-calendar-week__day' => 'border: {{SIZE}}{{UNIT}} solid; border-right-width: 0; border-bottom-width: 0;',
						'{{WRAPPER}} .jet-calendar-week .jet-calendar-week__day:last-child' => 'border-right-width: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} tbody .jet-calendar-week:last-child .jet-calendar-week__day' => 'border-bottom-width: {{SIZE}}{{UNIT}};'
					),
				)
			);

			$this->add_control(
				'day_border_color',
				array(
					'label'  => esc_html__( 'Border Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-grid .jet-calendar-week .jet-calendar-week__day' => 'border-color: {{VALUE}}',
					),
				)
			);

			$this->add_responsive_control(
				'day_border_radius',
				array(
					'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'selectors'  => array(
						'{{WRAPPER}} tbody .jet-calendar-week:first-child .jet-calendar-week__day:first-child' => 'border-radius: {{TOP}}{{UNIT}} 0 0 0;',
						'{{WRAPPER}} tbody .jet-calendar-week:first-child .jet-calendar-week__day:last-child' => 'border-radius: 0 {{RIGHT}}{{UNIT}} 0 0;',
						'{{WRAPPER}} tbody .jet-calendar-week:last-child .jet-calendar-week__day:first-child' => 'border-radius: 0 0 0 {{BOTTOM}}{{UNIT}};',
						'{{WRAPPER}} tbody .jet-calendar-week:last-child .jet-calendar-week__day:last-child' => 'border-radius: 0 0 {{LEFT}}{{UNIT}} 0;',
					),
				)
			);

			$this->add_control(
				'day_label_styles',
				array(
					'label'     => esc_html__( 'Date Label', 'jet-engine' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->start_controls_tabs( 'tabs_day_label_style' );

			$this->start_controls_tab(
				'tabs_day_label_noraml',
				array(
					'label' => esc_html__( 'Normal', 'jet-engine' ),
				)
			);

			$this->add_control(
				'day_label_color',
				array(
					'label'  => esc_html__( 'Text Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day-date' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'day_label_bg_color',
				array(
					'label' => esc_html__( 'Background Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day-date' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tabs_day_label_has_events',
				array(
					'label' => esc_html__( 'Has Events', 'jet-engine' ),
				)
			);

			$this->add_control(
				'day_label_color_has_events',
				array(
					'label'  => esc_html__( 'Text Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .has-events .jet-calendar-week__day-date' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'day_label_bg_color_has_events',
				array(
					'label' => esc_html__( 'Background Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .has-events .jet-calendar-week__day-date' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'tabs_separator',
				array(
					'type' => Controls_Manager::DIVIDER,
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'day_label_typography',
					'selector' => '{{WRAPPER}} .jet-calendar-week__day-date',
				)
			);

			$this->add_responsive_control(
				'day_label_alignment',
				array(
					'label'   => esc_html__( 'Date Box Alignment', 'jet-engine' ),
					'type'    => Controls_Manager::CHOOSE,
					'default' => 'flex-end',
					'options' => array(
						'flex-start' => array(
							'title' => esc_html__( 'Start', 'jet-engine' ),
							'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'jet-engine' ),
							'icon'  => 'eicon-h-align-center',
						),
						'flex-end' => array(
							'title' => esc_html__( 'End', 'jet-engine' ),
							'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-week__day-header' => 'justify-content: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'day_label_text_alignment',
				array(
					'label'   => esc_html__( 'Date Text Alignment', 'jet-engine' ),
					'type'    => Controls_Manager::CHOOSE,
					'default' => 'center',
					'options' => array(
						'flex-start'    => array(
							'title' => esc_html__( 'Start', 'jet-engine' ),
							'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'jet-engine' ),
							'icon'  => 'eicon-h-align-center',
						),
						'flex-end' => array(
							'title' => esc_html__( 'End', 'jet-engine' ),
							'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-week__day-date' => 'justify-content: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'day_label_width',
				array(
					'label' => esc_html__( 'Width', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'range' => array(
						'%' => array(
							'min' => 1,
							'max' => 100,
						),
						'px' => array(
							'min' => 10,
							'max' => 100,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day-date' => 'width: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'day_label_height',
				array(
					'label' => esc_html__( 'Height', 'jet-engine' ),
					'type'  => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 10,
							'max' => 100,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day-date' => 'height: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'day_label_border',
					'label'          => esc_html__( 'Border', 'jet-engine' ),
					'placeholder'    => '1px',
					'selector'       => '{{WRAPPER}} .jet-calendar-week__day-date',
				)
			);

			$this->add_responsive_control(
				'day_label_border_radius',
				array(
					'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-week__day-date' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'day_label_padding',
				array(
					'label'      => esc_html__( 'Padding', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-week__day-date' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'day_label_margin',
				array(
					'label'      => esc_html__( 'Margin', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-week__day-date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'current_day_styles',
				array(
					'label'     => esc_html__( 'Current Day', 'jet-engine' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'current_day_bg_color',
				array(
					'label' => esc_html__( 'Day Background Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day.current-day' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'current_day_label_color',
				array(
					'label'  => esc_html__( 'Day Label Text Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day.current-day .jet-calendar-week__day-date' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'current_day_label_bg_color',
				array(
					'label' => esc_html__( 'Day Label Background Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day.current-day .jet-calendar-week__day-date' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'current_day_label_border_color',
				array(
					'label' => esc_html__( 'Day Label Border Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day.current-day .jet-calendar-week__day-date' => 'border-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'day_disabled_styles',
				array(
					'label'     => esc_html__( 'Disabled Days (not in current month)', 'jet-engine' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'day_opacity',
				array(
					'label' => esc_html__( 'Opacity', 'jet-engine' ),
					'type'  => Controls_Manager::NUMBER,
					'min' => 0,
					'max' => 1,
					'step' => 0.1,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day.day-pad' => 'opacity: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'day_bg_color_disabled',
				array(
					'label' => esc_html__( 'Day Background Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day.day-pad' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'day_border_color_disabled',
				array(
					'label'  => esc_html__( 'Day Border Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day.day-pad' => 'border-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'day_label_color_disabled',
				array(
					'label'  => esc_html__( 'Day Label Text Color', 'jet-engine' ),
					'type'   => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day.day-pad .jet-calendar-week__day-date' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'day_label_bg_color_disabled',
				array(
					'label' => esc_html__( 'Day Label Background Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day.day-pad .jet-calendar-week__day-date' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'day_label_border_color_disabled',
				array(
					'label' => esc_html__( 'Day Label Border Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day.day-pad .jet-calendar-week__day-date' => 'border-color: {{VALUE}}',
					),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'calendar_mobile_style',
				array(
					'label'      => esc_html__( 'Mobile', 'jet-engine' ),
					'tab'        => Controls_Manager::TAB_STYLE,
					'show_label' => false,
				)
			);

			$this->add_control(
				'mobile_trigger_color',
				array(
					'label' => esc_html__( 'Mobile Trigger Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day-mobile-trigger' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'mobile_trigger_color_active',
				array(
					'label' => esc_html__( 'Active Mobile Trigger Color', 'jet-engine' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .calendar-event-active .jet-calendar-week__day-mobile-trigger' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'mobile_trigger_width',
				array(
					'label'      => esc_html__( 'Mobile Trigger Width', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'range'      => array(
						'px' => array(
							'min' => 10,
							'max' => 100,
						),
						'%' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day-mobile-trigger' => 'width: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'mobile_trigger_height',
				array(
					'label'      => esc_html__( 'Mobile Trigger Height', 'jet-engine' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min' => 10,
							'max' => 200,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .jet-calendar-week__day-mobile-trigger' => 'height: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'mobile_trigger_alignment',
				array(
					'label'   => esc_html__( 'Mobile Trigger Alignment', 'jet-engine' ),
					'type'    => Controls_Manager::CHOOSE,
					'default' => 'flex-end',
					'options' => array(
						'flex-start'    => array(
							'title' => esc_html__( 'Start', 'jet-engine' ),
							'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'jet-engine' ),
							'icon'  => 'eicon-h-align-center',
						),
						'flex-end' => array(
							'title' => esc_html__( 'End', 'jet-engine' ),
							'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-week__day-mobile-wrap' => 'justify-content: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'mobile_trigger_border_radius',
				array(
					'label'      => esc_html__( 'Mobile Trigger Border Radius', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-week__day-mobile-trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'mobile_trigger_margin',
				array(
					'label'      => esc_html__( 'Mobile Trigger Margin', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-week__day-mobile-trigger' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'mobile_event_margin',
				array(
					'label'      => esc_html__( 'Mobile Event Margin', 'jet-engine' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
					'selectors'  => array(
						'{{WRAPPER}} .jet-calendar-week__day-mobile-event' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);


			$this->end_controls_section();

		}

		/**
		 * Render grid posts
		 *
		 * @return void
		 */
		public function render_posts() {
			$instance = jet_engine()->listings->get_render_instance( 'listing-calendar', $this->get_widget_settings() );
			$instance->render_content();
		}

		protected function render() {
			$this->render_posts();
		}

	}

}
