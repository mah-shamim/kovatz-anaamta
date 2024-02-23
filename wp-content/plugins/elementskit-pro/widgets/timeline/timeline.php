<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_timeline_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_timeline extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;

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
        return 'https://wpmet.com/doc/timeline/';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'ekit_timeline_content_section',
            [
                'label' => esc_html__( 'Content', 'elementskit' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'ekit_timeline_style',
            [
                'label' => esc_html__( 'Time line Style', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'vertical',
                'options' => [
                    'vertical'  => esc_html__( 'Vertical', 'elementskit' ),
                    'horizontal' => esc_html__( 'Horizontal', 'elementskit' ),
                ],
            ]
        );

        $this->add_control(
            'ekit_timeline_vertical_style',
            [
                'label' => esc_html__( 'Content Style', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'bothside',
                'options' => [
                    'oneside'  => esc_html__( 'Same Side', 'elementskit' ),
                    'bothside' => esc_html__( 'Both side', 'elementskit' ),
                ],
                'condition' => [
                    'ekit_timeline_style' => 'vertical',
                ]
            ]
        );
        
        $this->add_control(
            'ekit_timeline_horizontal_style',
            [
                'label' => esc_html__( 'Content Position', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'top',
                'options' => [
                    'top'  => esc_html__( 'Top', 'elementskit' ),
                    'bottom' => esc_html__( 'Bottom', 'elementskit' ),
                ],
                'condition' => [
                    'ekit_timeline_style' => 'horizontal',
                ]
            ]
        );


        $repeater = new Repeater();

        $repeater->add_control(
            'ekit_timeline_line_subtitle', [
                'label' => esc_html__( 'Sub Title', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Subtitle' , 'elementskit' ),
                'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $repeater->add_control(
            'ekit_timeline_line_title', [
                'label' => esc_html__( 'Title', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Title' , 'elementskit' ),
                'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
            ]
        );
        $repeater->add_control(
			'ekit_timeline_date_link',
			[
				'label' => esc_html__( 'Title Link', 'elementskit' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://example.com', 'elementskit' ),
				'show_external' => true,
				'default' => [
					'url' => '',
                ],
				'dynamic' => [
					'active' => true,
				],
			]
		);
        $repeater->add_control(
            'ekit_timeline_line_content',
            [
                'label' => esc_html__( 'Description', 'elementskit' ),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 5,
                'default' => esc_html__( 'Default description', 'elementskit' ),
                'placeholder' => esc_html__( 'Type your description here', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
            ]
        );
         $repeater->add_control(
            'ekit_timeline_title_icons',
            [
                'label' => esc_html__( 'Title Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_timeline_title_icon',
                'default' => [
                    'value' => 'icon icon-trophy',
                    'library' => 'ekiticons',
                ],
            ]
        );
        $repeater->add_control(
            'ekit_timeline_content_date', [
                'label' => esc_html__( 'Date', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( '01 February 2015' , 'elementskit' ),
                'label_block' => true,
                'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
            ]
        );
        $repeater->add_control(
            'ekit_timelinehr_content_address', [
                'label' => esc_html__( 'Address', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'New Office, CA' , 'elementskit' ),
                'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
            ]
        );



        $repeater->add_control(
            'ekit_timelinehr_repeater_style',
            [
                'label' => esc_html__( 'Repeater Item Style', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->start_controls_tabs('ekit_timeline_repeater_style_tab');

        $repeater->start_controls_tab(
			'ekit_timeline_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);
        $repeater->add_control(
			'ekit_timelinearrow_subtitle_color',
			[
				'label' => esc_html__( 'Sub Title Color ', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .timeline-item .subtitle' => 'color: {{VALUE}};',
				],
			]
		);
        $repeater->add_control(
            'ekit_timeline_item_background_color_group',
            [
                'label' => esc_html__( 'Content box Background', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con {{CURRENT_ITEM}} .timeline-item, {{WRAPPER}} .ekit-wid-con {{CURRENT_ITEM}} .single-timeline .timeline-item .timeline-icon, {{WRAPPER}} .ekit-wid-con {{CURRENT_ITEM}} .single-timeline .timeline-item .timeline-icon'=>'background-color:{{VALUE}};',
				    '{{WRAPPER}} .ekit-wid-con .vertical-timeline {{CURRENT_ITEM}}:nth-child(odd) .timeline-pin' => 'border-color: {{VALUE}} {{VALUE}} transparent transparent;', 
				    '{{WRAPPER}} .ekit-wid-con .vertical-timeline {{CURRENT_ITEM}}:nth-child(even) .timeline-pin' => 'border-color: transparent transparent {{VALUE}} {{VALUE}};', 
                    '{{WRAPPER}} .ekit-wid-con .horizantal-timeline {{CURRENT_ITEM}} .timeline-pin' => 'border-color: transparent transparent {{VALUE}} {{VALUE}};',
                    '{{WRAPPER}} .ekit-wid-con .horizantal-timeline {{CURRENT_ITEM}} .pin-top' => 'border-color: {{VALUE}} {{VALUE}} transparent transparent;',
				],

            ]
        );
		$repeater->add_control(
			'ekit_timeline_item_icon_color_section_title',
			[
				'label' => esc_html__( 'Icon section', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'ekit_timeline_item_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .timeline-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .timeline-icon svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};'
				],
			]
		);
        $repeater->add_control(
            'ekit_timeline_item_icon_bg_color_group',
            [
                'label' => esc_html__( 'Icon Background', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  {{CURRENT_ITEM}} .timeline-icon'=>'background-color:{{VALUE}}',
				],

            ]
        );


       $repeater->end_controls_tab();
        //  Hover style
       $repeater->start_controls_tab(
			'ekit_timeline_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

       $repeater->add_control(
			'ekit_timelinearrow_subtitle_color_hover',
			[
				'label' => esc_html__( 'Sub Title Color ', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover .timeline-item .subtitle' => 'color: {{VALUE}}!important;',
				],
			]
		);


         $repeater->add_control(
            'ekit_timeline_item_background__hv_color_group',
            [
                'label' => esc_html__( 'Content box Background', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con {{CURRENT_ITEM}}:hover .timeline-item, {{WRAPPER}} .ekit-wid-con {{CURRENT_ITEM}}:hover .single-timeline .timeline-item .timeline-icon, {{WRAPPER}} .ekit-wid-con {{CURRENT_ITEM}}:hover .single-timeline .timeline-item .timeline-icon'=>'background-color:{{VALUE}}',
                    '{{WRAPPER}} .ekit-wid-con .vertical-timeline {{CURRENT_ITEM}}:nth-child(odd):hover .timeline-pin' => 'border-color: {{VALUE}} {{VALUE}} transparent transparent;', 
				    '{{WRAPPER}} .ekit-wid-con .vertical-timeline {{CURRENT_ITEM}}:nth-child(even):hover .timeline-pin' => 'border-color: transparent transparent {{VALUE}} {{VALUE}};', 
                    '{{WRAPPER}} .ekit-wid-con .horizantal-timeline {{CURRENT_ITEM}}:hover .timeline-pin' => 'border-color: transparent transparent {{VALUE}} {{VALUE}};',
                    '{{WRAPPER}} .ekit-wid-con .horizantal-timeline {{CURRENT_ITEM}}:hover .pin-top' => 'border-color: {{VALUE}} {{VALUE}} transparent transparent;',
				],

            ]
        );

        // Icon
        $repeater->add_control(
			'ekit_timeline_item_icon_color_section_title_hv',
			[
				'label' => esc_html__( 'Icon section', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'ekit_timeline_item_icon_color_hv',
			[
				'label' => esc_html__( 'Icon Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}:hover .timeline-icon i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} {{CURRENT_ITEM}}:hover .timeline-icon svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};'
				],
			]
		);
        $repeater->add_control(
            'ekit_timeline_item_icon_bg_color_hv_group',
            [
                'label' => esc_html__( 'Icon Background', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}:hover .timeline-icon'=>'background-color:{{VALUE}}',
				],

            ]
        );

        $repeater->end_controls_tab();

		$repeater->end_controls_tabs();


        $this->add_control(
            'ekit_timelinehr_content_repeater',
            [
                'label' => esc_html__( 'Time Line Content', 'elementskit' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'ekit_timeline_line_title' => esc_html__( 'Title #1', 'elementskit' ),
                        'ekit_timeline_line_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'elementskit' ),
                    ],
                    [
                        'ekit_timeline_line_title' => esc_html__( 'Title #1', 'elementskit' ),
                        'ekit_timeline_line_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'elementskit' ),
                    ],
                    [
                        'ekit_timeline_line_title' => esc_html__( 'Title #1', 'elementskit' ),
                        'ekit_timeline_line_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'elementskit' ),
                    ],
                ],
                'title_field' => '{{{ ekit_timeline_line_title }}}',

            ]
        );

        $this->end_controls_section();

        // Settings

        $this->start_controls_section(
            'ekit_timeline_setting_section',
            [
                'label' => esc_html__( 'Settings', 'elementskit' ),
                'tab' => Controls_Manager::TAB_CONTENT,

            ]
        );
          $this->add_control(
			'ekit_timeline_date_icons',
			[
				'label' => esc_html__( 'Date Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_timeline_date_icon',
                'default' => [
                    'value' => '',
                ],
                'condition' => [
                    'ekit_timeline_style' => 'vertical',
                ]
			],
            
		);
          $this->add_control(
			'ekit_timelinehr_address_icons',
			[
				'label' => esc_html__( ' Address Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_timelinehr_address_icon',
                'default' => [
                    'value' => '',
                ],
                'condition' => [
                    'ekit_timeline_style' => 'vertical',
                ]
			]
		);
           //  Item animation
        $this->add_control(
			'ekit_timeline_left_entrance_animation',
			[
				'label' => esc_html__( 'Content box Entrance Animation', 'elementskit' ),
				'type' => Controls_Manager::ANIMATION,
			]
		);

        $this->add_control(
			'ekit_timeline_right_entrance_animation',
			[
				'label' => esc_html__( 'Address, date Entrance Animation', 'elementskit' ),
				'type' => Controls_Manager::ANIMATION,
			]
		);
        $this->add_control(
            'ekit_timelinehr_pinpoint_icon',
            [
                'label' => esc_html__( 'Pinpoint Style', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'doted',
                'options' => [
                    'doted'  => esc_html__( 'default', 'elementskit' ),
                    'icon' =>esc_html__( 'Icon', 'elementskit' ),
                ],
                'condition' => [
                    'ekit_timeline_style' => 'vertical',
                ]
            ]
        );

        $this->add_control(
            'ekit_timeline_icons',
            [
                'label' => esc_html__( 'Pinpoint Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_timeline_icon',
                'default' => [
                    'value' => 'icon icon-star1',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                        'ekit_timelinehr_pinpoint_icon' => 'icon',
                ]
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'ekit_timeline_style_section',
            [
                'label' => esc_html__( 'Content', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'ekit_timelinehr_alignment',
            [
                'label' => esc_html__( 'Alignment', 'elementskit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'elementskit' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'elementskit' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'elementskit' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__( 'justify', 'elementskit' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .single-timeline .timeline-content' => 'text-align: {{VALUE}} !important;',
                ],
            ]
        );
        		$this->add_responsive_control(
			'ekit_timeline__container_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .timeline-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

		$this->add_responsive_control(
			'ekit_timeline__container_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .timeline-item .timeline-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

		$this->add_responsive_control(
			'ekit_timeline__container_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .timeline-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );


        $this->start_controls_tabs(
            'ekit_timeline_style_content_tabs'
        );

        $this->start_controls_tab(
            'ekit_timeline_style_content_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_timeline_subtitle_color_nl',
            [
                'label' => esc_html__( 'Sub Title Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .timeline-content .subtitle' => 'color: {{VALUE}}',
                ],
            ]
        );

         $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_timeline_content__sub_title_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}}  .subtitle',
			]
        );

        $this->add_responsive_control(
			'ekit_timeline_content__sub_title_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .single-timeline .subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

		$this->add_control(
			'ekit_timeline_content__sub_title_margin_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

        $this->add_control(
            'ekit_timeline_content_title_color',
            [
                'label' => esc_html__( 'Title Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .single-timeline .title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'ekit_timeline_content_title_color_a',
            [
                'label' => esc_html__( 'Title Anchor Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .single-timeline .title a' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_timeline_content_title_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .single-timeline .title',
			]
        );

        $this->add_responsive_control(
			'ekit_timeline_content_title_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .single-timeline .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

		$this->add_control(
			'ekit_timeline_content__title_margin_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

        $this->add_control(
            'ekit_timeline_content_color',
            [
                'label' => esc_html__( 'Content Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .single-timeline .timeline-content p' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_timeline_content_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .single-timeline .timeline-content p',
			]
        );

        $this->add_responsive_control(
			'ekit_timeline_content_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .single-timeline .timeline-content p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_control(
            'ekit_timeline_arrow_separator',
            [
                'label' => esc_html__( 'Arrow', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'ekit_timeline_arrow_size',
            [
                'label' => esc_html__( 'Arrow Size (px)', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 2,
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 40,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .vertical-timeline .timeline-pin' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; right: calc(-{{SIZE}}{{UNIT}} / 2); border-width: calc({{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .ekit-wid-con .vertical-timeline .single-timeline:nth-child(even) .timeline-pin' => 'right: inherit; left: calc(-{{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .ekit-wid-con .horizantal-timeline .single-timeline .timeline-pin' => 'top: calc(100% - {{SIZE}}{{UNIT}}/2); width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; border-width: calc({{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .ekit-wid-con .horizantal-timeline .single-timeline .pin-top' => 'top: unset; bottom: calc(100% - {{SIZE}}{{UNIT}}/2);',
                ],
            ]
        );

        $this->add_control(
            'ekit_timeline_round_color_separator',
            [
                'label' => esc_html__( 'Round icon', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'ekit_timeline_icon_color',
            [
                'label' => esc_html__( 'Round Icon Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .timeline-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .timeline-icon svg path'   => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'ekit_timeline_item_icon_bg_color_group',
            [
                'label' => esc_html__( 'Round Icon Background', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .timeline-icon'=>'background-color:{{VALUE}}',
				],

            ]
        );
        $this->add_responsive_control(
			'ekit_timeline_icon_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .horizantal-timeline .timeline-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_timeline_style' => 'horizontal',
                ]
			]
		);

        $this->add_control(
            'ekit_timeline_timeline_more_options',
            [
                'label' => esc_html__( 'Timeline info', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'ekit_timeline_icon_date_color',
            [
                'label' => esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-style .single-timeline .timeline-info .date' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .floating-style .single-timeline .timeline-info .date a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .horizantal-timeline .bottom-content .date' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .single-timeline .timeline-info .date svg path'    => 'stroke: {{VALUE}}; fill: {{VALUE}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_timeline_icon_date_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .single-timeline .timeline-info .date i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .single-timeline .timeline-info .date svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_timeline_icon_date_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .single-timeline .timeline-info .date, {{WRAPPER}} .horizantal-timeline .bottom-content .date', 
			]
        );

        $this->add_responsive_control(
			'ekit_timeline_icon_date_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .single-timeline .timeline-info .date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .horizantal-timeline .bottom-content .date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );



        $this->add_control(
            'ekit_timeline_timeline_address_head',
            [
                'label' => esc_html__( 'Address', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_control(
            'ekit_timeline_icon_address_color',
            [
                'label' => esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-style .single-timeline .timeline-info p' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .single-timeline .timeline-info p' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .single-timeline .timeline-info .place svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_timeline_style' => 'vertical',
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_timeline_icon_address_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .single-timeline .timeline-info .place i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .single-timeline .timeline-info .place svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_timeline_icon_address_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .single-timeline .timeline-info .place',
			]
        );

        $this->add_responsive_control(
			'ekit_timeline_icon_address_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .single-timeline .timeline-info p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_timeline_style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_timeline_subtitle_color_hv',
            [
                'label' => esc_html__( 'Sub Title Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .single-timeline:hover .timeline-content .subtitle' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .single-timeline:hover .timeline-content .subtitle' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_timeline_content_title_color_hv',
            [
                'label' => esc_html__( 'Title Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .single-timeline:hover .timeline-item .title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .single-timeline:hover .timeline-item .title a' => 'color: {{VALUE}}',
                ],
            ]
        );$this->add_control(
            'ekit_timeline_content_title_color_a_hv',
            [
                'label' => esc_html__( 'Title Anchor Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .single-timeline .timeline-item .title a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_timeline_content_color_hv',
            [
                'label' => esc_html__( 'Content Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .single-timeline:hover .timeline-content p' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_timeline_icon_color_hv',
            [
                'label' => esc_html__( 'Round Icon Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .timeline-area .timeline-item:hover .timeline-icon>i' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'ekit_timeline_item_icon_bg_color_group_hv',
            [
                'label' => esc_html__( 'Round Icon Background', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .timeline-item:hover .timeline-icon'=>'background-color:{{VALUE}}',
				],

            ]
        );

        $this->add_control(
            'ekit_timeline_more_options_hover',
            [
                'label' => esc_html__( 'Timeline info', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'ekit_timeline_icon_date_color_hv',
            [
                'label' => esc_html__( 'Date', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .single-timeline:hover .timeline-info .date' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .single-timeline:hover .timeline-info .date a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_timeline_icon_address_color_hv',
            [
                'label' => esc_html__( 'Address', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .single-timeline:hover .timeline-info p' => 'color: {{VALUE}}',

                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // Line color

        $this->start_controls_section(
			'ekit_timeline_style_line_section',
			[
				'label' => esc_html__( 'Line ', 'elementskit' ),
				'tab' =>   Controls_Manager::TAB_STYLE,
			]
        );

        $this->add_control(
            'ekit_timeline_line_color',
            [
                'label' => esc_html__( 'Line Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .timeline-bar' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .horizantal-timeline .bar' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_timeline_pin_color',
            [
                'label' => esc_html__( 'Pin Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .timeline-img' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .horizantal-timeline .bar .pin' => 'background-color: {{VALUE}}',
                ],

                'condition' => [
                    'ekit_timelinehr_pinpoint_icon' => 'doted',
                ]

            ]
        );

        $this->add_control(
            'ekit_timeline_pin_active_border_color',
            [
                'label' => esc_html__( 'Active Pin Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .horizantal-timeline .single-timeline.hover .bar .pin' => 'border-color: {{VALUE}}',
                ],

                'condition' => [
                    'ekit_timelinehr_pinpoint_icon' => 'doted',
                ]

            ]
        );

        $this->add_control(
            'ekit_timeline_pin_hover_color',
            [
                'label' => esc_html__( 'Pinpoint Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .timeline-img:before' => 'background-color: {{VALUE}}',
                ],

                'condition' => [
                    'ekit_timelinehr_pinpoint_icon' => 'doted',
                    'ekit_timeline_style' => 'vertical',
                ]

            ]
        );

        $this->start_controls_tabs(
            'ekit_timeline_line_style_tabs',
            [
                'condition' => [
                    'ekit_timelinehr_pinpoint_icon' => 'icon',
                ]
            ]
        );

		$this->start_controls_tab(
			'ekit_timeline_line_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
        );

        $this->add_control(
			'ekit_timeline_line_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'elementskit' ),
                'type' =>  Controls_Manager::COLOR,
                'default'=> '#2575fc',
				'selectors' => [
                    '{{WRAPPER}} .timeline-pin-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .timeline-pin-icon svg path'   => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ]
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_timeline_line_icon_background_group',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .single-timeline .timeline-pin-icon'
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_timeline_line_icon_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .timeline-pin-icon'
			]
		);

		$this->add_responsive_control(
			'ekit_timeline_line_icon_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .timeline-pin-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
			'ekit_timeline_line_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
        );

        $this->add_control(
			'ekit_timeline_line_icon_color_hover',
			[
				'label' => esc_html__( 'Icon Color', 'elementskit' ),
                'type' =>  Controls_Manager::COLOR,
                'default'=> '#000',
				'selectors' => [
                    '{{WRAPPER}} .single-timeline:hover .timeline-pin-icon i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .single-timeline:hover .timeline-pin-icon svg path'   => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ]
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_timeline_line_icon_background_hover_group',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .single-timeline:hover .timeline-pin-icon'
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_timeline_line_icon_border_hover',
				'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .single-timeline:hover .timeline-pin-icon'
			]
		);

		$this->add_responsive_control(
			'ekit_timeline_line_icon_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .single-timeline:hover .timeline-pin-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		  $this->start_controls_section(
			'ekit_timeline__container_style_tab',
			[
				'label' => esc_html__( 'Container', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);



        $this->add_responsive_control(
			'ekit_timeline__container_info_margin_right',
			[
				'label' => esc_html__( 'Info (Address, date) Gap', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 60,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 60,
				],
				'selectors' => [
					'{{WRAPPER}} .vertical-timeline .single-timeline:nth-child(even) .timeline-info' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .vertical-timeline .single-timeline:nth-child(odd) .timeline-info' => 'margin-left: {{SIZE}}{{UNIT}};',

                ],
                'condition' => [
                    'ekit_timeline_vertical_style' => 'bothside'
                ]
			]
		);



        $this->add_responsive_control(
			'ekit_timeline__item_margin_bottom',
			[
				'label' => esc_html__( 'Item Bottom Spacing', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .single-timeline:not(:nth-last-child(2)), {{WRAPPER}} .horizantal-timeline > .single-timeline' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_timeline_vertical_style' => 'bothside'
                ]
			]
		);


		$this->end_controls_section();

        $this->insert_pro_message();
    }

    /**
     * has_animation
     * 
     * @private
     * Used for checking if 'left_animation' and 'right_animation' controls has values set
     */
    private function has_animation($dir) {
        $settings = $this->get_settings_for_display();
        $name = 'ekit_timeline_'. $dir .'_entrance_animation';

        if ( $settings[ $name ] && $settings[ $name ] !== 'none' ):
            return ' elementskit-invisible';
        endif;
        
        return '';
    }

    protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {

        $settings = $this->get_settings_for_display();

        if($settings['ekit_timeline_style'] == 'vertical') {

            $this->add_render_attribute( 'timeline', 'class', 'timeline-area vertical-timeline  multi-gradient floating-style' );
          }

          if($settings['ekit_timeline_style'] == 'horizontal') {

           $this->add_render_attribute( 'timeline', 'class', 'timeline-area horizantal-timeline clearfix' );

         }

          $this->add_render_attribute( 'timeline', 'class', $settings['ekit_timeline_vertical_style'] );

      ?>
        <div <?php echo \ElementsKit_Lite\Utils::render($this->get_render_attribute_string( 'timeline' )); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Displaying with Elementor content rendering ?> > 

         <?php

             $contents = $settings['ekit_timelinehr_content_repeater'];
             $count = 0;
             foreach ($contents as $content) :

            $left_entranceAnimation = [
                    '_animation' => $settings['ekit_timeline_left_entrance_animation']
            ];

              $right_entranceAnimation = [
                    '_animation' => $settings['ekit_timeline_right_entrance_animation']
            ];
            $link_before = $link_after = '';
            if ( ! empty( $content['ekit_timeline_date_link']['url'] ) ) {

                if ( ! empty( $settings['ekit_timeline_date_link']['url'] ) ) {
                    $this->add_link_attributes( 'info_link', $settings['ekit_timeline_date_link'] );
                }

                $link_before .= "<a ".$this->get_render_attribute_string( 'info_link' ).">";
                    $link_after .= '</a>';
            }

             if($settings['ekit_timeline_style'] == 'vertical') : ?>

                    <div class="single-timeline media single-timeline-count-<?php echo esc_attr(($count+1)); ?> elementor-repeater-item-<?php echo esc_attr( $content[ '_id' ] ); ?>" >
                            <div class="timeline-item media<?php echo esc_attr($this->has_animation('left')); ?>" data-settings="<?php echo esc_attr(json_encode($left_entranceAnimation)); ?>">
                                <div class="timeline-content">

                                     <?php if($content['ekit_timeline_line_subtitle'] != '') : ?>
                                        <h2 class="subtitle"><?php echo esc_html($content['ekit_timeline_line_subtitle']); ?></h2>
                                    <?php endif; ?>

                                    <?php
                                        if($content['ekit_timeline_line_title'] != '') {
                                            if ( ! empty( $content['ekit_timeline_date_link']['url'] ) ) {
                                    ?>
                                        <h3 class="title"><a href="<?php echo esc_url( $content['ekit_timeline_date_link']['url'] ); ?>" rel="<?php echo esc_attr($content['ekit_timeline_date_link']['nofollow'] ? 'nofollow' : '' ); ?>" target="<?php echo esc_attr($content['ekit_timeline_date_link']['is_external'] ? '_blank' : '_self' ); ?>"><?php echo wp_kses(\ElementsKit_Lite\Utils::kses($content['ekit_timeline_line_title']), \ElementsKit_Lite\Utils::get_kses_array()); ?></a></h3>
                                    <?php
                                    } else { ?>
                                        <h3 class="title"><?php echo wp_kses(\ElementsKit_Lite\Utils::kses($content['ekit_timeline_line_title']), \ElementsKit_Lite\Utils::get_kses_array()); ?></h3>
                                    <?php };
                                    } ?>

                              <?php if($content['ekit_timeline_line_content'] != '') : ?>
                                    <p><?php echo wp_kses(\ElementsKit_Lite\Utils::kses($content['ekit_timeline_line_content']), \ElementsKit_Lite\Utils::get_kses_array()); ?></p>
                              <?php endif;
                              ?>



                            <?php if($settings['ekit_timeline_style'] == 'vertical' && $settings['ekit_timeline_vertical_style'] == 'oneside') : ?>

                            <div class="timeline-info timeline-info-onside<?php echo esc_attr($this->has_animation('right')); ?>" data-settings="<?php echo esc_attr(json_encode($right_entranceAnimation)); ?>">
                            <?php if($content['ekit_timeline_content_date'] != '') : ?>

                            <h4 class="date">
                                <?php
                                    Icons_Manager::render_icon( $settings['ekit_timeline_date_icons'], [ 'aria-hidden' => 'true' ] );
                                ?>
                            <?php endif;
                                if($content['ekit_timeline_content_date'] != '') :
                                echo \ElementsKit_Lite\Utils::render( esc_html( $content['ekit_timeline_content_date'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Displaying with Elementor content rendering ?> </h4>

                            <?php endif;

                            if($content['ekit_timelinehr_content_address'] != '') : ?>

                            <p class="place">
                            <?php
                            if($settings['ekit_timelinehr_address_icons']) :
                                    Icons_Manager::render_icon( $settings['ekit_timelinehr_address_icons'], [ 'aria-hidden' => 'true' ] );
                                 endif;
                                 echo '<span> '. esc_html($content['ekit_timelinehr_content_address']).'</span>';

                             endif; ?>
                            </p>
                            </div>
                            <?php endif; ?>

                                </div><!-- .timeline-content END -->
                                 <?php if(!empty($content['ekit_timeline_title_icons']) && $content['ekit_timeline_title_icons']['value'] != '') : ?>
                                <div class="timeline-icon">
                                <?php
                                    // new icon
                                    $migrated = isset( $content['__fa4_migrated']['ekit_timeline_title_icons'] );
                                    // Check if its a new widget without previously selected icon using the old Icon control
                                    $is_new = empty( $content['ekit_timeline_title_icon'] );
                                    if ( $is_new || $migrated ) {
                                        // new icon
                                        Icons_Manager::render_icon( $content['ekit_timeline_title_icons'], [ 'aria-hidden' => 'true' ] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($content['ekit_timeline_title_icon']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }
                                ?>
                                </div>

                            <?php endif; if(!empty($content['ekit_timeline_title_icons']) && $content['ekit_timeline_title_icons']['value'] != '') : ?>
                            <div class="watermark-icon">
                                <?php
                                    // new icon
                                    $migrated = isset( $content['__fa4_migrated']['ekit_timeline_title_icons'] );
                                    // Check if its a new widget without previously selected icon using the old Icon control
                                    $is_new = empty( $content['ekit_timeline_title_icon'] );
                                    if ( $is_new || $migrated ) {
                                        // new icon
                                        Icons_Manager::render_icon( $content['ekit_timeline_title_icons'], [ 'aria-hidden' => 'true' ] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($content['ekit_timeline_title_icon']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }
                                ?>
                            </div>
                            <?php endif; ?>
                              <div class="timeline-pin"></div>
                            </div><!-- .timeline-item .media END -->

                    <?php if($settings['ekit_timeline_style'] == 'vertical' && $settings['ekit_timeline_vertical_style'] == 'bothside') : ?>

                    <div class="timeline-info<?php echo esc_attr($this->has_animation('right')); ?>" data-settings="<?php echo esc_attr(json_encode($right_entranceAnimation)); ?>">
                    <?php if($content['ekit_timeline_content_date'] != '') : ?>

                    <h4 class="date">
                    <?php
                            // new icon
                            Icons_Manager::render_icon( $settings['ekit_timeline_date_icons'], [ 'aria-hidden' => 'true' ] );
                    ?>
                    <?php endif;
                    if($content['ekit_timeline_content_date'] != '') :
                     echo \ElementsKit_Lite\Utils::render( esc_html( $content['ekit_timeline_content_date'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Displaying with Elementor content rendering ?> </h4>

                     <?php endif;

                     if($content['ekit_timelinehr_content_address'] != '') : ?>

                     <p class="place">

                     <?php if($settings['ekit_timelinehr_address_icons']) : ?>
                        <?php
                           Icons_Manager::render_icon( $settings['ekit_timelinehr_address_icons'], [ 'aria-hidden' => 'true' ] );
                        ?>

                     <?php endif;
                               echo \ElementsKit_Lite\Utils::render( esc_html( $content['ekit_timelinehr_content_address'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Displaying with Elementor content rendering ?> </p>
                     <?php endif; ?>
                    </div>

                <?php endif;

                if($settings['ekit_timelinehr_pinpoint_icon'] == 'doted') : ?>
                    <div class="timeline-img"></div>


           <?php endif;

     if($settings['ekit_timelinehr_pinpoint_icon'] == 'icon') : ?>
        <div class="timeline-pin-icon">
            <?php
                // new icon
                $migrated = isset( $settings['__fa4_migrated']['ekit_timeline_icons'] );
                // Check if its a new widget without previously selected icon using the old Icon control
                $is_new = empty( $settings['ekit_timeline_icon'] );
                if ( $is_new || $migrated ) {
                    // new icon
                    Icons_Manager::render_icon( $settings['ekit_timeline_icons'], [ 'aria-hidden' => 'true' ] );
                } else {
                    ?>
                    <i class="<?php echo esc_attr($settings['ekit_timeline_icon']); ?>" aria-hidden="true"></i>
                    <?php
                }
            ?>
        </div>
        <?php endif; ?>

                        </div><!-- .single-timeline .media END -->
       <?php endif;


        if($settings['ekit_timeline_style'] == 'horizontal') { 
            if($settings['ekit_timeline_horizontal_style'] == 'top'){ ?>

                <div class="single-timeline <?php echo esc_attr(($count == 1) ? 'hover' : ''); ?> elementor-repeater-item-<?php echo esc_attr( $content[ '_id' ] ); ?>">
                        <div class="timeline-item <?php echo esc_attr($this->has_animation('left')); ?>" data-settings="<?php echo esc_attr(json_encode($left_entranceAnimation)); ?>">

                        <?php if(!empty($content['ekit_timeline_title_icons']) && $content['ekit_timeline_title_icons']['value'] != '') : ?>
                            <div class="timeline-icon">
                            <?php
                                // new icon
                                $migrated = isset( $content['__fa4_migrated']['ekit_timeline_title_icons'] );
                                // Check if its a new widget without previously selected icon using the old Icon control
                                $is_new = empty( $content['ekit_timeline_title_icon'] );
                                if ( $is_new || $migrated ) {
                                    // new icon
                                    Icons_Manager::render_icon( $content['ekit_timeline_title_icons'], [ 'aria-hidden' => 'true' ] );
                                } else {
                                    ?>
                                    <i class="<?php echo esc_attr($content['ekit_timeline_title_icon']); ?>" aria-hidden="true"></i>
                                    <?php
                                }
                            ?>
                            </div><!-- .timeline-icon END -->
                        <?php endif; ?>
                            <div class="timeline-content">

                                <?php if($content['ekit_timeline_line_subtitle'] != '') : ?>
                                    <h2 class="subtitle"><?php echo esc_html($content['ekit_timeline_line_subtitle']); ?></h2>
                                <?php endif; ?>

                                <?php
                                    if($content['ekit_timeline_line_title'] != '') {
                                        if ( ! empty( $content['ekit_timeline_date_link']['url'] ) ) {
                                ?>
                                    <h3 class="title"><a href="<?php echo esc_url( $content['ekit_timeline_date_link']['url'] ); ?>" rel="<?php echo esc_attr($content['ekit_timeline_date_link']['nofollow'] ? 'nofollow' : '' ); ?>" target="<?php echo esc_attr($content['ekit_timeline_date_link']['is_external'] ? '_blank' : '_self' ); ?>"><?php echo wp_kses(\ElementsKit_Lite\Utils::kses($content['ekit_timeline_line_title']), \ElementsKit_Lite\Utils::get_kses_array()); ?></a></h3>
                                <?php
                                } else { ?>
                                    <h3 class="title"><?php echo wp_kses(\ElementsKit_Lite\Utils::kses($content['ekit_timeline_line_title']), \ElementsKit_Lite\Utils::get_kses_array());  ?></h3>
                                <?php };
                                } ?>

                                <?php if($content['ekit_timeline_line_content'] != '') : ?>
                                    <p><?php echo wp_kses(\ElementsKit_Lite\Utils::kses( $content['ekit_timeline_line_content'] ), \ElementsKit_Lite\Utils::get_kses_array()); ?></p>
                                <?php endif; ?>

                            </div>
                            <div class="timeline-pin"></div>
                        </div>
                        <div class="content-group text-center">
                            <div class="top-content<?php echo esc_attr($this->has_animation('right')); ?>" data-settings="<?php echo esc_attr(json_encode($right_entranceAnimation)); ?>">
                            <?php
                                if($content['ekit_timeline_line_title'] != '') {
                                    if ( ! empty( $content['ekit_timeline_date_link']['url'] ) ) {
                            ?>
                                <h3 class="title"><a href="<?php echo esc_url( $content['ekit_timeline_date_link']['url'] ); ?>" rel="<?php echo esc_attr($content['ekit_timeline_date_link']['nofollow'] ? 'nofollow' : '' ); ?>" target="<?php echo esc_attr($content['ekit_timeline_date_link']['is_external'] ? '_blank' : '_self' ); ?>"><?php echo wp_kses(\ElementsKit_Lite\Utils::kses($content['ekit_timeline_line_title']), \ElementsKit_Lite\Utils::get_kses_array()); ?></a></h3>
                            <?php
                            } else { ?>
                                <h3 class="title"><?php echo wp_kses(\ElementsKit_Lite\Utils::kses($content['ekit_timeline_line_title']), \ElementsKit_Lite\Utils::get_kses_array()); ?></h3>
                            <?php };
                            } ?>

                            </div>
                            <div class="bar">
                                <div class="pin"></div>
                            </div>
                            <?php if($content['ekit_timeline_content_date'] != '') :  ?>
                                <div class="bottom-content<?php echo esc_attr($this->has_animation('right')); ?>" data-settings="<?php echo esc_attr(json_encode($right_entranceAnimation)); ?>">
                                    <p class="date"><?php echo esc_html( $content['ekit_timeline_content_date'] ); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
    <?php } else { ?>
        <div class="single-timeline <?php echo esc_attr(($count == 1) ? 'hover' : ''); ?> elementor-repeater-item-<?php echo esc_attr( $content[ '_id' ] ); ?>">
            
            <div class="content-group text-center">
                <?php if($content['ekit_timeline_content_date'] != '') :  ?>
                    <div class="bottom-content<?php echo esc_attr($this->has_animation('right')); ?>" data-settings="<?php echo esc_attr(json_encode($right_entranceAnimation)); ?>">
                        <p class="date"><?php echo esc_html( $content['ekit_timeline_content_date'] ); ?></p>
                    </div>
                <?php endif; ?>

                <div class="bar">
                    <div class="pin"></div>
                </div>
                
                <div class="top-content<?php echo esc_attr($this->has_animation('right')); ?>" data-settings="<?php echo esc_attr(json_encode($right_entranceAnimation)); ?>">
                <?php
                    if($content['ekit_timeline_line_title'] != '') {
                        if ( ! empty( $content['ekit_timeline_date_link']['url'] ) ) {
                ?>
                    <h3 class="title"><a href="<?php echo esc_url( $content['ekit_timeline_date_link']['url'] ); ?>" rel="<?php echo esc_attr($content['ekit_timeline_date_link']['nofollow'] ? 'nofollow' : '' ); ?>" target="<?php echo esc_attr($content['ekit_timeline_date_link']['is_external'] ? '_blank' : '_self' ); ?>"><?php echo wp_kses(\ElementsKit_Lite\Utils::kses($content['ekit_timeline_line_title']), \ElementsKit_Lite\Utils::get_kses_array()); ?></a></h3>
                <?php
                } else { ?>
                    <h3 class="title"><?php echo wp_kses(\ElementsKit_Lite\Utils::kses($content['ekit_timeline_line_title']), \ElementsKit_Lite\Utils::get_kses_array()); ?></h3>
                <?php };
                } ?>
                </div>
                
            </div>
 
            <div class="timeline-item timeline-item-horizontal-bottom <?php echo esc_attr($this->has_animation('left')); ?>" data-settings="<?php echo esc_attr(json_encode($left_entranceAnimation)); ?>">
                <div class="timeline-pin pin-top"></div> 
            <?php if(!empty($content['ekit_timeline_title_icons']) && $content['ekit_timeline_title_icons']['value'] != '') : ?>
                <div class="timeline-icon">
                <?php
                    // new icon
                    $migrated = isset( $content['__fa4_migrated']['ekit_timeline_title_icons'] );
                    // Check if its a new widget without previously selected icon using the old Icon control
                    $is_new = empty( $content['ekit_timeline_title_icon'] );
                    if ( $is_new || $migrated ) {
                        // new icon
                        Icons_Manager::render_icon( $content['ekit_timeline_title_icons'], [ 'aria-hidden' => 'true' ] );
                    } else {
                        ?>
                        <i class="<?php echo esc_attr($content['ekit_timeline_title_icon']); ?>" aria-hidden="true"></i>
                        <?php
                    }
                ?>
                </div><!-- .timeline-icon END -->
            <?php endif; ?>
                  
                <div class="timeline-content">

                    <?php if($content['ekit_timeline_line_subtitle'] != '') : ?>
                        <h2 class="subtitle"><?php echo esc_html($content['ekit_timeline_line_subtitle']); ?></h2>
                    <?php endif; ?>

                    <?php
                        if($content['ekit_timeline_line_title'] != '') {
                            if ( ! empty( $content['ekit_timeline_date_link']['url'] ) ) {
                    ?>
                        <h3 class="title"><a href="<?php echo esc_url( $content['ekit_timeline_date_link']['url'] ); ?>" rel="<?php echo esc_attr($content['ekit_timeline_date_link']['nofollow'] ? 'nofollow' : '' ); ?>" target="<?php echo esc_attr($content['ekit_timeline_date_link']['is_external'] ? '_blank' : '_self' ); ?>"><?php echo wp_kses(\ElementsKit_Lite\Utils::kses($content['ekit_timeline_line_title']), \ElementsKit_Lite\Utils::get_kses_array()); ?></a></h3>
                    <?php
                    } else { ?>
                        <h3 class="title"><?php echo wp_kses(\ElementsKit_Lite\Utils::kses($content['ekit_timeline_line_title']), \ElementsKit_Lite\Utils::get_kses_array()); ?></h3>
                    <?php };
                    } ?>

                    <?php if($content['ekit_timeline_line_content'] != '') : ?>
                        <p><?php echo wp_kses(\ElementsKit_Lite\Utils::kses( $content['ekit_timeline_line_content'] ), \ElementsKit_Lite\Utils::get_kses_array()); ?></p>
                    <?php endif; ?>

                </div>          
            </div>

        </div>
    <?php } 
        } $count++;  endforeach;

    if($settings['ekit_timeline_style'] == 'vertical') { ?>
    <div class="timeline-bar"></div>
    <?php }
    echo "</div>";
    }
}
