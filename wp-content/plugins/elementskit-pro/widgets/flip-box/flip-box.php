<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Flip_Box_Handler as Handler;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Flip_Box extends Widget_Base {
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
        return 'https://wpmet.com/doc/flip-box/';
    }

    protected function register_controls() {

        // Flip Box
        $this->start_controls_section(
            'ekit_flip_content_section',
            [
                'label' => esc_html__( 'Flip Box', 'elementskit' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
			'ekit_flip_box_style',
			[
				'label'       => esc_html__('Flip Style', 'elementskit'),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'flip',
				'options'     => [
					'flip' => esc_html__('Flip', 'elementskit'),
					'slide' => esc_html__('Slide', 'elementskit'),
                    'zoom' => esc_html__('Zoom', 'elementskit'),
                    '3d' => esc_html__('3D', 'elementskit'),
                    'push' => esc_html__('Push', 'elementskit'),
                    'transform' => esc_html__('Transform', 'elementskit'),
                    'fade' => esc_html__('Fade', 'elementskit'),
				],
			]
		);

        $this->add_control(
			'ekit_flip_box_direction',
			[
				'label' => esc_html__( 'Direction', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left_to_right',
				'options' => [
					'left_to_right'  => esc_html__( 'Left To Right', 'elementskit' ),
					'right_to_left' => esc_html__( 'Right To Left', 'elementskit' ),
					'top_to_bottom' => esc_html__( 'Top To Bottom', 'elementskit' ),
					'bottom_to_top' => esc_html__( 'Bottom To Top', 'elementskit' ),
				],
                'condition' => [
                    'ekit_flip_box_style!' => [ 'zoom', 'fade'],
                ],
			]
		);

        $this->add_control(
			'ekit_flip_box_zoom_direction',
			[
				'label' => esc_html__( 'Direction', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
                'default' => 'zoom_up',
				'options' => [
                    'zoom_up' => esc_html__( 'Zoom In', 'elementskit' ),
                    'zoom_out' => esc_html__( 'Zoom Out', 'elementskit' ),
				],
                'condition' => [
                    'ekit_flip_box_style' => 'zoom',
                ],
			]
		);

        $this->add_control(
			'ekit_flip_box_style_trigger',
			[
				'label' => esc_html__( 'Mouse Event', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'box_hover',
				'options' => [
					'box_hover'  => esc_html__( 'Hover', 'elementskit' ),
					'box_click' => esc_html__( 'Box Click', 'elementskit' ),
					'button_click' => esc_html__( 'Button Click', 'elementskit' ),
				],
                'condition' => [
                    'ekit_flip_box_style!' => ['fade', 'push' , 'transform'],
                ],
			]
		);

        $this->start_controls_tabs(
            'flip_content_tabs'
        );
        
        $this->start_controls_tab(
            'flip_content_front_tab',
            [
                'label' => esc_html__( 'Front', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_flip_front_media_type', [
                'label'       => esc_html__( 'Media Type', 'elementskit' ),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'none' => [
                        'title' => esc_html__( 'None', 'elementskit' ),
                        'icon'  => 'fa fa-ban',
                    ],
                    'icon' => [
                        'title' => esc_html__( 'Icon', 'elementskit' ),
                        'icon'  => 'fa fa-paint-brush',
                    ],
                    'image' => [
                        'title' => esc_html__( 'Image', 'elementskit' ),
                        'icon'  => 'fa fa-image',
                    ],
                ],
                'toggle' => false,
                'dynamic' => [
					'active' => true,
				],
                'default'       => 'icon',
            ]
        );

        $this->add_control(
            'ekit_flip_icon',
            [
                'label' => esc_html__( 'Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_icon_box_header_icon',
                'default' => [
                    'value' => 'icon icon-review',
                    'library' => 'ekiticons',
                ],
                'label_block' => true,
                'condition' => [
                    'ekit_flip_front_media_type' => 'icon',
				]
            ]
        );

        $this->add_control(
			'ekit_flip_front_image',
			[
				'label' => esc_html__( 'Choose Image', 'elementskit' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
                'dynamic' => [
					'active' => true,
				],
                'condition' => [
                    'ekit_flip_front_media_type' => 'image',
				]
			]
		);

        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'ekit_flip_front_thumbnail',
				'default' => '',
                'condition' => [
                    'ekit_flip_front_media_type' => 'image',
				]
			]
		);

        $this->add_control(
            'ekit_flip_title',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__( 'Title', 'elementskit' ),
                'dynamic' => [
					'active' => true,
				],
                'default' => esc_html__('Ekit Flip Box', 'elementskit'),
            ]
        );

        $this->add_control(
			'ekit_flip_title_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h4',
			]
		);

        $this->add_control(
            'ekit_flip_sub_title',
            [
                'type' => Controls_Manager::TEXTAREA,
                'label' => esc_html__( 'Sub Title', 'elementskit' ),
                'default' => esc_html__('Amazingly on mouse hover', 'elementskit'),
                'rows' => 2,
                'label_block' => true,
                'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
			'ekit_flip_front_description',
			[
				'label' => esc_html__( 'Flip Description', 'elementskit' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'rows' => 3,
				'label_block'	 => true,
				'default'	 => esc_html__( 'A flip box is a box that flips over when you hover over it.', 'elementskit' ),
				'placeholder' => esc_html__( 'Title Description', 'elementskit' ),
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'flip_content_back_tab',
            [
                'label' => esc_html__( 'Back', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_flip_back_media_type', [
                'label'       => esc_html__( 'Media Type', 'elementskit' ),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'none' => [
                        'title' => esc_html__( 'None', 'elementskit' ),
                        'icon'  => 'fa fa-ban',
                    ],
                    'icon' => [
                        'title' => esc_html__( 'Icon', 'elementskit' ),
                        'icon'  => 'fa fa-paint-brush',
                    ],
                    'image' => [
                        'title' => esc_html__( 'Image', 'elementskit' ),
                        'icon'  => 'fa fa-image',
                    ],
                ],
                'toggle' => false,
                'dynamic' => [
					'active' => true,
				],
                'default'    => 'icon',
            ]
        );

        $this->add_control(
            'ekit_flip_back_icon',
            [
                'label' => esc_html__( 'Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_icon_box_header_icon',
                'default' => [
                    'value' => 'icon icon-star',
                    'library' => 'ekiticons',
                ],
                'label_block' => true,
                'condition' => [
                    'ekit_flip_back_media_type' => 'icon',
				]
            ]
        );

        $this->add_control(
			'ekit_flip_back_image',
			[
				'label' => esc_html__( 'Choose Image', 'elementskit' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
                'dynamic' => [
					'active' => true,
				],
                'condition' => [
                    'ekit_flip_back_media_type' => 'image',
				]
			]
		);

        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'ekit_flip_back_thumbnail', 
				'include' => [],
				'default' => 'thumbnail',
                'condition' => [
                    'ekit_flip_back_media_type' => 'image',
				]
			]
		);

        $this->add_control(
            'ekit_flip_back_title',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__( 'Title', 'elementskit' ),
                'default' => esc_html__('Ekit Flip', 'elementskit'),
                'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
			'ekit_flip_back_title_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h4',
			]
		);

        $this->add_control(
            'ekit_flip_back_sub_title',
            [
                'type' => Controls_Manager::TEXTAREA,
                'label' => esc_html__( 'Sub Title', 'elementskit' ),
                'default' => esc_html__('Ceate New Feature', 'elementskit'),
                'rows' => 2,
				'label_block'	 => true,
                'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
			'ekit_flip_back_description',
			[
				'label' => esc_html__( 'Flip Description', 'elementskit' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'rows' => 3,
				'label_block'	 => true,
				'default'	=> esc_html__( 'A flip box is a box that flips over when you hover over it. You can choose from different animations', 'elementskit' ),
				'placeholder' => esc_html__( 'Title Description', 'elementskit' ),
			]
		);

        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_control(
			'ekit_flip_box_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'elementskit' ),
				'type' => Controls_Manager::NUMBER,
                'separator' => 'before',
                'dynamic' 	=> [
                    'active' => true,
                ],
				'min' => 0,
				'max' => 10,
				'step' => 0.1,
				'default' => 0.9,
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-inner' => 'transition: transform {{VALUE}}s, -webkit-transform {{VALUE}}s;',
                    '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-inner .ekit-flip-box-back' => 'transition: {{VALUE}}s ease-in-out;',
                    '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-inner .ekit-flip-box-front' => 'transition: {{VALUE}}s ease-in-out;',
                ],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'ekit_flip_box_icon_button',
            [
                'label' => esc_html__( 'Button', 'elementskit' ),
                'tab' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
			'ekit_flip_front_button_info',
			[
				'label' => esc_html__( 'Front Button', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

        $this->add_control(
			'ekit_flip_box_icon_select',
			[
				'label' => esc_html__( 'Button Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_icon_box_header_icon',
                'default' => [
                    'value' => 'icon icon-review',
                    'library' => 'ekiticons',
                ],
                'skin' => 'inline',
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				],
				'label_block' => true,
			]
		);

        $this->add_control(
			'ekit_flip_box_icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before' => esc_html__( 'Before', 'elementskit' ),
					'after' => esc_html__( 'After', 'elementskit' ),
				],
                'condition' => [
                    'ekit_flip_box_icon_select[value]!' => '',
				]
			]
		);

        $this->add_control(
            'ekit_flip_front_button_text',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__( 'Button Text', 'elementskit' ),
                'default' => esc_html__('Click Me', 'elementskit'),
                'dynamic' => [
					'active' => true,
				],
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				],
                'separator' => 'after'
            ]
        );

        $this->add_control(
			'ekit_flip_back_button_info',
			[
				'label' => esc_html__( 'Back Button', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

        $this->add_control(
			'ekit_flip_box_back_icons',
			[
				'label' => esc_html__( 'Button Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_icon_box_header_icon',
                'default' => [
                    'value' => 'icon icon-review',
                    'library' => 'ekiticons',
                ],
                'skin' => 'inline',
				'label_block' => true,
			]
		);

        $this->add_control(
			'ekit_flip_box_back_icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before' => esc_html__( 'Before', 'elementskit' ),
					'after' => esc_html__( 'After', 'elementskit' ),
				],
                'condition' => [
                    'ekit_flip_box_back_icons[value]!' => '',
				],
			]
		);

        $this->add_control(
            'ekit_flip_back_button_text',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__( 'Button Text', 'elementskit' ),
                'default' => esc_html__('Read More', 'elementskit'),
                'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
			'ekit_flip_back_button_url',
			[
				'label' => esc_html__( 'URL', 'elementskit' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_url('https://wpmet.com/'),
				'dynamic' => [
                    'active' => true,
                ],
				'default' => [
					'url' => 'https://wpmet.com/',
				],
			]
		);

        $this->end_controls_section();

        // Filp box Style
        $this->start_controls_section(
            'ekit_flip_box_layout_style',
            [
                'label' => esc_html__( 'Wrapper', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'ekit_flip_box_height',
            [
                'label' => esc_html__( 'Flip Box Height', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 300,
                        'max' => 500,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 380,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-flip-box'    => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'ekit_flip_box_bg_tabs'
        );
        
        $this->start_controls_tab(
            'flip_box_front_bg_tab',
            [
                'label' => esc_html__( 'Front', 'elementskit' ),
            ]
        );

        $this->add_control(
			'ekit_flip_box_front_align',
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
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-inner .ekit-flip-box-front' => 'text-align: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_flip_box_front_bg',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient'],
                'exclude' => ['video'],
                'selector' => '{{WRAPPER}} .ekit-flip-box-front',
            ]
        );

        $this->add_control(
			'ekit_flip_box_front_overlay',
			[
				'label' => esc_html__( 'Overlay', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-front::after' => 'background: {{VALUE}}',
				],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'ekit_flip_box_front_bg_background',
                            'operator' => 'in',
                            'value' => [ 'classic' ],
                        ],
                    ],
                ],
			]
		);

        $this->add_responsive_control(
            'ekit_flip_box_front_overlay_border_radius',
            [
                'label' => esc_html__( 'Oerlay Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top'      => '0',
                    'right'    => '0',
                    'bottom'   => '0',
                    'left'     => '0',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-front::after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'ekit_flip_box_front_bg_background',
                            'operator' => 'in',
                            'value' => [ 'classic' ],
                        ],
                    ],
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_flip_box_front_shadow',
                'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-inner .ekit-flip-box-front',
                'condition' => [
                    'ekit_flip_box_style!' => [ 'push', 'slide' ],
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_flip_box_front_border_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-inner .ekit-flip-box-front',
                'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
                    'size_units'     => ['px'],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
						],
					],
                ]    
            ]
        );

        $this->add_responsive_control(
            'ekit_flip_box_front_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-inner .ekit-flip-box-front' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'flip_box_back_bg_tab',
            [
                'label' => esc_html__( 'Back', 'elementskit' ),
            ]
        );

        $this->add_control(
			'ekit_flip_box_back_align',
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
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-inner .ekit-flip-box-back' => 'text-align: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_flip_box_back_bg',
                'label' => esc_html__( 'Flip Back Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ekit-flip-box-back',
            ]
        );

        $this->add_control(
			'ekit_flip_box_back_bg_overlay',
			[
				'label' => esc_html__( 'Overlay', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-back::after' => 'background: {{VALUE}}',
				],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'ekit_flip_box_back_bg_background',
                            'operator' => 'in',
                            'value' => [ 'classic' ],
                        ],
                    ],
                ],
			]
		);

        $this->add_responsive_control(
            'ekit_flip_box_back_bg_border_radius',
            [
                'label' => esc_html__( 'Oerlay Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top'      => '0',
                    'right'    => '0',
                    'bottom'   => '0',
                    'left'     => '0',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-back::after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'ekit_flip_box_back_bg_background',
                            'operator' => 'in',
                            'value' => [ 'classic' ],
                        ],
                    ],
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_flip_box_back_shadow',
                'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-inner .ekit-flip-box-back',
                'condition' => [
                    'ekit_flip_box_style!' => [ 'push', 'slide' ],
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_flip_box_back_border_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-inner .ekit-flip-box-back',
                'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
                    'size_units'     => ['px'],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
						],
					],
                ]    
            ]
        );
    
        $this->add_responsive_control(
            'ekit_flip_box_back_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-inner .ekit-flip-box-back' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'ekit_flip_box_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em' ],
                'separator' => 'before',
                'default' =>     [
                    'top' => '30',
                    'right' => '30',
                    'bottom' => '30',
                    'left' => '30',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-flip-box-inner :is( .ekit-flip-box-front, .ekit-flip-box-back )' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_flip_box_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default'    => [
					'unit'     => 'px',
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-flip-box-inner :is( .ekit-flip-box-front, .ekit-flip-box-back )' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

        // Icon style
        $this->start_controls_section(
            'ekit_flip_icon_style',
            [
                'label' => esc_html__( 'Icon', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'ekit_flip_front_media_type',
                            'operator' => '===',
                            'value' => 'icon',
                        ],
                        [
                            'name' => 'ekit_flip_back_media_type',
                            'operator' => '===',
                            'value' => 'icon',
                        ],
                    ],
                ],
            ]
        );

        // Front Icon
        $this->add_control(
			'ekit_flip_icon_front',
			[
				'label' => esc_html__( 'Front Icon', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'ekit_flip_front_media_type',
                            'operator' => '===',
                            'value' => 'icon',
                        ],
                        [
                            'name' => 'ekit_flip_back_media_type',
                            'operator' => '===',
                            'value' => 'icon',
                        ],
                    ],
                ],
			]
		);

        $this->add_control(
			'ekit_flip_icon_size',
			[
				'label'      => esc_html__('Icon Font Size', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 150,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-icon-wrapper i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-icon-wrapper svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_flip_front_media_type' => 'icon',
				],
			]
		);

        $this->start_controls_tabs(
            'ekit_flip_icon_style_tabs',
            [
                'condition' => [
                    'ekit_flip_front_media_type' => 'icon',
				]
            ]
        );
        
        $this->start_controls_tab(
            'ekit_flip_icon_style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_flip_icon_color', 
            [
                'label' => esc_html__('Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-icon-wrapper i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-icon-wrapper svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_flip_icon_style_bg_group',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ekit-icon-wrapper',
                'condition' => [
                    'ekit_flip_front_media_type' => 'icon',
				]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_flip_icon_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-flip-box-inner-wrap .ekit-icon-wrapper',
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'size_units'     => ['px'],
                    'width'  => [
                        'default' => [
                            'top'      => '1',
                            'right'    => '1',
                            'bottom'   => '1',
                            'left'     => '1',
                        ],
                    ],
                    'color'  => [
                        'default' => '#fff',
                    ]
                ],
                'condition' => [
                    'ekit_flip_front_media_type' => 'icon',
				]  
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_flip_icon_style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
                'condition' => [
                    'ekit_flip_front_media_type' => 'icon',
				]
            ]
        );

        $this->add_control(
            'ekit_flip_icon_h_color', 
            [
                'label' => esc_html__('Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-icon-wrapper:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-icon-wrapper:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_flip_icon_bg_hover_group',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ekit-icon-wrapper:hover',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_flip_icon_box_border_hv_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-icon-wrapper:hover',
            ]
        );

        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'ekit_flip_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%'],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                    'unit'     => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-icon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_flip_front_media_type' => 'icon',
				],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'ekit_flip_icon_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em' ],
                'default' =>     [
                    'top' => '15',
                    'right' => '15',
                    'bottom' => '15',
                    'left' => '15',
                    'unit' => 'px',
                ],
                'condition' => [
                    'ekit_flip_front_media_type' => 'icon',
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-icon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_flip_icon_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default'    => [
					'unit'     => 'px',
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 10,
					'left'     => 0,
					'isLinked' => true,
				],
                'condition' => [
                    'ekit_flip_front_media_type' => 'icon',
				],
				'selectors'  => [
                    '{{WRAPPER}} .ekit-icon-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'separator' => 'after',
			]
		);

        // Back Icon
        $this->add_control(
			'ekit_flip_icon_back',
			[
				'label' => esc_html__( 'Back Icon', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'ekit_flip_front_media_type',
                            'operator' => '===',
                            'value' => 'icon',
                        ],
                        [
                            'name' => 'ekit_flip_back_media_type',
                            'operator' => '===',
                            'value' => 'icon',
                        ],
                    ],
                ],
			]
		);

        $this->add_control(
			'ekit_flip-back_icon_size',
			[
				'label'      => esc_html__('Icon Font Size', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 150,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-back-icon-wrapper i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-back-icon-wrapper svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; position: relative; top: 3px;',
				],
                'condition' => [
                    'ekit_flip_back_media_type' => 'icon',
				],
			]
		);

        $this->start_controls_tabs(
            'ekit_flip_back_icon_style_tabs',
            [
                'condition' => [
                    'ekit_flip_back_media_type' => 'icon',
				]
            ]
        );
        
        $this->start_controls_tab(
            'ekit_flip_back_icon_style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_flip_back_icon_color', 
            [
                'label' => esc_html__('Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-back-icon-wrapper i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-back-icon-wrapper svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_flip_back_icon_style_bg_group',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ekit-back-icon-wrapper',
                'exclude'  => ['image'],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_flip_back_icon_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-back-icon-wrapper',
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'size_units'     => ['px'],
                    'width'  => [
                        'default' => [
                            'top'      => '1',
                            'right'    => '1',
                            'bottom'   => '1',
                            'left'     => '1',
                        ],
                    ],
                    'color'  => [
                        'default' => '#fff',
                    ]
                ], 
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_flip_back_icon_style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
                'condition' => [
                    'ekit_flip_back_media_type' => 'icon',
				]
            ]
        );

        $this->add_control(
            'ekit_flip_back_icon_h_color', 
            [
                'label' => esc_html__('Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-back-icon-wrapper:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-back-icon-wrapper:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_flip_back_icon_bg_hover_group',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ekit-back-icon-wrapper:hover',
                'exclude'  => ['image'],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_flip_back_icon_box_border_hv_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-back-icon-wrapper:hover',
            ]
        );

        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'ekit_flip_back_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                    'unit'     => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-back-icon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_flip_back_media_type' => 'icon',
				],
                'separator'  => 'before',
            ]
        );

        $this->add_responsive_control(
            'ekit_flip-back_icon_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em' ],
                'default' =>     [
                    'top' => '15',
                    'right' => '15',
                    'bottom' => '15',
                    'left' => '15',
                    'unit' => 'px',
                ],
                'condition' => [
                    'ekit_flip_back_media_type' => 'icon',
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-back-icon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_flip-back_icon_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default'    => [
					'unit'     => 'px',
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 10,
					'left'     => 0,
					'isLinked' => true,
				],
                'condition' => [
                    'ekit_flip_back_media_type' => 'icon',
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-back-icon-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

        // Image style
        $this->start_controls_section(
            'ekit_flip_image_style',
            [
                'label' => esc_html__( 'Image', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'ekit_flip_front_media_type',
                            'operator' => '===',
                            'value' => 'image',
                        ],
                        [
                            'name' => 'ekit_flip_back_media_type',
                            'operator' => '===',
                            'value' => 'image',
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
			'ekit_flip_image_front',
			[
				'label' => esc_html__( 'Front', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'ekit_flip_front_media_type',
                            'operator' => '===',
                            'value' => 'image',
                        ],
                        [
                            'name' => 'ekit_flip_back_media_type',
                            'operator' => '===',
                            'value' => 'image',
                        ],
                    ],
                ],
			]
		);

        $this->add_control(
			'ekit_flip_front_image_width',
			[
				'label' => esc_html__( 'Image Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%',],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-top-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_flip_front_media_type' => 'image',
				]
			]
		);

        $this->add_responsive_control(
            'ekit_flip_image_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                    'unit'     => 'px',
                ],
                'condition' => [
                    'ekit_flip_front_media_type' => 'image',
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-flip-box-top-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_flip_image_back',
			[
				'label' => esc_html__( 'Back', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'ekit_flip_front_media_type',
                            'operator' => '===',
                            'value' => 'image',
                        ],
                        [
                            'name' => 'ekit_flip_back_media_type',
                            'operator' => '===',
                            'value' => 'image',
                        ],
                    ],
                ],
			]
		);

        $this->add_control(
			'ekit_flip_back_image_width',
			[
				'label' => esc_html__( 'Image Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%',],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-back-image > img' => 'width: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_flip_back_media_type' => 'image',
				]
			]
		);

        $this->add_responsive_control(
            'ekit_flip_image_back_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                    'unit'     => 'px',
                ],
                'condition' => [
                    'ekit_flip_back_media_type' => 'image',
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-flip-box-back-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Filp Title Style
        $this->start_controls_section(
            'ekit_flip_title_style',
            [
                'label' => esc_html__( 'Title', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'ekit_flip_title_tabs'
        );
        
        $this->start_controls_tab(
            'ekit_flip_title_front_tab',
            [
                'label' => esc_html__( 'Front', 'elementskit' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_flip_title_typography',
                'selector'	 => '{{WRAPPER}} .ekit-flip-box-front-title',
            ]
        );

        $this->add_control(
            'ekit_flip_title_color', 
            [
                'label' => esc_html__('Title Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-flip-box-front-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_flip_title_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default'    => [
					'unit'     => 'px',
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 10,
					'left'     => 0,
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-flip-box-front-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_flip_title_back_tab',
            [
                'label' => esc_html__( 'Back', 'elementskit' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_flip_back_title_typography',
                'selector'	 => '{{WRAPPER}} .ekit-flip-box-back-title',
            ]
        );

        $this->add_control(
            'ekit_flip_back_title_color', 
            [
                'label' => esc_html__('Title Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-flip-box-back-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_flip_back_title_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default'    => [
					'unit'     => 'px',
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 10,
					'left'     => 0,
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-flip-box-back-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // Filp Sub Title Style
        $this->start_controls_section(
            'ekit_flip_sub_title_style',
            [
                'label' => esc_html__( 'Sub Title', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'ekit_flip_sub_title_tabs'
        );
        
        $this->start_controls_tab(
            'ekit_flip_sub_title_front_tab',
            [
                'label' => esc_html__( 'Front', 'elementskit' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_flip_sub_title_typography',
                'selector'	 => '{{WRAPPER}} .ekit-flip-box-front-sub-title',
            ]
        );

        $this->add_control(
            'ekit_flip_sub_title_color', 
            [
                'label' => esc_html__('Title Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-flip-box-front-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_flip_sub_title_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default'    => [
					'unit'     => 'px',
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-flip-box-front-sub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_flip_sub_title_back_tab',
            [
                'label' => esc_html__( 'Back', 'elementskit' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_flip_sub_back_title_typography',
                'selector'	 => '{{WRAPPER}} .ekit-flip-box-back-sub-title',
            ]
        );

        $this->add_control(
            'ekit_flip_sub_back_title_color', 
            [
                'label' => esc_html__('Title Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-flip-box-back-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_flip_sub_back_title_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default'    => [
					'unit'     => 'px',
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-flip-box-back-sub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->end_controls_section();

        // Filp Description Style
        $this->start_controls_section(
            'ekit_flip_description_style',
            [
                'label' => esc_html__( 'Description', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'ekit_flip_description_tabs'
        );

        $this->start_controls_tab(
            'ekit_flip_description_front_tab',
            [
                'label' => esc_html__( 'Front', 'elementskit' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_flip_front_description_typography',
                'selector'	 => '{{WRAPPER}} .ekit-flip-box-front-description',
            ]
        );

        $this->add_control(
            'ekit_flip_front_description_color', 
            [
                'label' => esc_html__('Description Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-flip-box-front-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_flip_front_description_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default'    => [
					'unit'     => 'px',
					'top'      => 10,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-flip-box-front-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_flip_description_back_tab',
            [
                'label' => esc_html__( 'Back', 'elementskit' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_flip_back_description_typography',
                'selector'	 => '{{WRAPPER}} .ekit-flip-box-back-description',
            ]
        );

        $this->add_control(
            'ekit_flip_description_color', 
            [
                'label' => esc_html__('Description Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-flip-box-back-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_flip_description_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default'    => [
					'unit'     => 'px',
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 10,
					'left'     => 0,
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-flip-box-back-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // Filp box button Style
        $this->start_controls_section(
			'ekit_flip_button_style',
			[
				'label' => esc_html__( 'Button', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
            'ekit_flip_button_front_heading',
            [
                'label' => esc_html__( 'Front Button', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_flip_button_front_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-flip-box-front-button',
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

		$this->start_controls_tabs( 'ekit_flip_front_btn_tabs_style' );

		$this->start_controls_tab(
			'ekit_flip_front_btn_tabnormal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

		$this->add_responsive_control(
			'ekit_flip_box_front_btn_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-front-button' => 'color: {{VALUE}};',
				],
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

		$this->add_responsive_control(
			'ekit_flip_box_front_btn_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-front-button' => 'background-color: {{VALUE}}',
				],
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_flip_box_front_btn_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-flip-box-front-button',
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_flip_box_front_btn_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

		$this->add_responsive_control(
			'ekit_flip_box_front_btn_h_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-front-button:hover' => 'color: {{VALUE}};',
				],
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

		$this->add_responsive_control(
			'ekit_flip_box_front_btn_h_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-front-button:hover' => 'background-color: {{VALUE}}',
				],
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_flip_box_front_btn_h_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-flip-box-front-button:hover',
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_btn_bg_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-front-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                    'unit'     => 'px',
                ],
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				],
                'separator'	=> 'before'
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
			  'name' => 'ekit_flip_box_front_btn_shadow',
			  'selector' => '{{WRAPPER}} .ekit-flip-box-front-button',
              'condition' => [
                'ekit_flip_box_style_trigger' => 'button_click',
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_flip_box_front_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '10',
                    'bottom'   => '5',
                    'left'     => '10',
                    'unit'     => 'px',
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-front-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				]
			]
		);

        $this->add_responsive_control(
			'ekit_flip_box_front_btn_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
                'default' => [
                    'top'      => '15',
                    'right'    => '0',
                    'bottom'   => '0',
                    'left'     => '0',
                    'unit'     => 'px',
                ],
                'condition' => [
                    'ekit_flip_box_style_trigger' => 'button_click',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-front-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'separator'	=> 'after'
			]
		);

        $this->add_control(
            'ekit_flip_button_back_heading',
            [
                'label' => esc_html__( 'Back Button', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
            ],
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_flip_button_back_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-flip-box-back-button',
			]
		);

		$this->start_controls_tabs( 'ekit_flip_back_btn_tabs_style' );

		$this->start_controls_tab(
			'ekit_flip_back_btn_tabnormal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_flip_box_back_btn_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
                'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-back-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_flip_box_back_btn_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-back-button' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_flip_box_back_btn_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-flip-box-back-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_flip_box_back_btn_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_flip_box_back_btn_h_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-back-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_flip_box_back_btn_h_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-back-button:hover' => 'background-color: {{VALUE}}',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_flip_box_back_btn_h_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-flip-box-back-button:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_flip_box_back_btn_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-back-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                    'unit'     => 'px',
                ],
                'separator'	=> 'before'
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
			  'name' => 'ekit_flip_box_back_btn_shadow',
			  'selector' => '{{WRAPPER}} .ekit-flip-box-back-button',
			]
		);

        $this->add_responsive_control(
			'ekit_flip_box_back_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],
                'default' => [
                    'top'      => '5',
                    'right'    => '10',
                    'bottom'   => '5',
                    'left'     => '10',
                    'unit'     => 'px',
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-back-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_flip_box_back_btn_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],
                'default' => [
                    'top'      => '0',
                    'right'    => '0',
                    'bottom'   => '0',
                    'left'     => '0',
                    'unit'     => 'px',
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit-flip-box-back-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'ekit_flip_button_icon_style',
            [
                'label' => esc_html__( 'Button Icon', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'ekit_flip_box_icon_select[value]',
                            'operator' => '!=',
                            'value' => '',
                        ],
                        [
                            'name' => 'ekit_flip_box_back_icons[value]!',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
			'ekit_flip_button_icon_front',
			[
				'label' => esc_html__( 'Front', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
                'condition' => [
                    'ekit_flip_box_icon_select[value]!' => '',
				],
			]
		);

        $this->add_control(
			'ekit_flip_box_button_icon_size',
			[
				'label' => esc_html__('Size (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
                'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-front-button > i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-front-button > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; position:relative; top: 4px;',
				],
                'condition' => [
                    'ekit_flip_box_icon_select[value]!' => '',
				],
			]
		);

        $this->add_control(
			'ekit_flip_box_button_icon_spacing',
			[
				'label' => esc_html__('Spacing (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
                'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-front-button > .flip-box-button-icon-after' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-front-button > .flip-box-button-icon-before' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-front-button > svg' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_flip_box_icon_select[value]!' => '',
				],
			]
		);

        $this->add_control(
			'ekit_flip_box_button_icon_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-front-button > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-front-button > svg' => 'fill: {{VALUE}};',
				],
                'condition' => [
                    'ekit_flip_box_icon_select[value]!' => '',
				],
			]
		);

		$this->add_control(
			'ekit_flip_box_button_icon_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-front-button > i:hover ' => 'color: {{VALUE}}; transition: .3s; ',
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-front-button > svg:hover' => 'fill: {{VALUE}}; transition: .3s;',
				],
                'condition' => [
                    'ekit_flip_box_icon_select[value]!' => '',
				],
                'separator' => 'after',
			]
		);

        $this->add_control(
			'ekit_flip_button_icon_back',
			[
				'label' => esc_html__( 'Back', 'elementskit' ),
				'type' => \Elementor\Controls_Manager::HEADING,
                'condition' => [
                    'ekit_flip_box_back_icons[value]!' => '',
				],
			]
		);

        $this->add_control(
			'ekit_flip_box_button_back_icon_size',
			[
				'label' => esc_html__('Size (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
                'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-back-button > i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-back-button > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_flip_box_back_icons[value]!' => '',
				],
			]
		);

        $this->add_control(
			'ekit_flip_box_back_button_icon_spacing',
			[
				'label' => esc_html__('Spacing (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
                'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-back-button > .flip-box-back-button-icon-after' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-back-button > .flip-box-back-button-icon-before' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-wid-con .ekit-flip-box-back-button > svg' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}}; position:relative; top: 4px;',
				],
                'condition' => [
                    'ekit_flip_box_back_icons[value]!' => '',
				],
			]
		);

        $this->add_control(
			'ekit_flip_box_back_button_icon_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-back-button > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-back-button > svg' => 'fill: {{VALUE}}',
				],
                'condition' => [
                    'ekit_flip_box_back_icons[value]!' => '',
				],
			]
		);

		$this->add_control(
			'ekit_flip_box_back_button_icon_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-back-button:hover > i' => 'color: {{VALUE}}; transition: .3s;',
					'{{WRAPPER}} .ekit-wid-con .ekit-flip-box-back-button:hover > svg' => 'fill: {{VALUE}}; transition: .3s;',
				],
                'condition' => [
                    'ekit_flip_box_back_icons[value]!' => '',
				],
			]
		);

        $this->end_controls_section();

        $this->insert_pro_message();
    }

    protected function render() {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw() {
        $settings = $this->get_settings_for_display();
        extract($settings);

        $options_ekit_flip_title_tag = array_keys([
			'h1' => 'H1',
			'h2' => 'H2',
			'h3' => 'H3',
			'h4' => 'H4',
			'h5' => 'H5',
			'h6' => 'H6',
			'div' => 'div',
			'span' => 'span',
			'p' => 'p',
		]);

        if ( ! empty( $settings['ekit_flip_back_button_url']['url'] ) ) {
			$this->add_link_attributes( 'ekit_flip_back_button_url', $settings['ekit_flip_back_button_url'] );
		}

		$ekit_flip_title_tag = \ElementsKit_Lite\Utils::esc_options($ekit_flip_title_tag, $options_ekit_flip_title_tag, 'h2');
        $ekit_flip_back_title_tag = \ElementsKit_Lite\Utils::esc_options($ekit_flip_back_title_tag, $options_ekit_flip_title_tag, 'h2');

        $flip_class='';
        switch ($ekit_flip_box_style) {
            case "flip":
                switch($ekit_flip_box_direction) {
                    case 'left_to_right':
                        $flip_class = "flip_style left_to_right";
                        break;
                    case 'right_to_left':
                        $flip_class = "flip_style right_to_left";
                        break;
                    case 'top_to_bottom':
                        $flip_class = "flip_style top_to_bottom";
                        break;
                    case 'bottom_to_top':
                        $flip_class = "flip_style bottom_to_top";
                        break;
                }
                break;
            case "slide":
                switch($ekit_flip_box_direction) {
                    case 'left_to_right':
                        $flip_class = "slide_style left_to_right";
                        break;
                    case 'right_to_left':
                        $flip_class = "slide_style right_to_left";
                        break;
                    case 'top_to_bottom':
                        $flip_class = "slide_style top_to_bottom";
                        break;
                    case 'bottom_to_top':
                        $flip_class = "slide_style bottom_to_top";
                        break;
                }
              break;
            case "3d":
                switch($ekit_flip_box_direction) {
                    case 'left_to_right':
                        $flip_class = "style_3d left_to_right";
                        break;
                    case 'right_to_left':
                        $flip_class = "style_3d right_to_left";
                        break;
                    case 'top_to_bottom':
                        $flip_class = "style_3d top_to_bottom";
                        break;
                    case 'bottom_to_top':
                        $flip_class = "style_3d bottom_to_top";
                        break;
                }
              break;
            case "push":
            switch($ekit_flip_box_direction) {
                case 'left_to_right':
                    $flip_class = "push_style left_to_right";
                    break;
                case 'right_to_left':
                    $flip_class = "push_style right_to_left";
                    break;
                case 'top_to_bottom':
                    $flip_class = "push_style top_to_bottom";
                    break;
                case 'bottom_to_top':
                    $flip_class = "push_style bottom_to_top";
                    break;
            }
            break;
            case "transform":
            switch($ekit_flip_box_direction) {
                case 'left_to_right':
                    $flip_class = "transform_style left_to_right";
                    break;
                case 'right_to_left':
                    $flip_class = "transform_style right_to_left";
                    break;
                case 'top_to_bottom':
                    $flip_class = "transform_style top_to_bottom";
                    break;
                case 'bottom_to_top':
                    $flip_class = "transform_style bottom_to_top";
                    break;
            }
            break;
            case "zoom":
                switch($ekit_flip_box_zoom_direction) {
                    case 'zoom_up':
                        $flip_class = "zoom_style zoom_up";
                        break;
                    case 'zoom_out':
                        $flip_class = "zoom_style zoom_out";
                        break;
                }
                break;
            case "onclick":
                switch($ekit_flip_box_direction) {
                    case 'left_to_right':
                        $flip_class = "style_onclick left_to_right";
                        break;
                    case 'right_to_left':
                        $flip_class = "style_onclick right_to_left";
                        break;
                    case 'top_to_bottom':
                        $flip_class = "style_onclick top_to_bottom";
                        break;
                    case 'bottom_to_top':
                        $flip_class = "style_onclick bottom_to_top";
                        break;
                }
                break;
            default:
            $flip_class = "";
        }

        $flip_tigger_class='';
        switch ($ekit_flip_box_style_trigger) {
            case "box_hover":
                $flip_tigger_class = " box_hover";
                break;
            case "box_click":
                $flip_tigger_class = " box_click";
                break;
            case "button_click":
                $flip_tigger_class = " button_click";
                break;
            default:
            $flip_tigger_class = "";
        }

        if($ekit_flip_box_style == 'fade'){
            $extra = 'fade_style';
        }else{
            $extra = '';
        }

        ?>
        <div class="ekit-flip-box <?php echo esc_attr($flip_class.$flip_tigger_class.$extra); ?>">
            <div class="ekit-flip-box-inner">
                <div class="ekit-flip-box-front">
                    <div class="ekit-flip-box-inner-wrap">
                        <?php if(!empty($ekit_flip_icon)):?>
                            <div class="ekit-icon-wrapper">
                                <?php Icons_Manager::render_icon( $settings['ekit_flip_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                            </div>
                        <?php endif;
                        if(!empty($ekit_flip_front_image['url'])): ?>
                            <div class="ekit-flip-box-top-image"> 
                                <?php echo wp_kses(Group_Control_Image_Size::get_attachment_image_html($settings, 'ekit_flip_front_thumbnail', 'ekit_flip_front_image'), \ElementsKit_Lite\Utils::get_kses_array()); ?>
                            </div>
                        <?php endif;
                        if( !empty($ekit_flip_title)): ?>
                            <?php echo sprintf('<%1$s class="ekit-flip-box-front-title">%2$s</%1$s>', esc_html($ekit_flip_title_tag), esc_html($ekit_flip_title)); ?>
                        <?php endif;
                        if( !empty($ekit_flip_sub_title)): ?>
                            <h5 class="ekit-flip-box-front-sub-title"><?php echo esc_html($ekit_flip_sub_title);?></h5>
                        <?php endif;
                        if( !empty($ekit_flip_front_description)): ?>
                            <p class="ekit-flip-box-front-description"><?php echo wp_kses($ekit_flip_front_description, \ElementsKit_Lite\Utils::get_kses_array()); ?></p>
                        <?php endif;
                        if($ekit_flip_box_style_trigger === 'button_click' &&  !empty($ekit_flip_front_button_text)): ?>
                            <a href="javascript:void(0)" class="ekit-flip-box-front-button">
                                <?php
                                    $ekit_flip_box_icon_position == 'before' && Icons_Manager::render_icon( $settings['ekit_flip_box_icon_select'], [ 'aria-hidden' => 'true', 'class' => 'flip-box-button-icon-before']);	
                                        echo esc_html($ekit_flip_front_button_text);	 
                                    $ekit_flip_box_icon_position == 'after' &&  Icons_Manager::render_icon( $settings['ekit_flip_box_icon_select'], [ 'aria-hidden' => 'true', 'class' => 'flip-box-button-icon-after' ]);
                                ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="ekit-flip-box-back">
                    <div class="ekit-flip-box-inner-wrap wrap-back">
                        <div class="ekit-flip-box-back-wrap">
                            <?php if(!empty($ekit_flip_back_icon)):?>
                                <div class="ekit-back-icon-wrapper">
                                    <?php Icons_Manager::render_icon( $settings['ekit_flip_back_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                                </div>
                            <?php endif;
                            if(!empty($ekit_flip_back_image['url'])): ?>
                                <div class="ekit-flip-box-back-image">
                                    <?php echo wp_kses(Group_Control_Image_Size::get_attachment_image_html($settings, 'ekit_flip_back_thumbnail', 'ekit_flip_back_image'), \ElementsKit_Lite\Utils::get_kses_array()); ?>
                                </div>
                            <?php endif;
                            if( !empty($ekit_flip_back_title)): ?>
                                <?php echo sprintf('<%1$s class="ekit-flip-box-back-title">%2$s</%1$s>', esc_html($ekit_flip_back_title_tag), esc_html($ekit_flip_back_title)); ?>
                            <?php endif;
                            if( !empty($ekit_flip_back_sub_title)): ?>
                                <h5 class="ekit-flip-box-back-sub-title"><?php echo esc_html($ekit_flip_back_sub_title);?></h5>
                            <?php endif;
                            if( !empty($ekit_flip_back_description)): ?>
                                <p class="ekit-flip-box-back-description"><?php echo wp_kses($ekit_flip_back_description, \ElementsKit_Lite\Utils::get_kses_array()); ?></p>
                            <?php endif;
                            if($ekit_flip_back_button_text): ?>
                                <a <?php $this->print_render_attribute_string( 'ekit_flip_back_button_url' );?> class="ekit-flip-box-back-button">
                                    <?php
                                        $ekit_flip_box_back_icon_position == 'before' && Icons_Manager::render_icon( $settings['ekit_flip_box_back_icons'], [ 'aria-hidden' => 'true', 'class' => 'flip-box-back-button-icon-before' ]);	
                                            echo esc_html($ekit_flip_back_button_text);	 
                                        $ekit_flip_box_back_icon_position == 'after' && Icons_Manager::render_icon( $settings['ekit_flip_box_back_icons'], [ 'aria-hidden' => 'true', 'class' => 'flip-box-back-button-icon-after' ]);
                                    ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
