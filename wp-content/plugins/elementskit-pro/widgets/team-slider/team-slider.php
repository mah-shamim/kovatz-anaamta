<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Team_Slider_Handler as Handler;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Team_Slider extends Widget_Base {
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
		return ['ekit','team', 'slider', 'popup', 'carousel', 'member'];
	}

    public function get_help_url() {
        return 'https://wpmet.com/doc/team-carousel-slider';
    }

    protected function register_controls() {

        // Team Content
        $this->start_controls_section(
            'ekit_team_content', [
                'label' => esc_html__( 'Team Content', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_team_style',
            [
                'label' =>esc_html__( 'Style', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => esc_html__( 'Default', 'elementskit' ),
                    'overlay' => esc_html__( 'Overlay', 'elementskit' ),
                    'centered_style' => esc_html__( 'Centered ', 'elementskit' ),
                    'hover_info' => esc_html__( 'Hover on social', 'elementskit' ),
                    'overlay_details' => esc_html__( 'Overlay with details', 'elementskit' ),
                    'centered_style_details' => esc_html__( 'Centered with details ', 'elementskit' ),
                    'long_height_hover' => esc_html__( 'Long height with hover ', 'elementskit' ),
                    'long_height_details' => esc_html__( 'Long height with details ', 'elementskit' ),
                    'long_height_details_hover' => esc_html__( 'Long height with details & hover', 'elementskit' ),
                    'overlay_circle' => esc_html__( 'Overlay with circle shape', 'elementskit' ),
                    'overlay_circle_hover' => esc_html__( 'Overlay with circle shape & hover', 'elementskit' ),
                    'overlay_content_hover' => esc_html__( 'Overlay content with hover', 'elementskit' ),
                ],
            ]
        );

        $this->add_control(
			'ekit_team_chose_popup',
			[
				'label' => esc_html__( 'Enable Popup', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
				'default' => '',
			]
		);

        $this->add_control(
            'ekit_team_socail_enable',
            [
                'label' => esc_html__( 'Display Social Profiles', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'return_value' => 'yes',
                'default' => 'yes'
            ]
        );

        $this->add_control(
            'ekit_team_content_stable',
            [
                'label' => esc_html__( 'Keep Content Stable', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'return_value' => 'yes',
                'default' => '',
                'condition' => [
                    'ekit_team_style' => ['overlay_circle', 'long_height_hover'] 
                ]
            ]
        );

        $this->add_control(
            'ekit_team_choose_details',
            [
                'label' => esc_html__( 'Display Description', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'return_value' => 'yes',
                'default' => 'no'
            ]
        );


        $this->add_control(
			'ekit_team_chose_button',
			[
				'label' => esc_html__( 'Display Button', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'return_value' => 'yes',
				'default' => 'no',
			]
		);

        $this->add_control(
			'ekit_team_chose_button_popup',
			[
				'label' => esc_html__( 'Enable Popup For Button', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'return_value' => 'yes',
				'default' => 'no',
                'condition' => [
                    'ekit_team_chose_popup!' => 'yes',
                    'ekit_team_chose_button' => 'yes'
                ]
			]
		);

        $repeater = new Repeater();
  
        // start tab for content
        $repeater->start_controls_tabs(
            'content_tabs'
        );

        // start normal tab
        $repeater->start_controls_tab(
            'member_tab',
            [
                'label' => esc_html__( 'Member Content', 'elementskit' ),
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => esc_html__( 'Choose Member Image', 'elementskit' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                    'id'    => -1
                ],
            ]
        );

        $repeater->add_control(
            'name',
            [
                'label' => esc_html__( 'Member Name', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'Jane Doe', 'elementskit' ),
                'placeholder' => esc_html__( 'Member Name', 'elementskit' ),
            ]
        );

        $repeater->add_control(
            'position',
            [
                'label' => esc_html__( 'Member Position', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'Designer', 'elementskit' ),
                'placeholder' => esc_html__( 'Member Position', 'elementskit' ),

            ]
        );

        $repeater->add_control(
            'short_description',
            [
                'label' => esc_html__( 'About Member', 'elementskit' ),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'A small river named Duden flows by their place and supplies it with the necessary', 'elementskit' ),
                'placeholder' => esc_html__( 'About Member', 'elementskit' ),
            ]
        );

        $repeater->add_control(
			'button_heading',
			[
				'label' => esc_html__( 'Button Content', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

        $repeater->add_control(
            'button_icon',
            [
                'label' => esc_html__( 'Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'label_block' => false,
                'exclude_inline_options' => ['svg'],
            ]
        );

        $repeater->add_control(
            'button_icon_position',
            [
                'label' => esc_html__( 'Icon Position', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'before',
				'options' => [
					'before'  => esc_html__( 'Before', 'elementskit' ),
					'after' => esc_html__( 'After', 'elementskit' ),
				]
            ]
        );

        $repeater->add_control(
            'button_text',
            [
                'label' => esc_html__( 'Text', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'View Details', 'elementskit' ),
            ]
        );

        $repeater->add_control(
			'button_link',
			[
				'label' => esc_html__('Link URL', 'elementskit'),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__('https://wpmet.com', 'elementskit'),		
				'dynamic' => [
					'active' => true,
				],
			]
		);

        // popup controls
        $repeater->add_control(
			'popup_heading',
			[
				'label' => esc_html__( 'Popup Content', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

        $repeater->add_control(
            'popup_phone',
            [
                'label' => esc_html__( 'Phone', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '+1 (859) 254-6589',
                'placeholder' => esc_html__( 'Phone Number', 'elementskit' )
            ]
        );

        $repeater->add_control(
            'popup_email',
            [
                'label' => esc_html__( 'Email', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => 'info@example.com',
                'placeholder' => esc_html__( 'Email Address', 'elementskit' ),
            ]
        );

        $repeater->add_control(
            'popup_website',
            [
                'label' => esc_html__( 'Website', 'elementskit' ),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => esc_html__( 'Website URL', 'elementskit' ),
            ]
        );

        $repeater->end_controls_tab();
        // end normal tab

        //start hover tab
        $repeater->start_controls_tab(
            'socialmedia_tab',
            [
                'label' => esc_html__( 'Social Content', 'elementskit' ),
            ]
        );

        for($i=0;$i<5;$i++) {
            $tag ='';
            $icon_value = '';
            $url = 'https://wpmet.com/';

            switch($i) {
                case 0:
                    $tag ='One';
                    $icon_value = 'icon icon-facebook';
                    break;
                case 1:
                    $tag = 'Two';
                    $icon_value = 'icon icon-twitter';
                    break;
                case 2:
                    $tag = 'Three';
                    $icon_value = 'icon icon-linkedin';
                    break;
                case 3:
                    $tag = 'Four';
                    break;
                case 4:
                    $tag = 'Five';
                    break;
                default:
                    $tag = 'One';
            }

            $repeater->add_control(
                'social_'.$i, [
                    'label'       => esc_html__( 'Social '.$tag, 'elementskit' ),
                    'type'        => Controls_Manager::HEADING,
                ]
            );

            $repeater->add_control(
                'link_'.$i,
                [
                    'show_label' => false,
                    'type' => Controls_Manager::URL,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'default' => [
                        'url' => $url,
                    ],
                ]
                );

            $repeater->add_control(
                'icon_'.$i,
                [
                    'show_label' => false,
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => $icon_value,
                        'library' => 'ekiticons',
                    ],
                    'skin' => 'inline'
                ]
            );
        }

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();  


        $this->add_control(
			'ekit_team_members',
			[
				'label' => esc_html__('Team Members', 'elementskit'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'name' => esc_html__('Jane Doe', 'elementskit'),
						'position' => esc_html__('Designer', 'elementskit'),
					],
					[
						'name' => esc_html__('Davis Wilson', 'elementskit'),
						'position' => esc_html__('Developer', 'elementskit'),
					],
					[
						'name' => esc_html__('Peter Hammond', 'elementskit'),
						'position' => esc_html__('Content Writer', 'elementskit'),
					],
				],
				'title_field' => '{{{ name }}}',
			]
		);

        $this->end_controls_section();

        // Slider Settings
		$this->start_controls_section(
			'ekit_team_slider_settings',
			[
				'label' => esc_html__( 'Slider Settings', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_team_slider_spacing',
			[
				'label' => esc_html__( 'Spacing (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider' => '--ekit-team-slider-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_team_slidetoshow',
			[
				'label' => esc_html__( 'Slides To Show', 'elementskit' ),
				'type' =>  Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 12,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 3,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 2,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'default' => [
					'size' => 3,
					'unit' => 'px',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider' => '--ekit-team-slider-slides-to-show: {{SIZE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_team_slidesToScroll',
			[
				'label' => esc_html__( 'Slides To Scroll', 'elementskit' ),
				'type' =>  Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 4,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'default' => [
					'size' => 1,
					'unit' => 'px',
				],
			]
		);

		$this->add_control(
			'ekit_team_autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'elementskit' ),
				'type' =>  Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'ekit_team_speed',
			[
				'label' => esc_html__( 'Speed (ms)', 'elementskit' ),
				'type' =>  Controls_Manager::NUMBER,
				'min' => 500,
				'max' => 15000,
				'step' => 100,
				'default' => 1000
			]
		);

		$this->add_control(
			'ekit_team_pause_on_hover',
			[
				'label' => esc_html__( 'Pause On Hover', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'ekit_team_autoplay' => 'yes',
				]
			]
		);

		$this->add_control(
			'ekit_team_show_arrow',
			[
				'label' => esc_html__( 'Show Arrow', 'elementskit' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'ekit_team_show_arrow_hover',
			[
				'label' => esc_html__( 'Show Arrow On hover', 'elementskit' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => '0',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-slider .elementor-swiper-button' => 'opacity: {{VALUE}};',
                    '{{WRAPPER}} .ekit-team-slider:hover .elementor-swiper-button' => 'opacity:1;'
                ],
			]
		);

        $this->add_control(
            'ekit_team_arrow_type',
            [
                'label' =>esc_html__( 'Arrow Type', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'icon',
                'options' => [
                    'icon' => esc_html__( 'Icon', 'elementskit' ),
                    'text' => esc_html__( 'Text', 'elementskit' ),
                    'text_with_icon' => esc_html__( 'Text With Icon', 'elementskit' ),
                ],
                'condition' => [
                    'ekit_team_show_arrow' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ekit_team_arrow_left_text',
            [
                'label' => esc_html__( 'Left Arrow Text', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Previous', 'elementskit' ),
                'dynamic' => [
                    'active' => true,
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'ekit_team_show_arrow',
                            'operator' => '===',
                            'value' => 'yes',
                        ],
                        [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'ekit_team_arrow_type',
                                    'operator' => '===',
                                    'value' => 'text',
                                ],
                                [
                                    'name' => 'ekit_team_arrow_type',
                                    'operator' => '===',
                                    'value' => 'text_with_icon',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'ekit_team_arrow_right_text',
            [
                'label' => esc_html__( 'Right Arrow Text', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Next', 'elementskit' ),
                'dynamic' => [
                    'active' => true,
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'ekit_team_show_arrow',
                            'operator' => '===',
                            'value' => 'yes',
                        ],
                        [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'ekit_team_arrow_type',
                                    'operator' => '===',
                                    'value' => 'text',
                                ],
                                [
                                    'name' => 'ekit_team_arrow_type',
                                    'operator' => '===',
                                    'value' => 'text_with_icon',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

		$this->add_control(
			'ekit_team_slider_left_arrow_icon',
			[
				'label' => esc_html__( 'Left Arrow Icon', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_team_left_arrow',
				'default' => [
					'value' => 'icon icon-left-arrows',
					'library' => 'ekiticons',
				],
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'ekit_team_show_arrow',
                            'operator' => '===',
                            'value' => 'yes',
                        ],
                        [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'ekit_team_arrow_type',
                                    'operator' => '===',
                                    'value' => 'icon',
                                ],
                                [
                                    'name' => 'ekit_team_arrow_type',
                                    'operator' => '===',
                                    'value' => 'text_with_icon',
                                ],
                            ],
                        ],
                    ],
                ],
			]
		);

		$this->add_control(
			'ekit_team_slider_right_arrow_icon',
			[
				'label' => esc_html__( 'Right Arrow Icon', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_team_right_arrow',
				'default' => [
					'value' => 'icon icon-right-arrow1',
					'library' => 'ekiticons',
				],
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'ekit_team_show_arrow',
                            'operator' => '===',
                            'value' => 'yes',
                        ],
                        [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'ekit_team_arrow_type',
                                    'operator' => '===',
                                    'value' => 'icon',
                                ],
                                [
                                    'name' => 'ekit_team_arrow_type',
                                    'operator' => '===',
                                    'value' => 'text_with_icon',
                                ],
                            ],
                        ],
                    ],
                ],
			]
		);

		$this->add_control(
			'ekit_team_loop',
			[
				'label' => esc_html__( 'Enable Loop?', 'elementskit' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
                'condition' => [
                    'ekit_team_chose_popup!' => 'yes',
                    'ekit_team_chose_button_popup!' => 'yes'
                ]
			]
		);

		$this->add_control(
			'ekit_team_show_dot',
			[
				'label' => esc_html__( 'Show Dots', 'elementskit' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->end_controls_section();

        //condition variables
        $content_stable_condition = [
            'ekit_team_style' => 'long_height_hover',
            'ekit_team_content_stable' => 'yes'
        ];

        $content_stable_hide_condition = [
            'relation' => 'or',
            'terms' => [
                [
                    'name' => 'ekit_team_content_stable',
                    'operator' => '===',
                    'value' => '',
                ],
                [
                    'name' => 'ekit_team_content_stable',
                    'operator' => '===',
                    'value' => null,
                ],
            ],
        ];

        // Team content style section
        $this->start_controls_section(
            'ekit_team_content_style', [
                'label' => esc_html__( 'Content', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ekit_team_content_text_align',
            [
                'label' => esc_html__( 'Alignment', 'elementskit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'text-left' => [
                        'title' => esc_html__( 'Left', 'elementskit' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'text-center' => [
                        'title' => esc_html__( 'Center', 'elementskit' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'text-right' => [
                        'title' => esc_html__( 'Right', 'elementskit' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'text-center',
                'toggle' => true,
            ]
        );

		$this->start_controls_tabs(
            'ekit_team_background_tabs'
        );

		// start normal tab
        $this->start_controls_tab(
            'ekit_team_content_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_team_background_content_normal',
				'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
                'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .profile-card, {{WRAPPER}} .profile-image-card, {{WRAPPER}} .team-stable-content',
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'ekit_team_content_box_shadow',
                'selector'  => '{{WRAPPER}} .ekit-wid-con .ekit-team-slider .profile-card, {{WRAPPER}} .ekit-wid-con .ekit-team-slider .profile-image-card, {{WRAPPER}} .ekit-wid-con .ekit-team-slider .profile-square-v:has(.ekit-team-style-long_height_details)',
                'condition' => [
                    'ekit_team_style!' => 'long_height_hover',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'ekit_team_content_box_shadow_stable',
                'selector'  => '{{WRAPPER}} .team-stable-content',
                'condition' => $content_stable_condition
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_content_border_color_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .profile-card, {{WRAPPER}} .profile-image-card',
                'conditions' => $content_stable_hide_condition
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_content_stable_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-team-slider .team-stable-content',
                'condition' => $content_stable_condition
            ]
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
            'ekit_team_content_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_team_background_content_hover',
				'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
                'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .profile-card:hover, {{WRAPPER}} .profile-image-card:hover, {{WRAPPER}} .profile-card::before, {{WRAPPER}} .profile-image-card::before, {{WRAPPER}} div .profile-card .profile-body::before, {{WRAPPER}} .image-card-v3 .profile-image-card:after, {{WRAPPER}} .team-stable-content:hover',
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'ekit_team_content_box_shadow_hover_group',
                'selector'  => '{{WRAPPER}} .ekit-wid-con .ekit-team-slider .profile-card:hover, {{WRAPPER}} .ekit-wid-con .ekit-team-slider .profile-square-v:hover:has(.ekit-team-style-long_height_details)',
                'condition' => [
                    'ekit_team_style!' => 'long_height_hover',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'ekit_team_content_box_shadow_hover_stable',
                'selector'  => '{{WRAPPER}} .team-stable-content:hover',
                'condition' => $content_stable_condition
            ]
        );
        
        $this->add_control(
            'team_hover_animation',
            [
                'label'         => esc_html__( 'Hover Animation', 'elementskit' ),
                'type'          => Controls_Manager::HOVER_ANIMATION,
            ]
        );
    
        $this->add_responsive_control(
            'overlay_height',
            [
                'label'         => esc_html__('Overlay Height', 'elementskit'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['%', 'px'],
                'range'         => [
                    '%'     => [
                        'min'   => 0,
                        'max'   => 100
                    ],
                    'px'    => [
                        'min'   => 0,
                        'max'   => 500,
                        'step'  => 5
                    ]
                ],
                'default'       => [
                    'unit'  => '%',
                ],
                'selectors'     => [
                    '{{WRAPPER}} .ekit-team-style-long_height_hover:after'  => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_style'   => 'long_height_hover',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_content_border_hover_color_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .profile-card:hover, {{WRAPPER}} .profile-image-card:hover',
                'conditions' => $content_stable_hide_condition
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_content_stable_border_hover',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-team-slider .team-stable-content:hover',
                'condition' => $content_stable_condition
            ]
        );

		$this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->add_control(
            'content_tabs_after',
            [
                'type'  => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_responsive_control(
			'ekit_team_content_border_radius',
			[
				'label' =>esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '' ,
					'left' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .profile-card, {{WRAPPER}} .profile-image-card' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-team-slider .team-stable-content' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-team-slider .team-stable-content .profile-image-card' =>  'border-radius: 0px',
				],
			]
		);

		// contentmax height
        $this->add_responsive_control(
			'ekit_team_content_max_weight',
			[
				'label' => esc_html__( 'Max Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 380,
				],
				'selectors' => [
					'{{WRAPPER}} .profile-square-v .profile-card' => 'max-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_style' => 'hover_info'
                ]
			]
		);    

        $this->add_responsive_control(
			'ekit_team_content_padding',
			[
				'label' =>esc_html__( 'Item Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .profile-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-team-slider .team-stable-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'ekit_team_content_inner_padding',
            [
                'label' =>esc_html__( 'Content Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .profile-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-wid-con .profile-square-v .profile-card .profile-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_content_wrapper_padding',
            [
                'label' =>esc_html__( 'Wrapper Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-slider .swiper-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-team-slider .swiper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_content_wrapper_margin',
            [
                'label' =>esc_html__( 'Wrapper Margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-team-slider .swiper-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-team-slider .swiper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_team_content_overly_color_heading',
            [
                'label' => esc_html__( 'Hover Overy Color', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'ekit_team_style' => 'overlay_details'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_content_overly_color',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'gradient'],
                'selector' => '{{WRAPPER}} .image-card-v2 .profile-image-card::before',
                'condition' => [
                       'ekit_team_style' => 'overlay_details'
                ]
            ]
        );

        $this->end_controls_section();
        // team content section style end

        // Image Styles section
        $this->start_controls_section(
            'ekit_team_image_style', [
                'label' => esc_html__( 'Image', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [   
                'name' => 'ekit_team_image_size',
                'default' => 'large',
            ]
        );

		$this->add_responsive_control(
            'ekit_team_image_weight',
            [
                'label' => esc_html__( 'Image Size (Height and Weight)', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range'  => [
                    'px' => [
                        'min'   => 10,
                        'max'   => 500,
                    ],
                    'em' => [
                        'min'   => 1,
                        'max'   => 30,
                    ],
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .profile-square-v.square-v4 .profile-card .profile-header' => 'padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .profile-header > img, {{WRAPPER}} .profile-image-card img, {{WRAPPER}} .profile-image-card, {{WRAPPER}} .profile-header ' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'
				],
				'default' => [
					'unit' => '%'
				]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'ekit_team_image_shadow',
                'selector'  => '{{WRAPPER}} .profile-card .profile-header, {{WRAPPER}} .team-stable-content .profile-image-card',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'modal_img_shadow',
                'label'     => esc_html__('Box Shadow (Popup)', 'elementskit'),
                'selector'  => '{{WRAPPER}} .ekit-team-modal-img > img',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_image_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .profile-card .profile-header',
                'conditions' => $content_stable_hide_condition
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_image_border_stable',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-team-slider .team-stable-content .profile-image-card',
                'condition' => $content_stable_condition
            ]
        );

        $this->add_responsive_control(
            'ekit_team_image_radius',
            [
                'label' => esc_html__( 'Border radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
					'top' => '50',
					'right' => '50',
					'left' => '50',
					'bottom' => '50',
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-img.profile-header > img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-team-slider .team-stable-content .profile-image-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-team-slider .ekit-team-style-default .profile-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
            ]
        );

        $this->add_responsive_control(
            'ekit_team_image_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em' ],
                'condition' => [
                    'ekit_team_style' => 'default',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-slider .ekit-team-style-default .profile-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_image_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'condition' => [
                    'ekit_team_style!' => ['overlay','long_height_hover'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-card .profile-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_image_background',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .profile-card .profile-header',
                'condition' => [
                    'ekit_team_style!' => 'default',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_default_img_overlay',
                'label' => esc_html__( 'Overlay', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .profile-header:before',
                'exclude' => ['image'],
                'condition' => [
                    'ekit_team_style' => 'default',
                ],
            ]
        );

        $this->end_controls_section();

        // Icon Styles
        $this->start_controls_section(
            'ekit_team_top_icon_style',
            [
                'label' => esc_html__( 'Icon', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_team_style' => 'default',
                    'ekit_team_toggle_icon' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_top_icon_align',
            [
                'label' => esc_html__( 'Alignment', 'elementskit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => esc_html__( 'Left', 'elementskit' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Right', 'elementskit' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'end' => [
                        'title' => esc_html__( 'Right', 'elementskit' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'toggle' => true,
            ]
        );

        $this->add_responsive_control(
			'ekit_team_top_icon_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->add_responsive_control(
			'ekit_team_top_icon_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'ekit_team_top_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'   => [
                    'top'   => '50',
                    'left'  => '50',
                    'right' => '50',
                    'bottom'=> '50',
                    'unit' => '%'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_team_top_icon_shadow',
                'selector' => '{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg',
            ]
        );
        
		$this->add_responsive_control(
            'ekit_team_top_icon_fsize',
            [
                'label' => esc_html__( 'Font Size', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'default' => [
                    'size' => 22,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-icon > i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .profile-icon > svg'   => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_team_top_icon_hw',
			[
                'label' => esc_html__( 'Use Height Width', 'elementskit' ),
                'description'   => esc_html__('For svg icon, We don\'t need this. We will use font size and padding for adjusting size.', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
        
		$this->add_responsive_control(
            'ekit_team_top_icon_width',
            [
                'label' => esc_html__( 'Width', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => 60,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-icon > i' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_top_icon_hw' => 'yes'
                ],
            ]
        );
        
		$this->add_responsive_control(
            'ekit_team_top_icon_height',
            [
                'label' => esc_html__( 'Height', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => 60,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-icon > i' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_top_icon_hw' => 'yes'
                ],
            ]
        );
        
		$this->add_responsive_control(
            'ekit_team_top_icon_lheight',
            [
                'label' => esc_html__( 'Line Height', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => 60,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-icon > i' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_top_icon_hw' => 'yes'
                ],
            ]
        );

        $this->start_controls_tabs( 'top_icon_colors' );
            $this->start_controls_tab(
                'ekit_team_top_icon_colors_normal',
                [
                    'label' => esc_html__( 'Normal', 'elementskit' ),
                ]
            );

            $this->add_control(
                'ekit_team_top_icon_n_color',
                [
                    'label' => esc_html__( 'Color', 'elementskit' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#fff',
                    'selectors' => [
                        '{{WRAPPER}} .profile-icon > i' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .profile-icon > svg'  => 'fill: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ekit_team_top_icon_n_bgcolor',
                [
                    'label' => esc_html__( 'Background Color', 'elementskit' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#fc0467',
                    'selectors' => [
                        '{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'ekit_team_top_icon_n_border',
                    'label' => esc_html__( 'Border', 'elementskit' ),
                    'selector' => '{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg',
                ]
            );

            $this->end_controls_tab();
            
            $this->start_controls_tab(
                'ekit_team_top_icon_colors_hover',
                [
                    'label' => esc_html__( 'Hover', 'elementskit' ),
                ]
            );

                $this->add_control(
                    'ekit_team_top_icon_h_color',
                    [
                        'label' => esc_html__( 'Color', 'elementskit' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .profile-icon > i:hover' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .profile-icon > svg:hover'    => 'fill: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_control(
                    'ekit_team_top_icon_h_bgcolor',
                    [
                        'label' => esc_html__( 'Background Color', 'elementskit' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .profile-icon > i:hover, {{WRAPPER}} .profile-icon > svg:hover' => 'background-color: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'ekit_team_top_icon_h_border',
                        'label' => esc_html__( 'Border', 'elementskit' ),
                        'selector' => '{{WRAPPER}} .profile-icon > i:hover, {{WRAPPER}} .profile-icon > svg:hover',
                    ]
                );

            $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // Name Styles
        $this->start_controls_section(
            'ekit_team_name_style', [
                'label' => esc_html__( 'Name', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_name_typography',
                'selector'   => '{{WRAPPER}} .profile-body .profile-title',
            ]
        );

        $this->start_controls_tabs(
            'ekit_team_name_tabs'
        );

        $this->start_controls_tab(
            'ekit_team_name_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_team_name_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .profile-body .profile-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-team-slider .profile-card:hover .profile-body .profile-title' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_team_name_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_team_name_hover_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-team-slider .profile-body .profile-title:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-wid-con .ekit-team-slider .profile-card .profile-title:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
			'ekit_team_name_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'allowed_dimensions' => 'vertical',
                'default' => [
                    'unit' => 'px',
                    'isLinked' => false,
                ],
				'selectors' => [
					'{{WRAPPER}} .profile-body .profile-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
			]
		);

        $this->end_controls_section();

        // Designation Styles
        $this->start_controls_section(
            'ekit_team_position_style', [
                'label' => esc_html__( 'Designation', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_position_typography',
                'selector'   => '{{WRAPPER}} .profile-body .profile-designation',
            ]
        );

        $this->start_controls_tabs(
            'ekit_team_position_tabs'
        );

        $this->start_controls_tab(
            'ekit_team_position_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_team_position_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .profile-body .profile-designation' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_team_position_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_team_position_hover_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .profile-card:hover .profile-body .profile-designation,
                    {{WRAPPER}} .profile-body .profile-designation:hover' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(), [
                'name'       => 'ekit_team_position_hover_shadow',
                'selector'   => '{{WRAPPER}} .profile-card:hover .profile-body .profile-designation,
                    {{WRAPPER}} .profile-body .profile-designation:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'ekit_team_position_margin_bottom',
            [
                'label' => esc_html__( 'Margin Bottom', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],

                'selectors' => [
                    '{{WRAPPER}} .profile-body .profile-designation' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        // Description Styles
        $this->start_controls_section(
            'ekit_team_text_content_style_tab', [
                'label' => esc_html__( 'Description', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_text_content_typography',
                'selector'   => '{{WRAPPER}} .profile-body .profile-content',
            ]
        );

        $this->start_controls_tabs(
            'ekit_team_text_content_tabs'
        );

        $this->start_controls_tab(
            'ekit_team_text_content_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_team_text_content_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .profile-body .profile-content' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_team_text_content_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_team_text_content_hover_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .profile-body:hover .profile-content' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .profile-card:hover .profile-body .profile-content' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .profile-image-card:hover .profile-body .profile-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
			'ekit_team_text_content_margin_bottom',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .profile-body .profile-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
			]
		);

        $this->end_controls_section();

        // Button style
		$this->start_controls_section(
			'ekit_team_section_button_style',
			[
				'label' => esc_html__('Button', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_team_chose_button' => 'yes',
                ]
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_team_btn_typography_group',
				'label' =>esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn',
			]
		);

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'ekit_team_tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_team_box_button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_btn_background_group',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'classic' => 'image'
				],
                'selector' => '{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_button_border_color_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_team_tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_team_btn_hover_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_btn_background_hover_group',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'classic' => 'image'
				],
                'selector' => '{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_button_border_hv_color_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
			'ekit_team_btn_border_radius',
			[
				'label' =>esc_html__( 'Border Radius (px)', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '' ,
					'left' => '',
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_team_btn_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_team_btn_margin',
			[
				'label' =>esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        // Button icon style
        $this->start_controls_section(
			'ekit_team_btn_icon_style',
			[
				'label' => esc_html__('Button Icon', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_team_chose_button' => 'yes',
                ]
			]
		);

		$this->add_control(
			'ekit_team_btn_icon_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn-icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_team_btn_icon_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn:hover > i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_team_btn_icon_size',
			[
				'label' => esc_html__('Size (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn > i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'ekit_team_btn_icon_spacing',
            [
                'label' => esc_html__( 'Margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-slider .profile-body .elementskit-btn > i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->end_controls_section(); 

        // Social Styles
        $this->start_controls_section(
            'ekit_team_social_style', [
                'label' => esc_html__( 'Social Profiles', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'ekit_socialmedai_list_item_align',
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
                ],
                'default' => 'center',
                'toggle' => true,
				'selectors' => [
                    '{{WRAPPER}} .ekit-team-slider .ekit-team-social-list' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_socialmedia_list_position',
            [
                'label' => esc_html__( 'Position (Top to Bottom)', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%','px' ],
                'default' => [
					'unit' => '%',
				],
                'selectors' => [
                    '{{WRAPPER}} .profile-image-card .hover-area' => 'top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_content_stable' => 'yes',
                    'ekit_team_style' => ['overlay_circle', 'long_height_hover']
                ]
            ]
        );

		// Display design
		$this->add_responsive_control(
            'ekit_socialmedai_list_display',
            [
                'label' => esc_html__( 'Display', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'inline-block',
                'options' => [
                    'inline-block' => esc_html__( 'Inline Block', 'elementskit' ),
                    'block' => esc_html__( 'Block', 'elementskit' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_socialmedai_list_icon_size',
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
                    '{{WRAPPER}} .ekit-team-social-list > li > a i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-team-social-list > li > a svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_socialmedai_list_style_use_animation',
			[
                'label' => esc_html__( 'Disable Animation', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'none',
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-team-slider .ekit-team-social-list > li > a:hover > i::before' => 'animation: {{VALUE}};',
                    '{{WRAPPER}} .ekit-wid-con .ekit-team-slider .ekit-team-social-list > li > a:hover svg' => 'animation: {{VALUE}};',
                ],
			]
		);

        $this->add_control(
			'ekit_socialmedai_list_style_use_height_and_width',
			[
                'label' => esc_html__( 'Use Height Width', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedai_list_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-social-list > li > a' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedai_list_style_use_height_and_width' => 'yes'
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedai_list_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-social-list > li > a' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedai_list_style_use_height_and_width' => 'yes'
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedai_list_line_height',
			[
				'label' => esc_html__( 'Line Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-social-list > li > a' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedai_list_style_use_height_and_width' => 'yes'
                ]
			]
		);

        // start tab for content
        $this->start_controls_tabs(
            'ekit_team_socialmedia_tabs'
        );

        // start normal tab
        $this->start_controls_tab(
            'ekit_team_socialmedia_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        // set social icon color
        $this->add_control(
            'ekit_team_socialmedia_icon_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li > a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-team-social-list > li > a svg' => 'fill: {{VALUE}};'
                ],
            ]
        );

        // set social icon background color
        $this->add_control(
            'ekit_team_socialmedia_icon_bg_color',
            [
                'label' =>esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#a1a1a1',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li > a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'       => 'ekit_team_socialmedai_list_box_shadow',
                'selector'   => '{{WRAPPER}} .ekit-team-social-list > li > a',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_socialmedia_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-team-social-list > li > a',
            ]
        );

        $this->end_controls_tab();
        // end normal tab

        //start hover tab
        $this->start_controls_tab(
            'ekit_team_socialmedia_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        // set social icon color
        $this->add_control(
            'ekit_team_socialmedia_icon_hover_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li > a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-team-social-list > li > a:hover svg'   => 'fill: {{VALUE}};',
                ],
            ]
        );

        // set social icon background color
        $this->add_control(
            'ekit_team_socialmedia_icon_hover_bg_color',
            [
                'label' =>esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#3b5998',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li > a:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'       => 'ekit_team_socialmedai_list_box_shadow_hover',
                'selector'   => '{{WRAPPER}} .ekit-team-social-list > li > a:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_socialmedia_border_hover',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-team-social-list > li > a:hover',
            ]
        );

        $this->end_controls_tab();
        //end hover tab

        $this->end_controls_tabs();
        
        // border radius
		$this->add_responsive_control(
            'ekit_socialmedai_list_border_radius',
            [
                'label' => esc_html__( 'Border radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '50',
					'right' => '50',
					'bottom' => '50',
					'left' => '50',
					'unit' => '%',
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before'
            ]
        );

		// Padding style
		$this->add_responsive_control(
            'ekit_socialmedai_list_padding',
            [
                'label'         => esc_html__('Padding', 'elementskit'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		// margin style
		$this->add_responsive_control(
            'ekit_socialmedai_list_margin',
            [
                'label'         => esc_html__('Margin', 'elementskit'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px', 'em'],
                'default' => [
					'top' => '1',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
                    'isLinked' => false
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Overlay Styles
        $this->start_controls_section(
            'ekit_team_overlay_style', [
                'label' => esc_html__( 'Overlay', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_team_style!' => 'default',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_blur_overlay',
            [
                'label' => esc_html__( 'Blur', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'max' => 10,
                        'step' => 0.5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-image-card:before' => 'backdrop-filter: blur({{SIZE}}{{UNIT}});',
                    '{{WRAPPER}} .profile-square-v .profile-card::before' => 'backdrop-filter: blur({{SIZE}}{{UNIT}});',
                    '{{WRAPPER}} .profile-square-v.square-v4 .profile-card .profile-body::before' => 'backdrop-filter: blur({{SIZE}}{{UNIT}});',
                    '{{WRAPPER}} .image-card-v2 .profile-image-card::before' => 'backdrop-filter: blur({{SIZE}}{{UNIT}});',
                    '{{WRAPPER}} .image-card-v3 .profile-image-card::after' => 'backdrop-filter: blur({{SIZE}}{{UNIT}});',
                    '{{WRAPPER}} .image-card-v2 .ekit-team-style-overlay_details .overlay-content-hover' => 'backdrop-filter: blur({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_background_overlay',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic','gradient' ],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .profile-image-card:before, {{WRAPPER}} .profile-square-v .profile-card::before, {{WRAPPER}} .profile-square-v.square-v4 .profile-card .profile-body::before, {{WRAPPER}} .image-card-v2 .profile-image-card::before, {{WRAPPER}} .image-card-v3 .profile-image-card::after, {{WRAPPER}} .ekit-team-slider .image-card-v2 .ekit-team-style-overlay_details .overlay-content-hover',
            ]
        );

        $this->end_controls_section();

         // Border Styles
         $this->start_controls_section(
            'ekit_team_border_style', [
                'label' => esc_html__( 'Border', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_team_style' => 'long_height_details',
                ],
            ]
        );

        $this->add_control(
            'ekit_team_content_border_color',
            [
                'label' => esc_html__( 'Border Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#2965f133',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-slider .profile-square-v.square-v6 .profile-card::after' => 'background-color: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'ekit_team_content_border_hover_color',
            [
                'label' => esc_html__( 'Border Hover Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#2965f1',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-slider .profile-square-v.square-v6 .profile-card:hover::after' => 'background-color: {{VALUE}};',
                ]
            ]
        );

        $this->end_controls_section();


        // Modal Styles start here
        $this->start_controls_section(
            'ekit_team_modal_style', [
                'label' => esc_html__( 'Modal Controls', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'ekit_team_chose_popup',
                            'operator' => '===',
                            'value' => 'yes',
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'ekit_team_chose_button',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                                [
                                    'name' => 'ekit_team_chose_button_popup',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
			'ekit_team_modal_overlay_heading',
			[
				'label' => esc_html__( 'Overlay', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_modal_overlay_background',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}}.elementor-widget-elementskit-team-slider .mfp-bg.ekit-promo-popup',
            ]
        );

        $this->add_control(
			'ekit_team_modal_heading',
			[
				'label' => esc_html__( 'Modal', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_modal_background',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .elementskit-team-popup .modal-content',
            ]
        );

        $this->add_responsive_control(
            'ekit_team_modal_padding',
            [
                'label'         => esc_html__('Padding (px)', 'elementskit'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .elementskit-team-popup .modal-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_team_modal_name_heading',
			[
				'label' => esc_html__( 'Name', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
            'ekit_team_modal_name_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .ekit-team-modal-title' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_modal_name_typography',
                'selector'   => '{{WRAPPER}} .ekit-team-modal-title',
            ]
        );

        $this->add_responsive_control(
            'ekit_team_modal_name_margin_bottom',
            [
                'label'         => esc_html__('Margin (px)', 'elementskit'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_team_modal_position_heading',
			[
				'label' => esc_html__( 'Designation', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
            'ekit_team_modal_position_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .ekit-team-modal-position' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_modal_position_typography',
                'selector'   => '{{WRAPPER}} .ekit-team-modal-position',
            ]
        );

        $this->add_responsive_control(
            'ekit_team_modal_position_margin_bottom',
            [
                'label' => esc_html__( 'Margin Bottom (px)', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-position' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Modal Description
        $this->add_control(
            'modal_desc',
            [
                'label'     => esc_html__('Description', 'elementskit'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        // Modal Description - Color
        $this->add_control(
            'modal_desc_color',
            [
                'label'     => esc_html__('Color', 'elementskit'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-content'  => 'color: {{VALUE}};',
                ]
            ]
        );

        // Modal Description - Typography
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'modal_desc_font',
                'selector'   => '{{WRAPPER}} .ekit-team-modal-content',
            ]
        );

        // Modal Description - Margin Bottom
        $this->add_responsive_control(
            'modal_desc_margin_bottom',
            [
                'label'         => esc_html__( 'Margin Bottom (px)', 'elementskit' ),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'selectors'     => [
                    '{{WRAPPER}} .ekit-team-modal-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'more_options',
			[
				'label' => esc_html__( 'Phone, Email & Website', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
			'ekit_team_info_border',
			[
                'label' => esc_html__( 'Disable Border', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => '0',
                'selectors'  => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-team-modal-list li' => 'border: {{VALUE}}px;',
                    '{{WRAPPER}} .ekit-wid-con .ekit-team-modal-list' => 'border-top: 1px solid rgba(0, 0, 0, 0.05);',
                ],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_info_typography',
                'selector'   => '{{WRAPPER}} .ekit-team-modal-list',
            ]
        );

        $this->add_control(
            'ekit_team_info_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .ekit-team-modal-list' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'ekit_team_info_hover_color',
            [
                'label'      => esc_html__( 'Color Hover', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .ekit-team-modal-list a:hover' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_info_space_between',
            [
                'label' => esc_html__( 'Space Between', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .ekit-team-modal-list > li:first-child' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-team-modal .ekit-team-modal-list > li:last-child' => 'padding-top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-team-modal .ekit-team-modal-list > li:not(:last-child,:first-child)' => 'padding: 0{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_info_space_top_bottom',
            [
                'label'         => esc_html__('Padding (px)', 'elementskit'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px'],
                'allowed_dimensions' => 'vertical',
                'default' => [
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .ekit-team-modal-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-wid-con .ekit-team-modal-list > li' => 'padding: 0{{UNIT}};',
                ],
            ]
        );

        // Social Styles for popup

        $this->add_control(
			'ekit_socialmedai_popup',
			[
				'label' => esc_html__( 'Social Profiles', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
			'ekit_socialmedai_popup_enable',
			[
				'label' => esc_html__( 'Enable Social Media', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'return_value' => 'yes',
			]
		);

        $this->add_responsive_control(
            'ekit_socialmedai_popup_list_item_align',
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
                ],
                'default' => 'left',
                'toggle' => true,
				'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list' => 'text-align: {{VALUE}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'ekit_socialmedai_popup_list_display',
            [
                'label' => esc_html__( 'Display', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'inline-block',
                'options' => [
                    'inline-block' => esc_html__( 'Inline Block', 'elementskit' ),
                    'block' => esc_html__( 'Block', 'elementskit' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_socialmedai_popup_list_icon_size',
            [
                'label' => esc_html__( 'Icon Size (px)', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_socialmedai_popup_list_style_use_animation',
			[
                'label' => esc_html__( 'Disable Animation', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'none',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a:hover > i::before' => 'animation: {{VALUE}};',
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a:hover svg' => 'animation: {{VALUE}};',
                ],
			]
		);

        $this->add_control(
			'ekit_socialmedai_popup_list_style_use_height_and_width',
			[
                'label' => esc_html__( 'Use Height Width', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedai_popup_list_width',
			[
				'label' => esc_html__( 'Width (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedai_popup_list_style_use_height_and_width' => 'yes'
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedai_popup_list_height',
			[
				'label' => esc_html__( 'Height (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedai_popup_list_style_use_height_and_width' => 'yes'
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedai_popup_list_line_height',
			[
				'label' => esc_html__( 'Line Height (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedai_list_style_use_height_and_width' => 'yes'
                ]
			]
		);

        // start tab for content
        $this->start_controls_tabs(
            'ekit_team_socialmedia_popup_tabs'
        );

        // start normal tab
        $this->start_controls_tab(
            'ekit_team_socialmedia_popup_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        // set social icon color
        $this->add_control(
            'ekit_team_socialmedia_popup_icon_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a svg' => 'fill: {{VALUE}};'
                ],
            ]
        );

        // set social icon background color
        $this->add_control(
            'ekit_team_socialmedia_popup_icon_bg_color',
            [
                'label' =>esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#a1a1a1',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'       => 'ekit_team_socialmedai_popup_list_box_shadow',
                'selector'   => '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_socialmedia_popup_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a',
            ]
        );

        $this->end_controls_tab();
        // end normal tab

        //start hover tab
        $this->start_controls_tab(
            'ekit_team_socialmedia_popup_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        // set social icon color
        $this->add_control(
            'ekit_team_socialmedia_popup_icon_hover_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a:hover svg'   => 'fill: {{VALUE}};',
                ],
            ]
        );

        // set social icon background color
        $this->add_control(
            'ekit_team_socialmedia_popup_icon_hover_bg_color',
            [
                'label' =>esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#3b5998',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'       => 'ekit_team_socialmedai_popup_list_box_shadow_hover',
                'selector'   => '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_socialmedia_popup_border_hover',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a:hover',
            ]
        );

        $this->end_controls_tab();
        //end hover tab

        $this->end_controls_tabs();

		$this->add_responsive_control(
            'ekit_socialmedai_popup_list_border_radius',
            [
                'label' => esc_html__( 'Border radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
				'default' => [
					'top' => '50',
					'right' => '50',
					'bottom' => '50',
					'left' => '50',
					'unit' => 'px'
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'ekit_socialmedai_popup_list_padding',
            [
                'label'         => esc_html__('Padding (px)', 'elementskit'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'ekit_socialmedai_popup_list_margin',
            [
                'label'         => esc_html__('Margin (px)', 'elementskit'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px'],
                'default' => [
					'top' => '1',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
                    'isLinked' => false
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal .profile-footer .ekit-team-social-list > li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ekit_team_close_icon',
            [
                'label' => esc_html__( 'Close Icon', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Close icon change option
        $this->add_control(
            'ekit_team_close_icon_changes',
            [
                'label' => esc_html__( 'Close Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_team_close_icon_change',
                'default' => [
                    'value' => 'fas fa-times',
                    'library' => 'fa-solid',
                ],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'ekit_team_close_icon_alignment',
            [
                'label' => esc_html__( 'Close Icon Alignment', 'elementskit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'elementskit' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'elementskit' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => '{{VALUE}}: 10px;',
                ],
                'default' => 'right',
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_size',
            [
                'label' => esc_html__( 'Font Size', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-team-modal-close svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'ekit_icon_box_icon_colors' );

        $this->start_controls_tab(
            'ekit_team_icon_colors_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_team_icon_primary_color',
            [
                'label' => esc_html__( 'Icon Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#656565',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-team-modal-close svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_team_icon_secondary_color_normal',
            [
                'label' => esc_html__( 'Icon Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-team-modal-close',
            ]
        );
 
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_icon_icon_box_shadow_normal_group',
                'selector' => '{{WRAPPER}} .ekit-team-modal-close',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_team_icon_colors_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_team_hover_primary_color',
            [
                'label' => esc_html__( 'Icon Color (Hover)', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-team-modal-close:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_team_hover_background_color',
            [
                'label' => esc_html__( 'Icon BG Color (Hover)', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_border_icon_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-team-modal-close:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_team_shadow_group',
                'selector' => '{{WRAPPER}} .ekit-team-modal-close:hover',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'ekit_team_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_enable_height_width',
            [
                'label' => esc_html__( 'Use Height Width', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_width',
            [
                'label' => esc_html__( 'Width', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                  'ekit_team_close_icon_enable_height_width' => 'yes',
              ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_height',
            [
                'label' => esc_html__( 'Height', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_close_icon_enable_height_width' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_line_height',
            [
                'label' => esc_html__( 'Line Height', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_close_icon_enable_height_width' => 'yes',
                ],

            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_vertical_align',
            [
                'label' => esc_html__( 'Vertical Position ', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-infobox .elementskit-box-header .elementskit-info-box-icon' => ' -webkit-transform: translateY({{SIZE}}{{UNIT}}); -ms-transform: translateY({{SIZE}}{{UNIT}}); transform: translateY({{SIZE}}{{UNIT}});',
                ],
                'condition' => [
                    'ekit_icon_box_icon_position!' => 'top'
                ]

            ]
        );

        $this->end_controls_section();

        /** Arrow Style Section */  
        $this->start_controls_section(
			'ekit_team_section_navigation',
			[
				'label' => esc_html__( 'Arrows', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_team_show_arrow' => 'yes'
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_team_arrow_size',
			[
				'label' => esc_html__( 'Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .elementor-swiper-button' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-team-slider .elementor-swiper-button > svg' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-team-slider .elementor-swiper-button > span' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_team_arrow_text_space',
			[
				'label' => esc_html__( 'Space Between', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
                'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .ekit-team-slider-button-prev span' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-team-slider .ekit-team-slider-button-next span' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_team_show_arrow' => 'yes',
                    'ekit_team_arrow_type' => 'text_with_icon'
                ]
			]
        );

		$this->add_control(
			'ekit_team_position_popover_toggle',
			[
				'label' => esc_html__( 'Arrow Position', 'elementskit' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => esc_html__( 'Default', 'elementskit' ),
				'label_on' => esc_html__( 'Custom', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->start_popover();

		$this->add_control(
			'ekit_team_arrow_pos_head',
			[
				'label' => esc_html__( 'Left Arrow Position', 'elementskit' ),
				'type' => Controls_Manager::HEADING
			]
		);

		$this->add_responsive_control(
			'ekit_team_arrow_left_pos_left',
			[
				'label' => esc_html__( 'Left Arrow Position (X)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-team-slider-button-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_team_arrow_left_pos_top',
			[
				'label' => esc_html__( 'Left Arrow Position (Y)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],

				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-team-slider-button-prev' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
        );

		$this->add_control(
			'ekit_team_arrow_right_pos_head',
			[
				'label' => esc_html__( 'Right Arrow Position', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_responsive_control(
			'ekit_team_arrow_right_pos_right',
			[
				'label' => esc_html__( 'Right Arrow Position (X)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-team-slider-button-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_team_arrow_right_pos_top',
			[
				'label' => esc_html__( 'Right Arrow Position (Y)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-team-slider-button-next' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
        );

		$this->end_popover();

        // Arrow Normal
		$this->start_controls_tabs('ekit_team_logo_style_tabs');

        $this->start_controls_tab(
			'ekit_team_logo_arrow_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

        $this->add_control(
			'ekit_team_arrow_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
                'default' => '#00000090',
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .elementor-swiper-button > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-team-slider .elementor-swiper-button > svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ekit-team-slider .elementor-swiper-button > span' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_control(
			'ekit_team_arrow_background',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-team-slider .elementor-swiper-button' => 'background: {{VALUE}}',
				],
			]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_team_arrow_border_group',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-team-slider .elementor-swiper-button',
			]
        );

        $this->end_controls_tab();

        //  Arrow hover tab
        $this->start_controls_tab(
			'ekit_team_arrow_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
        );

        $this->add_control(
			'ekit_team_arrow_hv_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .elementor-swiper-button:hover > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-team-slider .elementor-swiper-button:hover > svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ekit-team-slider .elementor-swiper-button:hover > span' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_control(
			'ekit_team_arrow_hover_background',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-team-slider .elementor-swiper-button:hover' => 'background: {{VALUE}}',
				],
			]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_team_arrow_border_hover_group',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-team-slider .elementor-swiper-button:hover',
			]
        );

        $this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_team_arrow_border_radious',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-team-slider .elementor-swiper-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before'
			]
        );

		$this->add_responsive_control(
			'ekit_team_arrow_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-team-slider .elementor-swiper-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

		$this->end_controls_section();

		/** Dot Style Section */ 
		$this->start_controls_section(
			'ekit_team_navigation_dot',
			[
				'label' => esc_html__( 'Dots', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
						'ekit_team_show_dot' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_team_dots_left_right_spacing',
			[
				'label' => esc_html__( 'Space Between', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet' => 'margin-right: {{SIZE}}{{UNIT}};margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_team_dots_top_to_bottom',
			[
				'label' => esc_html__( 'Spacing Top To Bottom', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .swiper-pagination' => 'transform: translateY({{SIZE}}{{UNIT}});',
				],
			]
		);

        $this->add_control(
			'ekit_team_dots_opacity',
			[
				'label' => esc_html__( 'Opacity', 'elementskit' ),
				'type' =>  Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1,
				'step' => 0.1,
                'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-team-slider .swiper-pagination .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'opacity: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_team_dot_width',
			[
				'label' => esc_html__( 'Width (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_team_dot_height',
			[
				'label' => esc_html__( 'Height (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_team_dot_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet',
			]
        );

		$this->add_control(
			'ekit_team_dot_border_radius',
			[
				'label' => esc_html__( 'Border radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_team_dot_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet',
			]
		);

		$this->add_control(
			'ekit_team_dot_active_heading',
			[
				'label' => esc_html__( 'Active', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_team_dot_active_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet-active',
			]
		);

		$this->add_responsive_control(
			'ekit_team_dot_active_scale',
			[
				'label' => esc_html__( 'Scale', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1.2,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet-active' => 'transform: scale({{SIZE}});',
				],
			]
		);

        $this->add_control(
			'ekit_team_dot_position',
			[
				'label' => esc_html__( 'Position Vertical', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet-active' => 'transform: translateY({{SIZE}}{{UNIT}});',
				],
			]
		);

        $this->add_control(
			'ekit_team_dot_active_width',
			[
				'label' => esc_html__( 'Width (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_team_dot_active_height',
			[
				'label' => esc_html__( 'Height (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_team_dot_border_active',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-team-slider .swiper-pagination .swiper-pagination-bullet-active',
			]
        );

		$this->end_controls_section();
    }

    protected function render( ) {
        echo '<div class="ekit-wid-con">';
            $this->render_raw();
        echo '</div>';
    }

    protected function get_image_html($ekit_team_member, $settings) {
        $image_html = '';

        if ( !empty($ekit_team_member['image']['url']) ) {
            $ekit_team_member['image_size_size'] = $settings['ekit_team_image_size_size'];
            $ekit_team_member['image_size_custom_dimension'] = $settings['ekit_team_image_size_custom_dimension'];
            $image_html = Group_Control_Image_Size::get_attachment_image_html($ekit_team_member, 'image_size', 'image');
            return $image_html;
        }

        return $image_html;
    }

	protected function render_raw( ) {
		$settings = $this->get_settings_for_display();
        extract($settings);

        $widget_id =$this->get_id();

        $config = [
            'rtl'				=> is_rtl(),
            'arrows'			=> !empty($ekit_team_show_arrow),
            'dots'				=> !empty($ekit_team_show_dot),
            'autoplay'			=> !empty($ekit_team_autoplay),
            'speed' 			=> $ekit_team_speed,
            'slidesPerView'		=> $ekit_team_slidetoshow['size'] ?? 3,
            'slidesPerGroup'	=> $ekit_team_slidesToScroll['size'] ?? 1,
            'spaceBetween' 		=> $ekit_team_slider_spacing['size'] ?? 30,
            'pauseOnHover'	    => !empty($ekit_team_pause_on_hover),
            'loop'  			=> !empty($ekit_team_loop),
            'breakpoints'		=> [
                360 => [
                    'slidesPerView'      => $ekit_team_slidetoshow_mobile['size'] ?? 1,
                    'slidesPerGroup'    => $ekit_team_slidesToScroll_mobile['size'] ?? 1
                ],
                767 => [
                    'slidesPerView'      => $ekit_team_slidetoshow_tablet['size'] ?? 2,
                    'slidesPerGroup'    => $ekit_team_slidesToScroll_tablet['size'] ?? 1,
                ],
                1024 => [
                    'slidesPerView'      => $ekit_team_slidetoshow['size'] ?? 2,
                    'slidesPerGroup'    => $ekit_team_slidesToScroll['size'] ?? 1,
                ]
            ],
        ];

		$this->add_render_attribute('profile_card',
			[
				'class' => 'swiper-slide profile-card elementor-animation-'. $team_hover_animation .' ' . $ekit_team_content_text_align . ' ekit-team-style-'.$ekit_team_style,
			]
		);

		// Swiper container
		$this->add_render_attribute(
			'swiper-container',
			[
				'class'	=>  method_exists('\ElementsKit_Lite\Utils', 'swiper_class') ? \ElementsKit_Lite\Utils::swiper_class() : 'swiper',
				'data-config'	=> esc_attr(json_encode($config)),
			]
		);
		?>
		<div class="ekit-team-slider">
			<div <?php $this->print_render_attribute_string('swiper-container'); ?>>
				<div class="swiper-wrapper">

					<?php if ( in_array($ekit_team_style, ['default', 'centered_style', 'centered_style_details', 'long_height_details', 'long_height_details_hover']) ) :
						foreach($ekit_team_members as $index => $ekit_team_member) :
							$image_html = $this->get_image_html($ekit_team_member, $settings);

							if($ekit_team_style == 'centered_style'): ?> <div class="profile-square-v swiper-slide"> <?php endif;
							if($ekit_team_style == 'centered_style_details'): ?> <div class="profile-square-v square-v5 no_gutters swiper-slide"> <?php endif;
							if($ekit_team_style == 'long_height_details'): ?> <div class="profile-square-v square-v6 no_gutters swiper-slide"> <?php endif;
							if($ekit_team_style == 'long_height_details_hover'): ?> <div class="profile-square-v square-v6 square-v6-v2 no_gutters swiper-slide"><?php endif; ?>

							<div <?php $this->print_render_attribute_string('profile_card'); ?>>
								<?php if ($settings['ekit_team_chose_popup'] == 'yes') : ?>
									<a href="javascript:void(0)" data-mfp-src="#ekit_team_modal_<?php echo esc_attr( $ekit_team_member['_id'].$widget_id); ?>" class="ekit-team-popup">
								<?php endif; ?>

								<div class="profile-header ekit-team-img <?php echo esc_attr($ekit_team_style == 'default' ? 'ekit-img-overlay ekit-team-img-block' : ''); ?>" <?php if ( (isset($settings['ekit_team_chose_popup']) ? $settings['ekit_team_chose_popup'] : 'no')  == 'yes') :?> data-toggle="modal" data-target="ekit_team_modal_#<?php echo esc_attr($ekit_team_member['_id'].$widget_id); ?>" <?php endif; ?>>
									<?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
								</div>

								<?php if ($settings['ekit_team_chose_popup'] == 'yes') : ?>
									</a>
								<?php endif; ?>
				
								<?php include Handler::get_dir() . 'parts/content.php'; ?>

								<?php if($settings['ekit_team_socail_enable'] == 'yes') : ?>                       
									<?php include Handler::get_dir() . 'parts/social-list.php'; ?>
								<?php endif; ?>
							</div>

							<?php if(!in_array($ekit_team_style, ['default'])): ?> </div> <?php endif; ?>
							
							<?php if ( $settings['ekit_team_chose_popup'] == 'yes' || $settings['ekit_team_chose_button_popup'] == 'yes' ) {
								include Handler::get_dir() . 'parts/popup.php';
							} ?>
						<?php endforeach; ?>

					<?php elseif ( in_array($ekit_team_style, ['overlay', 'overlay_details', 'long_height_hover', 'overlay_circle', 'overlay_circle_hover', 'overlay_content_hover']) ) :
						foreach($ekit_team_members as $index => $ekit_team_member) :
							$image_html = $this->get_image_html($ekit_team_member, $settings);

							if($ekit_team_style == 'overlay_details'): ?> <div class="image-card-v2 swiper-slide"> <?php endif;
							if($ekit_team_style == 'long_height_hover'): ?> <div class="image-card-v3 swiper-slide <?php if( $ekit_team_content_stable == 'yes') echo 'team-stable-content'; ?>"> <?php endif;
							if($ekit_team_style == 'overlay_circle'): ?> <div class="style-circle ekit-team-img-fit swiper-slide"> <?php endif;
							if($ekit_team_style == 'overlay_circle_hover'): ?> <div class="image-card-v2 style-circle swiper-slide"> <?php endif; ?>

							<div class="profile-image-card elementor-animation-<?php echo esc_attr($team_hover_animation) ?> ekit-team-img ekit-team-style-<?php echo esc_attr($ekit_team_style); ?> <?php if(isset($ekit_team_content_text_align)) { echo esc_attr($ekit_team_content_text_align);} ?> <?php if($ekit_team_style == 'overlay' || $ekit_team_style == 'overlay_content_hover') echo 'swiper-slide'; ?>">
								<?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>

								<div class="hover-area">
									<?php if($ekit_team_content_stable != 'yes') : ?>
										<?php include Handler::get_dir() . 'parts/content.php'; ?>
									<?php endif; ?>

									<?php if(isset($settings['ekit_team_socail_enable']) && $settings['ekit_team_socail_enable'] == 'yes') { ?>
										<?php include Handler::get_dir() . 'parts/social-list.php'; 
									} ?>
								</div>   

								<?php if($ekit_team_style == 'overlay_content_hover' || $ekit_team_style == 'overlay_details') : ?>
									<div class="profile-body ekit-none overlay-content-hover <?php if($ekit_team_content_stable == 'yes' && isset($ekit_team_content_text_align)) { echo esc_attr($ekit_team_content_text_align);} ?>">
										<h2 class="profile-title">
											<?php if ($settings['ekit_team_chose_popup'] == 'yes') : ?>
												<a  href="javascript:void(0)" data-mfp-src="#ekit_team_modal_<?php echo esc_attr($ekit_team_member['_id'].$widget_id); ?>" class="ekit-team-popup">
													<?php echo esc_html( $ekit_team_member['name'] ); ?>
												</a>
												<?php else: ?>
													<?php echo esc_html( $ekit_team_member['name'] ); ?>
											<?php endif; ?>
										</h2>
										<p class="profile-designation"> 
											<?php echo esc_html( $ekit_team_member['position'] ); ?>
										</p>
									</div>
								<?php endif; ?>                             
							</div>

							<?php if($ekit_team_content_stable == 'yes') : ?>
								<?php include Handler::get_dir() . 'parts/content.php'; ?>
							<?php endif; ?>
							
							<?php if(!in_array($ekit_team_style, ['overlay', 'overlay_content_hover'])): ?> </div> <?php endif; ?>

							<?php if ( $settings['ekit_team_chose_popup'] == 'yes' || $settings['ekit_team_chose_button_popup'] == 'yes' ) {
								include Handler::get_dir() . 'parts/popup.php';
							} ?>
						<?php endforeach;

					elseif ( in_array($ekit_team_style, ['hover_info']) ) :
						foreach($ekit_team_members as $index => $ekit_team_member) :
							$image_html = $this->get_image_html($ekit_team_member, $settings); ?>

							<div class="profile-square-v square-v4 elementor-animation-<?php echo esc_attr($team_hover_animation) ?> ekit-team-style-<?php echo esc_attr($ekit_team_style); ?> swiper-slide">
								<div class="profile-card <?php if(isset($ekit_team_content_text_align)) { echo esc_attr($ekit_team_content_text_align);} ?>">
									<div class="profile-header ekit-team-img" <?php if ($settings['ekit_team_chose_popup'] == 'yes') :?> data-toggle="modal" data-target="#ekit_team_modal_<?php echo esc_attr($ekit_team_member['_id'].$widget_id); ?>" <?php endif; ?>>
										<?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
									</div>

									<?php include Handler::get_dir() . 'parts/content.php'; ?>
					
								</div>
							</div>

							<?php if ( $settings['ekit_team_chose_popup'] == 'yes' || $settings['ekit_team_chose_button_popup'] == 'yes' ) {
								include Handler::get_dir() . 'parts/popup.php';
							} ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>

				<?php if($ekit_team_show_dot == 'yes') : ?>
					<div class="swiper-pagination"></div>
				<?php endif; ?>

				<?php if($ekit_team_show_arrow == 'yes') : ?>
					<div class="elementor-swiper-button ekit-team-slider-button-prev">
						<?php if($ekit_team_arrow_type == 'icon') :
							Icons_Manager::render_icon( $ekit_team_slider_left_arrow_icon, [ 'aria-hidden' => 'true' ]); 
						elseif($ekit_team_arrow_type == 'text') : ?>
							<span><?php echo esc_html($ekit_team_arrow_left_text); ?></span>
						<?php else :
							Icons_Manager::render_icon( $ekit_team_slider_left_arrow_icon, [ 'aria-hidden' => 'true' ]); ?>
							<span><?php echo esc_html($ekit_team_arrow_left_text); ?></span>
						<?php endif; ?>
					</div>
					<div class="elementor-swiper-button ekit-team-slider-button-next">
						<?php if($ekit_team_arrow_type == 'icon') :
							Icons_Manager::render_icon( $ekit_team_slider_right_arrow_icon, [ 'aria-hidden' => 'true' ]);
						elseif($ekit_team_arrow_type == 'text') : ?>
							<span><?php echo esc_html($ekit_team_arrow_right_text); ?></span>
						<?php else : ?>
							<span><?php echo esc_html($ekit_team_arrow_right_text); ?></span>
							<?php Icons_Manager::render_icon( $ekit_team_slider_right_arrow_icon, [ 'aria-hidden' => 'true' ]); 
						endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
