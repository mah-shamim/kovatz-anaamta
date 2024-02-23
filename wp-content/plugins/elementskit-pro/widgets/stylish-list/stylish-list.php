<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Stylish_List_Handler as Handler;

defined('ABSPATH') || exit;
class ElementsKit_Widget_Stylish_List extends Widget_Base {
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
        return [ 'style', 'list', 'info', 'stylish', 'feature' ];
	}

    public function get_help_url() {
        return 'https://wpmet.com/doc/stylish-list';
    }

    protected function register_controls() {

        $this->start_controls_section(
			'ekit_section_list_content',
			[
				'label' => esc_html__('Stylish Lists', 'elementskit'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_stylish_list_type',
			[
				'label' => esc_html__('List Type', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'column',
				'options' => [
					'column'  => esc_html__( 'Vertical', 'elementskit' ),
					'row' => esc_html__( 'Horizontal', 'elementskit' ),
				]
			]
		);

		$this->add_control(
			'ekit_stylish_list_divider',
			[
				'label' => esc_html__('Use Divider', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'ekit_stylish_list_counter',
			[
				'label' => esc_html__( 'Use Counter', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no'		
			]
		);

		$this->add_control(
			'ekit_stylish_list_counter_style',
			[
				'label' => esc_html__( 'Counter Style', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'number-normal',
				'options' => [								
					'number-normal'  => esc_html__( 'Normal', 'elementskit' ),
					'decimal-leading-zero'  => esc_html__( 'Decimal Leading Zero', 'elementskit' ),
					'upper-alpha'  => esc_html__( 'Upper Alpha', 'elementskit' ),
					'lower-alpha'  => esc_html__( 'Lower Alpha', 'elementskit' ),
					'lower-roman'  => esc_html__( 'Lower Roman', 'elementskit' ),
					'upper-roman'  => esc_html__( 'Upper Roman', 'elementskit' ),
					'lower-greek'  => esc_html__( 'Lower Greek', 'elementskit' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list' => 'counter-reset: ekit-counter;',
					'{{WRAPPER}} .ekit-stylish-list-content-counter:before' => 'content: counter(ekit-counter, {{VALUE}}) "{{ekit_stylish_list_counter_suffix.VALUE}}"; counter-increment: ekit-counter;',
				],
				'condition'    => [
					'ekit_stylish_list_counter' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_counter_suffix',
			[
				'label' => esc_html__( 'Counter Suffix', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => '.',
				'options' => [							
					'.'  => esc_html__( 'Dot', 'elementskit' ),
					')'  => esc_html__( 'Bracket', 'elementskit' ),
					':'  => esc_html__( 'Colon', 'elementskit' ),
					' '  => esc_html__( 'None', 'elementskit' ),
				],
				'condition'    => [
					'ekit_stylish_list_counter' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label' => esc_html__('Title', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'default' => 'List Item',
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
				'label_block' => 'true',
				'toggle' => false,
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
                'default'       => 'icon',
				'toggle' => false
            ]
        );

		$repeater->add_control(
            'icon',
            [
                'label' => esc_html__( 'Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'icon icon-right-arrow1',
                    'library' => 'ekiticons',
                ],
                'label_block' => true,
                'condition' => [
                    'media_type' => 'icon',
				]
            ]
        );

		$repeater->add_control(
			'image',
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
			'badge_show',
			[
				'label' => esc_html__('Show Badge', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'badge_title',
			[
				'label'     => esc_html__( 'Badge Text', 'elementskit' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'PRO', 'elementskit' ),
				'condition' => [
					'badge_show' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'badge_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ekit-stylish-list-content-badge span' => 'color: {{VALUE}}',
				],
				'condition' => [
					'badge_show' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'badge_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ekit-stylish-list-content-badge span' => 'background-color: {{VALUE}}',
				],
				'separator' => 'after',
				'condition' => [
					'badge_show' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => esc_html__('Link', 'elementskit'),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'particular_style_show',
			[
				'label' => esc_html__('Add Particular Style', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before'
			]
		);

		$repeater->add_control(
			'list_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ekit-stylish-list-content-wrapper' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'particular_style_show' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'list_border_color',
			[
				'label' => esc_html__( 'Border Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ekit-stylish-list-content-wrapper' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'particular_style_show' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'list_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ekit-stylish-list-content-icon > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ekit-stylish-list-content-text p' => 'color: {{VALUE}}',
				],
				'condition' => [
					'particular_style_show' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'list_icon_bg_color',
			[
				'label' => esc_html__( 'Icon Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ekit-stylish-list-content-icon > i' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ekit-stylish-list-content-text p' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'particular_style_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekit_stylist_lists',
			[
				'label' => esc_html__('List Items', 'elementskit'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => esc_html__('List Item 1', 'elementskit'),
					],
					[
						'title' => esc_html__('List Item 2', 'elementskit'),
					],
					[
						'title' => esc_html__('List Item 3', 'elementskit'),
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_animation',
			[
				'label' => esc_html__('Animation', 'elementskit'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_stylish_list_hover_use',
			[
				'label' => esc_html__( 'Choose Hover Style', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'None', 'elementskit' ),
					'blur(1.6px)' => esc_html__( 'Blur', 'elementskit' ),
					'translateX(10px)' => esc_html__( 'Slide', 'elementskit' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list:hover .ekit-stylish-list-content-wrapper' => 'filter: {{VALUE}};',
					'{{WRAPPER}} .ekit-stylish-list:hover .ekit-stylish-list-content-wrapper:hover' => 'filter: none;',
					'{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover' => 'transform: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ekit_stylish_list_animation_use',
			[
				'label' => esc_html__('Use Entrance Animation', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_stylish_list_hover_use!' => '0.6'
				]
			]
		);

		$this->add_control(
			'ekit_stylish_list_entrance_animation',
			[
				'label' => esc_html__( 'Entrance Animation', 'elementskit' ),
				'type' => Controls_Manager::ANIMATION,
				'condition' => [
					'ekit_stylish_list_animation_use' => 'yes',
					'ekit_stylish_list_hover_use!' => '0.6'
				]
			]
		);

		$this->add_control(
			'ekit_stylish_list_animation_duration',
			[
				'label'     => esc_html__( 'Animation Duration', 'elementskit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					'slow' => esc_html__( 'Slow', 'elementskit' ),
					''     => esc_html__( 'Normal', 'elementskit' ),
					'fast' => esc_html__( 'Fast', 'elementskit' ),
				],
				'condition' => [
					'ekit_stylish_list_entrance_animation!'=> '',
					'ekit_stylish_list_animation_use' => 'yes',
					'ekit_stylish_list_hover_use!' => '0.6'
				]
			]
		);

		$this->add_control(
			'ekit_stylish_list_animation_delay',
			[
				'label'              => esc_html__( 'Animation Delay in Between (s)', 'elementskit' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 0,
				'step'               => 0.1,
				'condition'          => [
					'ekit_stylish_list_entrance_animation!'=> '',
					'ekit_stylish_list_animation_use' => 'yes',
					'ekit_stylish_list_hover_use!' => '0.6'
				]
			]
		);

		$this->end_controls_section();

		/**
		 * Start of style section
		 */
		$this->start_controls_section(
			'ekit_section_common_style',
			[
				'label' => esc_html__('Common', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_stylish_list_justify',
			[
				'label' => esc_html__('Justify Content', 'elementskit'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__('Left', 'elementskit'),
						'icon' => 'eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html__('Center', 'elementskit'),
						'icon' => 'eicon-justify-center-h',
					],
					'end' => [
						'title' => esc_html__('Right', 'elementskit'),
						'icon' => 'eicon-justify-end-h',
					],
					'space-between' => [
						'title' => esc_html__('Space Between', 'elementskit'),
						'icon' => 'eicon-justify-space-between-h',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .ekit-stylish-list.list-inline' => 'justify-content: {{VALUE}};',
				]
			]
		);
		
		$this->add_control(
			'ekit_stylish_list_align_item',
			[
				'label' => esc_html__('Align Items', 'elementskit'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__('Start', 'elementskit'),
						'icon' => 'eicon-align-start-v',
					],
					'center' => [
						'title' => esc_html__('Center', 'elementskit'),
						'icon' => 'eicon-align-center-v',
					],
					'end' => [
						'title' => esc_html__('End', 'elementskit'),
						'icon' => 'eicon-align-end-v',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content' => 'align-items: {{VALUE}};',
				]
			]
		);

		$this->start_controls_tabs(
			'ekit_stylish_list_tabs'
		);
		
		$this->start_controls_tab(
			'ekit_stylish_list_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_stylish_list_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-wrapper',
			],
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_stylish_list_box_shadow',
				'label' => esc_html__( 'List Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_stylish_list_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-wrapper',
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_stylish_list_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_stylish_list_hover_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover',
			],
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_stylish_list_hover_box_shadow',
				'label' => esc_html__( 'List Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_stylish_list_border_hover',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover',
			]
		);
		
		$this->end_controls_tab();
		
		$this->end_controls_tabs();

		$this->add_control(
			'ekit_stylish_list_border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_stylish_list_padding',
			[
				'label' => esc_html__('Padding', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_margin',
			[
				'label' => esc_html__('Margin', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Icon Style Section 
		$this->start_controls_section(
			'ekit_stylish_list_section_icon_style',
			[
				'label' => esc_html__('Icon / Image', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_stylish_list_icon_direction',
			[
				'label' => esc_html__('Direction', 'elementskit'),
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
					'left'   => 'row',
					'top'    => 'column',
					'bottom' => 'column-reverse',
					'right'  => 'row-reverse',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content' => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_icon_size',
			[
				'label' => esc_html__('Size', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','em'],
				'default' => [
					'unit' => 'px',
					'size' => '13',
				],
				'range' => [
					'px' => [
						'max' => 150,
					],
					'em' => [
						'max' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-icon > i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-stylish-list-content-icon > svg' => 'height: {{SIZE}}{{UNIT}}; width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-stylish-list-content-icon > img' => 'height: {{SIZE}}{{UNIT}}; width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_icon_heigh_width',
			[
				'label' => esc_html__('Use Height Width', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'ekit_stylish_list_icon_width',
			[
				'label' => esc_html__('Width', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','em'],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-icon' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_stylish_list_icon_heigh_width' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_stylish_list_icon_height',
			[
				'label' => esc_html__('Height', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','em'],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-icon' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_stylish_list_icon_heigh_width' => 'yes'
				]
			]
		);

		$this->start_controls_tabs(
			'ekit_stylish_list_icon_tabs'
		);
		
		$this->start_controls_tab(
			'ekit_stylish_list_icon_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_stylish_list_icon_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E93469',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-icon > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-stylish-list-content-icon > svg path' => 'stroke: {{VALUE}} !important;',
					'{{WRAPPER}} .ekit-stylish-list-content-icon > img' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_stylish_list_icon_bg_color',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-icon',
			]
		);

		$this->add_control(
			'ekit_stylish_list_icon_border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%','px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_stylish_list_icon_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_stylish_list_icon_hover_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E93469',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover .ekit-stylish-list-content-icon > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover .ekit-stylish-list-content-icon > img' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover .ekit-stylish-list-content-icon > svg path' => 'stroke: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_stylish_list_icon_hover_bg_color',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover .ekit-stylish-list-content-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_border_hover',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover .ekit-stylish-list-content-icon',
			]
		);

		$this->add_control(
			'ekit_stylish_list_icon_border_radius_hover',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%','px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover .ekit-stylish-list-content-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_tab();
		
		$this->end_controls_tabs();

		$this->add_control(
			'ekit_stylish_list_icon_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_icon_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top' => '0',
					'right' => '6',
					'bottom' => '3',
					'left' => '0',
					'unit' => 'px',
					'isLinked' => false
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Title style section
		$this->start_controls_section(
			'ekit_section_title_style',
			[
				'label' => esc_html__('Title', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'ekit_stylish_list_title_text_stroke',
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-title',
			]
		);

		$this->start_controls_tabs(
			'ekit_stylish_list_title_tabs'
		);
		
		$this->start_controls_tab(
			'ekit_stylish_list_title_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_stylish_list_title_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => 'inherit',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-title' => 'color: {{VALUE}}; transition: all 0.3s ease-out;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_stylish_list_title_typography',
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_stylish_list_title_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_stylish_list_title_hover_color',
			[
				'label' => esc_html__('Hover Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#E93469',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover .ekit-stylish-list-content-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_stylish_list_title_typography_hover',
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-title:hover',
			]
		);

		$this->end_controls_tab();
		
		$this->end_controls_tabs();

		$this->add_control(
			'ekit_stylish_list_title_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top' => '0',
					'right' => '6',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
					'isLinked' => false
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Description style section
		$this->start_controls_section(
			'ekit_section_description_style',
			[
				'label' => esc_html__('Description', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_stylish_list_description_direction',
			[
				'label' => esc_html__('Description Direction', 'elementskit'),
				'type' => Controls_Manager::CHOOSE,
				'options'	=> [
					'bottom' => [
						'title'	=> esc_html__( 'Bottom', 'elementskit' ),
						'icon'	=> 'eicon-caret-down',
					],
					'right' => [
						'title'	=> esc_html__( 'Right', 'elementskit' ),
						'icon'	=> 'eicon-caret-right',
					]
				],
				'default' => 'bottom',
				'selectors_dictionary' => [
					'right'   => 'row',
					'bottom' => 'column',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-text' => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_description_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => 'inherit',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-description' => 'color: {{VALUE}}; transition: color 0.3s ease;',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_description_hover_color',
			[
				'label' => esc_html__('Hover Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover .ekit-stylish-list-content-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_stylish_list_description_typography',
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-description',
			]
		);

		$this->add_control(
			'ekit_stylish_list_description_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top' => '0',
					'right' => '6',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
					'isLinked' => false
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Badge style section
		$this->start_controls_section(
			'ekit_section_badge_style',
			[
				'label' => esc_html__('Badge', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_stylish_list_badge_direction',
			[
				'label' => esc_html__('Badge Direction', 'elementskit'),
				'type' => Controls_Manager::CHOOSE,
				'options'	=> [
					'2' => [
						'title'	=> esc_html__( 'Left', 'elementskit' ),
						'icon'	=> 'eicon-caret-left',
					],
					'10' => [
						'title'	=> esc_html__( 'Right', 'elementskit' ),
						'icon'	=> 'eicon-caret-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-badge' => 'order: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_badge_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-badge span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_badge_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#EF0A0A',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-badge span' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_stylish_list_badge_typography',
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-badge span',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_stylish_list_badge_shadow',
				'label' => esc_html__( 'Badge Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-badge span',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_stylish_list_badge_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-badge span',
			]
		);

		$this->add_control(
			'ekit_stylish_list_badge_border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => '4',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-badge span' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_badge_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top' => '2',
					'right' => '5',
					'bottom' => '2',
					'left' => '5',
					'unit' => 'px',
					'isLinked' => false
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-badge span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'ekit_stylish_list_badge_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//Counter Style Section
		$this->start_controls_section(
			'ekit_section_counter_style',
			[
				'label' => esc_html__('Counter', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'$ekit_stylish_list_counter' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_stylish_list_counter_direction',
			[
				'label' => esc_html__('Counter Direction', 'elementskit'),
				'type' => Controls_Manager::CHOOSE,
				'options'	=> [
					'1' => [
						'title'	=> esc_html__( 'Left', 'elementskit' ),
						'icon'	=> 'eicon-caret-left',
					],
					'11' => [
						'title'	=> esc_html__( 'Right', 'elementskit' ),
						'icon'	=> 'eicon-caret-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-counter' => 'order: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_counter_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => 'inherit',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-counter:before' => 'color: {{VALUE}}; transition: color 0.3s ease;',
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_counter_hover_color',
			[
				'label' => esc_html__('Hover Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-wrapper:hover .ekit-stylish-list-content-counter:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_stylish_list_counter_typography',
				'selector' => '{{WRAPPER}} .ekit-stylish-list-content-counter:before',
			]
		);

		$this->add_control(
			'ekit_stylish_list_counter_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-content-counter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//Divider Style Section
		$this->start_controls_section(
			'ekit_section_divider_style',
			[
				'label' => esc_html__('Divider', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'$ekit_stylish_list_divider' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_stylish_list_divider_color',
			[
				'label'     => __( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E6E7EC',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-divider:not(:last-child)::before' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .ekit-stylish-list-divider-inline:not(:last-child)::before' => 'border-left-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ekit_stylish_list_divider_type',
			[
				'label'     => __( 'Divider Style', 'elementskit' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => __( 'Solid', 'elementskit' ),
					'double' => __( 'Double', 'elementskit' ),
					'dotted' => __( 'Dotted', 'elementskit' ),
					'dashed' => __( 'Dashed', 'elementskit' ),
					'groove' => __( 'Groove', 'elementskit' ),
				],
				'default'   => 'solid',
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-divider:not(:last-child)::before' => 'border-top-style: {{VALUE}};',
					'{{WRAPPER}} .ekit-stylish-list-divider-inline:not(:last-child)::before' => 'border-left-style: {{VALUE}};',
				]
			]
		);

		$this->add_responsive_control(
			'ekit_stylish_list_divider_width',
			[
				'label'       => __( ' Width', 'elementskit' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'em' ],
				'range'       => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'em' => [
						'min' => 0,
						'max' => 65,
					],
				],

				'selectors'   => [
					'{{WRAPPER}} .ekit-stylish-list-divider:not(:last-child)::before' => 'width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-stylish-list-divider-inline:not(:last-child)::before ' => 'border-left-width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'ekit_stylish_list_divider_height',
			[
				'label'       => __( ' Height', 'elementskit' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'em' ],
				'selectors'   => [
					'{{WRAPPER}} .ekit-stylish-list-divider:not(:last-child)::before' => 'border-top-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-stylish-list-divider-inline:not(:last-child)::before' => 'height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'ekit_stylish_list_divider_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-stylish-list-divider:not(:last-child)::before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-stylish-list-divider-inline:not(:last-child)::before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_section();
    }

     protected function render() {
        echo '<div class="ekit-wid-con">';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw() {
        $settings = $this->get_settings_for_display();
        extract($settings);

		$list_type_class = $ekit_stylish_list_type == 'row' ? 'list-inline' : '';
		$divider_class = $ekit_stylish_list_type == 'row' ? '-inline' : '';

		$entrance_animation = '';
		$animation_duration = '';

		if($ekit_stylish_list_animation_use == 'yes' ) {
			$entrance_animation = "data-ekit-animation= $ekit_stylish_list_entrance_animation";
			$animation_duration = ' animated-'.$ekit_stylish_list_animation_duration;
		}	
	?>
		<ul class="ekit-stylish-list <?php echo esc_attr($list_type_class)?>" <?php echo esc_attr($entrance_animation); ?>>
			<?php foreach($ekit_stylist_lists as $index => $list) : 
					$animation_delay = $ekit_stylish_list_animation_use == 'yes' ? (' data-ekit-delay='.$ekit_stylish_list_animation_delay * $index * 1000) : '';
				?>
				<li class="ekit-stylish-list-content-wrapper elementor-repeater-item-<?php echo esc_attr( $list[ '_id' ].$animation_duration ); ?>" <?php echo esc_attr($animation_delay); ?>>
					<?php if(!empty($list['link']['url'])) : 
						$this->add_link_attributes('link'.$list[ '_id' ] , $list['link']); ?>
						<a class="ekit-wrapper-link" <?php $this->print_render_attribute_string('link'.$list[ '_id' ]); ?>></a>
					<?php endif; ?>
					<div class="ekit-stylish-list-content">
						<?php if ($ekit_stylish_list_counter == 'yes') : ?>	
							<div class="ekit-stylish-list-content-counter"></div>
						<?php endif; ?>
						<?php if ($list['media_type'] == 'icon' && !empty($list['icon']['value'])) : ?>
							<div class="ekit-stylish-list-content-icon">			
								<?php Icons_Manager::render_icon( $list['icon'], [ 'aria-hidden' => 'true' ] ); ?>			
							</div>
						<?php endif; ?>
						<?php if ($list['media_type'] == 'image' && !empty($list['image']['id'])) : ?>
							<div class="ekit-stylish-list-content-icon">			
								<?php echo wp_kses(wp_get_attachment_image( $list['image']['id'], 'full', false,'' ), \ElementsKit_Lite\Utils::get_kses_array()); ?>
							</div>
						<?php endif; ?>
						<div class="ekit-stylish-list-content-text">
							<span class="ekit-stylish-list-content-title"><?php echo wp_kses($list['title'], \ElementsKit_Lite\Utils::get_kses_array())?></span>
							<?php if (!empty($list['description'])) : ?>
								<span class="ekit-stylish-list-content-description"><?php echo wp_kses($list['description'], \ElementsKit_Lite\Utils::get_kses_array()); ?></span>
							<?php endif; ?>
						</div>
						<?php if ($list['badge_show'] == 'yes') : ?>
							<div class="ekit-stylish-list-content-badge">
								<span class="elementor-inline-editing"><?php echo esc_html($list['badge_title']); ?></span>
							</div>
						<?php endif; ?>
					</div>
					
				</li>
				<?php if($ekit_stylish_list_divider == 'yes') : ?>
					<div class="ekit-stylish-list-divider<?php echo esc_attr($divider_class)?>"></div>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php 
	}
}
