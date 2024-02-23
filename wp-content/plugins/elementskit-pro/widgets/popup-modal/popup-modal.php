<?php

namespace Elementor;

defined('ABSPATH') || exit;

use \Elementor\ElementsKit_Widget_Popup_Modal_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;
use \ElementsKit_Lite\Modules\Controls\Widget_Area_Utils as Widget_Area_Utils;

class ElementsKit_Widget_Popup_Modal extends Widget_Base
{

    public $base;

    public function get_name()
    {
        return Handler::get_name();
    }

    public function get_title()
    {
        return Handler::get_title();
    }

    public function get_icon()
    {
        return Handler::get_icon();
    }

    public function get_categories()
    {
        return Handler::get_categories();
    }

	public function get_keywords() {
		return Handler::get_keywords();
	}

    public function get_help_url() {
        return 'https://wpmet.com/doc/pop-up-modal/';
    }

    private function control_border($key, $selectors, $config = ['default' => '8', 'unit' => 'px', 'separator' => true, 'heading' => true])
    {
        $condition = isset($config['condition']) ? $config['condition'] : [];
        $selectors = array_map(function ($selector) {return "{{WRAPPER}} " . $selector;}, $selectors);

        if ($config['heading']) {
            // Border heading
            $this->add_control($key, [
                'label' => esc_html__('Border', 'elementskit'),
                'type' => Controls_Manager::HEADING,
                'separator' => $config['separator'] ? 'before' : 'none',
                'condition' => $condition
            ]);
        }

        // Review card border
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name' => $key . '_type',
                'label' => esc_html__('Border Type', 'elementskit'),
                'selector' => implode(',', $selectors),
                'condition' => $condition
            ]
        );

        $new_selectors = array();
        $border_radius = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
        foreach ($selectors as $key) {$new_selectors[$key] = $border_radius;}

        // Review card border radius
        $this->add_control($key . '_radius', [
            'label' => esc_html__('Border Radius', 'elementskit'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => $new_selectors,
            'default' => [
                'top' => $config['default'], 'right' => $config['default'],
                'bottom' => $config['default'], 'left' => $config['default'],
                'unit' => $config['unit'], 'isLinked' => true,
                'condition' => $condition
            ],
        ]);
    }

    private function control_button($name, $selector, $excludes = [], $condition = []) {

        // Typography
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => $name . '_typography',
            'selector' => '{{WRAPPER}} ' . $selector,
            'condition' => $condition
        ]);

        // Tabs
        $this->start_controls_tabs(
            $name . '_tabs', [
                'condition' => $condition
            ]
            
        );

        // Tab Normal
        $this->start_controls_tab(
            $name . '_tab_normal', [
                'label' => esc_html__('Normal', 'elementskit'),
            ]
        );

        // Tab normal background color
        $this->add_group_control(
            Group_Control_Background::get_type(), [
                'name' => $name . '_background_normal',
                'label' => esc_html__('Background', 'elementskit'),
                'types' => ['classic'],
                'selector' => '{{WRAPPER}} ' . $selector,
            ]
        );

        if (!in_array('shadow', $excludes)) {
            // Box shadow
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
                    'name' => $name . "_box_shadow",
                    'label' => __( 'Box Shadow', 'elementskit' ),
                    'selector' => "{{WRAPPER}} $selector"
                ]
            );
        }

        // Tab normal text color
        $this->add_control($name . '_color_normal',
            [
                'label' => esc_html__('Text Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $selector => 'color: {{VALUE}}',
                ],
            ]
        );

        if (!in_array("border", $excludes)) {
            // Border
            $this->control_border($name . '_border', [$selector], [
                'default' => '4', 'unit' => 'px',
                'separator' => false, 'heading' => false,
                'condition' => $condition
            ]);
        }

        $this->end_controls_tab();

        // Tab Hover
        $this->start_controls_tab(
            $name . "_tab_hover", [
                'label' => esc_html__('Hover', 'elementskit'),
            ]
        );

        // Tab hover background color
        $this->add_group_control(
            Group_Control_Background::get_type(), [
                'name' => $name . '_background_hover',
                'label' => esc_html__('Background', 'elementskit'),
                'types' => ['classic'],
                'selector' => '{{WRAPPER}} ' . $selector . ':hover',
            ]
        );

        if (!in_array('shadow', $excludes)) {
            // Box shadow
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
                    'name' => $name . "_box_shadow_hover",
                    'label' => __( 'Box Shadow', 'elementskit' ),
                    'selector' => "{{WRAPPER}} $selector:hover"
                ]
            );
        }

        // Tab hover text color
        $this->add_control($name . '_color_hover',
            [
                'label' => esc_html__('Text Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $selector . ":hover" => 'color: {{VALUE}}',
                ],
            ]
        );

        if (!in_array("border", $excludes)) {
            // Border
            $this->control_border($name . '_border_hover', ["$selector:hover"], [
                'default' => '4', 'unit' => 'px',
                'separator' => false, 'heading' => false,
                'condition' => $condition
            ]);
        }

        $this->end_controls_tab();
        $this->end_controls_tabs();
    }

    private function controls_section($config) {

        // New configs
        $section_config = ['label' => esc_html($config['label'])];

        // Formatting configs
        if (isset($config['tab'])) {
            $section_config['tab'] = $config['tab'];
        }

        if (isset($config['condition'])) {
            $section_config['condition'] = $config['condition'];
        }

        // Start section
        $this->start_controls_section($config['name'] . '_section', $section_config);

        // Call the callback function
        call_user_func(array($this, 'control_section_' . $config['name']));

        // End section
        $this->end_controls_section();

    }

    private function control_section_layout() {
        $this->add_control( 'toggler_type', [
            'label' => esc_html__('Toggler Type', 'elementskit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'button',
            'options' => [
                'button' => esc_html__('Button', 'elementskit'),
                'image' => esc_html__('Image', 'elementskit'),
                'time' => esc_html__('Time', 'elementskit'),
            ],
        ]);

        $this->add_control( 'toggle_after', [
            'label' => esc_html__( 'Toggle After (Seconds)', 'elementskit' ),
            'type' => Controls_Manager::NUMBER,
            'min' => 1, 'max' => 120, 'step' => 1,
            'default' => 3,
            'condition' => [
                'toggler_type' => 'time'
            ]
        ]);

		$this->add_control('enable_cookie_consent', [
            'label'                 => esc_html__('Enable Cookie Consent', 'elementskit'),
            'type'                  => Controls_Manager::SWITCHER,
            'return_value'          => 'yes',
            'frontend_available'    => true,
            'condition'             => [
                'toggler_type'  => 'time',
            ]
        ]);

        $this->add_control( 'popup_type', [
            'label' => esc_html__('Popup Show Type', 'elementskit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'inside',
            'options' => [
                'inside' => esc_html__('Modal', 'elementskit'),
                'outside' => esc_html__('Slide', 'elementskit'),
            ],
        ]);

        $this->add_control( 'popup_position', [
            'label' => esc_html__('Popup Position', 'elementskit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'middle center',
            'options' => [
                'top left' => esc_html__('Top Left', 'elementskit'),
                'top center' => esc_html__('Top Center', 'elementskit'),
                'top right' => esc_html__('Top Right', 'elementskit'),
                'middle left' => esc_html__('Center Left', 'elementskit'),
                'middle center' => esc_html__('Center', 'elementskit'),
                'middle right' => esc_html__('Center Right', 'elementskit'),
                'bottom left' => esc_html__('Bottom Left', 'elementskit'),
                'bottom center' => esc_html__('Bottom Center', 'elementskit'),
                'bottom right' => esc_html__('Bottom Right', 'elementskit')
            ],
            'condition' => [ 'popup_type' => 'inside' ]
        ]);

        $this->add_control( 'popup_position_outside', [
            'label' => esc_html__('Appear From', 'elementskit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'left',
            'options' => [
                'left' => esc_html__('Left', 'elementskit'),
                'top' => esc_html__('Top', 'elementskit'),
                'right' => esc_html__('Right', 'elementskit'),
                'bottom' => esc_html__('Bottom', 'elementskit')
            ],
            'condition' => [ 'popup_type' => 'outside' ]
        ]);

        $this->add_control( 'popup_widget_content', [
            'label' => esc_html__('Content', 'elementskit'),
            'type' => ElementsKit_Controls_Manager::WIDGETAREA,
            'label_block' => true,
        ]);
    }

    private function control_section_popup() {

        $this->add_responsive_control('popup_width', [
            'label' => esc_html__('Width', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vw'],
            'range' => [
                'px' => ['min' => 100, 'max' => 1920, 'step' => 16]
            ],
            'default' => ['unit' => 'px', 'size' => 700],
            'mobile_default' => ['unit' => '%', 'size' => 90],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup__content' => 'width: {{SIZE}}{{UNIT}}',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'popup_type',
                        'operator' => '==',
                        'value' => 'inside'    
                    ],
                    [
                        'terms' => [
                            [
                                'name' => 'popup_type',
                                'operator' => '==',
                                'value' => 'outside'
                            ],
                            [
                                'name' => 'popup_position_outside',
                                'operator' => 'in',
                                'value' => ['left', 'right']
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $this->add_responsive_control('popup_width_otb', [
            'label' => esc_html__('Width', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vw'],
            'range' => [
                'px' => ['min' => 100, 'max' => 1920, 'step' => 16]
            ],
            'default' => ['unit' => '%', 'size' => 100],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup__content' => 'width: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
                'popup_type' => 'outside',
                'popup_position_outside' => ['top', 'bottom']
            ]
        ]);

        $this->add_responsive_control('popup_max_height', [
            'label' => esc_html__('Maximum Height', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [
                'px' => ['min' => 64, 'max' => 1000, 'step' => 16],
                'vh' => ['min' => 10, 'max' => 100, 'step' => 1],
            ],
            'default' => ['unit' => 'vh', 'size' => 90],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup__content' => 'max-height: {{SIZE}}{{UNIT}}'
            ],
            'condition' => [
                'popup_type' => 'inside'
            ]
        ]);

        $this->add_responsive_control('popup_height', [
            'label' => esc_html__('Height', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [
                'px' => ['min' => 64, 'max' => 1000, 'step' => 16],
                'vh' => ['min' => 10, 'max' => 100, 'step' => 1],
            ],
            'default' => ['unit' => 'vh', 'size' => 100],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup__content' => 'height: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
                'popup_type' => 'outside',
                'popup_position_outside' => ['left', 'right']
            ]
        ]);

        $this->add_responsive_control('popup_height_top_bottom', [
            'label' => esc_html__('Height', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [
                'px' => ['min' => 64, 'max' => 1000, 'step' => 16],
                'vh' => ['min' => 10, 'max' => 100, 'step' => 1],
            ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup__content' => 'height: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
                'popup_type' => 'outside',
                'popup_position_outside' => ['top', 'bottom']
            ]
        ]);

        // Show overlay
        $this->add_control( 'show_overlay', [
            'label'        => esc_html__('Show Overlay', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes'
        ]);

        // Close button
        $this->add_control( 'show_close_btn', [
            'label'        => esc_html__('Close Icon', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes'
        ]);

        // Header
        $this->add_control('show_header', [
            'label'        => esc_html__('Show Header', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes'
        ]);

        // Footer
        $this->add_control('show_footer', [
            'label'        => esc_html__('Show Footer', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes'
        ]);

        $this->add_control('popup_position_heading', [
            'label' => esc_html__('Position', 'elementskit'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'popup_type' => 'inside',
                'popup_position!' => 'middle center'
            ]
        ]);

        $this->add_responsive_control('vertical_top_position', [
            'label' => esc_html__('Vertical Position', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'default' => [ 'size' => 32, 'unit' => 'px' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__content' => 
                    'top: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
                'popup_type' => 'inside',
                'popup_position' => ['top left', 'top center', 'top right']
            ]
        ]);

        $this->add_responsive_control('vertical_bottom_position', [
            'label' => esc_html__('Vertical Position', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'default' => [ 'size' => 32, 'unit' => 'px' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__content' => 
                    'bottom: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
                'popup_type' => 'inside',
                'popup_position' => ['bottom left', 'bottom center', 'bottom right']
            ]
        ]);

        $this->add_responsive_control('horizontal_left_position', [
            'label' => esc_html__('Horizontal Position', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'default' => [ 'size' => 32, 'unit' => 'px' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__content' => 
                    'left: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
                'popup_type' => 'inside',
                'popup_position' => ['top left', 'middle left', 'bottom left']
            ]
        ]);

        $this->add_responsive_control('horizontal_right_position', [
            'label' => esc_html__('Horizontal Position', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'default' => [ 'size' => 32, 'unit' => 'px' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__content' => 
                    'right: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
                'popup_type' => 'inside',
                'popup_position' => ['top right', 'middle right', 'bottom right']
            ]
        ]);

        $this->add_control( 'inside_animation', [
            'label' => esc_html__('Entrance Animation', 'elementskit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'fade-in-up',
            'separator' => 'before',
            'label_block' => true,
            'options' => [
                // Fade
                'fade-in-up' => esc_html__('Default', 'elementskit'),
                'fadeIn' => esc_html__('Fade In', 'elementskit'),
                'fadeInUp' => esc_html__('Fade In Up', 'elementskit'),
                'fadeInLeft' => esc_html__('Fade In Left', 'elementskit'),
                'fadeInRight' => esc_html__('Fade In Right', 'elementskit'),
                'fadeInDown' => esc_html__('Fade In Down', 'elementskit'),
                // Zoom
                'zoomIn' => esc_html__('Zoom In', 'elementskit'),
                'zoomInUp' => esc_html__('Zoom In Up', 'elementskit'),
                'zoomInLeft' => esc_html__('Zoom In Left', 'elementskit'),
                'zoomInRight' => esc_html__('Zoom In Right', 'elementskit'),
                'zoomInDown' => esc_html__('Zoom In Down', 'elementskit'),
                // Bounce
                'bounceIn' => esc_html__('Bounce In', 'elementskit'),
                'bounceInUp' => esc_html__('Bounce In Up', 'elementskit'),
                'bounceInLeft' => esc_html__('Bounce In Left', 'elementskit'),
                'bounceInRight' => esc_html__('Bounce In Right', 'elementskit'),
                'bounceInDown' => esc_html__('Bounce In Down', 'elementskit'),
                // Rotate
                'rotateIn' => esc_html__('Rotate In', 'elementskit'),
                'rotateInUpLeft' => esc_html__('Rotate In Up Left', 'elementskit'),
                'rotateInUpRight' => esc_html__('Rotate In Up Right', 'elementskit'),
                'rotateInDownLeft' => esc_html__('Rotate In Down Left', 'elementskit'),
                'rotateInDownRight' => esc_html__('Rotate In Down Right', 'elementskit'),
            ],
            'condition' => [
                'popup_type' => 'inside'
            ]
        ]);

        $this->add_control( 'inside_animation_duration', [
            'label' => esc_html__('Duration', 'elementskit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'normal',
            'options' => [
                'slow' => esc_html__('Slow', 'elementskit'),
                'normal' => esc_html__('Normal', 'elementskit'),
                'fast' => esc_html__('Fast', 'elementskit'),
                //'custom' => esc_html__('Custom', 'elementskit'),
            ],
            'condition' => [
                'popup_type' => 'inside'
            ]
        ]);

    }

    private function control_section_popup_style(){

        // Background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'popup_background',
				'label'    => esc_html__('Popup Background', 'elementskit'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ekit-popup__content',
			]
		);

        // Box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name' => 'popup_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-popup__content'
			]
        );
        
        $this->add_responsive_control( 'popup_padding', [
            'label' =>esc_html__( 'Padding', 'elementskit' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup__content' => 
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        // Border
		$this->control_border( 'popup_border', [ 
			'.ekit-popup-modal__content' ], [ 
			'default' => '4', 
			'unit' => 'px', 
			'separator' => true, 
			'heading' => true 
		]);
    }

    private function control_section_overlay_style(){

        // ekit_behance_feed_widget_background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'overlay_background',
				'label'    => esc_html__('Overlay Background', 'elementskit'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ekit-popup-modal .ekit-popup-modal__overlay',
			]
		);

    }

    private function control_section_close_button(){

        $this->add_responsive_control( 'close_btn_size', [
            'label'           => esc_html__('Size', 'elementskit'),
            'type'            => Controls_Manager::SLIDER,
            'size_units'      => ['px','em'],
            'range'           => [
                'px' => [ 'min'  => 12, 'max'  => 256, 'step' => 4 ],
            ],
            'default'         => [ 'size' => 20, 'unit' => 'px' ],
            'selectors'       => [
                '{{WRAPPER}} .ekit-popup-modal__close' => 
                    'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
            ]
        ]);

        $this->add_responsive_control( 'close_btn_font_size', [
            'label'           => esc_html__('Icon Size', 'elementskit'),
            'type'            => Controls_Manager::SLIDER,
            'size_units'      => ['px','em'],
            'range'           => [
                'px' => [ 'min'  => 0, 'max'  => 96, 'step' => 2 ],
                'em' => [ 'min'  => 0, 'max'  => 6, 'step' => 0.2 ]
            ],
            'devices'         => ['desktop', 'tablet', 'mobile'],
            'default'         => [ 'size' => 28, 'unit' => 'px' ],
            'selectors'       => [
                '{{WRAPPER}} .ekit-popup-modal__close i' => 'font-size: {{SIZE}}{{UNIT}};',
            ]
        ]);

        $this->control_border('close_btn_border', ['.ekit-popup-modal__close'], [
            'default' => '2', 'unit' => 'px',
            'separator' => false, 'heading' => false,
        ]);

        // Tabs
		$this->start_controls_tabs( 'close_btn_tabs', ['separator' => 'before'] );

		// Tab Normal 
        $this->start_controls_tab(
            'close_btn_tab_normal', [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
		);

        // Tab normal background color
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'close_btn_background_normal',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic'],
				'selector' => '{{WRAPPER}} .ekit-popup-modal__close',
			]
		);

		// Tab normal text color
		$this->add_control( 'close_btn_color_normal', [
            'label'     => esc_html__('Color', 'elementskit'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__close i' => 'color: {{VALUE}}',
            ],
        ]);

		$this->add_control( 'close_btn_border_color_normal', [
            'label'     => esc_html__('Border Color', 'elementskit'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__close' => 'border-color: {{VALUE}}',
            ],
        ]);

		$this->end_controls_tab();

		// Tab Hover
        $this->start_controls_tab( 
            'close_btn_tab_hover', [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
		);

        // Tab hover background color
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'close_btn_background_hover',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic'],
				'selector' => '{{WRAPPER}} .ekit-popup-modal__close:hover',
			]
		);

		// Tab hover text color
		$this->add_control( 'close_btn_color_hover', [
            'label'     => esc_html__('Color', 'elementskit'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__close:hover i' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control( 'close_btn_border_color_normal_hover', [
            'label'     => esc_html__('Border Color', 'elementskit'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__close:hover' => 'border-color: {{VALUE}}',
            ],
        ]);

		$this->end_controls_tab();
        $this->end_controls_tabs();

    }

    protected function register_controls() {

        // Layout
        $this->controls_section([
            'name' => 'layout',
            'label' => 'Layout',
        ]);

        $this->start_controls_section(
			'toggler_btn_section', [
                'label' => esc_html__('Toggler Button', 'elementskit'),
                'condition' => [
                    'toggler_type' => 'button'
                ]
            ]
        );
        
		$this->add_control( 'toggler_btn_text', [
            'label' =>esc_html__( 'Label', 'elementskit' ),
            'type' => Controls_Manager::TEXT,
            'default' =>esc_html__( 'OPEN MODAL', 'elementskit' ),
            'dynamic' => [
                'active' => true,
            ],
        ]);

		$this->add_responsive_control( 'toggler_btn_alignment', [
            'label' =>esc_html__( 'Alignment', 'elementskit' ),
            'type' => Controls_Manager::CHOOSE,
            'separator' => 'after',
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
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__toggler-wrapper' => 'text-align: {{VALUE}};',
            ],
		]);
		
		$this->add_responsive_control( 'toggler_btn_width', [
            'label'			=> esc_html__( 'Width (%)', 'elementskit' ),
            'type'			=> Controls_Manager::SLIDER,
            'selectors'		=> [
                '{{WRAPPER}} .ekit-popup-modal-toggler' => 'width: {{SIZE}}%;',
            ]
        ]);

		$this->add_control( 'show_toggler_btn_icon', [
            'label' => esc_html__('Show Icon', 'elementskit'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'no'
        ]);

		$this->add_control( 'toggler_btn_icons', [
            'label' =>esc_html__( 'Icon', 'elementskit' ),
            'type' => Controls_Manager::ICONS,
            'label_block' => true,
            'condition'	=> [
                'show_toggler_btn_icon'	=> 'yes'
            ]
        ]);

        $this->add_control('toggle_btn_icon_position', [
            'label' =>esc_html__( 'Icon Position', 'elementskit' ),
            'type' => Controls_Manager::SELECT,
            'default' => 'left',
            'options' => [
                'left' =>esc_html__( 'Before', 'elementskit' ),
                'right' =>esc_html__( 'After', 'elementskit' ),
            ],
            'condition'	=> [
                'show_toggler_btn_icon'	=> 'yes'
            ]
        ]);

        $this->add_responsive_control('toggle_btn_icon_size', [
            'label' => esc_html__('Icon Size', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'default' => [ 'size' => 14, 'unit' => 'px' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-btn.ekit-popup__toggler i' => 
                    'font-size: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
				'show_toggler_btn_icon' => 'yes'
            ]
		]);

        $this->add_responsive_control('toggle_btn_icon_spacing_right', [
            'label' => esc_html__('Spacing', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'default' => [ 'size' => 8, 'unit' => 'px' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal-toggler i' => 
                    'margin-right: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
				'show_toggler_btn_icon' => 'yes',
                'toggle_btn_icon_position' => 'left'
            ]
        ]);

        $this->add_responsive_control('toggle_btn_icon_spacing_left', [
            'label' => esc_html__('Spacing', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'default' => [ 'size' => 8, 'unit' => 'px' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal-toggler i' => 
                    'margin-left: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
				'show_toggler_btn_icon' => 'yes',
                'toggle_btn_icon_position' => 'right'
            ]
        ]);

		$this->end_controls_section();
        
        $this->start_controls_section( 'toggler_img_style', [
            'label' => esc_html__('Toggler Image', 'elementskit'),
            'condition' => [
                'toggler_type' => 'image'
            ]
        ]);

        $this->add_control( 'toggler_image', [
            'label' => esc_html__('Choose Image', 'elementskit'),
            'type' => Controls_Manager::MEDIA,
            'default' => [
                'url' => Utils::get_placeholder_image_src(),
                'id'    => -1
            ],
			'dynamic' => [
				'active' => true,
			],
        ]);

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'toggler_img_size',
                'default' => 'medium',
                'separator' => 'none',
            ]
        );

        $this->add_responsive_control( 'toggler_img_alignment', [
            'label' =>esc_html__( 'Alignment', 'elementskit' ),
            'type' => Controls_Manager::CHOOSE,
            'separator' => 'after',
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
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__toggler-wrapper' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
        
        $this->start_controls_section( 'toggler_img_style_section', [
            'label' => esc_html__('Toggler Image', 'elementskit'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'toggler_type' => 'image'
            ]
        ]);

        $this->add_responsive_control('toggler_image_width', [
            'label' => esc_html__('Width', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vw'],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__toggler-wrapper img' => 
                    'width: {{SIZE}}{{UNIT}}',
            ]
        ]);

        $this->add_responsive_control('toggler_image_height', [
            'label' => esc_html__('Height', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vw'],
            'default' => [ 'size' => 300, 'unit' => 'px' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__toggler-wrapper img' => 
                    'height: {{SIZE}}{{UNIT}}',
            ]
        ]);

        // Box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name' => 'toggler_image_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-popup-modal__toggler-wrapper img'
			]
        );

        $this->control_border('toggler_image_border', [
            '.ekit-popup-modal__toggler-wrapper img'
        ]);

        $this->end_controls_section();

        $this->start_controls_section( 
            'toggler_btn_style_section', [
                'label' => esc_html__('Toggler Button', 'elementskit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'toggler_type' => 'button'
                ]
            ]
        );

		$this->add_responsive_control( 'toggler_btn_padding', [
            'label' =>esc_html__( 'Padding', 'elementskit' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal-toggler' => 
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->control_button( 
            'toggler_button', 
            '#ekit-popup-modal-toggler'
        );
        $this->end_controls_section();
        
        // Layout
        $this->controls_section([
            'name' => 'popup',
            'label' => 'Popup',
        ]);

        /**
         * Start Content Section
         */
        $this->start_controls_section( 'content_section', [
            'label' => esc_html__('Content', 'elementskit')
        ]);

        $this->add_control( 'popup_enable_template', [
            'label'   => esc_html__('Enable Template', 'elementskit'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'no',
        ]);

        $this->add_control( 'popup_content', [
            'label'     => esc_html__( 'Content', 'elementskit' ),
            'type'      => Controls_Manager::WYSIWYG,
            'default'	    => esc_html__( 'We know your demands for building a perfect website. You will find all the functionalities of your imagination here.', 'elementskit' ),
            'condition' => [
                'popup_enable_template!' => 'yes'
			],
			'dynamic' => [
				'active' => true,
			],
        ]);

        $this->end_controls_section();
        // End Content Section


        /**
         * Start Overlay Section
         */
        $this->start_controls_section( 'overlay_section', [
            'label' => esc_html__('Overlay', 'elementskit'),
            'condition' => [
                'show_overlay' => 'yes'
            ]
        ]);

        $this->add_control( 'close_onclick_overlay', [
            'label'        => esc_html__('Close Popup OnClick Overlay', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes'
        ]);

        $this->end_controls_section();
        // End Overlay Section
        
        /**
         * Start Close Button Section
         */
        $this->start_controls_section( 'close_btn_section', [
            'label' => esc_html__('Close Icon', 'elementskit'),
            'condition' => [
                'show_close_btn' => 'yes'
            ]
        ]);

        $this->add_control( 'close_btn_position', [
            'label' => esc_html__('Position', 'elementskit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'popup-top-right',
            'options' => [
                'popup-top-right' => esc_html__('Popup Top Right', 'elementskit'),
                'popup-top-left' => esc_html__('Popup Top Left', 'elementskit'),
                'window-top-right' => esc_html__('Window Top Right', 'elementskit'),
                'window-top-left' => esc_html__('Wondow Top Left', 'elementskit')
            ],
        ]);

        // Vertical position
        $this->add_responsive_control( 'close_btn_vertical_position', [
            'label'           => esc_html__('Vertical Position', 'elementskit'),
            'type'            => Controls_Manager::SLIDER,
            'size_units'      => ['px','em'],
            'range'           => [ 'px' => [ 'min'  => -256, 'max'  => 256, 'step' => 2 ] ],
            'selectors'       => [
                '{{WRAPPER}} .ekit-popup-modal__close' => 'top: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'close_btn_position' => ['popup-top-left', 'popup-top-right'] ]
        ]);

        $this->add_responsive_control( 'close_btn_vertical_position_window', [
            'label'           => esc_html__('Vertical Position', 'elementskit'),
            'type'            => Controls_Manager::SLIDER,
            'size_units'      => ['px','em'],
            'range'           => [ 'px' => [ 'min'  => 0, 'max'  => 256, 'step' => 2 ] ],
            'selectors'       => [
                '{{WRAPPER}} .ekit-popup-modal__close' => 'top: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'close_btn_position' => ['window-top-left', 'window-top-right'] ]
        ]);

        // Horizontal position
        $this->add_responsive_control( 'close_btn_horizontal_position_left', [
            'label'           => esc_html__('Horizontal Position', 'elementskit'),
            'type'            => Controls_Manager::SLIDER,
            'size_units'      => ['px','em'],
            'default'         => [ 'size' => 32, 'unit' => 'px' ],
            'range'           => [ 'px' => [ 'min'  => -256, 'max'  => 256, 'step' => 2 ] ],
            'selectors'       => [
                '{{WRAPPER}} .ekit-popup-modal__close' => 'left: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'close_btn_position' => 'popup-top-left' ]
        ]);

        $this->add_responsive_control( 'close_btn_horizontal_position_window_left', [
            'label'           => esc_html__('Horizontal Position', 'elementskit'),
            'type'            => Controls_Manager::SLIDER,
            'size_units'      => ['px','em'],
            'default'         => [ 'size' => 32, 'unit' => 'px' ],
            'range'           => [ 'px' => [ 'min'  => 0, 'max'  => 256, 'step' => 2 ] ],
            'selectors'       => [
                '{{WRAPPER}} .ekit-popup-modal__close' => 'left: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'close_btn_position' => 'window-top-left' ]
        ]);

        $this->add_responsive_control( 'close_btn_horizontal_position_right', [
            'label'           => esc_html__('Horizontal Position', 'elementskit'),
            'type'            => Controls_Manager::SLIDER,
            'size_units'      => ['px','em'],
            'range'           => [ 'px' => [ 'min'  => -256, 'max'  => 256, 'step' => 2 ] ],
            'default'         => [ 'size' => 32, 'unit' => 'px' ],
            'selectors'       => [
                '{{WRAPPER}} .ekit-popup-modal__close' => 'right: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'close_btn_position' => 'popup-top-right' ]
        ]);

        $this->add_responsive_control( 'close_btn_horizontal_position_window_right', [
            'label'           => esc_html__('Horizontal Position', 'elementskit'),
            'type'            => Controls_Manager::SLIDER,
            'size_units'      => ['px','em'],
            'range'           => [ 'px' => [ 'min'  => 0, 'max'  => 256, 'step' => 2 ] ],
            'default'         => [ 'size' => 32, 'unit' => 'px' ],
            'selectors'       => [
                '{{WRAPPER}} .ekit-popup-modal__close' => 'right: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'close_btn_position' => 'window-top-right' ]
        ]);

        // Start Close Icon
        $this->add_control('close_btn_icon_heading', [
            'label' => esc_html__('Close Icon', 'elementskit'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control( 'close_button_icons', [
            'label' => esc_html__( 'Icon', 'elementskit' ),
            'type' => Controls_Manager::ICONS,
            'label_block' => true,
            'default' => [
                'value' => 'icon icon-cross',
                'library' => 'ekiticons',
            ]
        ]);

        $this->end_controls_section();
        // End Close Button Section

        /**
         * Start Header Section
         */
        $this->start_controls_section( 'header_section', [
            'label' => esc_html__('Header', 'elementskit'),
            'condition' => [
                'show_header' => 'yes'
            ]
        ]);

        $this->add_control( 'popup_title', [
            'label'			 => esc_html__( 'Title Text', 'elementskit' ),
            'type'			 => Controls_Manager::TEXT,
            'label_block'	 => true,
            'default'	    => esc_html__( 'This is popup title', 'elementskit' ),
			'dynamic'		=> [
				'active' => true,
			],
        ]);
        
        $this->add_control('show_subtitle', [
            'label'        => esc_html__('Show Subtitle', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes'
        ]);
            
        $this->add_control( 'popup_subtitle', [
            'label'			 => esc_html__( 'Subtitle Text', 'elementskit' ),
            'type'			 => Controls_Manager::TEXT,
            'label_block'	 => true,
            'condition'     => [
                'show_subtitle' => 'yes',
            ],
            'default'	    => esc_html__( 'You don\'t need to waste your time and money anymore.', 'elementskit' ),
			'dynamic' 		=> [
				'active' => true,
			],
        ]);

        $this->add_control('show_header_divider', [
            'label'        => esc_html__('Show Divider', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes'
        ]);

        $this->end_controls_section();
        // End Header Section

        /**
         * Start Footer Section
         */
        $this->start_controls_section( 'footer_section', [
            'label' => esc_html__('Footer', 'elementskit'),
            'condition' => [
                'show_footer' => 'yes'
            ]
		]);
		
		$this->add_control('show_footer_divider', [
            'label'        => esc_html__('Footer Divider', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes'
        ]);

        $this->add_control('buttons_heading', [
            'label' => esc_html__('Buttons', 'elementskit'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        // Tabs
        $this->start_controls_tabs('footer_btn_tabs');

        // Tab Normal
        $this->start_controls_tab(
            'footer_cta_tab', [
                'label' => esc_html__('CTA', 'elementskit'),
            ]
        );

        $this->add_control( 'show_footer_cta', [
            'label'        => esc_html__('Show', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes'
        ]);

        $this->add_control( 'footer_cta_text', [
            'label' =>esc_html__( 'Label', 'elementskit' ),
            'type' => Controls_Manager::TEXT,
            'default' =>esc_html__( 'Buy Now', 'elementskit' ),
            'condition' => [
                'show_footer_cta' => 'yes'
			],
			'dynamic' => [
				'active' => true,
			],
        ]);

        $this->add_control( 'footer_cta_type', [
            'label' => esc_html__('Variant', 'elementskit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'ekit-popup-btn__filled',
            'options' => [
                'ekit-popup-btn__filled' => esc_html__('Filled', 'elementskit'),
                'ekit-popup-btn__outlined' => esc_html__('Outlined', 'elementskit'),
                'ekit-popup-btn__text' => esc_html__('Text', 'elementskit'),
            ],
            'condition' => [
                'show_footer_cta' => 'yes'
            ]
        ]);

        $this->add_control(
            'footer_cta_url', [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__( 'Url', 'elementskit' ),
                'default' =>  esc_html__('https://www.google.com/', 'elementskit'),
                'label_block' => true,
                'condition' => [
                    'show_footer_cta' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control('footer_cta_new_tab', [
            'label'        => esc_html__('Open in new tab', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition' => [
                'show_footer_cta' => 'yes'
            ]
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab(
            "footer_close_tab", [
                'label' => esc_html__('CLOSE', 'elementskit'),
            ]
        );

        $this->add_control( 'show_footer_close', [
            'label'        => esc_html__('Show', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes'
        ]);

        $this->add_control( 'footer_close_text', [
            'label' =>esc_html__( 'Label', 'elementskit' ),
            'type' => Controls_Manager::TEXT,
            'default' =>esc_html__( 'Close', 'elementskit' ),
            'condition' => [
                'show_footer_close' => 'yes'
			],
			'dynamic' => [
				'active' => true,
			],
        ]);

        $this->add_control( 'footer_close_type', [
            'label' => esc_html__('Variant', 'elementskit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'ekit-popup-btn__text',
            'options' => [
                'ekit-popup-btn__filled' => esc_html__('Filled', 'elementskit'),
                'ekit-popup-btn__outlined' => esc_html__('Outlined', 'elementskit'),
                'ekit-popup-btn__text' => esc_html__('Text', 'elementskit'),
            ],
            'condition' => [
                'show_footer_close' => 'yes'
            ]
        ]);

        $this->end_controls_tab();
        $this->end_controls_tabs();

		$this->add_responsive_control( 'footer_buttons_alignment', [
            'label' =>esc_html__( 'Alignment', 'elementskit' ),
			'type' => Controls_Manager::CHOOSE,
            'default' => 'flex-end',
            'separator'    => 'before',
            'description' => esc_html__('it will work reverse if `Reverse Order` is enabled.', 'elementskit'),
            'options' => [
                'flex-start'    => [
                    'title' =>esc_html__( 'Left', 'elementskit' ),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' =>esc_html__( 'Center', 'elementskit' ),
                    'icon' => 'eicon-text-align-center',
                ],
                'flex-end' => [
                    'title' =>esc_html__( 'Right', 'elementskit' ),
                    'icon' => 'eicon-text-align-right',
                ],
                'space-between' => [
                    'title' =>esc_html__( 'Space Between', 'elementskit' ),
                    'icon' => 'eicon-h-align-stretch',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__actions' => 'justify-content: {{VALUE}};',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'show_footer_cta',
                        'operator' => '==',
                        'value' => 'yes'    
                    ],
                    [
                        'name' => 'show_footer_close',
                        'operator' => '==',
                        'value' => 'yes'    
                    ],
                ]
            ]
		]);

        $this->add_control('footer_btn_reverse_order', [
            'label'        => esc_html__('Reverse Order', 'elementskit'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition' => [
                'show_footer_cta' => 'yes',
                'show_footer_close' => 'yes',
            ]
        ]);

        $this->add_responsive_control('footer_btns_spacing', [
            'label' => esc_html__('Spacing', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vw'],
            'range' => [
                'px' => ['min' => 0, 'max' => 64, 'step' => 1]
            ],
            'default' => ['unit' => 'px', 'size' => 4],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-footer__close' => 'margin-right: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
                'footer_buttons_alignment!' => 'space-between',
                'footer_btn_reverse_order!' => 'yes',
                'show_footer_cta' => 'yes',
                'show_footer_close' => 'yes',
            ]
        ]);

        $this->add_responsive_control('footer_btns_spacing_reverse', [
            'label' => esc_html__('Spacing', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vw'],
            'range' => [
                'px' => ['min' => 0, 'max' => 64, 'step' => 1]
            ],
            'default' => ['unit' => 'px', 'size' => 4],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-footer__close' => 'margin-left: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
                'footer_buttons_alignment!' => 'space-between',
                'footer_btn_reverse_order' => 'yes',
                'show_footer_cta' => 'yes',
                'show_footer_close' => 'yes',
            ]
        ]);

        $this->end_controls_section();
        // End Footer Section

        // Popup
        $this->controls_section([
            'name' => 'popup_style',
            'label' => esc_html__('Popup','elementskit'),
            'tab' => Controls_Manager::TAB_STYLE
		]);
		
		/**
		 * Start Header style section
		 */
		$this->start_controls_section( 'header_style_section', [
			'label' => esc_html__('Header', 'elementskit'),
			'tab' => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_header' => 'yes'
			]
		]);

		$this->add_group_control(
            Group_Control_Background::get_type(), [
                'name' => 'header_background',
                'label' => esc_html__('Background', 'elementskit'),
                'selector' => '{{WRAPPER}} .ekit-popup__header'
            ]
        );

		$this->add_responsive_control( 'header_padding', [
            'label' =>esc_html__( 'Padding', 'elementskit' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup__header' => 
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('header_border_radius', [
            'label' => esc_html__('Border Radius', 'elementskit'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                "{{WRAPPER}} .ekit-popup__header" => 
                    'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
            'default' => [
                'top' => 4, 'right' => 4,
                'bottom' => 0, 'left' => 0,
                'unit' => 'px', 'isLinked' => false,
            ],
        ]);

		$this->add_control('title_heading', [
            'label' => esc_html__('Title', 'elementskit'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before'
        ]);

		$this->add_control( 'title_color', [
			'label'     => esc_html__('Text Color', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .ekit-popup__title' => 'color: {{VALUE}}',
			]
		]);

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'     => 'title_typography',
                'label'    => esc_html__('Typography', 'elementskit'),
                'selector' => '{{WRAPPER}} .ekit-popup__title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(), [
                'name' => 'title_text_shadow',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-popup__title'
            ]
        );

        $this->add_responsive_control( 'title_margin', [
            'label'          => esc_html__('Margin', 'elementskit'),
            'type'           => Controls_Manager::DIMENSIONS,
            'size_units'     => ['px', '%', 'em'],
            'default'        => [
                'top' => '0', 'right' => '0',
                'bottom' => '8', 'left' => '0',
                'unit' => 'px', 'isLinked' => false,
            ],
            'selectors'      => [ 
                '{{WRAPPER}} .ekit-popup__title' => 
                    'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
        ]);

		$this->add_control('subtitle_heading', [
            'label' => esc_html__('Sub Title', 'elementskit'),
            'type' => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
                'show_subtitle' => 'yes'
            ]
        ]);

        $this->add_control( 'subtitle_color', [
			'label'     => esc_html__('Text Color', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .ekit-popup__subtitle' => 'color: {{VALUE}}',
			],
            'condition' => [
                'show_subtitle' => 'yes'
            ]
		]);

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'     => 'subtitle_typography',
                'label'    => esc_html__('Typography', 'elementskit'),
                'selector' => '{{WRAPPER}} .ekit-popup__subtitle',
                'condition' => [
                    'show_subtitle' => 'yes'
                ]
            ]
        );

		$this->add_control('header_divider_heading', [
            'label' => esc_html__('Divider', 'elementskit'),
            'type' => Controls_Manager::HEADING,
            'condition' => [ 'show_header_divider' => 'yes' ],
			'separator' => 'before',
		]);
		
		$this->add_responsive_control('header_divider_height', [
            'label' => esc_html__('Height', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'default' => [ 'size' => 1, 'unit' => 'px' ],
			'condition' => [ 'show_header_divider' => 'yes' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup__header' => 
                    'border-bottom-width: {{SIZE}}{{UNIT}}',
            ],
		]);
		
		$this->add_control( 'header_divider_color', [
			'label'     => esc_html__('Color', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'condition' => [ 'show_header_divider' => 'yes' ],
			'selectors' => [
				'{{WRAPPER}} .ekit-popup__header' => 'border-bottom-color: {{VALUE}}',
			]
		]);

		$this->end_controls_section();
        // End Header Section

        /**
		 * Start Content Style Section
		 */
        $this->start_controls_section(
			'content_style_section', [
				'label' => esc_html__('Content', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE
            ]
        );

		$this->add_group_control(
            Group_Control_Background::get_type(), [
                'name' => 'content_background',
                'label' => esc_html__('Background', 'elementskit'),
                'selector' => '{{WRAPPER}} .ekit-popup-modal__body'
            ]
        );

        $this->add_control( 'content_text_color', [
			'label'     => esc_html__('Text Color', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .ekit-popup-modal__body' => 'color: {{VALUE}}',
			],
            'condition' => [
                'popup_enable_template!' => 'yes'
            ]
		]);

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'     => 'content_text_typography',
                'label'    => esc_html__('Typography', 'elementskit'),
                'selector' => '{{WRAPPER}} .ekit-popup-modal__body',
                'condition' => [
                    'popup_enable_template!' => 'yes'
                ]
            ]
        );

		$this->add_responsive_control( 'content_padding', [
            'label' =>esc_html__( 'Padding', 'elementskit' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-modal__body' => 
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

		$this->end_controls_section();
        // End Content Style Section

        // Overlay
        $this->controls_section([
            'name' => 'overlay_style',
            'label' => esc_html__('Overlay', 'elementskit'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'show_overlay' => 'yes'
            ]
        ]);
    
        // Close Button
        $this->controls_section([
            'name' => 'close_button',
            'label' => esc_html__('Close Icon', 'elementskit'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'show_close_btn' => 'yes'
            ]
		]);
				
		/** 
		 * Start Footer Style Section
		 */
		$this->start_controls_section( 'footer_style_section', [
			'label' => esc_html__('Footer', 'elementskit'),
			'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'show_footer' => 'yes'
            ]
		]);

		$this->add_group_control(
            Group_Control_Background::get_type(), [
                'name' => 'footer_background',
                'label' => esc_html__('Background', 'elementskit'),
                'selector' => '{{WRAPPER}} .ekit-popup-footer'
            ]
        );

		$this->add_responsive_control( 'footer_padding', [
            'label' =>esc_html__( 'Padding', 'elementskit' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-footer' => 
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('footer_border_radius', [
            'label' => esc_html__('Border Radius', 'elementskit'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                "{{WRAPPER}} .ekit-popup-footer" => 
                    'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
            'default' => [
                'top' => 0, 'right' => 0,
                'bottom' => 4, 'left' => 4,
                'unit' => 'px', 'isLinked' => false,
            ],
        ]);

		$this->add_control('footer_divider_heading', [
            'label' => esc_html__('Divider', 'elementskit'),
            'type' => Controls_Manager::HEADING,
            'condition' => [ 'show_footer_divider' => 'yes' ],
			'separator' => 'before',
		]);
		
		$this->add_responsive_control('footer_divider_height', [
            'label' => esc_html__('Height', 'elementskit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'default' => [ 'size' => 1, 'unit' => 'px' ],
			'condition' => [ 'show_footer_divider' => 'yes' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-footer' => 
                    'border-top-width: {{SIZE}}{{UNIT}}',
            ],
		]);
		
		$this->add_control( 'footer_divider_color', [
			'label'     => esc_html__('Color', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'condition' => [ 'show_footer_divider' => 'yes' ],
			'selectors' => [
				'{{WRAPPER}} .ekit-popup-footer' => 
					'border-top-color: {{VALUE}}',
			]
        ]);
        
        $this->add_control('footer_cta_style_heading', [
            'label' => esc_html__('CTA Button', 'elementskit'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'show_footer_cta' => 'yes'
            ]
        ]);
        

        $this->add_responsive_control( 'footer_cta_padding', [
            'label' =>esc_html__( 'Padding', 'elementskit' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup__cta' => 
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'show_footer_cta' => 'yes'
            ]
        ]);

        $this->control_button( 'footer_cta', '.ekit-popup__cta', [], [
            'show_footer_cta' => 'yes'
        ]);

        $this->add_control('footer_close_style_heading', [
            'label' => esc_html__('Close Button', 'elementskit'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'show_footer_close' => 'yes'
            ]
        ]);
        

        $this->add_responsive_control( 'footer_close_padding', [
            'label' =>esc_html__( 'Padding', 'elementskit' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .ekit-popup-footer__close' => 
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'show_footer_close' => 'yes'
            ]
        ]);

        $this->control_button( 'footer_close', '.ekit-popup-footer__close', [], [
            'show_footer_close' => 'yes'
        ]);

		$this->end_controls_section();
        // End Header Section

    }

    protected function render()
    {
        echo '<div class="ekit-wid-con" >';
        $this->render_raw();
        echo '</div>';
    }

    protected function render_raw()
    {

        $settings = $this->get_settings_for_display();
        extract($settings);

        $header_divider = $show_header_divider === 'yes' ? __(' ekit-popup-modal__header-with-divider', 'elementskit') : '';
        $footer_divider = $show_footer_divider === 'yes' ?  __(' ekit-popup-modal__footer-with-divider', 'elementskit') : '';
        $toggle_after = (isset($toggle_after) && $toggle_after > 0) ? $toggle_after : 0;
        $close_btn_popup = $show_close_btn == 'yes' && strpos($close_btn_position, 'popup-top') !== false;
        $close_btn_window = $show_close_btn == 'yes' && strpos($close_btn_position, 'window-top') !== false;
        $appear_from = $popup_type == 'outside' ? " appear-from-$popup_position_outside" : "";

        $outside_position_style = "";
        if($popup_type == 'outside'):

            $width = isset($popup_width) 
                ? $popup_width['size'] . $popup_width['unit'] 
                : $popup_width_otb['size'] . $popup_width_otb['unit'];

            if($popup_position_outside == 'top') $outside_position_style = "top:-$width";
            if($popup_position_outside == 'right') $outside_position_style = "right:-$width";
            if($popup_position_outside == 'bottom') $outside_position_style = "bottom:-$width";
            if($popup_position_outside == 'left') $outside_position_style = "left:-$width";
        endif;
        
        $popup_classes = "ekit-popup-modal";
        $popup_classes .= " ekit-popup-modal__$popup_type";
        if(empty($appear_from)) $popup_classes .= " $popup_position";

        $animation_duration = isset($inside_animation_duration) ? $inside_animation_duration : '';

        $content_classes = "ekit-popup-modal__content ekit-popup__content animated";
        $content_classes .= " animated-$animation_duration";
        if(!empty($appear_from)) $content_classes .=  $appear_from;

        $overlay_classes = "ekit-popup-modal__overlay";
        if($show_overlay && $close_onclick_overlay == 'yes') $overlay_classes .= " ekit-popup__close";

        ?>
        <!-- Start Markup -->
            <?php if ($toggler_type == 'image' && !empty($toggler_image['url'] )): ?>
                <div class='ekit-popup-modal__toggler-wrapper'><?php 
                    echo \ElementsKit_Lite\Utils::render(
                        Group_Control_Image_Size::get_attachment_image_html( 
                            $settings, 'toggler_img_size', 'toggler_image'
                        ), [ 
                            'class' => 'ekit-popup-modal-toggler'
                        ]
                    );
                ?></div>
            <?php elseif($toggler_type == 'button'): ?>
                <div class='ekit-popup-modal__toggler-wrapper'>
                    <!-- Button trigger modal -->
                    <button 
                        type="button" 
                        class="elementskit-btn ekit-popup-btn ekit-popup-btn__filled ekit-popup__toggler ekit-popup-modal-toggler whitespace--normal" 
                        data-toggle="modal" 
                        data-target="#ekit-popup-modal"
                        id="ekit-popup-modal-toggler"
                    >
                        <?php if($show_toggler_btn_icon ): ?>
                            <div class='ekit-popup-btn__has-icon'>
                                <?php if($toggle_btn_icon_position == 'left'):
                                    Icons_Manager::render_icon(
                                        $toggler_btn_icons, [ 
                                            'aria-hidden' => 'true' 
                                        ]
                                    );
                                endif; ?>
                                <?php echo esc_html($toggler_btn_text) ?>

                                <?php if($toggle_btn_icon_position == 'right'): 
                                    Icons_Manager::render_icon(
                                        $toggler_btn_icons, [ 
                                            'aria-hidden' => 'true' 
                                        ]
                                    );
                                endif; ?>
                            </div>
                        <?php else:
                            echo esc_html($toggler_btn_text) ;
                        endif; ?>
                    </button>
                </div>
            <?php endif?>

            <!-- Modal -->
            <div 
                class="<?php echo esc_attr($popup_classes) ?>" 
                data-toggleafter="<?php echo esc_attr($toggle_after); ?>"
                data-toggletype="<?php echo esc_attr($toggler_type); ?>"
                data-cookieconsent="<?php if($enable_cookie_consent=='yes') echo esc_attr( $this->get_id() ); ?>"
            >
                <?php if($close_btn_window): ?>
                    <div class="ekit-popup-modal__close ekit-popup__close ekit-popup__close-btn <?php echo esc_attr( $close_btn_position ) ?>">
                        <?php Icons_Manager::render_icon( 
                            $close_button_icons, [
                                'aria-hidden' => 'true'
                            ]
                        )?>
                    </div>
                <?php endif; ?>
                <div
                    class="<?php echo esc_attr( $content_classes ) ?>" 
                    style="<?php echo esc_attr( $outside_position_style ) ?>"
                    data-animation="<?php echo esc_attr( !empty($inside_animation) ? $inside_animation : '' ) ?>"
                >
                    <?php if($close_btn_popup): ?>
                        <div class="ekit-popup-modal__close <?php echo esc_attr( $close_btn_position ) ?>">
                            <?php Icons_Manager::render_icon( 
                                $close_button_icons, [
                                    'aria-hidden' => 'true'
                                ]
                            )?>
                        </div>
                    <?php endif; ?>

                    <!-- Start Header -->
                    <?php if($show_header): ?>
                        <div class="ekit-popup-modal__header ekit-popup__header <?php echo esc_attr( $header_divider ) ?>">
                            <h4 class="ekit-popup-modal__title ekit-popup__title">
                                <?php echo esc_html( $popup_title ) ?>
                            </h4>
                            <?php if($show_subtitle): ?>
                            <p class="ekit-popup-modal__subtitle ekit-popup__subtitle">
                                <?php echo esc_html( $popup_subtitle ) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <!-- End Header -->

                    <!-- Start Body -->
                    <div class="ekit-popup-modal__body ekit-popup__body">
                        <?php if(!empty($popup_enable_template) && $popup_enable_template == 'yes'):
 							echo Widget_Area_Utils::parse(
                                $settings['popup_widget_content'], 
                                'popup',
                                 $this->get_id()
                            );
						else: ?>
							<div class="ekit-popup__raw-content">
								<?php $editor_content = $this->parse_text_editor( $popup_content ); // PHPCS - the main text of a widget should not be escaped.
									echo $editor_content; // PHPCS:ignore WordPress.Security.EscapeOutput
								?>
							</div>
						<?php endif ?>
                    </div>
                    <!-- Emd Body -->

                    <!-- Start Footer -->
                    <?php if($show_footer): ?>
                        <div class="ekit-popup-modal__footer ekit-popup-footer<?php echo esc_attr( $footer_divider ) ?>">
                            <div 
                                class='ekit-popup-modal__actions'
                                style="<?php echo $footer_btn_reverse_order == 'yes' ? 'flex-direction: row-reverse' : '' ?>">
                                <?php if($show_footer_close == "yes"): ?>
                                    <span>
                                        <a 
                                            href="#" 
                                            class="elementskit-btn ekit-popup-btn ekit-popup-footer__close whitespace--normal <?php echo esc_attr( $footer_close_type ) ?>"
                                            ><?php echo esc_html( $footer_close_text ); ?>
                                        </a>
                                    </span>
                                <?php endif; ?>
                                <?php if($show_footer_cta == "yes"): ?>
                                    <span>
                                        <a 
                                            href="<?php echo !empty($footer_cta_url) ? esc_url(  $footer_cta_url ) : '#' ?>" 
                                            class="elementskit-btn ekit-popup-btn ekit-popup__cta whitespace--normal <?php echo esc_attr( $footer_cta_type ) ?>"
                                            target="<?php echo $footer_cta_new_tab == 'yes' ? '_blank' : '' ?>"
                                            ><?php echo esc_html( $footer_cta_text ); ?>
                                        </a>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <!-- End Footer -->
                </div>
                <?php if($show_overlay): ?>
                    <div class="<?php echo esc_attr( $overlay_classes ) ?>"></div>
                <?php endif; ?>
            </div>
        <!-- End Markup -->
    <?php }}?>
