<?php

namespace Elementor;

class ElementsKit_Section_Effect_Controls{
    public function __construct()
    {
        add_action('elementor/element/section/section_advanced/after_section_end', array( $this, 'register_controls' ), 5, 2);

        // Flexbox Container support
		add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 5, 2 );
    }

    public function register_controls($control, $args)
    {
        $control->start_controls_section(
            'ekit_section_parallax',
            [
                'label' => esc_html__('ElementsKit Effects', 'elementskit'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
        $control->add_control(
            'ekit_section_parallax_bg',
            [
                'label' => esc_html__('Background Image Parallax', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementskit'),
                'label_off' => esc_html__('Hide', 'elementskit'),
                'render_type' => 'none',
				'frontend_available' => true,
            ]
        );
        $control->add_control(
            'ekit_section_parallax_bg_speed', [
                'label' => esc_html__('Speed', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'max' => .9,
                'frontend_available' => true,
                'min' => .1,
                'step' => .1,
                'default' => 0.5,
                'condition' => [
                    'ekit_section_parallax_bg' => 'yes',
                ]
            ]
        );

        $control->add_control(
            'ekit_section_parallax_multi',
            array(
                'label' => esc_html__('Multi Item Parallax', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'label_on' => esc_html__('Show', 'elementskit'),
                'label_off' => esc_html__('Hide', 'elementskit'),
            )
        );
        $repeater = new Repeater();
        $repeater->add_control(
            'parallax_style',  [
            'label' => esc_html__('Effect Type', 'elementskit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'animation',
            'options' => [
                'animation' => esc_html__('Css Animation', 'elementskit'),
                'mousemove' => esc_html__('Mouse Move', 'elementskit'),
                'onscroll' => esc_html__('On Scroll', 'elementskit'),
                'tilt' => esc_html__('Parallax Tilt', 'elementskit'),
            ],
        ]
        );
        $repeater->add_control(
            'item_source',
            [
                'label' => esc_html__( 'Item source', 'elementskit' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'toggle' => false,
                'default' => 'image',
                'options' => [
                    'image' => [
                        'title' => 'Image',
                        'icon' => 'eicon-image',
                    ],
                    'shape' => [
                        'title' => 'Shape',
                        'icon' => 'eicon-divider-shape',
                    ],
                ],
                'classes' => 'elementor-control-start-end',
                'render_type' => 'ui',

            ]
        );
        $repeater->add_control(
            'image',[
                'label' => esc_html__('Choose Image', 'elementskit'),
                'type' => Controls_Manager::MEDIA,
                'condition' => [
                    'item_source' => 'image',
                ],
            ]
        );

        $repeater->add_control(
            'shape',  [
                'label' => esc_html__('Choose Shape', 'elementskit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'angle',
                'options' => [
                    'angle' => esc_html__('Shape 1', 'elementskit'),
                    'double_stroke' => esc_html__('Shape 2', 'elementskit'),
                    'fat_stroke' => esc_html__('Shape 3', 'elementskit'),
                    'fill_circle' => esc_html__('Shape 4', 'elementskit'),
                    'round_triangle' => esc_html__('Shape 5', 'elementskit'),
                    'single_stroke' => esc_html__('Shape 6', 'elementskit'),
                    'stroke_circle' => esc_html__('Shape 7', 'elementskit'),
                    'triangle' => esc_html__('Shape 8', 'elementskit'),
                    'zigzag' => esc_html__('Shape 9', 'elementskit'),
                    'zigzag_2' => esc_html__('Shape 10', 'elementskit'),
                    'shape_1' => esc_html__('Shape 11', 'elementskit'),
                    'shape_2' => esc_html__('Shape 12', 'elementskit'),
                    'shape_3' => esc_html__('Shape 13', 'elementskit'),
                    'shape_4' => esc_html__('Shape 14', 'elementskit'),
                ],
                'condition' => [
                    'item_source' => 'shape',
                ]
            ]
        );

        $repeater->add_control(
             'shape_color',
            [
                'label' => esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'item_source' => 'shape',
                ],
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}} .elementskit-parallax-graphic" => 'fill: {{VALUE}}; stroke: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'width_type',
            [
                'label' => esc_html__( 'Width', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__( 'Auto', 'elementskit' ),
                    'custom' => esc_html__( 'Custom', 'elementskit' ),
                ],

            ]
        );

        $repeater->add_responsive_control(
            'custom_width',
            [
                'label' => esc_html__( 'Custom Width', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'width_type' => 'custom',
                ],
                'device_args' => [
                    Core\Breakpoints\Manager::BREAKPOINT_KEY_TABLET => [
                        'condition' => [
                            'ekit_prlx_width_tablet' => [ 'custom' ],
                        ],
                    ],
                    Core\Breakpoints\Manager::BREAKPOINT_KEY_MOBILE => [
                        'condition' => [
                            'ekit_prlx_width_mobile' => [ 'custom' ],
                        ],
                    ],
                ],
                'size_units' => [ 'px', '%', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .elementskit-parallax-graphic' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'source_rotate', [
                'label' => esc_html__('Rotate', 'elementskit'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [
                    'deg' => [
                        'min' => -180,
                        'max' => 180,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'deg',
                    'size' => 0,
                ],
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}} .elementskit-parallax-graphic" => 'transform: rotate({{SIZE}}{{UNIT}})',
                ],

            ]
        );

        $repeater->add_responsive_control(
			'parallax_blur_effect',
			[
				'label' => esc_html__( 'Blur', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => .5,
					],
					'rem' => [
						'min' => 0,
                        'max' => 2,
                        'step' => .1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementskit-parallax-graphic' => 'filter: blur({{SIZE}}{{UNIT}});',
                ],
			]
        );
        
        $repeater->add_responsive_control(
            'pos_x', [
                'label' => esc_html__('Position X (%)', 'elementskit'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%','px'],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 10,
                ],
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}.ekit-section-parallax-layer" => 'left: {{SIZE}}{{UNIT}}',
                ],

            ]
        );

        $repeater->add_responsive_control(
            'pos_y',[
                'label' => esc_html__('Position Y (%)', 'elementskit'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%','px'],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 200,
                    ],
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 10,
                ],
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}.ekit-section-parallax-layer" => 'top: {{SIZE}}{{UNIT}}',

                ],

            ]
        );
        $repeater->add_responsive_control(
            'animation',
            [
                'label' => esc_html__( 'Animation', 'elementskit' ),
                'type' => Controls_Manager::SELECT2,
                'frontend_available' => true,
                'default' => 'ekit-fade',
                'options' => [
                    'ekit-fade'=> 'Fade',
                    'ekit-rotate'=> 'Rotate',
                    'ekit-bounce'=> 'Bounce',
                    'ekit-zoom'=> 'Zoom',
                    'ekit-rotate-box'=> 'RotateBox',
                    'ekit-left-right'=> 'Left Right',
                    'bounce'=> 'Bounce 2',
                    'flash'=> 'Flash',
                    'pulse'=> 'Pulse',
                    'shake'=> 'Shake',
                    'headShake'=> 'HeadShake',
                    'swing'=> 'Swing',
                    'tada'=> 'Tada',
                    'wobble'=> 'Wobble',
                    'jello'=> 'Jello',
                ],
                'condition' => [
                    'parallax_style' => 'animation',
                ],
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => '-webkit-animation-name:{{UNIT}}',
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => 'animation-name:{{UNIT}}',
                ],
            ]
        );
        $repeater->add_control(
            'item_opacity',
            [
                'label' => esc_html__( 'Opacity', 'elementskit' ),
                'description' => esc_html__( 'Opacity will be (0-1), default value 1', 'elementskit' ),
                'type' => Controls_Manager::NUMBER,
                'default' => '1',
                'min' => 0,
                'step' => 1,
                'render_type' => 'none',
                'frontend_available' => true,
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => 'opacity:{{UNIT}}'
                ],
            ]
        );

        $repeater->add_control(
            'animation_speed',
            [
                'label' => esc_html__( 'Animation speed', 'elementskit' ) . ' (s)',
                'type' => Controls_Manager::NUMBER,
                'default' => '5',
                'min' => 1,
                'step' => 100,
                'render_type' => 'none',
                'frontend_available' => true,
                'condition' => [
                    'parallax_style' => 'animation',
                ],
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => '-webkit-animation-duration:{{UNIT}}s',
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => 'animation-duration:{{UNIT}}s'
                ],
            ]
        );
        $repeater->add_control(
            'animation_iteration_count',
            [
                'label' => esc_html__( 'Animation Iteration Count', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'infinite',
                'options' => [
                    'infinite' => esc_html__( 'Infinite', 'elementskit' ),
                    'unset' => esc_html__( 'Unset', 'elementskit' ),
                ],
                'condition' => [
                    'parallax_style' => 'animation',
                ],
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => 'animation-iteration-count:{{UNIT}}'
                ],
            ]
        );
        $repeater->add_control(
            'animation_direction',
            [
                'label' => esc_html__( 'Animation Direction', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => [
                    'normal' => esc_html__( 'Normal', 'elementskit' ),
                    'reverse' => esc_html__( 'Reverse', 'elementskit' ),
                    'alternate' => esc_html__( 'Alternate', 'elementskit' ),
                ],
                'condition' => [
                    'parallax_style' => 'animation',
                ],
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => 'animation-direction:{{UNIT}}'
                ],
            ]
        );

        $repeater->add_control(
            'parallax_speed', [
                'label' => esc_html__('Speed', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 40,
                'min' => 10,
                'max' => 150,
                'condition' => [
                    'parallax_style' => 'mousemove',
                ]
            ]
        );

        $repeater->add_control(
            'parallax_transform', [
                'label' => esc_html__( 'Parallax style', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'translateY',
                'options' => [
                    'translateX' => esc_html__( 'X axis', 'elementskit' ),
                    'translateY' => esc_html__( 'Y axis', 'elementskit' ),
                    'rotate' => esc_html__( 'rotate', 'elementskit' ),
                    'rotateX' => esc_html__( 'rotateX', 'elementskit' ),
                    'rotateY' => esc_html__( 'rotateY', 'elementskit' ),
                    'scale' => esc_html__( 'scale', 'elementskit' ),
                    'scaleX' => esc_html__( 'scaleX', 'elementskit' ),
                    'scaleY' => esc_html__( 'scaleY', 'elementskit' ),
                ],
                'condition' => [
                    'parallax_style' => 'onscroll'
                ],
            ]
        );
        $repeater->add_control(
            'parallax_transform_value', [
                'label' => esc_html__( 'Parallax Transition ', 'elementskit' ),
                'description' => esc_html__( 'X, Y and Z Axis will be pixels, Rotate will be degrees (0-180), scale will be ratio', 'elementskit' ),
                'type' => Controls_Manager::NUMBER,
                'default' => '250',
                'condition' => [
                    'parallax_style' => 'onscroll'
                ]
            ]
        );
        $repeater->add_control(
            'smoothness', [
                'label' => esc_html__( 'Smoothness', 'elementskit' ),
                'description' => esc_html__( 'factor that slowdown the animation, the more the smoothier (default: 700)', 'elementskit' ),
                'type' => Controls_Manager::NUMBER,
                'default' => '700',
                'min' => 0,
                'max' => 1000,
                'step' => 100,
                'condition' => [
                    'parallax_style' => 'onscroll'
                ]
            ]
        );
        $repeater->add_control(
            'offsettop',[
                'label' => esc_html__( 'Offset Top', 'elementskit' ),
                'description' => esc_html__( 'default: 0; when the element is visible', 'elementskit' ),
                'type' => Controls_Manager::NUMBER,
                'default' => '0',
                'condition' => [
                    'parallax_style' => 'onscroll'
                ]
            ]
        );
        $repeater->add_control(
            'offsetbottom', [
                'label' => esc_html__( 'Offset Bottom', 'elementskit' ),
                'description' => esc_html__( 'default: 0; when the element is visible', 'elementskit' ),
                'type' => Controls_Manager::NUMBER,
                'default' => '0',
                'condition' => [
                    'parallax_style' => 'onscroll'
                ]
            ]
        );
        $repeater->add_control(
            'maxtilt',[
                'label' => esc_html__( 'MaxTilt', 'elementskit' ),
                'type' => Controls_Manager::NUMBER,
                'default' => '20',
                'condition' => [
                    'parallax_style' => 'tilt',
                ]
            ]
        );
        $repeater->add_control(
            'scale',[
                'label' => esc_html__( 'Image Scale', 'elementskit' ),
                'description' => esc_html__( '2 = 200%, 1.5 = 150%, etc.. Default 1', 'elementskit' ),
                'type' => Controls_Manager::NUMBER,
                'default' => '1',
                'condition' => [
                    'parallax_style' => 'tilt',
                ]
            ]
        );
        $repeater->add_control(
            'disableaxis', [
                'label' => esc_html__( 'Disable Axis', 'elementskit' ),
                'description' => esc_html__( 'What axis should be disabled. Can be X or Y.', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__( 'None', 'elementskit' ),
                    'x' => esc_html__( 'X axis', 'elementskit' ),
                    'y' => esc_html__( 'Y axis', 'elementskit' ),
                ],

                'condition' => [
                    'parallax_style' => 'tilt',
                ]
            ]
        );
        $repeater->add_control(
            'zindex',   [
                'label' => esc_html__('Z-index', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => esc_html__('2', 'elementskit'),
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => 'z-index: {{UNIT}}',
                ],
            ]
        );
        $control->add_control(
            'ekit_section_parallax_multi_items',
            [
                'label' => esc_html__( 'Parallax', 'elementskit' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'frontend_available' => true,
                'title_field' => '{{{ parallax_style }}}',
                'condition' => [
                    'ekit_section_parallax_multi' => 'yes'
                ],

            ]
        );

        $control->add_control(
            'ekit_section_parallax_overflow',
            [
                'label' => esc_html__('Section Overflow', 'elementskit'),
                'type' => Controls_Manager::SELECT,
				'default' => 'visible',
				'options' => [
					'visible'  => esc_html__( 'Visible', 'elementskit' ),
					'hidden' => esc_html__( 'Hidden', 'elementskit' ),
				],
                'selectors' => [
                    "{{WRAPPER}}" => 'overflow: {{VALUE}}'
                ]
            ]
        );

        $control->end_controls_section();
    }
}