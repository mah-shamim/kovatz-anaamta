<?php

namespace Elementor;

defined('ABSPATH') || exit;

use Elementor\ElementsKit_Widget_Behance_Feed_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

class ElementsKit_Widget_Behance_Feed extends Widget_Base {

	public $base;

	public function __construct($data = [], $args = null) {

		parent::__construct($data, $args);

		wp_enqueue_script('ekit-behance-feed-script-handle', Handler::get_url() . 'assets/js/script.js', ['elementor-editor'], \ElementsKit_Lite::version(), true);

		$data['rest_url'] = get_rest_url();
		$data['nonce']    = wp_create_nonce('wp_rest');

		Utils::print_js_config('ekit-behance-feed-script-handle', 'behance_config', $data);
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
        return 'https://wpmet.com/doc/behance-feed/';
    }

	protected function format_column( $settings, $control_name ){
		$column = $settings[$control_name];
		if(isset($settings[$control_name.'_tablet'])){
			$splitted = explode('ekit-fb-col-',$settings[$control_name.'_tablet']);
			$column .= ' ekit-fb-col-tablet-' . $splitted[1];
		}
		if(isset($settings[$control_name.'_mobile'])){
			$splitted = explode('ekit-fb-col-',$settings[$control_name.'_mobile']);
			$column .= ' ekit-fb-col-mobile-' . $splitted[1];
		}
		return $column;
	}

    /**
	 * Convert number or array of number to dimension format
	 *
	 * @param number|array	$value		16 | [0, 0 , 16, 0 ]
	 * @param string		$unit		px | em | rem | % | vh | vw
	 * @param boolean		$linked		true | false
	 * @return array 		
	 *	[ 
	 *		'top' 		=> '16', 		'right' 	=> '16', 
	 *		'bottom' 	=> '16', 		'left' 		=> '16', 
	 *		'unit' 		=> 'px', 		'isLinked' 	=> true 
	 *	];
	 */
	 private function get_dimension( $value = 1, $unit = 'em', $linked = true ){
        $is_arr = is_array( $value );
        return [
			'top'      => strval($is_arr ? $value[0] : $value), 
			'right'    => strval($is_arr ? $value[1] : $value),
			'bottom'   => strval($is_arr ? $value[2] : $value), 
			'left'     => strval($is_arr ? $value[3] : $value),
            'unit'     => $unit, 'isLinked' =>  $linked,
        ];
    }

	private function controls_section( $config ){

		// New configs
		$section_config = [ 'label' => $config['label'] ];
		
		// Formatting configs
		if(isset($config['tab'])) $section_config['tab'] = $config['tab'];
		if(isset($config['condition'])) $section_config['condition'] = $config['condition'];

		// Start section
		$this->start_controls_section( $config['name'] . '_section',  $section_config);

		// Call the callback function
		call_user_func(array($this, 'control_section_' . $config['name']));

		// End section
        $this->end_controls_section();

	}

    private function control_border($key, $selectors, $config = [ 'default' => '8', 'unit' => 'px', 'separator' => true, 'heading' => true ]){
		
		$selectors = array_map( function($selector) { return "{{WRAPPER}} " . $selector ;}, $selectors );

		if(!empty($config['heading'])){
            // Border heading
            $this->add_control( $key, [
                'label'     => esc_html__('Border', 'elementskit'),
                'type'      => Controls_Manager::HEADING,
                'separator' => $config['separator'] ? 'before' : 'none',
            ]);
        }

		// Review card border
		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name'     => $key . '_type',
				'label'    => esc_html__('Border Type', 'elementskit'),
				'selector' => implode(',', $selectors)
			]
		);

		$new_selectors = array();
		$border_radius = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;';
		foreach ($selectors as $key) { $new_selectors[$key] = $border_radius; }

		// Review card border radius
		$this->add_control( $key . '_radius', [
			'label'			=> esc_html__('Border Radius', 'elementskit'),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> ['px', '%', 'em'],
			'selectors'		=> $new_selectors,
			'default'    => [
				'top'      => $config['default'], 'right'	=> $config['default'],
				'bottom'   => $config['default'], 'left'	=> $config['default'],
				'unit'     => $config['unit'], 'isLinked' => true,
			]
		]);
	}

	private function control_text( $key, $selector, $exclude = [], $config = [] ){

		// Page name color
		$this->add_control( $key . '_color', [
			'label'     => esc_html__('Text Color', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} '. $selector => 'color: {{VALUE}}',
			],
		]);

		if(!in_array("shadow", $exclude)){
			// Page name text shadow
			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(), [
					'name' => $key . '_text_shadow',
					'label' => esc_html__( 'Text Shadow', 'elementskit' ),
					'selector' => '{{WRAPPER}} ' . $selector
				]
			);
		}

		if(!in_array("typography", $exclude)){
			// Page name typography
			$this->add_group_control(
				Group_Control_Typography::get_type(), [
					'name'     => $key . '_typography',
					'label'    => esc_html__('Typography', 'elementskit'),
					'selector' => '{{WRAPPER}} ' . $selector
				]
			);
		}

		if(!in_array("margin", $exclude)){ 
			// controls_section_overview_page_name_margin
			$value = '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';

			$def_margin = isset($config['def_margin']) 
				? $config['def_margin'] : [ 'bottom' => '16', 'unit' => 'px', 'isLinked' => false ];

			$this->add_responsive_control( $key . '_margin', [
				'label'          => esc_html__('Margin', 'elementskit'),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => ['px', '%', 'em'],
				'default'        => $def_margin,
				'tablet_default' => $def_margin,
				'mobile_default' => $def_margin,
				'selectors'      => [ '{{WRAPPER}} ' . $selector => 'margin:' . $value ],
			]);
		}
    }
    
    private function control_button( $name, $selector ){

        // Typography
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'		 => $name . '_typography',
			'selector'	 => '{{WRAPPER}} ' . $selector,
		]);

        // Border
        $this->control_border( $name. '_border', [ $selector ], [ 
            'default' => '2', 'unit' => 'em', 
            'separator' => false, 'heading' => false
        ]);

        // Tabs
		$this->start_controls_tabs( $name . '_tabs' );

		// Tab Normal 
        $this->start_controls_tab(
            $name . '_tab_normal', [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
		);

        // Tab normal background color
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => $name . '_background_normal',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic'],
				'selector' => '{{WRAPPER}} '. $selector,
			]
		);

		// Tab normal text color
		$this->add_control( $name . '_color_normal',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $selector => 'color: {{VALUE}}',
				],
			]
        );

		// Tab normal border color
		$this->add_control( $name . '_border_color_normal',
			[
				'label'     => esc_html__('Border Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $selector => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		// Tab Hover
        $this->start_controls_tab( 
            $name . '_tab_hover', [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
		);

        // Tab hover background color
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => $name . '_background_hover',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic'],
				'selector' => '{{WRAPPER}} '. $selector . ':hover',
			]
		);

		// Tab hover text color
		$this->add_control( $name . '_color_hover',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $selector . ":hover" => 'color: {{VALUE}}',
				],
			]
        );
        
        // Tab hover border color
		$this->add_control( $name . '_border_color_hover',
            [
                'label'     => esc_html__('Border Color', 'elementskit'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $selector . ':hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

		$this->end_controls_tab();
		$this->end_controls_tabs();
    }

    private function control_icon( $name, $selector, $config ) {

        $key = $name . '_';


        // Heading
		$this->add_control(  $key . 'heading', [
			'label'     => esc_html__(ucwords($name), 'elementskit'),
			'type'      => Controls_Manager::HEADING
        ]);
        
        // Typography
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'     => $key . 'typography',
                'label'    => esc_html__('Typography', 'elementskit'),
                'selector' => '{{WRAPPER}} ' . $selector
            ]
        );

        // Text Color
		$this->add_control( $key . 'color', [
			'label'     => esc_html__('Text Color', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} '. $selector => 'color: {{VALUE}}',
			],
        ]);
        
        // Icon
        $this->add_control( $key . 'icons', [
            'label' => esc_html__( 'Icon', 'elementskit' ),
            'type' => Controls_Manager::ICONS,
            'label_block' => true,
            'fa4compatibility' => $key . 'icon',
            'default' => $config['def_icon']
        ]);

    }

    private function control_section_settings(){

        // username
        $this->add_control( 'behance_user_name', [
            'label'       => esc_html__('Behance username', 'elementskit'),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => esc_html__('Behance Username', 'elementskit'),
        ]);

		// Fetch item per request
		$this->add_control( 'ekit_feed_flex_fetch_per_request', [
            'label'   => esc_html__('Fetch Per Request', 'elementskit'),
            'type'    => Controls_Manager::SELECT,
            'default' => '12',
            'options' => [
                '6'  => esc_html__('6', 'elementskit'),
                '12' => esc_html__('12', 'elementskit'),
                '18' => esc_html__('18', 'elementskit'),
                '24' => esc_html__('24', 'elementskit'),
                '30' => esc_html__('30', 'elementskit'),
            ],
        ]);

        // Delete cache
		$this->add_control( 'behance_delete_cache', [
            'label'       => esc_html__('', 'elementskit'),
            'type'        => Controls_Manager::BUTTON,
            'button_type' => 'info',
            'text'        => esc_html__(' Delete Cache ', 'elementskit') . '<span class="elementor-state-icon">
            <i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i></span>',
            'event'       => 'ekit:editor:be_del_cache_click',
        ]);
    }

    private function control_section_layout(){

        // Card style [ekit_behance_feed_card_styles]
		$this->add_control( 'card_styles', [
            'label'   => esc_html__('Choose Style', 'elementskit'),
            'type'    => ElementsKit_Controls_Manager::IMAGECHOOSE,
            'default' => 'style1',
            'options' => [
                'style1' => [
                    'title'      => esc_html__('Default', 'elementskit'),
                    'imagelarge' => Handler::get_url() . 'assets/images/style-1.png',
                    'imagesmall' => Handler::get_url() . 'assets/images/style-1.png',
                    'width'      => '33.33%',
                ],
                'style2' => [
                    'title'      => esc_html__('Grid Style without image', 'elementskit'),
                    'imagelarge' => Handler::get_url() . 'assets/images/style-2.png',
                    'imagesmall' => Handler::get_url() . 'assets/images/style-2.png',
                    'width'      => '33.33%',
                ],
                'style3' => [
                    'title'      => esc_html__('Image with Ratting', 'elementskit'),
                    'imagelarge' => Handler::get_url() . 'assets/images/style-3.png',
                    'imagesmall' => Handler::get_url() . 'assets/images/style-3.png',
                    'width'      => '33.33%',
                ],
                'style4' => [
                    'title'      => esc_html__('image style 4', 'elementskit'),
                    'imagelarge' => Handler::get_url() . 'assets/images/style-4.png',
                    'imagesmall' => Handler::get_url() . 'assets/images/style-4.png',
                    'width'      => '33.33%',
                ],
                'style5' => [
                    'title'      => esc_html__('image style 5', 'elementskit'),
                    'imagelarge' => Handler::get_url() . 'assets/images/style-5.png',
                    'imagesmall' => Handler::get_url() . 'assets/images/style-5.png',
                    'width'      => '33.33%',
                ],
                'style6' => [
                    'title'      => esc_html__('image style 6', 'elementskit'),
                    'imagelarge' => Handler::get_url() . 'assets/images/style-6.png',
                    'imagesmall' => Handler::get_url() . 'assets/images/style-6.png',
                    'width'      => '33.33%',
                ],
            ],
        ]);

        $this->add_responsive_control(
			'ekit_responsive_column',
			[
				'label'     => esc_html__('Column Count', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ekit-fb-col-4',
				'tablet_default'   => 'ekit-fb-col-6',
				'mobile_default'   => 'ekit-fb-col-12',
				'options'   => [
					'ekit-fb-col-12' => esc_html__('1 Columns', 'elementskit'),
					'ekit-fb-col-6' => esc_html__('2 Columns', 'elementskit'),
					'ekit-fb-col-4' => esc_html__('3 Columns', 'elementskit'),
					'ekit-fb-col-3' => esc_html__('4 Columns', 'elementskit'),
					'ekit-fb-col-2' => esc_html__('6 Columns', 'elementskit'),
				]
			]
		);

        // Column gap [ekit_behance_feed_column_gap]
		$this->add_responsive_control( 'column_gap', [
            'label'           => esc_html__('Column Gap', 'elementskit'),
            'type'            => Controls_Manager::SLIDER,
            'size_units'      => ['px','em'],
            'range'           => [
                'px' => [ 'min'  => 0, 'max'  => 96, 'step' => 2 ],
                'em' => [ 'min'  => 0, 'max'  => 6, 'step' => 0.2 ]
            ],
            'devices'         => ['desktop', 'tablet', 'mobile'],
            'default'         => [ 'size' => 16, 'unit' => 'px' ],
            'tablet_default'  => [ 'size' => 12, 'unit' => 'px' ],
            'mobile_default'  => [ 'size' => 8, 'unit' => 'px' ],
            'selectors'       => [
                '{{WRAPPER}} .ekit-feed-items-wrapper-behance .row.ekit-layout-grid' => 
                    'margin-right: calc(-{{SIZE}}{{UNIT}} / 2);margin-left: calc(-{{SIZE}}{{UNIT}} / 2);',
                '{{WRAPPER}} .ekit-feed-items-wrapper-behance .row.ekit-layout-grid > div' => 
                    'padding-right: calc({{SIZE}}{{UNIT}} / 2);padding-left: calc({{SIZE}}{{UNIT}} / 2);padding-bottom: {{SIZE}}{{UNIT}};',
            ]
        ]);

    }

    private function control_section_widget_style(){

        // ekit_behance_feed_widget_background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'widget_background',
				'label'    => esc_html__('Widget Background', 'elementskit'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ekit-feed-wrapper-behance',
			]
		);

		// ekit_behance_feed_widget_padding
		$this->add_responsive_control( 'widget_padding', [
            'label'          => esc_html__('Padding', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-wrapper-behance' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'default'           => $this->get_dimension(16, 'px'),
            'tablet_default'    => $this->get_dimension(12, 'px'),
            'mobile_default'    => $this->get_dimension(8 , 'px'),
        ]);

        // ekit_behance_feed_widget_border
		$this->control_border( 'widget_border', 
            [ '.ekit-feed-wrapper-behance' ], [
                'default' => '0', 'unit' => 'px',
                'separator' => false, 'heading' => false 
            ]
		);
    }

    private function control_section_header_card(){

        // ekit_behance_feed_header_card_background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'header_card_background',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ekit-feed-header-behance',
			]
		);

		// ekit_behance_feed_header_card_padding
		$this->add_responsive_control( 'header_card_padding', [
            'label'          => esc_html__('Padding', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-header-behance' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'default'           => $this->get_dimension(16, 'px'),
            'tablet_default'    => $this->get_dimension(12, 'px'),
            'mobile_default'    => $this->get_dimension(8 , 'px'),
        ]);

		// ekit_behance_feed_header_card_margin
		$this->add_responsive_control( 'header_card_margin', [
            'label'          => esc_html__('Margin', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-header-behance' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'default'           => $this->get_dimension([0, 0, 16, 0], 'px', false),
            'tablet_default'    => $this->get_dimension([0, 0, 12, 0], 'px', false),
            'mobile_default'    => $this->get_dimension([0, 0, 8, 0], 'px', false),
        ]);

        // ekit_behance_feed_header_card_border
		$this->control_border(  'header_card_border', 
            [ '.ekit-feed-header-behance' ], [ 
                'default' => '0', 'unit' => 'px',
                'separator' => true, 'heading' => true 
            ]
		);
    }

    private function control_section_profile_picture(){

        // ekit_behance_feed_profile_picture_size
        $this->add_responsive_control( 'profile_picture_size', [
            'label' => esc_html__( 'Picture Size', 'elementskit' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [ 'min' => 16, 'max' => 96, 'step' => 4 ],
                'em' => [ 'min' => 1, 'max' => 6, 'step' => 0.2 ],
            ],
            'default' => [ 'unit' => 'px', 'size' => 40 ],
            'tablet_default' => [ 'unit' => 'px', 'size' => 40 ],
            'mobile_default' => [ 'unit' => 'px', 'size' => 40 ],
            'selectors' => [
                '{{WRAPPER}} .ekit-feed-header-behance .ekit-feed-header--thumbnail' => "height:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};",
            ],
        ]);

        // ekit_behance_feed_profile_picture_margin_right
        $this->add_responsive_control( 'profile_picture_margin_right', [
            'label' => esc_html__( 'Margin Right', 'elementskit' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [ 'min' => 0, 'max' => 32, 'step' => 1 ],
                'em' => [ 'min' => 0, 'max' => 2, 'step' => 0.1 ],
            ],
            'default' => [ 'unit' => 'px', 'size' => 16 ],
            'tablet_default' => [ 'unit' => 'px', 'size' => 16 ],
            'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
            'selectors' => [
                '{{WRAPPER}} .ekit-feed-header-behance .ekit-feed-header--thumbnail' => "margin-right:{{SIZE}}{{UNIT}};",
            ],
        ]);

        // ekit_behance_feed_profile_picture_border_radius
		$this->add_control( 'profile_picture_border_radius', [
			'label'			=> esc_html__('Border Radius', 'elementskit'),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> ['px', '%', 'em'],
			'selectors'		=> [ 
                '{{WRAPPER}} .ekit-feed-header-behance .ekit-feed-header--thumbnail' => 
                    'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
			'default'    => $this->get_dimension(50, '%')
		]);
    }

    private function control_section_user_name(){
		$this->control_text(
            'header_user_name', 
            '.ekit-feed-header-behance .ekit-feed-header--name', [], [
                'def_margin' => $this->get_dimension([0, 0, 6, 0], 'px', false)
            ]
        );
    }

    private function control_section_user_desc(){
        $this->control_text(
            'user_desc', 
            '.ekit-feed-header-behance .ekit-feed-header--location', 
            ['margin', 'shadow']
        );
        
        // Icon Heading
		$this->add_control( 'user_desc_icon_heading', [
			'label'     => esc_html__('Icon', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);
        
        // ekit_behance_feed_user_desc_icons
        $this->add_control( 'user_desc_icons', [
            'label' => esc_html__( 'Icon', 'elementskit' ),
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'user_desc_icon',
            'default' => [
                'value' => 'fas fa-map-marker-alt',
                'library' => 'fa-solid',
            ],
            'label_block' => true
        ]);

        // ekit_behance_feed_profile_picture_margin_right
        $this->add_responsive_control( 'user_desc_icon_margin_right', [
            'label' => esc_html__( 'Margin Right', 'elementskit' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [ 'min' => 0, 'max' => 32, 'step' => 1 ],
                'em' => [ 'min' => 0, 'max' => 2, 'step' => 0.1 ],
            ],
            'default' => [ 'unit' => 'px', 'size' => 4 ],
            'tablet_default' => [ 'unit' => 'px', 'size' => 4 ],
            'mobile_default' => [ 'unit' => 'px', 'size' => 4 ],
            'selectors' => [
                '{{WRAPPER}} .ekit-feed-header-behance .ekit-feed-header--location i' => "margin-right: {{SIZE}}{{UNIT}};",
            ],
        ]);
    }

    private function control_section_header_buttons(){

        // Cards container padding
		$this->add_responsive_control( 'header_buttons_padding', [
            'label'      => esc_html__('Padding', 'elementskit'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors'  => [
                '{{WRAPPER}} .ekit-feed-header-behance .ekit-feed-header--actions .btn' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};min-width:auto;min-height:auto;',
            ],
        ]);

		// Follow button heading
		$this->add_control('header_follow_button_heading', [
			'label'     => esc_html__('Follow', 'elementskit'),
			'type'      => Controls_Manager::HEADING
		]);

        $this->control_button('header_follow_button',  '.ekit-feed-header-behance .ekit-feed-header--actions .btn:first-child');
        
        // Message button heading
		$this->add_control('header_message_button_heading', [
			'label'     => esc_html__('Message', 'elementskit'),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before'
        ]);
        
		$this->control_button(
            'header_message_button', 
            '.ekit-feed-header-behance .ekit-feed-header--actions .btn:last-child'
        );
                    
    }

    private function control_section_cards_container(){

        // Cards container background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'cards_container_background',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ekit-feed-items-wrapper-behance',
			]
		);

		// Cards container padding
		$this->add_responsive_control( 'cards_container_padding', [
            'label'      => esc_html__('Padding', 'elementskit'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'default'    => $this->get_dimension([16, 16, 0, 16], 'px'),
            'tablet_default'    => $this->get_dimension([12, 12, 0, 12], 'px'),
            'mobile_default'    => $this->get_dimension([8, 8, 0, 8], 'px'),
            'selectors'  => [
                '{{WRAPPER}} .ekit-feed-items-wrapper-behance' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        // Cards container border
        $this->control_border( 'cards_container_border', 
            [ '.ekit-feed-items-wrapper-behance' ], [
                'default' => '0' , 
                'unit' => 'px',
                'separator' => true, 
                'heading' => true 
            ]
        );
    }

    private function control_section_card_title(){

        // padding
		$this->add_responsive_control('card_title_padding', [
            'label'          => esc_html__('Padding', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-item-behance .ekit-feed-item--title' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'default'           => $this->get_dimension(16, 'px'),
            'tablet_default'    => $this->get_dimension(12, 'px'),
            'mobile_default'    => $this->get_dimension(8, 'px'),
        ]);

        // min height
        $this->add_responsive_control( 'card_title_min_height', [
            'label' => __( 'Min Height', 'elementskit' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [ 'min' => 32, 'max' => 80, 'step' => 1 ],
                'em' => [ 'min' => 2, 'max' => 5, 'step' => 0.1 ],
            ],
            'default' => [ 'unit' => 'px', 'size' => 42 ],
            'tablet_default' => [ 'unit' => 'px', 'size' => 42 ],
            'mobile_default' => [ 'unit' => 'px', 'size' => 42 ],
            'selectors' => [
                '{{WRAPPER}} .ekit-feed-item-behance .ekit-feed-item--title' => 
                    "min-height:{{SIZE}}{{UNIT}};",
            ],
        ]);

		$this->control_text(
            'card_title', 
            '.ekit-feed-item-behance .ekit-feed-item--title h4', ['shadow'], [
                'def_margin' => $this->get_dimension(0, 'px')
            ]
        );
    }

    private function control_section_hover_overlay(){

        // overlay_heading
		$this->add_control( 'overlay_heading', [
			'label'     => esc_html__('Overlay', 'elementskit'),
			'type'      => Controls_Manager::HEADING
        ]);

        // overlay_background
        $this->add_group_control(
            Group_Control_Background::get_type(), [
                'name'      => 'overlay_background',
                'label'     => esc_html__( 'Background', 'elementskit' ),
                'types'     => [ 'classic', 'gradient' ],
                'selector'  => '{{WRAPPER}} .ekit-feed-item-behance .ekit-feed-item--go-arrow'
            ]
        );

        // arrow_icon_circle_heading
		$this->add_control('arrow_icon_circle_heading', [
			'label'     => esc_html__('Circle', 'elementskit'),
			'type'      => Controls_Manager::HEADING
        ]);

        // arrow_icon_circle_size
		$this->add_responsive_control( 'arrow_icon_circle_size', [
            'label' => esc_html__( 'Circle Size', 'elementskit' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [ 'min' => 24, 'max' => 96, 'step' => 4 ],
                'em' => [ 'min' => 1.5, 'max' => 6, 'step' => 0.2 ],
            ],
            'default' => [ 'unit' => 'px', 'size' => 40 ],
            'tablet_default' => [ 'unit' => 'px', 'size' => 40 ],
            'mobile_default' => [ 'unit' => 'px', 'size' => 40 ],
            'selectors' => [
                '{{WRAPPER}} .ekit-feed-item-behance .ekit-feed-item--go-arrow a' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        // arrow_icon_circle_background
        $this->add_group_control(
            Group_Control_Background::get_type(), [
                'name'      => 'arrow_icon_circle_background',
                'label'     => esc_html__( 'Background', 'elementskit' ),
                'types'     => [ 'classic', 'gradient' ],
                'selector'  => '{{WRAPPER}} .ekit-feed-item-behance .ekit-feed-item--go-arrow a'
            ]
        );

        // Box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name' => 'arrow_icon_circle_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-feed-item-behance .ekit-feed-item--go-arrow a'
			]
		);

        // arrow_icon_icon_heading
		$this->add_control( 'arrow_icon_icon_heading', [
			'label'     => esc_html__('Icon', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);

        // arrow_icons
        $this->add_control( 'arrow_icons', [
            'label' => esc_html__( 'Arrow Icon', 'elementskit' ),
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'arrow_icon',
            'default' => [
                'value' => 'fas fa-arrow-right',
                'library' => 'fa-solid',
            ],
            'label_block' => true
        ]);

        // arrow_icon_size
		$this->add_responsive_control( 'arrow_icon_size', [
            'label' => esc_html__( 'Icon Size', 'elementskit' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [ 'min' => 0, 'max' => 96, 'step' => 4 ],
                'em' => [ 'min' => 0, 'max' => 6, 'step' => 0.2 ],
            ],
            'default' => [ 'unit' => 'px', 'size' => 20 ],
            'tablet_default' => [ 'unit' => 'px', 'size' => 20 ],
            'mobile_default' => [ 'unit' => 'px', 'size' => 20 ],
            'selectors' => [
                '{{WRAPPER}} .ekit-feed-item-behance .ekit-feed-item--go-arrow a i' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        // arrow_icon_color
        $this->add_control( 'arrow_icon_color', [
				'label'     => esc_html__('Icon Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-feed-item-behance .ekit-feed-item--go-arrow a i' => 'color: {{VALUE}}',
				],
			]
		);
    }

    private function control_section_overview(){
        
        // ekit_behance_feed_overview_padding
		$this->add_responsive_control( 'overview_padding', [
            'label'          => esc_html__('Padding', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-item-behance .ekit-feed-item--overview' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'default'           => $this->get_dimension([16, 0, 16, 0], 'px', false),
            'tablet_default'    => $this->get_dimension([12, 0, 12, 0], 'px', false),
            'mobile_default'    => $this->get_dimension([8, 0, 8, 0] , 'px', false),
        ]);

        $this->control_border('overview_border', 
            [ '.ekit-feed-item-behance .ekit-feed-item--overview' ], [
            'default' => '0', 
            'unit' => 'px', 
            'separator' => true, 
            'heading' => true
        ]);

        $this->control_icon('likes', '.ekit-feed-item-behance .ekit-feed-item--overview .likes', [
            "def_icon" => [
                'value' => 'fas fa-thumbs-up',
                'library' => 'fa-solid',
            ]
        ]);

        $this->control_icon('views', '.ekit-feed-item-behance .ekit-feed-item--overview .views', [
            "def_icon" => [
                'value' => 'fas fa-eye',
                'library' => 'fa-solid',
            ]
        ]);

        $this->control_icon('comments', '.ekit-feed-item-behance .ekit-feed-item--overview .comments', [
            "def_icon" => [
                'value' => 'fas fa-comment-alt',
                'library' => 'fa-solid',
            ]
        ]);
    }

    private function control_section_feed_card(){

        // ekit_behance_feed_header_card_background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'feed_card_background',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ekit-feed-item-behance',
			]
        );
        
         // Box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name' => 'feed_card_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-feed-item-behance'
			]
		);

		// ekit_behance_feed_header_card_padding
		$this->add_responsive_control( 'feed_card_padding', [
            'label'          => esc_html__('Padding', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-item-behance' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'default'           => $this->get_dimension(0, 'px'),
            'tablet_default'    => $this->get_dimension(0, 'px'),
            'mobile_default'    => $this->get_dimension(0 , 'px'),
        ]);

		// ekit_behance_feed_header_card_margin
		$this->add_responsive_control( 'feed_card_margin', [
            'label'          => esc_html__('Margin', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-item-behance' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'default'           => $this->get_dimension(0, 'px'),
            'tablet_default'    => $this->get_dimension(0, 'px'),
            'mobile_default'    => $this->get_dimension(0, 'px'),
        ]);

        // ekit_behance_feed_header_card_border
		$this->control_border( 'feed_card_border', 
            [ '.ekit-feed-item-behance' ], [
                'default' => '0', 'unit' => 'px',
                'separator' => true, 'heading' => true 
            ]
		);
    }

	protected function register_controls() {

        // Settings section
        $this->controls_section([
            'name'      => 'settings',
            'label'     => esc_html__('Settings', 'elementskit')
        ]);

        // Settings section
        $this->controls_section([
            'name'      => 'layout',
            'label'     => esc_html__('Layout', 'elementskit')
        ]);

        // Widget style section
        $this->controls_section([
            'name'      => 'widget_style',
            'label'     => esc_html__('Widget', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);

        // Header card section
        $this->controls_section([
            'name'      => 'header_card',
            'label'     => esc_html__('Header Card', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);

        // User profile pic
        $this->controls_section([
            'name'      => 'profile_picture',
            'label'     => esc_html__('Profile Picture', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);

        // User name
        $this->controls_section([
            'name'      => 'user_name',
            'label'     => esc_html__('User Name', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);

        // // User name
        // $this->controls_section([
        //     'name'      => 'user_name',
        //     'label'     => 'User Name',
        //     'tab'       => Controls_Manager::TAB_STYLE
        // ]);

        // User description
        $this->controls_section([
            'name'      => 'user_desc',
            'label'     => esc_html__('User Description', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);

        // Header buttons
        $this->controls_section([
            'name'      => 'header_buttons',
            'label'     => esc_html__('Header Buttons', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);

        // Cards Container
        $this->controls_section([
            'name'      => 'cards_container',
            'label'     => esc_html__('Cards Container', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);

        // Cards Container
        $this->controls_section([
            'name'      => 'feed_card',
            'label'     => esc_html__('Feed Card', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);
        
        // Card Title
        $this->controls_section([
            'name'      => 'card_title',
            'label'     => esc_html__('Card Title', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [
                'card_styles!' => 'style1'
            ]
        ]);

        // Overview
        $this->controls_section([
            'name'      => 'overview',
            'label'     => esc_html__('Overview', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);

        // Hover Overlay
        $this->controls_section([
            'name'      => 'hover_overlay',
            'label'     => esc_html__('Hover Overlay', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);

	}


	protected function render() {
		echo '<div class="ekit-wid-con" >';
		$this->render_raw();
		echo '</div>';
	}


	/**
	 * Get behance user's profile image
	 *
	 * @param $user
	 * @param int $size
	 *
	 * @return mixed|string
	 */
	private function get_user_pic_url($user, $size = 100) {

		$pic = Handler::get_url() . 'assets/images/profile-thumbnail.png';

		if(!empty($user->images)) {

			if(!empty($user->images->$size)) {

				return $user->images->$size;
			}

			$pics = (array) $user->images;
			$pic  = array_pop($pics);
		}

		return $pic;
	}


	private function format_count($count) {
		$count = intval($count);
		if($count < 1000) return $count;
		if($count >= 1000000) return round($count / 1000000, 2) . 'M';
		return round($count / 1000, 2) . 'K';
	}

	protected function render_raw() {

		$settings       = $this->get_settings_for_display();
		$widget_id      = $this->get_id();
		extract($settings);

		if(empty($settings['behance_user_name'])) : ?>
            <div>
                <strong>
                    <?php echo esc_html__('Please set a valid username first', 'elementskit')?>
                </strong>
            </div>
        <?php else:

			/**
			 * cached username --- behance_username
			 * new username --- behance_user_name
			 */

			$data = Handler::get_the_feed($settings['behance_user_name']);
            
            if($data['success'] && $data['data']['feed']->http_code == 200):

				$user               = $data['data']['user'];
				$items              = $data['data']['feed']->projects;
                $item_count         = empty($settings['ekit_feed_flex_fetch_per_request']) ? 10 : intval($settings['ekit_feed_flex_fetch_per_request']);
                $sliced_items       = array_slice($items, 0, $item_count); 

				$user_display_name  = $user->display_name;
				$user_profile_pic   = $this->get_user_pic_url($user);
				$user_location      = $user->location;

				$user_follower      = empty($user->stats->followers) ? 0 : intval($user->stats->followers);
				$user_following     = empty($user->stats->following) ? 0 : intval($user->stats->following);
				$user_about         = empty($user->sections->About) ? 0 : $user->sections->About;

                $show_title         = $card_styles == 'style3' || $card_styles == 'style4' || $card_styles == 'style5';
                $column_count       = $this->format_column($settings, 'ekit_responsive_column');

				?>
                <!-- Start Markup  -->
                <div class="ekit-feed-wrapper ekit-feed-wrapper-behance">

                    <!-- Start feed header -->
                    <div class="ekit-feed-header ekit-feed-header-behance">
                        <!-- Start header left -->
                        <div class="header-left">
                            <!-- Start thumbnail -->
                            <div class="ekit-feed-header--thumbnail">
                                <img src="<?php echo esc_url( $user_profile_pic ) ?>" alt="<?php echo esc_attr( $user_display_name ) ?>">
                            </div>
                            <!-- End thumbnail -->
                            <div class='ekit-feed-header--user-info'>
                                <h4 class='ekit-feed-header--name'>
									<?php echo esc_html( $user_display_name ) ?>
                                </h4>

                                <!-- Start Location -->
								<?php if(!empty($user_location)): ?>
                                    <div class='ekit-feed-header--location'>
                                        <?php 
                                            $migrated = isset( $settings['__fa4_migrated']['user_desc_icons'] );
                                            $is_new = empty( $user_desc_icon );
                                            if ( $is_new || $migrated ) :
                                                Icons_Manager::render_icon( $user_desc_icons, [ 'aria-hidden' => 'true'] );
                                            else : ?>
                                                <i class="<?php echo esc_attr( $user_desc_icon ); ?>" aria-hidden="true"></i>
                                            <?php endif;
                                        ?>
                                        <p><?php echo esc_html( $user_location ) ?></p>
                                    </div>
								<?php endif ?>
                                <!-- End Location -->

                            </div>
                        </div>
                        <!-- End header left -->
                        <div class="header-right">
                            <div class="ekit-feed-header--actions">
                                <a href="<?php echo esc_url( $user->url ) ?>" target="_" class="btn btn-primary btn-pill">
                                    <?php echo esc_html__('Follow', 'elementskit')?>
                                </a>
                                <a href="<?php echo esc_url( $user->url ) ?>" target="_" class="btn btn-outline-secondary btn-pill">
                                    <?php echo esc_html__('Message', 'elementskit')?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- End feed header -->

                    <!-- Start feed items -->
                    <div class="ekit-feed-items-wrapper ekit-feed-items-wrapper-behance">
                        <div class="row ekit-fb-row ekit-layout-grid">
                            <?php foreach($sliced_items as $item):
                            
                                $img_url = $this->get_cover_image_src($item, $ekit_responsive_column); 
                                $appreciations  = !empty($item->stats->appreciations)    ? $this->format_count($item->stats->appreciations) : 0;
                                $views          = !empty($item->stats->views)            ? $this->format_count($item->stats->views)         : 0;
                                $comments       = !empty($item->stats->comments)         ? $this->format_count($item->stats->comments)      : 0;
                            
                            ?>
                                <div class="<?php echo esc_attr($column_count) ?>">
                                    <!-- Start feed item -->
                                    <div class="ekit-feed-item ekit-feed-item-behance <?php echo esc_attr( $card_styles ) ?>">
                                        <!-- Start cover photo -->
                                        <div class="ekit-feed-item--cover">
                                            <img src="<?php echo esc_url( $img_url ) ?>" alt="<?php echo esc_attr( $item->name ) ?>"/>
                                            <?php if($card_styles != 'style5'): ?>
                                                <div class="ekit-feed-item--go-arrow">
                                                    <?php if($card_styles == 'style6'): ?>
                                                        <div>
                                                            <h4><?php echo esc_html( $item->name ) ?></h4>
                                                            <ul>
                                                                <?php foreach($item->fields as $field): ?>
                                                                    <li><?php echo esc_html( $field ) ?></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php else: ?>
                                                        <a href="<?php echo esc_url( $item->url ) ?>" target="_blank">
                                                            <?php
                                                                $migrated = isset( $settings['__fa4_migrated']['arrow_icons'] );
                                                                $is_new = empty( $arrow_icon );
                                                                if ( $is_new || $migrated ) :
                                                                Icons_Manager::render_icon( $arrow_icons, [ 'aria-hidden' => 'true'] );
                                                                else : ?>
                                                                    <i class="<?php echo esc_attr( $arrow_icon ); ?>" aria-hidden="true"></i>
                                                                <?php endif;
                                                            ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <!-- End cover photo -->

                                        <div class="ekit-feed-item--info">
											<?php if( $show_title ): ?>
                                                <div class="ekit-feed-item--title">
                                                    <h4><?php echo esc_html( $item->name ) ?></h4>
                                                </div>
												<?php

											endif;

											if($card_styles != 'style1'): ?>
                                                <div class="ekit-feed-item--overview">
                                                    <div class="likes">
                                                        <?php 
                                                            $migrated = isset( $settings['__fa4_migrated']['likes_icons'] );
                                                            $is_new = empty( $likes_icon );
                                                            if ( $is_new || $migrated ) :
                                                                Icons_Manager::render_icon( $likes_icons, [ 'aria-hidden' => 'true'] );
                                                            else : ?>
                                                                <i class="<?php echo esc_attr( $likes_icon ); ?>" aria-hidden="true"></i>
                                                            <?php endif;
                                                        ?>
                                                        <span>
	                                                        <?php echo esc_html( $appreciations ) ?>
                                                        </span>
                                                    </div>
                                                    <div class="views">
                                                        <?php 
                                                            $migrated = isset( $settings['__fa4_migrated']['views_icons'] );
                                                            $is_new = empty( $views_icon );
                                                            if ( $is_new || $migrated ) :
                                                                Icons_Manager::render_icon( $views_icons, [ 'aria-hidden' => 'true'] );
                                                            else : ?>
                                                                <i class="<?php echo esc_attr(  $views_icon ); ?>" aria-hidden="true"></i>
                                                            <?php endif;
                                                        ?>
                                                        <span>
	                                                        <?php echo esc_html( $views ) ?>
                                                        </span>
                                                    </div>
                                                    <div class="comments">
                                                        <?php 
                                                            $migrated = isset( $settings['__fa4_migrated']['comments_icons'] );
                                                            $is_new = empty( $comments_icon );
                                                            if ( $is_new || $migrated ) :
                                                                Icons_Manager::render_icon( $comments_icons, [ 'aria-hidden' => 'true'] );
                                                            else : ?>
                                                                <i class="<?php echo esc_attr( $comments_icon ); ?>" aria-hidden="true"></i>
                                                            <?php endif;
                                                        ?>
                                                        <span>
	                                                        <?php echo esc_html( $comments ) ?>
                                                        </span>
                                                    </div>
                                                </div>
												<?php

											endif; ?>
                                            <!-- End Feed item overview -->

                                        </div>

                                        <?php if ($card_styles === 'style5') {
                                            /**
                                             * Link wrapper for 'style-5'
                                             */ ?>
                                            <a href="<?php echo esc_url( $item->url ) ?>" class="ekit-feed-item--link"></a>
                                        <?php } ?>
                                    </div>
                                    <!-- End feed item -->
                                </div>
							<?php endforeach ?>
                        </div>
                        <!-- <div class="ekit-feed-items-load-more">
                            <a class="btn load_more_b_feed" style="cursor: pointer">Load More</a>
                        </div> -->
                    </div>
                    <!-- End feed items -->
                </div>
                <!-- End Markup  -->

                <!-- Data Fetch error -->
				<?php

			else: ?>
                <div>
                    <strong>
                        <?php echo esc_html__('Data fetch error', 'elementskit')?>
                    </strong>
                </div>
				<?php

            endif;
		endif;
	}


	private function get_cover_image_src($item, $col_size) {

		if($col_size == 'ekit-fb-col-2' || $col_size == 'ekit-fb-col-3') {
			$size = 202;
		} elseif($col_size == 'ekit-fb-col-4' || $col_size == 'ekit-fb-col-6') {
			$size = 404;
		} else {
			$size = 808;
		}


		/**
		 * If given size found then return
		 */
		if(!empty($item->covers->$size)) {

			return $item->covers->$size;
		}

		$size = 'max_' . $size;

		if(!empty($item->covers->$size)) {

			return $item->covers->$size;
		}

		return $item->covers->original;
	}
}
