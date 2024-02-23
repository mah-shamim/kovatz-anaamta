<?php

namespace Elementor;

use \Elementor\ElementsKit_Widget_Whatsapp_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;
use ElementsKit_Lite;

if (!defined('ABSPATH')) exit;

class ElementsKit_Widget_Whatsapp extends Widget_Base
{
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
		return 'https://wpmet.com/doc/get-whatsapp-button-on-website-elementskit/';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'user_tab_section',
			[
				'label' => esc_html__('Header', 'elementskit'),
			]
		);

		$this->add_control(
			'whatsapp_user_image',
			[
				'label' => esc_html__('Choose Profile Photo', 'elementskit'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Handler::get_url().'assets/images/whatsapp_user.png',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'whatsapp_username',
			[
				'label' => esc_html__('Username', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('John Doe', 'elementskit'),
				'placeholder' => esc_html__('Type your title here', 'elementskit'),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'whatsapp_user_text',
			[
				'label' => esc_html__('User Text', 'elementskit'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__('Typically replies within a day', 'elementskit'),
				'placeholder' => esc_html__('Type your text here', 'elementskit'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'ekit_whatsapp_active',
			[
				'label' => esc_html__( 'Enable Active Dot', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
                'return_value' => '1',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__header--img:after' => 'opacity: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_whatsapp_active_custom',
			[
				'label' => esc_html__( 'Enable Custome Active Time', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
				'condition' => [
					'ekit_whatsapp_active' => '1'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_active_start_time',
			[
				'label' => esc_html__( 'Start Time', 'elementskit' ),
				'type' => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'noCalendar'=> true
				],
				'condition' => [
					'ekit_whatsapp_active_custom' => 'yes',
					'ekit_whatsapp_active' => '1'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_active_end_time',
			[
				'label' => esc_html__( 'End Time', 'elementskit' ),
				'type' => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'noCalendar'=> true
				],
				'condition' => [
					'ekit_whatsapp_active_custom' => 'yes',
					'ekit_whatsapp_active' => '1'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_active_holidays',
			[
				'label' => esc_html__( 'Choose Holidays', 'elementskit' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'Friday'  => esc_html__( 'Friday', 'elementskit' ),
					'Saturday' => esc_html__( 'Saturday', 'elementskit' ),
					'Sunday' => esc_html__( 'Sunday', 'elementskit' ),
					'Monday' => esc_html__( 'Monday', 'elementskit' ),
					'Tuesday' => esc_html__( 'Tuesday', 'elementskit' ),
					'Wednesday' => esc_html__( 'Wednesday', 'elementskit' ),
					'Thursday' => esc_html__( 'Thursday', 'elementskit' ),
				],
				'condition' => [
					'ekit_whatsapp_active_custom' => 'yes',
					'ekit_whatsapp_active' => '1'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_vacation_text',
			[
				'label' => esc_html__('Enter Vacation Message', 'elementskit'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__('We are closed for vacation now', 'elementskit'),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ekit_whatsapp_active_custom' => 'yes',
					'ekit_whatsapp_active' => '1'
				]
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'body_tab_section',
			[
				'label' => esc_html__('Body', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_whatsapp_body_loader',
			[
				'label' => esc_html__( 'Enable Loader?', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'ekit_whatsapp_body_username',
			[
				'label' => esc_html__( 'Show Username?', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'whatsapp_asking_text',
			[
				'label' => esc_html__('Asking Text', 'elementskit'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__('Hey, Do you want to talk with us?', 'elementskit'),
				'placeholder' => esc_html__('Type your text here', 'elementskit'),
				'dynamic' => [
					'active' => true,
				],
			]
		);	

		$this->add_control(
			'ekit_whatsapp_btn_position_toggle',
			[
				'label' => esc_html__( 'Position', 'elementskit' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => esc_html__( 'Default', 'elementskit' ),
				'label_on' => esc_html__( 'Custom', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_control(
			'ekit_whatsapp_btn_direction',
			[
				'label' => esc_html__( 'Direction', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__( 'Left', 'elementskit' ),
					'right' => esc_html__( 'Right', 'elementskit' ),
				],
			]
		);

		$this->add_control(
			'ekit_whatsapp_btn_direction_verticle',
			[
				'label' => esc_html__( 'Vertical Position (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'custom' ],
				'default' => [
					'unit' => 'px',
					'min' => -1000,
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__content' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_whatsapp_btn_direction_horizontal',
			[
				'label' => esc_html__( 'Horizontal Position (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'custom' ],
				'default' => [
					'unit' => 'px',
					'min' => -1000,
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__content' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__content' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				],
			]
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'footer_tab_section',
			[
				'label' => esc_html__('Footer', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_whatsapp_footer_style',
			[
				'label' => esc_html__( 'Choose Style', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'input',
				'options' => [
					'input'  => esc_html__( 'Input', 'elementskit' ),
					'button' => esc_html__( 'Button', 'elementskit' ),
					'inner-input' => esc_html__( 'Inner Input', 'elementskit' ),
				],
			]
		);

		$this->add_control(
            'ekit_whatsapp_footer_btn_icon',
            [
                'label' => esc_html__( 'Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'default' => [
					'value' => 'fab fa-whatsapp',
					'library' => 'fa-brands',
				],
                'label_block' => true,
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
            ]
        );

		$this->add_control(
			'ekit_whatsapp_input_footer_btn_text',
			[
				'label' => esc_html__('Text', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Start Chat', 'elementskit'),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
			]
		);

		$this->add_control(
			'whatsapp_input_placeholder',
			[
				'label' => esc_html__('Input Placeholder', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Write Something', 'elementskit'),
				'placeholder' => esc_html__('Type your text here', 'elementskit'),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ekit_whatsapp_footer_style!' => 'button'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_footer_link_target',
			[
				'label' => esc_html__( 'Open Link Option', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => '_self',
				'options' => [
					'_self'  => esc_html__( 'Same Page', 'elementskit' ),
					'_blank' => esc_html__( 'New Tab', 'elementskit' ),
					'popup' => esc_html__( 'Popup', 'elementskit' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_tab_section',
			[
				'label' => esc_html__('Button', 'elementskit'),
			]
		);

		$this->add_control(
            'ekit_whatsapp_style',
            [
                'label' => esc_html__('Choose Style', 'elementskit'),
                'type' => ElementsKit_Controls_Manager::IMAGECHOOSE,
                'default' => 'icon',
                'options' => [
                    'icon' => [
                        'title' => esc_html__( 'Icon', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/only-icon.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/only-icon.png',
                        'width' => '50%',
                    ],
                    'icon_with_text' => [
                        'title' => esc_html__( 'Icon With Text', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/icon-with-text.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/icon-with-text.png',
                        'width' => '50%',
                    ],
                    'icon_separate_text' => [
                        'title' => esc_html__( 'Icon Separate Text', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/icon-separate-text.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/icon-separate-text.png',
                        'width' => '50%',
					],
                    'photo_with_text' => [
                        'title' => esc_html__( 'Icon Separate Text', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/photo-with-text.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/photo-with-text.png',
                        'width' => '50%',
					],
                ]
            ]
        );

		$this->add_control(
            'ekit_whatsapp_btn_icon',
            [
                'label' => esc_html__( 'Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
                'label_block' => true,
				'condition' => [
					'ekit_whatsapp_style!' => 'photo_with_text'
				]
            ]
        );

		$this->add_control(
			'ekit_whatsapp_btn_text',
			[
				'label' => esc_html__('Text', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'label_block' => 'true',
				'default' => 'Contact us',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ekit_whatsapp_style!' => 'icon'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_btn_subtext',
			[
				'label' => esc_html__('Subtext', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'label_block' => 'true',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ekit_whatsapp_style' => 'photo_with_text'
				]
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'settings_tab_section',
			[
				'label' => esc_html__('Settings', 'elementskit'),
			]
		);

		$this->add_control(
			'whatsapp_number',
			[
				'label' => esc_html__('Whatsapp Number', 'elementskit'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__('+880175555555', 'elementskit'),
				'placeholder' => esc_html__('Type your whatsapp number', 'elementskit'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'show_from_first',
			[
				'label' => esc_html__( 'Show From First', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'whatsapp_btn_style_section',
			[
				'label' => esc_html__( 'Sticky Button', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_whatsapp_sticky_btn_width',
			[
				'label' => esc_html__( 'Button Width (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp .elementskit-whatsapp__popup--btn' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_whatsapp_sticky_btn_height',
			[
				'label' => esc_html__( 'Button Height (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp .elementskit-whatsapp__popup--btn' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_border_radius',
			[
				'label' => esc_html__('Border Radius (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],	
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__popup--btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_bg',
			[
				'label' => esc_html__( 'Button Background', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__popup--btn' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'ekit_whatsapp_sticky_btn_box_shadow',
                'selector'  => '{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__popup--btn',
            ]
        );		

		$this->add_control(
			'ekit_whatsapp_sticky_btn_padding',
			[
				'label' => esc_html__('Padding (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],	
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__popup--btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_icon_heading',
			[
				'label' => esc_html__( 'Icon', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_whatsapp_style!' => 'photo_with_text'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .whatsapp-rotate-icon path' => 'stroke: {{VALUE}}',
					'{{WRAPPER}} .elementskit-whatsapp__popup--btn i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_whatsapp_style!' => 'photo_with_text'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_icon_background',
			[
				'label' => esc_html__( 'Icon Background', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#54CC61',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__popup--btn.icon_separate_text .elementskit-whatsapp__popup--btn-icon' => 'background: {{VALUE}}',
				],
				'condition' => [
					'ekit_whatsapp_style' => 'icon_separate_text'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_whatsapp_sticky_btn_icon_size',
			[
				'label' => esc_html__( 'Icon Size (px)', 'elementskit' ),
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
					'size' => 26,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp .elementskit-whatsapp__popup--btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-whatsapp .elementskit-whatsapp__popup--btn i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_whatsapp_style!' => 'photo_with_text'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_icon_padding',
			[
				'label' => esc_html__( 'Icon Padding (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__popup--btn.icon_separate_text .elementskit-whatsapp__popup--btn-icon' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_whatsapp_style' => 'icon_separate_text'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_img_heading',
			[
				'label' => esc_html__( 'Image', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_whatsapp_style' => 'photo_with_text'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_img_size',
			[
				'label' => esc_html__( 'Size (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
				'selectors' => [
					'{{WRAPPER}}  .elementskit-whatsapp__popup--btn.photo_with_text img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_whatsapp_style' => 'photo_with_text'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_text_heading',
			[
				'label' => esc_html__( 'Text', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_whatsapp_style!' => 'icon'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__popup--btn-text' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_whatsapp_style!' => 'icon'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_whatsapp_sticky_btn_text_typography',
				'selector' => '{{WRAPPER}} .elementskit-whatsapp__popup--btn-text',
				'exclude' => ['font_style', 'text_decoration'],
				'condition' => [
					'ekit_whatsapp_style!' => 'icon'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_space',
			[
				'label' => esc_html__( 'Space between (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp .elementskit-whatsapp__popup--btn .elementskit-whatsapp__popup--btn-text' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_whatsapp_style!' => 'icon'
				]
			]
		);
		
		$this->add_control(
			'ekit_whatsapp_sticky_btn_subtext_heading',
			[
				'label' => esc_html__( 'Subtext', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_whatsapp_style' => 'photo_with_text'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_btn_subtext_color',
			[
				'label' => esc_html__( 'Subtext Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__popup--btn.photo_with_text .elementskit-whatsapp__popup--btn-text span:nth-child(2)' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_whatsapp_style' => 'photo_with_text'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_whatsapp_sticky_btn_subtext_typography',
				'selector' => '{{WRAPPER}} .elementskit-whatsapp__popup--btn.photo_with_text .elementskit-whatsapp__popup--btn-text span:nth-child(2)',
				'exclude' => ['font_style', 'text_decoration'],
				'fields_options' => [
					'typography' => [
						'default'	=> 'yes'
					],
					'font_size'		=> [
						'default' => [
							'unit' => 'px',
							'size' => 10,
						],
					],
				],
				'condition' => [
					'ekit_whatsapp_style' => 'photo_with_text'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_whatsapp_sticky_btn_align',
			[
				'label' => esc_html__( 'Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'elementskit' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon' => 'eicon-h-align-center',
					],
					'end' => [
						'title' => esc_html__( 'End', 'elementskit' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'whatsapp_header_style_section',
			[
				'label' => esc_html__( 'Header', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'header_bg',
			[
				'label' => esc_html__( 'Header Background', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__header' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'user_photo_headings',
			[
				'label' => esc_html__( 'User Image Style', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'user_img_border',
				'label' => esc_html__( 'Image Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-whatsapp__header--img img, {{WRAPPER}} .elementskit-whatsapp__popup--btn.photo_with_text img',
			]
		);

		$this->add_control(
			'user_info_headings',
			[
				'label' => esc_html__( 'User Info', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'username_color',
			[
				'label' => esc_html__( 'Username Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__header--name' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'user_text_color',
			[
				'label' => esc_html__( 'User Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__header--text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'username_typography',
				'label' => 'Username Typography',
				'selector' => '{{WRAPPER}} .elementskit-whatsapp__header--name',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'user_text_typography',
				'label' => 'User Text Typography',
				'selector' => '{{WRAPPER}} .elementskit-whatsapp__header--text',
			]
		);

		$this->add_control(
			'ekit_whatsapp_user_dot_headings',
			[
				'label' => esc_html__( 'Active / Inactive Dot', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_whatsapp_active' => '1'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_user_dot_size',
			[
				'label' => esc_html__( 'Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 15,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' =>10,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__header--img:after' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'ekit_whatsapp_active' => '1'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_user_dot_color',
			[
				'label' => esc_html__('Border Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#008069',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__header--img:after' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'ekit_whatsapp_active' => '1'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_user_close_headings',
			[
				'label' => esc_html__( 'Close Icon', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_whatsapp_user_close_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__header--close' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_user_close_hover_color',
			[
				'label' => esc_html__('Hover Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__header--close:hover' => 'color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'whatsapp_body_style_section',
			[
				'label' => esc_html__( 'Body', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_whatsapp_theme_color',
			[
				'label' => esc_html__( 'Theme Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp .elementskit-whatsapp__wrapper' => 'background-color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'asking_text_color',
			[
				'label' => esc_html__( 'Asking Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__chat--title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'asking_text_typography',
				'label' => 'Asking Text Typography',
				'selector' => '{{WRAPPER}} .elementskit-whatsapp__chat--title',
			]
		);

		$this->add_control(
			'ekit_whatsapp_asking_text_username_text_color',
			[
				'label' => esc_html__( 'Useranme Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#999999',
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__chat--title-username' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
				'condition' => [
					'ekit_whatsapp_body_username' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_whatsapp__asking_text_username_typography',
				'label' => 'Username Typography',
				'fields_options' => [
					'typography' => [
						'default'	=> 'yes'
					],
					'font_weight'		=> [
						'default' => '600'
					],
				],
				'selector' => '{{WRAPPER}} .elementskit-whatsapp__chat--title-username',
				'condition' => [
					'ekit_whatsapp_body_username' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'whatsapp_footer_style_section',
			[
				'label' => esc_html__( 'Footer', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_whatsapp_footer_background',
			[
				'label' => esc_html__( 'Background', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__input--wrapper' => 'background: {{VALUE}}',
				],
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
			]
		);

		$this->add_control(
			'input_placeholder_color',
			[
				'label' => esc_html__( 'Input Placeholder Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__input--field::placeholder' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_whatsapp_footer_style!' => 'button'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_placeholder_typography',
				'label' => 'Input Placeholder Typography',
				'selector' => '{{WRAPPER}} .elementskit-whatsapp__input--field::placeholder',
				'condition' => [
					'ekit_whatsapp_footer_style!' => 'button'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_sticky_footer_btn_padding',
			[
				'label' => esc_html__('Padding', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],	
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__input--wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_footer_btn_icon_heading',
			[
				'label' => esc_html__( 'Icon', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				],
			]
		);

		$this->add_control(
			'ekit_whatsapp_footer_btn_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__input--button svg path' => 'stroke: {{VALUE}}',
					'{{WRAPPER}} .elementskit-whatsapp__input--button i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_whatsapp_footer_btn_icon_size',
			[
				'label' => esc_html__( 'Icon Size (px)', 'elementskit' ),
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
					'size' => 18,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp .elementskit-whatsapp__input--button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-whatsapp .elementskit-whatsapp__input--button i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_footer_btn_text_heading',
			[
				'label' => esc_html__( 'Text', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_footer_btn_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__input--button-text' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_whatsapp_footer_btn_text_typography',
				'selector' => '{{WRAPPER}} .elementskit-whatsapp__input--button-text',
				'exclude' => ['font_style', 'text_decoration', 'line_height'],
				'fields_options' => [
					'typography' => [
						'default'	=> 'yes'
					],
					'font_size'		=> [
						'default' => [
							'unit' => 'px',
							'size' => 15,
						],
					],
				],
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_footer_btn_space',
			[
				'label' => esc_html__( 'Space between (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-whatsapp__input--button-text' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_footer_btn_heading',
			[
				'label' => esc_html__( 'Button', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_footer_btn_background',
			[
				'label' => esc_html__( 'Background', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#5CC263',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__input--button' => 'background: {{VALUE}}',
				],
				'condition' => [
					'ekit_whatsapp_footer_style' => 'button'
				]
			]
		);

		$this->add_control(
			'ekit_whatsapp_footer_btn_border_radius',
			[
				'label' => esc_html__( 'Border Radius (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__input--button' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-whatsapp__input.inner-input .elementskit-whatsapp__input--wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_whatsapp_footer_style!' => 'input'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'whatsapp_content_style_section',
			[
				'label' => esc_html__( 'Content Wrapper', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_whatsapp_content_width',
			[
				'label' => esc_html__( 'Width (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 270,
						'max' => 350,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__content' => 'width: {{SIZE}}{{UNIT}}; --ekit-whatsapp-width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'ekit_whatsapp_content_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__content' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp .elementskit-whatsapp__wrapper' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0px 0px;',
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__header' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0px 0px;',
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__input--wrapper' => 'border-radius: 0px 0px {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp .elementskit-whatsapp__wrapper:has(.inner-input)' => 'border-radius:{{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'ekit_whatsapp_content_box_shadow',
                'selector'  => '{{WRAPPER}} .ekit-wid-con .elementskit-whatsapp__content',
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

		$local_time = current_time('H:i');
		$active_dot = '';
		
		if( $ekit_whatsapp_active_custom == 'yes') {
			if(!empty($ekit_whatsapp_active_holidays) && in_array( date("l"), $ekit_whatsapp_active_holidays)){
				$active_dot = 'inactive';
				!empty($ekit_whatsapp_vacation_text) && $whatsapp_asking_text = $ekit_whatsapp_vacation_text;
			} elseif (!($local_time > $ekit_whatsapp_active_start_time && $local_time <= $ekit_whatsapp_active_end_time)) {
				$active_dot = 'inactive';
				!empty($ekit_whatsapp_vacation_text) && $whatsapp_asking_text = $ekit_whatsapp_vacation_text;
			}
		}

		$user_img = $whatsapp_user_image['url'] ?? Handler::get_url().'assets/images/whatsapp_user.png';
		$whatsapp_background = Handler::get_url().'assets/images/bg-whatsapp.png';
		$show_first = $settings['show_from_first'] === 'yes' ? 'show' : 'hide';
		$loader_class = '';
		$footer_class = '';
		?>
		<div class="elementskit-whatsapp elementskit-whatsapp--<?php echo esc_attr($ekit_whatsapp_btn_direction); ?>">
			<div class="elementskit-whatsapp__content" data-show="<?php esc_attr_e($show_first, 'elementskit'); ?>">
				<div class="elementskit-whatsapp__wrapper" style="background-image : url('<?php echo esc_url($whatsapp_background); ?>">
					<div class="elementskit-whatsapp__header">
						<div class="elementskit-whatsapp__header--img <?php echo esc_attr($active_dot) ?>">
							<img src="<?php echo esc_url($user_img); ?>" alt="<?php esc_attr_e($whatsapp_username, 'elementskit'); ?>">
						</div>
						<div class="elementskit-whatsapp__header--content">
							<h4 class="elementskit-whatsapp__header--name"><?php  echo esc_html($whatsapp_username,'elementskit'); ?></h4>
							<p class="elementskit-whatsapp__header--text"><?php  echo esc_html($whatsapp_user_text,'elementskit'); ?></p>
						</div>
						<span class="elementskit-whatsapp__header--close dashicons dashicons-no-alt"></span>
                 	</div>

					<div class="elementskit-whatsapp__body">
						<div class="elementskit-whatsapp__chat">
							<?php if($ekit_whatsapp_body_loader == 'yes') : 
								$loader_class = 'loader-active'; 
								?>
								<div class="ekit-whatsapp-loader">
									<div class="loader-one"></div>
									<div class="loader-two"></div>
									<div class="loader-three"></div>
								</div>
							<?php endif; ?>
							<p class="elementskit-whatsapp__chat--title <?php echo esc_html($loader_class) ?>" data-time="<?php echo esc_attr($local_time) ?>">
								<?php if($ekit_whatsapp_body_username == 'yes') : ?>
									<span class="elementskit-whatsapp__chat--title-username"><?php echo esc_html($whatsapp_username) ?></span>
								<?php endif;
								 echo wp_kses(\ElementsKit_Lite\Utils::kspan($whatsapp_asking_text), \ElementsKit_Lite\Utils::get_kses_array()); ?>
							</p>
						</div>
					</div>

					<?php if($ekit_whatsapp_footer_style == "inner-input") : 
						$footer_class = 'inner-input';
						include Handler::get_dir().'parts/footer.php';
					endif; ?>
				</div>

				<?php if($ekit_whatsapp_footer_style != "inner-input") : 
					include Handler::get_dir().'parts/footer.php';
				endif;?>
			</div>

			<div class="elementskit-whatsapp__popup">
				<button class="elementskit-whatsapp__popup--btn <?php echo esc_attr($ekit_whatsapp_style)  ?>" aria-label="whatsapp" >
					<span class="elementskit-whatsapp__popup--btn-icon">
						<?php if( $ekit_whatsapp_style !=  'photo_with_text' ) : 
							if(!empty($ekit_whatsapp_btn_icon['value'])) : 
								Icons_Manager::render_icon( $ekit_whatsapp_btn_icon, [ 'aria-hidden' => 'true', 'class' => 'whatsapp-rotate-icon' ]);
							else : ?>
								<svg xmlns="http://www.w3.org/2000/svg" class="whatsapp-rotate-icon" width="26" height="26" viewBox="0 0 26 26" fill="none">
									<path d="M7.47533 22.3167C9.10033 23.2917 11.0503 23.8334 13.0003 23.8334C18.9587 23.8334 23.8337 18.9584 23.8337 13.0001C23.8337 7.04175 18.9587 2.16675 13.0003 2.16675C7.04199 2.16675 2.16699 7.04175 2.16699 13.0001C2.16699 14.9501 2.70866 16.7917 3.57533 18.4167L2.60744 22.1394C2.41271 22.8883 3.10592 23.5651 3.84998 23.3526L7.47533 22.3167Z" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
									<path d="M17.875 16.0859C17.875 16.2614 17.8359 16.4418 17.7529 16.6173C17.6699 16.7928 17.5625 16.9585 17.4209 17.1145C17.1817 17.3778 16.9181 17.5679 16.6202 17.6898C16.3273 17.8116 16.01 17.875 15.6682 17.875C15.1702 17.875 14.638 17.758 14.0766 17.5191C13.5151 17.2803 12.9536 16.9585 12.397 16.5539C11.8356 16.1444 11.3034 15.691 10.7956 15.1889C10.2928 14.6819 9.8387 14.1505 9.43346 13.5948C9.03311 13.039 8.71088 12.4833 8.47653 11.9324C8.24218 11.3766 8.125 10.8453 8.125 10.3383C8.125 10.0068 8.18359 9.68988 8.30076 9.39738C8.41794 9.1 8.60347 8.827 8.86223 8.58325C9.1747 8.27613 9.51646 8.125 9.87775 8.125C10.0145 8.125 10.1512 8.15425 10.2732 8.21275C10.4002 8.27125 10.5125 8.359 10.6003 8.48575L11.733 10.0799C11.8209 10.2018 11.8844 10.3139 11.9283 10.4211C11.9723 10.5235 11.9967 10.6259 11.9967 10.7185C11.9967 10.8355 11.9625 10.9525 11.8942 11.0646C11.8307 11.1767 11.7379 11.2937 11.6207 11.4107L11.2497 11.7959C11.196 11.8495 11.1716 11.9129 11.1716 11.9909C11.1716 12.0299 11.1765 12.064 11.1862 12.103C11.2009 12.142 11.2155 12.1712 11.2253 12.2005C11.3132 12.3614 11.4645 12.571 11.6793 12.8245C11.899 13.078 12.1334 13.3364 12.3873 13.5948C12.6509 13.8531 12.9048 14.092 13.1636 14.3114C13.4174 14.5259 13.6274 14.6721 13.7934 14.7599C13.8178 14.7696 13.8471 14.7842 13.8813 14.7989C13.9203 14.8135 13.9594 14.8184 14.0033 14.8184C14.0863 14.8184 14.1498 14.7891 14.2035 14.7355L14.5745 14.3699C14.6966 14.248 14.8138 14.1554 14.9261 14.0969C15.0384 14.0286 15.1507 13.9945 15.2727 13.9945C15.3655 13.9945 15.4631 14.014 15.5705 14.0579C15.678 14.1018 15.7902 14.1651 15.9123 14.248L17.5284 15.3936C17.6553 15.4814 17.7432 15.5837 17.7969 15.7056C17.8457 15.8275 17.875 15.9494 17.875 16.0859Z" stroke="white" stroke-width="1.5" stroke-miterlimit="10" />
								</svg>
							<?php endif; 
						else: ?>
							<img src="<?php echo esc_url($user_img); ?>" alt="<?php esc_attr_e($whatsapp_username, 'elementskit'); ?>">
						<?php endif;?>
					</span>
					<?php if(!empty($ekit_whatsapp_btn_text)) : ?> 
						<div class="elementskit-whatsapp__popup--btn-text">
							<span><?php echo esc_html( $ekit_whatsapp_btn_text ); ?></span>
							<?php if(!empty($ekit_whatsapp_btn_subtext)) : ?>
								<span><?php echo esc_html( $ekit_whatsapp_btn_subtext ); ?></span>
							<?php endif;?>
						</div>
					<?php endif;?>
				</button>
			</div>
		</div>
<?php
	}
}
