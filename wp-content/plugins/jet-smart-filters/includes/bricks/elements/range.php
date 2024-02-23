<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Range extends Jet_Smart_Filters_Bricks_Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-range'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-range-filter'; // Themify icon font class
	public $css_selector = '.jet-range__values'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'range';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Range Filter', 'jet-smart-filters' );
	}

	public function register_filter_style_group() {
		$this->register_jet_control_group(
			'section_slider_style',
			[
				'title' => esc_html__( 'Slider', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_inputs_style',
			[
				'title' => esc_html__( 'Inputs', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_style_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/range/css-scheme',
			array(
				'slider'           => '.jet-range__slider',
				'slider-track'     => '.jet-range__slider__track',
				'slider-range'     => '.jet-range__slider__track__range',
				'slider-input'     => '.jet-range__slider__input',
				'range-values'     => '.jet-range__values',
				'inputs'           => '.jet-range__inputs',
				'inputs-container' => '.jet-range__inputs__container',
				'input-group'      => '.jet-range__inputs__group',
				'input'            => '.jet-range__inputs__group input',
				'input-text'       => '.jet-range__inputs__group__text'
			)
		);

		$this->start_jet_control_group( 'section_slider_style' );

		$this->register_jet_control(
			'slider_stroke',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Stroke', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '4px',
				'css'     => [
					[
						'property' => 'height',
						'selector' => $css_scheme['slider-track'],
					],
				],
			]
		);

		$this->register_jet_control(
			'slider_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['slider-track'],
					]
				],
			]
		);

		$this->register_jet_control(
			'slider_range_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Range color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['slider-range'],
					]
				],
			]
		);

		$this->register_jet_control(
			'slider_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-smart-filters' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['slider-track'],
					],
				],
			]
		);

		$this->register_jet_control(
			'slider_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['slider'],
					],
				],
			]
		);

		$this->register_jet_control(
			'range_points_heading',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Range points', 'jet-smart-filters' ),
			]
		);

		$this->register_jet_control(
			'range_points_width',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Points size', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '16px',
				'css'     => [
					[
						'property' => 'height',
						'selector' => $css_scheme['slider-input'],
					],
					[
						'property' => 'width',
						'selector' => $css_scheme['slider-input'] . '::-webkit-slider-thumb',
					],
					[
						'property' => 'height',
						'selector' => $css_scheme['slider-input'] . '::-webkit-slider-thumb',
					],
					[
						'property' => 'width',
						'selector' => $css_scheme['slider-input'] . '::-moz-range-thumb',
					],
					[
						'property' => 'height',
						'selector' => $css_scheme['slider-input'] . '::-moz-range-thumb',
					],
					[
						'property' => 'width',
						'selector' => $css_scheme['slider-input'] . '::-ms-thumb',
					],
					[
						'property' => 'height',
						'selector' => $css_scheme['slider-input'] . '::-ms-thumb',
					],
				],
			]
		);

		$this->register_jet_control(
			'range_points_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['slider-input'] . '::-webkit-slider-thumb',
					],
					[
						'property' => 'background-color',
						'selector' => $css_scheme['slider-input'] . '::-moz-range-thumb',
					],
					[
						'property' => 'background-color',
						'selector' => $css_scheme['slider-input'] . '::-ms-thumb',
					],
				],
			]
		);

		$this->register_jet_control(
			'range_points_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-smart-filters' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['slider-input'] . '::-webkit-slider-thumb',
					],
					[
						'property' => 'border',
						'selector' => $css_scheme['slider-input'] . '::-moz-range-thumb',
					],
					[
						'property' => 'border',
						'selector' => $css_scheme['slider-input'] . '::-ms-thumb',
					],
				],
			]
		);

		$this->register_jet_control(
			'range_points_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['slider-input'] . '::-webkit-slider-thumb',
					],
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['slider-input'] . '::-moz-range-thumb',
					],
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['slider-input'] . '::-ms-thumb',
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_inputs_style' );

		$this->register_jet_control(
			'inputs_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Container width', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'max-width',
						'selector' => $css_scheme['inputs-container'],
					],
					[
						'property' => 'flex-basis',
						'selector' => $css_scheme['inputs-container'],
					],
				],
			]
		);

		$this->register_jet_control(
			'inputs_offset',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['inputs-container'],
					],
				],
			]
		);

		$this->register_jet_control(
			'inputs_alignment',
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
						'selector' => $css_scheme['inputs'],
					],
				],
			]
		);

		$this->register_jet_control(
			'input_heading',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Input', 'jet-smart-filters' ),
			]
		);

		$this->register_jet_control(
			'input_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['input-group'],
					],
				],
			]
		);

		$this->register_jet_control(
			'input_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['input-group'],
					],
				],
			]
		);

		$this->register_jet_control(
			'input_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['input'],
					],
				],
			]
		);

		$this->register_jet_control(
			'input_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-smart-filters' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['input-group'],
					],
				],
			]
		);

		$this->register_jet_control(
			'input_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['input-group'],
					],
				],
			]
		);

		$this->register_jet_control(
			'input_text_heading',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Prefix/Suffix', 'jet-smart-filters' ),
			]
		);

		$this->register_jet_control(
			'input_text_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['input-text'],
					],
				],
			]
		);

		$this->register_jet_control(
			'input_text_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['input-text'],
					],
				],
			]
		);

		$this->register_jet_control(
			'input_text_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['input-text'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}
}