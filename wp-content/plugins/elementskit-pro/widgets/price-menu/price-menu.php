<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Price_Menu_Handler as Handler;

defined('ABSPATH') || exit;
class ElementsKit_Widget_Price_Menu extends Widget_Base {
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
		return 'https://wpmet.com/doc/price-menu/';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ekit_price_menu_list_section',
			[
				'label' => esc_html__('List', 'elementskit'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_price_menu_list_style',
			[
				'label' => esc_html__( 'Choose Style', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'list',
				'options' => [
					'list'  => esc_html__( 'List', 'elementskit' ),
					'card' => esc_html__( 'Card', 'elementskit' ),
				],
			]
		);

		$this->add_control(
            'ekit_price_menu_price_position', [
                'label'       => esc_html__( 'Price Position', 'elementskit' ),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'right' => [
                        'title' => esc_html__( 'Right', 'elementskit' ),
                        'icon'  => 'eicon-arrow-right',
                    ],
                    'bottom' => [
                        'title' => esc_html__( 'Bottom', 'elementskit' ),
                        'icon'  => 'eicon-arrow-down',
                    ],
                ],
				'default' => 'right',
				'toggle' => false,
				'condition' => [
					'ekit_price_menu_list_style' => 'list',
				],
            ]
        );

		$this->add_control(
			'ekit_price_menu_separator_show',
			[
				'label' => esc_html__('Hide Separator?', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => '0',
				'default' => '1',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-price-menu-caption-separator' => 'flex-grow: {{VALUE}};',
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'list',
					'ekit_price_menu_price_position' => 'right'
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_enable_slider',
			[
				'label' => esc_html__( 'Enable Slider?', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_price_menu_list_style' => 'card',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_price_menu_items_per_row',
			[
				'label' => esc_html__( 'Items Per Row', 'elementskit' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 12,
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => 4,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card' => 'display: grid ; grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'card',
					'ekit_price_menu_enable_slider!' => 'yes'
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label' => esc_html__('Title', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Chicken Masala Fries',
				'label_block' => 'true',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'description',
			[
				'label' => esc_html__('Description', 'elementskit'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => 'Cheese Kunafa (Arabian Dessert)',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'price',
			[
				'label' => esc_html__('Price', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'default' => '$12.48',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
            'media_type', [
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
                'default'       => 'none',
            ]
        );

        $repeater->add_control(
            'icon',
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
                    'media_type' => 'icon',
				]
            ]
        );

		$repeater->add_control(
			'ekit_price_menu_image',
			[
				'label' => esc_html__('Image', 'elementskit'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
					'id' => -1
				],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
                    'media_type' => 'image',
                ]
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => esc_html__('Link', 'elementskit'),
				'type' => Controls_Manager::URL,
				'default' => ['url' => '#'],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ekit_price_menu_button_show!' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'ekit_price_menu_button_heading',
			[
				'label' => esc_html__( 'Button', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_price_menu_button_show' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'ekit_price_menu_button_show',
			[
				'label' => esc_html__('Use Button', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'elementskit'),
				'label_off' => esc_html__('Hide', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label' => esc_html__('Button Text', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('ADD TO CART', 'elementskit'),
				'placeholder' => esc_html__('Type your title here', 'elementskit'),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ekit_price_menu_button_show' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label' => esc_html__('Button Link', 'elementskit'),
				'type' => Controls_Manager::URL,
				'default' => ['url' => '#'],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ekit_price_menu_button_show' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'button_icon_switch',
			[
				'label' => esc_html__('Add Icon In Button?', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_price_menu_button_show' => 'yes'
				]
			]
		);

		$repeater->add_control(
            'button_icon_position',
            [
                'label' => esc_html__( 'Icon Position', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'after',
				'options' => [
					'before'  => esc_html__( 'Before', 'elementskit' ),
					'after' => esc_html__( 'After', 'elementskit' ),
				],
                'condition' => [
					'ekit_price_menu_button_show' => 'yes',
                    'button_icon_switch' => 'yes',
                ]
            ]
        );

		$repeater->add_control(
            'button_icon',
            [
                'label' => esc_html__( 'Button Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_icon_box_header_icon',
                'default' => [
                    'value' => 'icon icon-review',
                    'library' => 'ekiticons',
                ],
                'label_block' => true,
                'condition' => [
					'ekit_price_menu_button_show' => 'yes',
                    'button_icon_switch' => 'yes',
                ]
            ]
        );

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ekit-price-menu .ekit-price-menu-item{{CURRENT_ITEM}}, {{WRAPPER}} .ekit-price-card .ekit-price-card-item{{CURRENT_ITEM}}',
				'separator' => 'before'
			],
		);

		$this->add_control(
			'ekit_price_lists',
			[
				'label' => esc_html__('List Items', 'elementskit'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => esc_html__('Chicken Masala Fries', 'elementskit'),
						'description' => esc_html__('Cheese Kunafa (Arabian Dessert)', 'elementskit'),
						'price' => esc_html__('$12.48', 'elementskit'),
					],
					[
						'title' => esc_html__('Beef Belly Tacos', 'elementskit'),
						'description' => esc_html__('Cheese Kunafa (Arabian Dessert)', 'elementskit'),
						'price' => esc_html__('$12.48', 'elementskit'),
					],
					[
						'title' => esc_html__('Signature BBQ Beef', 'elementskit'),
						'description' => esc_html__('Cheese Kunafa (Arabian Dessert)', 'elementskit'),
						'price' => esc_html__('$12.48', 'elementskit'),
					],
					[
						'title' => esc_html__('Hot Pastrami Sandwich', 'elementskit'),
						'description' => esc_html__('Cheese Kunafa (Arabian Dessert)', 'elementskit'),
						'price' => esc_html__('$12.48', 'elementskit'),
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		/** Slider Settings */
		$this->start_controls_section(
			'ekit_price_menu_slider_settings',
			[
				'label' => esc_html__( 'Slider Settings', 'elementskit' ),
				'condition' => [
					'ekit_price_menu_list_style' => 'card',
					'ekit_price_menu_enable_slider' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'ekit_price_menu_slider_spacing',
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
					'{{WRAPPER}} .ekit-price-card-slider' => '--ekit-team-slider-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_price_menu_slidetoshow',
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
					'size' => 4,
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
					'size' => 4,
					'unit' => 'px',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider' => '--ekit-team-slider-slides-to-show:  {{SIZE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_price_menu_slidesToScroll',
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
			'ekit_price_menu_autoplay',
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
			'ekit_price_menu_speed',
			[
				'label' => esc_html__( 'Speed (ms)', 'elementskit' ),
				'type' =>  Controls_Manager::NUMBER,
				'min' => 500,
				'max' => 15000,
				'step' => 100,
				'default' => 1000,
				'condition' => [
					'ekit_price_menu_autoplay' => 'yes',
				]
			]
		);

		$this->add_control(
			'ekit_price_menu_pause_on_hover',
			[
				'label' => esc_html__( 'Pause On Hover', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'ekit_price_menu_autoplay' => 'yes',
				]
			]
		);

		$this->add_control(
			'ekit_price_menu_show_arrow',
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
			'ekit_price_menu_slider_left_arrow_icon',
			[
				'label' => esc_html__( 'Left Arrow Icon', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_price_menu_left_arrow',
				'default' => [
					'value' => 'icon icon-left-arrows',
					'library' => 'ekiticons',
				],
				'condition' => [
					'ekit_price_menu_show_arrow' => 'yes',
				]
			]
		);

		$this->add_control(
			'ekit_price_menu_slider_right_arrow_icon',
			[
				'label' => esc_html__( 'Right Arrow Icon', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_price_menu_right_arrow',
				'default' => [
					'value' => 'icon icon-right-arrow1',
					'library' => 'ekiticons',
				],
				'condition' => [
					'ekit_price_menu_show_arrow' => 'yes',
				]
			]
		);

		$this->add_control(
			'ekit_price_menu_loop',
			[
				'label' => esc_html__( 'Enable Loop?', 'elementskit' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'ekit_price_menu_show_dot',
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

		/** Style Section Start */
		/** Item Style Section */
		$this->start_controls_section(
			'section_item_style',
			[
				'label' => esc_html__('Item', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_control(
			'ekit_price_menu_vertical_align',
			[
				'label' => esc_html__('Vertical Align', 'elementskit'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => esc_html__('Top', 'elementskit'),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__('Center', 'elementskit'),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__('Bottom', 'elementskit'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-price-menu-item' => 'align-items: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'bottom' => 'flex-end',
				],
				'default' => 'top',
				'condition' => [
					'ekit_price_menu_list_style' => 'list',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_item_row_gap',
			[
				'label' => esc_html__('Row Gap (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
				],	
				'condition' => [
					'ekit_price_menu_list_style' => 'card',
					'ekit_price_menu_enable_slider!' => 'yes'
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_item_column_gap',
			[
				'label' => esc_html__('Column Gap (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card ' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
				],	
				'condition' => [
					'ekit_price_menu_list_style' => 'card',
					'ekit_price_menu_enable_slider!' => 'yes'
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_row_margin_gap',
			[
				'label' => esc_html__('Space Between (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-price-menu li:not(:first-child)' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'default' => [
					'size' => 30,
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'list',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_item_border_disable_last',
			[
				'label' => esc_html__('Disable Border For Last Element?', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before',
				'condition' => [
					'ekit_price_menu_list_style' => 'list',
				],
			]
		);	

		$this->add_control(
			'ekit_price_menu_item_border_style',
			[
				'label' => esc_html__('Border Type', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid' => esc_html__('Solid', 'elementskit'),
					'dotted' => esc_html__('Dotted', 'elementskit'),
					'dashed' => esc_html__('Dashed', 'elementskit'),
					'double' => esc_html__('Double', 'elementskit'),
					'none' => esc_html__('None', 'elementskit'),
				],
				'default' => 'dashed',
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu li:not(:last-child)' => 'border-style: {{VALUE}}',
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'list',
					'ekit_price_menu_item_border_disable_last' => 'yes'
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_item_border_weight',
			[
				'label' => esc_html__('Weight', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 1,
					'left' => 0,
					'unit' => 'px',
					'isLinked' => false,
				],		
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu li:not(:last-child)' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_price_menu_item_border_style!' => 'none',
					'ekit_price_menu_list_style' => 'list',
					'ekit_price_menu_item_border_disable_last' => 'yes'
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_item_border_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu li:not(:last-child)' => 'border-color: {{VALUE}};',
				],
				'default' => 'rgba(6, 33, 38, 0.2)',
				'condition' => [
					'ekit_price_menu_item_border_style!' => 'none',
					'ekit_price_menu_list_style' => 'list',
					'ekit_price_menu_item_border_disable_last' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_item_border_full',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-price-menu .ekit-price-menu-item, {{WRAPPER}} .ekit-price-card .ekit-price-card-item',
				'condition' => [
					'ekit_price_menu_item_border_disable_last!' => 'yes'
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_item_border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu .ekit-price-menu-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-price-card .ekit-price-card-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'ekit_price_menu_item_tabs'
		);
		
		$this->start_controls_tab(
			'ekit_price_menu_item_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_price_menu_item_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ekit-price-menu .ekit-price-menu-item, {{WRAPPER}} .ekit-price-card .ekit-price-card-item',
			],
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_price_menu_item_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-price-menu .ekit-price-menu-item, {{WRAPPER}} .ekit-price-card .ekit-price-card-item',
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_price_menu_item_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_item_price_menu_hover_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ekit-price-menu .ekit-price-menu-item:hover, {{WRAPPER}} .ekit-price-card .ekit-price-card-item:hover',
			],
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_price_menu_item_hover_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-price-menu .ekit-price-menu-item:hover, {{WRAPPER}} .ekit-price-card .ekit-price-card-item:hover',
			]
		);
		
		$this->end_controls_tab();
		
		$this->end_controls_tabs();

		$this->add_control(
			'ekit_price_menu_item_padding',
			[
				'label' => esc_html__('Padding', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu .ekit-price-menu-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-price-card-item .ekit-price-card-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_price_menu_item_wrapper_padding',
			[
				'label' => esc_html__('Wrapper Padding', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'card',
					'ekit_price_menu_enable_slider' => 'yes'
				],
			]
		);

		

		$this->end_controls_section();

		/** Image Style Section*/
		$this->start_controls_section(
			'section_image_style',
			[
				'label' => esc_html__('Image', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'ekit_price_menu_image_size',
				'default' => 'full'
			]
		);

		$this->add_control(
			'ekit_price_menu_image_border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_image_spacing',
			[
				'label' => esc_html__('Spacing', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-image' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/** Icon Style Section */
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__('Icon', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_price_menu_icon_text_align',
			[
				'label' => esc_html__('Alignment', 'elementskit'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'elementskit'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'elementskit'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'elementskit'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-item .ekit-price-menu-icon' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'card',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_icon_spacing',
			[
				'label' => esc_html__('Spacing', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-item .ekit-price-menu-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-price-card-item .ekit-price-menu-icon' => 'margin-top: {{SIZE}}{{UNIT}}; margin-right: 0px;',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_icon_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-icon > i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_icon_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-icon > i' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-price-menu-icon > i',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'ekit_price_menu_icon_border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%','px'],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-icon > i' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_icon_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-icon > i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/** List Style Section */
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__('Content', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_price_menu_text_align',
			[
				'label' => esc_html__('Text Align', 'elementskit'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'elementskit'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'elementskit'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'elementskit'),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__('Justify', 'elementskit'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-caption .ekit-price-card-caption-header' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'card',
				],
				'separator' => 'after',
			]
		);

		// title style 
		$this->add_control(
			'ekit_price_menu_title_heading',
			[
				'label' => esc_html__('Title', 'elementskit'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'ekit_price_menu_title_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption-header .ekit-price-menu-caption-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-price-menu-caption-header .ekit-price-menu-caption-title a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-price-card-caption-header .ekit-price-card-caption-header-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_title_hover_color',
			[
				'label' => esc_html__('Hover Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-item:hover .ekit-price-menu-caption-header .ekit-price-menu-caption-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-price-menu-item:hover .ekit-price-menu-caption-header .ekit-price-menu-caption-title a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-price-card-item:hover .ekit-price-card-caption-header .ekit-price-card-caption-header-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_price_menu_title_typography',
				'selector' => '{{WRAPPER}} .ekit-price-menu-caption-header .ekit-price-menu-caption-title, {{WRAPPER}} .ekit-price-card-caption-header .ekit-price-card-caption-header-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'ekit_price_menu_title_text_stroke',
				'selector' => '{{WRAPPER}} .ekit-price-menu-caption-header .ekit-price-menu-caption-title, {{WRAPPER}} .ekit-price-card-caption-header .ekit-price-card-caption-header-title',
			]
		);

		$this->add_control(
			'ekit_price_menu_title_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption-header .ekit-price-menu-caption-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-price-card-caption-header .ekit-price-card-caption-header-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// description style
		$this->add_control(
			'ekit_price_menu_description_heading',
			[
				'label' => esc_html__('Description', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_price_menu_description_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-description' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-price-card-caption-header .ekit-price-card-caption-header-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_price_menu_description_typography',
				'selector' => '{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-description, {{WRAPPER}} .ekit-price-card-caption-header .ekit-price-card-caption-header-description',
			]
		);

		$this->add_control(
			'ekit_price_menu_description_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-price-card-caption-header .ekit-price-card-caption-header-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_description_space_top',
			[
				'label' => esc_html__( 'Space Top (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-caption-header .ekit-price-card-caption-header-description' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'card'
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_description_space_bottom',
			[
				'label' => esc_html__( 'Space Bottom (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-caption-header .ekit-price-card-caption-header-description' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'card'
				],
			]
		);

		// price style
		$this->add_control(
			'ekit_price_menu_price_heading',
			[
				'label' => __('Price', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_price_menu_price_color',
			[
				'label' => __('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-price' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-price-card-caption-footer .ekit-price-card-caption-footer-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_price_menu_price_typography',
				'selector' => '{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-price, {{WRAPPER}} .ekit-price-card-caption-footer .ekit-price-card-caption-footer-price',
			]
		);

		$this->add_control(
			'ekit_price_menu_price_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-price-card-caption-footer .ekit-price-card-caption-footer-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		// separator style
		$this->add_control(
			'ekit_price_menu_separator_heading',
			[
				'label' => esc_html__('Separator', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_price_menu_list_style' => 'list',
					'ekit_price_menu_separator_show!' => '0',
					'ekit_price_menu_price_position' => 'right'
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_price_menu_separator_style',
			[
				'label' => esc_html__('Style', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid' => esc_html__('Solid', 'elementskit'),
					'dotted' => esc_html__('Dotted', 'elementskit'),
					'dashed' => esc_html__('Dashed', 'elementskit'),
					'double' => esc_html__('Double', 'elementskit'),
					'none' => esc_html__('None', 'elementskit'),
				],
				'default' => 'solid',
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption-header .ekit-price-menu-caption-separator' => 'border-bottom-style: {{VALUE}}',
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'list',
					'ekit_price_menu_separator_show!' => '0',
					'ekit_price_menu_price_position' => 'right'
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_separator_weight',
			[
				'label' => esc_html__('Weight', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 10,
					],
				],
				'default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption-header .ekit-price-menu-caption-separator' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],		
				'condition' => [
					'ekit_price_menu_list_style' => 'list',
					'ekit_price_menu_separator_style!' => 'none',
					'ekit_price_menu_separator_show!' => '0',
					'ekit_price_menu_price_position' => 'right'
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_separator_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption-header .ekit-price-menu-caption-separator' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'list',
					'ekit_price_menu_separator_style!' => 'none',
					'ekit_price_menu_separator_show!' => '0',
					'ekit_price_menu_price_position' => 'right'
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_separator_spacing',
			[
				'label' => esc_html__('Spacing', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'list',
					'ekit_price_menu_separator_style!' => 'none',
					'ekit_price_menu_separator_show!' => '0',
					'ekit_price_menu_price_position' => 'right'
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption-header .ekit-price-menu-caption-separator' => 'margin: 0px {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/** Button Style Section */
		$this->start_controls_section(
			'ekit_price_menu_section_button_style',
			[
				'label' => esc_html__('Button', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_price_menu_button_price_direction',
			[
				'label' => esc_html__('Button Direction', 'elementskit'),
				'type' => Controls_Manager::CHOOSE,
				'options'	=> [
					'left' => [
						'title'	=> esc_html__( 'Left', 'elementskit' ),
						'icon'	=> 'eicon-caret-left',
					],
					'top' => [
						'title'	=> esc_html__( 'Top', 'elementskit' ),
						'icon'	=> 'eicon-caret-up',
					],
					'bottom' => [
						'title'	=> esc_html__( 'Bottom', 'elementskit' ),
						'icon'	=> 'eicon-caret-down',
					],
					'right' => [
						'title'	=> esc_html__( 'Right', 'elementskit' ),
						'icon'	=> 'eicon-caret-right',
					],
				],
				'selectors_dictionary' => [
					'left'   => 'row-reverse',
					'top'    => 'column-reverse',
					'bottom' => 'column',
					'right'  => 'row',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card-caption-footer' => 'flex-direction: {{VALUE}};',
				],
				'condition' => [
					'ekit_price_menu_list_style' => 'card',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_price_menu_image_btn_typography_group',
				'label' =>esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-button, {{WRAPPER}} .ekit-price-card-caption-footer-button',
			]
		);

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'ekit_price_menu_image_tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_price_menu_image_box_button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-button' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-price-card-caption-footer-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_price_menu_image_btn_background_group',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'classic' => 'image'
				],
                'selector' => '{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-button, {{WRAPPER}} .ekit-price-card-caption-footer-button',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_price_menu_image_button_border_color_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-button, {{WRAPPER}} .ekit-price-card-caption-footer-button',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_price_menu_image_tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_price_menu_image_btn_hover_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-button:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-price-card-caption-footer-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_price_menu_image_btn_background_hover_group',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'classic' => 'image'
				],
                'selector' => '{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-button:hover, {{WRAPPER}} .ekit-price-card-caption-footer-button:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_price_menu_image_button_border_hv_color_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-button:hover, {{WRAPPER}} .ekit-price-card-caption-footer-button:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_price_menu_image_btn_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-price-card-caption-footer-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_price_menu_image_btn_border_radius',
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
				'selectors' => [
					'{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-button' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-price-card-caption-footer-button' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_price_menu_image_button_box_shadow',
                'selector' => '{{WRAPPER}} .ekit-price-menu-caption .ekit-price-menu-caption-button, {{WRAPPER}} .ekit-price-card-caption-footer-button',
            ]
        );

		$this->end_controls_section(); 

		/** Button Style Section */
		$this->start_controls_section(
			'ekit_price_menu_section_button_icon_style',
			[
				'label' => esc_html__('Button Icon', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_price_menu_button_icon_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card-caption-footer-button > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-price-menu-caption-button > i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_button_icon_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card-caption-footer-button:hover > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-price-menu-caption-button:hover > i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_button_icon_size',
			[
				'label' => esc_html__('Size (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card-caption-footer-button > i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-price-menu-caption-button > i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_button_icon_spacing',
			[
				'label' => esc_html__('Spacing (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .price-menu-button-icon-before' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .price-menu-button-icon-after' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section(); 


		/** Arrow Style Section */  
        $this->start_controls_section(
			'ekit_price_menu_section_navigation',
			[
				'label' => esc_html__( 'Arrows', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_price_menu_show_arrow' => 'yes'
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_price_menu_arrow_size',
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
					'{{WRAPPER}} .ekit-price-card-slider .elementor-swiper-button > i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
        );

		$this->add_control(
			'ekit_price_menu_position_popover_toggle',
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
			'ekit_price_menu_arrow_pos_head',
			[
				'label' => esc_html__( 'Left Arrow Position', 'elementskit' ),
				'type' => Controls_Manager::HEADING
			]
		);

		$this->add_responsive_control(
			'ekit_price_menu_arrow_left_pos_left',
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
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card-slider-button-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_price_menu_arrow_left_pos_top',
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
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card-slider-button-prev' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
        );

		$this->add_control(
			'ekit_price_menu_arrow_right_pos_head',
			[
				'label' => esc_html__( 'Right Arrow Position', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_responsive_control(
			'ekit_price_menu_arrow_right_pos_right',
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
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card-slider-button-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_price_menu_arrow_right_pos_top',
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
					'{{WRAPPER}} .ekit-wid-con .ekit-price-card-slider-button-next' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
        );

		$this->end_popover();

        // Arrow Normal
		$this->start_controls_tabs('ekit_price_menu_logo_style_tabs');

        $this->start_controls_tab(
			'ekit_price_menu_logo_arrow_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

        $this->add_control(
			'ekit_price_menu_arrow_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider .elementor-swiper-button > i' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_control(
			'ekit_price_menu_arrow_background',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider .elementor-swiper-button > i' => 'background: {{VALUE}}',
				],
			]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_price_menu_arrow_border_group',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-price-card-slider .elementor-swiper-button > i',
			]
        );

        $this->end_controls_tab();

        //  Arrow hover tab
        $this->start_controls_tab(
			'ekit_price_menu_arrow_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
        );

        $this->add_control(
			'ekit_price_menu_arrow_hv_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider .elementor-swiper-button:hover > i' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_control(
			'ekit_price_menu_arrow_hover_background',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider .elementor-swiper-button:hover > i' => 'background: {{VALUE}}',
				],
			]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_price_menu_arrow_border_hover_group',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-price-card-slider .elementor-swiper-button:hover > i',
			]
        );

        $this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_price_menu_arrow_border_radious',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider .elementor-swiper-button > i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before'
			]
        );

		$this->add_responsive_control(
			'ekit_price_menu_arrow_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider .elementor-swiper-button > i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

		$this->end_controls_section();

		/** Dot Style Section */ 
		$this->start_controls_section(
			'ekit_price_menu_navigation_dot',
			[
				'label' => esc_html__( 'Dots', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
						'ekit_price_menu_show_dot' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_price_menu_dots_left_right_spacing',
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
					'{{WRAPPER}} .ekit-price-card-slider .swiper-pagination .swiper-pagination-bullet' => 'margin-right: {{SIZE}}{{UNIT}};margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_dots_top_to_bottom',
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
				'default' => [
					'size' => 50,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider .swiper-pagination' => 'transform: translateY({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_dot_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider .swiper-pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_dot_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider .swiper-pagination .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_price_menu_dot_border_radius',
			[
				'label' => esc_html__( 'Border radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider .swiper-pagination .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_price_menu_dot_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ekit-price-card-slider .swiper-pagination .swiper-pagination-bullet',
			]
		);

		$this->add_control(
			'ekit_price_menu_dot_active_heading',
			[
				'label' => esc_html__( 'Active', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_price_menu_dot_active_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ekit-price-card-slider .swiper-pagination .swiper-pagination-bullet-active',
			]
		);

		$this->add_responsive_control(
			'ekit_price_menu_dot_active_scale',
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
					'{{WRAPPER}} .ekit-price-card-slider .swiper-pagination .swiper-pagination-bullet-active' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		echo '<div class="ekit-wid-con" >';
		$this->render_raw();
		echo '</div>';
	}

	protected function render_raw() {
		$settings = $this->get_settings_for_display();
		extract($settings); 
		include Handler::get_dir().'/styles/'.$ekit_price_menu_list_style.'.php';		
 	}
}
