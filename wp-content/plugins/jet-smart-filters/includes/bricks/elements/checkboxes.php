<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Checkboxes extends Jet_Smart_Filters_Bricks_Base_Checkbox {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-checkboxes'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-checkboxes-filter'; // Themify icon font class
	public $css_selector = '.jet-checkboxes-list__button'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'checkboxes';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Checkboxes Filter', 'jet-smart-filters' );
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
			'jet-smart-filters/widgets/checkboxes/css-scheme',
			[
				'item'                  => '.jet-checkboxes-list__row',
				'item-checked'          => '.jet-checkboxes-list__input:checked ~ .jet-checkboxes-list__button',
				'button'                => '.jet-checkboxes-list__button',
				'label'                 => '.jet-checkboxes-list__label',
				'checkbox'              => '.jet-checkboxes-list__decorator',
				'checkbox-checked'      => '.jet-checkboxes-list__input:checked ~ .jet-checkboxes-list__button .jet-checkboxes-list__decorator',
				'checkbox-checked-icon' => '.jet-checkboxes-list__checked-icon',
				'list-item'             => '.jet-checkboxes-list__row',
				'list-wrapper'          => '.jet-checkboxes-list-wrapper',
				'list-children'         => '.jet-list-tree__children',
			]
		);

		$this->register_checkbox_controls($css_scheme);
	}
}