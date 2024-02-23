<?php

namespace Jet_Engine\Modules\Calendar\Bricks_Views;

use Jet_Engine\Bricks_Views\Elements\Listing_Grid;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Calendar extends Listing_Grid {
	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-listing-calendar'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-listing-calendar'; // Themify icon font class
	public $css_selector = '.jet-calendar-grid'; // Default CSS selector
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'listing-calendar';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Calendar', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {

		$this->register_jet_control_group(
			'section_general',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

		$this->register_group_query_settings();
		$this->register_group_visibility_settings();

		$this->register_jet_control_group(
			'section_table_base',
			[
				'title' => esc_html__( 'Table base', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_caption_style',
			[
				'title' => esc_html__( 'Caption', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_nav_style',
			[
				'title' => esc_html__( 'Navigation arrows', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_week_style',
			[
				'title' => esc_html__( 'Week days', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_day_style',
			[
				'title' => esc_html__( 'Days', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'calendar_mobile_style',
			[
				'title' => esc_html__( 'Mobile', 'jet-engine' ),
				'tab'   => 'style',
			]
		);


	}

	// Set builder controls
	public function set_controls() {

		$this->start_jet_control_group( 'section_general' );

		$this->register_jet_control(
			'lisitng_id',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Listing', 'jet-engine' ),
				'type'        => 'select',
				'options'     => jet_engine()->listings->get_listings_for_options(),
				'inline'      => true,
				'clearable'   => false,
				'searchable'  => true,
				'pasteStyles' => false,
			]
		);

		$module = jet_engine()->modules->get_module( 'calendar' );

		$this->register_jet_control(
			'group_by',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Group posts by', 'jet-engine' ),
				'type'    => 'select',
				'options' => $module->get_calendar_group_keys(),
				'default' => 'post_date',
			]
		);

		$this->register_jet_control(
			'group_by_key',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Meta field name', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'This field must contain date to group posts by. Works only if "Save as timestamp" option for meta field is active', 'jet-engine' ),
				'required'    => [ 'group_by', '=', 'meta_date' ],
			]
		);

		$this->register_jet_control(
			'allow_multiday',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Allow multi-day events', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'group_by', '=', 'meta_date' ],
			]
		);

		$this->register_jet_control(
			'end_date_key',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'End date field name', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'This field must contain date when events ends. Works only if "Save as timestamp" option for meta field is active', 'jet-engine' ),
				'required'    => [
					[ 'group_by', '=', 'meta_date' ],
					[ 'allow_multiday', '=', true ],
				],
			]
		);

		$this->register_jet_control(
			'use_custom_post_types',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Use custom post types', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'custom_post_types',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Post types', 'jet-engine' ),
				'type'     => 'select',
				'multiple' => true,
				'options'  => jet_engine()->listings->get_post_types_for_options(),
				'required' => [ 'use_custom_post_types', '=', true ],
			]
		);

		$this->register_jet_control(
			'week_days_format',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Week days format', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'full'    => esc_html__( 'Full', 'jet-engine' ),
					'short'   => esc_html__( 'Short', 'jet-engine' ),
					'initial' => esc_html__( 'Initial letter', 'jet-engine' ),
				],
				'default' => 'short',
			]
		);

		$this->register_jet_control(
			'custom_start_from',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Start from custom month', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'start_from_month',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Start from month', 'jet-engine' ),
				'type'     => 'select',
				'options'  => $this->get_months(),
				'default'  => date( 'F' ),
				'required' => [ 'custom_start_from', '=', true ],
			]
		);

		$this->register_jet_control(
			'start_from_year',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Start from year', 'jet-engine' ),
				'type'     => 'text',
				'default'  => date( 'Y' ),
				'required' => [ 'custom_start_from', '=', true ],
			]
		);

		$this->register_jet_control(
			'show_posts_nearby_months',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show posts from the nearby months', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'hide_past_events',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Hide past events', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->end_jet_control_group();

		$this->register_controls_query_settings();
		$this->register_controls_visibility_settings();

		$this->start_jet_control_group( 'section_table_base' );

		$this->register_jet_control(
			'table_collapse',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Layout', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'separate' => esc_html__( 'Separate', 'jet-engine' ),
					'collapse' => esc_html__( 'Collapse', 'jet-engine' ),
				],
				'css'     => [
					[
						'property' => 'border-collapse',
					],
				],
				'default' => 'separate',
			]
		);

		$this->register_jet_control(
			'table_border_spacing',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Border spacing', 'jet-engine' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [
					[
						'property' => 'border-spacing',
					],
				],
				'required' => [ 'table_collapse', '=', 'separate' ],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_caption_style' );

		$this->register_jet_control(
			'caption_layout',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Layout', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'layout-1' => esc_html__( 'Layout 1', 'jet-engine' ),
					'layout-2' => esc_html__( 'Layout 2', 'jet-engine' ),
					'layout-3' => esc_html__( 'Layout 3', 'jet-engine' ),
					'layout-4' => esc_html__( 'Layout 4', 'jet-engine' ),
				],
				'default' => 'layout-1',
			]
		);

		$this->register_jet_control(
			'caption_txt_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => '.jet-calendar-caption__name',
					]
				],
			]
		);

		$this->register_jet_control(
			'caption_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-calendar-caption',
					]
				],
			]
		);

		$this->register_jet_control(
			'caption_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => '.jet-calendar-caption',
					]
				],
			]
		);

		$this->register_jet_control(
			'caption_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => '.jet-calendar-caption',
					]
				],
			]
		);

		$this->register_jet_control(
			'caption_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-calendar-caption',
					]
				],
			]
		);

		$this->register_jet_control(
			'caption_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap between caption elements', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'gap',
						'selector' => '.jet-calendar-caption__wrap',
					]
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_nav_style' );

		$this->register_jet_control(
			'nav_box_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Nav box size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => '.jet-calendar-nav__link',
					],
					[
						'property' => 'height',
						'selector' => '.jet-calendar-nav__link',
					],
				],
			]
		);

		$this->register_jet_control(
			'nav_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Arrow size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'font-size',
						'selector' => '.jet-calendar-nav__link',
					]
				],
			]
		);

		$this->register_jet_control(
			'nav_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.jet-calendar-nav__link',
					]
				],
			]
		);

		$this->register_jet_control(
			'nav_background_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-calendar-nav__link',
					]
				],
			]
		);

		$this->register_jet_control(
			'nav_prev_title',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Prev arrow', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'nav_prev_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Nav gap', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'margin-right',
						'selector' => '.jet-calendar-nav__link.nav-link-prev',
					],
				],
			]
		);

		$this->register_jet_control(
			'nav_border_prev',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-calendar-nav__link.nav-link-prev',
					]
				],
			]
		);

		$this->register_jet_control(
			'nav_next_title',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Next arrow', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'nav_next_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Nav gap', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'margin-left',
						'selector' => '.jet-calendar-nav__link.nav-link-next',
					],
				],
			]
		);

		$this->register_jet_control(
			'nav_border_next',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-calendar-nav__link.nav-link-next',
					]
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_week_style' );

		$this->register_jet_control(
			'week_txt_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => '.jet-calendar-header__week-day',
					]
				],
			]
		);

		$this->register_jet_control(
			'week_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-calendar-header__week-day',
					]
				],
			]
		);

		$this->register_jet_control(
			'week_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => '.jet-calendar-header__week-day',
					]
				],
			]
		);

		$this->register_jet_control(
			'week_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-calendar-header__week-day',
					]
				],
			]
		);


		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_day_style' );

		$this->register_jet_control(
			'day_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-calendar-week__day',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_padding',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Padding', 'jet-engine' ),
				'type'    => 'dimensions',
				'css'     => [
					[
						'property' => 'padding',
						'selector' => '.jet-calendar-week__day',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-calendar-week__day',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_styles',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Date Label', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'day_label_typography',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Typography', 'jet-engine' ),
				'type'    => 'typography',
				'css'     => [
					[
						'property' => 'typography',
						'selector' => '.jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => '.jet-calendar-week__day-date',
					],
					[
						'property' => 'height',
						'selector' => '.jet-calendar-week__day-date',
					],
				],
			]
		);

		$this->register_jet_control(
			'day_label_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => '.jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => '.jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_alignment',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Box alignment', 'jet-engine' ),
				'type'    => 'justify-content',
				'css'     => [
					[
						'property' => 'justify-content',
						'selector' => '.jet-calendar-week__day-header',
					],
				],
				'exclude' => [
					'space-between',
					'space-around',
					'space-evenly',
				],
			]
		);

		$this->register_jet_control(
			'day_event_styles',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Event day', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'day_event_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => '.jet-calendar-week__day-content',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_event_min_height',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Min height', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'min-height',
						'selector' => '.jet-calendar-week__day-content',
					],
				],
			]
		);

		$this->register_jet_control(
			'day_events_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap between events', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'margin-top',
						'selector' => '.jet-calendar-week__day-event + .jet-calendar-week__day-event',
					],
				],
			]
		);

		$this->register_jet_control(
			'day_bg_color_has_events',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-calendar-week__day.has-events',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_border_color_has_events',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day border color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => '.jet-calendar-week__day.has-events',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_color_has_events',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day label text color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.has-events .jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_bg_color_has_events',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day label background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.has-events .jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_border_color_has_events',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day label border color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => '.has-events .jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'current_day_styles',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Current day', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'current_day_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-calendar-week__day.current-day',
					]
				],
			]
		);

		$this->register_jet_control(
			'current_day_border_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day border color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => '.jet-calendar-week__day.current-day',
					]
				],
			]
		);

		$this->register_jet_control(
			'current_day_label_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day label text color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.current-day .jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'current_day_label_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day label background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.current-day .jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'current_day_label_border_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day label border color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => '.current-day .jet-calendar-week__day-date',
					]
				],
			]
		);


		$this->register_jet_control(
			'day_disabled_styles',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Disabled days (not in current month)', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'day_opacity',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Opacity', 'jet-engine' ),
				'type'  => 'number',
				'step'  => '.01',
				'min'   => '0',
				'max'   => '1',
				'css'   => [
					[
						'property' => 'opacity',
						'selector' => '.jet-calendar-week__day.day-pad',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_bg_color_disabled',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-calendar-week__day.day-pad',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_border_color_disabled',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day border color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => '.jet-calendar-week__day.day-pad',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_color_disabled',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day label text color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.day-pad .jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_bg_color_disabled',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day label background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.day-pad .jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->register_jet_control(
			'day_label_border_color_disabled',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Day label border color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => '.day-pad .jet-calendar-week__day-date',
					]
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'calendar_mobile_style' );

		$this->register_jet_control(
			'mobile_trigger_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Mobile trigger color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-calendar-week__day-mobile-trigger',
					]
				],
			]
		);

		$this->register_jet_control(
			'mobile_trigger_color_active',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Active mobile trigger color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.calendar-event-active .jet-calendar-week__day-mobile-trigger',
					]
				],
			]
		);

		$this->register_jet_control(
			'mobile_trigger_box-size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Mobile trigger box size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => '.jet-calendar-week__day-mobile-trigger',
					],
					[
						'property' => 'height',
						'selector' => '.jet-calendar-week__day-mobile-trigger',
					],
				],
			]
		);

		$this->register_jet_control(
			'mobile_trigger_alignment',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Mobile trigger alignment', 'jet-engine' ),
				'type'    => 'justify-content',
				'css'     => [
					[
						'property' => 'justify-content',
						'selector' => '.jet-calendar-week__day-mobile-wrap',
					],
				],
				'exclude' => [
					'space-between',
					'space-around',
					'space-evenly',
				],
			]
		);

		$this->register_jet_control(
			'mobile_trigger_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Mobile trigger border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-calendar-week__day-mobile-trigger',
					]
				],
			]
		);

		$this->register_jet_control(
			'mobile_trigger_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Mobile trigger margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => '.jet-calendar-week__day-mobile-trigger',
					]
				],
			]
		);

		$this->register_jet_control(
			'mobile_event_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Mobile event padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => '.jet-calendar-week__day-mobile-event',
					]
				],
			]
		);

		$this->end_jet_control_group();
	}

	// Enqueue element styles and scripts
	public function enqueue_scripts() {
		wp_enqueue_style( 'jet-engine-frontend' );
	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$attrs['show_posts_nearby_months'] = $attrs['show_posts_nearby_months'] ?? false;

		return $attrs;
	}

	/**
	 * Returns available months list
	 *
	 * @return [type] [description]
	 */
	public function get_months() {
		return array(
			'January'   => esc_html__( 'January', 'jet-engine' ),
			'February'  => esc_html__( 'February', 'jet-engine' ),
			'March'     => esc_html__( 'March', 'jet-engine' ),
			'April'     => esc_html__( 'April', 'jet-engine' ),
			'May'       => esc_html__( 'May', 'jet-engine' ),
			'June'      => esc_html__( 'June', 'jet-engine' ),
			'July'      => esc_html__( 'July', 'jet-engine' ),
			'August'    => esc_html__( 'August', 'jet-engine' ),
			'September' => esc_html__( 'September', 'jet-engine' ),
			'October'   => esc_html__( 'October', 'jet-engine' ),
			'November'  => esc_html__( 'November', 'jet-engine' ),
			'December'  => esc_html__( 'December', 'jet-engine' ),
		);
	}
}