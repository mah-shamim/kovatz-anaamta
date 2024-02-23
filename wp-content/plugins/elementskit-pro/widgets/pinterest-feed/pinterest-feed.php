<?php

namespace Elementor;

defined('ABSPATH') || exit;

use Elementor\ElementsKit_Widget_Pinterest_Feed_Handler as Handler;


class ElementsKit_Widget_Pinterest_Feed extends Widget_Base {

	public $base;

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		wp_register_script('ekit-pinterest-feed-script-handle', Handler::get_url() . 'assets/js/script.js', ['elementor-frontend'], \ElementsKit_Lite::version(), true);

		$data['rest_url'] = get_rest_url();
		$data['nonce']    = wp_create_nonce('wp_rest');

		wp_localize_script('ekit-pinterest-feed-script-handle', 'pinterest_config', $data);
	}

	public function get_script_depends() {
		return ['ekit-pinterest-feed-script-handle'];
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
        return 'https://wpmet.com/doc/pinterest-feed-widget/';
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

    private function control_border($key, $selectors, $config = [ 'default' => '8', 'unit' => 'px', 'separator' => true, 'heading' => true ]){
		
		$selectors = array_map( function($selector) { return "{{WRAPPER}} " . $selector ;}, $selectors );

		if($config['heading']){
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
		$border_radius = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
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

    private function control_button( $name, $selector, $excludes = [] ){

        // Typography
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'		 => $name . '_typography',
			'selector'	 => '{{WRAPPER}} ' . $selector,
		]);

        if(!in_array("border", $excludes)){
            // Border
            $this->control_border( $name. '_border', [ $selector ], [ 
                'default' => '2', 'unit' => 'em', 
                'separator' => false, 'heading' => false
            ]);
        }

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

        if(!in_array('br_color', $excludes)){
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
        }

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
        
        if(!in_array('br_color', $excludes)){
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
        }

		$this->end_controls_tab();
		$this->end_controls_tabs();
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

    private function control_section_header_card(){

        // ekit_pinterest_feed_header_card_background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'header_card_background',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ekit-feed-header-pinterest',
			]
		);

		// ekit_pinterest_feed_header_card_padding
		$this->add_responsive_control( 'header_card_padding', [
            'label'          => esc_html__('Padding', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-header-pinterest' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'default'           => $this->get_dimension(16, 'px'),
            'tablet_default'    => $this->get_dimension(12, 'px'),
            'mobile_default'    => $this->get_dimension(8 , 'px'),
        ]);

		// ekit_pinterest_feed_header_card_margin
		$this->add_responsive_control( 'header_card_margin', [
            'label'          => esc_html__('Margin', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-header-pinterest' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'default'           => $this->get_dimension([0, 0, 16, 0], 'px', false),
            'tablet_default'    => $this->get_dimension([0, 0, 12, 0], 'px', false),
            'mobile_default'    => $this->get_dimension([0, 0, 8, 0], 'px', false),
        ]);

        // ekit_pinterest_feed_header_card_border
		$this->control_border( 'header_card_border', 
            [ '.ekit-feed-header-pinterest' ], [ 
				'default' => '0', 'unit' => 'px',
				'separator' => true, 'heading' => true 
			]
		);
    }

    private function control_section_profile_picture(){

        // ekit_pinterest_feed_profile_picture_size
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
                '{{WRAPPER}} .ekit-feed-header-pinterest .ekit-feed-header--thumbnail' => "height:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};",
            ],
        ]);

        // ekit_pinterest_feed_profile_picture_margin_right
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
                '{{WRAPPER}} .ekit-feed-header-pinterest .ekit-feed-header--thumbnail' => "margin-right:{{SIZE}}{{UNIT}};",
            ],
        ]);

        // ekit_pinterest_feed_profile_picture_border_radius
		$this->add_control( 'profile_picture_border_radius', [
			'label'			=> esc_html__('Border Radius', 'elementskit'),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> ['px', '%', 'em'],
			'selectors'		=> [ 
                '{{WRAPPER}} .ekit-feed-header-pinterest .ekit-feed-header--thumbnail' => 
                    'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
			'default'    => $this->get_dimension(50, '%')
		]);
    }

    private function control_section_user_name(){
		$this->control_text(
			'user_name', 
			'.ekit-feed-header-pinterest .ekit-feed-header--name', [], [
				'def_margin' => $this->get_dimension([0, 0, 6, 0], 'px', false)
			]
		);
    }

    private function control_section_user_desc(){
        $this->control_text(
			'user_desc', 
			'.ekit-feed-header-pinterest .ekit-feed-header--desc', 
			['margin', 'shadow']
		);
    }

    private function control_section_header_button(){

		// Follow button heading
		$this->add_control('header_follow_button_heading', [
			'label'     => esc_html__('Follow', 'elementskit'),
			'type'      => Controls_Manager::HEADING
		]);

        $this->control_button(
			'header_follow_button',
			'.ekit-feed-header-pinterest .ekit-feed-header--actions .btn', 
			['border', 'br_color']
		);
                  
    }

    private function control_section_top_right_logo(){

        // Circle size
		$this->add_responsive_control( 'top_right_logo_circle_size', [
            'label' => esc_html__( 'Circle Size', 'elementskit' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [ 'min' => 16, 'max' => 96, 'step' => 4 ],
                'em' => [ 'min' => 16, 'max' => 6, 'step' => 0.2 ],
            ],
            'default' => [ 'unit' => 'px', 'size' => 34 ],
            'tablet_default' => [ 'unit' => 'px', 'size' => 34 ],
            'mobile_default' => [ 'unit' => 'px', 'size' => 34 ],
            'selectors' => [
                '{{WRAPPER}} .ekit-feed-pinterest-pin .ekit-feed-pinterest-pin--top-logo' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        // Top right brand icon size
		$this->add_responsive_control( 'top_right_logo_icon_size', [
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
                '{{WRAPPER}} .ekit-feed-pinterest-pin .ekit-feed-pinterest-pin--top-logo i' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        // Circle heading
        $this->add_control( 'top_right_logo_circle_heading', [
            'label'     => esc_html__('Circle', 'elementskit'),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        // Circle position
		$this->add_responsive_control( 'top_right_logo_circle_position', [
            'label'          => esc_html__('Circle Position', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'allowed_dimensions' => ['top', 'right'],
            'selectors'      => [
                '{{WRAPPER}} .ekit-feed-pinterest-pin .ekit-feed-pinterest-pin--top-logo' => ''.
                    'top: {{TOP}}{{UNIT}};'.
                    'right: {{RIGHT}}{{UNIT}};'.
                '',
            ],
            'default'        => $this->get_dimension([16, 16, 0, 0], 'px'),
            'tablet_default' => $this->get_dimension([16, 16, 0, 0], 'px'),
            'mobile_default' => $this->get_dimension([16, 16, 0, 0], 'px'),
        ]);

        // Circle background color
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'top_right_logo_circle_background',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic'],
				'selector' => '{{WRAPPER}} .ekit-feed-pinterest-pin .ekit-feed-pinterest-pin--top-logo',
			]
        );
        
        // Circle border radius
		$this->add_control( 'top_right_logo_circle_border_radius', [
			'label'			=> esc_html__('Border Radius', 'elementskit'),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> ['px', '%', 'em'],
			'selectors'		=> [ 
                '{{WRAPPER}} .ekit-feed-pinterest-pin .ekit-feed-pinterest-pin--top-logo' => 
                    'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
			'default'    => $this->get_dimension(50, '%')
		]);

        // Icon heading
        $this->add_control( 'top_right_logo_icon_heading', [
            'label'     => esc_html__('Icon', 'elementskit'),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        // Top right brand icon
        $this->add_control( 'top_right_logo_icons', [
            'label' => esc_html__( 'Header Icon', 'elementskit' ),
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'top_right_logo_icon',
            'default' => [
                'value' => 'fab fa-pinterest-p',
                'library' => 'fa-brands',
            ],
            'label_block' => true
        ]);

        // Top right brand icon color
        $this->add_control( 'top_right_logo_icon_color', [
            'label'     => esc_html__('Icon Color', 'elementskit'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .ekit-feed-pinterest-pin .ekit-feed-pinterest-pin--top-logo i' => 'color: {{VALUE}}',
            ],
        ]);
    }

	protected function register_controls() {

		$this->start_controls_section(
			'ekit_lite_section_content',
			[
				'label' => esc_html__('Settings', 'elementskit'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'pinterest_user_name',
			[
				'label'       => esc_html__('Username', 'elementskit'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__('johndoe', 'elementskit'),
			]
		);


		$this->add_control(
			'pinterest_feed_type',
			[
				'label'   => esc_html__('Feed Type', 'elementskit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'home',
				'options' => [
					'home'  => esc_html__('Home Feed', 'elementskit'),
					'board' => esc_html__('Board Feed', 'elementskit'),
				],
			]
		);

		$this->add_control(
			'pinterest_board_name',
			[
				'label'       => esc_html__('Board name', 'elementskit'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__('Pinterest Board name', 'elementskit'),
				'condition'   => ['pinterest_feed_type' => 'board'],
			]
		);

		// Pins to show
		$this->add_control(
			'pin_to_show',
			[
				'label'   => esc_html__('Pin to Show', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'default' => 9,
				'min'     => 1,
				'max'     => 100,
				'step'    => 1,

			]
		);

		$this->end_controls_section();


		// ==========================
		// Start Layout Section
		// ==========================
		$this->start_controls_section(
			'ekit_feed_pins_section', [
				'label' => esc_html__('Layout', 'elementskit'),
			]
		);

		// Pins card style
		$this->add_control(
			'ekit_feed_pins_card_style',
			[
				'label'   => esc_html__('Cards Style', 'elementskit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'masonry',
				'options' => [
					'grid'    => esc_html__('Grid', 'elementskit'),
					'masonry' => esc_html__('Masonry', 'elementskit'),
				],
			]
		);

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

		// ekit_feed_pins_grid_col_gap
		$this->add_responsive_control(
			'ekit_feed_pins_grid_col_gap',
			[
				'label'          => esc_html__('Column Gap', 'elementskit'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['px', 'em'],
				'range'          => [
					'px' => ['min' => 0, 'max' => 64, 'step' => 1],
					'em' => ['min' => 0, 'max' => 4, 'step' => 0.1],
				],
				'default'        => ['unit' => 'em', 'size' => 1],
				'tablet_default' => ['unit' => 'em', 'size' => 0.75],
				'mobile_default' => ['unit' => 'em', 'size' => 0.5],
				'selectors'      => [
					'{{WRAPPER}} .ekit-feed-pinterest-pins .row'       => 
						'margin-right: calc(-{{SIZE}}{{UNIT}} / 2);margin-left: calc(-{{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .ekit-feed-pinterest-pins .row > div' => 
						'padding-right: calc({{SIZE}}{{UNIT}} / 2);padding-left: calc({{SIZE}}{{UNIT}} / 2);padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'ekit_feed_pins_card_style' => 'grid',
				],
			]
		);

		// ekit_feed_pins_masonry_col_gap
		$this->add_responsive_control(
			'ekit_feed_pins_masonry_col_gap',
			[
				'label'          => esc_html__('Column Gap', 'elementskit'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['px', 'em'],
				'range'          => [
					'px' => ['min' => 0, 'max' => 64, 'step' => 1],
					'em' => ['min' => 0, 'max' => 4, 'step' => 0.1],
				],
				'default'        => ['unit' => 'em', 'size' => 1],
				'tablet_default' => ['unit' => 'em', 'size' => 0.75],
				'mobile_default' => ['unit' => 'em', 'size' => 0.5],
				'selectors'      => [
					'{{WRAPPER}} .ekit-feed-wrapper-pinterest .ekit-layout-masonary' => 
						'column-gap: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .ekit-feed-wrapper-pinterest .ekit-feed-pinterest-pin' => 
						'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'      => [
					'ekit_feed_pins_card_style' => 'masonry',
				],
			]
		);

		// ekit_feed_pins_masonry_col_gap
		$this->add_responsive_control(
			'grid_column_height',
			[
				'label'          => esc_html__('Column Height', 'elementskit'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['px', 'em'],
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 1080,
						'step' => 4
					],
				],
				'default'        => ['unit' => 'px', 'size' => 250],
				'selectors'      => [
					'{{WRAPPER}} .ekit-feed-wrapper-pinterest .row.ekit-layout-grid img' => 
						'height: {{SIZE}}{{UNIT}};'
				],
				'condition'      => [
					'ekit_feed_pins_card_style' => 'grid',
				],
			]
		);

		$this->end_controls_section();
		// ==========================
		// End Layout Section
		// ==========================


		// ==========================
		// Start widget style section
		// ==========================
		$this->start_controls_section(
			'ekit_feed_widget_style_section_heading', [
				'label' => esc_html__('Widget styles', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// ekit_review_widget_background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'ekit_feed_widget_background',
				'label'    => esc_html__('Widget Background', 'elementskit'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ekit-feed-wrapper-pinterest',
			]
		);

		// Widget padding
		$this->add_responsive_control(
			'ekit_feed_widget_padding',
			[
				'label'          => esc_html__('Padding', 'elementskit'),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => ['px', '%', 'em'],
				'selectors'      => [
					'{{WRAPPER}} .ekit-feed-wrapper-pinterest' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default'        => $this->get_dimension(1),
				'tablet_default' => $this->get_dimension(8, 'px'),
				'mobile_default' => $this->get_dimension(8, 'px'),
			]
		);

		$this->end_controls_section();
		// ==========================
		// End widget style section
        // ==========================
        
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

        // User description
        $this->controls_section([
            'name'      => 'user_desc',
            'label'     => esc_html__('User Description', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);

        // Header buttons
        $this->controls_section([
            'name'      => 'header_button',
            'label'     => esc_html__('Header Button', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);


		// ==========================
		// Start Pins container styles
		// ==========================
		$this->start_controls_section(
			'ekit_feed_container_styles_section', [
				'label' => esc_html__('Pins Container', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// ekit_feed_container_background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'ekit_feed_container_background',
				'label'    => esc_html__('Widget Background', 'elementskit'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ekit-feed-pinterest-pins, {{WRAPPER}} .ekit-feed-items-load-more',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'	=> 'ekit_feed_container_border',
				'label'	=> esc_html__( 'Border', 'elementskit' ),
				'selector'	=> '{{WRAPPER}} .ekit-feed-pinterest-pins',
                'separator'	=> 'before'
			]
		);

		// Feed container padding
		$this->add_responsive_control(
			'ekit_feed_container_padding',
			[
				'label'          => esc_html__('Padding', 'elementskit'),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => ['px', '%', 'em'],
				'selectors'      => [
					'{{WRAPPER}} .ekit-feed-pinterest-pins' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
				'default'        => $this->get_dimension(1),
				'tablet_default' => $this->get_dimension(0.75),
				'mobile_default' => $this->get_dimension(0.5),
                'separator'	=> 'before'
			]
		);

		$this->end_controls_section();

		// ==========================
		// End Pins container styles
		// ==========================

		// ==========================
		// Start feed pin styles
		// ==========================
		$this->start_controls_section(
			'ekit_feed_pins_styles_section', [
				'label' => esc_html__('Pin Card', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_feed_pin_border_radius', [
				'label'      => esc_html__('Border Radius', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'default'    => [
					'top'      => '0.25',
					'right'    => '0.25',
					'bottom'   => '0.25',
					'left'     => '0.25',
					'unit'     => 'em',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-feed-pinterest-pin' =>
						'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		// ==========================
		// End feed pin styles
        // ==========================
        
        // Top right logo
        $this->controls_section([
            'name'      => 'top_right_logo',
            'label'     => esc_html__('Top Right Logo', 'elementskit'),
            'tab'       => Controls_Manager::TAB_STYLE
        ]);

	}


	protected function render() {
		echo '<div class="ekit-wid-con" >';
		$this->render_raw();
		echo '</div>';
	}


	protected function render_raw() {

		$settings = $this->get_settings_for_display();

		if(empty($settings['pinterest_user_name'])): ?>
			<div><strong><?php echo esc_html__('Please set a valid username first', 'elementskit'); ?></strong></div>
		<?php else:

			$board_name = '';

			if($settings['pinterest_feed_type'] == 'board') :

				if(empty($settings['pinterest_board_name'])) : ?>
					<div><strong><?php echo esc_html__('Please set a valid board name first', 'elementskit'); ?></strong></div>
				<?php return '';

				endif;

				$board_name = str_replace(' ','-', $settings['pinterest_board_name']);
			endif;

			$data = Handler::get_the_feed($settings['pinterest_user_name'], $settings['pinterest_feed_type'], $board_name);

			if(empty($data['data']['item'])) : ?>
				<div><strong><?php echo esc_html__('0 items fetched!....', 'elementskit'); ?></strong></div>
				<?php return '';
			endif;

			$user        = $data['data'];
			$items       = $data['data']['item'];
			$handler_url = Handler::get_url();
			$column_count = $this->format_column($settings, 'ekit_responsive_column');

			?>

            <!-- Start Markup  -->
            <div class="ekit-feed-wrapper ekit-feed-wrapper-pinterest">

                <!-- Start Header -->
				<?php require Handler::get_dir() . 'markup/header.php' ?>

                <!-- Start feed items -->
                <div class="ekit-feed-items-wrapper ekit-feed-items-wrapper-pinterest">

                    <!-- Start Tabbar -->
					<?php /* require Handler::get_dir() . 'markup/tab-bar.php' */ ?>

                    <!-- Start boards -->
                    <!-- <div class="ekit-feed-pinterest-boards visible" style='padding: 2rem;'>
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <?php /* require Handler::get_dir() . 'markup/board.php' */ ?>
                            </div>
                        </div>
                    </div> -->
                    <!-- End boards -->

                    <!-- Start pins -->
                    <div class="ekit-feed-pinterest-pins visible">

                        <!-- Start masonry style -->
						<?php if($settings['ekit_feed_pins_card_style'] == 'masonry'): ?>
                            <div class='masonary ekit-fb-row ekit-layout-masonary <?php echo esc_attr( $column_count ); ?>'>
								<?php foreach($items as $index => $item):
									if($index < $settings['pin_to_show']):
										require Handler::get_dir() . 'markup/pin.php';
									endif;
								endforeach ?>
                            </div>
                            <!-- End masonry style -->

                            <!-- Start grid style -->
						<?php elseif($settings['ekit_feed_pins_card_style'] == 'grid'): ?>
                            <div class="row ekit-fb-row ekit-layout-grid">
								<?php foreach($items as $index => $item):
									if($index < $settings['pin_to_show']): ?>
										<div class="<?php echo esc_attr($column_count) ?>">
											<?php require Handler::get_dir() . 'markup/pin.php' ?>
										</div>
									<?php endif;
								endforeach ?>
                            </div>
						<?php endif ?>
                        <!-- End grid style -->

                    </div>
                    <!-- End pins -->

                    <!-- Start Load more -->
					<?php /* require Handler::get_dir() . 'markup/load-more.php' */ ?>

                </div>
                <!-- End feed items -->
            </div>
            <!-- End Markup  -->

			<?php

		endif;
	}
}
