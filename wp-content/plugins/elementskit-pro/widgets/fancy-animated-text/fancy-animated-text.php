<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Fancy_Animated_Text_Handler as Handler;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Fancy_Animated_Text extends Widget_Base {
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
        return Handler::get_help_url();
    }

    protected function register_controls() {

        // Settings options section
        $this->start_controls_section(
            'ekit_section_fancy_text',
            [
                'label' => esc_html__('Fancy Text', 'elementskit'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // fancy heading animated start
        $this->add_control(
            'fancy_text_animation',
            [
                'label' => esc_html__( 'Animation', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'ekit_fancy_animation_style',
            [
                'label' => esc_html__('Animation Style', 'elementskit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'animated',
                'options' => [
                    'animated' => esc_html__('Text', 'elementskit'),
                    'highlighted' => esc_html__('SVG', 'elementskit'),
                ],
            ]
        );

        // animated type start
        $this->add_control(
            'ekit_fancy_animation_type',
            [
                'label' => esc_html__('Animation Type', 'elementskit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'clip',
                'options' => [
                    'clip' => esc_html__('Clip', 'elementskit'),
                    'rotate-1' => esc_html__('Flip Rotate', 'elementskit'),
                    'rotate-2' => esc_html__('Latter FadeIn', 'elementskit'),
                    'rotate-3' => esc_html__('Latter Rotate', 'elementskit'),
                    'type' => esc_html__('Typing Latter', 'elementskit'),
                    'bar-loading' => esc_html__('Bar Loading', 'elementskit'),
                    'slide' => esc_html__('Slide Top', 'elementskit'),
                    'zoom-out' => esc_html__('Zoom Out', 'elementskit'),
                    'scale' => esc_html__('Scale In', 'elementskit'),
                    'push' => esc_html__('Push Left', 'elementskit'),
                    'color-effect' => esc_html__('Color Effect', 'elementskit'),
                    'bouncing' => esc_html__('Bouncing Effect', 'elementskit'),
                ],
                'condition'	=> [
                    'ekit_fancy_animation_style' => ['animated'],
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_animation_delay',
            [
                'label' => esc_html__('Animation Delay (ms)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 2500,
                'min'  => 1,
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['rotate-1', 'rotate-2', 'rotate-3', 'slide', 'zoom-out', 'scale', 'push', 'color-effect', 'bouncing'],
                    'ekit_fancy_animation_style' => ['animated'],
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_loading_bar',
            [
                'label' => esc_html__('Animation Delay (ms)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3800,
                'min'  => 1,
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['bar-loading'],
                    'ekit_fancy_animation_style' => ['animated'],
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_letters_delay',
            [
                'label' => esc_html__('Letters Delay (ms)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 50,
                'min'  => 1,
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['rotate-2', 'rotate-3', 'scale', 'bouncing'],
                    'ekit_fancy_animation_style' => ['animated'],
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_letters_delay_bar',
            [
                'label' => esc_html__('Letters Delay (ms)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 300,
                'min'  => 200,
                'max'  => 1000,
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['bar-loading'],
                ],
                'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-fancy-text.bar-loading .ekit-fancy-text-lists b.is-visible' => 'transition: {{VALUE}}ms ease-in-out;',
				],
            ]
        );

        $this->add_control(
            'ekit_fancy_type_letters_delay',
            [
                'label' => esc_html__('Type Letters Delay (ms)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 150,
                'min'  => 1,
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['type'],
                    'ekit_fancy_animation_style' => ['animated'],
                ],
            ]
        ); 

        $this->add_control(
            'ekit_fancy_selection_duration',
            [
                'label' => esc_html__('Selection Duration (ms)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 500,
                'min'  => 1,
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['type'],
                    'ekit_fancy_animation_style' => ['animated'],
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_reveal_duration',
            [
                'label' => esc_html__('Reveal Duration (ms)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 600,
                'min'  => 1,
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['clip'],
                    'ekit_fancy_animation_style' => ['animated'],
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_reveal_animation_delay',
            [
                'label' => esc_html__('Reveal Animation Delay (ms)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1500,
                'min'  => 1,
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['clip'],
                    'ekit_fancy_animation_style' => ['animated'],
                ],
            ]
        );

        // highlighted type start
        $this->add_control(
            'ekit_fancy_highlighted_type',
            [
                'label' => esc_html__('SVG Type', 'elementskit'),
				'type' => Controls_Manager::SELECT,
                'default' => 'circle-01',
				'options' => [
                    'arrow' => esc_html__('Arrow', 'elementskit'),
					'circle-01' => esc_html__('Circle 01', 'elementskit'),
					'circle-02' => esc_html__('Circle 02', 'elementskit'),
                    'clouds' => esc_html__('Clouds', 'elementskit'),
					'curly' => esc_html__('Curly', 'elementskit'),
					'double-line' => esc_html__('Double line', 'elementskit'),
					'double-underline' => esc_html__('Double underline', 'elementskit'),
					'shape-x' => esc_html__('Shape X', 'elementskit'),
					'zigzag' => esc_html__('Zigzag', 'elementskit'),
					'waves' => esc_html__('Waves', 'elementskit'),
					'round-line-01' => esc_html__('Round Line 01', 'elementskit'),
					'round-line-02' => esc_html__('Round Line 02', 'elementskit'),
					
				],
                'condition'	=> [
                    'ekit_fancy_animation_style' => ['highlighted'],
                ],
            ]
        );
        
        $this->add_control(
            'ekit_fancy_highlighted_animation_delay',
            [
                'label' => esc_html__('Animation Delay (s)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'min'  => 1,
                'default' => 2,
                'condition'	=> [
                    'ekit_fancy_highlighted_type' => ['circle-01', 'circle-02', 'curly', 'double-line', 'arrow', 'double-underline', 'shape-x', 'zigzag','waves', 'clouds', 'round-line-01', 'round-line-02'],
                    'ekit_fancy_animation_style' => ['highlighted'],
                ],
                'selectors' => [
					'{{WRAPPER}} .ekit-fancy-text .ekit-highlighted-effect svg path' => '-webkit-animation-delay: {{VALUE}}s; animation-delay: {{VALUE}}s;',
					'{{WRAPPER}} .ekit-fancy-text .ekit-highlighted-effect svg.ekit-svg-shape-x path:first-child' => '-webkit-animation-delay: -webkit-calc({{VALUE}}s + 0.3s); animation-delay: calc({{VALUE}}s + 0.3s);',
					'{{WRAPPER}} .ekit-fancy-text .ekit-highlighted-effect svg.ekit-svg-double-underline path:last-child' => '-webkit-animation-delay: -webkit-calc({{VALUE}}s + 0.3s); animation-delay: calc({{VALUE}}s + 0.3s);',
					'{{WRAPPER}} .ekit-fancy-text .ekit-highlighted-effect svg.ekit-svg-double-line path:last-child' => '-webkit-animation-delay: -webkit-calc({{VALUE}}s + 0.3s); animation-delay: calc({{VALUE}}s + 0.3s);',
				],   
            ]
        );
        
        $this->add_control(
            'ekit_fancy_highlighted_duration',
            [
                'label' => esc_html__('Animation Duration (s)', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 5,
                'min'  => 1,
                'condition'	=> [
                    'ekit_fancy_highlighted_type' =>  ['circle-01', 'circle-02', 'curly', 'double-line', 'arrow', 'double-underline', 'shape-x', 'zigzag', 'waves', 'clouds', 'round-line-01', 'round-line-02'],
                    'ekit_fancy_animation_style' => ['highlighted'],
                ],
                'selectors' => [
					'{{WRAPPER}} .ekit-fancy-text .ekit-highlighted-effect svg path' => '-webkit-animation-duration: {{VALUE}}s; animation-duration: {{VALUE}}s;',
				],
            ]
        );

        $this->add_control(
			'ekit_fancy_highlighted_loop',
			[
				'label' => esc_html__( 'Loop', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
                'prefix_class' => 'ekit-highlighted-loop-',
				'selectors' => [
					'{{WRAPPER}} .ekit-fancy-text .ekit-highlighted-effect svg path' => '-webkit-animation-iteration-count: infinite; animation-iteration-count: infinite;',
				],
                'condition'	=> [
                    'ekit_fancy_highlighted_type' =>  ['circle-01', 'circle-02', 'curly', 'double-line', 'arrow', 'double-underline', 'shape-x', 'zigzag', 'waves', 'clouds', 'round-line-01', 'round-line-02'],
                    'ekit_fancy_animation_style' => ['highlighted'],
                ],
			]
		);
        
        // Heading Content start
        $this->add_control(
            'fancy_text_content',
            [
                'label' => esc_html__( 'Content', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'ekit_fancy_prefix_text',
            [
                'label' => esc_html__('Prefix Text', 'elementskit'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Elementskit is ', 'elementskit'),
                'description' => esc_html__('Text before fancy text', 'elementskit'),
                'label_block' => true,
                'dynamic' => [
                   'active' => true,
                ],
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'ekit_fancy_text', 
            [
                'label' => esc_html__('Fancy Text', 'elementskit'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Addon' , 'elementskit'),
                'label_block' => true,
                'dynamic' => [
                   'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'ekit_fancy_text_color',
            [
                'label' => esc_html__('Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text .ekit-fancy-text-lists .ekit-fancy-text{{CURRENT_ITEM}}' => 'color: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_control(
            'ekit_fancy_text_background_color',
            [
                'label' => esc_html__('Background Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text .ekit-fancy-text-lists .ekit-fancy-text{{CURRENT_ITEM}}' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_text_lists',
            [
                'label' => esc_html__('Fancy Lists', 'elementskit'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'ekit_fancy_text' => esc_html__('Most', 'elementskit'),
                    ],
                    [
                        'ekit_fancy_text' => esc_html__('Popular', 'elementskit'),
                    ],
                    [
                        'ekit_fancy_text' => esc_html__( 'Addon', 'elementskit'),
                    ],
                ],
                'title_field' => '{{{ ekit_fancy_text }}}',
                'condition'	=> [
                    'ekit_fancy_animation_style' => ['animated'],
                ],
            ]
        );

        // fancy heading animated content start
        $this->add_control(
            'ekit_fancy_highlighted_text',
            [
                'label' => esc_html__('Highlighted Text', 'elementskit'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Popular Addon', 'elementskit'),
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'condition'	=> [
                    'ekit_fancy_animation_style' => ['highlighted'],
                ],
            ]
        );

       // fancy heading animated content end

        $this->add_control(
            'ekit_fancy_suffix_text',
            [
                'label' => esc_html__('Suffix Text', 'elementskit'),
                'type' => Controls_Manager::TEXT,
                'description' => esc_html__('Text after fancy text', 'elementskit'),
                'label_block' => true,
                'dynamic' => [
                   'active' => true,
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_text_title_tag',
            [
                'label' => esc_html__('Title HTML Tag', 'elementskit'),
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
                'default' => 'h2',
            ]
        );

        $this->add_control( 
            'ekit_fancy_text_link', 
            [
                'label' =>esc_html__('Link (Optional)', 'elementskit'),
                'type' => Controls_Manager::URL,
                'label_block' => true,
                'placeholder' => esc_html__( 'https://your-link.com', 'elementskit'),
                'autocomplete' => false,
                'options' => ['is_external', 'nofollow', 'custom_attributes'],
                'dynamic' => [
                    'active' => true,
                ],
           ]
       );

        $this->end_controls_section();

        /** widget style controls */
        // heading style section
        $this->start_controls_section(
            'ekit_heading_style_section',
            [
                'label' => esc_html__('Heading Text', 'elementskit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'ekit_fancy_text_alignment',
            [
                'type' => Controls_Manager::CHOOSE,
                'label' => esc_html__('Alignment', 'elementskit'),
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
                'default'   => 'left',
                'toggle'    => true,
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_fancy_heading_typography',
                'selector'	 => '{{WRAPPER}} .ekit-fancy-text, {{WRAPPER}} .ekit-fancy-text a',
            ]
        );

        $this->add_control(
            'ekit_fancy_heading_color',
             [
                'label'		 =>esc_html__('Color', 'elementskit'),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .ekit-fancy-text, {{WRAPPER}} .ekit-fancy-text a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_heading_color_hover', 
            [
                'label'		 =>esc_html__('Hover Color', 'elementskit'),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .ekit-fancy-text:hover, {{WRAPPER}} .ekit-fancy-text:hover a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_fancy_heading_shadow',
                'selector' => '{{WRAPPER}} .ekit-fancy-text',
            ]
        );

        $this->end_controls_section();
        

        // heading fancy lists style section
        $this->start_controls_section(
            'ekit_fancy_lists_style_section',
            [
                'label' => esc_html__('Fancy Text Lists', 'elementskit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'	=> [
                    'ekit_fancy_animation_style' => ['animated'],
                ],
            ]
        );
        
        $this->add_responsive_control(
            'ekit_fancy_lists_typography_alignment',
            [
                'type' => Controls_Manager::CHOOSE,
                'label' => esc_html__('Alignment', 'elementskit'),
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
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['rotate-1'],
                ],
                'default'   => '',
                'toggle'    => true,
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-fancy-text-lists' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'ekit_fancy_lists_typography',
                'selector' => '{{WRAPPER}} .ekit-fancy-text .ekit-fancy-text-lists b',
            ]
        );

        $this->add_control(
            'ekit_fancy_lists_color', 
            [
                'label' => esc_html__('Text Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text .ekit-fancy-text-lists b' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_fancy_lists_color_effect_color',
			[
				'label' => esc_html__( 'Text Gradient Color', 'elementskit' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['color-effect'],
                ],
			]
		);

		$this->start_popover();

		$gradient_color = '{{WRAPPER}} .ekit-fancy-text.color-effect .ekit-fancy-text-lists .ekit-fancy-text';
        $this->add_control(
            'gradient_color_01', 
            [
                'label' => esc_html__('Color One', 'elementskit'),
                'type' => Controls_Manager::COLOR,

            ]
        );

        $this->add_control(
            'gradient_color_02', 
            [
                'label' => esc_html__('Color Two', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
					$gradient_color => 'background-image: linear-gradient(-120deg, {{gradient_color_01.VALUE}} 0%, {{gradient_color_02.VALUE}} 100%)!important'
				],
            ]
        );

        $this->add_control(
            'gradient_color_03', 
            [
                'label' => esc_html__('Color Three', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $gradient_color => 'background-image: linear-gradient(-120deg, {{gradient_color_01.VALUE}} 0%, {{gradient_color_02.VALUE}} 50%, {{gradient_color_03.VALUE}} 100%)!important',
                ],
            ]
        );

        $this->add_control(
            'gradient_color_04', 
            [
                'label' => esc_html__('Color Four', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $gradient_color => 'background-image: linear-gradient(-120deg, {{gradient_color_01.VALUE}} 0%, {{gradient_color_02.VALUE}} 29%, {{gradient_color_03.VALUE}} 67%, {{gradient_color_04.VALUE}} 100%)!important',
                ],
            ]
        );

		$this->end_popover();

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_fancy_lists_background_color',
				'label' => esc_html__( 'Background Color', 'elementskit'),
				'types' => [ 'classic', 'gradient'],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ekit-fancy-text .ekit-fancy-text-lists',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
			]
		);

        $this->add_responsive_control(
            'ekit_fancy_lists_padding',
            [
                'label' => esc_html__('Padding', 'elementskit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%' ],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text .ekit-fancy-text-lists b' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
           ]
        );

        $this->add_responsive_control(
            'ekit_fancy_lists_margin',
            [
                'label' => esc_html__('Margin', 'elementskit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'allowed_dimensions' => ['right', 'left'],
                'placeholder' => [
					'top' => 'auto',
					'right' => '',
					'bottom' => 'auto',
					'left' => '',
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text .ekit-fancy-text-lists' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_fancy_lists_border',
                'label' => esc_html__( 'Border', 'elementskit'),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .ekit-fancy-text .ekit-fancy-text-lists',
            ]
        );

        $this->end_controls_section();

        // Fancy heading highlighted style sart
        $this->start_controls_section(
            'ekit_fancy_highlighted_style_section',
            [
                'label' => esc_html__('Highlighted Text', 'elementskit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'	=> [
                    'ekit_fancy_animation_style' => ['highlighted'],
                ],
            ]

        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'ekit_fancy_highlighted_typography',
                'selector' => '{{WRAPPER}} .ekit-fancy-heading .ekit-highlighted-text',
            ]
        );

        $this->add_control(
            'ekit_fancy_highlighted_color', 
            [
                'label' => esc_html__('Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text .ekit-highlighted-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_fancy_highlighted_padding',
            [
                'label' => esc_html__('Padding', 'elementskit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%' ],
                'separator' => 'before',
                'default' => [
					'top' => 5,
					'right' => 5,
					'bottom' => 5,
					'left' => 5,
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text .ekit-highlighted-effect' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ekit_fancy_highlighted_style_svg_section',
            [
                'label' => esc_html__('SVG', 'elementskit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'	=> [
                    'ekit_fancy_animation_style' => ['highlighted'],
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_highlighted_svg_color', 
            [
                'label' => esc_html__('Stroke Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'default' => '#F386B9',
                'selectors' => [
					'{{WRAPPER}} .ekit-fancy-text .ekit-highlighted-effect svg path' => 'stroke: {{VALUE}};',
				],
            ]
        );

        $this->add_control(
			'ekit_fancy_highlighted_weight',
			[
				'label' => esc_html__( 'Stroke Weight (px)', 'elementskit' ),
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
					'{{WRAPPER}} .ekit-fancy-text .ekit-highlighted-effect svg path' => 'stroke-width: {{SIZE}}{{UNIT}}',
				],	
			]
		);
        
        $this->add_control(
			'ekit_fancy_stroke_position',
			[
				'label' => esc_html__( 'Stroke Position (%)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-fancy-text .ekit-highlighted-effect svg' => 'left: {{SIZE}}{{UNIT}}',
				],	
			]
		);

        $this->end_controls_section();

        //Heading fancy cursor style section
        $this->start_controls_section(
            'ekit_fancy_cursor_style_section',
            [
                'label' => esc_html__('Fancy Cursor', 'elementskit'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['clip', 'bar-loading','type'],
                    'ekit_fancy_animation_style' => ['animated'],
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_cursor_color',
             [
                'label' => esc_html__('Cursor Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'default' => "#333333",
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text.clip .ekit-fancy-text-lists::after' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-fancy-text.type .ekit-fancy-text-lists::after' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-fancy-text.bar-loading .ekit-fancy-text-lists::after' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_cursor_width',
            [
                'label' => esc_html__( 'Cursor Width', 'elementskit'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 2,
                ],
               'condition'	=> [
                    'ekit_fancy_animation_type' => ['clip','type'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text.clip .ekit-fancy-text-lists::after' => 'width: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .ekit-fancy-text.type .ekit-fancy-text-lists::after' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        ); 

        $this->add_control(
            'ekit_fancy_cursor_height',
            [
                'label' => esc_html__( 'Cursor Height', 'elementskit'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'condition'	=> [
                    'ekit_fancy_animation_type' => ['clip','type'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text.clip .ekit-fancy-text-lists::after' => 'height: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .ekit-fancy-text.type .ekit-fancy-text-lists::after' => 'height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_fancy_loading_bar_height',
            [
                'label' => esc_html__( 'Loading Bar Height', 'elementskit'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 15,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 2,
                ],
                'condition'	=> [
                    'ekit_fancy_animation_type' => 'bar-loading',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-fancy-text.bar-loading .ekit-fancy-text-lists::after' => 'height: {{SIZE}}{{UNIT}}',
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
        // image effect class
		$animation_types = [
			'clip' => 'clip is-full-width',
			'rotate-1' => 'rotate-1',
			'rotate-2' => 'letters rotate-2',
			'rotate-3' => 'letters rotate-3',
			'type' => 'letters type',
			'bar-loading' => 'bar-loading',
			'slide' => 'slide',
			'zoom-out' => 'zoom-out',
			'scale' => 'letters scale',
			'push' => 'push',
			'color-effect' => 'color-effect',
			'bouncing' => 'letters bouncing'
		];
		
		$fancy_animation_type_class = isset($animation_types[$ekit_fancy_animation_type]) ? $animation_types[$ekit_fancy_animation_type] : 'clip is-full-width';

        // Sanitize title tags
        $options_ekit_text_title_tag = array_keys([
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

        $title_tag = \ElementsKit_Lite\Utils::esc_options($ekit_fancy_text_title_tag, $options_ekit_text_title_tag, 'h2');

		$fancy_animation_settings = [
			'animationStyle' => $ekit_fancy_animation_style,
			'animationDelay' => !empty($ekit_fancy_animation_delay) ? (int) $ekit_fancy_animation_delay : 2500 ,
			'loadingBar' => !empty($ekit_fancy_loading_bar) ? (int) $ekit_fancy_loading_bar : 3800,
			'lettersDelay' => !empty($ekit_fancy_letters_delay) ? (int) $ekit_fancy_letters_delay : 50,
			'typeLettersDelay' => !empty($ekit_fancy_type_letters_delay) ? (int) $ekit_fancy_type_letters_delay :150,
			'duration' => !empty($ekit_fancy_selection_duration) ? (int) $ekit_fancy_selection_duration : 500,
			'revealDuration' => !empty($ekit_fancy_reveal_duration) ? (int) $ekit_fancy_reveal_duration : 600,
			'revealAnimationDelay' => !empty($ekit_fancy_reveal_animation_delay) ? (int) $ekit_fancy_reveal_animation_delay : 1500,
		];

		$this->add_render_attribute( 'fancy-text-wrap', [
			'class' => [
				'ekit-fancy-text' . ('highlighted' !== $ekit_fancy_animation_style ? ' ' . esc_attr($fancy_animation_type_class) : '')
			],
			'data-id' => $this->get_id(),
			'data-animation-settings' => wp_json_encode($fancy_animation_settings),

		] );

        ?>
            <<?php echo esc_attr($title_tag); ?> <?php $this->print_render_attribute_string('fancy-text-wrap'); ?>>
                <?php
                $fancy_content = $this->get_fancy_content($settings);

                // wrap with link
                if (!empty($ekit_fancy_text_link['url'])) {
                    $this->add_link_attributes('link', $ekit_fancy_text_link);
                    $fancy_content = sprintf('<a %1$s>%2$s</a>', $this->get_render_attribute_string('link'), $fancy_content);
                }

                // echo final output
                echo wp_kses($fancy_content, \ElementsKit_Lite\Utils::get_kses_array());
                ?>
            </<?php echo esc_attr($title_tag); ?>>
        <?php
    }

    protected function get_fancy_content($settings) {
        
        extract($settings);
        ob_start();

        if(!empty($ekit_fancy_prefix_text)) : ?>
            <span class="ekit-fancy-prefix-text"><?php echo wp_kses( $ekit_fancy_prefix_text, \ElementsKit_Lite\Utils::get_kses_array()); ?></span>
        <?php endif;

        if ( 'animated' === $ekit_fancy_animation_style ) {
            $this->add_render_attribute('fancy-text-lists', 'class', ($ekit_fancy_animation_type == 'type') ? 'ekit-fancy-text-lists waiting' : 'ekit-fancy-text-lists');
            if(!empty($ekit_fancy_text_lists)) : ?>
                <span <?php $this->print_render_attribute_string('fancy-text-lists'); ?>>
                    <?php foreach($ekit_fancy_text_lists as $key => $ekit_fancy_text_list): ?>
                    <b class="ekit-fancy-text elementor-repeater-item-<?php echo esc_attr($ekit_fancy_text_list['_id']) ?> <?php echo esc_attr(($key == 0) ? 'is-visible' : ''); ?>"><?php echo esc_html($ekit_fancy_text_list['ekit_fancy_text']); ?></b>
                    <?php endforeach; ?>
                </span>
            <?php endif;
        } elseif ( 'highlighted' === $ekit_fancy_animation_style ) {
            if (!empty($ekit_fancy_highlighted_text)) : ?>
                 <span class="ekit-highlighted-effect">
                    <span class="ekit-highlighted-text"> <?php echo esc_attr( $ekit_fancy_highlighted_text); ?></span>
                    <?php $this->get_svg_content($settings)?>
                 </span>
            <?php endif ; 
        }

        if(!empty($ekit_fancy_suffix_text)) : ?>
            <span class="ekit-fancy-suffix-text"><?php echo wp_kses($ekit_fancy_suffix_text, \ElementsKit_Lite\Utils::get_kses_array()) ?></span>
        <?php endif;

        return ob_get_clean();
    }

	protected function get_svg_content($settings) {
        extract($settings);

        $svg = Utils::file_get_contents(Handler::get_dir() . 'assets/shapes/' . $ekit_fancy_highlighted_type . '.svg');
        $simplexmlEl = simplexml_load_string($svg);
        $viewbox_values = ($simplexmlEl && !empty($simplexmlEl->attributes()->viewBox)) ? explode(' ', $simplexmlEl->attributes()->viewBox) : [0, 0, 500, 150];
       
        $viewbox = [
            $viewbox_values[0],
            $viewbox_values[1],
            $viewbox_values[2],
            $viewbox_values[3],
        ];

        ?>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="<?php echo esc_attr(implode(' ', $viewbox)); ?>" class="ekit-svg-<?php echo esc_attr($ekit_fancy_highlighted_type); ?>" preserveAspectRatio="none">
            <?php echo strip_tags($svg, '<path>'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- It will escape finally inside $this->render_raw() method ?>
        </svg> 
    <?php }
}