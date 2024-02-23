<?php
namespace Elementor;
use \Elementor\ElementsKit_Widget_Advanced_Slider_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;
use \ElementsKit_Lite\Modules\Controls\Widget_Area_Utils as Widget_Area_Utils;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Advanced_Slider extends Widget_Base {
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
        return 'https://wpmet.com/doc/advanced-slider-in-elementor-with-elementskit/';
    }

	protected function register_controls() {

		$this->start_controls_section(
			'ekit_slider_general_section',
			[
				'label' => esc_html__('General', 'elementskit'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'ekit_slider_title', [
				'label' => esc_html__('Title', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

        $repeater->add_control(
			'ekit_slider_thumbs_image',[
                'label' => esc_html__( 'Choose Image', 'elementskit'),
                'description' => esc_html__( 'Thumb Image Optional', 'elementskit'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
		);

		$repeater->add_control(
			'ekit_slider_tab_content', [
				'label' => esc_html__('Content', 'elementskit'),
				'type' => ElementsKit_Controls_Manager::WIDGETAREA,
				'label_block' => true,
			]
		);

		$this->add_control(
			'ekit_slider_tab_items',
			[
				'label' => esc_html__('Tab content', 'elementskit'),
				'type' => Controls_Manager::REPEATER,
				'separator' => 'before',
				'title_field' => '{{ ekit_slider_title }}',
				'default' => [
					[
						'ekit_slider_title' => esc_html__('Slide One', 'elementskit'),
					],
					[
						'ekit_slider_title' => esc_html__('Slide Two', 'elementskit'),
					],
				],
				'fields' => $repeater->get_controls(),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_slider_nav_control_section',
			[
				'label' => esc_html__('Settings', 'elementskit'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		 /**
		 * Control: Enable Ajax.
		 */
		$this->add_control(
			'ekit_ajax_template',
			[
				'label'         => esc_html__( 'Enable Ajax', 'elementskit' ),
				'type'          => Controls_Manager::HIDDEN,
				'prefix_class'  => 'ekit-template-ajax--',
				'render_type'   => 'template',
			]
		);

		$this->add_control(
			'ekit_slider_effect_style',
			[
				'label' => esc_html__( 'Slider Effect Style', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'elementskit' ),
					'fade'  => esc_html__( 'Fade', 'elementskit' ),
					'cube' => esc_html__( 'Cube', 'elementskit' ),
					'flip' => esc_html__( 'Flip', 'elementskit' ),
					'coverflow' => esc_html__( 'Coverflow', 'elementskit' ),
				],
			]
		);

        $this->add_control(
			'ekit_slider_show_direction_type',
			[
				'label' => esc_html__( 'Slider Direction Type', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal'  => esc_html__( 'Horizontal', 'elementskit' ),
					'vertical'  => esc_html__( 'Vertical', 'elementskit' ),
				],
			]
		);

        $this->add_responsive_control(
			'ekit_slider_slides_per_view',
			[
				'label' => esc_html__( 'Slides Per View', 'elementskit' ),
				'type'      => Controls_Manager::SLIDER,
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
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider' => '--ekit-swiper-slide-per-view:  {{SIZE}};',
				],
                'condition' => ['ekit_slider_show_direction_type' => 'horizontal']
			]
		);
		
        $this->add_responsive_control(
			'ekit_slider_space_between',
			[
				'label' => esc_html__( 'Space Between', 'elementskit' ),
				'type' =>  Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider' => '--ekit_slider_space_betweens: {{SIZE}}{{UNIT}};',
				],
                'condition' => ['ekit_slider_show_direction_type' => 'horizontal']
			]
		);

		$this->add_control(
			'ekit_slider_show_pagination',
			[
				'label' => esc_html__( 'Pagination Show', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

        $this->add_control(
			'ekit_slider_thumbs_image_show',
			[
				'label' => esc_html__( 'Thumbs Show', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		
		$this->add_control(
			'ekit_slider_thumbs_scale',
			[
				'label' => esc_html__( 'Thumbs Scale', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'ON', 'elementskit' ),
				'label_off' => esc_html__( 'OFF', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => ['ekit_slider_thumbs_image_show' => 'yes'],
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .slider-thumbs-yes .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'transform: scale(.9)',
				],
			]
		);

        $this->add_control(
			'ekit_slider_mouse_scroll',
			[
				'label' => esc_html__( 'Mouse Scroll', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

        $this->add_control(
			'ekit_slider_grab_cursor',
			[
				'label' => esc_html__( ' Grab Cursor', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'ekit_slider_infinite_loop',
			[
				'label' => esc_html__( 'Infinite Loop', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'ON', 'elementskit' ),
				'label_off' => esc_html__( 'OFF', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		
		$this->add_control(
			'ekit_slider_speed_time',
			[
				'label' => esc_html__( 'Speed (ms)', 'elementskit' ),
				'type'      => Controls_Manager::NUMBER,
                'min'       => 300,
                'max'       => 10000,
				'step' 		=> 1,
                'default'   => 600,
			]
		);
        
        $this->add_control(
			'ekit_slider_auto_play',
			[
				'label' => esc_html__( 'Auto Play', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

        $this->add_control(
			'ekit_slider_progress_bar',
			[
				'label' => esc_html__( 'Progress Bar', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
                'condition' => ['ekit_slider_auto_play' => 'yes']
			]
            

		);

        $this->add_control(
			'ekit_slider_auto_play_delay',
			[
				'label' => esc_html__( 'Delay (ms)', 'elementskit' ),
				'type'      => Controls_Manager::NUMBER,
                'min'       => 1000,
                'max'       => 10000,
                'default'   => 3000,
                'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .ekit-swiper-progress-bar .ekit-progress-bar' => '-webkit-animation-duration: {{VALUE}}ms; animation-duration: {{VALUE}}ms',
				],
                'condition' => ['ekit_slider_auto_play' => 'yes']
			]
		);

		$this->add_control(
			'ekit_slider_nav_show_controls',
			[
				'label' => esc_html__( 'Show Navigation', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'ekit_slider_nav_left_arrow_icon',
			[
				'label' => esc_html__( 'Left Arrow Icon', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'eicon-chevron-left',
					'library' => 'solid',
				],
				'condition' => ['ekit_slider_nav_show_controls' => 'yes']
			]
		);

		$this->add_control(
			'ekit_slider_nav_right_arrow_icon',
			[
				'label' => esc_html__( 'Right Arrow Icon', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'eicon-chevron-right',
					'library' => 'solid',
				],
				'condition' => ['ekit_slider_nav_show_controls' => 'yes']
			]
		);

		$this->end_controls_section();
        
        $this->start_controls_section(
			'ekit_slider_com_style_section',
			[
				'label' => esc_html__( 'Common', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
			'ekit_slider_wrapper_padding',
			[
				'label' => esc_html__( 'Wrapper Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%', 'rem' ],
				'allowed_dimensions' => 'vertical',
				'placeholder' => [
					'top' => '',
					'right' => 'auto',
					'bottom' => '110',
					'left' => 'auto',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-slider-wrapper' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
			'ekit_slider_nav_style_section',
			[
				'label' => esc_html__( 'Navigation', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['ekit_slider_nav_show_controls' => 'yes']
			]
		);

		$this->add_responsive_control(
			'ekit_slider_nav_icon_width',
			[
				'label' => esc_html__( 'width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%' ],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 200,
					],
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
			
				'selectors' => [
					'{{WRAPPER}} .swiper-nav-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_slider_nav_icon_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%' ],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 200,
					],
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
			
				'selectors' => [
					'{{WRAPPER}} .swiper-nav-button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'ekit_slider_nav_icon_size',
			[
				'label' => esc_html__( 'Icon Size (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
                'default' => [
					'unit' => 'px',
					'size' => 20,
				],

				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-nav-button i' => ' font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'ekit_slider_nav_tabs'
		);

		$this->start_controls_tab(
			'ekit_slider_nav_tab_normal',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_slider_nav_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default'	=> '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-nav-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_slider_nav_icon_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default'	=> '#101010',
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-nav-button' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_slider_nav_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-advanced-slider .swiper-nav-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_slider_nav_tab_hover',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_slider_nav_icon_color_hover',
			[
				'label' => esc_html__( 'Icon Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-nav-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_slider_nav_icon_bg_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-nav-button:hover' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_slider_nav_border_hover',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-advanced-slider .swiper-nav-button:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'ekit_slider_nav_border_radius',
			[
				'label' => esc_html__( 'Nav Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-nav-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_control(
			'ekit_slider_arrow_align_left_rigt',
			[
				'label' => esc_html__( 'Arrow Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left: 50px' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'right: 14px;' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
				],
                'separator' => 'before',
				'default' => 'left: 50px',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-container-vertical .ekit-swiper-arrow-button' => '{{VALUE}}',
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-vertical .ekit-swiper-arrow-button' => '{{VALUE}}',
				],
                'condition' => ['ekit_slider_show_direction_type' => 'vertical']
			]
		);

		$this->add_control(
			'ekit_slider_arrow_space_between',
			[
				'label' => esc_html__( 'Arrow Space Between', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
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
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-container-vertical .ekit-swiper-arrow-button' => '--space-between-top:{{SIZE}}{{UNIT}}; --space-between-bottom : -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-vertical .ekit-swiper-arrow-button' => '--space-between-top:{{SIZE}}{{UNIT}}; --space-between-bottom : -{{SIZE}}{{UNIT}}',
				],
                'condition' => ['ekit_slider_show_direction_type' => 'vertical']
			]
		);

        $this->add_responsive_control(
            "ekit_slider_arrow_vertical",
            [
                'label' => esc_html__( 'Arrow Vertical Align (%)', 'elementskit' ),
                'size_units' => ['%'],
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
					'unit' => '%',
					'size' => 50,
				],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-advanced-slider .swiper-container-vertical .ekit-swiper-arrow-button' => 'top: {{SIZE}}%;',
                    '{{WRAPPER}} .elementskit-advanced-slider .swiper-container-horizontal .ekit-swiper-arrow-button .swiper-button-next' => 'top: {{SIZE}}%;',
                    '{{WRAPPER}} .elementskit-advanced-slider .swiper-container-horizontal .ekit-swiper-arrow-button .swiper-button-prev' => 'top: {{SIZE}}%;',
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-vertical .ekit-swiper-arrow-button' => 'top: {{SIZE}}%;',
                    '{{WRAPPER}} .elementskit-advanced-slider .swiper-horizontal .ekit-swiper-arrow-button .swiper-button-next' => 'top: {{SIZE}}%;',
                    '{{WRAPPER}} .elementskit-advanced-slider .swiper-horizontal .ekit-swiper-arrow-button .swiper-button-prev' => 'top: {{SIZE}}%;',
                ],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_slider_pagination_section',
			[
				'label' => esc_html__( 'Pagination / Thumbs', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'ekit_slider_show_pagination',
                            'operator' => '===',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'ekit_slider_thumbs_image_show',
                            'operator' => '===',
                            'value' => 'yes',
                        ],
                    ],
                ],
			]
		);

        $this->add_control(
			'ekit_slider_bullet_point_color',
			[
				'label' => esc_html__( 'Dot Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-pagination .swiper-pagination-bullet::before' => ' background-color: {{VALUE}}',
				],
                'default' => '#282828',
                'condition' => ['ekit_slider_thumbs_image_show!' => 'yes']
			]
		);

		$this->add_control(
			'ekit_slider_pagination_size',
			[
				'label' => esc_html__( 'Dot Size (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
                'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-pagination .swiper-pagination-bullet::before' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
                'condition' => ['ekit_slider_thumbs_image_show!' => 'yes']
			]
		);

		$this->add_control(
			'ekit_slider_pagination_bg_color',
			[
				'label' => esc_html__( 'Dot Border Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-pagination .swiper-pagination-bullet' => ' border-color: {{VALUE}}',
				],
                'condition' => ['ekit_slider_thumbs_image_show!' => 'yes']
			]
		);
        $this->add_control(
			'ekit_slider_pagination_border',
			[
				'label' => esc_html__( 'Dot Border (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
                'separator' => 'after',
                'condition' => ['ekit_slider_thumbs_image_show!' => 'yes'],
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_slider_pagination_align_inline',
			[
				'label' => esc_html__( 'Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left:0' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'left: 50%;transform: translateX(-50%);' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon' => 'eicon-text-align-center',
					],
					'right:0;left:inherit;' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left: 50%;transform: translateX(-50%);',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-container-horizontal .swiper-pagination' => '{{VALUE}}',
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-horizontal .swiper-pagination' => '{{VALUE}}',
				],
                'condition' => ['ekit_slider_show_direction_type' => 'horizontal']    
			]
		);

        $this->add_control(
			'ekit_slider_pagination_align_vertical',
			[
				'label' => esc_html__( 'Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left: 14px; align-content: start;' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'right: 14px; align-content: end;' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left: 14px; align-content: start;',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-container-vertical .swiper-pagination' => '{{VALUE}}',
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-vertical .swiper-pagination' => '{{VALUE}}',
				],
                'condition' => ['ekit_slider_show_direction_type' => 'vertical']
			]
		);

        $this->add_responsive_control(
            "ekit_slider_pagination_vertical",
            [
                'label' => esc_html__( 'Vertical Align', 'elementskit' ),
                'size_units' => ['%'],
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
					'unit' => '%',
					'size' => 50,
				],
                'selectors' => [
                    "{{WRAPPER}} .elementskit-advanced-slider .swiper-container-vertical > .swiper-pagination-bullets" => 'top: {{SIZE}}%;',
					"{{WRAPPER}} .elementskit-advanced-slider .swiper-vertical > .swiper-pagination-bullets" => 'top: {{SIZE}}%;',
                ],
                'condition' => ['ekit_slider_show_direction_type' => 'vertical']
            ]
        );

		$this->add_responsive_control(
			'ekit_slider_bottom_to_top',
			[
				'label' => esc_html__( 'Vertical Align (%)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-container-horizontal > .swiper-pagination' => 'top: {{SIZE}}%;',
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-horizontal > .swiper-pagination' => 'top: {{SIZE}}%;',
				],
                'condition' => ['ekit_slider_show_direction_type' => 'horizontal']
			]
		);

        $this->start_controls_tabs(
			'ekit_slider_thumbs_style_tabs',
            [
                'condition' => ['ekit_slider_thumbs_image_show' => 'yes'],
            ]
		);

		$this->start_controls_tab(
			'ekit_slider_thumbs_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);
        $this->add_responsive_control(
			'ekit_slider_thumbs_normal_opacity',
			[
				'label' => esc_html__( 'Opacity', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
                'default' => [
					'size' => .5,
				],
				'range' => [
					'px' => [
						'max' => 1,
						'step' => 0.01,
					],
				],
                
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .slider-thumbs-yes .swiper-pagination .swiper-pagination-bullet' => 'opacity: {{SIZE}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_slider_thumbs_image_border',
                'label' => esc_html__( 'Thumbs Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-advanced-slider .slider-thumbs-yes .swiper-pagination .swiper-pagination-bullet',
            ]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_slider_thumbs_active_tab',
			[
				'label' => esc_html__( 'Active', 'elementskit' ),
			]
		);

        $this->add_control(
			'ekit_slider_thumbs_active_opacity',
			[
				'label' => esc_html__( 'Opacity', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
                'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'max' => 1,
						'step' => 0.01,
					],
				],
                
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .slider-thumbs-yes .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'opacity: {{SIZE}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_slider_thumbs_active_border',
                'label' => esc_html__( 'Thumbs Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-advanced-slider .slider-thumbs-yes .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active',
            ]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
        
		$this->add_control(
			'ekit_slider_pagination_border_radius',
			[
				'label' => esc_html__( 'Border Radius (px)', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
                'default' => [
					'unit' => 'px',
					'size' => 0,
				],
                'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-pagination .swiper-pagination-bullet::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_slider_pagination_margin',
			[
				'label' => esc_html__( 'Pagination Gap (px)', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-pagination-bullet' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => ['ekit_slider_thumbs_image_show!' => 'yes']
			]
		);

        $this->add_control(
			'ekit_slider_thumbs_image_divider',
			[
				'type' => Controls_Manager::DIVIDER,
                'condition' => ['ekit_slider_thumbs_image_show' => 'yes']
			]
		);
        $this->add_responsive_control(
			'ekit_slider_thumbs_vertical_width',
			[
				'label' => esc_html__( 'Thumbs Width (%)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
                'default' => [
					'unit' => '%',
					'size' => 15,
				],
                'condition' => [
                    'ekit_slider_thumbs_image_show' => 'yes',
                    'ekit_slider_show_direction_type' => 'vertical'
                ],
                'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-container-vertical > .swiper-pagination-bullets' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-vertical > .swiper-pagination-bullets' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_slider_thumbs_height',
			[
				'label' => esc_html__( 'Thumbs Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
                'default' => [
					'unit' => 'px',
					'size' => 110,
				],
                'condition' => ['ekit_slider_thumbs_image_show' => 'yes'],
                'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .slider-thumbs-yes .swiper-pagination .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_slider_thumbs_image_grid_gap',
			[
				'label' => esc_html__( 'Thumbs Gap (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
                'default' => [
					'unit' => 'px',
					'size' => 0,
				],
                'condition' => ['ekit_slider_thumbs_image_show' => 'yes'],
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .swiper-pagination' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_slider_thumbs_image_offset',
			[
				'label' => esc_html__( 'Offset Gap (%)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
                'default' => [
					'unit' => '%',
					'size' => 70,
				],
                'condition' => [
                    'ekit_slider_thumbs_image_show' => 'yes',
                    'ekit_slider_show_direction_type!' => 'vertical'
                ]
			]
		);
		$this->end_controls_section();

        $this->start_controls_section(
			'ekit_slider_progress_bar_section',
			[
				'label' => esc_html__( 'Progress Bar', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
                'condition' => ['ekit_slider_progress_bar' => 'yes']
			]
		);

        $this->add_control(
			'ekit_slider_progress_bar_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffc000',
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .ekit-swiper-progress-bar .ekit-progress-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_slider_progress_bar_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
                'default' => '#272727',
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .ekit-swiper-progress-bar' => 'background-color: {{VALUE}};',
				],
                'separator' => 'after',
			]
		);

        $this->add_responsive_control(
			'ekit_slider_progress_bar_width',
			[
				'label' => esc_html__('Width', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
                    'size' => 100
				],
				'tablet_default' => [
					'unit' => '%',
                    'size' => 100
				],
				'mobile_default' => [
					'unit' => '%',
                    'size' => 100
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
					'{{WRAPPER}} .elementskit-advanced-slider .ekit-swiper-progress-bar' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_slider_progress_bar_height',
			[
				'label' => esc_html__('Height (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
                    'size' => 8
				],
				'tablet_default' => [
					'unit' => 'px',
                    'size' => 8
				],
				'mobile_default' => [
					'unit' => 'px',
                    'size' => 8
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
                
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .ekit-swiper-progress-bar' => 'height: {{SIZE}}{{UNIT}}; margin: -{{SIZE}}{{UNIT}} auto 0 auto;',
					'{{WRAPPER}} .elementskit-advanced-slider .ekit-swiper-progress-bar .ekit-progress-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_slider_progress_bar_bottom_to_top',
			[
				'label' => esc_html__( 'Vertical Align (%)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => '100',
                ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-advanced-slider .ekit-swiper-progress-bar' => 'top: {{SIZE}}%;',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render( ) {
		echo '<div class="ekit-wid-con" >';
			$this->render_raw();
		echo '</div>';
	}

	protected function render_raw( ) {
		$settings = $this->get_settings_for_display();
		extract($settings);

        $advanced_slider_options = [
            'type' => 'advanced-slider',
            'id' => 'ekit-advanced-slider' . $this->get_id(),
            'sliderOptions' => [     
                'slidesPerViewItem' => $ekit_slider_slides_per_view['size'] ?? 1,
                'spaceBetweenGap' => $ekit_slider_space_between['size'] ?? 0,
                'sliderGrapCursor' => $ekit_slider_grab_cursor,
                'sliderDirectionType' => $ekit_slider_show_direction_type,
                'sliderMouseScroll' => $ekit_slider_mouse_scroll,
                'sliderAutoPlay' => $ekit_slider_auto_play,
                'sliderEffect' => $ekit_slider_effect_style,
                'sliderThumbsShow' => $ekit_slider_thumbs_image_show,
                'sliderThumbsOffset' => $ekit_slider_thumbs_image_offset,
                'sliderTabItems' => $ekit_slider_tab_items,
                'progressBar' => $ekit_slider_progress_bar,
                'speedTime' => $ekit_slider_speed_time,
                'loopEnable' => $ekit_slider_infinite_loop,
                'autoPlayDelay' => $ekit_slider_auto_play_delay,
				'breakpointsOption' => [
					360 => [
						'slidesPerView' => $ekit_slider_slides_per_view_mobile['size'] ?? 1,
						'spaceBetween'  => $ekit_slider_space_between_mobile ['size']?? 0
					],
					767 => [
						'slidesPerView' => $ekit_slider_slides_per_view_tablet['size'] ?? 1,
						'spaceBetween'  => $ekit_slider_space_between_tablet['size'] ?? 0
					],
					1024 => [
						'slidesPerView' => $ekit_slider_slides_per_view['size'] ?? 1,
						'spaceBetween'  => $ekit_slider_space_between['size'] ?? 0
					]
				],
            ],
        ];
        $this->add_render_attribute( 'advanced-slider-data', [
            'class' => 'elementskit-advanced-slider',
            'data-widget_settings' => wp_json_encode( $advanced_slider_options ),
        ]);
		?>

		<div <?php $this->print_render_attribute_string( 'advanced-slider-data' ); ?>>
			<div class="<?php echo method_exists('\ElementsKit_Lite\Utils', 'swiper_class') ? esc_attr(\ElementsKit_Lite\Utils::swiper_class()) : 'swiper'; ?> ekit-slider-wrapper">
				<div class="swiper-wrapper ekit-swiper-wrapper">
					<?php foreach ($ekit_slider_tab_items as $i => $tab) : ?>
						<div class="swiper-slide ekit-swiper-slide elementor-repeater-item-<?php echo esc_attr( $tab[ '_id' ] ); ?>">
							<?php echo Widget_Area_Utils::parse( $tab['ekit_slider_tab_content'], $this->get_id(), $tab[ '_id' ], $ekit_ajax_template, ($i + 1) ); ?>    
						</div>
					<?php endforeach; ?>
				</div>

				<?php if($ekit_slider_nav_show_controls === 'yes') : ?>
					<!-- next / prev arrows -->
				<div class="ekit-swiper-arrow-button">
                    <div class="swiper-nav-button swiper-button-next"> 
						<?php Icons_Manager::render_icon( $ekit_slider_nav_right_arrow_icon, [ 'aria-hidden' => 'true' ] ); ?>
					</div>
					<div class="swiper-nav-button swiper-button-prev">
						<?php Icons_Manager::render_icon( $ekit_slider_nav_left_arrow_icon, [ 'aria-hidden' => 'true' ] ); ?>
				    </div>
                </div>
					<!-- !next / prev arrows -->
				<?php endif; ?>

				<?php if($ekit_slider_show_pagination === 'yes' || $ekit_slider_thumbs_image_show === 'yes') : ?>
					<div class="swiper-pagination ekit-swiper-pagination""></div>
				<?php endif; ?>

                <?php if($ekit_slider_progress_bar === 'yes'):?>
                    <div class="ekit-swiper-progress-bar">
                        <div class="ekit-progress-bar"></div>
                    </div>
                <?php endif ;?>
			</div>

		</div>

		<?php
	}

	protected function content_template() { }
}