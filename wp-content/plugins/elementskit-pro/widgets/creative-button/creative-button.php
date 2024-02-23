<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Creative_Button_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Creative_Button extends Widget_Base {
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
        return 'https://wpmet.com/doc/creative-button/';
    }

    protected function register_controls() {


		$this->start_controls_section(
			'ekit_btn_section_content',
			array(
				'label' => esc_html__( 'Content', 'elementskit' ),
			)
		);

		$this->add_control(
			'ekit_btn_text',
			[
				'label' =>esc_html__( 'Label', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'default' =>esc_html__( 'Learn more ', 'elementskit' ),
				'placeholder' =>esc_html__( 'Learn more ', 'elementskit' ),
				'dynamic' => [
                    'active' => true,
                ],
			]
		);


		$this->add_control(
			'ekit_btn_url',
			[
				'label' =>esc_html__( 'URL', 'elementskit' ),
				'type' => Controls_Manager::URL,
				'placeholder' =>esc_url('https://wpmet.com'),
				'dynamic' => [
                    'active' => true,
                ],
				'default' => [
					'url' => '#',
				],
			]
		);

        $this->add_control(
            'ekit_btn_section_settings',
            [
                'label' => esc_html__( 'Settings', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);
		
		$this->add_control(
            'ekit_btn_icons__switch',
            [
                'label' => esc_html__('Add icon? ', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' =>esc_html__( 'Yes', 'elementskit' ),
                'label_off' =>esc_html__( 'No', 'elementskit' ),
            ]
		);

		$this->add_control(
			'ekit_btn_icons',
			[
				'label' =>esc_html__( 'Icon', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_btn_icon',
				'label_block' => true,
				'condition'	=> [
					'ekit_btn_icons__switch'	=> 'yes'
				]
			]
		);
        $this->add_control(
            'ekit_btn_icon_align',
            [
                'label' =>esc_html__( 'Icon Position', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' =>esc_html__( 'Before', 'elementskit' ),
                    'right' =>esc_html__( 'After', 'elementskit' ),
                ],
                'condition'	=> [
					'ekit_btn_icons__switch'	=> 'yes'
				]
            ]
        );
		$this->add_responsive_control(
			'ekit_btn_align',
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ekit-btn-wraper' => 'text-align: {{VALUE}};',
				],
			]
		);
	    $this->add_control(
		    'ekit_btn_class',
		    [
			    'label' => esc_html__( 'Class', 'elementskit' ),
			    'type' => Controls_Manager::TEXT,
			    'placeholder' => esc_html__( 'Class Name', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
		    ]
	    );

	    $this->add_control(
		    'ekit_btn_id',
		    [
			    'label' => esc_html__( 'id', 'elementskit' ),
			    'type' => Controls_Manager::TEXT,
			    'placeholder' => esc_html__( 'ID', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
		    ]
		);

		$this->add_control(
			'ekit_creative_hover_btn_style',
			[
				'label' => esc_html__( 'Hover Style', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
				'default' => 'fade_only',
				'options' => [
					'fade_only'  => esc_html__( 'No Effect', 'elementskit' ),
					'left_to_right_with_hypen'  => esc_html__( 'Left to right with hypen', 'elementskit' ),
					'left_to_right' => esc_html__( 'Left to right', 'elementskit' ),
					'left_to_right_with_out_hypen' => esc_html__( 'Left to right with icon', 'elementskit' ),
					'border_left_to_right' => esc_html__( 'Border left to right', 'elementskit' ),
					'center_ripple_effect' => esc_html__( 'Center ripple effect', 'elementskit' ),
					'fade_with_icon' => esc_html__( 'Fade with icon', 'elementskit' ),
					'fade_with_icon_skew' => esc_html__( 'Fade with icon skew', 'elementskit' ),
					'fade_with_icon_on_hover' => esc_html__( 'Fade with icon on hover', 'elementskit' ),
					'border_ripple' => esc_html__( 'Border ripple', 'elementskit' ),
					'two_side_border' => esc_html__( 'Two side border', 'elementskit' ),
					'ripple_position_aware' => esc_html__( 'Ripple position aware', 'elementskit' ),
					'two_dot_ripple' => esc_html__( 'Two dot ripple', 'elementskit' ),
					'doors_open' => esc_html__( 'Doors open', 'elementskit' ),
					'skew_open' => esc_html__( 'Skew open', 'elementskit' ),
					'vertical_text_cut' => esc_html__( 'Vertical text cut', 'elementskit' ),
					'zingle' => esc_html__( 'Zingle', 'elementskit' ),
					'two_shade' => esc_html__( 'Two shade', 'elementskit' ),
					'outer_dot_click_splash_effect' => esc_html__( 'Outer dot click splash effect', 'elementskit' ),
					'water_ripple_click_effect' => esc_html__( 'Water ripple click effect', 'elementskit' ),
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_ripple_border_color',
			[
				'label' => esc_html__( 'Ripple Border Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_outline_style_one::before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_outline_style_one::after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_outline_style_one .ekit_outline_btn_lines::before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_outline_style_one .ekit_outline_btn_lines::after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_outline_style_two::before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_outline_style_two::after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_outline_style_two .ekit_outline_btn_lines::before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_outline_style_two .ekit_outline_btn_lines::after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => ['border_ripple', 'two_side_border']
				]
			]
		);

		$this->add_responsive_control(
			'ekit_btn_hypen_left_position',
			[
				'label' => esc_html__( 'Left', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_slide_in_line::after' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => 'left_to_right_with_hypen'
				]
			]
		);


		$this->end_controls_section();


        $this->start_controls_section(
			'ekit_btn_section_style',
			[
				'label' =>esc_html__( 'Button', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_btn_text_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit_creative_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_btn_typography',
				'label' =>esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit_creative_button',
			]
		);

        $this->add_group_control(
        	Group_Control_Text_Shadow::get_type(),
        	[
        		'name' => 'ekit_btn_shadow',
        		'selector' => '{{WRAPPER}} .ekit_creative_button',
        	]
        );

		$this->start_controls_tabs( 'ekit_btn_tabs_style' );

		$this->start_controls_tab(
			'ekit_btn_tabnormal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_btn_text_color',
			[
				'label' =>esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_creative_button:not(.ekit_tamaya) > .ekit_creative_button_text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit_creative_button.ekit_tamaya::before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit_creative_button.ekit_tamaya::after' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit_creative_button > i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit_slide_in_line::after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ekit_creative_button svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_bg_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_creative_button:not(.ekit_tamaya)' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_creative_button.ekit_tamaya::before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_creative_button.ekit_tamaya::after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_distorted_btn .ekit_button__bg' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_btn_tab_button_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_btn_hover_color',
			[
				'label' =>esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_creative_button:hover > .ekit_creative_button_text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit_creative_button:hover > i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit_creative_button:hover svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_btn_bg_hover_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_creative_button:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style!' => ['two_dot_ripple', 'left_to_right_with_hypen', 'border_left_to_right', 'center_ripple_effect', 'ripple_position_aware', 'doors_open', 'skew_open', 'two_shade', 'outer_dot_click_splash_effect']
				]
			]
		);

		$this->add_responsive_control(
			'ekit_btn_bg_hover_slide_bg_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_creative_button.ekit_slide_bg:before' => 'background-color: {{VALUE}}',
				],
				// 'condition' => [
				// 	'ekit_creative_hover_btn_style' => 'left_to_right_with_hypen'
				// ]
			]
		);

		$this->add_responsive_control(
			'ekit_btn_bg_hover_slide_border_left_to_right_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_creative_button.ekit_slide_left_border:before' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => 'border_left_to_right'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_btn_bg_hover_ripple_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_creative_button.ekit_btn_splash:before' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => 'center_ripple_effect'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_btn_bg_hover_position_aware_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_position_aware_bg' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => 'ripple_position_aware'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_btn_bg_hover_two_dot_ripple_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_hover_on_collision::before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_hover_on_collision::after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => 'two_dot_ripple'
				]
			]
		);
		$this->add_responsive_control(
			'ekit_btn_bg_hover_doors_open_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_ujarak::before' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => 'doors_open'
				]
			]
		);
		$this->add_responsive_control(
			'ekit_btn_bg_hover_skew_open_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_wayra::before' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => 'skew_open'
				]
			]
		);
		$this->add_responsive_control(
			'ekit_btn_bg_hover_two_shade_color_one',
			[
				'label' =>esc_html__( 'Background Color One', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_aylen:before' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => 'two_shade'
				]
			]
		);
		$this->add_responsive_control(
			'ekit_btn_bg_hover_two_shade_color_two',
			[
				'label' =>esc_html__( 'Background Color Two', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_aylen:after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => 'two_shade'
				]
			]
		);
		$this->add_responsive_control(
			'ekit_btn_bg_hover_splash_bg_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_distorted_btn:hover:before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_distorted_btn:hover .ekit_goo_left' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_distorted_btn:hover .ekit_goo_right' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_distorted_btn:hover .ekit_button__bg' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => 'outer_dot_click_splash_effect'
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();




        $this->start_controls_section(
			'ekit_btn_border_style_tabs',
			[
				'label' =>esc_html__( 'Border', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_btn_bg_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit_creative_button',
			]
		);

		$this->add_responsive_control(
			'ekit_btn_bg_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit_creative_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
			'ekit_btn_box_shadow_style',
			[
				'label' =>esc_html__( 'Shadow', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
			  'name' => 'ekit_btn_box_shadow_group',
			  'selector' => '{{WRAPPER}} .ekit_creative_button',
			]
		);


		$this->end_controls_section();

        $this->start_controls_section(
			'ekit_btn_iconw_style',
			[
				'label' =>esc_html__( 'Icon', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'ekit_btn_icons__switch'	=> 'yes'
				]
			]
		);
		$this->add_responsive_control(
			'ekit_btn_normal_icon_font_size',
			array(
				'label'      => esc_html__( 'Font Size', 'elementskit' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'rem',
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .ekit_creative_button > i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit_creative_button > svg'	=> 'max-width: {{SIZE}}{{UNIT}};'
				),
			)
		);

		$this->add_responsive_control(
			'ekit_btn_normal_icon_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit_creative_button > i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_normal_icon_bg_color',
			[
				'label' => esc_html__( 'Iocn Bg Normal', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_group_btn:not(.ekit_skew_bg) > i' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_group_btn.ekit_skew_bg > i::after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => ['fade_with_icon', 'fade_with_icon_skew'],
				]
			]
		);
		$this->add_responsive_control(
			'ekit_btn_normal_icon_bg_hover_color',
			[
				'label' => esc_html__( 'Iocn Bg Hover', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_group_btn:not(.ekit_skew_bg):hover > i' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_group_btn.ekit_skew_bg:hover > i::after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_creative_hover_btn_style' => ['fade_with_icon', 'fade_with_icon_skew'],
				]
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

		$btn_text = $settings['ekit_btn_text'];
        $btn_class = ($settings['ekit_btn_class'] != '') ? $settings['ekit_btn_class'] : '';
        $btn_id = ($settings['ekit_btn_id'] != '') ? 'id='.$settings['ekit_btn_id'] : '';
		$icon_align = $settings['ekit_btn_icon_align'];
		
		if ( ! empty( $settings['ekit_btn_url']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['ekit_btn_url'] );
		}

		$data_text = "";
		if ($settings['ekit_creative_hover_btn_style'] == 'vertical_text_cut') {
			$data_text = $btn_text;
		}

		$position_aware_bg = "";
		if ($settings['ekit_creative_hover_btn_style'] == 'ripple_position_aware') {
			$position_aware_bg = "<span class='ekit_position_aware_bg'></span>";
		}
		$outline_extra_border = "";
		if ($settings['ekit_creative_hover_btn_style'] == 'two_side_border' || $settings['ekit_creative_hover_btn_style'] == 'border_ripple') {
			$outline_extra_border = "<span class='ekit_outline_btn_lines'></span>";
		}

		$creative_btn_class = "";
		switch ($settings['ekit_creative_hover_btn_style']) {
			case 'fade_only':
				$creative_btn_class = "";
				break;
			case 'left_to_right_with_hypen':
				$creative_btn_class = "ekit_slide_bg ekit_slide_in_line";
				break;
			case 'left_to_right':
				$creative_btn_class = "ekit_slide_bg ekit_slide_text_left ekit_slide_icon";
				break;
			case 'left_to_right_with_out_hypen':
				$creative_btn_class = "ekit_slide_bg ekit_slide_text_left ekit_slide_icon ekit_icon_fade_in";
				break;
			case 'border_left_to_right':
				$creative_btn_class = "ekit_slide_left_border";
				break;
			case 'center_ripple_effect':
				$creative_btn_class = "ekit_slide_in_line ekit_slide_text_right ekit_btn_splash";
				break;
			case 'fade_with_icon':
				$creative_btn_class = "ekit_group_btn";
				break;
			case 'fade_with_icon_skew':
				$creative_btn_class = "ekit_group_btn ekit_skew_bg";
				break;
			case 'fade_with_icon_on_hover':
				$creative_btn_class = "ekit_slide_icon_2";
				break;
			case 'border_ripple':
				$creative_btn_class = "ekit_outline_style_one";
				break;
			case 'two_side_border':
				$creative_btn_class = "ekit_outline_style_two";
				break;
			case 'ripple_position_aware':
				$creative_btn_class = "ekit_position_aware";
				break;
			case 'two_dot_ripple':
				$creative_btn_class = "ekit_hover_on_collision";
				break;
			case 'doors_open':
				$creative_btn_class = "ekit_ujarak";
				break;
			case 'skew_open':
				$creative_btn_class = "ekit_wayra";
				break;
			case 'vertical_text_cut':
				$creative_btn_class = "ekit_tamaya";
				break;
			case 'zingle':
				$creative_btn_class = "ekit_moema";
				break;
			case 'two_shade':
				$creative_btn_class = "ekit_aylen";
				break;
			case 'outer_dot_click_splash_effect':
				$creative_btn_class = "ekit_distorted_btn";
				break;
			case 'water_ripple_click_effect':
				$creative_btn_class = "ekit_ripple_effect";
				break;
			default:
				$creative_btn_class = "";
				break;
		}

		?>
		<div class="ekit-btn-wraper">
			<?php switch ($settings['ekit_creative_hover_btn_style']) {
				case 'left_to_right_with_hypen':
				case 'left_to_right':
				case 'left_to_right_with_out_hypen':
				case 'border_left_to_right':
				case 'center_ripple_effect':
				case 'fade_with_icon':
				case 'fade_with_icon_skew':
				case 'fade_with_icon_on_hover':
				case 'border_ripple':
				case 'two_side_border':
				case 'ripple_position_aware':
				case 'two_dot_ripple':
				case 'doors_open':
				case 'skew_open':
				case 'vertical_text_cut':
				case 'zingle':
				case 'two_shade':
				case 'fade_only':
				?>
					<a <?php echo $this->get_render_attribute_string( 'button' ); ?> data-text="<?php echo esc_html( $data_text ); ?>" class="ekit_creative_button <?php echo esc_attr($creative_btn_class .' '. $btn_class);?>" <?php echo esc_attr($btn_id); ?>>

						<?php if($icon_align == 'left'): ?>
							<?php
								// new icon
								$migrated = isset( $settings['__fa4_migrated']['ekit_btn_icons'] );
								// Check if its a new widget without previously selected icon using the old Icon control
								$is_new = empty( $settings['ekit_btn_icon'] );
								if ( $is_new || $migrated ) {
									// new icon
									Icons_Manager::render_icon( $settings['ekit_btn_icons'], [ 'aria-hidden' => 'true', 'class'    => 'ekit_creative_button_icon_before' ] );
								} else {
									?>
									<i class="<?php echo esc_attr($settings['ekit_btn_icon']); ?> ekit_creative_button_icon_before" aria-hidden="true"></i>
									<?php
								}
							?>
						<?php endif; ?>

						<span class="ekit_creative_button_text"><?php echo esc_html( $btn_text ); ?></span>

						<?php if($icon_align == 'right'): ?>
							<?php
								// new icon
								$migrated = isset( $settings['__fa4_migrated']['ekit_btn_icons'] );
								// Check if its a new widget without previously selected icon using the old Icon control
								$is_new = empty( $settings['ekit_btn_icon'] );
								if ( $is_new || $migrated ) {
									// new icon
									Icons_Manager::render_icon( $settings['ekit_btn_icons'], [ 'aria-hidden' => 'true', 'class'    => 'ekit_creative_button_icon_after' ] );
								} else {
									?>
									<i class="<?php echo esc_attr($settings['ekit_btn_icon']); ?> ekit_creative_button_icon_after" aria-hidden="true"></i>
									<?php
								}
							?>

						<?php endif; ?>

						<?php echo \ElementsKit_Lite\Utils::kses($position_aware_bg . $outline_extra_border); ?>
					</a>
				<?php break;
				case 'outer_dot_click_splash_effect':
					?>
					<svg xmlns="https://www.w3.org/2000/svg" version="1.1" class="ekit_svg-filters">
                        <defs>
                            <filter class="ekit-filter-goo" id="ekit-filter-goo-<?php echo esc_attr($this->get_id());?>" data-id="ekit-filter-goo-<?php echo esc_attr($this->get_id());?>">
                                <feGaussianBlur in="SourceGraphic" stdDeviation="7" result="blur" />
                                <feColorMatrix in="blur" data-mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo" />
                                <feComposite in="SourceGraphic" in2="goo" />
                            </filter>
                        </defs>
                    </svg>
                    <a <?php echo $this->get_render_attribute_string( 'button' ); ?> class="ekit_creative_button <?php echo esc_attr($creative_btn_class .' '. $btn_class);?>" <?php echo esc_attr($btn_id); ?> style="filter: url('#ekit-filter-goo-<?php echo esc_attr($this->get_id());?>')">
                        <span class="ekit_creative_button_text"><?php echo esc_html( $btn_text ); ?></span>
                        <span class="ekit_button__bg"></span>
					</a>
					<?php break;
				case 'water_ripple_click_effect': ?>
					<svg xmlns="https://www.w3.org/2000/svg" version="1.1" class="ekit_svg-filters">
                        <defs>
                            <filter class="ekit-filter-ripple" id="ekit-filter-ripple-<?php echo esc_attr($this->get_id());?>" data-id="ekit-filter-ripple-<?php echo esc_attr($this->get_id());?>">
                                <feImage xlink:href="<?php echo \ElementsKit::widget_url() . 'init/assets/img/ripple.png'; ?>" x="30" y="20" width="0" height="0" result="ripple"></feImage>
                                <feDisplacementMap xChannelSelector="R" yChannelSelector="G" color-interpolation-filters="sRGB" in="SourceGraphic" in2="ripple" scale="20" result="dm" />
                                <feComposite operator="in" in2="ripple"></feComposite>
                                <feComposite in2="SourceGraphic"></feComposite>
                            </filter>
                        </defs>
                    </svg>
                    <a <?php echo $this->get_render_attribute_string( 'button' ); ?> class="ekit_creative_button <?php echo esc_attr($creative_btn_class .' '. $btn_class);?>" <?php echo esc_attr($btn_id); ?> style="filter: url('#ekit-filter-ripple-<?php echo esc_attr ($this->get_id());?>')">
						<span class="ekit_creative_button_text"><?php echo esc_html( $btn_text ); ?></span>
					</a>
				<?php break;
				default : ?>
					<a <?php echo $this->get_render_attribute_string( 'button' ); ?> class="ekit_creative_button <?php echo esc_attr($btn_class); ?>" <?php echo esc_attr($btn_id); ?>>
						<span class="ekit_creative_button_text"><?php echo esc_html( $btn_text ); ?></span>
						<?php
							$bg_border = \ElementsKit_Lite\Utils::kses($position_aware_bg . $outline_extra_border);
							echo \ElementsKit_Lite\Utils::render( $bg_border );
						?>
					</a>
				<?php break;
			}?>
		</div>
        <?php
    }
}
