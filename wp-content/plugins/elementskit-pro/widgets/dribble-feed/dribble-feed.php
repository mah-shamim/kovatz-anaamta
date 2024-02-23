<?php

namespace Elementor;

defined('ABSPATH') || exit;

use Elementor\ElementsKit_Widget_Dribble_Feed_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;


class ElementsKit_Widget_Dribble_Feed extends Widget_Base {

	public $base;


	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		wp_register_script('ekit-dribble-feed-script-handle', Handler::get_url() . 'assets/js/script.js', ['elementor-frontend'], \ElementsKit_Lite::version(), true);
	}


	public function get_script_depends() {
		return ['ekit-dribble-feed-script-handle'];
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
        return 'https://wpmet.com/doc/dribble-feed/';
    }

    private function controls_section( $config, $callback ){

		// New configs
		$newConfig = [ 'label' => $config['label'] ];
		
		// Formatting configs
		if(isset($config['tab'])) $newConfig['tab'] = $config['tab'];
		if(isset($config['condition'])) $newConfig['condition'] = $config['condition'];

		// Start section
		$this->start_controls_section( $config['key'],  $config);

		// Call the callback function
		call_user_func(array($this, $callback));

		// End section
		$this->end_controls_section();
	}
    
    private function controls_section_arrow_icon(){

		$root = '.ekit-feed-item-dribble .ekit-feed-item--go-arrow a';
		$icon = $root . ' i';

        // Circle
		$this->add_control( 'ekit_dribbble_feed_arrow_icon_circle_heading', [
			'label'     => esc_html__('Circle', 'elementskit'),
			'type'      => Controls_Manager::HEADING
        ]);

        // Circle size
		$this->add_responsive_control(
			'ekit_dribbble_feed_arrow_icon_circle_size', [
				'label' => __( 'Circle Size', 'elementskit' ),
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
					'{{WRAPPER}} ' . $root => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
			]
        );

        // Circle background
        $this->add_group_control(
            Group_Control_Background::get_type(), [
                'name'      => 'ekit_dribbble_feed_arrow_icon_circle_background',
                'label'     => esc_html__( 'Background', 'elementskit' ),
                'types'     => [ 'classic', 'gradient' ],
                'selector'  => '{{WRAPPER}} ' . $root
            ]
        );

        // Icon
		$this->add_control( 'ekit_dribbble_feed_arrow_icon_icon_heading', [
			'label'     => esc_html__('Icon', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);

        // ekit_dribbble_feed_arrow_icons
        $this->add_control(
            'ekit_dribbble_feed_arrow_icons', [
                'label' => esc_html__( 'Header Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_dribbble_feed_arrow_icon',
                'default' => [
                    'value' => 'icon icon-right-arrow1',
                    'library' => 'ekiticons',
                ],
                'label_block' => true
            ]
        );

        // ekit_dribbble_feed_arrow_icon_size
		$this->add_responsive_control(
			'ekit_dribbble_feed_arrow_icon_size', [
				'label' => __( 'Icon Size', 'elementskit' ),
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
					'{{WRAPPER}} ' . $icon => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
        );

        // ekit_dribbble_feed_arrow_icon_color
        $this->add_control(
			'ekit_dribbble_feed_arrow_icon_color', [
				'label'     => __('Icon Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $icon => 'color: {{VALUE}}',
				],
			]
		);
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


	protected function register_controls() {

	
        // ==========================
        // Start layout section
        // ==========================
        $this->start_controls_section(
            'ekit_feed_layout_section', [
                'label' => esc_html__( 'Layout', 'elementskit' ),
            ]
        );

        // Card style [ekit_feed_card_styles]
		$this->add_control(
            'ekit_feed_card_styles',
            [
                'label' => esc_html__('Choose Style', 'elementskit'),
                'type' => ElementsKit_Controls_Manager::IMAGECHOOSE,
                'default' => 'style1',
                'options' => [
					'style1' => [
						'title' => esc_html__( 'Default', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/images/style-1.png',
                        'imagesmall' => Handler::get_url() . 'assets/images/style-1.png',
                        'width' => '33.33%',
					],
					'style2' => [
						'title' => esc_html__( 'Grid Style without image', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/images/style-2.png',
                        'imagesmall' => Handler::get_url() . 'assets/images/style-2.png',
                        'width' => '33.33%',
					],
					'style3' => [
						'title' => esc_html__( 'Image with Ratting', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/images/style-3.png',
                        'imagesmall' => Handler::get_url() . 'assets/images/style-3.png',
                        'width' => '33.33%',
					],
					'style4' => [
						'title' => esc_html__( 'image style 4', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/images/style-4.png',
                        'imagesmall' => Handler::get_url() . 'assets/images/style-4.png',
                        'width' => '33.33%',
					],
					'style5' => [
						'title' => esc_html__( 'image style 5', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/images/style-5.png',
                        'imagesmall' => Handler::get_url() . 'assets/images/style-5.png',
                        'width' => '33.33%',
					]
				],
            ]
        );

		// Fetch item per request
		$this->add_control( 'ekit_dribbble_feed_per_page', [
			'label'   => esc_html__('Feeds Per Page', 'elementskit'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 50,
				'step' => 1,
				'default' => 12,
		]);

        $this->add_responsive_control(
            'ekit_dribbble_feed_column',
            [
                'label'             => esc_html__( 'Columns', 'elementskit' ),
                'type'              => Controls_Manager::SLIDER,
                'range'             => [
                    'px' => [
                        'min' => 1,
                        'max' => 12,
                    ],
                ],
                'default'           => [
                    'size'  => 4,
                ],
                'tablet_default'    => [
                    'size'  => 2,
                ],
                'mobile_default'    => [
                    'size'  => 1,
                ],
                'selectors'         => [
                    '{{WRAPPER}} .ekit-feed-item-col' => '-ms-flex: 0 0 calc(100% / {{SIZE}}); flex: 0 0 calc(100% / {{SIZE}}); max-width: calc(100% / {{SIZE}});',
                ],
                'separator'         => 'before',
            ]
        );

        $this->add_control(
            'ekit_feed_card_styles_header',
            [
                'label'     => esc_html__( 'Enable Header', 'elementskit' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'default'   => '',
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
        // ==========================
        // ENd layout section
        // ==========================
        
        // ==========================
        // Start widget style section
        // ==========================
        $this->start_controls_section(
            'ekit_feed_widget_style_section_heading', [
                'label' => esc_html__( 'Widget styles', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // ekit_review_widget_background
        $this->add_group_control(
            Group_Control_Background::get_type(), [
                'name'      => 'ekit_feed_widget_background',
                'label'     => esc_html__( 'Widget Background', 'elementskit' ),
                'types'     => [ 'classic', 'gradient' ],
                'selector'  => '{{WRAPPER}} .ekit-feed-wrapper-dribble'
                ]
        );

        // Widget padding
        $this->add_responsive_control(
            'ekit_feed_widget_padding',
            [
                'label'      => esc_html__( 'Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .ekit-feed-wrapper-dribble' => 
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'  => [
                    'top' => 1, 'right' => 1,
                    'bottom' => 1, 'left' => 1,
                    'unit' => 'em', 'isLinked' => true,
                ],
                'tablet_default'  => [
                    'top' => '8', 'right' => '8',
                    'bottom' => '8', 'left' => '8',
                    'unit' => 'px', 'isLinked' => true,
                 ],
                 'mobile_default'  => [
                    'top' => '8', 'right' => '8',
                    'bottom' => '8', 'left' => '8',
                    'unit' => 'px', 'isLinked' => true,
                 ],
            ]
        );

        $this->end_controls_section();
        // ==========================
        // End widget style section
        // ==========================

        // ==========================
        // Start feed header styles
        // ==========================
        $this->start_controls_section(
            'ekit_feed_header_styles_section', [
                'label' => esc_html__( 'Feed Header', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_feed_card_styles_header' => 'yes'
                ]
            ]
        );
        
        // Feed header title color
        $this->add_control(
            'ekit_feed_header_name_color', [
                'label' => __( 'Primary Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-feed-header-dribble .ekit-feed-header--name' => 'color: {{VALUE}}',
                ],
            ]
        );
        // Feed header desc and location color
        $this->add_control(
            'ekit_feed_header_desc_and_location_color', [
                'label' => __( 'Secondary Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-feed-header-dribble .ekit-feed-header--desc' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ekit-feed-header-dribble .ekit-feed-header--location' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        // Feed header typography
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'ekit_feed_header_typography',
                'label' => __( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-feed-header-dribble',
            ]
        );

        // Feed header background
        $this->add_group_control(
            Group_Control_Background::get_type(), [
                'name'      => 'ekit_feed_header_background',
                'label'     => esc_html__( 'Background', 'elementskit' ),
                'types'     => [ 'classic', 'gradient' ],
                'selector'  => '{{WRAPPER}} .ekit-feed-header',
            ]
        );

        // Feed header padding
        $this->add_responsive_control(
            'ekit_feed_header_padding',
            [
                'label'      => esc_html__( 'Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .ekit-feed-header-dribble' => 
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'  => [
                    'top' => 1, 'right' => 1,
                    'bottom' => 1, 'left' => 1,
                    'unit' => 'em', 'isLinked' => true,
                ],
            ]
        );

        // Feed header margin
        $this->add_responsive_control(
            'ekit_feed_header_margin', [
                'label' => esc_html__( 'Margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default'  => [
                    'top' => 0, 'right' => 0,
                    'bottom' => 1, 'left' => 0,
                    'unit' => 'em', 'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-feed-header-dribble' => 
                        'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        // Feed header border heading
        $this->add_control(
            'ekit_feed_header_border_heading', [
                'label' => esc_html__( 'Border', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );
        // Feed header border
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'     => 'ekit_feed_header_border_type',
                'label'    => esc_html__( 'Border Type', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-feed-header-dribble',
            ]
        );
        // Feed header border radius
        $this->add_control(
            'ekit_feed_header_border_radius', [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-feed-header-dribble' => 
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ==========================
        // End feed header styles
        // ==========================

        // ==========================
        // Start feed header styles
        // ==========================
        $this->start_controls_section(
            'ekit_feed_header_styles_buttons_section', [
                'label' => esc_html__( 'Feed Header Buttons', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_feed_card_styles_header' => 'yes'
                ]
            ]
        );

        // Cards container padding
		$this->add_responsive_control( 'ekit_feed_header_buttons_padding', [
            'label'      => esc_html__('Padding', 'elementskit'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors'  => [
                '{{WRAPPER}} .ekit-feed-header-dribble .ekit-feed-header--actions .btn' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};min-width:auto;min-height:auto;',
            ],
        ]);

		// Follow button heading
		$this->add_control('ekit_header_follow_button_heading', [
			'label'     => esc_html__('Follow', 'elementskit'),
			'type'      => Controls_Manager::HEADING
		]);

        $this->control_button('ekit_header_follow_button',  '.ekit-feed-header-dribble .ekit-feed-header--actions .btn:first-child');
        
        // Message button heading
		$this->add_control('ekit_header_message_button_heading', [
			'label'     => esc_html__('Message', 'elementskit'),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before'
        ]);
        
		$this->control_button('ekit_header_message_button',  '.ekit-feed-header-dribble .ekit-feed-header--actions .btn:last-child');

        $this->end_controls_section();
        // ==========================
        // End feed header styles
        // ==========================


        // ==========================
        // Start feed item cards
        // ==========================
        $this->start_controls_section(
            'ekit_feed_cards_section_heading', [
                'label' => esc_html__( 'Cards Container', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Feed item cards background
        $this->add_group_control(
            Group_Control_Background::get_type(), [
                'name'      => 'ekit_feed_cards_background',
                'label'     => esc_html__( 'Background', 'elementskit' ),
                'types'     => [ 'classic', 'gradient' ],
                'selector'  => '{{WRAPPER}} .ekit-feed-items-wrapper'
                ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'     => 'ekit_feed_cards_border_type',
                'label'    => esc_html__( 'Border Type', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-feed-items-wrapper',
            ]
        );


        // Feed item cards padding
        $this->add_responsive_control(
            'ekit_feed_item_cards_padding', [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default'  => [
                    'top' => 1, 'right' => 1,
                    'bottom' => 0, 'left' => 1,
                    'unit' => 'em', 'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-feed-items-wrapper-dribble' => 
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();
        // ==========================
        // End feed cards
        // ==========================


        // ==========================
        // Start feed item card
        // ==========================
        $this->start_controls_section(
            'ekit_feed_item_card_section_heading', [
                'label' => esc_html__( 'Feed Card', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Feed item card background
        $this->add_group_control(
            Group_Control_Background::get_type(), [
                'name'      => 'ekit_feed_card_background',
                'label'     => esc_html__( 'Background', 'elementskit' ),
                'types'     => [ 'classic', 'gradient' ],
                'selector'  => '{{WRAPPER}} .ekit-feed-item-dribble'
                ]
        );
            
            // Box shadow
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name' => 'ekit_feed_card_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-feed-item-dribble'
            ]
        );

        // ekit_behance_feed_header_card_padding
        $this->add_responsive_control( 'ekit_feed_card_padding', [
            'label'          => esc_html__('Padding', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-item-dribble' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'     => 'ekit_feed_card_border',
                'label'    => esc_html__('Border Type', 'elementskit'),
                'selector' => '{{WRAPPER}} .ekit-feed-item-dribble',
            ]
        );

        $this->add_control( 'ekit_feed_card_radius', [
            'label'			=> esc_html__('Border Radius', 'elementskit'),
            'type'			=> Controls_Manager::DIMENSIONS,
            'size_units'	=> ['px', '%', 'em'],
            'selector'		=> '{{WRAPPER}} .ekit-feed-item-dribble',
        ]);

        // Feed item card margin
        $this->add_responsive_control(
            'ekit_feed_item_card_margin', [
                'label' => esc_html__( 'Spacing', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default'  => [
                    'top' => '0',
                    'right' => '0.5',
                    'bottom' => '1',
                    'left' => '0.5',
                    'unit'  => 'em',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-feed-item-dribble' => 
                        'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-feed-item-row' => 'margin-left: -{{LEFT}}{{UNIT}}; margin-right: -{{RIGHT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );


        $this->end_controls_section();
        // ==========================
        // End feed item card
        // ==========================

        // ==========================
        // Start feed item card title
        // ==========================
        $this->start_controls_section(
            'ekit_feed_item_card_section_title', [
                'label' => esc_html__( 'Card Title', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_feed_card_styles!' => 'style1'
                ]
            ]
        );
        
		// Page name color
		$this->add_control( 'ekit_feed_item_card_title_color', [
			'label'     => esc_html__('Text Color', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .ekit-feed-item-dribble .ekit-feed-item--title h4' => 'color: {{VALUE}}',
			],
		]);

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'     => 'ekit_feed_item_card_title_typography',
                'label'    => esc_html__('Typography', 'elementskit'),
                'selector' => '{{WRAPPER}} .ekit-feed-item-dribble .ekit-feed-item--title h4'
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(), [
                'name' => 'ekit_feed_item_card_title_text_shadow',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-feed-item-dribble .ekit-feed-item--title h4' 
            ]
        );

        $this->add_responsive_control( 'ekit_feed_item_card_title_margin', [
            'label'          => esc_html__('Margin', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-item-dribble .ekit-feed-item--title h4' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
        ]);

        // padding
		$this->add_responsive_control('ekit_feed_item_card_title_padding', [
            'label'          => esc_html__('Padding', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-item-dribble .ekit-feed-item--title h4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
        // ==========================
        // End feed item card title
        // ==========================

        // Top right brand logo
        $this->controls_section(
            [ 
                'label' => esc_html__('Arrow Icon', 'elementskit'),  
                'key' => 'ekit_dribbble_feed_arrow_icon',       
                'tab' => Controls_Manager::TAB_STYLE
            ], 
            'controls_section_arrow_icon'
        );

	}


	protected function render() {
		echo '<div class="ekit-wid-con" >';
		$this->render_raw();
		echo '</div>';
	}


	protected function render_raw() {

        $settings  = $this->get_settings_for_display();
        extract($settings);
		$widget_id = $this->get_id();

		$config = Handler::get_config();

		if(empty($config['access_token'])) : ?>

            <h1><?php echo esc_html__('Dribbble Feed', 'elementskit') ?></h1>
            <div><?php echo esc_html__('Please Get a access token first', 'elementskit') ?></div>

		<?php else:

			$feed = Handler::get_feed($config['access_token']);

			if($feed === false) : ?>

                <h1><?php echo esc_html__('Data retrieved failed!', 'elementskit') ?></h1>

			<?php else :
                $profile = Handler::get_user_info($config['access_token']);
                $items = !empty($feed) ? $feed : [];
				$item_count         = empty($ekit_dribbble_feed_per_page) ? 12 : intval($ekit_dribbble_feed_per_page);
				$sliced_items       = array_slice($items, 0, $item_count);
            ?>

                <!-- Start Markup  -->
                <div class="ekit-feed-wrapper ekit-feed-wrapper-dribble">

                    <?php if( $profile && 'yes' === $ekit_feed_card_styles_header ): ?>
                    <!-- Start feed header -->
                    <div class="ekit-feed-header ekit-feed-header-dribble">
                        <!-- Start header left -->
                        <div class="header-left">
                            <!-- Start thumbnail -->
                            <div class="ekit-feed-header--thumbnail">
                                <?php 
                                $thumbnail = !empty($profile['avatar_url']) 
                                    ? $profile['avatar_url']
                                    : Handler::get_url() . 'assets/images/profile-thumbnail.png'
                                ?>
                                <img src="<?php echo esc_url($thumbnail) ?>" alt="<?php echo esc_attr($profile['name']) ?>">
                            </div>
                            <!-- End thumbnail -->
                            <div>
                                <h4 class='ekit-feed-header--name'>
                                    <?php echo esc_html($profile['name']) ?>
                                </h4>

                                <!-- Start Location -->
                                <?php if(!empty($profile['location'])):?>
                                    <div class='ekit-feed-header--location'>
                                        <i class="icon icon-map-marker"></i>
                                        <p><?php echo esc_html($profile['location']) ?></p>
                                    </div>
                                <?php endif ?>
                                <!-- End Location -->

                                <!-- Start description -->
                                <?php if(!empty($profile['bio'])):?>
                                    <div class='ekit-feed-header--desc'>
                                        <i class="icon icon-information"></i>
                                        <p><?php echo esc_html($profile['bio']) ?></p>
                                    </div>
                                <?php endif ?>
                                <!-- End description -->

                            </div>
                        </div>
                        <!-- End header left -->
                        <div class="header-right">
                            <div class="ekit-feed-header--actions">
                                <a href="<?php echo esc_url($profile['html_url']) ?>" target="_" class="btn btn-primary btn-pill">
                                    <?php echo esc_html__('Follow', 'elementskit') ?>
                                </a>
                                <a href="<?php echo esc_url($profile['html_url']) ?>" target="_" class="btn btn-primary-outlined btn-pill">
                                    <?php echo esc_html__('Message', 'elementskit') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- End feed header -->
                    <?php endif ?>

                    <!-- Start feed items -->
                    <div class="ekit-feed-items-wrapper ekit-feed-items-wrapper-dribble">
                        <div class="ekit-feed-item-row">
                            <?php foreach($sliced_items as $item): ?>
                            <div class="ekit-feed-item-col">
                                <div class="ekit-feed-item ekit-feed-item-dribble <?php echo esc_attr($ekit_feed_card_styles) ?>">
                                    <div class="ekit-feed-item--cover">
                                        <img src="<?php echo esc_url($item->images->normal) ?>" alt="<?php echo esc_attr($item->title) ?>">
                                        <div class="ekit-feed-item--go-arrow">
                                            <a href="<?php echo esc_attr($item->html_url) ?>" target="_">
                                                <?php
                                                    $migrated = isset( $settings['__fa4_migrated']['ekit_dribbble_feed_arrow_icons'] );
                                                    $is_new = empty( $ekit_dribbble_feed_arrow_icon );
                                                    if ( $is_new || $migrated ) :
                                                        \Elementor\Icons_Manager::render_icon( $ekit_dribbble_feed_arrow_icons, [ 'aria-hidden' => 'true'] );
                                                    else : ?>
                                                        <i class="<?php echo esc_attr($ekit_dribbble_feed_arrow_icon); ?>" aria-hidden="true"></i>
                                                    <?php endif;
                                                ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ekit-feed-item--info">
                                        <?php if(
                                            $ekit_feed_card_styles == 'style3' || 
                                            $ekit_feed_card_styles == 'style4' ||
                                            $ekit_feed_card_styles == 'style5'
                                        ): ?>
                                        <div class="ekit-feed-item--title">
                                            <?php if ( $ekit_feed_card_styles == 'style4' ) : ?>
                                            <h4><?php echo esc_html($item->title) ?></h4>
                                            <?php else : ?>
                                            <a href="<?php echo esc_attr($item->html_url) ?>" target="_">
                                                <h4><?php echo esc_html($item->title) ?></h4>
                                            </a>
                                            <?php endif ?>
                                        </div>
                                        <?php endif ?>

                                        <!-- Start Feed item overview -->
                                        <?php if($ekit_feed_card_styles != 'style1' && (!empty($item->likes_count) || !empty($item->views_count) || !empty($item->comments_count))):?> 
                                            <div class="ekit-feed-item--overview">
                                                <?php if(!empty($item->likes_count)):?>
                                                    <div class="likes">
                                                        <span><i class="icon icon-like1"></i> <?php echo esc_html($item->likes_count)?></span>
                                                    </div>
                                                <?php endif ?>
                                                <?php if(!empty($item->views_count)):?>
                                                    <div class="views">
                                                        <span><i class="icon icon-eye"></i> <?php echo esc_html($item->views_count)?></span>
                                                    </div>
                                                <?php endif ?>
                                                <?php if(!empty($item->comments_count)):?>
                                                    <div class="comments">
                                                        <span><i class="icon icon-comment2"></i> <?php echo esc_html($item->comments_count)?></span>
                                                    </div>
                                                <?php endif ?>
                                            </div>
                                        <?php endif ?>
                                        <!-- End Feed item overview -->
                                    </div>
                                </div>
                            </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                    <!-- End feed items -->
                </div>
                <!-- End Markup  -->
            <?php endif ;

		endif;

	}
}
