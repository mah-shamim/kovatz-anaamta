<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Alphabet extends Jet_Smart_Filters_Bricks_Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-alphabet'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-alphabet-filter'; // Themify icon font class
	public $css_selector = '.jet-alphabet-list__button'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'alphabet';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Alphabet filter', 'jet-smart-filters' );
	}

	public function register_filter_style_group() {
		$this->register_jet_control_group(
			'section_items_style',
			[
				'title' => esc_html__( 'Items', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_item_style',
			[
				'title' => esc_html__( 'Item', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_style_controls() {
		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/alphabet/css-scheme',
			array(
				'list-wrapper' => '.jet-alphabet-list__wrapper > fieldset',
				'list-item'    => '.jet-alphabet-list__row',
				'item'         => '.jet-alphabet-list__item',
				'button'       => '.jet-alphabet-list__button',
			)
		);

		$this->start_jet_control_group( 'section_items_style' );

		$this->register_jet_control(
			'items_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['list-wrapper'],
					],
				],
			]
		);

		$this->register_jet_control(
			'items_align',
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
						'selector' => $css_scheme['list-wrapper'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_item_style' );

		$this->register_jet_control(
			'item_min_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Min width', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'min-width',
						'selector' => $css_scheme['button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'item_checked_heading',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Checked state', 'jet-smart-filters' ),
			]
		);

		$this->register_jet_control(
			'item_checked_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.jet-alphabet-list__input:checked ~ ' . $css_scheme['button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'item_checked_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-alphabet-list__input:checked ~ ' . $css_scheme['button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'item_checked_border_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => '.jet-alphabet-list__input:checked ~ ' . $css_scheme['button'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}
}