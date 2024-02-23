<?php

namespace Elementor;

use ElementsKit_Lite\Libs\Framework\Attr;
use Elementor\ElementsKit_Widget_Google_Map_Handler as Handler;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Google_Map extends Widget_Base {

	public $base;

    public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
	}

	public function get_script_depends() {
		return ['ekit-google-map-api', 'ekit-google-gmaps'];
	}

	public function get_name() {
		return Handler::get_name();
	}

	public function get_title() {
		return Handler::get_title();
	}

	public function get_icon() {
		return Handler::get_icon();
	}

	public function get_categories() {
		return Handler::get_categories();
	}

	public function get_keywords() {
		return Handler::get_keywords();
	}

    public function get_help_url() {
        return 'https://wpmet.com/doc/google-map/';
    }

	protected function register_controls() {

		$this->start_controls_section( 'map_settings', [
			'label' => esc_html__( 'Settings', 'elementskit' )
		]);

		$this->add_control( 'map_type', [
			'label'       	=> esc_html__( 'Map Type', 'elementskit' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'basic',
			'options' 		=> [
				'basic'  	=> esc_html__( 'Basic', 'elementskit' ),
				'marker'  	=> esc_html__( 'Multiple Marker', 'elementskit' ),
				'static'  	=> esc_html__( 'Static', 'elementskit' ),
				'polyline'  => esc_html__( 'Polyline', 'elementskit' ),
				'polygon'  	=> esc_html__( 'Polygon', 'elementskit' ),
				'overlay'  	=> esc_html__( 'Overlay', 'elementskit' ),
				'routes'  	=> esc_html__( 'With Routes', 'elementskit' ),
				'panorama'  => esc_html__( 'Panorama', 'elementskit' ),
			]
		]);

		$this->add_control( 'map_address_type',
			[
				'label' => __( 'Address Type', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
                'default' => 'coordinates',
				'options' => [
					'address' => [
						'title' => __( 'Address', 'elementskit' ),
						'icon' => 'fa fa-map',
					],
					'coordinates' => [
						'title' => __( 'Coordinates', 'elementskit' ),
						'icon' => 'fa fa-map-marker',
					],
				],
			]
		);

	   	$this->add_control( 'map_addr',
			[
				'label' => esc_html__( 'Address', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( 'Dhaka, Bangladesh', 'elementskit' ),
				'condition' => [
					'map_address_type' => ['address']
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control( 'map_lat',
			[
				'label' => esc_html__( 'Latitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__( '23.749981', 'elementskit' ),
				'condition' => [
					'map_address_type' => ['coordinates']
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control( 'map_lng',
			[
				'label' => esc_html__( 'Longitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__( '90.365641', 'elementskit' ),
				'condition' => [
					'map_address_type' => ['coordinates']
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		// Start map type static
		// $this->add_control( 'map_static_lat',
		// 	[
		// 		'label' => esc_html__( 'Latitude', 'elementskit' ),
		// 		'type' => Controls_Manager::TEXT,
		// 		'label_block' => false,
		// 		'default' => esc_html__( '23.7808875', 'elementskit' ),
		// 		'condition' => [
		// 			'map_type' => ['static'],
		// 		]
		// 	]
		// );

		// $this->add_control( 'map_static_lng',
		// 	[
		// 		'label' => esc_html__( 'Longitude', 'elementskit' ),
		// 		'type' => Controls_Manager::TEXT,
		// 		'label_block' => false,
		// 		'default' => esc_html__( '90.2792373', 'elementskit' ),
		// 		'condition' => [
		// 			'map_type' => ['static'],
		// 		]
		// 	]
		// );

		$this->add_control( 'map_resolution_title',
			[
				'label' => esc_html__( 'Map Image Resolution', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'map_type' => 'static'
				]
			]
		);

		$this->add_control(
			'map_static_dimension_note',
			[
				'type'	=> Controls_Manager::RAW_HTML,
				'raw'	=> esc_html__('Static Maps images can be returned in any size up to 640 x 640 pixels. Google Maps Platform Premium Plan customers, who are correctly authenticating requests to the Maps Static API, can request images up to 2048 x 2048 pixels.', 'elementskit'),
				'content_classes'	=> 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => [
					'map_type' => 'static'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control( 'map_static_width',
			[
				'label' => esc_html__( 'Static Image Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 610
				],
				'range' => [
					'px' => [
						'max' => 1400,
					],
				],
				'condition' => [
					'map_type' => 'static'
				]
			]
		);

		$this->add_control( 'map_static_height',
			[
				'label' => esc_html__( 'Static Image Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [ 'size' => 300 ],
				'range' => [ 'px' => [ 'max' => 700 ] ],
				'condition' => [
					'map_type' => 'static'
				]
			]
		);
		// End map type static

		// Start map type panoroma
		$this->add_control( 'map_panorama_lat',
			[
				'label' => esc_html__( 'Latitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__( '23.7808875', 'elementskit' ),
				'condition' => [
					'map_type' => ['panorama'],
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control( 'map_panorama_lng',
			[
				'label' => esc_html__( 'Longitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__( '90.2792373', 'elementskit' ),
				'condition' => [
					'map_type' => ['panorama'],
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		// End map type panoroma

		$this->add_control( 'map_overlay_content',
			[
				'label' => __( 'Content', 'elementskit' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => __( 'Your content will goes here', 'elementskit' ),
				'condition' => [
					'map_type' => 'overlay'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		
		$this->start_controls_section(
			'ekit_section_google_map_basic_marker_settings',
			[
				'label' => esc_html__( 'Marker Settings', 'elementskit' ),
				'condition' => [
					'map_type' => ['basic']
				]
			]
		);
		$this->add_control(
			'map_basic_marker_title',
			[
				'label' => esc_html__( 'Title', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( 'Google Map Title', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'map_basic_marker_content',
			[
				'label' => esc_html__( 'Content', 'elementskit' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default' => esc_html__( 'Google map content', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'map_basic_marker_icon_enable',
			[
				'label' => __( 'Custom Marker Icon', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => __( 'Yes', 'elementskit' ),
				'label_off' => __( 'No', 'elementskit' ),
				'return_value' => 'yes',
			]
		);
			$this->add_control(
			'map_basic_marker_icon',
			[
				'label' => esc_html__( 'Marker Icon', 'elementskit' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					// 'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'map_basic_marker_icon_enable' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'map_basic_marker_icon_width',
			[
				'label' => esc_html__( 'Marker Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 32
				],
				'range' => [
					'px' => [
						'max' => 150,
					],
				],
				'condition' => [
					'map_basic_marker_icon_enable' => 'yes'
				]
			]
		);
		$this->add_control(
			'map_basic_marker_icon_height',
			[
				'label' => esc_html__( 'Marker Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 32
				],
				'range' => [
					'px' => [
						'max' => 150,
					],
				],
				'condition' => [
					'map_basic_marker_icon_enable' => 'yes'
				]
			]
		);
		$this->end_controls_section();
			

		$this->start_controls_section(
				'ekit_section_google_map_marker_settings',
				[
					'label' => esc_html__( 'Marker Settings', 'elementskit' ),
					'condition' => [
						'map_type' => ['marker', 'polyline', 'routes', 'static']
					]
				]
		);

		$map_marker_repeater = new Repeater();

		$map_marker_repeater->add_control(
			'map_marker_lat',
			[
				'label' => esc_html__( 'Latitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( '28.948790', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$map_marker_repeater->add_control(
			'map_marker_lng',
			[
				'label' => esc_html__( 'Longitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( '-81.298843', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$map_marker_repeater->add_control(
			'map_marker_title',
			[
				'label' => esc_html__( 'Tooltrip', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( 'Marker Title', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$map_marker_repeater->add_control(
			'map_marker_content',
			[
				'label' => esc_html__( 'Content', 'elementskit' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default' => esc_html__( 'Marker Content. You can put html here.', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$map_marker_repeater->add_control(
			'map_marker_icon_enable',
			[
				'label' => __( 'Use Custom Icon', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => __( 'Yes', 'elementskit' ),
				'label_off' => __( 'No', 'elementskit' ),
				'return_value' => 'yes',
			]
		);

		$map_marker_repeater->add_control(
			'map_marker_icon',
			[
				'label' => esc_html__( 'Custom Icon', 'elementskit' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [],
				'condition' => [
					'map_marker_icon_enable' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$map_marker_repeater->add_control(
			'map_marker_icon_width',
			[
				'label' => esc_html__( 'Icon Width', 'elementskit' ),
				'type' => Controls_Manager::NUMBER,
				'default' => esc_html__( '32', 'elementskit' ),
				'condition' => [
					'map_marker_icon_enable' => 'yes'
				]
			]
		);

		$map_marker_repeater->add_control(
			'map_marker_icon_height',
			[
				'label' => esc_html__( 'Icon Height', 'elementskit' ),
				'type' => Controls_Manager::NUMBER,
				'default' => esc_html__( '32', 'elementskit' ),
				'condition' => [
					'map_marker_icon_enable' => 'yes'
				]
			]
		);

		$this->add_control(
			'map_markers',
			[
				'type' => Controls_Manager::REPEATER,
				'seperator' => 'before',
				'default' => [
					[ 
						'map_marker_title' => esc_html__( 'Daffodil International University', 'elementskit' ),
						'map_marker_lat' => esc_html__( '23.754539', 'elementskit' ),
						'map_marker_lng' => esc_html__( '90.3769106', 'elementskit' ),
					],
					[ 
						'map_marker_title' => esc_html__( 'National Parliament House', 'elementskit' ),
						'map_marker_lat' => esc_html__( '23.7626233', 'elementskit' ),
						'map_marker_lng' => esc_html__( '90.3777502', 'elementskit' ),
					],
				],
				'fields' => $map_marker_repeater->get_controls(),
				'title_field' => '{{map_marker_title}}',
			]
		);
		$this->end_controls_section();


		
		$this->start_controls_section(
			'ekit_section_google_map_polyline_settings',
			[
				'label' => esc_html__( 'Coordinate Settings', 'elementskit' ),
				'condition' => [
					'map_type' => ['polyline', 'polygon']
				]
			]
		);

		$coordinate_repeater = new Repeater();

		$coordinate_repeater->add_control(
			'map_polyline_title',
			[
				'label' => esc_html__( 'Title', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( '#', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$coordinate_repeater->add_control(
			'map_polyline_lat',
			[
				'label' => esc_html__( 'Latitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( '28.948790', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$coordinate_repeater->add_control(
			'map_polyline_lng',
			[
				'label' => esc_html__( 'Longitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( '-81.298843', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'map_polylines',
			[
				'type' => Controls_Manager::REPEATER,
				'seperator' => 'before',
				'default' => [
					[ 
                        'map_polyline_title' => esc_html__( '#1', 'elementskit' ),
                        'map_polyline_lat' => esc_html__( '23.749981', 'elementskit' ),
                        'map_polyline_lng' => esc_html__( '90.365641', 'elementskit' ),
                    ],
					[ 
                        'map_polyline_title' => esc_html__( '#2', 'elementskit' ),
                        'map_polyline_lat' => esc_html__( '23.7416692', 'elementskit' ),
                        'map_polyline_lng' => esc_html__( '90.3622266', 'elementskit' ),
                    ],
					[ 
                        'map_polyline_title' => esc_html__( '#3', 'elementskit' ),
                        'map_polyline_lat' => esc_html__( '23.7514466', 'elementskit' ),
                        'map_polyline_lng' => esc_html__( '90.3967484', 'elementskit' ),
                    ],
				],
				'fields' => $coordinate_repeater->get_controls(),
				'title_field' => '{{map_polyline_title}}',
			]
		);

		$this->end_controls_section();

		
		$this->start_controls_section(
				'ekit_section_google_map_routes_settings',
				[
					'label' => esc_html__( 'Routes Coordinate Settings', 'elementskit' ),
					'condition' => [
						'map_type' => ['routes']
					]
				]
			);
		$this->add_control(
			'map_routes_origin',
			[
				'label' => esc_html__( 'Origin', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_control(
			'map_routes_origin_lat',
			[
				'label' => esc_html__( 'Latitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__( '28.948790', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'map_routes_origin_lng',
			[
				'label' => esc_html__( 'Longitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__( '-81.298843', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'map_routes_dest',
			[
				'label' => esc_html__( 'Destination', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_control(
			'map_routes_dest_lat',
			[
				'label' => esc_html__( 'Latitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__( '1.2833808', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'map_routes_dest_lng',
			[
				'label' => esc_html__( 'Longitude', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__( '103.8585377', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
				'map_routes_travel_mode',
				[
					'label'       	=> esc_html__( 'Travel Mode', 'elementskit' ),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'walking',
				'label_block' 	=> false,
				'options' 		=> [
					'walking'  	=> esc_html__( 'Walking', 'elementskit' ),
					'bicycling' => esc_html__( 'Bicycling', 'elementskit' ),
					'driving' 	=> esc_html__( 'Driving', 'elementskit' ),
				]
				]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_map_controls',
			[
				'label'	=> esc_html__( 'Controls', 'elementskit' )
			]
		);
		$this->add_control(
			'map_zoom',
			[
				'label' => esc_html__( 'Zoom Level', 'elementskit' ),
				'type' => Controls_Manager::NUMBER,
				'label_block' => false,
				'default' => esc_html__( '14', 'elementskit' ),
			]
		);
		$this->add_control(
			'ekit_map_streeview_control',
			[
				'label'                 => esc_html__( 'Street View Controls', 'elementskit' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'true',
				'label_on'              => __( 'On', 'elementskit' ),
				'label_off'             => __( 'Off', 'elementskit' ),
				'return_value'          => 'true',
			]
		);
		$this->add_control(
			'ekit_map_type_control',
			[
				'label'                 => esc_html__( 'Map Type Control', 'elementskit' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'On', 'elementskit' ),
				'label_off'             => __( 'Off', 'elementskit' ),
				'return_value'          => 'yes',
			]
		);

		$this->add_control(
			'ekit_map_zoom_control',
			[
				'label'                 => esc_html__( 'Zoom Control', 'elementskit' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'On', 'elementskit' ),
				'label_off'             => __( 'Off', 'elementskit' ),
				'return_value'          => 'yes',
			]
		);

		$this->add_control(
			'ekit_map_fullscreen_control',
			[
				'label'                 => esc_html__( 'Fullscreen Control', 'elementskit' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'On', 'elementskit' ),
				'label_off'             => __( 'Off', 'elementskit' ),
				'return_value'          => 'yes',
			]
		);

		$this->add_control(
			'ekit_map_scroll_zoom',
			[
				'label'                 => esc_html__( 'Scroll Wheel Zoom', 'elementskit' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'On', 'elementskit' ),
				'label_off'             => __( 'Off', 'elementskit' ),
				'return_value'          => 'yes',
			]
		);
		$this->end_controls_section();
			
		
		$this->start_controls_section(
			'ekit_section_google_map_theme_settings',
			[
				'label'		=> esc_html__( 'Theme', 'elementskit' ),
				'condition' => [
					'map_type!'	=> ['static', 'panorama']
				]
			]
		);
		$this->add_control(
			'map_theme_source',
			[
				'label'		=> __( 'Theme Source', 'elementskit' ),
				'type'		=> Controls_Manager::CHOOSE,
				'options' => [
					'gstandard' => [
						'title' => __( 'Google Standard', 'elementskit' ),
						'icon' => 'fa fa-map',
					],
					'snazzymaps' => [
						'title' => __( 'Snazzy Maps', 'elementskit' ),
						'icon' => 'fa fa-map-marker',
					],
					'custom' => [
						'title' => __( 'Custom', 'elementskit' ),
						'icon' => 'fa fa-edit',
					],
				],
				'default'	=> 'gstandard'
			]
		);
		$this->add_control(
			'map_gstandards',
			[
				'label'                 => esc_html__( 'Google Themes', 'elementskit' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'standard',
				'options'               => [
					'standard'     => __( 'Standard', 'elementskit' ),
					'silver'       => __( 'Silver', 'elementskit' ),
					'retro'        => __( 'Retro', 'elementskit' ),
					'dark'         => __( 'Dark', 'elementskit' ),
					'night'        => __( 'Night', 'elementskit' ),
					'aubergine'    => __( 'Aubergine', 'elementskit' )
				],
				'description'           => sprintf( '<a href="https://mapstyle.withgoogle.com/" target="_blank">%1$s</a> %2$s',__( 'Click here', 'elementskit' ), __( 'to generate your own theme and use JSON within Custom style field.', 'elementskit' ) ),
				'condition'	=> [
					'map_theme_source'	=> 'gstandard'
				]
			]
		);
		$this->add_control(
			'map_snazzymaps',
			[
				'label'                 => esc_html__( 'SnazzyMaps Themes', 'elementskit' ),
				'type'                  => Controls_Manager::SELECT,
				'label_block'			=> true,
				'default'               => 'colorful',
				'options'               => [
					'default'		=> __( 'Default', 'elementskit' ),
					'simple'		=> __( 'Simple', 'elementskit' ),
					'colorful'		=> __( 'Colorful', 'elementskit' ),
					'complex'		=> __( 'Complex', 'elementskit' ),
					'dark'			=> __( 'Dark', 'elementskit' ),
					'greyscale'		=> __( 'Greyscale', 'elementskit' ),
					'light'			=> __( 'Light', 'elementskit' ),
					'monochrome'	=> __( 'Monochrome', 'elementskit' ),
					'nolabels'		=> __( 'No Labels', 'elementskit' ),
					'twotone'		=> __( 'Two Tone', 'elementskit' )
				],
				'description'           => sprintf( '<a href="https://snazzymaps.com/explore" target="_blank">%1$s</a> %2$s',__( 'Click here', 'elementskit' ), __( 'to explore more themes and use JSON within custom style field.', 'elementskit' ) ),
				'condition'	=> [
					'map_theme_source'	=> 'snazzymaps'
				]
			]
		);
		$this->add_control(
			'map_custom_style',
			[
				'label'                 => __( 'Custom Style', 'elementskit' ),
				'description'           => sprintf( '<a href="https://mapstyle.withgoogle.com/" target="_blank">%1$s</a> %2$s',__( 'Click here', 'elementskit' ), __( 'to get JSON style code to style your map', 'elementskit' ) ),
				'type'                  => Controls_Manager::TEXTAREA,
				'condition'             => [
					'map_theme_source'     => 'custom',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->end_controls_section(); 
		
		
		$this->start_controls_section(
			'map_style_settings',
			[
				'label' => esc_html__( 'Map Container', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control( 'map_max_width',
			[
				'label' => __( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1140,
					'unit' => 'px',
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1400,
						'step' => 10,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-google-map' => 'max-width: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->add_responsive_control( 'map_max_height',
			[
				'label' => __( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 400,
					'unit' => 'px',
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1400,
						'step' => 10,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-google-map' => 'height: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control( 'map_alignment',
			[
				'label' => __( 'Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementskit' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
				]
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'map_overlay_style_settings',
			[
				'label' => esc_html__( 'Overlay Style', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'map_type' => ['overlay']
				]
			]
		);
		$this->add_responsive_control(
			'map_overlay_width',
			[
				'label' => __( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 200,
					'unit' => 'px',
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1100,
						'step' => 10,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-gmap-overlay' => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->add_control(
			'map_overlay_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .ekit-gmap-overlay' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'mapoverlay_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
						'{{WRAPPER}} .ekit-gmap-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'map_overlay_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
						'{{WRAPPER}} .ekit-gmap-overlay' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'map_overlay_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-gmap-overlay',
			]
		);
		$this->add_responsive_control(
			'map_overlay_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
						'{{WRAPPER}} .ekit-gmap-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'map_overlay_box_shadow',
				'selector' => '{{WRAPPER}} .ekit-gmap-overlay',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'map_overlay_typography',
				'selector' => '{{WRAPPER}} .ekit-gmap-overlay',
			]
		);
		$this->add_control(
			'map_overlay_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#222',
				'selectors' => [
					'{{WRAPPER}} .ekit-gmap-overlay' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();

		
		$this->start_controls_section(
			'ekit_section_google_map_stroke_style_settings',
			[
				'label' => esc_html__( 'Stroke Style', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'map_type' => ['polyline', 'polygon', 'routes']
				]
			]
		);
		$this->add_control(
			'map_stroke_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e23a47',
			]
		);
		$this->add_responsive_control(
			'map_stroke_opacity',
			[
				'label' => __( 'Opacity', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.8,
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0.2,
						'max' => 1,
						'step' => 0.1,
					]
				],
			]
		);
		$this->add_responsive_control(
			'map_stroke_weight',
			[
				'label' => __( 'Weight', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 4,
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
						'step' => 1,
					]
				],
			]
		);
		$this->add_control(
			'map_stroke_fill_color',
			[
				'label' => esc_html__( 'Fill Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e23a47',
				'condition' => [
					'map_type' => ['polygon']
				]
			]
		);
		$this->add_responsive_control(
			'map_stroke_fill_opacity',
			[
				'label' => __( 'Fill Opacity', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.4,
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0.2,
						'max' => 1,
						'step' => 0.1,
					]
				],
				'condition' => [
					'map_type' => ['polygon']
				]
			]
		);
		$this->end_controls_section();


	}

	protected function ekit_get_map_theme($settings) {

		if($settings['map_theme_source'] == 'custom') {
			return strip_tags($settings['map_custom_style']);
		}else {
			$themes = include('google-map-themes.php');
			if(isset($themes[$settings['map_theme_source']][$settings['map_gstandards']])) {
				return $themes[$settings['map_theme_source']][$settings['map_gstandards']];
			}elseif(isset($themes[$settings['map_theme_source']][$settings['map_snazzymaps']])) {
				return $themes[$settings['map_theme_source']][$settings['map_snazzymaps']];
			}else {
				return '';
			}
		}

	}

	protected function map_render_data_attributes( $settings ) {

		extract($settings);

		return [
			'data-map_type'						=> isset($map_type)							? esc_attr($map_type) 								:	'',
			'data-map_address_type'				=> isset($map_address_type)					? esc_attr($map_address_type)						:	'',
			'data-map_lat'						=> isset($map_lat)							? esc_attr($map_lat)								:	'',
			'data-map_lng'						=> isset($map_lng)							? esc_attr($map_lng)								:	'',
			'data-map_addr'						=> isset($map_addr)							? esc_attr($map_addr)								:	'',
			'data-map_basic_marker_title'		=> isset($map_basic_marker_title)			? esc_attr($map_basic_marker_title)					:	'',
			'data-map_basic_marker_content'		=> isset($map_basic_marker_content)			? esc_attr($map_basic_marker_content)				:	'',
			'data-map_basic_marker_icon_enable'	=> isset($map_basic_marker_icon_enable)		? esc_attr($map_basic_marker_icon_enable)			:	'',
			'data-map_basic_marker_icon'		=> isset($map_basic_marker_icon) 			? esc_attr($map_basic_marker_icon['url']) 			:	'',
			'data-map_basic_marker_icon_width'	=> isset($map_basic_marker_icon_width) 		? esc_attr($map_basic_marker_icon_width['size'])	:	'',
			'data-map_basic_marker_icon_height'	=> isset($map_basic_marker_icon_height)		? esc_attr($map_basic_marker_icon_height['size'])	:	'',
			'data-map_zoom'						=> isset($map_zoom)							? esc_attr($map_zoom)								:	'',
			'data-map_marker_content'			=> isset($map_marker_content)				? esc_attr($map_marker_content) 					:	'',
			'data-map_static_width'				=> isset($map_static_width)					? esc_attr($map_static_width['size'])				:	'',
			'data-map_static_height'			=> isset($map_static_height)				? esc_attr($map_static_height['size'])				:	'',
			//'data-map_static_lat'				=> isset($map_static_lat)					? esc_attr($map_static_lat)							:	'',
			//'data-map_static_lng'				=> isset($map_static_lng)					? esc_attr($map_static_lng)							:	'',
			'data-map_stroke_color'				=> isset($map_stroke_color)					? esc_attr($map_stroke_color)						:	'',
			'data-map_stroke_opacity'			=> isset($map_stroke_opacity)				? esc_attr($map_stroke_opacity['size'])				:	'',
			'data-map_stroke_weight'			=> isset($map_stroke_weight)				? esc_attr($map_stroke_weight['size'])				:	'',
			'data-map_stroke_fill_color'		=> isset($map_stroke_fill_color)			? esc_attr($map_stroke_fill_color)					:	'',
			'data-map_stroke_fill_opacity'		=> isset($map_stroke_fill_opacity)			? esc_attr($map_stroke_fill_opacity['size'])		:	'',
			'data-map_overlay_content'			=> isset($map_overlay_content)				? esc_attr($map_overlay_content)					:	'',
			'data-map_routes_origin_lat'		=> isset($map_routes_origin_lat)			? esc_attr($map_routes_origin_lat)					:	'',
			'data-map_routes_origin_lng'		=> isset($map_routes_origin_lng)			? esc_attr($map_routes_origin_lng)					:	'',
			'data-map_routes_dest_lat'			=> isset($map_routes_dest_lat)				? esc_attr($map_routes_dest_lat)					:	'',
			'data-map_routes_dest_lng'			=> isset($map_routes_dest_lng)				? esc_attr($map_routes_dest_lng)					:	'',
			'data-map_routes_travel_mode'		=> isset($map_routes_travel_mode)			? esc_attr($map_routes_travel_mode)					:	'',
			'data-map_panorama_lat'				=> isset($map_panorama_lat)					? esc_attr($map_panorama_lat)						:	'',
			'data-map_panorama_lng'				=> isset($map_panorama_lng)					? esc_attr($map_panorama_lng)						:	'',

			'data-map_theme'					=> urlencode(json_encode($this->ekit_get_map_theme($settings))),
			'data-map_markers'					=> urlencode(json_encode($map_markers)),
			'data-map_polylines'				=> urlencode(json_encode($map_polylines)),

			'data-map_streeview_control'		=> isset($ekit_map_streeview_control) 	&& $ekit_map_streeview_control 			? 'true': 'false',
			'data-map_type_control'				=> isset($ekit_map_type_control) 		&& $ekit_map_type_control 				? 'true': 'false',
			'data-map_zoom_control'				=> isset($ekit_map_zoom_control) 		&& $ekit_map_zoom_control 				? 'true': 'false',
			'data-map_fullscreen_control'		=> isset($ekit_map_fullscreen_control) 	&& $ekit_map_fullscreen_control 		? 'true': 'false',
			'data-map_scroll_zoom'				=> isset($ekit_map_scroll_zoom) 		&& $ekit_map_scroll_zoom 				? 'true': 'false'
		];
	}

	protected function get_map_render_data_attribute_string($settings) {

		$data_attributes = $this->map_render_data_attributes($settings);
		$data_string = '';

		foreach( $data_attributes as $key => $value ) {
			if( isset($key) && ! empty($value)) {
				$data_string .= ' '.$key.'="'.$value.'"';
			}
		}
		return $data_string;
	}

	protected function get_alignment( $align ){
		if( $align == 'left' ) { return ''; }
		return $align == 'center' 
			? esc_attr('margin-left:auto;margin-right:auto;text-align:center;' )
			: esc_attr('margin-left:auto;margin-right:0;text-align:right;');
	}
    
    protected function render() {
		echo '<div class="ekit-wid-con" >';
		$this->render_raw();
		echo '</div>';
	}

	protected function render_raw() {

		$settings = $this->get_settings_for_display();
        $user_data = Attr::instance()->utils->get_option('user_data', []);

		$hasApiKey = !empty($user_data['google_map']) && '' != $user_data['google_map']['api_key'];

		$this->add_render_attribute( 'map_wrap', [
			'class'					=> ['ekit-google-map'],
			'id'					=> 'ekit-google-map-'.esc_attr($this->get_id()),
			'data-id'				=> esc_attr($this->get_id()),
			'data-api_key'			=> $hasApiKey ? esc_attr($user_data['google_map']['api_key']) : '',
			'style'					=> $this->get_alignment($settings['map_alignment'])
		]);
	?>

	<?php if( ! empty($settings['map_type']) ) : ?>
		<div <?php echo $this->get_render_attribute_string('map_wrap'), $this->get_map_render_data_attribute_string($settings); ?>></div>
	<?php endif; ?>
		<div class="google-map-notice"></div>
	<?php

	}

}
