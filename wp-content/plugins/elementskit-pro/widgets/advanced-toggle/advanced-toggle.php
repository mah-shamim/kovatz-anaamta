<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Advanced_Toggle_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Advanced_Toggle extends Widget_Base {
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
        return 'https://wpmet.com/doc/advanced-toggle/';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'ekit_advanced_toggle_control_section', [
                'label' =>esc_html__( 'Advanced Toggle', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_advanced_toggle_style',
            [
                'label' => esc_html__('Choose Style', 'elementskit'),
                'type' => ElementsKit_Controls_Manager::IMAGECHOOSE,
                'default' => 'nav-tab-style',
                'options' => [
                    'nav-tab-style' => [
                        'title' => esc_html__( 'Image style 1', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/nav-tab-style.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/nav-tab-style.png',
                        'width' => '50%',
                    ],
                    'switch-style' => [
                        'title' => esc_html__( 'Image style 2', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/switch-style.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/switch-style.png',
                        'width' => '50%',
                    ],
                ],
            ]
        );

        $this->add_control(
			'ekit_advanced_toggle_description',
			[
				'raw' => '<strong>' . __( 'Please note!', 'elementskit' ) . '</strong> ' . __( 'This style show\'s only first two tabs.', 'elementskit' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type' => 'ui',
				'condition' => [
					'ekit_advanced_toggle_style' => 'switch-style',
				],
			]
		);

        $this->add_control(
			'ekit_advanced_toggle_alignment',
			[
				'label' => __( 'Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementskit' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
                ],
                'selectors'=> [
                    '{{WRAPPER}} .elemenetskit-toogle-controls-wraper-outer' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .ekit-switch-nav-wraper-outer' => 'text-align: {{VALUE}};'
                ],
				'default' => 'center',
				'toggle' => true,
			]
		);

            /**
             * Control: Enable Ajax.
             */
            $this->add_control(
                'ekit_ajax_template',
                [
                    'label'         => esc_html__( 'Enable Ajax', 'elementskit' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'prefix_class'  => 'ekit-template-ajax--',
                    'render_type'   => 'template',
                ]
            );

        $repeater = new Repeater();

        $repeater->add_control(
            'ekit_toggle_title', [
                'label' => esc_html__('Title', 'elementskit'),
                'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'ekit_toggle_content', [
                'label' => esc_html__('Content', 'elementskit'),
                'type' => ElementsKit_Controls_Manager::WIDGETAREA,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
			'ekit_toggle_indicator_bg_color',
			[
				'label' => __( 'Indicator Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#1868dd',
                'selectors' => [
                    '{{WRAPPER}} .elemenetskit-toggle-indicator{{CURRENT_ITEM}}'    => 'background-color: {{VALUE}};'
                ]
			]
        );

        $repeater->start_controls_tabs(
            'ekit_toggle_title_color_control_tabs'
		);
			// Normal
			$repeater->start_controls_tab(
				'ekit_toggle_title_color_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'elementskit' ),
				]
            );
            
            $repeater->add_control(
                'ekit_toggle_title_color_normal',
                [
                    'label' => __( 'Title Color', 'elementskit' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}} .elementskit-toggle-nav-link' => 'color: {{VALUE}}',
                        '{{WRAPPER}} {{CURRENT_ITEM}}.elementskit-switch-nav-link' => 'color: {{VALUE}}',
                    ],
                ]
            );
	
			$repeater->end_controls_tab();

			// Hover
			$repeater->start_controls_tab(
				'ekit_toggle_title_color_active_tab',
				[
					'label' => esc_html__( 'Active', 'elementskit' ),
				]
			);

			$repeater->add_control(
                'ekit_toggle_title_color_active_color',
                [
                    'label' => __( 'Title Color', 'elementskit' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}} .elementskit-toggle-nav-link.active' => 'color: {{VALUE}}',
                        '{{WRAPPER}} {{CURRENT_ITEM}}.elementskit-switch-nav-link.active' => 'color: {{VALUE}}',
                    ],
                ]
            );

			$repeater->end_controls_tab();

		$repeater->end_controls_tabs();
        
        $repeater->add_control(
            'ekit_toggle_title_is_active',
            [
                'label' => esc_html__('Keep this tab open? ', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' =>esc_html__( 'Yes', 'elementskit' ),
                'label_off' =>esc_html__( 'No', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_toggle_items',
            [
                'label' => esc_html__('Tab content', 'elementskit'),
                'type' => Controls_Manager::REPEATER,
                'separator' => 'before',
                'title_field' => '{{ ekit_toggle_title }}',
                'default' => [
                    [
                        'ekit_toggle_title' => 'Annual',
                    ],
                    [
                        'ekit_toggle_title' => 'Monthly',
                    ]
                ],
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->end_controls_section();

        // Style Control Start

        // --- Switch Container
        $this->start_controls_section(
			'ekit_toggle_switch_container_style_tab',
			[
				'label' => __( 'Switch Container', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_toggle_switch_container_background',
				'label' => __( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-wid-con .elemenetskit-toogle-controls-wraper, {{WRAPPER}} .ekit-wid-con .ekit-custom-control-label',
			]
        );
        
        $this->add_responsive_control(
			'ekit_toggle_switch_container_padding',
			[
				'label' => __( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elemenetskit-toogle-controls-wraper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-custom-control-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .elemenetskit-toggle-indicator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_toggle_switch_container_box_shadow',
				'label' => __( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .elemenetskit-toogle-controls-wraper, {{WRAPPER}} .ekit-wid-con .ekit-custom-control-label',
			]
        );
        
        $this->add_responsive_control(
			'ekit_toggle_switch_container_border_radius',
			[
				'label' => __( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elemenetskit-toogle-controls-wraper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-custom-control-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_toggle_switch_toggle__padding',
			[
				'label' => __( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-custom-control-label:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-toggle-nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
			]
        );
        
        
        $this->add_control(
			'ekit_toggle_switch_container_heading_one',
			[
				'label' => __( 'Switch Toggle', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
        );
        
        $this->add_responsive_control(
			'ekit_toggle_switch_toggle_border_radius',
			[
				'label' => __( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .elemenetskit-toggle-indicator' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-custom-control-label:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_toggle_switch_item_box_shadow',
				'label' => __( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .elemenetskit-toggle-indicator, {{WRAPPER}} .ekit-wid-con .ekit-custom-control-label:before',
			]
		);

        $this->add_responsive_control(
			'ekit_toggle_switch_toggle_2_left',
			[
				'label' => __( 'Left', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'description' => __( 'This control working only active side', 'elementskit' ),
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-slide-toggle input[type=checkbox]:checked+.ekit-custom-control-label:before' => 'left: calc(100% - {{SIZE}}{{UNIT}});',
                ],
                'condition' => [
					'ekit_advanced_toggle_style' => 'switch-style',
				],
			]
		);

        $this->start_controls_tabs(
            'ekit_toggle_switch_toggle_2_normal_and_active_tabs',
            [
                'condition' => [
					'ekit_advanced_toggle_style' => 'switch-style',
				],
            ]
		);
			// Normal
			$this->start_controls_tab(
				'ekit_toggle_switch_toggle_2_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'elementskit' ),
				]
            );
            
            $this->add_control(
                'ekit_toggle_switch_toggle_2_normal_color',
                [
                    'label' => __( 'Toggle Color', 'elementskit' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-wid-con .ekit-custom-control-label' => 'color: {{VALUE}}',
                    ],
                ]
            );
	
			$this->end_controls_tab();

			// Hover
			$this->start_controls_tab(
				'ekit_toggle_switch_toggle_2_active_tab',
				[
					'label' => esc_html__( 'Active', 'elementskit' ),
				]
			);

			$this->add_control(
                'ekit_toggle_switch_toggle_2_active_color',
                [
                    'label' => __( 'Toggle Color', 'elementskit' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-wid-con .ekit-slide-toggle input[type=checkbox]:checked+.ekit-custom-control-label' => 'color: {{VALUE}}',
                    ],
                ]
            );

			$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->end_controls_section();
        
        // ---- Switch Title
        
		$this->start_controls_section(
			'ekit_toggle_switch_content_style_tab',
			[
				'label' => __( 'Content', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_toggle_switch_content_typography',
				'label' => __( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .elementskit-toggle-nav-link, {{WRAPPER}} .ekit-wid-con .elementskit-switch-nav-link',
			]
		);

		$this->end_controls_section();

        $this->insert_pro_message();

    }

    protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {

        $settings = $this->get_settings_for_display();

        extract($settings);

        $toggle_id = uniqid();

        $has_user_defined_active_toggle = false;
        foreach($ekit_toggle_items as $toggle){
            if($toggle['ekit_toggle_title_is_active'] == 'yes'){
                $has_user_defined_active_toggle = true;
            }
        }
        require Handler::get_dir() . 'style/'.$ekit_advanced_toggle_style.'.php';
    }
}
