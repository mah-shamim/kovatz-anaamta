<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Color_Image extends Jet_Smart_Filters_Bricks_Base_Checkbox {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-color-image'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-color-image-filter'; // Themify icon font class
	public $css_selector = '.jet-color-image-list__button'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'color-image';

	public $checkbox_icon = false;

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Visual Filter', 'jet-smart-filters' );
	}

	public function register_filter_settings_group() {
		$this->register_jet_control_group(
			'section_display_options',
			[
				'title' => esc_html__( 'Filter options', 'jet-smart-filters' ),
				'tab'   => 'content',
			]
		);

		$this->register_checkbox_group();
	}

	public function register_filter_settings_controls() {
		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/color-image/css-scheme',
			array(
				'item'             => '.jet-color-image-list__row',
				'item-checked'     => '.jet-color-image-list__input:checked ~ .jet-color-image-list__button',
				'button'           => '.jet-color-image-list__button',
				'label'            => '.jet-color-image-list__label',
				'checkbox'         => '.jet-color-image-list__decorator > *',
				'checkbox-checked' => '.jet-color-image-list__input:checked ~ .jet-color-image-list__button .jet-color-image-list__decorator',
				'list-item'        => '.jet-color-image-list__row',
				'list-wrapper'     => '.jet-color-image-list-wrapper > fieldset',
				'list-children'    => '.jet-list-tree__children',
			)
		);

		$this->start_jet_control_group( 'section_display_options' );

		$this->register_jet_control(
			'show_items_label',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show items label', 'jet-smart-filters' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'filter_image_size',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Image size', 'jet-smart-filters' ),
				'type'    => 'select',
				'options' => jet_smart_filters()->utils->get_image_sizes(),
				'default' => 'full',
			]
		);

		$this->end_jet_control_group();

		$this->register_checkbox_controls( $css_scheme );
	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$attrs['show_items_label'] = $attrs['show_items_label'] ?? false;

		return $attrs;
	}
}