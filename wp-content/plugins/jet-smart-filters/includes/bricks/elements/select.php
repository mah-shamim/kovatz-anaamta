<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

// If this file is called directly, abort.
use Bricks\Database;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Select extends Jet_Smart_Filters_Bricks_Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-select'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-select-filter'; // Themify icon font class
	public $css_selector = '.jet-select__control'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'select';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Select Filter', 'jet-smart-filters' );
	}

	/**
	 * Register filter style controls. Specific for each widget.
	 *
	 * @return void
	 */
	public function register_filter_style_group() {
		$this->register_jet_control_group(
			'section_select_style',
			[
				'title' => esc_html__( 'Select', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_style_controls() {
		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/select/css-scheme',
			[
				'filter' => '.jet-select',
				'select' => '.jet-select__control',
			]
		);

		$this->start_jet_control_group( 'section_select_style' );

		$this->register_jet_control(
			'content_position',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Direction', 'jet-smart-filters' ),
				'type'    => 'select',
				'options' => [
					'flex'  => esc_html__( 'Line', 'jet-smart-filters' ),
					'block' => esc_html__( 'Columns', 'jet-smart-filters' ),
				],
				'default' => 'block',
				'css'     => [
					[
						'property' => 'display',
						'selector' => '.jet-smart-filters-select' . ', .jet-smart-filters-hierarchy ' . $css_scheme['filter'],
					],
				],
			]
		);

		$this->register_jet_control(
			'content_alignment',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'     => 'align-items',
				'css'      => [
					[
						'property' => 'align-items',
						'selector' => '.jet-smart-filters-select' . ', .jet-smart-filters-hierarchy ' . $css_scheme['filter'],
					],
				],
				'required' => [ 'content_position', '=', 'flex' ],
			]
		);

		$this->register_jet_control(
			'select_width',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Select width', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'css'     => [
					[
						'property' => 'max-width',
						'selector' => '.jet-smart-filters-select ' . $css_scheme['filter'] . ', .jet-smart-filters-hierarchy ' . $css_scheme['select'],
					],
					[
						'property' => 'flex-basis',
						'selector' => '.jet-smart-filters-select ' . $css_scheme['filter'] . ', .jet-smart-filters-hierarchy ' . $css_scheme['select'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}
}