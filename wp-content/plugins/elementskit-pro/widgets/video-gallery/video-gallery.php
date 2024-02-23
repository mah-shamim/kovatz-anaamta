<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Video_Gallery_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Video_Gallery extends Widget_Base {
	use \ElementsKit_Lite\Widgets\Widget_Notice;

	public $base;

    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
        $this->add_script_depends('magnific-popup');
        $this->add_script_depends('isotope');
	}

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
        return 'https://wpmet.com/doc/video-gallery-widget/';
    }

	protected function register_controls() {

		$this->start_controls_section(
            'section_content', [
                'label' =>esc_html__( 'Content', 'elementskit' ),
            ]
		);
		
		$repeater = new Repeater();

		$repeater->add_control(
            'video_type',
            [
                'label' =>esc_html__( 'Video Type', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'youtube',
                'options' => [
                    'youtube' =>esc_html__( 'Youtube', 'elementskit' ),
                    'vimeo' =>esc_html__( 'Vimeo', 'elementskit' )
                ],
            ]
		);
		
		$repeater->add_control(
			'video_url',
			[
				'label' =>esc_html__( 'URL', 'elementskit' ),
				'type' => Controls_Manager::URL,
				'placeholder' =>esc_url('https://wpmet.com'),
				'dynamic' => [
					'active' => true,
				],
			]
        );
        
        $repeater->add_control(
			'video_category',
			[
				'label' =>esc_html__( 'Category', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
        );
        
        $repeater->add_control(
            'video_image',
            [
                'label' => esc_html__( 'Custom Image', 'elementskit' ),
                'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'video_content',
            [
                'label' => esc_html__('Content', 'elementskit'),
                'type' => Controls_Manager::REPEATER,
                'separator' => 'before',
                'fields' => $repeater->get_controls(),
            ]
		);

		$this->end_controls_section();

		/**
         * Content Tab: Filter
         */
        $this->start_controls_section(
            'ekit_gallery_section_layout',
            [
                'label' => esc_html__( 'Layout', 'elementskit' ),
            ]
        );


        $this->add_control(
            'grid_style',
            [
                'label' => esc_html__( 'Style', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__( 'Grid', 'elementskit' ),
                    'masonry' => esc_html__( 'Masonry', 'elementskit' ),
                    'carousel' => esc_html__( 'Carousel', 'elementskit' ),
                ],
            ]
        );
        $ekit_gallery_columns = range( 1, 6 );
        $ekit_gallery_columns = array_combine( $ekit_gallery_columns, $ekit_gallery_columns );
        $this->add_responsive_control(
            'ekit_column',
            [
                'label' => esc_html__( 'Columns', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => $ekit_gallery_columns,
                'frontend_available' => true,
                'condition' => [
                    'grid_style!' => 'carousel'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_settings', [
                'label' =>esc_html__( 'Settings', 'elementskit' ),
            ]
		);

		$this->add_control(
            'click_action',
            [
                'label' =>esc_html__( 'Click Action', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'popup',
                'options' => [
                    'popup' =>esc_html__( 'Play In Popup', 'elementskit' ),
                    'inline' =>esc_html__( 'Play Inline', 'elementskit' )
                ],
            ]
		);

		 $this->add_control(
            'button_icons',
            [
                'label' =>esc_html__( 'Button Icon', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'button_icon',
                'default' => [
                    'value' => 'icon icon-play',
                    'library' => 'ekiticons',
                ],
				'label_block' => true
            ]
         );
         
         $this->add_control(
			'enable_filter',
			[
				'label' => esc_html__( 'Show Filter?', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'grid_style!' => ['carousel']
                ]
			]
        );

        $this->add_control(
			'play_btn',
			[
				'label' => esc_html__( 'Play button on hover?', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
                'default' => 'no'
			]
        );

        // carousel settings

        $this->add_control(
            'carousel_settings',
            [
                'label' => esc_html__( 'Carousel Settings', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'grid_style' => ['carousel']
                ]
            ]
        );

        $this->add_responsive_control(
			'left_right_spacing',
			[
				'label' => esc_html__( 'Spacing Left Right', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
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
					'size' => 15,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'default' => [
					'size' => 15,
					'unit' => 'px',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-slide' => '--ekit_video_slider_left_right_spacing: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'grid_style' => ['carousel']
                ]
			]
		);


        $this->add_responsive_control(
			'slidetosho',
			[
				'label' => esc_html__( 'Slides To Show', 'elementskit' ),
                'type' =>  Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
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
                'condition' => [
                    'grid_style' => ['carousel']
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-video-gallery.ekit-carousel' => '--ekit_video_slidetoshow: {{SIZE}};',
                ],
			]
        );

        $this->add_responsive_control(
			'slidesToScroll',
			[
				'label' => esc_html__( 'Slides To Scroll', 'elementskit' ),
                'type' =>  Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
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
                'condition' => [
                    'grid_style' => ['carousel']
                ]
			]
		);



		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'elementskit' ),
				'type' =>  Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'grid_style' => ['carousel']
                ]
			]
		);
        $this->add_control(
            'speed',
            [
                'label' => esc_html__( 'Speed (ms)', 'elementskit' ),
                'type' =>  Controls_Manager::NUMBER,
                'min' => 1000,
                'max' => 15000,
                'step' => 100,
                'default' => 1000,
                'condition' => [
                    'autoplay' => 'yes',
                    'grid_style' => ['carousel']
                ]
            ]
        );
        $this->add_control(
            'pause_on_hover',
            [
                'label' => esc_html__( 'Pause on Hover', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'autoplay' => 'yes', 
                    'grid_style' => ['carousel']
                ]
            ]
        );
        $this->add_control(
			'show_arrow',
			[
				'label' => esc_html__( 'Show arrow', 'elementskit' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
                'default' => '',
                'condition' => [
                    'grid_style' => ['carousel']
                ]
			]
        );
        $this->add_control(
            'left_arrow_icon',
            [
                'label' => esc_html__( 'Left arrow Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'left_arrow',
                'default' => [
                    'value' => 'icon icon-left-arrow2',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'show_arrow' => 'yes',
                    'grid_style' => ['carousel']
                ]
            ]
        );

        $this->add_control(
            'right_arrow_icon',
            [
                'label' => esc_html__( 'Right arrow Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'right_arrow',
                'default' => [
                    'value' => 'icon icon-right-arrow2',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'show_arrow' => 'yes',
                    'grid_style' => ['carousel']
                ]
            ]
        );
        $this->add_control(
			'show_dot',
			[
				'label' => esc_html__( 'Show dots', 'elementskit' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
                'default' => '',
                'condition' => [
                    'grid_style' => ['carousel']
                ]
			]
        );
        $this->add_control(
			'carousel_loop',
			[
				'label' => esc_html__( 'Enable Loop?', 'elementskit' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
                'condition' => [
                    'grid_style' => ['carousel']
                ]
			]
        );
        // ./end carousel settings
		
		$this->end_controls_section();

        // item style tab

        $this->start_controls_section(
            'ekit_gallery_item_style',
            [
                'label' => esc_html__( 'Item', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_responsive_control(
            'item_text_height', [
                'label'			 =>esc_html__( 'Height', 'elementskit' ),
                'type'			 => Controls_Manager::SLIDER,
                'size_units'	 => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ]
                ],
                'selectors'		 => [
                    '{{WRAPPER}} .ekit-video-item'	=> 'height: {{SIZE}}{{UNIT}};',
                ]
 
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'item_overlay_color',
                'label' => esc_html__( 'Overlay Color', 'elementskit' ),
                'types' => [ 'classic' ],
                'selector' => '{{WRAPPER}} .ekit-video-item a:before',
            ]
        );

        $this->add_control(
            'item_text_padding',
            [
                'label' =>esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'item_border',
                'label'    => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-video-item'
            ]
        );
        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' =>esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px'],
                'default' => [
                    'top' => '',
                    'right' => '',
                    'bottom' => '' ,
                    'left' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-item' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'item_box_shadow',
                'selector'  => '{{WRAPPER}} .ekit-video-item',
            ]
        );

        $this->end_controls_section();

        // image
        $this->start_controls_section(
            'ekit_gallery_image_style',
            [
                'label' => esc_html__( 'Image', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_control(
            'image_padding',
            [
                'label' =>esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-item a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'image_border',
                'label'    => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-video-item a'
            ]
        );
        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' =>esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px'],
                'default' => [
                    'top' => '',
                    'right' => '',
                    'bottom' => '' ,
                    'left' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-item a' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'image_box_shadow',
                'selector'  => '{{WRAPPER}} .ekit-video-item a',
            ]
        );

        $this->end_controls_section();

        // Video Icon
        $this->start_controls_section(
            'ekit_gallery_video_icon_style',
            [
                'label' => esc_html__( 'Video Icon', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_responsive_control(
            'video_icon_font_size',
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
                    '{{WRAPPER}} .ekit-video-item .video-icon' => 'font-size: {{SIZE}}{{UNIT}};'
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'video_icon_bgc',
                'selector' => '{{WRAPPER}} .ekit-video-item .video-icon',
            )
        );

        $this->add_control(
            'video_icon_padding',
            [
                'label' =>esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-item .video-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'video_icon_border',
                'label'    => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-video-item .video-icon'
            ]
        );
        $this->add_responsive_control(
            'video_icon_border_radius',
            [
                'label' =>esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px'],
                'default' => [
                    'top' => '',
                    'right' => '',
                    'bottom' => '' ,
                    'left' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-item .video-icon' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'video_icon_box_shadow',
                'selector'  => '{{WRAPPER}} .ekit-video-item .video-icon',
            ]
        );

        $this->end_controls_section();

        /**
         * Style Tab: Filter
         */
        $this->start_controls_section(
            'ekit_gallery_section_filter_style',
            [
                'label' => esc_html__( 'Filter', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'enable_filter' => 'yes'
                ]
            ]
        );
        $this->add_control(
			'ekit_gallery_filter_style_choose',
			[
				'label' => esc_html__( 'Filter Style', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'elementskit' ),
					'style_one' => esc_html__( 'Style One', 'elementskit' ),
					'style_two' => esc_html__( 'Style Two', 'elementskit' ),
					'style_three' => esc_html__( 'Style Three', 'elementskit' ),
					'style_four' => esc_html__( 'Style Four', 'elementskit' ),
					'style_five' => esc_html__( 'Style Five', 'elementskit' ),
					'style_six' => esc_html__( 'Style Six', 'elementskit' ),
					'style_seven' => esc_html__( 'Style Seven', 'elementskit' ),
					'style_eight' => esc_html__( 'Style Eight', 'elementskit' ),
					'style_nine' => esc_html__( 'Style Nine', 'elementskit' ),
					'style_ten' => esc_html__( 'Style Ten', 'elementskit' ),
				],
			]
		);

        $this->add_control(
			'ekit_gallery_fill_nav_content',
			[
				'label' => esc_html__( 'Nav Fill', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

        $this->add_responsive_control(
            'ekit_gallery_nav_items_alignment',
            [
                'label'    => esc_html__( 'Filter Items Alignment', 'elementskit' ),
                'type'     => Controls_Manager::CHOOSE,
                'options'  => [
                    'flex-start'   => [
                        'title' => esc_html__( 'Left', 'elementskit' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'elementskit' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'space-between'  => [
                        'title' => esc_html__( 'Justify', 'elementskit' ),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                    'flex-end'  => [
                        'title' => esc_html__( 'Right', 'elementskit' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'  => 'center',
                'selectors'=> [
                    '{{WRAPPER}} .elementskit-main-filter' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_gallery_fill_nav_content' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ekit_gallery_nav_alignment',
            [
                'label'    => esc_html__( 'Filter Alignment', 'elementskit' ),
                'type'     => Controls_Manager::CHOOSE,
                'options'  => [
                    'left'   => [
                        'title' => esc_html__( 'Left', 'elementskit' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'elementskit' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__( 'Right', 'elementskit' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'  => 'center',
                'condition' => [
                    'ekit_gallery_fill_nav_content!' => 'yes'
                ]
            ]
        );
        $this->add_control(
			'ekit_gallery_nav_item_show_caret',
			[
				'label' => esc_html__( 'Show Caret', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'ekit_gallery_filter_style_choose' => ['style_three', 'style_four']
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_filter_caret_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_three.elementskit_nav_caret > li > a.selected::before' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_nav_item_show_caret' => 'yes',
                    'ekit_gallery_filter_style_choose' => ['style_three']
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_gallery_filter_caret_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'size' => 19,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_three.elementskit_nav_caret > li > a.selected::before' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_nav_item_show_caret' => 'yes',
                    'ekit_gallery_filter_style_choose' => ['style_three']
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_filter_caret_bottom_position',
			[
				'label' => esc_html__( 'Top', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
                        'max' => 100,
                        'step' => .5
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_three.elementskit_nav_caret > li > a.selected::before' => 'top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_nav_item_show_caret' => 'yes',
                    'ekit_gallery_filter_style_choose' => ['style_three']
                ]
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_gallery_filter_caret_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit_filter_style_three.elementskit_nav_caret > li > a.selected::before',
                'condition' => [
                    'ekit_gallery_nav_item_show_caret' => 'yes',
                    'ekit_gallery_filter_style_choose' => ['style_three']

                ],
                'exclude' => [
                    'image'
                ],
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_filter_caret_four_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_four.elementskit_nav_caret > li > a.selected::after' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_nav_item_show_caret' => 'yes',
                    'ekit_gallery_filter_style_choose' => ['style_four']
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_gallery_filter_caret_four_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_four.elementskit_nav_caret > li > a.selected::after' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_nav_item_show_caret' => 'yes',
                    'ekit_gallery_filter_style_choose' => ['style_four']
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_filter_caret_four_bottom_position',
			[
				'label' => esc_html__( 'Bottom', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'%' => [
						'min' => -100,
                        'max' => 100,
                        'step' => .5
					],
					'px' => [
						'min' => -100,
                        'max' => 100,
                        'step' => .5
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => -7.5,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_four.elementskit_nav_caret > li > a.selected::after' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_nav_item_show_caret' => 'yes',
                    'ekit_gallery_filter_style_choose' => ['style_four']
                ]
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_gallery_filter_caret_four_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit_filter_style_four.elementskit_nav_caret > li > a.selected::after',
                'condition' => [
                    'ekit_gallery_nav_item_show_caret' => 'yes',
                    'ekit_gallery_filter_style_choose' => ['style_four']
                ],
                'exclude' => [
                    'image'
                ],
			]
        );

        $this->add_control(
			'ekit_gallery_filter_show_divider_hr_style_six',
			[
                'type' => Controls_Manager::DIVIDER,
                'condition' => [
                    'ekit_gallery_filter_style_choose' => ['style_four'],
                ],
			]
		);

        $this->add_control(
			'ekit_gallery_filter_show_divider',
			[
				'label' => esc_html__( 'Show Divider', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'ekit_gallery_filter_style_choose' => ['style_four', 'style_five'],
                ],
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_gallery_filter_divider_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit_filter_style_five.elementskit_divider_active > li > a::before, {{WRAPPER}} .elementskit_filter_style_four.elementskit_divider_active > li > a::before',
                'condition' => [
                    'ekit_gallery_filter_show_divider' => 'yes',
                    'ekit_gallery_filter_style_choose' => ['style_four', 'style_five'],
                ],
                'exclude' => [
                    'image'
                ],
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_filter_divider_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_five.elementskit_divider_active > li > a::before' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit_filter_style_four.elementskit_divider_active > li > a::before' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_filter_show_divider' => 'yes',
                    'ekit_gallery_filter_style_choose' => ['style_four', 'style_five'],
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_gallery_filter_divider_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'size' => 19,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_five.elementskit_divider_active > li > a::before' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit_filter_style_four.elementskit_divider_active > li > a::before' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_filter_show_divider' => 'yes',
                    'ekit_gallery_filter_style_choose' => ['style_four', 'style_five'],
                ]
			]
        );

        $this->add_control(
			'ekit_gallery_filter_divider_and_caret_hr',
			[
                'type' => Controls_Manager::DIVIDER,
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_gallery_filter_border_bottom_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit_filter_style_six > li > a.selected > .elementskit_filter_nav_text::before',
                'condition' => [
                    'ekit_gallery_filter_style_choose' => ['style_six'],
                ],
                'exclude' => [
                    'image'
                ],
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_filter_border_bottom_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_six > li > a > .elementskit_filter_nav_text::before' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_filter_style_choose' => ['style_six'],
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_gallery_filter_border_bottom_divider_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_six > li > a > .elementskit_filter_nav_text::before' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_filter_style_choose' => ['style_six'],
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_filter_border_bottom_divider_bottom',
			[
				'label' => esc_html__( 'Bottom', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 5,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => -5,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_six > li > a > .elementskit_filter_nav_text::before' => 'bottom: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_gallery_filter_style_choose' => ['style_six'],
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_gallery_filter_border_bottom_divider_left',
			[
				'label' => esc_html__( 'Left', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 5,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_filter_style_six > li > a > .elementskit_filter_nav_text::before' => 'left: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_gallery_filter_style_choose' => ['style_six'],
                ]
			]
		);

        $this->add_responsive_control(
            'ekit_gallery_filter_item_margin',
            [
                'label' => esc_html__( 'Nav Item Spacing', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-main-filter > li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filters_margin_bottom',
            [
                'label' => esc_html__( 'Nav Bottom Spacing', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'size_units' => 'px',
                'default' => [
					'unit' => 'px',
					'size' => 20,
				],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-main-filter' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'ekit_gallery_tabs_filter_style' );

        $this->start_controls_tab(
            'ekit_gallery_tab_filter_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_gallery_filter_typography_normal',
                'label' => esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-main-filter > li > a',
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_color_normal',
            [
                'label' => esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-main-filter > li > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_background_color_normal',
            [
                'label' => esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-main-filter > li > a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_gallery_filter_border_normal',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .elementskit-main-filter > li > a',
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_border_radius_normal',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-main-filter > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'placeholder' => [
                    'top' => '',
                    'right' => '',
                    'bottom' => '',
                    'left' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-main-filter > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_gallery_filter_box_shadow',
                'selector' => '{{WRAPPER}} .elementskit-main-filter > li > a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_gallery_tab_dots_hover',
            [
                'label' => esc_html__( 'Active', 'elementskit' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_gallery_filter_typography_hover',
                'label' => esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-main-filter > li > a.selected',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_gallery_filter_typography_hover_sahdow',
                'selector' => '{{WRAPPER}} .elementskit-main-filter > li > a.selected',
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_color_hover',
            [
                'label' => esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-main-filter > li > a.selected' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_background_color_hover',
            [
                'label' => esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-main-filter > li > a.selected' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_gallery_filter_style_choose!' => 'style_two'
                ]
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-main-filter > li > a.selected .elementskit_blur_shadow_panel,{{WRAPPER}} .elementskit-main-filter > li > a.selected',
                'condition' => [
                    'ekit_gallery_filter_style_choose' => 'style_two'
                ],
                'exclude' => [
                    'classic',
                    'image'
                ],
			]
		);

        $this->add_responsive_control(
            'ekit_gallery_filter_border_color_hover',
            [
                'label' => esc_html__( 'Border Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-main-filter > li > a.selected' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_gallery_filter_box_shadow_hover',
                'selector' => '{{WRAPPER}} .elementskit-main-filter > li > a.selected',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
			'ekit_gallery_filter_container_heading',
			[
				'label' => esc_html__( 'Container Style', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'ekit_gallery_fill_nav_content' => 'yes'
                ],
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_gallery_filter_container_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-main-filter',
                'exclude' => [
                    'image',
                ],
                'condition' => [
                    'ekit_gallery_fill_nav_content' => 'yes'
                ],
			]
		);

        $this->add_responsive_control(
			'ekit_gallery_filter_container_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-main-filter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_fill_nav_content' => 'yes'
                ],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_gallery_filter_container_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-main-filter',
                'condition' => [
                    'ekit_gallery_fill_nav_content' => 'yes'
                ],
			]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_gallery_filter_container_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-main-filter',
                'condition' => [
                    'ekit_gallery_fill_nav_content' => 'yes'
                ],
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_filter_container_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-main-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_gallery_fill_nav_content' => 'yes'
                ],
			]
        );

        $this->end_controls_section();

        //  Navigation section

        $this->start_controls_section(
            'section_navigation',
            [
                'label' => esc_html__( 'Arrows', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_arrow' => 'yes',
                    'grid_style' => ['carousel']
                ]
            ]
        );

        $this->add_responsive_control(
            'arrow_size',
            [
                'label' => esc_html__( 'Size', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-navigation-button i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'arrow_icon_typography',
                'label' => esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-navigation-button i',
            ]
        );

        $this->add_responsive_control(
            'arrow_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-navigation-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'arrow_border_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-navigation-button',
            ]
        );

        $this->add_responsive_control(
            'arrow_border_radious',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-navigation-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'arrow_shadow',
                'selector'  => '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-navigation-button',
            ]
        );
        
        $this->add_responsive_control(
            'arrow_left_pos',
            [
                'label' => esc_html__( 'Left Arrow Position', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -10,
                        'max' => 100,
                    ],
                ],

                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_right_pos',
            [
                'label' => esc_html__( 'Right Arrow Position', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -10,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        // Arrow Normal

        $this->start_controls_tabs('ekit_logo_style_tabs');

        $this->start_controls_tab(
            'ekit_logo_arrow_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label' => esc_html__( 'Arrow Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-navigation-button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'arrow_background',
            [
                'label' => esc_html__( 'Arrow Background', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-navigation-button' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        //  Arrow hover tab

        $this->start_controls_tab(
            'arrow_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_control(
            'arrow_hv_color',
            [
                'label' => esc_html__( 'Arrow Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-navigation-button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'arrow_hover_background',
            [
                'label' => esc_html__( 'Arrow Background', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-navigation-button:hover' => 'background: {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


        // Dots

        $this->start_controls_section(
            'navigation_dot',
            [
                'label' => esc_html__( 'Dots', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_dot' => 'yes',
                    'grid_style' => ['carousel']
                ]
            ]
        );

        $this->add_control(
            'client_logo_dot_style',
            [
                'label' => esc_html__( 'Dot Style', 'elementskit' ),
                'type' =>  Controls_Manager::SELECT,
                'default' => 'dot_dotted',
                'options' => [
                    'dot_default'  => esc_html__( 'Default', 'elementskit' ),
                    'dot_dashed' => esc_html__( 'Dashed', 'elementskit' ),
                    'dot_dotted' => esc_html__( 'Dotted', 'elementskit' ),
                    'dot_paginated' => esc_html__( 'Paginate', 'elementskit' ),
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_left_right_spacing',
            [
                'label' => esc_html__( 'Spacing Left Right', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 8,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'default' => [
                    'size' => 8,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-pagination span' => 'margin-right: {{SIZE}}{{UNIT}};margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_top_to_bottom',
            [
                'label' => esc_html__( 'Spacing Top To Bottom', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px'],
                'range' => [
                    'px' => [
                        'min' => -120,
                        'max' => 120,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],

                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-pagination' => ' -webkit-transform:translateY( {{SIZE}}{{UNIT}});transform: translateY( {{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_control(
            'dot_color',
            [
                'label' => esc_html__( 'Dot Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel.dot_paginated .swiper-pagination span' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'client_logo_dot_style' => 'dot_paginated'
                ]
            ]
        );

        $this->add_responsive_control(
            'dot_width',
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
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-pagination span' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dot_height',
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
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-pagination span' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dot_border_radius',
            [
                'label' => esc_html__( 'Border radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-pagination span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'dot_background',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-pagination span',
            ]
        );

        $this->add_control(
            'dot_active_heading',
            [
                'label' => esc_html__( 'Active', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'dot_active_background',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-pagination span.swiper-pagination-bullet-active',
            ]
        );

        $this->add_responsive_control(
            'dot_active_width',
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
                    'size' => 40,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-pagination span.swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'client_logo_dot_style' => 'dot_dashed'
                ],
            ]
        );

        $this->add_responsive_control(
            'dot_active_scale',
            [
                'label' => esc_html__( 'Height', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => .5,
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1.2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-video-gallery.ekit-carousel .swiper-pagination span.swiper-pagination-bullet-active' => 'transform: scale({{SIZE}});',
                ],
                'condition' => [
                    'client_logo_dot_style' => 'dot_dotted'
                ],
            ]
        );

        $this->end_controls_section();
		

		$this->insert_pro_message();
	}

	private function getVimeoVideoIdFromUrl($url = '') {
        $regs = array();
        $id = '';
        if (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs)) {
            $id = $regs[3];
        }
    
        return $id;
	}
	
	private function getVimeoVideoThumb($id = ''){
		if(empty($id)){ return false; }
		$hash = unserialize(file_get_contents("https://vimeo.com/api/v2/video/$id.php"));

		return $hash[0]['thumbnail_large'];  
	}

    private function slugify($text){
        if(empty($text)){ return ''; }
      // replace non letter or digits by -
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    
      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    
      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);
    
      // trim
      $text = trim($text, '-');
    
      // remove duplicate -
      $text = preg_replace('~-+~', '-', $text);
    
      // lowercase
      $text = strtolower($text);
    
      if (empty($text)) {
        return 'n-a';
      }
    
      return $text;
    }

	protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
	}

    protected function render_raw( ) {
		$settings = $this->get_settings_for_display();
        extract($settings);
        
        // Left Arrow Icon
        $migrated = isset( $settings['__fa4_migrated']['left_arrow_icon'] );
        // - Check if its a new widget without previously selected icon using the old Icon control
        $is_new = empty( $settings['left_arrow'] );
        $prevArrowIcon = ($is_new || $migrated) ? (!empty($settings['left_arrow_icon']) && $settings['left_arrow_icon']['library'] != 'svg' ? $settings['left_arrow_icon']['value'] : '') : $settings['left_arrow'];

		// Right Arrow Icon
        $migrated = isset( $settings['__fa4_migrated']['right_arrow_icon'] );
        // - Check if its a new widget without previously selected icon using the old Icon control
        $is_new = empty( $settings['right_arrow'] );
        $nextArrowIcon = ($is_new || $migrated) ? (!empty($settings['right_arrow_icon']) && $settings['left_arrow_icon']['library'] != 'svg' ? $settings['right_arrow_icon']['value'] : '') : $settings['right_arrow'];
        

		$this->add_render_attribute( 'wrapper', 'class', 'ekit-video-gallery');
		$this->add_render_attribute( 'wrapper', 'class', 'ekit-'. $grid_style);
        if($grid_style == 'carousel'){
            $this->add_render_attribute( 'wrapper', 'class', 'arrow_inside');
            $this->add_render_attribute( 'wrapper', 'class', !empty($settings['ekit_testimonial_show_dot']) ? 'slider-dotted' : '');
        }
        $this->add_render_attribute( 'wrapper', 'class', 'ekit-column-'. $ekit_column);
        $this->add_render_attribute( 'wrapper', 'class', 'ekit-column-tablet-'. $ekit_column_tablet);
        $this->add_render_attribute( 'wrapper', 'class', 'ekit-column-mobile-'. $ekit_column_mobile);
        $this->add_render_attribute( 'wrapper', 'class', $client_logo_dot_style);
        $this->add_render_attribute( 'wrapper', 'class', $play_btn == 'yes' ? 'ekit_play_on' : '');

        // Config
        $config = [
            'rtl'				=> is_rtl(),
            'arrows'			=> $show_arrow == 'yes' ? true : false,
            'dots'				=> $show_dot == 'yes' ? true : false,
			'prevArrow'			=> $prevArrowIcon,
			'nextArrow'			=> $nextArrowIcon,
            'autoplay'			=> $autoplay == 'yes' ? true : false,
            'speed'		        => !empty($speed) ? $speed : 1000,
            'infinite'			=> $autoplay == 'yes' ? true : false,
            'slidesPerView'		=> !empty($slidetosho) ? $slidetosho['size'] : '',
            'slidesPerGroup'	=> !empty($slidesToScroll) ? $slidesToScroll['size'] : '',
            'pauseOnMouseEnter'	    => $pause_on_hover == 'yes' ? true : false,
            'loop'                  => !empty($carousel_loop) ? true : false,
            'breakpointsInverse'    => true,
            'breakpoints'		=> [
                360 => [
                    'slidesPerView'     => !empty($slidetosho_mobile) ? $slidetosho_mobile['size'] : '',
                    'slidesPerGroup'    => !empty($slidesToScroll_mobile) ? $slidesToScroll_mobile['size'] : '',
                    'spaceBetween'      => !empty($left_right_spacing_mobile['size']) ? $left_right_spacing_mobile['size'] : 10,
                ],
                768 => [
                    'slidesPerView'     => !empty($slidetosho_tablet) ? $slidetosho_tablet['size'] : '',
                    'slidesPerGroup'    => !empty($slidesToScroll_tablet) ? $slidesToScroll_tablet['size'] : '',
                    'spaceBetween'      => !empty($left_right_spacing_tablet['size']) ? $left_right_spacing_tablet['size'] : 10,
                ],
                1024 => [
                    'slidesPerView'     => !empty($slidetosho) ? $slidetosho['size'] : '',
                    'slidesPerGroup'    => !empty($slidesToScroll) ? $slidesToScroll['size'] : '',
                    'spaceBetween'      => !empty($left_right_spacing['size']) ? $left_right_spacing['size'] : 15,
                ]
            ],
        ];

		$this->add_render_attribute( 'wrapper', 'data-config', wp_json_encode($config) );

		// Swiper container
		$this->add_render_attribute('swiper-container', 'class', method_exists('\ElementsKit_Lite\Utils', 'swiper_class') ? \ElementsKit_Lite\Utils::swiper_class() : 'swiper');
		?>
		<div class="ekit-video-gallery-wrapper">

			<!-- filter nav -->
			<?php if($enable_filter == 'yes') : ?>
				<ul class="elementskit-main-filter elementskit_filter_<?php echo esc_attr($ekit_gallery_filter_style_choose); ?>">
					<li><a href="#" data-value="" class="selected"><span class="elementskit_filter_nav_text">All</span></a></li>
					<?php 
					$appended_cat = [];
					foreach($video_content as $nav) {
						if(!empty($nav['video_category']) && !in_array($nav['video_category'], $appended_cat)) : ?>
							<li><a href='' data-value="<?php echo $this->slugify(esc_attr($nav['video_category'])); ?>"><span class="elementskit_filter_nav_text"><?php echo esc_html($nav['video_category']); ?></span></a></li>
							<!-- // removed duplication -->
							<?php array_push($appended_cat, $nav['video_category']);
						endif;
					} ?>
				</ul>
			<?php endif; ?>
			<!-- filter nav -->


			<div <?php echo \ElementsKit_Lite\Utils::render($this->get_render_attribute_string( 'wrapper' )); ?>>
				<?php if($grid_style == 'carousel') : ?>
					<div <?php $this->print_render_attribute_string('swiper-container'); ?>>
						<div class="swiper-wrapper">
				<?php endif; ?>

				<?php foreach($video_content as $video) :
					$video_url = $video['video_url'];
					$video_type = $video['video_type'];
					$video_id = '';
					$video_thumb = $video['video_image']['url'];
					$data_url = '';
					
					if($video_type == 'youtube') {
						parse_str( parse_url( $video_url['url'], PHP_URL_QUERY ), $y_v_id );
						$video_id = !empty($y_v_id['v']) ? $y_v_id['v'] : '';
						$video_thumb = empty($video_thumb) ? 'https://img.youtube.com/vi/'. esc_attr($video_id) .'/0.jpg' : $video_thumb;
						$data_url = 'https://www.youtube.com/embed/'. $video_id .'?autoplay=1&rel=0';
					} else if($video_type == 'vimeo'){
						$video_id = $this->getVimeoVideoIdFromUrl($video_url['url']);
						$video_thumb = empty($video_thumb) ? $this->getVimeoVideoThumb($video_id) : $video_thumb;
						$data_url = 'https://player.vimeo.com/video/'.$video_id.'?autoplay=1&version=3&enablejsapi=1';
					}
				?>
					<div class="ekit-video-item <?php echo $grid_style == 'carousel' ? 'swiper-slide ' : ' '; echo $this->slugify(esc_attr($video['video_category'])); ?>">

						<?php if($grid_style == 'carousel') : ?>
						<div class="swiper-slide-inner">
						<?php endif; ?>

							<a class="video-link <?php echo esc_attr($click_action); ?>" href="<?php echo esc_url($video_url['url']); ?>" data-url="<?php echo esc_url($data_url); ?>">
								<img src="<?php echo esc_url($video_thumb); ?>" alt="Video thumb">
								<?php 
									// new icon
										$migrated = isset( $settings['__fa4_migrated']['button_icons'] );
										// Check if its a new widget without previously selected icon using the old Icon control
										$is_new = empty( $settings['button_icon'] );
										if ( $is_new || $migrated ) {
											// new icon
											Icons_Manager::render_icon( $settings['button_icons'], [ 'aria-hidden' => 'true', 'class'=>  'video-icon' ] );
										} else {
											?>
											<i class="<?php echo esc_attr($settings['button_icon']); ?> video-icon" aria-hidden="true"></i>
											<?php
										}
								?>
							</a>

						<?php if($grid_style == 'carousel') : ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>

				<?php if($grid_style == 'carousel') : ?>
						</div>

						<?php if(!empty($settings['show_dot'])) : ?>
							<div class="swiper-pagination"></div>
						<?php endif; ?>

						<?php if(!empty($settings['show_arrow'])) : ?>
							<div class="swiper-navigation-button swiper-button-prev"><i class="<?php echo esc_attr($prevArrowIcon); ?>"></i></div>
							<div class="swiper-navigation-button swiper-button-next"><i class="<?php echo esc_attr($nextArrowIcon); ?>"></i></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
