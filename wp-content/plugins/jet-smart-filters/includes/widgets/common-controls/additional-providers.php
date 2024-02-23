<?php
namespace Elementor;

$this->add_control(
	'additional_providers_enabled',
	array(
		'label'        => esc_html__( 'Additional Providers Enabled', 'jet-smart-filters' ),
		'type'         => Controls_Manager::SWITCHER,
		'description'  => '',
		'label_on'     => esc_html__( 'Yes', 'jet-smart-filters' ),
		'label_off'    => esc_html__( 'No', 'jet-smart-filters' ),
		'return_value' => 'yes',
		'default'      => 'no',
	)
);

$repeater = new Repeater();

$repeater->add_control(
	'additional_provider',
	array(
		'label'       => __( 'Additional Provider', 'jet-smart-filters' ),
		'label_block' => true,
		'type'        => Controls_Manager::SELECT,
		'default'     => '',
		'options'     => jet_smart_filters()->data->content_providers(),
	)
);

$repeater->add_control(
	'additional_query_id',
	array(
		'label'       => esc_html__( 'Additional Query ID', 'jet-smart-filters' ),
		'label_block' => true,
		'type'        => Controls_Manager::TEXT,
	)
);

$this->add_control(
	'additional_providers_list',
	array(
		'label' => __( 'Additional Providers List', 'jet-smart-filters' ),
		'type'  => Controls_Manager::REPEATER,
		'fields' => $repeater->get_controls(),
		'title_field' => '{{ additional_provider + ( additional_query_id ? "/" + additional_query_id : "" ) }}',
		'condition'   => array(
			'additional_providers_enabled' => 'yes',
		),
	)
);