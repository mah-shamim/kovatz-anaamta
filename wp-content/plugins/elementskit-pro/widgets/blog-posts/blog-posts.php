<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Blog_Posts_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Blog_Posts extends Widget_Base {
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
        return 'https://wpmet.com/doc/blog-posts-2/';
    }

    public function format_colname($str) {
        return str_replace('ekit', 'col', $str);
    }

    protected function register_controls() {

        // Layout
        $this->start_controls_section(
           'ekit_blog_posts_general',
           [
               'label' => esc_html__( 'Layout', 'elementskit' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_layout_style',
           [
               'label'     => esc_html__( 'Layout Style', 'elementskit' ),
               'type'      => Controls_Manager::SELECT,
               'options'   => [
                   'elementskit-blog-block-post' => esc_html__( 'Block', 'elementskit' ),
                   'elementskit-post-image-card' => esc_html__( 'Grid With Thumb', 'elementskit' ),
                   'elementskit-post-card'       => esc_html__( 'Grid Without Thumb', 'elementskit' ),
               ],
               'default'   => 'elementskit-blog-block-post',
           ]
       );

	   $this->add_control(
		'ekit_blog_posts_enable_carousel',
			[
				'label'     => esc_html__( 'Enable Carousel', 'elementskit' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'default'   => 'no',
			]
		);

       $this->add_control(
           'ekit_blog_posts_feature_img',
           [
               'label'     => esc_html__( 'Show Featured Image', 'elementskit' ),
               'type'      => Controls_Manager::SWITCHER,
               'label_on'  => esc_html__( 'Yes', 'elementskit' ),
               'label_off' => esc_html__( 'No', 'elementskit' ),
               'default'   => 'yes',
               'condition' => [
                   'ekit_blog_posts_layout_style!' => 'elementskit-post-card',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_layout_style_thumb',
           [
               'label'     => esc_html__( 'Image Position', 'elementskit' ),
               'type'      => Controls_Manager::SELECT,
               'options'   => [
                   'block' => esc_html__( 'Top', 'elementskit' ),
                   'flex' => esc_html__( 'Left', 'elementskit' ),
               ],
               'default'   => 'block',
               'selectors' => [
                    '{{WRAPPER}} .elementskit-post-image-card' => 'display: {{VALUE}}'
               ],
               'condition' => [
                    'ekit_blog_posts_layout_style' => 'elementskit-post-image-card',
                    'ekit_blog_posts_feature_img'  => 'yes',
               ],
           ]
       );
       
        /**
         * Control: Featured Image Size
         */
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'              => 'ekit_blog_posts_feature_img_size',
                'fields_options'    => [
                    'size'  => [
                        'label' => esc_html__( 'Featured Image Size', 'elementskit' ),
                    ],
                ],
                'exclude'           => [ 'custom' ],
                'default'           => 'large',
                'condition'         => [
                    'ekit_blog_posts_feature_img'   => 'yes',
                    'ekit_blog_posts_layout_style!' => 'elementskit-post-card',
                ],
            ]
        );

       $this->add_control(
           'ekit_blog_posts_feature_img_float',
           [
               'label'     => esc_html__( 'Featured Image Alignment', 'elementskit' ),
               'type'      => Controls_Manager::CHOOSE,
               'options'   => [
                   'left'  => [
                       'title' => esc_html__( 'Left', 'elementskit' ),
                       'icon'  => 'eicon-text-align-left',
                   ],
                   'right' => [
                       'title' => esc_html__( 'Right', 'elementskit' ),
                       'icon'  => 'eicon-text-align-right',
                   ],
               ],
               'condition' => [
                   'ekit_blog_posts_feature_img' => 'yes',
                   'ekit_blog_posts_layout_style' => 'elementskit-blog-block-post',
               ],
               'default'   => 'left',
           ]
       );
       $this->add_control(
           'ekit_blog_posts_column',
           [
               'label'     => esc_html__( 'Show Posts Per Row', 'elementskit' ),
               'type'      => Controls_Manager::SELECT,
               'options'   => [
                   'ekit-lg-12 ekit-md-12'   => esc_html__( '1', 'elementskit' ),
                   'ekit-lg-6 ekit-md-6'     => esc_html__( '2', 'elementskit' ),
                   'ekit-lg-4 ekit-md-6'     => esc_html__( '3', 'elementskit' ),
                   'ekit-lg-3 ekit-md-6'     => esc_html__( '4', 'elementskit' ),
                   'ekit-lg-2 ekit-md-6'     => esc_html__( '6', 'elementskit' ),
               ],
				'condition' => [
					'ekit_blog_posts_layout_style!' => 'elementskit-blog-block-post',
					'ekit_blog_posts_enable_carousel!' => 'yes'
				],
               'default'   => 'ekit-lg-4 ekit-md-6',
           ]
       );
       $this->add_control(
           'ekit_blog_posts_title',
           [
               'label'     => esc_html__( 'Show Title', 'elementskit' ),
               'type'      => Controls_Manager::SWITCHER,
               'label_on'  => esc_html__( 'Yes', 'elementskit' ),
               'label_off' => esc_html__( 'No', 'elementskit' ),
               'default'   => 'yes',
           ]
       );
       $this->add_control(
            'ekit_blog_posts_title_trim',
            [
                'label'     => esc_html__( 'Crop title by word', 'elementskit' ),
                'type'      => Controls_Manager::NUMBER,
                'default'   => '',
                'condition' => [
                    'ekit_blog_posts_title' => 'yes',
                ],
            ]
        );
       $this->add_control(
           'ekit_blog_posts_content',
           [
               'label'     => esc_html__( 'Show Content', 'elementskit' ),
               'type'      => Controls_Manager::SWITCHER,
               'label_on'  => esc_html__( 'Yes', 'elementskit' ),
               'label_off' => esc_html__( 'No', 'elementskit' ),
               'default'   => 'yes',
           ]
       );
       $this->add_control(
            'ekit_blog_posts_content_trim',
            [
                'label'     => esc_html__( 'Crop content by word', 'elementskit' ),
                'type'      => Controls_Manager::NUMBER,
                'default'   => '',
                'condition' => [
                    'ekit_blog_posts_content' => 'yes',
                ],
            ]
        );

       $this->add_control(
           'ekit_blog_posts_read_more',
           [
               'label'     => esc_html__( 'Show Read More', 'elementskit' ),
               'type'      => Controls_Manager::SWITCHER,
               'label_on'  => esc_html__( 'Yes', 'elementskit' ),
               'label_off' => esc_html__( 'No', 'elementskit' ),
               'default'   => 'yes',
               'condition' => ['ekit_blog_posts_layout_style!' => 'elementskit-blog-block-post'],
           ]
	   );
	   
			$this->add_control(
				'grid_masonry',
				[
					'label'	=> esc_html__( 'Enable Masonry', 'elementskit' ),
					'type'	=> Controls_Manager::SWITCHER,
					'render_type' => 'template',
					'condition' => [
						'ekit_blog_posts_layout_style!' => 'elementskit-blog-block-post',
						'ekit_blog_posts_enable_carousel!' => 'yes'
					],
				]
            );

            $this->add_control(
                'ekit_blog_posts_is_pagination',
                [
                    'label'     => esc_html__( 'Pagination', 'elementskit' ),
                    'type'      => Controls_Manager::SWITCHER,
					'render_type' => 'template',
                    'label_on'  => esc_html__( 'Yes', 'elementskit' ),
                    'label_off' => esc_html__( 'No', 'elementskit' ),
                    'default'   => 'no',
					'condition' => [
						'ekit_blog_posts_enable_carousel!' => 'yes'
					],
                ]
            );

            $this->add_control(
                'ekit_blog_posts_pagination_style',
                [
                    'label'   => esc_html__( 'Style', 'elementskit' ),
                    'type'    => Controls_Manager::SELECT,
                    'options' => [
                        'simple'        => esc_html__( 'Simple', 'elementskit' ),
                        'numbered'      => esc_html__( 'Numbered', 'elementskit' ),
                        'loadmore'     => esc_html__( 'Load More', 'elementskit' )
                    ],
                    'default' => 'simple',
                    'condition' => [
                        'ekit_blog_posts_is_pagination' => 'yes'
                    ]
                ]
            );

       $this->end_controls_section();
       // Query
       $this->start_controls_section(
           'ekit_blog_posts_content_section',
           [
               'label' => esc_html__( 'Query', 'elementskit' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_num',
           [
               'label'     => esc_html__( 'Posts Count', 'elementskit' ),
               'type'      => Controls_Manager::NUMBER,
               'min'       => 1,
               'max'       => 100,
               'default'   => 3,
           ]
       );

       $this->add_control(
        'ekit_blog_posts_is_manual_selection',
        [
            'label' => esc_html__( 'Select posts by:', 'elementskit' ),
            'type' => Controls_Manager::SELECT,
            'default' => '',
            'options' => [
                'recent'    => esc_html__( 'Recent Post', 'elementskit' ),
                'yes'       => esc_html__( 'Selected Post', 'elementskit' ),
                ''        => esc_html__( 'Category Post', 'elementskit' ),
            ],

        ]
    );

       $this->add_control(
           'ekit_blog_posts_manual_selection',
           [
               'label' =>esc_html__('Search & Select', 'elementskit'),
               'type'      => ElementsKit_Controls_Manager::AJAXSELECT2,
               'options'   =>'ajaxselect2/post_list',
               'label_block' => true,
               'multiple'  => true,
               'condition' => [ 'ekit_blog_posts_is_manual_selection' => 'yes' ]
           ]
       );
       $this->add_control(
           'ekit_blog_posts_cats',
           [
               'label' =>esc_html__('Select Categories', 'elementskit'),
               'type'      => ElementsKit_Controls_Manager::AJAXSELECT2,
               'options'   =>'ajaxselect2/category',
               'label_block' => true,
               'multiple'  => true,
               'condition' => [ 'ekit_blog_posts_is_manual_selection' => '' ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_offset',
           [
               'label'     => esc_html__( 'Offset', 'elementskit' ),
               'type'      => Controls_Manager::NUMBER,
               'min'       => 0,
               'max'       => 20,
               'default'   => 0,
               'condition' => [
                   'ekit_blog_posts_is_pagination!' => 'yes'
               ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_order_by',
           [
               'label'   => esc_html__( 'Order by', 'elementskit' ),
               'type'    => Controls_Manager::SELECT,
               'options' => [
                   'date'          => esc_html__( 'Date', 'elementskit' ),
                   'title'         => esc_html__( 'Title', 'elementskit' ),
                   'author'        => esc_html__( 'Author', 'elementskit' ),
                   'modified'      => esc_html__( 'Modified', 'elementskit' ),
                   'comment_count' => esc_html__( 'Comments', 'elementskit' ),
               ],
               'default' => 'date',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_sort',
           [
               'label'   => esc_html__( 'Order', 'elementskit' ),
               'type'    => Controls_Manager::SELECT,
               'options' => [
                   'ASC'  => esc_html__( 'ASC', 'elementskit' ),
                   'DESC' => esc_html__( 'DESC', 'elementskit' ),
               ],
               'default' => 'DESC',
           ]
       );

       $this->end_controls_section();

        // meta data
		$this->start_controls_section(
			'ekit_blog_posts_meta_data_tab',
			[
				'label' => esc_html__( 'Meta Data', 'elementskit' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
            'ekit_blog_posts_floating_date',
            [
                'label'     => esc_html__( 'Show Floating Date', 'elementskit' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'default'   => 'no',
                'condition' => [
                    'ekit_blog_posts_layout_style' => 'elementskit-post-image-card',
                ],
            ]
        );
        $this->add_control(
            'ekit_blog_posts_floating_date_style',
            [
                'label' => esc_html__('Choose Style', 'elementskit'),
                'type' => ElementsKit_Controls_Manager::IMAGECHOOSE,
                'default' => 'style1',
                'options' => [
                    'style1' => [
                        'title' => esc_html__( 'Image style 1', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/floating-date-1.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/floating-date-1.png',
                        'width' => '50%',
                    ],
                    'style2' => [
                        'title' => esc_html__( 'Image style 2', 'elementskit' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/floating-date-2.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/floating-date-2.png',
                        'width' => '50%',
                    ],
                ],
                'condition' => [
                    'ekit_blog_posts_floating_date' => 'yes',
                ],
            ]
        );

		$this->add_control(
            'ekit_blog_posts_floating_category',
            [
                'label'     => esc_html__( 'Show Floating Category', 'elementskit' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'default'   => 'no',
                'condition' => [
                    'ekit_blog_posts_layout_style' => 'elementskit-post-image-card',
                ],
            ]
        );

        $this->add_control(
            'ekit_blog_posts_meta',
            [
                'label'     => esc_html__( 'Show Meta Data', 'elementskit' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'default'   => 'yes',
            ]
        );
        $this->add_control(
         'ekit_blog_posts_title_position',
             [
                 'label' => esc_html__( 'Meta Position', 'elementskit' ),
                 'type'  => Controls_Manager::SELECT,
                 'options' => [
                    'after_meta'  => esc_html__( 'Before Title', 'elementskit' ),
                     'before_meta' => esc_html__( 'After Title', 'elementskit' ),
                     'after_content'  => esc_html__( 'After Content', 'elementskit' ),
                 ],
                 'default' => 'after_meta',
                 'condition' => [
                     'ekit_blog_posts_meta' => 'yes',
                 ]
             ]
         );
        $this->add_control(
            'ekit_blog_posts_meta_select',
            [
                'label'     => esc_html__( 'Meta Data', 'elementskit' ),
                'type'      => Controls_Manager::SELECT2,
                'options'   => [
                    'author'     => esc_html__( 'Author', 'elementskit' ),
                    'date'   => esc_html__( 'Date', 'elementskit' ),
                    'category'     => esc_html__( 'Category', 'elementskit' ),
                    'comment'     => esc_html__( 'Comment', 'elementskit' ),
                ],
                'multiple' => true,
                // 'default'   => [
                //     'author',
                //     'date'
                // ],
                'condition' => [
                    'ekit_blog_posts_meta' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'ekit_blog_posts_author_image',
            [
                'label'     => esc_html__( 'Show Author Image', 'elementskit' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'default'   => 'no',
                'condition' => [
                    'ekit_blog_posts_meta' => 'yes',
                    'ekit_blog_posts_meta_select'  => 'author'
                ],
            ]
        );
        $this->add_control(
            'ekit_blog_posts_meta_author_icons',
            [
                'label' => esc_html__( 'Author Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_blog_posts_meta_author_icon',
                'default' => [
                    'value' => 'icon icon-user',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_blog_posts_author_image!' => 'yes',
                    'ekit_blog_posts_meta' => 'yes',
                    'ekit_blog_posts_meta_select'   => 'author'
                ]
            ]
        );
        $this->add_control(
            'ekit_blog_posts_meta_date_icons',
            [
                'label' => esc_html__( 'Date Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_blog_posts_meta_date_icon',
                'default' => [
                    'value' => 'icon icon-calendar3',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_blog_posts_meta' => 'yes',
                    'ekit_blog_posts_meta_select'   => 'date'
                ],
            ]
        );
        $this->add_control(
            'ekit_blog_posts_meta_category_icons',
            [
                'label' => esc_html__( 'Category Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_blog_posts_meta_category_icon',
                'default' => [
                    'value' => 'icon icon-folder',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_blog_posts_meta' => 'yes',
                    'ekit_blog_posts_meta_select'   => 'category'
                ],
            ]
        );
        $this->add_control(
            'ekit_blog_posts_meta_comment_icons',
            [
                'label' => esc_html__( 'Comment Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_blog_posts_meta_comment_icon',
                'default' => [
                    'value' => 'icon icon-comment',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_blog_posts_meta' => 'yes',
                    'ekit_blog_posts_meta_select'   => 'comment'
                ],
            ]
        );
        

		$this->end_controls_section();

       // Read More Button
       $this->start_controls_section(
           'ekit_blog_posts_more_section',
           [
               'label' => esc_html__( 'Read More Button', 'elementskit' ),
               'condition' => ['ekit_blog_posts_read_more' => 'yes', 'ekit_blog_posts_layout_style!' => 'elementskit-blog-block-post'],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_text',
           [
               'label' =>esc_html__( 'Label', 'elementskit' ),
               'type' => Controls_Manager::TEXT,
               'default' =>esc_html__( 'Learn more ', 'elementskit' ),
               'placeholder' =>esc_html__( 'Learn more ', 'elementskit' ),
           ]
       );

       $this->add_control(
        'ekit_blog_posts_btn_icons__switch',
        [
            'label' => esc_html__('Add icon? ', 'elementskit'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' =>esc_html__( 'Yes', 'elementskit' ),
            'label_off' =>esc_html__( 'No', 'elementskit' ),
        ]
    );

       $this->add_control(
           'ekit_blog_posts_btn_icons',
           [
               'label' =>esc_html__( 'Icon', 'elementskit' ),
               'type' => Controls_Manager::ICONS,
               'fa4compatibility' => 'ekit_blog_posts_btn_icon',
                'default' => [
                    'value' => '',
                ],
               'label_block' => true,
               'condition'  => [
                   'ekit_blog_posts_btn_icons__switch'  => 'yes'
               ]
           ]
       );
       $this->add_control(
        'ekit_blog_posts_btn_icon_align',
        [
            'label' =>esc_html__( 'Icon Position', 'elementskit' ),
            'type' => Controls_Manager::SELECT,
            'default' => 'left',
            'options' => [
                'left' =>esc_html__( 'Before', 'elementskit' ),
                'right' =>esc_html__( 'After', 'elementskit' ),
            ],
            'condition'  => [
                 'ekit_blog_posts_btn_icons__switch'  => 'yes'
             ]
        ]
    );
       $this->add_responsive_control(
           'ekit_blog_posts_btn_align',
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
               'selectors'=> [
                    '{{WRAPPER}} .btn-wraper' => 'text-align: {{VALUE}};',
                ],
               'default' => 'left',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_class',
           [
               'label' => esc_html__( 'Class', 'elementskit' ),
               'type' => Controls_Manager::TEXT,
               'placeholder' => esc_html__( 'Class Name', 'elementskit' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_id',
           [
               'label' => esc_html__( 'id', 'elementskit' ),
               'type' => Controls_Manager::TEXT,
               'placeholder' => esc_html__( 'ID', 'elementskit' ),
           ]
       );
       
       $this->end_controls_section();

		// Blog Post Carousel
	   $this->start_controls_section(
			'ekit_blog_posts_carousel_section',
			[
				'label' => esc_html__( 'Carousel Settings', 'elementskit' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => ['ekit_blog_posts_enable_carousel' => 'yes'],
			]
		);

		$this->add_responsive_control(
			'ekit_blog_posts_spacing',
			[
				'label' => esc_html__( 'Space Between (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .blogCarousel.swiper-container' => '--ekit_blog_posts_spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_blog_posts_slide_show',
			[
				'label' => esc_html__( 'Slides To Show', 'elementskit' ),
				'type' =>  Controls_Manager::SLIDER,
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
					'{{WRAPPER}} .blogCarousel.swiper-container' => '--ekit_blog_posts_slide_show:  {{SIZE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_blog_posts_slides_scroll',
			[
				'label' => esc_html__( 'Slides To Scroll', 'elementskit' ),
				'type' =>  Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
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
			]
		);

		$this->add_control(
			'ekit_blog_post_speed',
			[
				'label' => esc_html__( 'Speed (ms)', 'elementskit' ),
				'type' =>  Controls_Manager::NUMBER,
				'min' => 500,
				'max' => 15000,
				'step' => 100,
				'default' => 600,
			]
		);

		$this->add_control(
			'ekit_blog_post_autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'elementskit' ),
				'type' =>  Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		
		$this->add_control(
			'ekit_blog_post_autoplay_delay',
			[
				'label' => esc_html__( 'Delay (ms)', 'elementskit' ),
				'type' =>  Controls_Manager::NUMBER,
				'min' => 500,
				'max' => 15000,
				'step' => 100,
				'default' => 3000,
				'condition' => [
					'ekit_blog_post_autoplay' => 'yes',
				]
			]
		);

		$this->add_control(
			'ekit_blog_post_pause_on_hover',
			[
				'label' => esc_html__( 'Pause On Hover', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'ekit_blog_post_autoplay' => 'yes',
				]
			]
		);

		$this->add_control(
			'ekit_blog_post_show_pagination',
			[
				'label' => esc_html__( 'Show Pagination', 'elementskit' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'ekit_blog_post_show_arrow',
			[
				'label' => esc_html__( 'Show Arrow', 'elementskit' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'ekit_blog_post_left_arrow_icon',
			[
				'label' => esc_html__( 'Left Arrow Icon', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'icon icon-left-arrows',
					'library' => 'ekiticons',
				],
				'condition' => [
					'ekit_blog_post_show_arrow' => 'yes',
				]
			]
		);

		$this->add_control(
			'ekit_blog_post_right_arrow_icon',
			[
				'label' => esc_html__( 'Right Arrow Icon', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'icon icon-right-arrow1',
					'library' => 'ekiticons',
				],
				'condition' => [
					'ekit_blog_post_show_arrow' => 'yes',
				]
			]
		);

       $this->end_controls_section();

       // Post Styles
       $this->start_controls_section(
           'ekit_blog_posts_style',
           [
               'label' => esc_html__( 'Wrapper', 'elementskit' ),
               'tab'   => Controls_Manager::TAB_STYLE,
           ]
       );

        $this->start_controls_tabs(
            'ekit_blog_posts_tabs'
        );

        $this->start_controls_tab(
            'ekit_blog_posts_tab_normal',
            [
                'label' =>esc_html__( 'Normal', 'elementskit' ),
            ]
        );

       $this->add_group_control(
           Group_Control_Background::get_type(),
           [
               'name'     => 'ekit_blog_posts_background',
               'label'    => esc_html__( 'Background', 'elementskit' ),
               'types'    => [ 'classic', 'gradient' ],
               'selector' => '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card',
           ]
       );

       $this->add_group_control(
           Group_Control_Box_Shadow::get_type(), [
               'name'     => 'ekit_blog_posts_shadow',
               'selector' => '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card',
           ]
       );
       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_tab_hover',
           [
               'label' =>esc_html__( 'Hover', 'elementskit' ),
           ]
       );
       $this->add_group_control(
          Group_Control_Background::get_type(),
          [
              'name'     => 'ekit_blog_posts_background_hover',
              'label'    => esc_html__( 'Background', 'elementskit' ),
              'types'    => [ 'classic', 'gradient' ],
              'selector' => '{{WRAPPER}} .elementskit-blog-block-post:hover, {{WRAPPER}} .elementskit-post-image-card:hover, {{WRAPPER}} .elementskit-post-card:hover',
              'fields_options'  => [
                  'background' => [
                    'prefix_class' => 'ekit-blog-posts--bg-hover bg-hover-',
                  ],
              ],
          ]
       );

      $this->add_group_control(
          Group_Control_Box_Shadow::get_type(), [
              'name'     => 'ekit_blog_posts_shadow_hover',
              'selector' => '{{WRAPPER}} .elementskit-blog-block-post:hover, {{WRAPPER}} .elementskit-post-image-card:hover, {{WRAPPER}} .elementskit-post-card:hover',
          ]
      );
       $this->end_controls_tab();
       $this->end_controls_tabs();

        $this->add_control(
            'ekit_blog_posts_hr',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

       $this->add_control(
           'ekit_blog_posts_vertical_alignment',
           [
               'label'   => esc_html__( 'Vertical Alignment', 'elementskit' ),
               'type'    => Controls_Manager::CHOOSE,
               'options' => [
                   'flex-start'  => [
                       'title' => esc_html__( 'Top', 'elementskit' ),
                       'icon'  => 'eicon-v-align-top',
                   ],
                   'center' => [
                       'title' => esc_html__( 'Middle', 'elementskit' ),
                       'icon'  => 'eicon-v-align-middle',
                   ],
                   'flex-end'    => [
                       'title' => esc_html__( 'Bottom', 'elementskit' ),
                       'icon'  => 'eicon-v-align-bottom',
                   ],
               ],
               'condition' => [
                   'ekit_blog_posts_layout_style' => 'elementskit-blog-block-post',
               ],
               'default'   => 'flex-start',
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-blog-block-post > .row'    => 'align-items: {{VALUE}};',
               ],
           ]
       );

       $this->add_responsive_control(
        'ekit_blog_posts_radius',
        [
            'label'      => esc_html__( 'Container Border radius', 'elementskit' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors'  => [
                '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'ekit_blog_posts_padding',
        [
            'label'      => esc_html__( 'Container Padding', 'elementskit' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors'  => [
                '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'ekit_blog_posts_margin',
        [
            'label'      => esc_html__( 'Container Margin', 'elementskit' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'tablet_default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '30',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => 'false',
            ],
            'mobile_default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '30',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => 'false',
            ],
            'selectors'  => [
                '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'ekit_blog_posts_text_content_wraper_padding',
        [
            'label' => esc_html__( 'Content Padding', 'elementskit' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}} .elementskit-blog-block-post .elementskit-post-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .elementskit-post-image-card .elementskit-post-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

        $this->add_control(
            'ekit_blog_posts_container_border_title',
            [
                'label' => esc_html__( 'Container Border', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

       $this->add_group_control(
           Group_Control_Border::get_type(),
           [
               'name'     => 'ekit_blog_posts_border',
               'label'    => esc_html__( 'Container Border', 'elementskit' ),
               'selector' => '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card',
           ]
       );

        $this->add_control(
            'ekit_blog_posts_content_background',
            [
                'label' => esc_html__( 'Container Background Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-post-body' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_blog_posts_content_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .elementskit-post-body',
			]
        );
        
       $this->add_control(
           'ekit_blog_posts_content_border_title',
           [
               'label' => esc_html__( 'Content Border', 'elementskit' ),
               'type' => Controls_Manager::HEADING,
               'separator' => 'before',
               'condition' => [
                   'ekit_blog_posts_layout_style' =>  'elementskit-post-image-card',
                   'ekit_blog_posts_feature_img' => 'yes'
               ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_border_style',
           [
               'label' => esc_html_x( 'Border Type', 'Border Control', 'elementskit' ),
               'type' => Controls_Manager::SELECT,
               'options' => [
                   '' => esc_html__( 'None', 'elementskit' ),
                   'solid' => esc_html_x( 'Solid', 'Border Control', 'elementskit' ),
                   'double' => esc_html_x( 'Double', 'Border Control', 'elementskit' ),
                   'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementskit' ),
                   'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementskit' ),
                   'groove' => esc_html_x( 'Groove', 'Border Control', 'elementskit' ),
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body' => 'border-style: {{VALUE}};',
               ],
               'condition' => [
                   'ekit_blog_posts_layout_style' =>  'elementskit-post-image-card',
                   'ekit_blog_posts_feature_img' => 'yes'
               ]
           ]
       );
       $this->add_control(
           'ekit_blog_posts_content_border_dimensions',
           [
               'label' => esc_html_x( 'Width', 'Border Control', 'elementskit' ),
               'type' => Controls_Manager::DIMENSIONS,
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_layout_style' =>  'elementskit-post-image-card',
                   'ekit_blog_posts_feature_img' => 'yes'
               ]
           ]
       );
       $this->start_controls_tabs( 'ekit_blog_posts_content_border_tabs', [
           'condition' => [
               'ekit_blog_posts_layout_style' =>  'elementskit-post-image-card',
               'ekit_blog_posts_feature_img' => 'yes'
           ]
       ] );
       $this->start_controls_tab(
           'ekit_blog_posts_content_border_normal',
           [
               'label' =>esc_html__( 'Normal', 'elementskit' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_border_color_normal',
           [
               'label' => esc_html_x( 'Color', 'Border Control', 'elementskit' ),
               'type' => Controls_Manager::COLOR,
               'default' => '',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body' => 'border-color: {{VALUE}};',
               ],
           ]
       );
       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_content_border_color_hover_style',
           [
               'label' =>esc_html__( 'Hover', 'elementskit' ),
           ]
       );
       $this->add_control(
           'ekit_blog_posts_content_border_color_hover',
           [
               'label' => esc_html_x( 'Color', 'Border Control', 'elementskit' ),
               'type' => Controls_Manager::COLOR,
               'default' => '',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-image-card:hover .elementskit-post-body ' => 'border-color: {{VALUE}};',
               ],
           ]
       );
       $this->end_controls_tab();
       $this->end_controls_tabs();

       $this->end_controls_section();


       // Featured Image Styles
       $this->start_controls_section(
           'ekit_blog_posts_feature_img_style',
           [
               'label'     => esc_html__( 'Featured Image', 'elementskit' ),
               'tab'       => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_layout_style!' => 'elementskit-post-card',
                   'ekit_blog_posts_feature_img'    => 'yes'
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_feature_img_size',
           [
               'label' => esc_html__( 'Image Width', 'elementskit' ),
               'type' => Controls_Manager::SLIDER,
               'range' => [
                   'px' => [
                       'min' => 1,
                       'max' => 500,
                   ],
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-entry-thumb' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}',
               ],
               'condition' => [
                    'ekit_blog_posts_layout_style' => 'elementskit-post-image-card',
                    'ekit_blog_posts_layout_style_thumb' => 'flex',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Box_Shadow::get_type(), [
               'name'      => 'ekit_blog_posts_feature_img_shadow',
               'selector'  => '{{WRAPPER}} .elementskit-entry-thumb',
           ]
       );

       $this->add_group_control(
           Group_Control_Border::get_type(),
           [
               'name'      => 'ekit_blog_posts_feature_img_border',
               'label'     => esc_html__( 'Border', 'elementskit' ),
               'selector'  => '{{WRAPPER}} .elementskit-entry-thumb',
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_feature_img_radius',
           [
               'label'     => esc_html__( 'Border radius', 'elementskit' ),
               'type'      => Controls_Manager::DIMENSIONS,
               'size_units'=> [ 'px', '%', 'em' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-entry-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_feature_img_margin',
           [
               'label'      => esc_html__( 'Margin', 'elementskit' ),
               'type'       => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', '%', 'em' ],
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-entry-thumb' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_feature_img_padding',
           [
               'label'      => esc_html__( 'Padding', 'elementskit' ),
               'type'       => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', '%', 'em' ],
               'selectors'  => [
                   ' {{WRAPPER}} .ekit-wid-con .elementskit-entry-thumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

        /** Image Overlay Style */
        $this->start_controls_tabs(
            'ekit_blog_posts_image_overlay_normal_and_hover_tab',
            [
                'separator' => 'before'
            ]
        );

        $this->start_controls_tab(
            'ekit_blog_posts_image_ovarlay_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_blog_posts_image_overlay_normal',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => ['classic', 'gradient'],
                'exclude'   => ['image'],
                'fields_options'  => [
                    'background' => [
                        'label' => esc_html__( 'Overlay Background', 'elementskit' ),
                    ]
                ],
                'selector' => '{{WRAPPER}} .elementskit-post-image-card > .elementskit-entry-header > a.elementskit-entry-thumb::after, {{WRAPPER}} .elementskit-blog-block-post > .no-gutters a.elementskit-entry-thumb::after'
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_blog_posts_image_overlay_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_blog_posts_image_overlay_hover',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient', ],
                'exclude'   => ['image'],
                'fields_options'  => [
                    'background' => [
                        'label' => esc_html__( 'Overlay Background', 'elementskit' ),
                    ]
                ],
                'selector' => '{{WRAPPER}} .elementskit-post-image-card:hover > .elementskit-entry-header > a.elementskit-entry-thumb::before, {{WRAPPER}} .elementskit-blog-block-post:hover > .no-gutters a.elementskit-entry-thumb::before'
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs(); 

       $this->end_controls_section();

       // Meta Styles
       $this->start_controls_section(
           'ekit_blog_posts_meta_style',
           [
               'label'     => esc_html__( 'Meta', 'elementskit' ),
               'tab'       => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_meta' => 'yes',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Typography::get_type(), [
               'name'       => 'ekit_blog_posts_meta_typography',
               'selector'   => '{{WRAPPER}} .post-meta-list a, {{WRAPPER}} .post-meta-list .meta-date-text',
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_meta_alignment',
           [
               'label'    => esc_html__( 'Alignment', 'elementskit' ),
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
               'default'  => 'left',
               'selectors'=> [
                   '{{WRAPPER}} .post-meta-list' => 'text-align: {{VALUE}};',
               ],
           ]
       );

       $this->add_responsive_control(
            'ekit_blog_posts_meta_margin',
            [
                'label'     => esc_html__( 'Container Margin', 'elementskit' ),
                'type'      => Controls_Manager::DIMENSIONS,
                'size_units'=> [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

       $this->add_responsive_control(
           'ekit_blog_posts_meta_item_margin',
           [
               'label'     => esc_html__( 'Item Margin', 'elementskit' ),
               'type'      => Controls_Manager::DIMENSIONS,
               'size_units'=> [ 'px', '%', 'em' ],
               'selectors' => [
                   '{{WRAPPER}} .post-meta-list > span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_control(
            'ekit_blog_posts_meta_padding',
            [
                'label' => esc_html__( 'Item Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

       $this->add_control(
            'ekit_blog_posts_meta_icon_padding',
            [
                'label' => esc_html__( 'Icon Spacing', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list > span > i, {{WRAPPER}} .post-meta-list > span > svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_blog_posts_meta_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list > span > i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .post-meta-list > span > svg'  => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'ekit_blog_posts_meta_background_normal_and_hover_tab'
        );
        $this->start_controls_tab(
            'ekit_blog_posts_meta_background_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
		);

        $this->add_control(
            'ekit_blog_posts_meta_color_normal',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .post-meta-list > span' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .post-meta-list > span > svg path' => 'strock: {{VALUE}}; fill: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'ekit_blog_posts_meta_color_icon_normal',
            [
                'label'      => esc_html__( 'Icon Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .post-meta-list > span > i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .post-meta-list > span > svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};'
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_background_normal',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient', ],
                'selector' => '{{WRAPPER}} .post-meta-list > span',
                'exclude' => [
                    'image'
                ]
			]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_border_normal',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .post-meta-list > span',
			]
		);

        $this->add_control(
            'ekit_blog_posts_meta_border_radius_normal',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_box_shadow_normal',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .post-meta-list > span',
			]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(), [
                'name'       => 'ekit_blog_posts_meta_shadow_normal',
                'selector'   => '{{WRAPPER}} .post-meta-list > span',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_blog_posts_meta_background_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_blog_posts_meta_color_hover',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .post-meta-list > span:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .post-meta-list > span:hover > svg path' => 'strock: {{VALUE}}; fill: {{VALUE}};',

                    '{{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span' => 'color: {{VALUE}};',
                    '{{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span > svg path' => 'strock: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_blog_posts_meta_color_icon_hover',
            [
                'label'      => esc_html__( 'Icon Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .post-meta-list > span:hover > i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .post-meta-list > span:hover > svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',

                    '{{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span:hover > i' => 'color: {{VALUE}};',
                    '{{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span > svg path' => 'strock: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_background_hover',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient', ],
                'selector' => '{{WRAPPER}} .post-meta-list > span:hover, {{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span',
                'exclude' => [
                    'image'
                ]
			]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_border_hover',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .post-meta-list > span:hover, {{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span',
			]
		);

        $this->add_control(
            'ekit_blog_posts_meta_border_radius_hover',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list > span:hover, {{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_box_shadow_hover',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .post-meta-list > span:hover, {{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span',
			]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(), [
                'name'       => 'ekit_blog_posts_meta_shadow_hover',
                'selector'   => '{{WRAPPER}} .post-meta-list > span:hover, {{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span',
            ]
        );

		$this->end_controls_tab();

		$this->end_controls_tabs();

       $this->end_controls_section();

       // Floating Date Styles
       $this->start_controls_section(
           'ekit_blog_posts_floating_date_style_area',
           [
               'label'     => esc_html__( 'Floating Date', 'elementskit' ),
               'tab'       => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_floating_date' => 'yes',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_height', [
               'label'			 =>esc_html__( 'Height', 'elementskit' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '',
               ],
               'range'			 => [
                   'px' => [
                       'min'	 => -30,
                       'step'	 => 1,
                   ],
               ],
               'size_units'	 => ['px'],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta'	=> 'height: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style1',
               ],

           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_width', [
               'label'			 =>esc_html__( 'Width', 'elementskit' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '',
               ],
               'range'			 => [
                   'px' => [
                       'min'	 => -30,
                       'step'	 => 1,
                   ],
               ],
               'size_units'	 => ['px'],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta'	=> 'width: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style1',
               ],
           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_left_pos', [
               'label'			 =>esc_html__( 'Left', 'elementskit' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '',
               ],
               'size_units' => [ 'px', '%' ],
               'range'		 => [
                   'px' => [
                       'min'	 => -100,
                       'max'	 => 1000,
                       'step'	 => 1,
                   ],
                   '%'	 => [
                       'min'	 => 0,
                       'max'	 => 100,
                       'step'	 => 1,
                   ],
               ],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists'	=> 'left: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style1',
               ],
           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_top_pos', [
               'label'			 =>esc_html__( 'Top', 'elementskit' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '',
               ],
               'size_units' => [ 'px', '%' ],
               'range'		 => [
                   'px' => [
                       'min'	 => -100,
                       'max'	 => 1000,
                       'step'	 => 1,
                   ],
                   '%'	 => [
                       'min'	 => 0,
                       'max'	 => 100,
                       'step'	 => 1,
                   ],
               ],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists'	=> 'top: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style1',
               ],
           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_bottom_pos', [
               'label'			 =>esc_html__( 'Bottom', 'elementskit' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '',
               ],
               'size_units' => [ 'px', '%' ],
               'range'		 => [
                   'px' => [
                       'min'	 => 0,
                       'max'	 => 1000,
                       'step'	 => 1,
                   ],
                   '%'	 => [
                       'min'	 => 0,
                       'max'	 => 100,
                       'step'	 => 1,
                   ],
               ],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag'	=> 'bottom: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_style2_left_pos', [
               'label'			 =>esc_html__( 'Left', 'elementskit' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '-10',
                   'unit' => 'px'
               ],
               'size_units' => [ 'px', '%' ],
               'range'		 => [
                   'px' => [
                       'min'	 => -10,
                       'max'	 => 100,
                       'step'	 => 1,
                   ],
                   '%'	 => [
                       'min'	 => -10,
                       'max'	 => 100,
                       'step'	 => 1,
                   ],
               ],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag'	=> 'left: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_heading',
           [
               'label' => esc_html__( 'Date Typography', 'elementskit' ),
               'type' => Controls_Manager::HEADING,
               'separator' => 'before',
           ]
       );
       $this->add_group_control(
           Group_Control_Typography::get_type(), [
               'name'       => 'ekit_blog_posts_floating_date_typography_group',
               'selector'   => '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta .elementskit-meta-wraper strong',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_floating_date_color',
           [
               'label'      => esc_html__( 'Color', 'elementskit' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta .elementskit-meta-wraper strong' => 'color: {{VALUE}};'
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_month_heading',
           [
               'label' => esc_html__( 'Month Typography', 'elementskit' ),
               'type' => Controls_Manager::HEADING,
               'separator' => 'before',
           ]
       );
       $this->add_group_control(
           Group_Control_Typography::get_type(), [
               'name'       => 'ekit_blog_posts_floating_date_month_typography_group',
               'selector'   => '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_floating_date_month_color',
           [
               'label'      => esc_html__( 'Color', 'elementskit' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta .elementskit-meta-wraper' => 'color: {{VALUE}};'
               ],
           ]
       );
       $this->add_group_control(
           Group_Control_Background::get_type(),
           array(
               'name'     => 'ekit_blog_posts_floating_date_bg_color_group',
               'selector' => '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta',
               'separator' => 'before',
           )
       );

       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_padding',
           [
               'label' =>esc_html__( 'Padding', 'elementskit' ),
               'type' => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', 'em', '%' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag > .elementskit-single-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_group_control(
           Group_Control_Border::get_type(),
           [
               'name'     => 'ekit_blog_posts_floating_date_border_group',
               'label'    => esc_html__( 'Border', 'elementskit' ),
               'selector' => '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta',
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style1',
               ],
           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_border_radius',
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
                   '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style',
               ],
           ]
       );
       $this->add_group_control(
           Group_Control_Box_Shadow::get_type(), [
               'name'      => 'ekit_blog_posts_floating_date_shadow_group',
               'selector'  => '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta',
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_title',
           [
               'label' => esc_html__( 'Triangle', 'elementskit' ),
               'type' => Controls_Manager::HEADING,
               'separator' => 'before',
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_color',
           [
               'label' => esc_html__( 'Triangle Background', 'elementskit' ),
               'type' => Controls_Manager::COLOR,
               'selectors' => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag > .elementskit-single-meta::before' => 'color: {{VALUE}}',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_size',
           [
               'label' => esc_html__( 'Triangle Size', 'elementskit' ),
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
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
                   'size' => 5,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag > .elementskit-single-meta::before' => 'border-width: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_position_left',
           [
               'label' => esc_html__( 'Triangle Position Left', 'elementskit' ),
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 'px', '%' ],
               'range' => [
                   'px' => [
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
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag > .elementskit-single-meta::before' => 'left: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_position_top',
           [
               'label' => esc_html__( 'Triangle Position Top', 'elementskit' ),
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 'px', '%' ],
               'range' => [
                   'px' => [
                       'min' => -100,
                       'max' => 100,
                       'step' => 1,
                   ],
               ],
               'default' => [
                   'unit' => 'px',
                   'size' => -10,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag > .elementskit-single-meta::before' => 'top: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_position_alignment',
           [
               'label' => esc_html__( 'Triangle Direction', 'elementskit' ),
               'type' =>   Controls_Manager::CHOOSE,
               'options' => [
                   'triangle_left' => [
                       'title' => esc_html__( 'From Left', 'elementskit' ),
                       'icon' => 'fa fa-caret-right',
                   ],
                   'triangle_right' => [
                       'title' => esc_html__( 'From Right', 'elementskit' ),
                       'icon' => 'fa fa-caret-left',
                   ],
               ],
               'default' => 'triangle_left',
               'toggle' => true,
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_hr',
           [
               'type' => Controls_Manager::DIVIDER,
               'style' => 'thick',
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->end_controls_section();

       // Floating Category Styles
        $this->start_controls_section(
            'ekit_blog_posts_floating_category_style',
            [
                'label'     => esc_html__( 'Floating Category', 'elementskit' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_blog_posts_floating_category' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_blog_posts_floating_category_top_pos', [
                'label'			 =>esc_html__( 'Top', 'elementskit' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                ],
                'size_units' => [ 'px', '%' ],
                'range'		 => [
                    'px' => [
                        'min'	 => -100,
                        'max'	 => 1000,
                        'step'	 => 1,
                    ],
                    '%'	 => [
                        'min'	 => 0,
                        'max'	 => 100,
                        'step'	 => 1,
                    ],
                ],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-meta-categories'	=> 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_blog_posts_floating_category_left_pos', [
                'label'			 =>esc_html__( 'Left', 'elementskit' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                ],
                'size_units' => [ 'px', '%' ],
                'range'		 => [
                    'px' => [
                        'min'	 => -100,
                        'max'	 => 1000,
                        'step'	 => 1,
                    ],
                    '%'	 => [
                        'min'	 => 0,
                        'max'	 => 100,
                        'step'	 => 1,
                    ],
                ],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-meta-categories'	=> 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_blog_posts_floating_category_typography',
                'selector'   => '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span a',
            ]
        );
 
        $this->add_control(
            'ekit_blog_posts_floating_category_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span a' => 'color: {{VALUE}};'
                ],
            ]
        );
 
        $this->add_control(
            'ekit_blog_posts_floating_category_bg_color',
            [
                'label'      => esc_html__( 'Background Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span' => 'background-color: {{VALUE}};'
                ],
            ]
        );

        
        $this->add_responsive_control(
            'ekit_blog_posts_floating_category_padding',
            [
                'label'      => esc_html__( 'Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default'    => [
                    'unit'      => 'px',
                    'top'       => '4',
                    'right'     => '8',
                    'bottom'    => '4',
                    'left'      => '8',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_blog_posts_floating_category_padding_radius',
            [
                'label'     => esc_html__( 'Border Radius', 'elementskit' ),
                'type'      => Controls_Manager::DIMENSIONS,
                'size_units'=> [ 'px', '%', 'em' ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'ekit_blog_posts_floating_category_margin_right', [
                'label'			 =>esc_html__( 'Space Between Categories', 'elementskit' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                ],
                'size_units' => [ 'px', '%' ],
                'range'		 => [
                    'px' => [
                        'min'	 => -100,
                        'max'	 => 1000,
                        'step'	 => 1,
                    ],
                    '%'	 => [
                        'min'	 => 0,
                        'max'	 => 100,
                        'step'	 => 1,
                    ],
                ],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span:not(:last-child)'	=> 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();

       // Title Styles
       $this->start_controls_section(
           'ekit_blog_posts_title_style',
           [
               'label'     => esc_html__( 'Title', 'elementskit' ),
               'tab'       => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_title' => 'yes',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Typography::get_type(), [
               'name'       => 'ekit_blog_posts_title_typography',
               'selector'   => '{{WRAPPER}} .elementskit-post-body .entry-title, {{WRAPPER}} .elementskit-entry-header .entry-title, {{WRAPPER}} .elementskit-post-image-card .elementskit-post-body .entry-title  a,  {{WRAPPER}} .elementskit-post-card .elementskit-entry-header .entry-title  a,{{WRAPPER}} .elementskit-blog-block-post .elementskit-post-body .entry-title a',
           ]
       );

       $this->start_controls_tabs(
           'ekit_blog_posts_title_tabs'
       );

       $this->start_controls_tab(
           'ekit_blog_posts_title_normal',
           [
               'label' => esc_html__( 'Normal', 'elementskit' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_color',
           [
               'label'      => esc_html__( 'Color', 'elementskit' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-post-body .entry-title a' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-entry-header .entry-title a' => 'color: {{VALUE}};'
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Text_Shadow::get_type(), [
               'name'       => 'ekit_blog_posts_title_shadow',
               'selector'   => '{{WRAPPER}} .elementskit-post-body .entry-title a, {{WRAPPER}} .elementskit-entry-header .entry-title a',
           ]
       );

       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_title_hover',
           [
               'label' => esc_html__( 'Hover', 'elementskit' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_hover_color',
           [
               'label'      => esc_html__( 'Color', 'elementskit' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-post-body .entry-title a:hover' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-entry-header .entry-title a:hover' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-card:hover .entry-title a' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-image-card:hover .entry-title a' => 'color: {{VALUE}};'
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Text_Shadow::get_type(), [
               'name'       => 'ekit_blog_posts_title_hover_shadow',
               'selector'   => '{{WRAPPER}} .elementskit-post-body .entry-title a:hover, {{WRAPPER}} .elementskit-entry-header .entry-title a:hover',
           ]
       );

       $this->end_controls_tab();

       $this->end_controls_tabs();

       $this->add_control(
           'ekit_blog_posts_title_hover_shadow_hr',
           [
               'type' => Controls_Manager::DIVIDER,
               'style' => 'thick',
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_title_alignment',
           [
               'label'   => esc_html__( 'Alignment', 'elementskit' ),
               'type'    => Controls_Manager::CHOOSE,
               'options' => [
                   'left'   => [
                       'title' => esc_html__( 'Left', 'elementskit' ),
                       'icon'  => 'eicon-text-align-left',
                   ],
                   'center'  => [
                       'title' => esc_html__( 'Center', 'elementskit' ),
                       'icon'  => 'eicon-text-align-center',
                   ],
                   'right'   => [
                       'title' => esc_html__( 'Right', 'elementskit' ),
                       'icon'  => 'eicon-text-align-right',
                   ],
                   'justify' => [
                       'title' => esc_html__( 'justify', 'elementskit' ),
                       'icon'  => 'eicon-text-align-justify',
                   ],
               ],
               'default'   => 'left',
               'devices'   => [ 'desktop', 'tablet', 'mobile' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body .entry-title' => 'text-align: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-entry-header .entry-title' => 'text-align: {{VALUE}};',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_title_margin',
           [
               'label'      => esc_html__( 'Margin', 'elementskit' ),
               'type'       => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', '%', 'em' ],
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-post-body .entry-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                   '{{WRAPPER}} .elementskit-entry-header .entry-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator_hr',
           [
               'type' => Controls_Manager::DIVIDER,
               'style' => 'thick',
               'condition' => [
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator',
           [
               'label'     => esc_html__( 'Show Separator', 'elementskit' ),
               'type'      => Controls_Manager::SWITCHER,
               'label_on'  => esc_html__( 'Yes', 'elementskit' ),
               'label_off' => esc_html__( 'No', 'elementskit' ),
               'default'   => 'yes',
               'condition' => [
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator_color',
           [
               'label'      => esc_html__( 'Separator Color', 'elementskit' ),
               'type'       => Controls_Manager::COLOR,
               'condition' => [
                   'ekit_blog_posts_title_separator' => 'yes',
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-border-hr' => 'background-color: {{VALUE}};',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator_width',
           [
               'label' => esc_html__( 'Width', 'elementskit' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ '%' ],
               'range' => [
                   '%' => [
                       'min' => 0,
                       'max' => 100,
                   ],
               ],
               'default' => [
                   'unit' => '%',
                   'size' => 5,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-border-hr' => 'width: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_title_separator' => 'yes',
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator_height',
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
                   'size' => 3,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-border-hr' => 'height: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_title_separator' => 'yes',
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator_margin',
           [
               'label' => esc_html__( 'Margin', 'elementskit' ),
               'type' => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-border-hr' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_title_separator' => 'yes',
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
           ]
       );

       $this->end_controls_section();


       // Content Styles
       $this->start_controls_section(
           'ekit_blog_posts_content_style',
           [
               'label' => esc_html__( 'Content', 'elementskit' ),
               'tab'   => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_content' => 'yes',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_color',
           [
               'label'      => esc_html__( 'Color', 'elementskit' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-post-footer > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-body > p'   => 'color: {{VALUE}};',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_color_hover',
           [
               'label'      => esc_html__( 'Hover Color', 'elementskit' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-blog-block-post:hover .elementskit-post-footer > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-image-card:hover .elementskit-post-footer > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-card:hover .elementskit-post-footer > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-blog-block-post:hover .elementskit-post-body > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-image-card:hover .elementskit-post-body > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-card:hover .elementskit-post-body > p' => 'color: {{VALUE}};',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Typography::get_type(), [
               'name'       => 'ekit_blog_posts_content_typography',
               'selector'   => '{{WRAPPER}} .elementskit-post-footer > p, {{WRAPPER}} .elementskit-post-body > p',
           ]
       );

       $this->add_group_control(
           Group_Control_Text_Shadow::get_type(), [
               'name'       => 'ekit_blog_posts_content_shadow',
               'selector'   => '{{WRAPPER}} .elementskit-post-footer > p, {{WRAPPER}} .elementskit-post-body > p',
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_content_alignment',
           [
               'label'   => esc_html__( 'Alignment', 'elementskit' ),
               'type'    => Controls_Manager::CHOOSE,
               'options' => [
                   'left'    => [
                       'title' => esc_html__( 'Left', 'elementskit' ),
                       'icon'  => 'eicon-text-align-left',
                   ],
                   'center'  => [
                       'title' => esc_html__( 'Center', 'elementskit' ),
                       'icon'  => 'eicon-text-align-center',
                   ],
                   'right'   => [
                       'title' => esc_html__( 'Right', 'elementskit' ),
                       'icon'  => 'eicon-text-align-right',
                   ],
                   'justify' => [
                       'title' => esc_html__( 'justify', 'elementskit' ),
                       'icon'  => 'eicon-text-align-justify',
                   ],
               ],
               'default'   => 'left',
               'devices'   => [ 'desktop', 'tablet', 'mobile' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-footer'   => 'text-align: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-body > p' => 'text-align: {{VALUE}};',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_content_margin',
           [
               'label'      => esc_html__( 'Margin', 'elementskit' ),
               'type'       => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', '%', 'em' ],
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-post-footer'   => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                   '{{WRAPPER}} .elementskit-blog-block-post .elementskit-post-footer > p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                   '{{WRAPPER}} .elementskit-post-body > p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       //  content highlight

       $this->add_control(
           'ekit_blog_posts_content_highlight_border',
           [
               'label' => esc_html__( 'Show Highlight  Border', 'elementskit' ),
               'type' => Controls_Manager::SWITCHER,
               'label_on' => esc_html__( 'Show', 'elementskit' ),
               'label_off' => esc_html__( 'Hide', 'elementskit' ),
               'return_value' => 'yes',
               'default' => '',
               'separator' => 'before'
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_highlight_border_height',
           [
               'label' => esc_html__( 'Hight', 'elementskit' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 'px', '%' ],
               'range' => [
                   'px' => [
                       'min' => 5,
                       'max' => 300,
                       'step' => 1,
                   ],

               ],
               'default' => [
                   'unit' => 'px',
                   'size' => 100,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before' => 'height: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                       'ekit_blog_posts_content_highlight_border' => 'yes'
               ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_highlight_border_width',
           [
               'label' => esc_html__( 'Width', 'elementskit' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 'px', '%' ],
               'range' => [
                   'px' => [
                       'min' => 1,
                       'max' => 10,
                       'step' => 1,
                   ],

               ],
               'default' => [
                   'unit' => 'px',
                   'size' => 2,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before' => 'width: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_content_highlight_border' => 'yes'
               ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_highlight_border_top_bottom_pos',
           [
               'label' => esc_html__( 'Top Bottom Position', 'elementskit' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ '%' ],
               'range' => [
                   '%' => [
                       'min' => -10,
                       'max' => 110,
                       'step' => 1,
                   ],

               ],
               'default' => [
                   'unit' => '%',
                   'size' => 50,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before' => 'top: {{SIZE}}{{UNIT}};',
               ],

               'condition' => [
                   'ekit_blog_posts_content_highlight_border' => 'yes'
               ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_highlight_border_left_right_pos',
           [
               'label' => esc_html__( 'Left Right Position', 'elementskit' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ '%' ],
               'range' => [
                   '%' => [
                       'min' => -5,
                       'max' => 120,
                       'step' => 1,
                   ],

               ],
               'default' => [
                   'unit' => '%',
                   'size' => 0,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before' => 'left: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_content_highlight_border' => 'yes'
               ]
           ]
       );

       $this->start_controls_tabs('ekit_blog_posts_border_highlight_color_tabs',[
           'condition' => [
               'ekit_blog_posts_content_highlight_border' => 'yes'
           ]
       ]);

       $this->start_controls_tab(
           'ekit_blog_posts_border_highlight_color_normal_tab',
           [
               'label' => esc_html__( 'Normal', 'elementskit' ),
           ]
       );

       $this->add_group_control(
           Group_Control_Background::get_type(),
           [
               'name' => 'ekit_blog_posts_border_highlight_bg_color',
               'label' => esc_html__( 'Separator Color', 'elementskit' ),
               'types' => [ 'classic', 'gradient' ],
               'selector' => '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before',
           ]
       );

       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_border_highlight_color_hover_tab',
           [
               'label' => esc_html__( 'Hover', 'elementskit' ),
           ]
       );

       $this->add_group_control(
           Group_Control_Background::get_type(),
           [
               'name' => 'ekit_blog_posts_border_highlight_bg_color_hover',
               'label' => esc_html__( 'Separator Color', 'elementskit' ),
               'types' => [ 'classic', 'gradient' ],
               'selector' => '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:hover:before',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_highlight_border_transition',
           [
               'label' => esc_html__( 'Transition', 'elementskit' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 's' ],
               'range' => [
                   's' => [
                       'min' => .1,
                       'max' => 5,
                       'step' => .1,
                   ],

               ],
               'default' => [
                   'unit' => 's',
                   'size' => 0,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before' => '-webkit-transition: all {{SIZE}}{{UNIT}}; -o-transition: all {{SIZE}}{{UNIT}}; transition: all {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_content_highlight_border' => 'yes'
               ]
           ]
       );

       $this->end_controls_tab();
       $this->end_controls_tabs();

       $this->end_controls_section();


       // Author Image Styles
       $this->start_controls_section(
           'ekit_blog_posts_author_img_style',
           [
               'label'     => esc_html__( 'Author Image', 'elementskit' ),
               'tab'       => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_author_image' => 'yes',
               ],
           ]
       );

       $this->add_control(
        'ekit_blog_posts_author_img_size_width',
        [
            'label' => esc_html__( 'Image Width', 'elementskit' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 30,
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
                'size' => 30,
            ],
            'selectors' => [
                '{{WRAPPER}} .elementskit-post-body  .meta-author .author-img' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

       $this->add_control(
        'ekit_blog_posts_author_img_size_height',
        [
            'label' => esc_html__( 'Image Height', 'elementskit' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 30,
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
                'size' => 30,
            ],
            'selectors' => [
                '{{WRAPPER}} .elementskit-post-body  .meta-author .author-img' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

       $this->add_group_control(
           Group_Control_Box_Shadow::get_type(), [
               'name'      => 'ekit_blog_posts_author_img_shadow',
               'selector'  => '{{WRAPPER}} .elementskit-post-body .author-img',
           ]
       );

       $this->add_group_control(
           Group_Control_Border::get_type(),
           [
               'name'     => 'ekit_blog_posts_author_img_border',
               'label'    => esc_html__( 'Border', 'elementskit' ),
               'selector' => '{{WRAPPER}} .elementskit-post-body .author-img',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_author_img_margin',
           [
               'label' => esc_html__( 'Margin', 'elementskit' ),
               'type' => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', '%', 'em' ],
               'default' => [
                   'top' => '0',
                   'right' => '15',
                   'bottom' => '0',
                   'left' => '0',
                   'isLinked' => false,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body .author-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_author_img_radius',
           [
               'label'     => esc_html__( 'Radius', 'elementskit' ),
               'type'      => Controls_Manager::DIMENSIONS,
               'size_units'=> [ 'px', '%', 'em' ],
               'separator' => 'after',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body .author-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->end_controls_section();




       // Button
       $this->start_controls_section(
           'ekit_blog_posts_btn_section_style',
           [
               'label' =>esc_html__( 'Button', 'elementskit' ),
               'tab' => Controls_Manager::TAB_STYLE,
               'condition' => ['ekit_blog_posts_read_more' => 'yes', 'ekit_blog_posts_layout_style!' => 'elementskit-blog-block-post']
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_btn_text_padding',
           [
               'label' =>esc_html__( 'Padding', 'elementskit' ),
               'type' => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', 'em', '%' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_responsive_control(
            'ekit_blog_posts_btn_normal_icon_font_size',
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
                    '{{WRAPPER}} .elementskit-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementskit-btn svg'  => 'max-width: {{SIZE}}{{UNIT}};',
                ),
            )
        );

       $this->add_group_control(
           Group_Control_Typography::get_type(),
           [
               'name' => 'ekit_blog_posts_btn_typography',
               'label' =>esc_html__( 'Typography', 'elementskit' ),
               'selector' => '{{WRAPPER}} .elementskit-btn',
           ]
       );

       $this->start_controls_tabs( 'ekit_blog_posts_btn_tabs_style' );

       $this->start_controls_tab(
           'ekit_blog_posts_btn_tabnormal',
           [
               'label' =>esc_html__( 'Normal', 'elementskit' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_text_color',
           [
               'label' =>esc_html__( 'Text Color', 'elementskit' ),
               'type' => Controls_Manager::COLOR,
               'default' => '',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-btn svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};',
               ],
           ]
       );
       $this->add_group_control(
           Group_Control_Background::get_type(),
           array(
               'name'     => 'ekit_blog_posts_btn_bg_color',
               'selector' => '{{WRAPPER}} .elementskit-btn',
           )
       );

       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_btn_tab_button_hover',
           [
               'label' =>esc_html__( 'Hover', 'elementskit' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_hover_color',
           [
               'label' =>esc_html__( 'Text Color', 'elementskit' ),
               'type' => Controls_Manager::COLOR,
               'default' => '#ffffff',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn:hover' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-btn:hover svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Background::get_type(),
           array(
               'name'     => 'ekit_blog_posts_btn_bg_hover_color',
               'selector' => '{{WRAPPER}} .elementskit-btn:hover',
           )
       );

       $this->end_controls_tab();
       $this->end_controls_tabs();

       $this->add_control(
           'ekit_blog_posts_btn_border_style',
           [
               'label' => esc_html_x( 'Border Type', 'Border Control', 'elementskit' ),
               'type' => Controls_Manager::SELECT,
               'options' => [
                   '' => esc_html__( 'None', 'elementskit' ),
                   'solid' => esc_html_x( 'Solid', 'Border Control', 'elementskit' ),
                   'double' => esc_html_x( 'Double', 'Border Control', 'elementskit' ),
                   'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementskit' ),
                   'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementskit' ),
                   'groove' => esc_html_x( 'Groove', 'Border Control', 'elementskit' ),
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn' => 'border-style: {{VALUE}};',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_btn_border_dimensions',
           [
               'label' => esc_html_x( 'Width', 'Border Control', 'elementskit' ),
               'type' => Controls_Manager::DIMENSIONS,
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
               'condition'  => [
                   'ekit_blog_posts_btn_border_style!' => ''
               ]
           ]
       );
       $this->start_controls_tabs( 'xs_tabs_button_border_style' );
       $this->start_controls_tab(
           'ekit_blog_posts_btn_tab_border_normal',
           [
               'label' =>esc_html__( 'Normal', 'elementskit' ),
               'condition'  => [
                   'ekit_blog_posts_btn_border_style!' => ''
               ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_border_color',
           [
               'label' => esc_html_x( 'Color', 'Border Control', 'elementskit' ),
               'type' => Controls_Manager::COLOR,
               'default' => '',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn' => 'border-color: {{VALUE}};',
               ],
               'condition'  => [
                    'ekit_blog_posts_btn_border_style!' => ''
                ]
           ]
       );
       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_btn_tab_button_border_hover',
           [
               'label' =>esc_html__( 'Hover', 'elementskit' ),
               'condition'  => [
                   'ekit_blog_posts_btn_border_style!' => ''
               ]
           ]
       );
       $this->add_control(
           'ekit_blog_posts_btn_hover_border_color',
           [
               'label' => esc_html_x( 'Color', 'Border Control', 'elementskit' ),
               'type' => Controls_Manager::COLOR,
               'default' => '',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn:hover' => 'border-color: {{VALUE}};',
               ],
               'condition'  => [
                    'ekit_blog_posts_btn_border_style!' => ''
                ]
           ]
       );
       $this->end_controls_tab();
       $this->end_controls_tabs();
       $this->add_responsive_control(
           'ekit_blog_posts_btn_border_radius',
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
                   '{{WRAPPER}} .elementskit-btn' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Box_Shadow::get_type(), [
               'name'     => 'ekit_blog_posts_btn_box_shadow_group',
               'selector' => '{{WRAPPER}} .elementskit-btn',
           ]
       );

       
       $this->end_controls_section();

       $this->start_controls_section(
            'pagination_style',
            [
                'label' =>esc_html__( 'Pagination', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition'  => [
                    'ekit_blog_posts_is_pagination' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'pagination_alignment',
            [
                'label' =>esc_html__( 'Alignment', 'elementskit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start'    => [
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
                ],
                'selectors'=> [
                     '{{WRAPPER}} .ekit-blog-post-pagination-container' => 'justify-content: {{VALUE}};',
                 ],
                'default' => 'left',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'pagination_typo',
                'selector'   => '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers',
            ]
        );

        $this->add_responsive_control(
            'pagination_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default'    => [
                    'top' => '12',
                    'right' => '14',
                    'bottom' => '12',
                    'left' => '14',
                    'unit' => 'px',
                    'isLinked' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'pagination_margin',
            [
                'label' => esc_html__( 'margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
               
                'selectors' => [
                    '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default'    => [
                    'top' => '2',
                    'right' => '2',
                    'bottom' => '2',
                    'left' => '2',
                    'unit' => 'px',
                    'isLinked' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'pagination_style_tabs'
        );
 
        $this->start_controls_tab(
            'pagination_tab_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'pagination_bg',
                'default' => '#F7F8FB',
                'selector' => '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers',
            )
        );

        $this->add_control(
            'pagination_color',
            [
                'label' => esc_html_x( 'Text Color', 'Border Control', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#505255',
                'selectors' => [
                    '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'pagination_border',
                'label'    => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pagination_tab_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'pagination_bg_hover',
                'default' => '#505255',
                'selector' => '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers:hover',
            )
        );

        $this->add_control(
            'pagination_color_hover',
            [
                'label' => esc_html_x( 'Text Color', 'Border Control', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'pagination_border_hover',
                'label'    => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers:hover',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pagination_tab_active',
            [
                'label' => esc_html__( 'Active', 'elementskit' ),
                'condition' => [
                    'ekit_blog_posts_pagination_style'  => 'numbered'
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'pagination_bg_active',
                'default' => '#505255',
                'selector' => '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers.current',
            )
        );

        $this->add_control(
            'pagination_color_active',
            [
                'label' => esc_html_x( 'Text Color', 'Border Control', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers.current' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'pagination_active',
                'label'    => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-blog-post-pagination-container .page-numbers.current',
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

		
		$this->start_controls_section(
			'ekit_section_style_arrows',
			[
				'label' => esc_html__('Arrows', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_blog_posts_enable_carousel' => 'yes',
					'ekit_blog_post_show_arrow' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekit_blog_arrows_position',
			[
				'label' => esc_html__( 'Arrow Position', 'elementskit' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => esc_html__( 'Default', 'elementskit' ),
				'label_on' => esc_html__( 'Custom', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'ekit_blog_pagination_arrow_left',
			[
				'label' => esc_html__( 'Left Arrow Position', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%','px'],
				'range' => [
					'%' => [
						'min' => -15,
						'max' => 110,
					],
					'px' => [
						'min' => -100,
						'max' => 1200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-blog-carousel-button-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
        );


        $this->add_responsive_control(
			'ekit_blog_pagination_arrow_right',
			[
				'label' => esc_html__( 'Right Arrow Position', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range' => [
					'%' => [
						'min' => -15,
						'max' => 110,
					],
					'px' => [
						'min' => -100,
						'max' => 1200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-blog-carousel-button-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
        );

		$this->add_responsive_control(
			'ekit_blog_arrows_position_vertical',
			[
				'label' => esc_html__('Vertical Position', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px'],
				'range' => [
					'%' => [
						'min' => -20,
						'max' => 120,
					],
					'px' => [
						'min' => -200,
						'max' => 1200,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button' => 'top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_popover();

		$this->add_responsive_control(
			'ekit_blog_arrows_size',
			[
				'label' => esc_html__('Arrows Size (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs(
			'ekit_blog_arrows_style_tabs'
		);

		$this->start_controls_tab(
			'ekit_blog_arrows_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_blog_arrows_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-swiper-button svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_blog_arrows_background_color',
			[
				'label' => esc_html__('Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'ekit_blog_arrows_border_normal',
                'label'    => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementor-swiper-button',
            ]
        );


		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_blog_arrows_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_blog_arrows_hover_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-swiper-button svg:hover' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_blog_arrows_background_hover_color',
			[
				'label' => esc_html__('Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'ekit_blog_arrows_border_hover',
                'label'    => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .elementor-swiper-button:hover',
            ]
        );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_blog_arrows_border_radius',
			[
				'label' =>esc_html__( 'Border Radius (px)', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '' ,
					'left' => '',
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_blog_arrows_padding',
			[
				'label' => esc_html__('Padding (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'default' => [
					'top' => '10',
					'right' => '10',
					'bottom' => '10' ,
					'left' => '10',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button' =>  'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();		
		
		$this->start_controls_section(
			'ekit_section_style_pagination',
			[
				'label' => esc_html__('Pagination', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_blog_posts_enable_carousel' => 'yes',
					'ekit_blog_post_show_pagination' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'ekit_blog_pagination_horizontal_position',
			[
				'label' => esc_html__( 'Horizontal Position', 'elementskit' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'  => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'prefix_class' => 'elementskit-pagination-',
				'selectors' => [
					'{{WRAPPER}} .ekit-blog-carousel-pagination' => 'text-align: {{VALUE}}; justify-content: {{VALUE}} ',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_blog_pagination_position_vertical',
			[
				'label' => esc_html__('Vertical Position (%)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px'],
				'range' => [
					'%' => [
						'min' => -20,
						'max' => 120,
					],
					'px' => [
						'min' => -200,
						'max' => 1200,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 95,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-blog-carousel-pagination' => 'top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_blog_pagination_size',
			[
				'label' => esc_html__( 'Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-container-horizontal .swiper-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-pagination-fraction' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_blog_pagination_border_radius',
			[
				'label' =>esc_html__( 'Border Radius (px)', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0' ,
					'left' => '0',
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'ekit_blog_pagination_color_inactive',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#5141FFB5',
				'selectors' => [
					// The opacity property will override the default inactive dot color which is opacity 0.2.
					'{{WRAPPER}} .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'background-color: {{VALUE}}; opacity: 1;',
				],
			]
		);

		$this->add_control(
			'ekit_blog_pagination_more_options',
			[
				'label' => esc_html__( 'Active', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_blog_pagination_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#0000ff',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_blog_pagination_active_size',
			[
				'label' => esc_html__( 'Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1.2,
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'ekit_blog_pagination_active_border',
                'label'    => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .swiper-pagination-bullet-active',
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
       $settings = $this->get_settings();
       extract($settings);


       $request_ekit_blog_posts_paged = (int)(isset($_GET['ekit-blog-posts-paged']) ? $_GET['ekit-blog-posts-paged'] : 1);

       $highlight_border = $ekit_blog_posts_content_highlight_border == 'yes' ? 'ekit-highlight-border' : '';
       $ekit_blog_posts_offset = ($ekit_blog_posts_offset == '') ? 0 : $ekit_blog_posts_offset;

       $default    = [
           'orderby'           => array( $ekit_blog_posts_order_by => $ekit_blog_posts_sort ),
           'posts_per_page'    => $ekit_blog_posts_num,
           'ignore_sticky_posts' => 1
       ];

       if(isset($ekit_blog_posts_is_pagination) && $ekit_blog_posts_is_pagination === 'yes'){
            $default['paged'] = $request_ekit_blog_posts_paged; // if pagination is on
        }else{
            $default['offset'] = $ekit_blog_posts_offset; // if pagination is on
        }

        if($ekit_blog_posts_is_manual_selection === 'yes'){
            $default = \ElementsKit_Lite\Utils::array_push_assoc(
                $default, 'post__in', (!empty($ekit_blog_posts_manual_selection  && count($ekit_blog_posts_manual_selection) > 0 )) ? $ekit_blog_posts_manual_selection : [-1]
            );
        }

        if($ekit_blog_posts_is_manual_selection == '' && $ekit_blog_posts_cats != ''){
            $default = \ElementsKit_Lite\Utils::array_push_assoc(
                $default, 'category__in', $ekit_blog_posts_cats
            );
        }

        // Post Items
        $this->add_render_attribute(
            'post_items',
            [
                'id'    => 'post-items--' . $this->get_id(),
                'class' => 'row post-items ekit-blog-posts-content',
				'data-enable' => $ekit_blog_posts_enable_carousel === 'yes' ? 'yes' : 'no',
            ]
        );

        if ($grid_masonry === 'yes'){
            $this->add_render_attribute('post_items', 'data-masonry-config', 'true');
        }

       // Post Query

        if ( 'elementskit-blog-block-post' == $ekit_blog_posts_layout_style ) {
            $ekit_blog_posts_column = 'ekit-md-12';
        }

        $column_size   = 'ekit-md-12';
        $img_order     = 'order-1';
        $content_order = 'order-2';

        if ( 'right' == $ekit_blog_posts_feature_img_float ) {
            $img_order = 'order-2';
            $content_order = 'order-1';
        }

		// Blog carousel settings
		$carouselSettings = [
			'direction' 		=> is_rtl() ? 'rtl' : 'ltr',
			'spaceBetween' 		=> $ekit_blog_posts_spacing['size'] ?? 30,
			'slidesPerView'		=> $ekit_blog_posts_slide_show['size'] ?? 1,
			'slidesPerGroup'	=> $ekit_blog_posts_slides_scroll['size'] ?? 1,
			'navigation'		=> !empty($ekit_blog_post_show_arrow),
			'pagination'		=> !empty($ekit_blog_post_show_pagination),
			'speed'				=> $ekit_blog_post_speed ?? '600',
			'autoplay'			=> !empty($ekit_blog_post_autoplay),
			'autoplayDelay'		=> $ekit_blog_post_autoplay_delay ? $ekit_blog_post_autoplay_delay : 3000,
			'pauseOnHover'	    => !empty($ekit_blog_post_pause_on_hover),
			'breakPoints'		=> [
				360 => [
					'slidesPerView'     => $ekit_blog_posts_slide_show_mobile['size'] ?? 1,
					'slidesPerGroup'    => $ekit_blog_posts_slides_scroll_mobile['size'] ?? 1
				],
				767 => [
					'slidesPerView'     => $ekit_blog_posts_slide_show_tablet['size'] ?? 2,
					'slidesPerGroup'    => $ekit_blog_posts_slides_scroll_tablet['size'] ?? 1,
				],
				1024 => [
					'slidesPerView'     => $ekit_blog_posts_slide_show['size'] ?? 1,
					'slidesPerGroup'    => $ekit_blog_posts_slides_scroll['size'] ?? 1,
				],
				1400 => [
					'slidesPerView'     => $ekit_blog_posts_slide_show['size'] ?? 1,
					'slidesPerGroup'    => $ekit_blog_posts_slides_scroll['size'] ?? 1,
				]
			],
		];

        $this->add_render_attribute( 'blog-carousel', [
            'class' => [
				method_exists('\ElementsKit_Lite\Utils', 'swiper_class') ? esc_attr(\ElementsKit_Lite\Utils::swiper_class()) : 'swiper',
				'blogCarousel',
			],
            'data-settings'	=> wp_json_encode( $carouselSettings ),
        ]);

		// removing hook, may added by themes or plugins
		add_filter( 'excerpt_more', function( $more ) {
			return ' [&hellip;] ';
		}, 99 );
        ?>

        <div <?php echo $this->get_render_attribute_string('post_items'); ?>>

			<!-- Blog Carousel markup render -->
			<?php if ($ekit_blog_posts_enable_carousel === 'yes') :?>
				<div <?php $this->print_render_attribute_string( 'blog-carousel' ); ?>>
					<div class="swiper-wrapper">
			<?php endif ;

				$post_query = new \WP_Query( $default );
				while ( $post_query->have_posts() ) : $post_query->the_post();

					if ( 'yes' == $ekit_blog_posts_feature_img
					&& has_post_thumbnail()
					&& ( 'yes' == $ekit_blog_posts_title
					|| 'yes' == $ekit_blog_posts_content
					|| 'yes' == $ekit_blog_posts_meta
					|| 'yes' == $ekit_blog_posts_author ) ) {
						$column_size = 'ekit-md-6';
					}
					include ElementsKit_Widget_Blog_Posts_Handler::get_dir() . 'views/main.php'; ?>
				<?php endwhile; 

			//Blog Carousel markup render
			if ($ekit_blog_posts_enable_carousel === 'yes') : ?>
					</div >
				</div>
				<?php if($ekit_blog_post_show_arrow === 'yes') : ?>
					<div class="ekit-swiper-arrow">
						<div class="elementor-swiper-button elementor-swiper-button-prev ekit-blog-carousel-button-prev">
							<?php Icons_Manager::render_icon( $ekit_blog_post_left_arrow_icon, [ 'aria-hidden' => 'true' ]); ?>
						</div>
						<div class="elementor-swiper-button elementor-swiper-button-next ekit-blog-carousel-button-next">
							<?php Icons_Manager::render_icon( $ekit_blog_post_right_arrow_icon, [ 'aria-hidden' => 'true' ]); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php if($ekit_blog_post_show_pagination === 'yes') : ?>
					<div class="swiper-pagination ekit-blog-carousel-pagination"></div>
				<?php endif; ?>
			<?php endif ;?>
        </div>
        
        
        <?php if(isset($ekit_blog_posts_is_pagination) && $ekit_blog_posts_is_pagination == 'yes') : ?>
            <div 
            data-ekit-blog-post-style="<?php echo esc_attr($ekit_blog_posts_pagination_style); ?>"
            class="ekit-blog-post-pagination-container ekit-blog-post-pagination-style-<?php echo esc_attr($ekit_blog_posts_pagination_style); ?>" 
            id="post-nagivation--<?php echo $this->get_id(); ?>">
                <?php

                $next_button_text = (isset($ekit_blog_posts_pagination_style) && $ekit_blog_posts_pagination_style == 'loadmore') 
                                    ? esc_html__('Load More', 'elementskit') 
                                    : esc_html__('Next', 'elementskit');

                echo paginate_links( array(
                    // 'base'               => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format'                => '?ekit-blog-posts-paged=%#%',
                    'current'               => $request_ekit_blog_posts_paged,
                    'total'                 => $post_query->max_num_pages,
                    'prev_text'             => esc_html__( 'Previous', 'elementskit' ),
                    'next_text'             => $next_button_text,
                    'before_page_number'    => '',
                    'after_page_number'     => '',
                ) );
                ?>

            </div>

            <?php 
            endif;
        
        wp_reset_postdata();

		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ):
			$this->render_editor_script();
		endif;
   }

   protected function render_editor_script() {
       ?>
       <script>
           (function ($) {
               'use strict';

               $(function () {
                   var $postItems = $('#post-items--<?php echo esc_attr( $this->get_id() ); ?>[data-masonry-config]');

                   $postItems.imagesLoaded(function () {
                       $postItems.masonry();
                   });
               });
           }(jQuery));
       </script>
       <?php
   }
}
