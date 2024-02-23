<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Date_Period extends Jet_Smart_Filters_Bricks_Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-date-period'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-date-period-filter'; // Themify icon font class
	public $css_selector = '.jet-date-period__datepicker-button'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'date-period';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Date Period Filter', 'jet-smart-filters' );
	}

	public function register_filter_style_group() {
		$this->register_jet_control_group(
			'section_datepicker_button_style',
			[
				'title' => esc_html__( 'Datepicker Button', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'next_prev_buttons_style',
			[
				'title' => esc_html__( 'Prev/Next Buttons', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		// Waiting for the Bricks developers to fix the bug
		/*$this->register_jet_control_group(
			'section_calendar_styles',
			[
				'title' => esc_html__( 'Calendar', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_calendar_header_styles',
			[
				'title' => esc_html__( 'Calendar Header', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_calendar_week_days',
			[
				'title' => esc_html__( 'Calendar Week Days', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_calendar_days',
			[
				'title' => esc_html__( 'Calendar Days', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);*/
	}

	public function register_filter_style_controls() {
		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/date-period/css-scheme',
			array(
				'date-period-wrapper'   => '.jet-date-period__wrapper',
				'datepicker-button'     => '.jet-date-period__datepicker-button',
				'prev-button'           => '.jet-date-period__prev',
				'next-button'           => '.jet-date-period__next',
				'calendar'              => '.jet-date-period-range',
				'calendar-header'       => '.ui-datepicker-header',
				'calendar-prev-button'  => '.ui-datepicker-prev',
				'calendar-next-button'  => '.ui-datepicker-next',
				'calendar-title'        => '.ui-datepicker-title',
				'calendar-body-header'  => '.ui-datepicker-calendar thead',
				'calendar-body-content' => '.ui-datepicker-calendar tbody',
			)
		);

		$this->start_jet_control_group( 'section_datepicker_button_style' );

		$this->register_jet_control(
			'datepicker_button_alignment',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'    => 'justify-content',
				'exclude' => [
					'space-between',
					'space-around',
					'space-evenly',
				],
				'css'     => [
					[
						'property' => 'justify-content',
						'selector' => $css_scheme['date-period-wrapper'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'next_prev_buttons_style' );

		$this->register_jet_control(
			'next_prev_buttons_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Gap', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '4px',
				'css'     => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['date-period-wrapper'],
					],
				],
			]
		);

		$this->register_jet_control(
			'next_prev_buttons_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Width', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => $css_scheme['prev-button'] . ', ' . $css_scheme['next-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'next_prev_buttons_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['prev-button'] . ', ' . $css_scheme['next-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'next_prev_buttons_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['prev-button'] . ', ' . $css_scheme['next-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'next_prev_buttons_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-smart-filters' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['prev-button'] . ', ' . $css_scheme['next-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'next_prev_buttons_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['prev-button'] . ', ' . $css_scheme['next-button'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		// Waiting for the Bricks developers to fix the bug
		// Include Datepicker Style
		/*include jet_smart_filters()->plugin_path( 'includes/bricks/elements/common-controls/air-datepicker-style.php' );*/
	}

	// Enqueue element styles and scripts
	public function enqueue_scripts() {
		wp_enqueue_script( 'air-datepicker' );
		wp_enqueue_style( 'air-datepicker' );
	}
}