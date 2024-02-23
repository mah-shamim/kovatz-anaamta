<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Gallery_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Gallery extends Widget_Base
{
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;

    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );
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
        return 'https://wpmet.com/doc/how-to-create-gallery-in-wordpress/';
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'ekit_gallery_section_gallery',
            [
                'label' => esc_html__( 'Gallery Content', 'elementskit' ),
            ]
        );
        $repeater = new Repeater();

        $repeater->add_control(
            'ekit_gallery_filter_label',
            [
                'label' => esc_html__( 'Filter Label', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => '',
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $repeater->add_control(
            'ekit_gallery_filter_images',
            [
                'label' => esc_html__( 'Add Images', 'elementskit' ),
                'type' => Controls_Manager::GALLERY,
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'ekit_gallery_items',
            [
                'label' => '',
                'type' => Controls_Manager::REPEATER,
                'fields' =>  $repeater->get_controls(),
                'title_field' => '{{{ekit_gallery_filter_label}}}',
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
            'ekit_gallery_style',
            [
                'label' => esc_html__( 'Style', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__( 'Grid', 'elementskit' ),
                    'masonry' => esc_html__( 'Masonry', 'elementskit' ),
                ],
            ]
        );
        $ekit_gallery_columns = range( 1, 8 );
        $ekit_gallery_columns = array_combine( $ekit_gallery_columns, $ekit_gallery_columns );
        $this->add_responsive_control(
            'ekit_gallery_columns',
            [
                'label' => esc_html__( 'Columns', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => $ekit_gallery_columns,
                'frontend_available' => true,
                'condition' => [
                    'ekit_gallery_style' => [ 'grid', 'masonry' ]
				],
				'selectors'	=> [
					'{{WRAPPER}} .ekit_gallery_grid_item'	=> 'flex: 0 0 calc(100% / {{SIZE}}); width: calc(100% / {{SIZE}});',
				]
            ]
        );

        $this->add_responsive_control(
			'ekit_gallery_image_aspect_ratio',
			[
				'label' => esc_html__( 'Image Aspect Ratio', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => .5,
                        'max' => 2.5,
                        'step' => .1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => .5,
				],
                'condition' => [
                    'ekit_gallery_style' => [ 'grid' ]
                ]
			]
		);

        /**
        * Control: Image Size
        */
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'              => 'ekit_gallery_img_size',
                'default'           => 'full',
            ]
        );

        $this->end_controls_section();


        /*
         * Settings
         */
        $this->start_controls_section(
            'ekit_gallery_section_settings',
            [
                'label' => esc_html__( 'Settings', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_gallery_filter_all_label',
            [
                'label' => esc_html__( '"All" Filter Label', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'All', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_content_alignment',
            [
                'label'    => esc_html__( 'Content Alignment', 'elementskit' ),
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
                'selectors'=> [
                    '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-hover-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_gallery_content_vertical_alignment',
			[
				'label' => esc_html__( 'Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementskit' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementskit' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'center',
				'toggle' => true,
			]
		);


        $this->add_control(
			'ekit_gallery_caption',
			[
				'label' => esc_html__( 'Show Caption', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
        );

        $this->add_control(
			'ekit_gallery_description',
			[
				'label' => esc_html__( 'Show Description', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
                'default' => 'no',
                'description' => 'If you show description goto gallery and set image description'
			]
		);

        $this->add_control(
			'ekit_gallery_filter_label_show',
			[
				'label' => esc_html__( 'Show Filter Label', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
                'default' => 'no',
			]
		);

        $this->add_control(
			'ekit_gallery_open_lightbox',
			[
				'label' => esc_html__( 'Show Lightbox', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

        $this->add_control(
            'ekit_gallery_link_icons',
            [
                'label' => esc_html__( 'Lightbox Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_gallery_link_icon',
                'default' => [
                    'value' => 'icon icon-plus-circle',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_gallery_open_lightbox' => 'yes'
                ]
            ]
        );

        // $this->add_control(
        //     'ekit_gallery_enable_link',
        //     [
        //         'label' => esc_html__( 'Enable Link', 'elementskit-lite' ),
        //         'type' => Controls_Manager::SWITCHER,
        //         'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
        //         'label_off' => esc_html__( 'No', 'elementskit-lite' ),
        //         'return_value' => 'yes',
        //         'condition' => [
        //             'ekit_gallery_open_lightbox!' => 'yes'
        //         ]
        //     ]
        // );

        // $this->add_control(
        //     'ekit_gallery_web_link',
        //     [
        //         'label' => esc_html__( 'Link', 'elementskit-lite' ),
        //         'type' => Controls_Manager::URL,
        //         'placeholder' => esc_html__( 'https://wpmet.com', 'elementskit-lite' ),
        //         'show_external' => true,
        //         'condition' => [
        //             'ekit_gallery_enable_link' => 'yes',
        //             'ekit_gallery_open_lightbox!' => 'yes'
        //         ],
        //     ]
        // );

        $this->add_control(
			'ekit_gallery_show_image_gallery',
			[
				'label' => esc_html__( 'Show Image Slideshow', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'True', 'elementskit' ),
				'label_off' => esc_html__( 'Flase', 'elementskit' ),
				'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'ekit_gallery_open_lightbox' => 'yes'
                ]
			]
		);

        $this->add_control(
            'ekit_gallery_ordering',
            [
                'label' => esc_html__( 'Ordering', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => esc_html__( 'Default', 'elementskit' ),
                    'random' => esc_html__( 'Random', 'elementskit' ),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ekit_gallery_section_layout_style',
            [
                'label' => esc_html__( 'Layout', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'ekit_gallery_columns_gap',
            [
                'label' => esc_html__( 'Columns Gap', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 5,
                    'unit' => 'px',
                ],
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'selectors' => [
					'{{WRAPPER}} .ekit_gallery_grid_item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit_gallery_grid_wraper' => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
				],
            ]
        );
        $this->add_responsive_control(
			'ekit_gallery_rows_gap',
			[
				'label' => esc_html__( 'Rows Gap', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '10',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => 'false',
                ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        // $this->add_responsive_control(
        //     'ekit_gallery_rows_gap',
        //     [
        //         'label' => esc_html__( 'Rows Gap', 'elementskit-lite' ),
        //         'type' => Controls_Manager::SLIDER,
        //         'default' => [
        //             'size' => 10,
        //             'unit' => 'px',
        //         ],
        //         'size_units' => [ 'px', '%' ],
        //         'range' => [
        //             'px' => [
        //                 'max' => 100,
        //             ],
        //         ],
        //         'tablet_default' => [
        //             'unit' => 'px',
        //         ],
        //         'mobile_default' => [
        //             'unit' => 'px',
        //         ],
        //         'selectors' => [
		// 			'{{WRAPPER}} .elementskit-single-portfolio-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
		// 		],
        //     ]
        // );

        $this->add_responsive_control(
			'ekit_gallery_content_padding',
			[
				'label' => esc_html__( 'Content Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-hover-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

        /**
         * Style Tab: Thumbnails
         */
        $this->start_controls_section(
            'ekit_gallery_section_thumbnails_style',
            [
                'label' => esc_html__( 'Thumbnails', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
			'ekit_gallery_content_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->start_controls_tabs( 'ekit_gallery_thumbnail_hover_and_normal_tab_style' );

        $this->start_controls_tab(
            'ekit_gallery_tab_thumbnail_normal_style',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_gallery_tab_thumbnail_normal_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item',
			]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_gallery_tab_thumbnail_normal_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_gallery_thumbnail_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_gallery_tab_thumbnail_hover_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item:hover',
			]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_gallery_tab_thumbnail_hover_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item:hover',
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
			'ekit_gallery_thumbnail_filter_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

        $ekit_image_filters = [
            'normal' => esc_html__( 'Normal', 'elementskit' ),
            'opacity' => esc_html__( 'Opacity', 'elementskit' ),
            'scale' => esc_html__( 'Scale', 'elementskit' ),
            'rotate' => esc_html__( 'Rotate', 'elementskit' ),
            'blur' => esc_html__( 'Blur', 'elementskit' ),
            'gray-scale' => esc_html__( 'Gray Scale', 'elementskit' ),
        ];

        $this->add_control(
            'ekit_gallery_thumbnail_filter',
            [
                'label' => esc_html__( 'Image Filter', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => $ekit_image_filters,
            ]
        );

        $this->start_controls_tabs( 'ekit_gallery_thumbnail_filter_style' );

        $this->start_controls_tab(
            'ekit_gallery_tab_thumbnail_filter_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
                'condition' => [
                    'ekit_gallery_thumbnail_filter!' => 'normal'
                ]
            ]
        );

        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_normal_opacity',
			[
				'label' => esc_html__( 'Opacity', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0.5,
                        'max' => 1,
                        'step' => 0.1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 1,
				],
                'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-grid__img' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => 'opacity'
                ]
			]
		);
        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_normal_scale',
			[
				'label' => esc_html__( 'Scale', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
                        'max' => 2,
                        'step' => .1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
                'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-grid__img' => 'transform: scale({{SIZE}});',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => ['scale']
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_normal_scale_with_rotate',
			[
				'label' => esc_html__( 'Scale', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
                        'max' => 2,
                        'step' => .1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
                'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-portfolio-thumb' => 'transform: scale({{SIZE}});',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => ['rotate']
                ]
			]
        );


        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_normal_rotate',
			[
				'label' => esc_html__( 'Rotate', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
                        'max' => 360,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
                'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-grid__img' => 'transform: rotate({{SIZE}}deg);',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => 'rotate'
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_normal_blur',
			[
				'label' => esc_html__( 'Blur', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
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
					'{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-grid__img' => 'filter: blur({{SIZE}}{{UNIT}});',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => 'blur'
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_normal_grayscale',
			[
				'label' => esc_html__( 'Gray Scale', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
                        'max' => 100,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-grid__img' => 'filter: grayscale({{SIZE}}{{UNIT}});',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => 'gray-scale'
                ]
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_gallery_thumbnail_hover_filter',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
                'condition' => [
                    'ekit_gallery_thumbnail_filter!' => 'normal'
                ]
            ]
        );

        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_hover_opacity',
			[
				'label' => esc_html__( 'Opacity', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0.5,
                        'max' => 1,
                        'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0.5,
				],
                'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-grid__img' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => 'opacity'
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_hover_scale',
			[
				'label' => esc_html__( 'Scale', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
                        'max' => 2,
                        'step' => .1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
                'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-grid__img' => 'transform: scale({{SIZE}});',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => ['scale']
                ]
			]
		);
        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_hover_scale_with_rotate',
			[
				'label' => esc_html__( 'Scale', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
                        'max' => 2,
                        'step' => .1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
                'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-portfolio-thumb' => 'transform: scale({{SIZE}});',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => ['rotate']
                ]
			]
        );


        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_hover_rotate',
			[
				'label' => esc_html__( 'Rotate', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
                        'max' => 360,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
                'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-grid__img' => 'transform: rotate({{SIZE}}deg);',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => 'rotate'
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_hover_blur',
			[
				'label' => esc_html__( 'Blur', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
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
					'{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-grid__img' => 'filter: blur({{SIZE}}{{UNIT}});',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => 'blur'
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_gallery_thumbnail_filter_hover_grayscale',
			[
				'label' => esc_html__( 'Gray Scale', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
                        'max' => 100,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-grid__img' => 'filter: grayscale({{SIZE}}{{UNIT}});',
                ],
                'condition' => [
                    'ekit_gallery_thumbnail_filter' => 'gray-scale'
                ]
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'ekit_gallery_tilt_enable',
            [
                'label' => esc_html__( 'Tilt', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'ekit_gallery_tilt_axis',
            [
                'label' => esc_html__( 'Axis', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'both',
                'options' => [
                    'both' => esc_html__( 'Both', 'elementskit' ),
                    'x' => esc_html__( 'X Axis', 'elementskit' ),
                    'y' => esc_html__( 'Y Axis', 'elementskit' ),
                ],
                'condition' => [
                    'ekit_gallery_tilt_enable' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_tilt_amount',
            [
                'label' => esc_html__( 'Amount', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 20,
                ],
                'condition' => [
                    'ekit_gallery_tilt_enable' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_tilt_scale',
            [
                'label' => esc_html__( 'Scale', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 1.4,
                        'step' => 0.01,
                    ],
                ],
                'default' => [
                    'size' => 1.06,
                ],
                'condition' => [
                    'ekit_gallery_tilt_enable' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_tilt_caption_depth',
            [
                'label' => esc_html__( 'Depth', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-gallery-portfolio-tilt .elementskit-hover-area' => 'transform: translateZ({{SIZE}}px);',
                ],
                'condition' => [
                    'ekit_gallery_tilt_enable' => 'yes',
                ],
            ]
        );

        $this->add_control(
			'ekit_gallery_tilt_show_glare',
			[
				'label' => esc_html__( 'Show Glare', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'ekit_gallery_tilt_enable' => 'yes',
                ],
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_tilt_perspective',
			[
				'label' => esc_html__( 'Perspective', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 600,
                ],
                'condition' => [
                    'ekit_gallery_tilt_enable' => 'yes',
                ],
			]
		);

        $this->add_responsive_control(
			'ekit_gallery_tilt_maxGlare',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
                        'max' => 1,
                        'step' => .1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => .6,
				],
                'condition' => [
                    'ekit_gallery_tilt_show_glare' => 'yes',
                    'ekit_gallery_tilt_enable' => 'yes',
                ],
			]
		);

        $this->end_controls_section();


        /**
         * Style Tab: Caption
         */
        $this->start_controls_section(
            'ekit_gallery_section_caption_style',
            [
                'label' => esc_html__( 'Caption', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_gallery_caption' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_gallery_caption_typography',
                'label' => esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-title',
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_caption_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_caption_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'ekit_gallery_tabs_caption_style' );

        $this->start_controls_tab(
            'ekit_gallery_tab_caption_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_caption_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_caption_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_gallery_caption_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-title',
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_caption_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_gallery_caption_text_shadow',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-title',
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_gallery_caption_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-title',
			]
		);


        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_gallery_tab_caption_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_caption_bg_color_hover',
            [
                'label' => esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_caption_color_hover',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_caption_border_color_hover',
            [
                'label' => esc_html__( 'Border Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-title' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_gallery_caption_text_shadow_hover',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-title',
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_gallery_caption_box_shadow_hover',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-title',
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * Style Tab: Description
         */
        $this->start_controls_section(
            'ekit_gallery_section_description_style',
            [
                'label' => esc_html__( 'Description', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_gallery_description' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_gallery_description_typography',
                'label' => esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-description',
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_description_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_description_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'ekit_gallery_tabs_description_style' );

        $this->start_controls_tab(
            'ekit_gallery_tab_description_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_description_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-description' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_description_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_gallery_description_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-description',
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_description_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-description' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_gallery_description_text_shadow',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-description',
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_gallery_description_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-description',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_gallery_tab_description_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_description_bg_color_hover',
            [
                'label' => esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-description' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_description_color_hover',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_description_border_color_hover',
            [
                'label' => esc_html__( 'Border Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-description' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_gallery_description_text_shadow_hover',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-description',
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_gallery_description_box_shadow_hover',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-description',
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * Style Tab: Label
         */
        $this->start_controls_section(
            'ekit_gallery_section_filter_label_style',
            [
                'label' => esc_html__( 'Label', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_gallery_filter_label_show' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_gallery_filter_label_typography',
                'label' => esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-label',
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_label_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_label_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'ekit_gallery_tabs_filter_label_style' );

        $this->start_controls_tab(
            'ekit_gallery_tab_filter_label_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_label_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-label' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_label_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_gallery_filter_label_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-label',
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_label_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_gallery_filter_label_text_shadow',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-label',
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_gallery_filter_label_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item  .elementskit-gallery-label',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_gallery_tab_filter_label_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_label_bg_color_hover',
            [
                'label' => esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-label' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_label_color_hover',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_filter_label_border_color_hover',
            [
                'label' => esc_html__( 'Border Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-label' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_gallery_filter_label_text_shadow_hover',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-label',
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_gallery_filter_label_box_shadow_hover',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item:hover  .elementskit-gallery-label',
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * Style Tab: Link Icon
         */
        $this->start_controls_section(
            'ekit_gallery_section_link_icon_style',
            [
                'label' => esc_html__( 'Link Icon', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_gallery_open_lightbox' => 'yes',
                ]
            ]
        );

        $this->start_controls_tabs( 'ekit_gallery_tabs_link_icon_style' );

        $this->start_controls_tab(
            'ekit_gallery_tab_link_icon_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_link_icon_color',
            [
                'label' => esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_link_icon_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_gallery_link_icon_border_normal',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon',
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_link_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_link_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'size_units' => [ 'px' ],
                'condition' => [
                    'icon_type' => 'icon',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_link_icon_opacity_normal',
            [
                'label' => esc_html__( 'Opacity', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon, {{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon svg path' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_gallery_link_icon_height_width',
			[
				'label' => esc_html__( 'Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon i:before' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_gallery_link_icon_font_size',
			[
				'label' => esc_html__( 'Font Size', 'elementskit' ),
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
					'size' => 22,
				],
				'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-gallery-icon svg'  => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_gallery_tab_link_icon_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_link_icon_color_hover',
            [
                'label' => esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-gallery-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-gallery-icon svg path'   => 'stroke: {{VALUE}}; fill: {{VALUE}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_link_icon_bg_color_hover',
            [
                'label' => esc_html__( 'Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-gallery-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_link_icon_border_color_hover',
            [
                'label' => esc_html__( 'Border Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-gallery-icon' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_link_icon_opacity_hover',
            [
                'label' => esc_html__( 'Opacity', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-gallery-icon, {{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-gallery-icon svg path' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * Style Tab: Overlay
         */
        $this->start_controls_section(
            'ekit_gallery_image_overlay_style',
            [
                'label' => esc_html__( 'Overlay', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $ekit_image_blend_mode = [
            'normal' => esc_html__( 'Normal', 'elementskit' ),
            'multiply' => esc_html__( 'Multiply', 'elementskit' ),
            'screen' => esc_html__( 'Screen', 'elementskit' ),
            'overlay' => esc_html__( 'Overlay', 'elementskit' ),
            'darken' => esc_html__( 'Darken', 'elementskit' ),
            'lighten' => esc_html__( 'Lighten', 'elementskit' ),
            'color-dodge' => esc_html__( 'Color-dodge', 'elementskit' ),
            'color-burn' => esc_html__( 'Color-burn', 'elementskit' ),
            'difference' => esc_html__( 'Difference', 'elementskit' ),
            'exclusion' => esc_html__( 'Exclusion', 'elementskit' ),
            'hue' => esc_html__( 'hue', 'elementskit' ),
            'saturation' => esc_html__( 'Saturation', 'elementskit' ),
            'color' => esc_html__( 'Color', 'elementskit' ),
            'luminosity' => esc_html__( 'Luminosity', 'elementskit' ),
        ];

        $this->add_control(
			'ekit_gallery_show_image_overlay',
			[
				'label' => esc_html__( 'Show Overlay', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

        $this->start_controls_tabs( 'ekit_gallery_tab_overlay_style' );

        $this->start_controls_tab(
            'ekit_gallery_tab_overlay_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
                'condition' => [
                    'ekit_gallery_show_image_overlay' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_overlay_blend_mode',
            [
                'label' => esc_html__( 'Blend Mode', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => $ekit_image_blend_mode,
                'selectors' => [
                    '{{WRAPPER}} .ekit-gallery-image-overlay' => 'mix-blend-mode: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_gallery_show_image_overlay' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_gallery_overlay_background_color_normal',
                'types' => [ 'classic' ],
                'selector' => '{{WRAPPER}} .ekit-gallery-image-overlay',
                'exclude' => [
                    'image',
                    'gradient'
                ],
                'condition' => [
                    'ekit_gallery_show_image_overlay' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
			'ekit_gallery_overlay_background_scale_normal',
			[
				'label' => esc_html__( 'Scale', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
                        'max' => 2,
                        'step' => .1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
                'selectors' => [
					'{{WRAPPER}} .ekit-gallery-image-overlay' => 'transform: scale({{SIZE}});',
					'{{WRAPPER}} .elementskit-single-portfolio-item .elementskit-hover-area' => 'transform: scale({{SIZE}});',
                ],
                'condition' => [
                    'ekit_gallery_show_image_overlay' => 'yes'
                ]
			]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_gallery_tab_overlay_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
                'condition' => [
                    'ekit_gallery_show_image_overlay' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_gallery_overlay_blend_mode_hover',
            [
                'label' => esc_html__( 'Blend Mode', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => $ekit_image_blend_mode,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-portfolio-item:hover .ekit-gallery-image-overlay' => 'mix-blend-mode: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_gallery_show_image_overlay' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_gallery_overlay_background_color_hover',
                'types' => [ 'classic' ],
                'selector' => '{{WRAPPER}} .elementskit-single-portfolio-item:hover .ekit-gallery-image-overlay',
                'exclude' => [
                    'image',
                    'gradient'
                ],
                'condition' => [
                    'ekit_gallery_show_image_overlay' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
			'ekit_gallery_overlay_background_scale_hover',
			[
				'label' => esc_html__( 'Scale', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
                        'max' => 2,
                        'step' => .1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
                'selectors' => [
					'{{WRAPPER}} .elementskit-single-portfolio-item:hover .ekit-gallery-image-overlay' => 'transform: scale({{SIZE}});',
					'{{WRAPPER}} .elementskit-single-portfolio-item:hover .elementskit-hover-area' => 'transform: scale({{SIZE}});',
                ],
                'condition' => [
                    'ekit_gallery_show_image_overlay' => 'yes'
                ]
			]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * Style Tab: Filter
         */
        $this->start_controls_section(
            'ekit_gallery_section_filter_style',
            [
                'label' => esc_html__( 'Filter', 'elementskit' ),
                'tab' => Controls_Manager::TAB_STYLE,
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
                'size_units' => ['px'],
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

        $this->insert_pro_message();
    }

    /*
     * Optional Not used
     *
     */

    protected function clean_class($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return 'ekit_gallery__filter-' . strtolower( preg_replace('/[^A-Za-z0-9\-]/', '', $string)); // Removes special chars.
     }

     protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {
        $settings = $this->get_settings_for_display();
        extract($settings);

        $tilt_config = [
            'disableAxis'   => $ekit_gallery_tilt_axis,
            'easing'        => 'cubic-bezier(.03, .98, .52, .99)',
            'glare'         => $ekit_gallery_tilt_show_glare === 'yes' ? true : false,
            'transition'    => true,
        ];

        $this->add_render_attribute(
            'gallery_grid',
            [
                'class'             => 'ekit_gallery_grid',
                'data-tilt-config'  => wp_json_encode($tilt_config),
            ]
        );

        if ($ekit_gallery_tilt_show_glare === 'yes' && !empty($ekit_gallery_tilt_maxGlare['size'])) {
            $this->add_render_attribute('tilt_item_config', 'data-tilt-maxglare', $ekit_gallery_tilt_maxGlare['size']);
        }
        
        $masonry_config = [
            'itemSelector'  => '.ekit_gallery_grid_item',
        ];
        
        $this->add_render_attribute('gallery_grid', 'data-masonry-config', wp_json_encode($masonry_config));
    ?>
        <?php
            $gallery_grid_class = '';
            if (($ekit_gallery_style == 'grid')) {
                $gallery_grid_class = 'gallery_grid_style';
            }
        ?>
        <?php
        if(count($ekit_gallery_items) > 1 && $ekit_gallery_filter_label_show == 'yes'):
        ?>
            <div class="elemetskit_filter_wraper_outer <?php if($ekit_gallery_fill_nav_content != 'yes') : ?> elemetskit_filter_<?php echo esc_attr($ekit_gallery_nav_alignment); ?> <?php endif; ?>">
                <div class="filter-button-wraper" id="ekit_gallery__filter<?php echo esc_attr($this->get_id());?>">
                    <ul class="option-set elementskit-main-filter elementskit_filter_<?php echo esc_attr($ekit_gallery_filter_style_choose); ?> <?php if($ekit_gallery_nav_item_show_caret == 'yes'){echo esc_attr('elementskit_nav_caret'); } ?> <?php if($ekit_gallery_filter_show_divider == 'yes') {echo esc_attr('elementskit_divider_active');}?>">
                        <li><a href="#" data-option-value="*" class="selected">
                        <?php if($ekit_gallery_filter_style_choose == 'style_two') : ?><span class="elementskit_blur_shadow_panel"></span><?php endif;?>
                        <span class="elementskit_filter_nav_text"><?php echo esc_html(ucfirst($ekit_gallery_filter_all_label))?></span></a></li>
                    <?php foreach($ekit_gallery_items as $item): ?>
                        <?php if(($item['ekit_gallery_filter_label']) != '') : ?>
                        <li><a href="#" data-option-value=".<?php echo esc_attr($this->clean_class($item['ekit_gallery_filter_label'])); ?>">
                        <?php if($ekit_gallery_filter_style_choose == 'style_two') : ?><span class="elementskit_blur_shadow_panel"></span><?php endif;?>
                        <span class="elementskit_filter_nav_text"><?php echo esc_html(ucfirst($item['ekit_gallery_filter_label'])); ?></span></a></li>
                        <?php  endif; ?>
                    <?php  endforeach; ?>
                    </ul><!-- .elementskit-main-filter END -->
                </div><!-- .filter-button-wraper END -->
            </div>
        <?php endif; ?>
        <?php echo '<div class="ekit_gallery_grid_wraper"><div id="ekit_gallery_'.$this->get_id().'" '.$this->get_render_attribute_string( 'gallery_grid' ).'>'; ?>
        <?php
            if($ekit_gallery_ordering == 'random') {
                shuffle($ekit_gallery_items);
            }
        ?>
        <?php if(count($ekit_gallery_items) > 0):
				foreach($ekit_gallery_items as $item) : // ekit_gallery_items // ekit_gallery_filter_images
					if(($ekit_gallery_style == 'grid') || ($ekit_gallery_style == 'masonry')) :
						foreach( $item['ekit_gallery_filter_images'] as $img ):
							$metadata = \ElementsKit_Lite\Utils::img_meta($img['id']);
							$img_url = !empty($img['id']) ? wp_get_attachment_image_src($img['id'], $ekit_gallery_img_size_size) : '';
							$img_url = !empty($img_url[0]) ? $img_url[0] : '';
							?>
							<div class="ekit_gallery_grid_item <?php echo esc_attr($this->clean_class($item['ekit_gallery_filter_label']).' '.$gallery_grid_class); ?>">
								<div class="elementskit-single-portfolio-item <?php if($ekit_gallery_tilt_enable == 'yes') : ?> ekit-gallery-portfolio-tilt <?php endif; ?>"
								<?php if($ekit_gallery_tilt_enable == 'yes') : ?>
								data-tilt-scale="<?php echo esc_attr($ekit_gallery_tilt_scale['size']); ?>"
								data-tilt-maxtilt="<?php echo esc_attr($ekit_gallery_tilt_amount['size']); ?>"
								data-tilt-perspective="<?php echo esc_attr($ekit_gallery_tilt_perspective['size']); ?>"
								<?php echo $this->get_render_attribute_string('tilt_item_config'); ?>
								<?php endif; ?>
								>
									<div class="elementskit-portfolio-thumb"
									<?php if($ekit_gallery_style == 'grid') : ?>
									style="padding-bottom: calc(<?php echo esc_attr($ekit_gallery_image_aspect_ratio['size']); ?> * 100%)"
									<?php endif; ?>
									>
									<img class="elementskit-grid__img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo empty($metadata['alt']) ? 'gallery grid image' : esc_attr($metadata['alt']); ?>"/>
									</div>
									<div class="elementskit-hover-area">
										<div class="elementskit-hover-content ekit_vertical_alignment_<?php echo esc_attr($ekit_gallery_content_vertical_alignment); ?>">
											<?php
												if ($ekit_gallery_caption == 'yes') {
													echo \ElementsKit_Lite\Utils::kses(!empty($metadata['caption']) ? '<h3 class="elementskit-gallery-title">'.$metadata['caption'].'</h3>' : '');
												}
											?>
											<?php
												if ($ekit_gallery_description == 'yes') {
													echo \ElementsKit_Lite\Utils::kses(!empty($metadata['description'])? '<p class="elementskit-gallery-description">'.$metadata['description'].'</p>' : '');
												}
											?>
											<?php
												if ($ekit_gallery_filter_label_show == 'yes' ) {

													echo \ElementsKit_Lite\Utils::kses(!empty($item['ekit_gallery_filter_label']) ? '<span class="elementskit-gallery-label">'.$item['ekit_gallery_filter_label'].'</span>' : '');
												}
											?>
											<?php if(($ekit_gallery_open_lightbox == 'yes') && ($ekit_gallery_link_icons != '')): ?>
											<div class="elementskit-gallery-popup-icon-wraper">
												<a href="<?php echo esc_url( $img[ 'url' ] ); ?>" data-effect="mfp-zoom-in"
												class="elementskit-gallery-icon elementor-clickable" aria-label="mfp-zoom-in"
												<?php if($ekit_gallery_show_image_gallery == 'yes') : ?>
													data-elementor-lightbox-slideshow="<?php echo esc_attr($this->get_id()); ?>"
												<?php endif; ?>
													>

													<?php
														// new icon
														$migrated = isset( $settings['__fa4_migrated']['ekit_gallery_link_icons'] );
														// Check if its a new widget without previously selected icon using the old Icon control
														$is_new = empty( $settings['ekit_gallery_link_icon'] );
														if ( $is_new || $migrated ) {
															// new icon
															Icons_Manager::render_icon( $settings['ekit_gallery_link_icons'], [ 'aria-hidden' => 'true' ] );
														} else {
															?>
															<i class="<?php echo esc_attr($settings['ekit_gallery_link_icon']); ?>" aria-hidden="true"></i>
															<?php
														}
													?>

												</a>
											</div>
											<?php endif; ?>
										</div>
									</div><!-- .elementskit-hover-area END -->
									<?php if($ekit_gallery_show_image_overlay == 'yes') : ?>
									<div class="ekit-gallery-image-overlay"></div>
									<?php endif; ?>
								</div><!-- .elementskit-single-portfolio-item END -->
							</div>

						<?php endforeach;
					endif;
				endforeach;
			endif;
			echo '</div></div>';

		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			$this->render_editor_script();
		}
	}

    protected function render_editor_script() {
        ?>
        <script>
            (function ($) {
                'use strict';

                $(function () {
                    var $el = $('#ekit_gallery_<?php echo esc_attr($this->get_id()); ?>');

                    $el.imagesLoaded(function () {
                        $el.isotope();
                    });
                });
            }(jQuery));
        </script>
        <?php
    }
}
