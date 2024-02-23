<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

use Bricks\Database;
use Bricks\Helpers;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Sorting extends Jet_Smart_Filters_Bricks_Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-sorting'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-sorting-filter'; // Themify icon font class
	public $css_selector = '.jet-sorting-select'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'sorting';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Sorting Filter', 'jet-smart-filters' );
	}

	// Set builder control groups
	public function set_control_groups() {

		$this->register_general_group();
		$this->register_filter_settings_group();
		$this->register_filter_style_group();
		$this->register_filter_label_group();
		$this->register_filter_button_group();
	}

	// Set builder controls
	public function set_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/sorting/css-scheme',
			array(
				'filter'               => '.jet-sorting',
				'filters-label'        => '.jet-sorting-label',
				'select'               => '.jet-sorting-select',
				'apply-filters'        => '.apply-filters',
				'apply-filters-button' => '.apply-filters__button',
			)
		);

		$this->register_general_controls();
		$this->register_filter_settings_controls();
		$this->register_filter_style_controls( $css_scheme );
		$this->register_filter_label_controls( $css_scheme );
		$this->register_filter_button_controls( $css_scheme );
	}

	public function register_filter_settings_group() {
		$this->register_jet_control_group(
			'section_sorting_list',
			[
				'title' => esc_html__( 'Sorting List', 'jet-smart-filters' ),
				'tab'   => 'content',
			]
		);
	}

	public function register_filter_settings_controls() {
		$this->start_jet_control_group( 'section_sorting_list' );

		$repeater = new \Jet_Engine\Bricks_Views\Helpers\Repeater();

		$repeater->add_control(
			'title',
			[
				'label'          => esc_html__( 'Title', 'jet-smart-filters' ),
				'type'           => 'text',
				'hasDynamicData' => false,
			]
		);

		$repeater->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Order by', 'jet-smart-filters' ),
				'type'    => 'select',
				'options' => jet_smart_filters()->filter_types->get_filter_types( $this->jet_element_render )->orderby_options(),
			]
		);

		$repeater->add_control(
			'meta_key',
			[
				'label'          => esc_html__( 'Key', 'jet-smart-filters' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'required'       => [ 'orderby', '=', [ 'meta_value', 'meta_value_num', 'clause_value' ] ],
			]
		);

		$repeater->add_control(
			'order',
			[
				'label'    => esc_html__( 'Order', 'jet-smart-filters' ),
				'type'     => 'select',
				'options'  => array(
					'ASC'  => esc_html__( 'ASC', 'jet-smart-filters' ),
					'DESC' => esc_html__( 'DESC', 'jet-smart-filters' )
				),
				'required' => [ 'orderby', '!=', [ 'none', 'rand' ] ],
			]
		);

		$this->register_jet_control(
			'sorting_list',
			[
				'tab'           => 'content',
				'label'         => esc_html__( 'Sorting list', 'jet-smart-filters' ),
				'type'          => 'repeater',
				'titleProperty' => 'title',
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'title'   => esc_html__( 'By title from lowest to highest', 'jet-smart-filters' ),
						'orderby' => 'title',
						'order'   => 'ASC'
					],
					[
						'title'   => esc_html__( 'By title from highest to lowest', 'jet-smart-filters' ),
						'orderby' => 'title',
						'order'   => 'DESC'
					],
					[
						'title'   => esc_html__( 'By date from lowest to highest', 'jet-smart-filters' ),
						'orderby' => 'date',
						'order'   => 'ASC'
					],
					[
						'title'   => esc_html__( 'By date from highest to lowest', 'jet-smart-filters' ),
						'orderby' => 'date',
						'order'   => 'DESC'
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	public function register_filter_style_group() {
		$this->register_jet_control_group(
			'section_content_style',
			[
				'title'    => esc_html__( 'Content', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [ 'label_block', '=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_select_style',
			[
				'title' => esc_html__( 'Select', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_style_controls( $css_scheme = null ) {

		$this->start_jet_control_group( 'section_content_style' );

		$this->register_jet_control(
			'content_align_main_axis',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Align main axis', 'jet-smart-filters' ),
				'type'  => 'align-items',
				'css'   => [
					[
						'property' => 'align-items',
						'selector' => $css_scheme['filter'],
					],
				],
			]
		);

		$this->register_jet_control(
			'content_align_cross_axis',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Align cross axis', 'jet-smart-filters' ),
				'type'  => 'justify-content',
				'css'   => [
					[
						'property' => 'justify-content',
						'selector' => $css_scheme['filter'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_select_style' );

		$this->register_jet_control(
			'select_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Select width', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'max-width',
						'selector' => $css_scheme['select'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	public function register_filter_label_group() {
		$this->register_jet_control_group(
			'section_label_style',
			[
				'title'    => esc_html__( 'Label', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [ 'label', '!=', '' ],
			]
		);
	}

	// Render element HTML
	public function render() {
		jet_smart_filters()->set_filters_used();

		$settings = $this->parse_jet_render_attributes( $this->get_jet_settings() );
		$provider = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';

		// STEP: Content provider is empty: Show placeholder text
		if ( empty( $provider ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select content provider to show.', 'jet-smart-filters' )
				]
			);
		}

		if ( empty( $settings['apply_type'] ) ) {
			$settings['apply_type'] = 'ajax';
		}

		if ( empty( $settings['apply_on'] ) ) {
			$settings['apply_on'] = 'value';
		}

		$sorting_filter_type = jet_smart_filters()->filter_types->get_filter_types( $this->jet_element_render );
		$sorting_options     = $sorting_filter_type->sorting_options( $settings['sorting_list'] );
		$container_data_atts = $sorting_filter_type->container_data_atts( $settings );
		$placeholder         = ! empty( $settings['placeholder'] ) ? $settings['placeholder'] : esc_html__( 'Sort...', 'jet-smart-filters' );
		$label               = ! empty( $settings['label'] ) ? $settings['label'] : '';

		if ( empty( $settings['apply_button_text'] ) ) {
			$settings['apply_button_text'] = '';
		}

		echo "<div {$this->render_attributes( '_root' )}>";

		include jet_smart_filters()->get_template( 'filters/sorting.php' );
		include jet_smart_filters()->get_template( 'common/apply-filters.php' );

		echo "</div>";

	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$attrs['label_block'] = $attrs['label_block'] ?? false;

		return $attrs;
	}
}