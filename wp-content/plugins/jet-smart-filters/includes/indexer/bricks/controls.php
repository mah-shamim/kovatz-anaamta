<?php
/**
 * Jet Smart Filters Indexer Controls class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( class_exists( 'Jet_Smart_Filters_Bricks_Indexer_Controls' ) ) {
	return;
}

/**
 * Define Jet_Smart_Filters_Bricks_Indexer_Controls class
 */
class Jet_Smart_Filters_Bricks_Indexer_Controls {
	/**
	 * Constructor for the class
	 */

	public function __construct() {
		$indexed_filters = [
			'select'      => false,
			'checkboxes'  => true,
			'radio'       => true,
			'check-range' => true,
			'color-image' => true,
		];

		foreach ( $indexed_filters as $key => $value ) {
			add_filter( "bricks/elements/jet-smart-filters-$key/control_groups", [ $this, 'add_widget_control_groups' ] );
			add_filter( "bricks/elements/jet-smart-filters-$key/controls", [ $this, 'add_widget_controls' ] );

			if ( $value ) {
				add_filter( "bricks/elements/jet-smart-filters-$key/control_groups", [ $this, 'add_widget_style_control_groups' ] );
				add_filter( "bricks/elements/jet-smart-filters-$key/controls", [ $this, 'add_widget_style_controls' ] );
			}
		}
	}

	/**
	 * Add control groups to widgets
	 */
	public function add_widget_control_groups( $control_groups = array() ) {
		$control_groups['section_indexer_options'] = [
			'tab'   => 'content',
			'title' => esc_html__( 'Indexer Options', 'jet_smart_filters' ),
		];

		return $control_groups;
	}

	/**
	 * Add controls to widgets
	 */
	public function add_widget_controls( $controls = array() ) {
		$controls['apply_indexer'] = [
			'tab'   => 'content',
			'group' => 'section_indexer_options',
			'label' => esc_html__( 'Apply indexer', 'jet_smart_filters' ),
			'type'  => 'checkbox'
		];

		$controls['show_counter'] = [
			'tab'      => 'content',
			'group'    => 'section_indexer_options',
			'label'    => esc_html__( 'Show counter', 'jet_smart_filters' ),
			'type'     => 'checkbox',
			'required' => [ 'apply_indexer', '=', true ],
		];

		$controls['counter_prefix'] = [
			'tab'            => 'content',
			'group'          => 'section_indexer_options',
			'label'          => esc_html__( 'Counter prefix', 'jet_smart_filters' ),
			'type'           => 'text',
			'hasDynamicData' => false,
			'default'        => '(',
			'required'       => [
				[ 'apply_indexer', '=', true ],
				[ 'show_counter', '=', true ],
			],
		];

		$controls['counter_suffix'] = [
			'tab'            => 'content',
			'group'          => 'section_indexer_options',
			'label'          => esc_html__( 'Counter suffix', 'jet_smart_filters' ),
			'type'           => 'text',
			'hasDynamicData' => false,
			'default'        => ')',
			'required'       => [
				[ 'apply_indexer', '=', true ],
				[ 'show_counter', '=', true ],
			],
		];

		$controls['show_items_rule'] = [
			'tab'      => 'content',
			'group'    => 'section_indexer_options',
			'label'    => esc_html__( 'If item empty', 'jet_smart_filters' ),
			'type'     => 'select',
			'options'  => [
				'show'    => 'Show',
				'hide'    => 'Hide',
				'disable' => 'Disable',
			],
			'default'  => 'show',
			'required' => [ 'apply_indexer', '=', true ],
		];

		$controls['change_items_rule'] = [
			'tab'      => 'content',
			'group'    => 'section_indexer_options',
			'label'    => esc_html__( 'Change counters', 'jet_smart_filters' ),
			'type'     => 'select',
			'options'  => [
				'always'        => 'Always',
				'never'         => 'Never',
				'other_changed' => 'Other Filters Changed',
			],
			'default'  => 'always',
			'required' => [ 'apply_indexer', '=', true ],
		];

		return $controls;
	}

	/**
	 * Add style control groups to widgets
	 */
	public function add_widget_style_control_groups( $control_groups = array() ) {
		$control_groups['section_counter_style'] = [
			'tab'      => 'style',
			'title'    => esc_html__( 'Counter', 'jet_smart_filters' ),
			'required' => [ 'show_counter', '=', true ],
		];

		return $control_groups;
	}

	/**
	 * Add style controls to widgets
	 */
	public function add_widget_style_controls( $controls = array() ) {
		$controls['counter_typography'] = [
			'tab'   => 'style',
			'group' => 'section_counter_style',
			'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'typography',
					'selector' => '.jet-filters-counter',
				],
			],
		];

		$controls['counter_checked_color'] = [
			'tab'   => 'style',
			'group' => 'section_counter_style',
			'label' => esc_html__( 'Checked color', 'jet-smart-filters' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.jet-checkboxes-list__input:checked ~ .jet-checkboxes-list__button .jet-filters-counter, .jet-radio-list__input:checked ~ .jet-radio-list__button .jet-filters-counter, .jet-color-image-list__input:checked ~ .jet-color-image-list__button .jet-filters-counter',
				],
			],
		];

		$controls['counter_gap'] = [
			'tab'     => 'style',
			'group'   => 'section_counter_style',
			'label'   => esc_html__( 'Gap', 'jet-smart-filters' ),
			'type'    => 'number',
			'units'   => true,
			'default' => '4px',
			'css'     => [
				[
					'property' => 'margin-left',
					'selector' => '.jet-filters-counter',
				],
			],
		];

		return $controls;
	}
}