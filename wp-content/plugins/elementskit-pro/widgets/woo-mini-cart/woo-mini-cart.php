<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Woo_Mini_Cart_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Woo_Mini_Cart extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

	public $base;

    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
        
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
        return 'https://wpmet.com/doc/woocommerce-mini-cart/';
    }

    protected function register_controls() {

        $this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementskit' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_woo_mini_cart_icons',
			[
				'label' => esc_html__( 'Icon', 'elementskit' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_woo_mini_cart_icon',
                'default' => [
                    'value' => 'icon icon-cart2',
                    'library' => 'ekiticons',
                ],
			]
        );
        
        $this->add_control(
            'ekit_woo_mini_cart_text',
            [
                'label' => esc_html__( 'Cart Text', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => esc_html__( 'Cart', 'elementskit' ),
                'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'ekit_woo_mini_cart_visibility',
            [
                'label' => esc_html__( 'Cart Visibility', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'click',
				'options' => [
					'click'     => esc_html__( 'Click', 'elementskit' ),
					'hover'     => esc_html__( 'Hover', 'elementskit' ),
                    'off_canvas' => esc_html__( 'Off-Canvas', 'elementskit' ),
				],

            ]
        );

        $this->add_responsive_control(
            'ekit_woo_mini_cart_alignment',
            [
                'label' =>esc_html__( 'Alignment', 'elementskit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left'    => [
                        'title' =>esc_html__( 'Left', 'elementskit' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' =>esc_html__( 'Center', 'elementskit' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' =>esc_html__( 'Right', 'elementskit' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-mini-cart' => 'text-align: {{VALUE}};'
                ],
                'default' => 'left',
            ]
        );

        $this->end_controls_section();

        // menu btn
        $this->start_controls_section(
			'ekit_mini_cart_menu_button_section',
			[
				'label' => esc_html__( 'Cart Button', 'elementskit' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
        );

        $this->add_responsive_control(
            'ekit_mini_cart_menu_button_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-dropdown-back' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_mini_cart_menu_icon_size',
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
                    '{{WRAPPER}} .ekit-dropdown-back i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-dropdown-back svg' => 'max-width: {{SIZE}}{{UNIT}}; height: auto',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_mini_cart_menu_button_typo',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-dropdown-back',
			]
        );
        
        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_mini_cart_menu_button_txt_shadow',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-dropdown-back',
            ]
        );
        
        $this->add_responsive_control(
			'ekit_mini_cart_menu_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-dropdown-back' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
        );
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_mini_cart_menu_button_shadow',
				'selector' => '{{WRAPPER}} .ekit-dropdown-back',
			]
		);

        $this->start_controls_tabs('ekit_mini_cart_menu_button_color_tabs');
            $this->start_controls_tab(
                'ekit_mini_cart_menu_button_color_normal_tab',
                [
                    'label' => esc_html__('Normal', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_mini_cart_menu_button_normal_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-dropdown-back' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .ekit-dropdown-back .amount' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .ekit-dropdown-back svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                array(
                    'name'     => 'ekit_mini_cart_menu_button_normal_bg_color',
                    'selector' => '{{WRAPPER}} .ekit-dropdown-back',
                )
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'      => 'ekit_mini_cart_menu_button_border',
                    'label'     => esc_html__( 'Border', 'elementskit' ),
                    'selector'  => '{{WRAPPER}} .ekit-dropdown-back',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_mini_cart_menu_button_color_hover_tab',
                [
                    'label' => esc_html__('Hover', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_mini_cart_menu_button_hover_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-dropdown-back:hover' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .ekit-dropdown-back:hover svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                array(
                    'name'     => 'ekit_mini_cart_menu_button_hover_bg_color',
                    'selector' => '{{WRAPPER}} .ekit-dropdown-back:hover',
                )
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'      => 'ekit_mini_cart_menu_hover_button_border',
                    'label'     => esc_html__( 'Border', 'elementskit' ),
                    'selector'  => '{{WRAPPER}} .ekit-dropdown-back:hover',
                ]
            );

            $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
        // end menu btn


        $this->start_controls_section(
			'ekit_mini_cart_body_section',
			[
				'label' => esc_html__( 'Body', 'elementskit' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
        );

		$this->add_responsive_control(
			'body_alignment',
			[
				'label'     => __( 'Alignment', 'elementskit' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'right' => [
						'title' => __( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'left' => [
						'title' => __( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'left', // Visually 'Right' is the default, where css property is 'left'
				'selectors' => [
					'{{WRAPPER}} .ekit-mini-cart-container' => '{{VALUE}}: 0;',
				],
                'prefix_class'  => 'ekit-mini-cart--pos-',
			]
		);

        $this->add_responsive_control(
            'ekit_mini_cart_body_width',
            [
                'label' => esc_html__( 'Width', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-mini-cart-container' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
			'ekit_mini_cart_body_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_control(
			'ekit_mini_cart_body_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-mini-cart-visibility-hover:before' => 'min-height: {{TOP}}{{UNIT}};',
				],
			]
        );

        $this->add_control(
            'ekit_mini_cart_body_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .ekit-mini-cart-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'ekit_mini_cart_body_border',
                'label'       => esc_html__( 'Border', 'elementskit' ),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .ekit-mini-cart-container',
            ]
        );

        $this->add_responsive_control(
			'ekit_mini_cart_body_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_mini_cart_body_border_shadow',
				'selector' => '{{WRAPPER}} .ekit-mini-cart-container',
			]
		);

            $this->add_control(
                'ekit_mini_cart_overlay',
                [
                    'label'     => esc_html__( 'Overlay Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'separator' => 'before',
                    'default'   => '#101010c7',
                    'selectors' => [
                        '{{WRAPPER}} .ekit-mini-cart--backdrop' => 'background-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'ekit_woo_mini_cart_visibility' => 'off_canvas',
                    ],
                ]
            );
        
        $this->end_controls_section();


        $this->start_controls_section(
			'ekit_mini_cart_header_section',
			[
				'label' => esc_html__( 'Header', 'elementskit' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
        );

        $this->add_control(
			'ekit_mini_cart_header_content_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container .mini-cart-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_control(
			'ekit_mini_cart_header_content_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container .mini-cart-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_mini_cart_header_content_typo',
                'selector'	 => '{{WRAPPER}} .ekit-mini-cart-container .mini-cart-header ul li, {{WRAPPER}} .ekit-mini-cart-container .mini-cart-header ul li a',
            ]
        );

        $this->start_controls_tabs('ekit_mini_cart_header_content_color_tabs');
            $this->start_controls_tab(
                'ekit_mini_cart_header_content_normal_color_tab',
                [
                    'label' => esc_html__('Normal', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_mini_cart_header_content_normal_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#465157',
                    'selectors'	 => [
                        '{{WRAPPER}} .ekit-mini-cart-container .mini-cart-header ul li, {{WRAPPER}} .ekit-mini-cart-container .mini-cart-header ul li a' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_mini_cart_header_content_hover_color_tab',
                [
                    'label' => esc_html__('Hover', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_mini_cart_header_content_hover_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#465157',
                    'selectors'	 => [
                        '{{WRAPPER}} .ekit-mini-cart-container .mini-cart-header ul li:hover, {{WRAPPER}} .ekit-mini-cart-container .mini-cart-header ul li:hover a' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
			'ekit_mini_cart_item_section',
			[
				'label' => esc_html__( 'Item', 'elementskit' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
        );
        
        $this->add_control(
            'ekit_mini_cart_item_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'elementskit' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#E6EBEE',
                'selectors'	 => [
                    '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_mini_cart_item_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	 => [
					'top'		=> 15,
					'right'		=> 10,
					'bottom'	=> 15,
					'left'		=> 10
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'ekit_mini_cart_item_border_width',
            [
                'label' => esc_html__( 'Border Width', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'ekit_mini_cart_item_title_heading',
            [
                'label' => esc_html__( 'Title:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_mini_cart_item_title_typo',
                'selector'	 => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a:not(.remove)',
            ]
        );

        $this->start_controls_tabs('ekit_mini_cart_item_title_color_tabs');
            $this->start_controls_tab(
                'ekit_mini_cart_item_title_color_normal_tab',
                [
                    'label' => esc_html__( 'Normal', 'elementskit' )
                ]
            );

            $this->add_control(
                'ekit_mini_cart_item_title_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#465157',
                    'selectors'	 => [
                        '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a:not(.remove)' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_mini_cart_item_title_color_hover_tab',
                [
                    'label' => esc_html__( 'Hover', 'elementskit' )
                ]
            );

            $this->add_control(
                'ekit_mini_cart_item_title_hover_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#465157',
                    'selectors'	 => [
                        '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li:hover a:not(.remove)' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();
        $this->end_controls_tabs();

        
        
        $this->add_responsive_control(
            'ekit_mini_cart_item_quantity_heading',
            [
                'label' => esc_html__( 'Quantity:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_mini_cart_item_quantity_typo',
                'selector'	 => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li .quantity',
            ]
        );

        $this->start_controls_tabs('ekit_mini_cart_item_quantity_color_tabs');
            $this->start_controls_tab(
                'ekit_mini_cart_item_quantity_color_normal_tab',
                [
                    'label' => esc_html__( 'Normal', 'elementskit' )
                ]
            );

            $this->add_control(
                'ekit_mini_cart_item_quantity_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#737373',
                    'selectors'	 => [
                        '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li .quantity' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_mini_cart_item_quantity_color_hover_tab',
                [
                    'label' => esc_html__( 'Hover', 'elementskit' )
                ]
            );

            $this->add_control(
                'ekit_mini_cart_item_quantity_hover_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#465157',
                    'selectors'	 => [
                        '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li:hover .quantity' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'ekit_mini_cart_item_image_heading',
            [
                'label' => esc_html__( 'Image:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_mini_cart_item_image_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'selector'    => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a > img',
			]
		);

        $this->add_control(
			'ekit_mini_cart_item_image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	 => [
					'top'		=> 0,
					'right'		=> 0,
					'bottom'	=> 0,
					'left'		=> 0
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a > img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_mini_cart_item_image_shadow',
				'selector' => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a > img',
			]
        );
        
        $this->add_responsive_control(
            'ekit_mini_cart_item_remove_heading',
            [
                'label' => esc_html__( 'Remove:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_mini_cart_item_remove_typo',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove',
			]
        );
        
        $this->add_control(
			'ekit_mini_cart_item_remove_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
        );
        
        $this->add_control(
			'ekit_mini_cart_item_remove_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
        );
        
        $this->add_control(
			'ekit_mini_cart_item_remove_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	 => [
					'top'		=> 100,
					'right'		=> 100,
					'bottom'	=> 100,
					'left'		=> 100
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_mini_cart_item_remove_shadow',
				'selector' => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove',
			]
        );
        
        $this->add_control(
			'ekit_mini_cart_item_remove_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->add_control(
			'ekit_mini_cart_item_remove_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->start_controls_tabs('ekit_mini_cart_item_remove_color_tabs');
            $this->start_controls_tab(
                'ekit_mini_cart_item_remove_color_normal_tab',
                [
                    'label' => esc_html__( 'Normal', 'elementskit' )
                ]
            );

            $this->add_control(
                'ekit_mini_cart_item_remove_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#737373',
                    'selectors'	 => [
                        '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ekit_mini_cart_item_remove_background_color',
                [
                    'label'     => esc_html__( 'Background Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'default'   => '#fff',
                    'selectors' => [
                        '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'        => 'ekit_mini_cart_item_remove_border',
                    'label'       => esc_html__( 'Border', 'elementskit' ),
                    'placeholder' => '1px',
                    'default'     => '1px',
                    'selector'    => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_mini_cart_item_remove_color_hover_tab',
                [
                    'label' => esc_html__( 'Hover', 'elementskit' )
                ]
            );

            $this->add_control(
                'ekit_mini_cart_item_remove_hover_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#465157',
                    'selectors'	 => [
                        '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ekit_mini_cart_item_remove_hover_background_color',
                [
                    'label'     => esc_html__( 'Background Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'default'   => '#fff',
                    'selectors' => [
                        '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'        => 'ekit_mini_cart_item_remove_hover_border',
                    'label'       => esc_html__( 'Border', 'elementskit' ),
                    'placeholder' => '1px',
                    'default'     => '1px',
                    'selector'    => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart ul li a.remove:hover',
                ]
            );

            $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
			'ekit_mini_cart_sub_total_section',
			[
				'label' => esc_html__( 'Subtotal', 'elementskit' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
        );

        $this->add_control(
			'ekit_mini_cart_subtotal_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	 => [
					'top'		=> 15,
					'right'		=> 0,
					'bottom'	=> 15,
					'left'		=> 0
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__total' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->add_control(
			'ekit_mini_cart_subtotal_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__total' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


        $this->add_responsive_control(
            'ekit_mini_cart_subtotal_title_heading',
            [
                'label' => esc_html__( 'Title:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_mini_cart_subtotal_title_typo',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__total strong',
			]
        );

        $this->add_control(
            'ekit_mini_cart_subtotal_title_color',
            [
                'label'     => esc_html__( 'Color', 'elementskit' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#465157',
                'selectors'	 => [
                    '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__total strong' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_mini_cart_subtotal_price_heading',
            [
                'label' => esc_html__( 'Price:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_mini_cart_subtotal_price_typo',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__total .amount',
			]
        );

        $this->add_control(
            'ekit_mini_cart_subtotal_price_color',
            [
                'label'     => esc_html__( 'Color', 'elementskit' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#465157',
                'selectors'	 => [
                    '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__total .amount' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();


        $this->start_controls_section(
			'ekit_mini_cart_button_section',
			[
				'label' => esc_html__( 'Button', 'elementskit' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
        );

        $this->add_responsive_control(
            'ekit_mini_cart_button_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__buttons .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_mini_cart_button_typo',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__buttons .button',
			]
        );
        
        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_mini_cart_button_txt_shadow',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__buttons .button',
            ]
        );
        
        $this->add_responsive_control(
			'ekit_mini_cart_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__buttons .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
        );
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_mini_cart_button_shadow',
				'selector' => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__buttons .button',
			]
		);

        $this->start_controls_tabs('ekit_mini_cart_button_color_tabs');
            $this->start_controls_tab(
                'ekit_mini_cart_button_color_normal_tab',
                [
                    'label' => esc_html__('Normal', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_mini_cart_button_normal_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__buttons .button' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                array(
                    'name'     => 'ekit_mini_cart_button_normal_bg_color',
                    'selector' => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__buttons .button',
                )
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'      => 'ekit_mini_cart_button_border',
                    'label'     => esc_html__( 'Border', 'elementskit' ),
                    'selector'  => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__buttons .button',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_mini_cart_button_color_hover_tab',
                [
                    'label' => esc_html__('Hover', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_mini_cart_button_hover_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__buttons .button:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                array(
                    'name'     => 'ekit_mini_cart_button_hover_bg_color',
                    'selector' => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__buttons .button:hover',
                )
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'      => 'ekit_mini_cart_hover_button_border',
                    'label'     => esc_html__( 'Border', 'elementskit' ),
                    'selector'  => '{{WRAPPER}} .ekit-mini-cart-container .ekit-dropdown-menu-mini-cart .woocommerce-mini-cart__buttons .button:hover',
                ]
            );

            $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
        
        $this->insert_pro_message();
    }

    protected function render() {
        if(class_exists( 'WooCommerce' )) {
            echo '<div class="ekit-wid-con" >';
                $this->render_raw();
            echo '</div>';
        }
    }
    
    protected function render_raw(){

        $settings = $this->get_settings();
        ?>

        <div class="ekit-mini-cart">

            <div class="ekit-dropdown-back ekit-mini-cart-visibility-<?php echo esc_attr( $settings['ekit_woo_mini_cart_visibility'] ); ?>" data-toggle="mini-cart-dropdown">
                <?php
                    // new icon
                    $migrated = isset( $settings['__fa4_migrated']['ekit_woo_mini_cart_icons'] );
                    // Check if its a new widget without previously selected icon using the old Icon control
                    $is_new = empty( $settings['ekit_woo_mini_cart_icon'] );
                    if ( $is_new || $migrated ) {
                        // new icon
                        Icons_Manager::render_icon( $settings['ekit_woo_mini_cart_icons'], [ 'aria-hidden' => 'true' ] );
                    } else {
                        ?>
                        <i class="<?php echo esc_attr($settings['ekit_woo_mini_cart_icon']); ?>" aria-hidden="true"></i>
                        <?php
                    }

                    if(!empty($settings['ekit_woo_mini_cart_text'])){ ?>
                        <span class="ekit-mini-cart-text"><?php echo esc_html( $settings['ekit_woo_mini_cart_text'] ); ?></span>
                <?php }
                ?>   
                
                <div class="ekit-basket-item-count" style="display: inline;">
                    <span class="ekit-cart-items-count count">
                        <?php
                            echo (( WC()->cart != '' ) ? '<span class="ekit-cart-content-count">'. WC()->cart->get_cart_contents_count() .'</span>' : '' );
                            echo (( WC()->cart != '' ) ? '<span class="ekit-cart-content-separator"> - </span>' . WC()->cart->get_cart_total() : '' );
                        ?>
                    </span>
                </div>

                <div class="ekit-mini-cart-container">
                    <div class="mini-cart-header">
                        <ul>
                            <li><span class="ekit-cart-count"><?php echo (( WC()->cart != '' ) )?  WC()->cart->get_cart_contents_count() : '' ; ?></span> <?php esc_html_e( 'items', 'elementskit' ); ?></li>
                            <li><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'view cart', 'elementskit' ); ?></a></li>
                        </ul>
                    </div>
                    <div class="ekit-dropdown-menu ekit-dropdown-menu-mini-cart">
                        <div class="widget_shopping_cart_content">
                            <?php (( WC()->cart != '' ) ? woocommerce_mini_cart() : '' ); ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ( $settings['ekit_woo_mini_cart_visibility'] === 'off_canvas' ): ?>
                <div class="ekit-mini-cart--backdrop"></div>
            <?php endif; ?>
        </div>
        
    <?php


    }

}
