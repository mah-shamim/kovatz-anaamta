<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Image_Morphing_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;
class ElementsKit_Widget_Image_Morphing extends Widget_Base {
   use \ElementsKit_Lite\Widgets\Widget_Notice;

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
        return "https://wpmet.com/doc/image-morphing";
    }

	public function get_script_depends() {
		return ['animejs'];
	}

    protected function register_controls() {
        // IMAGE option/control
        $this->start_controls_section(
            'ekit_morphing_image_section',
            [
                'label' => esc_html__('Content', 'elementskit'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'ekit_morphing_select_effects_type',
            [
                'label' => esc_html__('Select Effect Type', 'elementskit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'images',
                'options' => [
                    'images' => esc_html__('Image', 'elementskit'),
                    'color' => esc_html__('Color', 'elementskit'),
                    'gradient' => esc_html__('Gradient Color', 'elementskit'),
                ],
            ]
        );

		$this->add_control(
			'ekit_morphing_image_masks',
			[
				'label' => esc_html__( 'Choose Image', 'elementskit'),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'image',
				'library_type' => 'image',
                'default' => [
					'url' => Utils::get_placeholder_image_src(),
                    'id' => 'morphing_image_092478'
				],
				'dynamic' => [
					'active' => true,
				],
                'condition'	=> [
                    'ekit_morphing_select_effects_type' => ['images'],
                ],
			]
		);

        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'ekit_morphing_image_size', 
				'default' => 'large',
				'separator' => 'none',
                'condition'	=> [
                    'ekit_morphing_select_effects_type' => ['images'],
                ],
			]
		);

        $this->add_control(
			'ekit_morphing_color_position_x1',
			[
				'label' => esc_html__('Position X1', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'size_units' => [ '%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'step' => 0.5,
                'condition'	=> [
                    'ekit_morphing_select_effects_type' => ['gradient'],
                ],
			]
		);
        
        $this->add_control(
			'ekit_morphing_color_position_x2',
			[
				'label' => esc_html__('Position X2', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'size_units' => [ '%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'step' => 0.5,
                'condition'	=> [
                    'ekit_morphing_select_effects_type' => ['gradient'],
                ],
			]
		);
        
        $this->add_control(
			'ekit_morphing_color_position_y1',
			[
				'label' => esc_html__('Position Y1', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'size_units' => [ '%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'step' => 0.5,
                'condition'	=> [
                    'ekit_morphing_select_effects_type' => ['gradient'],
                ],
			]
		);
        
        $this->add_control(
			'ekit_morphing_color_position_y2',
			[
				'label' => esc_html__('Position Y2', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'size_units' => [ '%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'step' => 0.5,
                'condition'	=> [
                    'ekit_morphing_select_effects_type' => ['gradient'],
                ],
			]
		);

        $this->add_control(
			'ekit_morphing_color_show_stroke',
			[
				'label' => esc_html__('Show Stroke', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'elementskit'),
				'label_off' => esc_html__('Hide', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
                'condition'	=> [
                    'ekit_morphing_select_effects_type' => ['color'],
                ],
			]
		);

        $this->end_controls_section();

        // Settings options section
        $this->start_controls_section(
            'ekit_morphing_settings_section',
            [
                'label' => esc_html__('Settings', 'elementskit'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'ekit_morphing_shape_type',
            [
                'label' => esc_html__('Shape Type', 'elementskit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => esc_html__('Default', 'elementskit'),
                    'custom' => esc_html__('Custom', 'elementskit'),
                ],
            ]
        );

        // animated type start
        $this->add_control(
            'ekit_morphing_style',
            [
                'label' => esc_html__('Choose Morphing Style', 'elementskit'),
                'type' => ElementsKit_Controls_Manager::IMAGECHOOSE,
                'default' => 'shape-01',
                'options' => [
					'shape-01' => [
						'title' => esc_html__( 'Shape 01', 'elementskit'),
                        'imagelarge' => Handler::get_url() . 'assets/shape_01/shape_01.svg',
                        'imagesmall' => Handler::get_url() . 'assets/shape_01/shape_01.svg',
                        'width' => '25%',
                        'height' => '100%',
					],
                    // 'shape-02' => [
					// 	'title' => esc_html__( 'Shape 02', 'elementskit'),
                    //     'imagelarge' => Handler::get_url() . 'assets/shape_02/shape_01.svg',
                    //     'imagesmall' => Handler::get_url() . 'assets/shape_02/shape_01.svg',
                    //     'width' => '25%',
                    //     'height' => '100%',
					// ],
                    'shape-03' => [
						'title' => esc_html__( 'Shape 03', 'elementskit'),
                        'imagelarge' => Handler::get_url() . 'assets/shape_03/shape_01.svg',
                        'imagesmall' => Handler::get_url() . 'assets/shape_03/shape_01.svg',
                        'width' => '25%',
                        'height' => '100%',
					],
                    'shape-04' => [
						'title' => esc_html__( 'Shape 04', 'elementskit'),
                        'imagelarge' => Handler::get_url() . 'assets/shape_04/shape_01.svg',
                        'imagesmall' => Handler::get_url() . 'assets/shape_04/shape_01.svg',
                        'width' => '25%',
                        'height' => '100%',
					],
                    'shape-05' => [
						'title' => esc_html__( 'Shape 05', 'elementskit'),
                        'imagelarge' => Handler::get_url() . 'assets/shape_05/shape_01.svg',
                        'imagesmall' => Handler::get_url() . 'assets/shape_05/shape_01.svg',
                        'width' => '25%',
                        'height' => '100%',
					],
                    'shape-06' => [
						'title' => esc_html__( 'Shape 06', 'elementskit'),
                        'imagelarge' => Handler::get_url() . 'assets/shape_06/shape_01.svg',
                        'imagesmall' => Handler::get_url() . 'assets/shape_06/shape_01.svg',
                        'width' => '25%',
                        'height' => '100%',
					],
                    'shape-07' => [
						'title' => esc_html__( 'Shape 07', 'elementskit'),
                        'imagelarge' => Handler::get_url() . 'assets/shape_07/shape_01.svg',
                        'imagesmall' => Handler::get_url() . 'assets/shape_07/shape_01.svg',
                        'width' => '25%',
                        'height' => '100%',
					],
                    'shape-08' => [
						'title' => esc_html__( 'Shape 08', 'elementskit'),
                        'imagelarge' => Handler::get_url() . 'assets/shape_08/shape_01.svg',
                        'imagesmall' => Handler::get_url() . 'assets/shape_08/shape_01.svg',
                        'width' => '25%',
                        'height' => '100%',
					],
                    'shape-09' => [
						'title' => esc_html__( 'Shape 09', 'elementskit'),
                        'imagelarge' => Handler::get_url() . 'assets/shape_09/shape_01.svg',
                        'imagesmall' => Handler::get_url() . 'assets/shape_09/shape_01.svg',
                        'width' => '25%',
                        'height' => '100%',
					],
                    'shape-10' => [
						'title' => esc_html__( 'Shape 10', 'elementskit'),
                        'imagelarge' => Handler::get_url() . 'assets/shape_10/shape_01.svg',
                        'imagesmall' => Handler::get_url() . 'assets/shape_10/shape_01.svg',
                        'width' => '25%',
                        'height' => '100%',
					],
                    'shape-11' => [
						'title' => esc_html__( 'Shape 11', 'elementskit'),
                        'imagelarge' => Handler::get_url() . 'assets/shape_11/shape_01.svg',
                        'imagesmall' => Handler::get_url() . 'assets/shape_11/shape_01.svg',
                        'width' => '25%',
                        'height' => '100%',
					],
                    'shape-12' => [
						'title' => esc_html__( 'Shape 12', 'elementskit'),
                        'imagelarge' => Handler::get_url() . 'assets/shape_12/shape_01.svg',
                        'imagesmall' => Handler::get_url() . 'assets/shape_12/shape_01.svg',
                        'width' => '25%',
                        'height' => '100%',
					],
                ],
                'condition'	=> [
                    'ekit_morphing_shape_type' => ['default'],
                ],
            ]
        );

        $this->add_control(
			'important_note_custom_svg',
			[
                'type' => Controls_Manager::RAW_HTML,
                'raw' => sprintf(
                    /* translators: 1: br tag, 2: icone print, 3: Link open tag, 4: Link close tag. */
                    esc_html__( 'You can generate morphing SVG with %1$s Or you may upload your own morphing SVG. Upload minimum two SVG to morphing work.', 'elementskit' ),
                    '<a href="https://www.blobmaker.app/" target="_blank">blobmaker</a>'
                ),
                'condition'	=> [
                    'ekit_morphing_shape_type' => ['custom'],
                ],
			]
		);

        $repeater = new Repeater();

        $repeater->add_control(
            'custom_svg', 
            [
                'label' => esc_html__( 'Choose Morphing Shape', 'elementskit' ),
				'type' => Controls_Manager::MEDIA,
                'show_label' => true,
				'media_type' => 'svg',
				'library_type' => 'svg+xml',
                'default' => [
					'url' => Handler::get_url() . 'assets/shapes/shape_01.svg',
				],
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'ekit_morphing_shapes',
            [
                'label' => esc_html__('Morphing Value', 'elementskit'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'max' => 2,
                'condition'	=> [      
                    'ekit_morphing_shape_type' => ['custom'],
                ],
            ]
        );

        $this->add_control(
            'ekit_morphing_effect',
            [
                'label' => esc_html__('Morphing Effect', 'elementskit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'easeInSine',
                'frontend_available' => true,
                'options' => [
                    'linear' => esc_html__('Linear', 'elementskit'),
                    'easeInQuad' => esc_html__('EaseInQuad', 'elementskit'),
                    'easeOutQuad' => esc_html__('EaseOutQuad', 'elementskit'),
                    'easeInOutQuad' => esc_html__('EaseInOutQuad', 'elementskit'),
                    'easeOutInQuad' => esc_html__('EaseOutInQuad', 'elementskit'),

                    'easeInCubic' => esc_html__('EaseInCubic', 'elementskit'),
                    'easeOutCubic' => esc_html__('EaseOutCubic', 'elementskit'),
                    'easeInOutCubic' => esc_html__('EaseInOutCubic', 'elementskit'),
                    'easeOutInCubic' => esc_html__('EaseOutInCubic', 'elementskit'),
                    
                    'easeInQuart' => esc_html__('EaseInQuart', 'elementskit'),
                    'easeOutQuart' => esc_html__('EaseOutQuart', 'elementskit'),
                    'easeInOutQuart' => esc_html__('EaseInOutQuart', 'elementskit'),
                    'easeOutInQuart' => esc_html__('EaseOutInQuart', 'elementskit'),

                    'easeInQuint' => esc_html__('EaseInQuint', 'elementskit'),
                    'easeOutQuint' => esc_html__('EaseOutQuint', 'elementskit'),
                    'easeInOutQuint' => esc_html__('EaseInOutQuint', 'elementskit'),
                    'easeOutInQuint' => esc_html__('EaseOutInQuint', 'elementskit'),

                    'easeInSine' => esc_html__('EaseInSine', 'elementskit'),
                    'easeOutSine' => esc_html__('EaseOutSine', 'elementskit'),
                    'easeInOutSine' => esc_html__('EaseInOutSine', 'elementskit'),
                    'easeOutInSine' => esc_html__('EaseOutInSine', 'elementskit'),

                    'easeInExpo' => esc_html__('EaseInExpo', 'elementskit'),
                    'easeOutExpo' => esc_html__('EaseOutExpo', 'elementskit'),
                    'easeInOutExpo' => esc_html__('EaseInOutExpo', 'elementskit'),
                    'easeOutInExpo' => esc_html__('EaseOutInExpo', 'elementskit'),

                    'easeInCirc' => esc_html__('EaseInCirc', 'elementskit'),
                    'easeOutCirc' => esc_html__('EaseOutCirc', 'elementskit'),
                    'easeInOutCirc' => esc_html__('EaseInOutCirc', 'elementskit'),
                    'easeOutInCirc' => esc_html__('EaseOutInCirc', 'elementskit'),

                    'easeInBack' => esc_html__('EaseInBack', 'elementskit'),
                    'easeOutBack' => esc_html__('EaseOutBack', 'elementskit'),
                    'easeInOutBack' => esc_html__('EaseInOutBack', 'elementskit'),
                    'easeOutInBack' => esc_html__('EaseOutInBack', 'elementskit'),

                    'easeInBounce' => esc_html__('EaseInBounce', 'elementskit'),
                    'easeOutBounce' => esc_html__('EaseOutBounce', 'elementskit'),
                    'easeInOutBounce' => esc_html__('EaseInOutBounce', 'elementskit'),
                    'easeOutInBounce' => esc_html__('EaseOutInBounce', 'elementskit'),
                ],
            ]
        );

        $this->add_control(
            'ekit_morphing_direction',
            [
                'label' => esc_html__('Morphing Direction', 'elementskit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'alternate',
                'frontend_available' => true,
                'options' => [
                    'normal' => esc_html__('Normal', 'elementskit'),
                    'reverse' => esc_html__('Reverse', 'elementskit'),
                    'alternate' => esc_html__('Alternate', 'elementskit'),
                ],
            ]
        );
        
        $this->add_control(
			'ekit_morphing_loop',
			[
				'label' => esc_html__('Loop', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
                'frontend_available' => true,
			]
		);

        $this->add_control(
            'ekit_morphing_duration',
            [
                'label' => esc_html__('Duration (s)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 2000,
                'min' => 0,
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'ekit_morphing_delay',
            [
                'label' => esc_html__('Delay (s)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => '',
                'min' => 0,
                'max' => 500,
                'frontend_available' => true,
            ]
        );        
        
        $this->add_control(
            'ekit_morphing_end_delay',
            [
                'label' => esc_html__('End Delay (s)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => '',
                'min' => 0,
                'max' => 500,
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();

         // Morphing image style section
         $this->start_controls_section(
            'ekit_morphing_image_section_style',
            [
                'label' => esc_html__('Image Style', 'elementskit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'	=> [
                    'ekit_morphing_select_effects_type' => ['images'],
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_morphing_image_width',
			[
				'label' => esc_html__('Width', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
                    'size' => 100
				],
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1200,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-morphing-wrapper svg .ekit-morphing-image' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_morphing_image_height',
			[
				'label' => esc_html__('Height', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
                    'size' => ''
				],
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1200,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-morphing-wrapper svg .ekit-morphing-image' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'ekit_morphing_image_opacity',
			[
				'label' => esc_html__('Opacity', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
                'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-morphing-wrapper svg .ekit-morphing-image' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'ekit_morphing_image_filters',
				'selector' => '{{WRAPPER}} .ekit-morphing-wrapper svg .ekit-morphing-image',

			]
		);

        $this->add_responsive_control(
            "ekit_morphing_image_transform_scale",
            [
                'label' => esc_html__('Image Zoom', 'elementskit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'separator' => 'before',
                'selectors' => [
                    "{{WRAPPER}} .ekit-morphing-wrapper svg .ekit-morphing-image" => 'transform: scale({{SIZE}})',
                ],
            ]
        );

        $this->add_control(
			'image_position_options',
			[
				'label' => esc_html__('Image Position', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_morphing_offset_x',
			[
				'label' => esc_html__('Horizontal Orientation', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
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
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '0',
				],
				'size_units' => ['px', '%', 'vw', 'vh'],
				'selectors' => [
					'{{WRAPPER}} .ekit-morphing-wrapper .ekit-morphing-image' => 'x: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_morphing_offset_y',
			[
				'label' => esc_html__('Vertical Orientation', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
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
					'vh' => [
						'min' => -100,
						'max' => 100,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'size_units' => ['px', '%', 'vh', 'vw'],
				'default' => [
					'size' => '0',
                    'unit' => '%'
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-morphing-wrapper .ekit-morphing-image' => 'y: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->end_controls_section();

        // Morphing gradient-color style section
        $this->start_controls_section(
            'ekit_morphing_gradient_section_style',
            [
                'label' => esc_html__('Gradient Style', 'elementskit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'	=> [
                    'ekit_morphing_select_effects_type' => ['gradient'],
                ],
            ]
        );

        $this->start_controls_tabs(
			'ekit_morphing_gradient_style_tabs'
		);

		$this->start_controls_tab(
			'ekit_morphing_gradient_color_01',
			[
				'label' => esc_html__('Color One', 'elementskit'),
			]
		);

        $this->add_control(
            'ekit_gradient_color_01',
            [
                'label' => esc_html__('Gradient Color One', 'elementskit'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'ekit_morphing_offset_01',
			[
				'label' => esc_html__('Offset One (%)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'size_units' => [ '%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
			]
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_morphing_gradient_color_02',
			[
				'label' => esc_html__('Color Two', 'elementskit'),
			]
		);

        $this->add_control(
            'ekit_gradient_color_02',
            [
                'label' => esc_html__('Gradient Color Two', 'elementskit'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'ekit_morphing_offset_02',
			[
				'label' => esc_html__('Offset Two (%)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
			]
        );

		$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->end_controls_section();

        // Morphing color style section
        $this->start_controls_section(
            'ekit_morphing_color_section_style',
            [
                'label' => esc_html__('Color Style', 'elementskit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'	=> [
                    'ekit_morphing_select_effects_type' => ['color'],
                ],
            ]
        );

        $this->add_control(
            'ekit_morphing_stroke_color',
            [
                'label' => esc_html__('Stroke Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'default' => '#BB004B',
                'selectors' => [
					'{{WRAPPER}} .ekit-morphing-wrapper svg path' => 'stroke: {{VALUE}};',
				],
                'condition' => [
                    'ekit_morphing_color_show_stroke' =>['yes']
                ]
                
            ]
        );

        $this->add_control(
			'ekit_morphing_stroke_weight',
			[
				'label' => esc_html__('Stroke Weight', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-morphing-wrapper svg path' => 'stroke-width: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_morphing_color_show_stroke' =>['yes']
                ]
			]
		);

        $this->add_control(
            'ekit_morphing_fill_color',
            [
                'label' => esc_html__('SVG Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}} .ekit-morphing-wrapper svg path' => 'fill: {{VALUE}};',
				],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ekit_morphing_shape_section_size',
            [
                'label' => esc_html__('Shape', 'elementskit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
			'ekit_morphing_shape_width',
			[
				'label' => esc_html__('Width', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => ['%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1200,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-morphing-wrapper svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_morphing_shape_height',
			[
				'label' => esc_html__('Height', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'size_units' => ['px', 'vh'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 800,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-morphing-wrapper svg' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_svg_path_position_more_options',
			[
				'label' => esc_html__( 'SVG Position', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'	=> [      
					'ekit_morphing_shape_type' => ['custom'],
				],
			]
		);

		$this->add_control(
			'ekit_svg_path_position_scale',
			[
				'label' => esc_html__('Scale', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => .5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'frontend_available' => true,
				'condition'	=> [      
					'ekit_morphing_shape_type' => ['custom'],
				],
			]
		);

		$this->add_control(
			'ekit_svg_path_position_translate_x',
			[
				'label' => esc_html__('TranslateX (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => .5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'frontend_available' => true,
				'condition'	=> [      
					'ekit_morphing_shape_type' => ['custom'],
				],
			]
		);

		$this->add_control(
			'ekit_svg_path_position_translate_y',
			[
				'label' => esc_html__('TranslateY (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => .5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'frontend_available' => true,
				'condition'	=> [      
					'ekit_morphing_shape_type' => ['custom'],
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
        $id = $this->get_id();

        $paths =  $this->process_paths($settings);
        $this->add_render_attribute( 'wrapper', [
            'class' => [
                'ekit-morphing-wrapper',
                'morphing-element-' . $id,
            ],
			'data-paths' => $paths['lasts'],
            'data-morphing-id' => $id,
        ] );

        $this->add_render_attribute('svg_shape',
            'class', ($ekit_morphing_shape_type == 'custom') ? 'ekit-custom-svg' : 'ekit-svg-shape'
        );

        $viewbox_result = [0, 0, 200, 200];
        if (!empty($ekit_morphing_shapes) && $ekit_morphing_shape_type == 'custom') {
            foreach($ekit_morphing_shapes as $ekit_morphing_shape) {
                if(!empty($ekit_morphing_shape['custom_svg']['url'])) {
					$svg = $this->curl_get_file_contents($ekit_morphing_shape['custom_svg']['url']);
					$dom = new \DOMDocument();
					if(!empty($svg)) {
						$dom->loadXML($svg);
						$viewBox = null;
						$element = $dom->getElementsByTagName('svg');
						if($element->length > 0 && !empty($element[0]->getAttribute('viewBox'))) {
							$viewBox = $element[0]->getAttribute('viewBox');
						}
					}
					$viewbox_values = ($svg && !empty($viewBox)) ? explode(' ', $viewBox) : [0, 0, 200, 200]; 
					$viewbox_result = [
						$viewbox_values[0],
						$viewbox_values[1],
						$viewbox_values[2],
						$viewbox_values[3],
					];
                }
            }
        }
        
        if ('images' === $ekit_morphing_select_effects_type) {
            $morphing_image = Group_Control_Image_Size::get_attachment_image_src($ekit_morphing_image_masks['id'], 'ekit_morphing_image_size', $settings);
            $image_url = !empty($morphing_image) ? $morphing_image : $ekit_morphing_image_masks['url'];
        }

        if ('gradient' === $ekit_morphing_select_effects_type) {
            $color_position = [
                'position_x' => [
                    (!empty($ekit_morphing_color_position_x1['size'])) ? $ekit_morphing_color_position_x1['size'].'%' : "0%",
                    (!empty($ekit_morphing_color_position_x2['size'])) ? $ekit_morphing_color_position_x2['size'].'%' : "100%",
                ], 
                'position_y' => [
                    (!empty($ekit_morphing_color_position_y1['size'])) ? $ekit_morphing_color_position_y1['size'].'%' : "70.711%",
                    (!empty($ekit_morphing_color_position_y2['size'])) ? $ekit_morphing_color_position_y2['size'].'%' : "100%",
                ], 
            ];

            $this->add_render_attribute( 'gradient', [
                'id' => 'shape-gradient-color-' . $id,
                'x1' => $color_position['position_x'][0],
                'x2' => $color_position['position_x'][1],
                'y1' => $color_position['position_y'][0],
                'y2' => $color_position['position_y'][1],
            ] );
           
            $gradient_color_percentis = [
                'offset' => [
                    (!empty($ekit_morphing_offset_01['size'])) ? $ekit_morphing_offset_01['size'].'%' : "0%",
                    (!empty($ekit_morphing_offset_02['size'])) ? $ekit_morphing_offset_02['size'].'%' : "100%",
                ],
                'stop_color' => [
                    (!empty($ekit_gradient_color_01)) ? $ekit_gradient_color_01 : "#5F3698",
                    (!empty($ekit_gradient_color_02)) ? $ekit_gradient_color_02 : "#DC638D",
                ],
            ];
        }
       ?>
        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if(empty($paths['first']) && empty($paths['lasts'])) : ?>
				<p>
					<?php echo sprintf(
						'%1$s <code style="color:#f2295b;">%2$s</code> %3$s',
						esc_html__('Please use valid SVG for morphing. Make sure SVG has', 'elementskit'),
						esc_attr('<path d=".....">'),
						esc_html__('attribute.', 'elementskit'),
					);
					?>
				</p>
			<?php else : ?>
				<svg viewBox="<?php echo esc_attr(implode(' ', $viewbox_result)); ?>" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" <?php $this->print_render_attribute_string('svg_shape') ?>>
					<?php  if('images' === $ekit_morphing_select_effects_type) :?>
						<clipPath id="shape-morphing-<?php echo esc_attr($id); ?>">
						<path d="<?php echo esc_attr($paths['first']); ?>"/>
						</clipPath>
						<g clip-path="url(#shape-morphing-<?php echo esc_attr($id); ?>)">
							<image id="<?php echo esc_attr($ekit_morphing_image_masks['id']) ?>" class="ekit-morphing-image" href="<?php echo esc_url($image_url);?>" preserveAspectRatio="none"></image>
						</g>
					<?php elseif('color' === $ekit_morphing_select_effects_type):?>
						<path fill="#FF0066" d="<?php echo esc_attr($paths['first']); ?>">
					<?php elseif('gradient' === $ekit_morphing_select_effects_type):?>
						<linearGradient <?php $this->print_render_attribute_string( 'gradient' ); ?>>
							<stop offset="<?php echo esc_attr($gradient_color_percentis['offset'][0]);?>" stop-color="<?php echo esc_attr($gradient_color_percentis['stop_color'][0]);?>" stop-opacity="1"></stop>
							<stop offset="<?php echo esc_attr($gradient_color_percentis['offset'][1]);?>" stop-color="<?php echo esc_attr($gradient_color_percentis['stop_color'][1]);?>" stop-opacity="1"></stop>
						</linearGradient>
						<path fill="url(#shape-gradient-color-<?php echo esc_attr($id); ?>)" d="<?php echo esc_attr($paths['first']); ?>" />
					<?php  endif;?>
				</svg>
			<?php endif; ?>
        </div>
       <?php
    }

    protected function process_paths($settings) {
        extract($settings);

        $shapes = [];
        if($ekit_morphing_shape_type == 'custom') {
            foreach($ekit_morphing_shapes as $ekit_morphing_shape) {
                if(!empty($ekit_morphing_shape['custom_svg']['url'])) {
					$svg = $this->curl_get_file_contents($ekit_morphing_shape['custom_svg']['url']);
					if(!empty($svg)) {
						$dom = new \DOMDocument();
						$dom->loadXML($svg);
						$element = $dom->getElementsByTagName('path');
						if($element->length > 0 && !empty($element[0]->getAttribute('d'))) {
							array_push($shapes, $element[0]->getAttribute('d'));
						}
					}
                }
            }
            return [
                'first' => array_shift($shapes),
                'lasts' => !empty($shapes) ? json_encode($shapes) : false,
            ];
        } else {
            $shapes = [
                'shape-01' => [
                    'M147.912 18.0665C183.292 27.0115 200 69.2493 200 91.838C200 105.388 197.791 157.994 142.263 189.61C131.204 195.72 83.2951 215.706 40.5433 175.558C22.5735 158.683 7.12816 122.864 1.72271 89.7424C-3.02773 60.6353 1.23116 10.4506 24.8184 1.91923C47.9142 -7.61589 55.2619 21.177 71.9929 27.0115C92.1403 34.0374 113.022 12.5462 147.912 18.0665Z',
                    'M192.807 17.4177C205.135 37.8179 199.445 80.2288 191.859 120.492C184.462 160.487 175.168 198.335 160.754 199.946C146.529 201.556 127.184 166.93 93.6131 152.435C60.0426 137.94 12.2474 143.308 2.19519 123.982C-8.04664 104.924 19.4546 60.9023 45.2489 36.2074C71.0431 11.5124 95.1304 5.87549 122.442 2.11756C149.754 -1.37194 180.289 -2.98248 192.807 17.4177Z',
                ],
                // 'shape-02' => [
                //     'M158.797 20.1118C174.734 35.4854 185.771 63.7522 191.431 88.8329C194.029 100.343 217.637 194.172 173.136 199.188C138.524 203.089 123.943 191.664 72.2659 191.664C48.2728 191.664 2.54701 176.615 0.0746313 126.97C-1.8222 88.8815 32.9162 48.7945 49.8199 32.6521C69.2551 14.0924 91.5501 0.548809 109.225 0.0471959C137.029 -0.741914 146.723 8.46508 158.797 20.1118Z',
                //     'M194.2 10.0179C206.828 23.5246 196.466 57.9196 185.619 81.7919C174.772 105.664 163.278 119.014 150.65 141.63C138.184 164.403 124.423 196.599 107.423 199.74C90.4244 202.881 70.1873 176.967 50.9217 154.351C31.4941 131.578 12.8761 111.946 4.61935 84.3048C-3.63736 56.5061 -1.6946 20.8547 17.571 7.34802C36.9986 -6.00162 73.749 2.79344 110.014 3.42165C146.117 4.04987 181.572 -3.48876 194.2 10.0179Z',
                // ],
                'shape-03' => [
                    'M130.838 43.7569C140.548 60.2952 147.214 71.1793 161.705 83.901C176.342 96.6227 198.804 111.323 199.963 124.893C200.978 138.463 180.834 151.043 164.894 164.755C148.953 178.466 137.07 193.59 121.999 198.255C106.927 202.92 88.5231 197.266 69.3942 192.601C50.1204 187.795 30.267 183.837 22.0068 171.681C13.6017 159.525 16.7898 139.17 12.7322 119.098C8.67457 99.0257 -2.62885 79.2364 0.559292 63.4049C3.89235 47.5735 21.717 35.6999 39.9763 22.9782C58.0908 10.2564 76.64 -3.45474 92.4358 0.785834C108.087 5.0264 120.984 27.2187 130.838 43.7569Z',
                    'M163.362 15.4463C177.825 19.3957 191.805 31.0645 197.269 46.3236C202.572 61.5828 199.358 80.7913 194.377 97.3071C189.234 114.002 182.324 128.364 175.414 149.727C168.665 170.91 161.916 199.274 149.703 199.992C137.651 200.531 120.135 173.244 110.333 153.497C100.531 133.57 98.4415 121.004 76.7476 115.08C54.8931 109.155 13.7551 109.873 2.98854 99.1023C-7.61734 88.1516 12.3089 65.8912 26.7714 45.067C41.3947 24.2427 50.5543 5.03418 65.0169 0.905241C79.3188 -3.2237 98.7629 7.90649 116.118 11.4969C133.473 15.2668 148.739 11.4969 163.362 15.4463Z',
                ],
                'shape-04' => [
                    'M148.658 9.08059C156.844 21.0077 154.86 48.0426 164.783 69.8296C174.706 91.6165 196.66 108.155 199.637 125.331C202.614 142.506 186.737 160.317 171.233 172.721C155.852 185.284 140.844 192.441 125.835 196.734C110.951 201.028 95.942 202.3 86.2671 192.918C76.5921 183.694 72.2508 163.657 58.4826 150.298C44.7145 136.781 21.5194 129.942 9.61184 112.926C-2.29576 95.7513 -2.91595 68.2393 6.5109 48.6787C15.8137 28.9592 35.1636 17.1911 53.149 10.8299C71.0104 4.30972 87.3834 3.51459 104.873 1.60624C122.238 -0.143073 140.471 -3.0056 148.658 9.08059Z',
                    'M141.398 31.2594C158.801 45.6794 180.055 52.7178 191.214 69.8845C202.373 87.0512 203.701 114.346 190.55 129.625C177.531 144.731 150.033 147.821 135.022 157.95C120.011 168.25 117.487 185.588 110.579 191.253C103.804 197.09 92.9111 191.253 75.9074 193.313C58.9036 195.202 36.0548 204.987 25.4275 196.747C14.8001 188.507 16.3942 162.241 11.2134 135.633C6.03258 108.853 -5.6575 81.5579 3.2429 70.9145C12.1433 60.0995 41.8999 65.9362 60.8963 52.5461C79.8926 39.1561 88.1288 6.36761 99.1547 0.874254C110.181 -4.6191 123.863 17.011 141.398 31.2594Z',
                ],
                'shape-05' => [
                    'M157.858 46.5167C175.891 58.1858 199.365 60.6686 199.987 69.3583C200.609 78.0481 178.223 92.8207 176.047 110.448C174.026 128.2 192.059 148.683 192.214 167.304C192.525 185.801 174.803 202.436 155.837 199.705C136.716 196.849 116.507 174.504 90.7006 168.173C64.7391 161.718 33.4922 171.153 16.3918 165.194C-0.863967 159.235 -3.66221 138.007 4.11067 121.124C12.039 104.242 30.6939 91.7034 37.3786 72.586C43.9078 53.4685 38.6223 27.7717 49.3488 13.4956C60.2309 -0.904545 87.2805 -3.88389 107.179 5.05414C127.078 13.9922 139.67 34.8476 157.858 46.5167Z',
                    'M169.46 2.11112C188.036 0.84197 202.466 21.1484 199.647 41.9988C196.827 62.8491 176.592 84.0621 169.626 104.369C162.493 124.675 168.464 143.894 165.147 161.843C161.664 179.611 148.893 196.291 133.633 199.374C118.208 202.637 100.295 192.121 75.581 191.396C50.7015 190.852 18.8557 199.918 9.40148 187.951C0.113135 175.985 13.0505 142.806 12.7188 114.159C12.387 85.5126 -1.37963 61.3987 0.113137 39.6418C1.77177 17.8849 18.524 -1.69633 38.5934 0.116743C58.6629 2.11113 81.8838 25.3185 104.939 25.4998C127.994 25.8624 150.883 3.19897 169.46 2.11112Z',
                ],
                'shape-06' => [
                    'M122.318 17.7375C140.562 27.4244 160.766 36.4864 176.598 53.2042C192.43 69.7658 203.889 94.1394 198.762 114.607C193.636 134.918 171.924 151.324 153.378 166.635C134.682 181.947 119.302 196.009 101.963 199.29C84.6235 202.415 65.4748 194.603 46.4768 188.04C27.4788 181.478 8.63164 176.01 6.06842 162.104C3.5052 148.199 17.226 125.7 16.1705 106.482C14.9643 87.1086 -0.867346 71.172 0.0373196 60.7038C0.791207 50.2356 18.583 45.2359 33.3592 33.6741C48.1354 22.2685 59.896 4.3007 73.9183 0.707155C87.9406 -2.88639 104.074 7.89425 122.318 17.7375Z',
                    'M150.779 2.00785C168.823 -3.65707 190.422 2.952 197.393 18.2158C204.228 33.3223 196.436 56.9261 187.688 75.4945C178.802 94.0628 168.96 107.753 160.758 128.524C152.556 149.296 145.994 177.148 131.094 190.524C116.194 203.899 92.9548 202.641 76.961 189.737C60.8304 176.834 51.8083 152.286 38.0016 132.931C24.0583 113.733 5.19378 99.8851 0.956094 82.1036C-3.28159 64.322 7.10757 42.6065 21.461 30.0177C35.9512 17.429 54.4056 13.9671 68.8957 17.9011C83.2492 21.8351 93.775 33.1649 106.078 29.5457C118.518 26.0838 132.871 7.83013 150.779 2.00785Z',
                ],
                'shape-07' => [
                    'M137.789 1.76327C153.288 6.40962 166.614 21.6762 179.795 41.0913C192.831 60.5064 205.723 83.904 197.322 99.1706C188.92 114.437 159.371 121.407 144.162 143.643C128.808 165.879 127.794 203.547 119.828 199.731C111.716 196.08 96.7966 151.11 85.0639 130.036C73.476 109.127 65.2196 112.28 51.459 111.284C37.5535 110.455 17.9989 105.31 7.85953 92.533C-2.27988 79.5896 -3.00412 59.0129 8.00438 49.7202C18.868 40.4275 41.4644 42.5847 56.5287 37.6065C71.5929 32.4623 79.2699 20.3487 92.0166 11.3878C104.618 2.59298 122.29 -3.04902 137.789 1.76327Z',
                    'M119.223 38.4649C130.349 48.3432 139.529 60.5654 155.244 76.6385C170.96 92.7116 193.074 112.803 198.637 136.243C204.061 159.683 192.796 186.639 173.742 196.015C154.688 205.223 127.846 196.852 109.348 187.309C90.851 177.598 80.8373 166.547 67.2076 164.203C53.5779 161.859 36.4712 168.222 22.0071 163.366C7.4038 158.678 -4.55697 142.605 1.70157 129.546C7.96011 116.486 32.7161 106.273 37.862 82.3311C42.8689 58.2214 28.4047 20.3827 32.438 6.65358C36.6103 -7.07553 59.1411 3.30501 77.0822 12.1787C94.8843 21.2198 107.958 28.5867 119.223 38.4649Z',
                ],
                'shape-08' => [
                    'M153.865 17.501C163.37 25.4437 172.74 31.8972 187.38 61.6827C194.213 75.5825 205.507 120.74 196.885 154.017C192.383 171.392 179.377 197.206 157.367 195.221C135.357 193.235 137.558 129.792 121.351 133.664C105.144 137.437 111.346 186.285 95.3385 186.285C85.2496 186.285 80.8325 157.492 71.3277 159.975C61.0126 162.668 60.8235 190.504 43.3153 199.192C29.3091 206.142 2.92668 167.147 0.296176 124.729C-1.70485 92.4608 6.79892 66.6468 14.8029 51.7542C35.2415 13.7248 65.3254 3.68729 82.3331 1.11898C99.3407 -1.44932 131.792 -0.945339 153.865 17.501Z',
                    'M103.149 42.1523C125.111 50.4455 157.085 48.8993 177.109 60.1444C196.971 71.2489 204.723 95.1446 197.133 114.683C189.543 134.221 166.612 149.261 144.974 158.117C123.173 166.832 102.665 169.362 81.5104 178.498C60.5175 187.635 38.8787 203.519 24.6681 199.302C10.296 194.944 3.35219 170.486 1.57586 151.51C-0.361943 132.675 2.86773 119.321 7.38928 109.341C11.7493 99.3614 17.4013 92.6144 13.3642 74.3412C9.32709 56.2086 -4.39904 26.5498 1.41438 11.7907C7.2278 -2.96838 32.4193 -2.82782 50.9899 6.44933C69.5606 15.5859 81.1874 33.7185 103.149 42.1523Z',
                ],
                'shape-09' => [
                    'M120.732 44.3594C134.09 54.4597 156.397 46.7041 172.997 55.1812C189.598 63.4779 200.621 87.827 199.973 111.455C199.454 135.263 187.393 158.349 168.977 156.365C150.561 154.381 125.92 127.327 108.801 133.459C91.5522 139.591 81.8254 178.73 67.9486 193.159C54.0718 207.588 36.1746 197.308 21.9088 179.812C7.51318 162.137 -2.99169 137.066 0.769311 114.701C4.53031 92.336 22.8166 72.4961 37.0825 58.9688C51.3483 45.4416 61.5938 38.227 71.3206 25.782C81.0473 13.3369 89.9959 -4.15834 96.6101 0.891838C103.224 6.12237 107.504 34.2591 120.732 44.3594Z',
                    'M147.363 15.0049C167.917 8.68118 192.012 17.9235 198.273 36.246C204.535 54.5685 192.964 82.1333 176.493 97.5372C160.158 113.103 139.059 116.67 129.394 135.155C119.729 153.64 121.771 186.88 114.012 196.608C106.389 206.499 89.1014 192.717 74.6722 181.367C60.243 169.854 48.8085 160.612 38.7353 148.775C28.6621 137.101 19.9501 122.67 12.191 103.861C4.56804 85.2141 -2.10206 62.1894 0.620433 39.8132C3.47905 17.5992 15.7303 -3.80406 32.7458 0.573881C49.6253 4.78968 71.1329 35.111 90.4626 39.1646C109.656 43.2183 126.808 21.3286 147.363 15.0049Z',
                ], 
                'shape-10' => [
                    'M110.587 54.2315C121.166 71.729 136.822 70.5721 156.993 78.3809C177.304 86.1897 201.989 102.964 199.873 115.545C197.898 128.126 169.123 136.513 148.107 140.273C127.231 144.032 114.113 143.02 99.1615 157.915C84.2099 172.665 67.2837 203.321 61.2184 199.706C55.2943 196.091 60.3721 158.059 46.408 138.682C32.5849 119.16 -0.2803 118.292 0.00180471 113.087C0.283909 108.025 33.8543 98.6259 51.2037 90.8171C68.5532 83.1529 69.6816 76.9348 73.3489 55.9668C76.8752 35.1434 82.9405 -0.42989 88.5826 0.00393124C94.3657 0.293145 99.8667 36.5895 110.587 54.2315Z',
                    'M153.935 0.205183C167.61 -2.1484 176.178 16.1371 184.91 34.9658C193.643 53.7944 202.705 73.1662 199.245 89.6413C195.62 106.116 179.638 119.514 167.28 138.342C154.923 156.99 146.355 181.069 130.538 192.475C114.885 203.881 91.983 202.794 81.2733 185.957C70.3988 169.12 71.717 136.532 56.7234 118.79C41.7298 101.047 10.5893 97.9694 2.35103 86.5636C-5.8872 74.9767 8.77685 55.2428 26.901 47.82C45.0251 40.2161 66.6093 45.1043 79.9552 47.4579C93.1364 49.6304 98.0793 49.0873 109.942 37.3194C121.641 25.5515 140.424 2.55876 153.935 0.205183Z',
                ], 
                'shape-11' => [
                    'M144.212 4.94253C170.632 13.3719 192.335 32.4489 198.185 53.8921C204.224 75.3353 194.411 99.1446 180.257 116.891C166.103 134.637 147.42 146.32 129.869 154.601C112.318 163.03 96.0883 167.911 76.6502 178.706C57.4008 189.502 34.9432 206.213 27.5832 197.635C20.2231 189.206 27.9606 155.488 30.2252 136.855C32.6786 118.37 29.4704 114.968 21.1667 108.609C12.8631 102.25 -0.724712 92.7856 0.0301655 87.166C0.785043 81.5464 15.5051 79.6239 27.2057 65.8707C38.9063 52.2654 47.21 26.5336 67.0255 12.6325C86.6523 -1.26859 117.98 -3.48686 144.212 4.94253Z',
                    'M152.236 6.82883C170.493 13.326 191.925 22.0971 198.01 39.1521C204.228 56.3695 195.1 81.7083 181.341 96.9765C167.582 112.082 149.193 117.118 136.889 121.016C124.718 124.914 118.5 127.838 108.049 147.004C97.7295 166.009 83.1769 201.418 77.0912 199.956C71.0056 198.494 73.2546 160.161 57.2467 140.02C41.1066 119.879 6.70943 117.767 0.888383 107.047C-4.80037 96.3268 17.9546 76.9979 37.1377 68.2267C56.453 59.4556 72.064 61.2423 82.3831 50.1972C92.8345 39.1521 97.9941 15.4375 108.181 5.69183C118.5 -4.05387 133.846 0.331699 152.236 6.82883Z',
                ],
                'shape-12' => [
                    'M160.284 8.38116C171.33 17.7008 166.912 40.8497 164.903 57.535C163.096 74.0699 163.498 83.9909 173.539 101.428C183.379 118.714 202.86 143.517 199.646 162.908C196.433 182.299 170.727 196.278 143.214 199.284C115.701 202.441 86.38 194.625 59.4692 186.207C32.5584 177.789 8.25834 168.77 1.83188 153.588C-4.59458 138.256 7.05337 116.61 18.7013 102.029C30.5501 87.4482 42.3989 79.9323 41.1939 62.0445C40.1898 44.307 26.1319 16.4983 33.1609 5.82576C40.1898 -4.69647 68.3056 2.06782 95.618 2.96972C122.93 4.02195 149.239 -0.788211 160.284 8.38116Z',
                    'M154.032 1.96703C178.292 6.39445 204.514 19.066 199.341 33.875C193.989 48.684 157.242 65.325 148.502 90.5155C139.939 115.553 159.205 148.988 153.318 170.82C147.431 192.804 116.393 203.033 88.3867 199.216C60.3806 195.552 35.5855 177.843 20.2446 158.606C5.08203 139.37 -0.804594 118.607 0.0873192 100.744C0.979232 82.882 8.29292 67.9203 19.3526 58.6075C30.4124 49.4473 44.8614 45.7832 56.4562 37.6917C68.0511 29.7529 76.6135 17.3867 91.9544 9.29517C107.474 1.20368 129.771 -2.61307 154.032 1.96703Z',
                ],
            ];

            return [
                'first' => array_shift($shapes[$ekit_morphing_style]),
                'lasts' => json_encode($shapes[$ekit_morphing_style]),
            ];
        }
    }

	protected function curl_get_file_contents($url) {
		$response = wp_remote_get($url);
		if (!is_wp_error($response)) {
			return $response['body'];
		}
		return false;
	}
}