<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

use Bricks\Database;
use Bricks\Element;
use Bricks\Helpers;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Date_Range extends Jet_Smart_Filters_Bricks_Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-date-range'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-date-range-filter'; // Themify icon font class
	public $css_selector = '.jet-date-range__control'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'date-range';
	public $filter_id_multiple = false;

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Date Range Filter', 'jet-smart-filters' );
	}

	// Set builder control groups
	public function set_control_groups() {

		$this->register_general_group();
		$this->register_filter_content_group();
		$this->register_filter_inputs_group();
		// Waiting for the Bricks developers to fix the bug
		/*$this->register_filter_calendar_group();*/
		$this->register_filter_label_group();
		$this->register_filter_button_group();
	}

	// Set builder controls
	public function set_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/date-range/css-scheme',
			[
				'filter-wrapper'            => '.jet-smart-filters-date-range',
				'filter-content'            => '.jet-smart-filters-date-range .jet-date-range',
				'filters-label'             => '.jet-filter-label',
				'inputs'                    => '.jet-date-range__inputs',
				'input'                     => '.jet-date-range__control',
				'apply-filters-button'      => '.jet-date-range__submit',
				'apply-filters-button-icon' => '.jet-date-range__submit-icon',
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

		$this->register_general_controls();
		$this->register_filter_content_controls( $css_scheme );
		$this->register_filter_inputs_controls( $css_scheme );
		// Waiting for the Bricks developers to fix the bug
		/*$this->register_filter_calendar_controls( $css_scheme );*/
		$this->register_filter_label_controls( $css_scheme );
		$this->register_filter_button_controls( $css_scheme );
	}

	public function register_filter_content_group() {
		$this->register_jet_control_group(
			'section_date_range_content_style',
			[
				'title' => esc_html__( 'Content', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_content_controls( $css_scheme = null ) {
		$this->start_jet_control_group( 'section_date_range_content_style' );

		$this->register_jet_control(
			'content_position',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Direction', 'jet-smart-filters' ),
				'type'  => 'direction',
				'css'   => [
					[
						'property' => 'flex-direction',
						'selector' => $css_scheme['filter-content'],
					],
				],
			]
		);

		$this->register_jet_control(
			'menu_main_axis',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Align main axis', 'jet-smart-filters' ),
				'type'     => 'justify-content',
				'tooltip'  => [
					'content'  => 'justify-content',
					'position' => 'top-left',
				],
				'css'      => [
					[
						'property' => 'justify-content',
						'selector' => $css_scheme['filter-content'],
					],
				],
				'required' => [ 'content_position', '=', 'row' ],
			]
		);

		$this->register_jet_control(
			'menu_cross_axis',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Align cross axis', 'jet-smart-filters' ),
				'type'    => 'align-items',
				'tooltip' => [
					'content'  => 'align-items',
					'position' => 'top-left',
				],
				'default' => 'flex-start',
				'css'     => [
					[
						'property' => 'align-items',
						'selector' => $css_scheme['filter-content'],
					],
				],
			]
		);

		$this->register_jet_control(
			'content_date_range_inputs_width',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Inputs Width', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '100%',
				'css'     => [
					[
						'property' => 'flex-basis',
						'selector' => $css_scheme['inputs'],
					],
					[
						'property' => 'width',
						'selector' => $css_scheme['inputs'],
					],
				],
			]
		);

		$this->register_jet_control(
			'content_date_range_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Gap', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '20px',
				'css'     => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['filter-content'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	public function register_filter_inputs_group() {
		$this->register_jet_control_group(
			'section_date_range_input_style',
			[
				'title' => esc_html__( 'Input', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_inputs_controls( $css_scheme = null ) {
		$this->start_jet_control_group( 'section_date_range_input_style' );

		$this->register_jet_control(
			'date_range_input_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Width', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'flex-basis',
						'selector' => $css_scheme['input'],
					],
				],
			]
		);

		$this->register_jet_control(
			'date_range_input_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Gap', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '20px',
				'css'     => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['inputs'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	public function register_filter_calendar_group() {
		$this->register_jet_control_group(
			'section_calendar_styles',
			[
				'title' => esc_html__( 'Calendar', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_calendar_title',
			[
				'title' => esc_html__( 'Calendar Caption', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_calendar_prev_next',
			[
				'title' => esc_html__( 'Calendar Navigation Arrows', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_calendar_header',
			[
				'title' => esc_html__( 'Calendar Week Days', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_calendar_content',
			[
				'title' => esc_html__( 'Calendar Days', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_calendar_controls( $css_scheme = null ) {
		// Include Datepicker Style
		include jet_smart_filters()->plugin_path( 'includes/bricks/elements/common-controls/datepicker-style.php' );
	}

	public function register_filter_button_group() {
		$this->register_jet_control_group(
			'section_filter_apply_button_style',
			[
				'title'    => esc_html__( 'Button', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [ 'hide_apply_button', '=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_filter_apply_button_icon_style',
			[
				'title'    => esc_html__( 'Button Icon', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [
					[ 'hide_apply_button', '=', false ],
					[ 'apply_button_icon', '!=', '' ],
				],
			]
		);
	}

	public function register_filter_button_controls( $css_scheme = null ) {
		$this->start_jet_control_group( 'section_filter_apply_button_style' );

		$this->register_jet_control(
			'filter_apply_button_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-smart-filters' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_filter_apply_button_icon_style' );

		$this->register_jet_control(
			'filter_apply_button_icon_direction',
			[
				'tab'       => 'style',
				'label'     => esc_html__( 'Direction', 'jet-smart-filters' ),
				'type'      => 'direction',
				'direction' => 'row',
				'css'       => [
					[
						'property' => 'flex-direction',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_icon_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['apply-filters-button-icon'],
					],
					[
						'property' => 'fill',
						'selector' => $css_scheme['apply-filters-button-icon'] . ' :is(svg, path)',
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_icon_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon size', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'font-size',
						'selector' => $css_scheme['apply-filters-button-icon'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_icon_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Icon gap', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '12px',
				'css'     => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	// Render element HTML
	public function render() {
		jet_smart_filters()->set_filters_used();

		$base_class = $this->name;
		$settings   = $this->parse_jet_render_attributes( $this->get_jet_settings() );
		$filter_id  = ! empty( $settings['filter_id'] ) ? $settings['filter_id'] : '';

		// STEP: Select filter is empty: Show placeholder text
		if ( empty( $filter_id ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select filter to show.', 'jet-smart-filters' )
				]
			);
		}

		$provider  = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';
		$filter_id = apply_filters( 'jet-smart-filters/render_filter_template/filter_id', $filter_id );

		// STEP: Content provider is empty: Show placeholder text
		if ( empty( $provider ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select content provider to show.', 'jet-smart-filters' )
				]
			);
		}

		echo "<div {$this->render_attributes( '_root' )}>";

		printf( '<div class="%1$s jet-filter">', $base_class );

		$apply_type = ! empty( $settings['apply_type'] ) ? $settings['apply_type'] : 'ajax';

		if ( 'ajax' === $apply_type ) {
			$apply_type = 'ajax-reload';
		}

		$query_id             = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$show_label           = ! empty( $settings['show_label'] ) ? filter_var( $settings['show_label'], FILTER_VALIDATE_BOOLEAN ) : false;
		$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );
		$icon                 = '';

		if ( ! empty( $settings['apply_button_icon'] ) ) {
			$rendered_icon = Element::render_icon( $settings['apply_button_icon'] );
			$format        = '<span class="jet-date-range__submit-icon">%s</span>';
			$icon          = sprintf( $format, $rendered_icon );
		}

		$hide_button       = ! empty( $settings['hide_apply_button'] ) ? $settings['hide_apply_button'] : false;
		$apply_button_text = ! empty( $settings['apply_button_text'] ) ? $settings['apply_button_text'] : '';
		$hide_button       = filter_var( $hide_button, FILTER_VALIDATE_BOOLEAN );

		$filter_template_args = [
			'filter_id'            => $filter_id,
			'content_provider'     => $provider,
			'additional_providers' => $additional_providers,
			'apply_type'           => $apply_type,
			'hide_button'          => $hide_button,
			'button_text'          => $apply_button_text,
			'button_icon'          => $icon,
			'query_id'             => $query_id,
		];

		jet_smart_filters()->admin_bar_register_item( $filter_id );

		include jet_smart_filters()->get_template( 'common/filter-label.php' );

		jet_smart_filters()->filter_types->render_filter_template( $this->jet_element_render, $filter_template_args );

		echo '</div>';

		echo "</div>";

	}

}