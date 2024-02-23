<?php

use Bricks\Database;
use Bricks\Helpers;
use Jet_Engine\Bricks_Views\Helpers\Options_Converter;

$this->register_jet_control(
	'additional_providers_enabled',
	[
		'tab'     => 'content',
		'label'   => esc_html__( 'Additional providers enabled', 'jet-smart-filters' ),
		'type'    => 'checkbox',
		'default' => false,
	]
);

$repeater = new \Jet_Engine\Bricks_Views\Helpers\Repeater();
$provider_allowed = \Jet_Smart_Filters\Bricks_Views\Manager::get_allowed_providers();

$repeater->add_control(
	'additional_provider_notice_cache_query_loop',
	[
		'tab'         => 'content',
		'type'        => 'info',
		'content'     => esc_html__( 'You have enabled the "Cache query loop" option.', 'jet-smart-filters' ),
		'description' => sprintf(
			esc_html__( 'This option will break the filters functionality. You can disable this option or use "JetEngine Query Builder" query type. Go to: %s > Cache query loop', 'jet-smart-filters' ),
			'<a href="' . Helpers::settings_url( '#tab-performance' ) . '" target="_blank">Bricks > ' . esc_html__( 'Settings', 'jet-smart-filters' ) . ' > Performance</a>'
		),
		'required'    => [
			[ 'additional_provider', '=', 'bricks-query-loop' ],
			[ 'cacheQueryLoops', '=', true, 'globalSettings' ],
		],
	]
);

$repeater->add_control(
	'additional_provider',
	[
		'label'      => esc_html__( 'Additional provider', 'jet-smart-filters' ),
		'type'       => 'select',
		'options'    => Options_Converter::filters_options_by_key( jet_smart_filters()->data->content_providers(), $provider_allowed ),
		'searchable' => true,
	]
);

$repeater->add_control(
	'additional_query_id',
	[
		'label' => esc_html__( 'Additional query ID', 'jet-smart-filters' ),
		'type'  => 'text',
	]
);

$this->register_jet_control(
	'additional_providers_list',
	[
		'tab'           => 'content',
		'label'         => esc_html__( 'Additional providers list', 'jet-smart-filters' ),
		'type'          => 'repeater',
		'titleProperty' => 'additional_provider',
		'fields'        => $repeater->get_controls(),
		'required'      => [ 'additional_providers_enabled', '=', true ],
	]
);