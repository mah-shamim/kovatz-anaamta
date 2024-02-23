<?php

/**
 * Search controls
 */
$this->register_jet_control(
	'search_enabled',
	[
		'tab'     => 'content',
		'label'   => esc_html__( 'Search enabled', 'jet-smart-filters' ),
		'type'    => 'checkbox',
		'default' => false,
	]
);

$this->register_jet_control(
	'search_placeholder',
	[
		'tab'     => 'content',
		'label'   => esc_html__( 'Search placeholder', 'jet-smart-filters' ),
		'type'    => 'text',
		'default' => esc_html__( 'Search...', 'jet-smart-filters' ),
		'required' => [ 'search_enabled', '=', true ],
	]
);


/**
 * More Less controls
 */
$this->register_jet_control(
	'moreless_enabled',
	[
		'tab'     => 'content',
		'label'   => esc_html__( 'More/less enabled', 'jet-smart-filters' ),
		'type'    => 'checkbox',
		'default' => false,
	]
);

$this->register_jet_control(
	'less_items_count',
	[
		'tab'      => 'content',
		'label'    => esc_html__( 'Less items count', 'jet-smart-filters' ),
		'type'     => 'number',
		'min'      => 1,
		'max'      => 50,
		'default'  => 5,
		'required' => [ 'moreless_enabled', '=', true ],
	]
);

$this->register_jet_control(
	'more_text',
	[
		'tab'     => 'content',
		'label'   => esc_html__( 'More text', 'jet-smart-filters' ),
		'type'    => 'text',
		'default' => esc_html__( 'More', 'jet-smart-filters' ),
		'required' => [ 'moreless_enabled', '=', true ],
	]
);

$this->register_jet_control(
	'less_text',
	[
		'tab'     => 'content',
		'label'   => esc_html__( 'Less text', 'jet-smart-filters' ),
		'type'    => 'text',
		'default' => esc_html__( 'Less', 'jet-smart-filters' ),
		'required' => [ 'moreless_enabled', '=', true ],
	]
);


/**
 * Dropdown controls
 */
$this->register_jet_control(
	'dropdown_enabled',
	[
		'tab'     => 'content',
		'label'   => esc_html__( 'Dropdown enabled', 'jet-smart-filters' ),
		'type'    => 'checkbox',
		'default' => false,
	]
);

$this->register_jet_control(
	'dropdown_placeholder',
	[
		'tab'     => 'content',
		'label'   => esc_html__( 'Placeholder', 'jet-smart-filters' ),
		'type'    => 'text',
		'default' => esc_html__( 'Select some options', 'jet-smart-filters' ),
		'required' => [ 'dropdown_enabled', '=', true ],
	]
);

/**
 * Dropdown N Selected
 */
if ( $this->jet_element_render !== 'radio' ) {

	$this->register_jet_control(
		'dropdown_n_selected_enabled',
		[
			'tab'      => 'content',
			'label'    => esc_html__( 'N Selected', 'jet-smart-filters' ),
			'type'     => 'checkbox',
			'default'  => false,
			'required' => [ 'dropdown_enabled', '=', true ],
		]
	);

	$this->register_jet_control(
		'dropdown_n_selected_number',
		[
			'tab'         => 'content',
			'label'       => esc_html__( 'Number of Named Items', 'jet-smart-filters' ),
			'type'        => 'number',
			'min'         => 0,
			'default'     => 3,
			'placeholder' => 3,
			'required'    => [
				[ 'dropdown_enabled', '=', true ],
				[ 'dropdown_n_selected_enabled', '=', true ]
			],
		]
	);

	$this->register_jet_control(
		'dropdown_n_selected_text',
		[
			'tab'         => 'content',
			'label'       => esc_html__( 'Generic text', 'jet-smart-filters' ),
			'type'        => 'text',
			'description' => esc_html__( 'Use a {number} macro to display the number of other items', 'jet-smart-filters' ),
			'placeholder' => esc_html__( 'and {number} others', 'jet-smart-filters' ),
			'required' => [
				[ 'dropdown_enabled', '=', true ],
				[ 'dropdown_n_selected_enabled', '=', true ]
			],
		]
	);
}

/**
 * Scroll controls
 */
$this->register_jet_control(
	'scroll_enabled',
	[
		'tab'     => 'content',
		'label'   => esc_html__( 'Scroll enabled', 'jet-smart-filters' ),
		'type'    => 'checkbox',
		'default' => false,
	]
);

$this->register_jet_control(
	'scroll_height',
	[
		'tab'      => 'content',
		'label'    => esc_html__( 'Scroll height(px)', 'jet-smart-filters' ),
		'type'     => 'number',
		'min'      => 100,
		'max'      => 1000,
		'default'  => 290,
		'required' => [ 'scroll_enabled', '=', true ],
	]
);