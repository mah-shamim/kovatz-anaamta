<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Radio extends Jet_Smart_Filters_Bricks_Base_Checkbox {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-radio'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-radio-filter'; // Themify icon font class
	public $css_selector = '.jet-radio-list__button'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'radio';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Radio Filter', 'jet-smart-filters' );
	}

	/**
	 * Register filter settings controls. Specific for each widget.
	 *
	 * @return void
	 */
	public function register_filter_settings_group() {
		$this->register_checkbox_group();
	}

	public function register_filter_settings_controls() {
		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/radio/css-scheme',
			[
				'item'                  => '.jet-radio-list__row',
				'item-checked'          => '.jet-radio-list__input:checked ~ .jet-radio-list__button',
				'button'                => '.jet-radio-list__button',
				'label'                 => '.jet-radio-list__label',
				'checkbox'              => '.jet-radio-list__decorator',
				'checkbox-checked'      => '.jet-radio-list__input:checked ~ .jet-radio-list__button .jet-radio-list__decorator',
				'checkbox-checked-icon' => '.jet-radio-list__checked-icon',
				'list-item'             => '.jet-radio-list__row',
				'list-wrapper'          => '.jet-radio-list-wrapper fieldset',
				'list-children'         => '.jet-list-tree__children',
			]
		);

		$this->register_checkbox_controls( $css_scheme );
	}
}