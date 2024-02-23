<?php

namespace Elementor;

class ElementsKit_Widget_Effect_Controls{
    public function __construct()
    {
        add_action('elementor/element/common/_section_style/after_section_end', [ $this, 'register_controls' ], 5, 2);
    }

    public function register_controls($control, $args)
    {
        $control->start_controls_section(
            'ekit_widget_effects',
            [
                'label' => esc_html__('ElementsKit Effects', 'elementskit'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );

        $control->add_control(
            'ekit_we_effect_on', [
                'label' => esc_html__( 'Effect Type', 'elementskit' ),
                'render_type' => 'none',
                'frontend_available' => true,
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => esc_html__( 'None', 'elementskit' ),
                    'css' => esc_html__( 'CSS3', 'elementskit' ),
                    'tilt' => esc_html__( 'Tilt', 'elementskit' ),
                    'onscroll' => esc_html__( 'On scroll', 'elementskit' ),
                    'mousemove' => esc_html__( 'On mouse move', 'elementskit' ),
                ],
            ]
        );

        /*
        * CSS animation begin
        */
        $control->add_control(
            'ekit_we_css_animation_fx',
            [
                'label' => esc_html__('CSS Animation', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'elementskit'),
                'render_type' => 'ui',
                'label_off' => esc_html__('Off', 'elementskit'),
                'condition' => [
                    'ekit_we_effect_on' => 'css',
                ],
            ]
        );
        $control->add_responsive_control(
            'ekit_we_css_animation',
            [
                'label' => esc_html__( 'Animation', 'elementskit' ),
                'type' => Controls_Manager::SELECT2,
                'render_type' => 'ui',
                'default' => 'ekit-fade',
                'options' => [
                    'none'=> 'None',
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
                    'ekit_we_css_animation_fx' => 'yes',
                    'ekit_we_effect_on' => 'css',
                ],
                'selectors' => [
                    "{{WRAPPER}} .elementor-widget-container" => '-webkit-animation-name:{{UNIT}}',
                    "{{WRAPPER}} .elementor-widget-container" => 'animation-name:{{UNIT}}',
                ],
            ]
        );

        $control->add_control(
            'ekit_we_css_animation_speed',
            [
                'label' => esc_html__( 'Animation speed', 'elementskit' ),
                'type' => Controls_Manager::NUMBER,
                'render_type' => 'ui',
                'default' => '5',
                'min' => 1,
                'step' => 100,
                'condition' => [
                    'ekit_we_css_animation_fx' => 'yes',
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_animation!' => 'none',
                ],
                'selectors' => [
                    "{{WRAPPER}} .elementor-widget-container" => '-webkit-animation-duration:{{UNIT}}s',
                    "{{WRAPPER}} .elementor-widget-container" => 'animation-duration:{{UNIT}}s'
                ],
            ]
        );
        $control->add_control(
            'ekit_we_css_animation_iteration_count',
            [
                'label' => esc_html__( 'Animation Iteration Count', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'render_type' => 'ui',
                'default' => 'infinite',
                'options' => [
                    'infinite' => esc_html__( 'Infinite', 'elementskit' ),
                    'unset' => esc_html__( 'Unset', 'elementskit' ),
                ],
                'condition' => [
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_animation_fx' => 'yes',
                    'ekit_we_css_animation!' => 'none',
                ],
                'selectors' => [
                    "{{WRAPPER}} .elementor-widget-container" => 'animation-iteration-count:{{UNIT}}'
                ],
            ]
        );
        $control->add_control(
            'ekit_we_css_animation_direction',
            [
                'label' => esc_html__( 'Animation Direction', 'elementskit' ),
                'render_type' => 'ui',
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => [
                    'normal' => esc_html__( 'Normal', 'elementskit' ),
                    'reverse' => esc_html__( 'Reverse', 'elementskit' ),
                    'alternate' => esc_html__( 'Alternate', 'elementskit' ),
                ],
                'condition' => [
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_animation_fx' => 'yes',
                    'ekit_we_css_animation!' => 'none',
                ],
                'selectors' => [
                    "{{WRAPPER}} .elementor-widget-container" => 'animation-direction:{{UNIT}}'
                ],
            ]
        );


		$control->add_control(
			'ekit_wex_hr_2',
			[
                'type' => Controls_Manager::DIVIDER,
                'condition' => [
                    'ekit_we_effect_on!' => 'none',
                    'ekit_we_css_animation!' => 'none',
                ]
			]
        );



        /*
        * tcss transform begin
        */
        $control->add_control(
            'ekit_we_css_transform_fx',
            [
                'label' => esc_html__( 'CSS Transform', 'elementskit' ),
                'render_type' => 'ui',
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'condition' => [
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_animation!' => 'none',
                ]
            ]
        );

        $control->add_control(
            'ekit_we_css_transform_fx_translate_toggle',
            [
                'label' => esc_html__( 'Translate', 'elementskit' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'render_type' => 'ui',
                'return_value' => 'yes',
                'condition' => [
                    'ekit_we_css_transform_fx' => 'yes',
                    'ekit_we_effect_on' => 'css',
                ],
            ]
        );

        $control->start_popover();

        $control->add_responsive_control(
            'ekit_we_css_transform_fx_translate_x',
            [
                'label' => esc_html__( 'Translate X', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'render_type' => 'ui',
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                    ]
                ],
                'condition' => [
                    'ekit_we_css_transform_fx_translate_toggle' => 'yes',
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
            ]
        );

        $control->add_responsive_control(
            'ekit_we_css_transform_fx_translate_y',
            [
                'label' => esc_html__( 'Translate Y', 'elementskit' ),
                'render_type' => 'ui',
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                    ]
                ],
                'condition' => [
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx_translate_toggle' => 'yes',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
                'selectors' => [
                    '(desktop){{WRAPPER}} .elementor-widget-container' =>
                        '-ms-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y.SIZE || 0}}px);'
                        . '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y.SIZE || 0}}px);'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y.SIZE || 0}}px);',
                    '(tablet){{WRAPPER}} .elementor-widget-container' =>
                        '-ms-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_tablet.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_tablet.SIZE || 0}}px);'
                        . '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_tablet.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_tablet.SIZE || 0}}px);'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_tablet.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_tablet.SIZE || 0}}px);',
                    '(mobile){{WRAPPER}} .elementor-widget-container' =>
                        '-ms-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_mobile.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_mobile.SIZE || 0}}px);'
                        . '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_mobile.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_mobile.SIZE || 0}}px);'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_mobile.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_mobile.SIZE || 0}}px);',
                ]
            ]
        );

        $control->end_popover();

        $control->add_control(
            'ekit_we_css_transform_fx_rotate_toggle',
            [
                'label' => esc_html__( 'Rotate', 'elementskit' ),
                'render_type' => 'ui',
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'condition' => [
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
            ]
        );

        $control->start_popover();

        $control->add_responsive_control(
            'ekit_we_css_transform_fx_rotate_z',
            [
                'label' => esc_html__( 'Rotate Z', 'elementskit' ),
                'render_type' => 'ui',
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ]
                ],
                'condition' => [
                    'ekit_we_css_transform_fx_rotate_toggle' => 'yes',
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
                'selectors' => [
                    '(desktop){{WRAPPER}} .elementor-widget-container' =>
                        '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z.SIZE || 0}}deg);'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z.SIZE || 0}}deg);',
                    '(tablet){{WRAPPER}} .elementor-widget-container' =>
                        '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_tablet.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_tablet.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_tablet.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_tablet.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_tablet.SIZE || 0}}deg);'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_tablet.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_tablet.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_tablet.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_tablet.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_tablet.SIZE || 0}}deg);',
                    '(mobile){{WRAPPER}} .elementor-widget-container' =>
                        '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_mobile.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_mobile.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_mobile.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_mobile.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_mobile.SIZE || 0}}deg);'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_mobile.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_mobile.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_mobile.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_mobile.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_mobile.SIZE || 0}}deg);'
                ]
            ]
        );

        $control->add_responsive_control(
            'ekit_we_css_transform_fx_rotate_x',
            [
                'label' => esc_html__( 'Rotate X', 'elementskit' ),
                'render_type' => 'ui',
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ]
                ],
                'condition' => [
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx_rotate_toggle' => 'yes',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
            ]
        );

        $control->add_responsive_control(
            'ekit_we_css_transform_fx_rotate_y',
            [
                'label' => esc_html__( 'Rotate Y', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'render_type' => 'ui',
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ]
                ],
                'condition' => [
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx_rotate_toggle' => 'yes',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
            ]
        );

        $control->end_popover();

        $control->add_control(
            'ekit_we_css_transform_fx_scale_toggle',
            [
                'label' => esc_html__( 'Scale', 'elementskit' ),
                'render_type' => 'ui',
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
            ]
        );

        $control->start_popover();

        $control->add_responsive_control(
            'ekit_we_css_transform_fx_scale_x',
            [
                'label' => esc_html__( 'Scale X', 'elementskit' ),
                'render_type' => 'ui',
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'default' => [
                    'size' => 1
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => .1
                    ]
                ],
                'condition' => [
                    'ekit_we_css_transform_fx_scale_toggle' => 'yes',
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
            ]
        );

        $control->add_responsive_control(
            'ekit_we_css_transform_fx_scale_y',
            [
                'label' => esc_html__( 'Scale Y', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'render_type' => 'ui',
                'size_units' => ['px'],
                'default' => [
                    'size' => 1
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => .1
                    ]
                ],
                'condition' => [
                    'ekit_we_css_transform_fx_scale_toggle' => 'yes',
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
                'selectors' => [
                    '(desktop){{WRAPPER}} .elementor-widget-container' =>
                        '-ms-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y.SIZE || 1}});'
                        . '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y.SIZE || 1}});'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y.SIZE || 1}});',
                    '(tablet){{WRAPPER}} .elementor-widget-container' =>
                        '-ms-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_tablet.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_tablet.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_tablet.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_tablet.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_tablet.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_tablet.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_tablet.SIZE || 1}});'
                        . '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_tablet.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_tablet.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_tablet.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_tablet.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_tablet.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_tablet.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_tablet.SIZE || 1}});'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_tablet.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_tablet.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_tablet.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_tablet.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_tablet.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_tablet.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_tablet.SIZE || 1}});',
                    '(mobile){{WRAPPER}} .elementor-widget-container' =>
                        '-ms-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_mobile.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_mobile.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_mobile.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_mobile.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_mobile.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_mobile.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_mobile.SIZE || 1}});'
                        . '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_mobile.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_mobile.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_mobile.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_mobile.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_mobile.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_mobile.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_mobile.SIZE || 1}});'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_mobile.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_mobile.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_mobile.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_mobile.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_mobile.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_mobile.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_mobile.SIZE || 1}});'
                ]
            ]
        );

        $control->end_popover();

        $control->add_control(
            'ekit_we_css_transform_fx_skew_toggle',
            [
                'label' => esc_html__( 'Skew', 'elementskit' ),
                'render_type' => 'ui',
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition' => [
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
            ]
        );

        $control->start_popover();

        $control->add_responsive_control(
            'ekit_we_css_transform_fx_skew_x',
            [
                'label' => esc_html__( 'Skew X', 'elementskit' ),
                'render_type' => 'ui',
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ]
                ],
                'condition' => [
                    'ekit_we_css_transform_fx_skew_toggle' => 'yes',
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
            ]
        );

        $control->add_responsive_control(
            'ekit_we_css_transform_fx_skew_y',
            [
                'label' => esc_html__( 'Skew Y', 'elementskit' ),
                'render_type' => 'ui',
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ]
                ],
                'condition' => [
                    'ekit_we_css_transform_fx_skew_toggle' => 'yes',
                    'ekit_we_effect_on' => 'css',
                    'ekit_we_css_transform_fx' => 'yes',
                ],
                'selectors' => [
                    '(desktop){{WRAPPER}} .elementor-widget-container' =>
                        '-ms-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y.SIZE || 1}}) '
                            . 'skew({{ekit_we_css_transform_fx_skew_x.SIZE || 0}}deg, {{ekit_we_css_transform_fx_skew_y.SIZE || 0}}deg);'
                        . '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y.SIZE || 1}}) '
                            . 'skew({{ekit_we_css_transform_fx_skew_x.SIZE || 0}}deg, {{ekit_we_css_transform_fx_skew_y.SIZE || 0}}deg);'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y.SIZE || 1}}) '
                            . 'skew({{ekit_we_css_transform_fx_skew_x.SIZE || 0}}deg, {{ekit_we_css_transform_fx_skew_y.SIZE || 0}}deg);',
                    '(tablet){{WRAPPER}} .elementor-widget-container' =>
                        '-ms-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_tablet.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_tablet.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_tablet.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_tablet.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_tablet.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_tablet.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_tablet.SIZE || 1}}) '
                            . 'skew({{ekit_we_css_transform_fx_skew_x_tablet.SIZE || 0}}deg, {{ekit_we_css_transform_fx_skew_y_tablet.SIZE || 0}}deg);'
                        . '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_tablet.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_tablet.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_tablet.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_tablet.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_tablet.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_tablet.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_tablet.SIZE || 1}}) '
                            . 'skew({{ekit_we_css_transform_fx_skew_x_tablet.SIZE || 0}}deg, {{ekit_we_css_transform_fx_skew_y_tablet.SIZE || 0}}deg);'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_tablet.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_tablet.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_tablet.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_tablet.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_tablet.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_tablet.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_tablet.SIZE || 1}}) '
                            . 'skew({{ekit_we_css_transform_fx_skew_x_tablet.SIZE || 0}}deg, {{ekit_we_css_transform_fx_skew_y_tablet.SIZE || 0}}deg);',
                    '(mobile){{WRAPPER}} .elementor-widget-container' =>
                        '-ms-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_mobile.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_mobile.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_mobile.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_mobile.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_mobile.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_mobile.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_mobile.SIZE || 1}}) '
                            . 'skew({{ekit_we_css_transform_fx_skew_x_mobile.SIZE || 0}}deg, {{ekit_we_css_transform_fx_skew_y_mobile.SIZE || 0}}deg);'
                        . '-webkit-transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_mobile.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_mobile.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_mobile.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_mobile.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_mobile.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_mobile.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_mobile.SIZE || 1}}) '
                            . 'skew({{ekit_we_css_transform_fx_skew_x_mobile.SIZE || 0}}deg, {{ekit_we_css_transform_fx_skew_y_mobile.SIZE || 0}}deg);'
                        . 'transform:'
                            . 'translate({{ekit_we_css_transform_fx_translate_x_mobile.SIZE || 0}}px, {{ekit_we_css_transform_fx_translate_y_mobile.SIZE || 0}}px) '
                            . 'rotateX({{ekit_we_css_transform_fx_rotate_x_mobile.SIZE || 0}}deg) rotateY({{ekit_we_css_transform_fx_rotate_y_mobile.SIZE || 0}}deg) rotateZ({{ekit_we_css_transform_fx_rotate_z_mobile.SIZE || 0}}deg) '
                            . 'scaleX({{ekit_we_css_transform_fx_scale_x_mobile.SIZE || 1}}) scaleY({{ekit_we_css_transform_fx_scale_y_mobile.SIZE || 1}}) '
                            . 'skew({{ekit_we_css_transform_fx_skew_x_mobile.SIZE || 0}}deg, {{ekit_we_css_transform_fx_skew_y_mobile.SIZE || 0}}deg);'
                ]
            ]
        );

        $control->end_popover();


        /*
        * test animation switcher
        */

        $control->add_control(
            'ekit_we_on_test_mode',
            [
                'render_type' => 'none',
                'frontend_available' => true,
                'label' => esc_html__('Disable animation in editor ', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'on',
                'label_on' => esc_html__('On', 'elementskit'),
                'label_off' => esc_html__('Off', 'elementskit'),
                'condition' => [
                    'ekit_we_effect_on' => ['tilt', 'mousemove', 'onscroll'],
                ]
            ]
        );

        /*
        * tilt effect begin
        */

        $control->add_control(
            'ekit_we_tilt_maxtilt',[
                'render_type' => 'none',
                'frontend_available' => true,
                'label' => esc_html__( 'MaxTilt', 'elementskit' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 20,
                'min' => 5,
                'max' => 40,
                'condition' => [
                    'ekit_we_effect_on' => 'tilt',
                ]
            ]
        );
        $control->add_control(
            'ekit_we_tilt_scale',[
                'label' => esc_html__( 'Image Scale', 'elementskit' ),
                'description' => esc_html__( '2 = 200%, 1.5 = 150%, etc.. Default 1', 'elementskit' ),
                'render_type' => 'none',
                'frontend_available' => true,
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'min' => .3,
                'step' => .2,
                'max' => 3,
                'condition' => [
                    'ekit_we_effect_on' => 'tilt',
                ]
            ]
        );
        $control->add_control(
            'ekit_we_tilt_disableaxis', [
                'label' => esc_html__( 'Disable Axis', 'elementskit' ),
                'description' => esc_html__( 'What axis should be disabled. Can be X or Y.', 'elementskit' ),
                'render_type' => 'none',
                'frontend_available' => true,
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__( 'None', 'elementskit' ),
                    'x' => esc_html__( 'X axis', 'elementskit' ),
                    'y' => esc_html__( 'Y axis', 'elementskit' ),
                ],

                'condition' => [
                    'ekit_we_effect_on' => 'tilt',
                ]
            ]
        );

        /*
        * mousemove effect begin
        */
        $control->add_control(
            'ekit_we_mousemove_parallax_speed', [
                'label' => esc_html__('Speed', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'render_type' => 'none',
                'frontend_available' => true,
                'default' => 40,
                'min' => 10,
                'max' => 150,
                'condition' => [
                    'ekit_we_effect_on' => 'mousemove',
                ]
            ]
        );

        /*
        * onscroll effect begin
        */
        $control->add_control(
            'ekit_we_scroll_animation', [
                'label' => esc_html__( 'Parallax style', 'elementskit' ),
                'render_type' => 'none',
                'frontend_available' => true,
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
                    'ekit_we_effect_on' => 'onscroll',
                ]
            ]
        );
        $control->add_control(
            'ekit_we_scroll_animation_value', [
                'label' => esc_html__( 'Parallax Transition ', 'elementskit' ),
                'description' => esc_html__( 'X, Y and Z Axis will be pixels, Rotate will be degrees (0-180), scale will be ratio', 'elementskit' ),
                'render_type' => 'none',
                'frontend_available' => true,
                'type' => Controls_Manager::NUMBER,
                'default' => 250,
                'condition' => [
                    'ekit_we_effect_on' => 'onscroll',
                ]
            ]
        );
        $control->add_control(
            'ekit_we_scroll_smoothness', [
                'label' => esc_html__( 'Smoothness', 'elementskit' ),
                'description' => esc_html__( 'factor that slowdown the animation, the more the smoothier (default: 700)', 'elementskit' ),
                'render_type' => 'none',
                'frontend_available' => true,
                'type' => Controls_Manager::NUMBER,
                'default' => 700,
                'min' => 0,
                'max' => 1000,
                'step' => 100,
                'condition' => [
                    'ekit_we_effect_on' => 'onscroll',
                ]
            ]
        );
        $control->add_control(
            'ekit_we_scroll_offsettop',[
                'label' => esc_html__( 'Offset Top', 'elementskit' ),
                'render_type' => 'none',
                'frontend_available' => true,
                'description' => esc_html__( 'default: 0; when the element is visible', 'elementskit' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'condition' => [
                    'ekit_we_effect_on' => 'onscroll',
                ]
            ]
        );
        $control->add_control(
            'ekit_we_scroll_offsetbottom', [
                'label' => esc_html__( 'Offset Bottom', 'elementskit' ),
                'render_type' => 'none',
                'frontend_available' => true,
                'description' => esc_html__( 'default: 0; when the element is visible', 'elementskit' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'condition' => [
                    'ekit_we_effect_on' => 'onscroll',
                ]
            ]
        );


        $control->end_controls_section();

    }
}