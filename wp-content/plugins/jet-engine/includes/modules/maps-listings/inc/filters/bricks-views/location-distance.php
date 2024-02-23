<?php

namespace Jet_Engine\Modules\Maps_Listings\Filters\Bricks;

use Jet_Engine\Bricks_Views\Helpers;
use Jet_Engine\Modules\Maps_Listings\Filters\Types\Location_Distance_Render;
use Jet_Smart_Filters\Bricks_Views\Elements\Jet_Smart_Filters_Bricks_Base;

class Location_Distance extends Jet_Smart_Filters_Bricks_Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-location-distance'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-location-distance'; // Themify icon font class
	public $css_selector = '.jsf-location-distance__location-input, .jsf-location-distance__distance'; // Default CSS selector
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'location-distance';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Location and Distance', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {

		$this->register_general_group();
		$this->register_filter_settings_group();
		$this->register_filter_style_group();

	}

	// Set builder controls
	public function set_controls() {

		$this->register_general_controls();
		$this->register_filter_settings_controls();
		$this->register_filter_style_controls();

	}

	public function register_general_controls() {

		$this->start_jet_control_group( 'section_general' );

		$query_builder_link = admin_url( 'admin.php?page=jet-engine-query' );

		$this->register_jet_control(
			'query_notice',
			[
				'content' => sprintf( __( 'This filter is compatible only with queries from <a href="%s" target="_blank">JetEngine Query Builder</a>. ALso you need to set up <a href="https://crocoblock.com/knowledge-base/jetsmartfilters/location-distance-filter-overview/" target="_blank">Geo Query</a> in your query settings to meke filter to work correctly.', 'jet-engine' ), $query_builder_link ),
				'type' => 'info',
			]
		);

		$this->register_jet_control(
			'filter_id',
			[
				'label'       => esc_html__( 'Select filter', 'jet-engine' ),
				'type'        => 'select',
				'options'     => jet_smart_filters()->data->get_filters_by_type( $this->jet_element_render ),
				'multiple'    => $this->filter_id_multiple,
				'searchable'  => true,
				'placeholder' => esc_html__( 'Select...', 'jet-engine' ),
			]
		);

		if ( jet_smart_filters()->get_version() > '3.0.4' ) {
			$provider_allowed = \Jet_Smart_Filters\Bricks_Views\Manager::get_allowed_providers();
		} else {
			$provider_allowed = [
				'jet-engine'          => true,
				'jet-engine-calendar' => true,
				'jet-engine-maps'     => true,
			];
		}

		$this->register_jet_control(
			'content_provider',
			[
				'tab'        => 'content',
				'label'      => esc_html__( 'This filter for', 'jet-engine' ),
				'type'       => 'select',
				'options'    => Helpers\Options_Converter::filters_options_by_key( jet_smart_filters()->data->content_providers(), $provider_allowed ),
				'searchable' => true,
			]
		);

		$this->register_jet_control(
			'epro_posts_notice',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Please set <b>jet-smart-filters</b> into Query ID option of Posts widget you want to filter', 'jet-engine' ),
				'type'     => 'info',
				'required' => [ 'content_provider', '=', [ 'epro-posts', 'epro-portfolio' ] ],
			]
		);

		$this->register_jet_control(
			'apply_type',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Apply type', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'ajax'   => esc_html__( 'AJAX', 'jet-engine' ),
					'reload' => esc_html__( 'Page reload', 'jet-engine' ),
					'mixed'  => esc_html__( 'Mixed', 'jet-engine' ),
				],
				'default' => 'ajax',
			]
		);

		$this->register_jet_control(
			'apply_on',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Apply on', 'jet-engine' ),
				'type'     => 'select',
				'options'  => [
					'value'  => esc_html__( 'Value change', 'jet-engine' ),
					'submit' => esc_html__( 'Click on apply button', 'jet-engine' ),
				],
				'default'  => 'value',
				'required' => [ 'apply_type', '=', [ 'ajax', 'mixed' ] ],
			]
		);

		$this->register_jet_control(
			'placeholder',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Placeholder', 'jet-engine' ),
				'type'           => 'text',
				'placeholder'    => esc_html__( 'Enter your location...', 'jet-engine' ),
				'default'        => esc_html__( 'Enter your location...', 'jet-engine' ),
				'hasDynamicData' => false,
				'description'    => esc_html__( 'Placeholder text for the location input', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'geolocation_placeholder',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Text for user geolocation control', 'jet-engine' ),
				'type'           => 'text',
				'placeholder'    => esc_html__( 'Your current location', 'jet-engine' ),
				'default'        => esc_html__( 'Your current location', 'jet-engine' ),
				'hasDynamicData' => false,
				'description'    => esc_html__( 'This text used for User Geolocation icon tooltip and as location input value, when User Geolocation is used', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'query_id',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Query ID', 'jet-engine' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'description'    => esc_html__( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'jet-engine' ),
			]
		);

		// Include Additional Providers Settings
		include jet_smart_filters()->plugin_path( 'includes/bricks/elements/common-controls/additional-providers.php' );

		$this->end_jet_control_group();

	}

	public function register_filter_settings_group() {

		$this->register_jet_control_group(
			'section_distance_list',
			[
				'title' => esc_html__( 'Distance List', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

	}

	public function register_filter_settings_controls() {

		$this->start_jet_control_group( 'section_distance_list' );

		$this->register_jet_control(
			'distance_units',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Distance Units', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'km' => __( 'Kilometers', 'jet-engine' ),
					'mi' => __( 'Miles', 'jet-engine' ),
				],
				'default' => 'km',
			]
		);

		$repeater = new Helpers\Repeater();

		$repeater->add_control(
			'distance',
			[
				'label'   => esc_html__( 'Distance', 'jet-engine' ),
				'type'    => 'number',
				'min'     => 1,
				'max'     => 1000,
				'default' => 50
			]
		);

		$this->register_jet_control(
			'distance_list',
			[
				'tab'           => 'content',
				'label'         => esc_html__( 'Distance list', 'jet-engine' ),
				'type'          => 'repeater',
				'titleProperty' => 'distance',
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'distance' => 5,
					],
					[
						'distance' => 10,
					],
					[
						'distance' => 25,
					],
					[
						'distance' => 50,
					],
					[
						'distance' => 100,
					],
				],
			]
		);

		$this->end_jet_control_group();

	}

	public function register_filter_style_group() {

		$this->register_jet_control_group(
			'section_content_style',
			[
				'title' => esc_html__( 'Location & Distance Inputs', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_location_controls_style',
			[
				'title' => esc_html__( 'More Location Controls', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

	}

	public function register_filter_style_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/location-distance/css-scheme',
			[
				'distance'                        => '.jsf-location-distance',
				'distance-location'               => '.jsf-location-distance__location',
				'distance-location-icon'          => '.jsf-location-distance__location-icon',
				'distance-location-icon-path'     => '.jsf-location-distance__location-icon path',
				'distance-location-dropdown'      => '.jsf-location-distance__location-dropdown',
				'distance-location-dropdown-item' => '.jsf-location-distance__location-dropdown-item',
			]
		);

		$this->start_jet_control_group( 'section_content_style' );

		$this->register_jet_control(
			'location_width',
			[
				'tab'         => 'style',
				'label'       => esc_html__( 'Location Input Width', 'jet-engine' ),
				'type'        => 'number',
				'units'       => true,
				'min'         => 10,
				'max'         => 95,
				'default'     => '80%',
				'css'         => [
					[
						'property' => 'flex-basis',
						'selector' => $css_scheme['distance-location'],
					]
				],
				'description' => esc_html__( 'Distance control will automatically fill the rest of space in the line', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'location_distance_gap',
			[
				'tab'         => 'style',
				'label'       => esc_html__( 'Gap', 'jet-engine' ),
				'type'        => 'number',
				'units'       => true,
				'min'         => 1,
				'max'         => 50,
				'default'     => 10,
				'css'         => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['distance'],
					]
				],
				'description' => esc_html__( 'Gap between location and distance inputs', 'jet-engine' ),
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_location_controls_style' );

		$this->register_jet_control(
			'location_control_icons',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Icons', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'location_icons_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['distance-location-icon-path'],
					],
				],
			]
		);

		$this->register_jet_control(
			'location_icons_opacity',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Opacity', 'jet-engine' ) . ' (%)',
				'type'    => 'number',
				'unit'    => '%',
				'min'     => 20,
				'max'     => 100,
				'default' => 50,
				'css'     => [
					[
						'property' => 'opacity',
						'selector' => $css_scheme['distance-location-icon'],
					]
				],
			]
		);

		$this->register_jet_control(
			'locations_dropdown',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Locations Dropdown', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'location_dropdown_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['distance-location-dropdown'],
					],
				],
			]
		);

		$this->register_jet_control(
			'location_dropdown_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['distance-location-dropdown'],
					],
				],
			]
		);

		$this->register_jet_control(
			'location_dropdown_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['distance-location-dropdown-item'],
					],
				],
			]
		);

		$this->register_jet_control(
			'location_dropdown_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box shadow', 'jet-engine' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['distance-location-dropdown'],
					],
				],
			]
		);

		$this->end_jet_control_group();

	}

	// Render element HTML
	public function render() {

		$settings        = $this->parse_jet_render_attributes( $this->get_jet_settings() );
		$render_instance = new Location_Distance_Render();

		echo "<div {$this->render_attributes( '_root' )}>";

		$render_instance->render( $settings, $this->name );

		echo "</div>";

	}
}