<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Twitter_Feed_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Twitter_Feed extends Widget_Base {
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
        return 'https://wpmet.com/doc/twitter-api-key/';
    }

	protected function register_controls() {

        $setting = new \Ekit_twitter_settings();



		/*Layout Settings*/
		$this->start_controls_section(
            'ekit_twitter_feed_layout_settings', [
                'label' => esc_html__( 'Layout Settings ', 'elementskit' ),
            ]
        );
		$this->add_control(
            'ekit_twitter_feed_show_profile',
            [
                'label' => esc_html__( 'Show Header', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit' ),
                'label_off' => esc_html__( 'Hide', 'elementskit' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
		);

		$this->add_control(
            'ekit_twitter_feed_show_cover_photo',
            [
                'label' => esc_html__( 'Show Cover Photo', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit' ),
                'label_off' => esc_html__( 'Hide', 'elementskit' ),
                'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'ekit_twitter_feed_show_profile' => 'yes'
				]
            ]
        );

		$this->add_control(
            'ekit_twitter_feed_show_statics',
            [
                'label' => esc_html__( 'Show Statics', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit' ),
                'label_off' => esc_html__( 'Hide', 'elementskit' ),
                'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'ekit_twitter_feed_show_profile' => 'yes'
				]
            ]
        );

		 $this->add_control(
            'ekit_twitter_feed_layout_style',
            [
                'label' => esc_html__( 'Feed Style', 'elementskit' ),
                'type' =>  Controls_Manager::SELECT,
                'default' => 'ekit-layout-list',
                'options' => [
                    'ekit-layout-list'  => esc_html__( 'List', 'elementskit' ),
                    'ekit-layout-grid' => esc_html__( 'Grid', 'elementskit' ),
                    'ekit-layout-masonary' => esc_html__( 'Masonary', 'elementskit' ),

                ],
            ]
        );

		$this->add_control(
            'ekit_twitter_feed_column_grid',
            [
                'label' => esc_html__( 'Columns Grid', 'elementskit' ),
                'type' =>  Controls_Manager::SELECT,
                'default' => 'ekit-col-4',
                'options' => [
                    'ekit-col-6'  => esc_html__( '2 Columns', 'elementskit' ),
                    'ekit-col-4' => esc_html__( '3 Columns', 'elementskit' ),
                    'ekit-col-3' => esc_html__( '4 Columns', 'elementskit' ),
                ],
				'condition' => ['ekit_twitter_feed_layout_style!' => 'ekit-layout-list'],
				'separator' => 'after',
            ]
        );

		$this->add_control(
            'ekit_twitter_feed_show_post',
            [
                'label'         => esc_html__('Show Post', 'elementskit'),
                'type'          => Controls_Manager::NUMBER,
                'default' 		=> 9,
				'min' 			=> 1,
				'max' 			=> 100,
				'step' 			=> 1,

            ]
        );
		$this->add_control(
            'ekit_twitter_feed_show_media',
            [
                'label' => esc_html__( 'Show Media', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit' ),
                'label_off' => esc_html__( 'Hide', 'elementskit' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );


        $this->add_control(
            'ekit_twitter_follow_btn_text',
            [
                'label' =>esc_html__( 'Label', 'elementskit' ),
                'type' => Controls_Manager::TEXT,
                'default' =>esc_html__( 'Follow', 'elementskit' ),
                'placeholder' =>esc_html__( 'Follow', 'elementskit' ),
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'ekit_twitter_follow_btn_icon',
            [
                'label'	=> esc_html__( 'Icon', 'elementskit' ),
                'type'	=> Controls_Manager::ICONS,
            ]
        );

		$this->end_controls_section();
		/* End layout settings*/

		/*Display Settings*/
		$this->start_controls_section(
            'ekit_twitter_feed_display_settings', [
                'label' => esc_html__( 'Display Settings ', 'elementskit' ),
            ]
        );


		$this->add_control(
            'ekit_twitter_feed_show_author',
            [
                'label' => esc_html__( 'Show Author', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit' ),
                'label_off' => esc_html__( 'Hide', 'elementskit' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
		$this->add_control(
            'ekit_twitter_feed_author_type',
            [
                'label' => esc_html__( 'Author Settings', 'elementskit' ),
                'type' =>  Controls_Manager::SELECT,
                'default' => 'both',
                'options' => [
                    'only-profile'  => esc_html__( 'Only Thumbnail Image', 'elementskit' ),
                    'only-name' => esc_html__( 'Only Name', 'elementskit' ),
                    'both' => esc_html__( 'Both Thumbnail & name', 'elementskit' ),
                ],
				'condition' => ['ekit_twitter_feed_show_author' => 'yes']
            ]
        );
		$this->add_control(
            'ekit_twitter_feed_author_style',
            [
                'label' => esc_html__( 'Thumbnail Style', 'elementskit' ),
                'type' =>  Controls_Manager::SELECT,
                'default' => 'ekit-twitter-profile-circle',
                'options' => [
                    'ekit-twitter-profile-circle'  => esc_html__( 'Circle', 'elementskit' ),
                    'ekit-twitter-profile-square' => esc_html__( 'Square', 'elementskit' ),
                ],
				'condition' => [
					'ekit_twitter_feed_author_type!' => 'only-name',
					'ekit_twitter_feed_show_author' => 'yes'
				]
            ]
        );
		$this->add_control(
            'ekit_twitter_feed_show_post_date',
            [
                'label' => esc_html__( 'Show Date', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit' ),
                'label_off' => esc_html__( 'Hide', 'elementskit' ),
                'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'after',
				'condition' => [
					'ekit_twitter_feed_show_author' => 'yes'
				]
            ]
        );



		$this->add_control(
            'ekit_twitter_feed_show_hash',
            [
                'label' => esc_html__( 'Show Hash', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit' ),
                'label_off' => esc_html__( 'Hide', 'elementskit' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'ekit_twitter_feed_show_read_more',
            [
                'label' => esc_html__( 'Show Read More', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit' ),
                'label_off' => esc_html__( 'Hide', 'elementskit' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
		$this->add_control(
            'ekit_twitter_feed_comments_box',
            [
                'label' => esc_html__( 'Comments Box', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit' ),
                'label_off' => esc_html__( 'Hide', 'elementskit' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
		$this->add_control(
            'ekit_twitter_feed_share_show',
            [
                'label' => esc_html__( 'Show Share', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit' ),
                'label_off' => esc_html__( 'Hide', 'elementskit' ),
                'return_value' => 'yes',
                'default' => '',
				'condition' => [
					'ekit_twitter_feed_comments_box' => 'yes'
				]
            ]
        );

		$this->end_controls_section();



		/* End display settings*/

		/* style component start */
		$this->start_controls_section(
			'ekit_twitter_feed_comments_header_style_tab',
			[
				'label' => esc_html__( 'Cover Photo', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_twitter_feed_show_profile' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_statics_heading',
			[
				'label' => esc_html__( 'Statics', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'ekit_statics_text_color',
			[
				'label' =>esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#657786',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-header-statistics > .ekit-twitter-tweet-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_statics_count_color',
			[
				'label' =>esc_html__( 'Count Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#abb8c2',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-header-statistics > .ekit-twitter-tweet-count > strong' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'	=> 'ekit_statics_border',
				'label'	=> esc_html__( 'Border', 'elementskit' ),
                'selector'  => '{{WRAPPER}} .ekit-wid-con .ekit-twitter-feed-header-statistics',
				'fields_options' => [
					'border_type' =>[ 
						'default' =>'yes' 
					],
					'border' => [
						'default' => 'solid',
                    ],
					'width' => [
						'default' => [
                            'top' => '1',
							'right' => '0',
							'bottom' => '1',
							'left' => '0',
							'unit' => 'px',
						],
                    ],
					'color' => [
						'alpha' => false,
						'default' => '#f0f0f0',
                    ],
                ],
                'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_statics_padding',
			[
				'label' =>esc_html__( 'Padding (px)', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-twitter-feed-header-statistics' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_btn_heading',
			[
				'label' => esc_html__( 'Button', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'ekit_btn_text_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_border_radius',
			[
				'label' =>esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_btn_typography',
				'label' =>esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a',
			]
		);

        $this->add_group_control(
        	Group_Control_Text_Shadow::get_type(),
        	[
        		'name' => 'ekit_btn_shadow',
        		'selector' => '{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a',
        	]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_btn_shadow_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a',
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
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_text_background_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1da1f2',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_btn_text_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a',
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
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_text_background_color_hover',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_hover_border_color',
			[
				'label' =>esc_html__( 'Border Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_btn_text_box_shadow_hover',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-twitter-feed-header-user-info-follow > a:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_twitter_feed_comments_container_style_tab',
			[
				'label' => esc_html__( 'Container', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_container_wrap_heading',
			[
				'label'	=> esc_html__( 'Wrap', 'elementskit' ),
				'type'	=> Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_container_wrap_bg_color',
			[
				'label'	=> esc_html__( 'Background', 'elementskit' ),
				'type' 	=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-twitter-feed' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_container_wrap_padding',
			[
				'label' =>esc_html__( 'Padding (px)', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-twitter-feed' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'separator' => 'before'
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_container_items_heading',
			[
				'label'	=> esc_html__( 'Items', 'elementskit' ),
				'type'	=> Controls_Manager::HEADING,
                'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_twitter_feed_comments_box_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-twitter-feed-content-wraper',
				'exclude' => [
					'image'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_twitter_feed_comments_box_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-twitter-feed-content-wraper',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_twitter_feed_comments_box_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-twitter-feed-content-wraper',
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_boreder_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-content-wraper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_margin_bottom',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-content-wraper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_masnory_margin_right',
			[
				'label' => esc_html__( 'Margin Right', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-layout-masonary' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_twitter_feed_layout_style' => 'ekit-layout-masonary'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_grid_margin_right',
			[
				'label' => esc_html__( 'Margin Right', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-layout-grid .ekit-twitter-feed-column' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-layout-grid ' => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_twitter_feed_layout_style' => 'ekit-layout-grid'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-content-wraper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// header
		$this->start_controls_section(
			'ekit_twitter_feed_comments_box_header_style_tab',
			[
				'label' => esc_html__( 'Header', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_twitter_feed_show_author' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_twitter_feed_comments_box_header_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-twitter-feed-profile-info-wraper',
			]
		);

		$this->start_controls_tabs(
            'ekit_twitter_feed_comments_box_header_hover_and_normal_tabs'
        );
        $this->start_controls_tab(
            'ekit_twitter_feed_comments_box_header_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_normal_name_heading',
			[
				'label' => esc_html__( 'Name', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_name_color_normal',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .profile-display-name .fullname' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-twitter-feed-header-user-info .ekit-twitter-fullname' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_normal_username_heading',
			[
				'label' => esc_html__( 'User Name', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_username_color_normal',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#657786',
				'selectors' => [
					'{{WRAPPER}} .profile-display-name .screen_name' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-twitter-feed-header-user-info .ekit-twitter-screenname' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_normal_date_heading',
			[
				'label' => esc_html__( 'Date', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_date_color_normal',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#657786',
				'selectors' => [
					'{{WRAPPER}} .profile-display-name .ekit-twitter-feed-item-user-date' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_icon_color_normal_heading',
			[
				'label' => esc_html__( 'Icon', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_icon_color_normal',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e7e7e7',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-header .ekit-twitter-logo > a' => 'color: {{VALUE}}',
				],
			]
		);

        $this->end_controls_tab();
        $this->start_controls_tab(
            'ekit_twitter_feed_comments_box_header_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_hover_name_heading',
			[
				'label' => esc_html__( 'Name', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_name_color_hover',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#D55732',
				'selectors' => [
					'{{WRAPPER}} .profile-display-name .fullname:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-twitter-feed-header-user-info .ekit-twitter-fullname:hover' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_hover_username_heading',
			[
				'label' => esc_html__( 'User Name', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_username_color_hover',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#D55732',
				'selectors' => [
					'{{WRAPPER}} .profile-display-name .screen_name:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-twitter-feed-header-user-info .ekit-twitter-screenname:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_icon_color_hover_heading',
			[
				'label' => esc_html__( 'Icon', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_icon_color_hover',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1DA1F2',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-header .ekit-twitter-logo > a:hover' => 'color: {{VALUE}}',
				],
			]
		);

        $this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_twitter_feed_comments_box_content_style_tab',
			[
				'label' => esc_html__( 'Content', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_content_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#14171a',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-content-wraper .feed-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_content_permalink_color',
			[
				'label' => esc_html__( 'Permalink Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#D55732',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-content-wraper .feed-title > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_twitter_feed_comments_box_content_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-twitter-feed-content-wraper .feed-title',
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_content_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-content-wraper .feed-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_twitter_feed_comments_box_content_read_more_style_tab',
			[
				'label' => esc_html__( 'Read More', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_twitter_feed_show_read_more' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_twitter_feed_comments_box_content_read_more_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .read-more-button > a',
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_content_read_more_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .read-more-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
            'ekit_twitter_feed_comments_box_header_hover_and_normal_readmore_tabs'
        );
        $this->start_controls_tab(
            'ekit_twitter_feed_comments_box_header_readmore_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_readmore_color_normal',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#D55732',
				'selectors' => [
					'{{WRAPPER}} .read-more-button > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_twitter_feed_comments_box_header_readmore_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_readmore_color_hover',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#14171a',
				'selectors' => [
					'{{WRAPPER}} .read-more-button > a:hover' => 'color: {{VALUE}}',
				],
			]
		);

        $this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_twitter_feed_comments_box_content_hash_style_tab',
			[
				'label' => esc_html__( 'Hash Tag', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_twitter_feed_show_hash' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_twitter_feed_comments_box_content_hash_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .feed-title-hash > a',
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_content_hash_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .feed-title-hash' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
            'ekit_twitter_feed_comments_box_header_hover_and_normal_hash_tabs'
        );
        $this->start_controls_tab(
            'ekit_twitter_feed_comments_box_header_hash_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_hash_color_normal',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#D55732',
				'selectors' => [
					'{{WRAPPER}} .feed-title-hash > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_twitter_feed_comments_box_header_hash_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_hash_color_hover',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#14171a',
				'selectors' => [
					'{{WRAPPER}} .feed-title-hash > a:hover' => 'color: {{VALUE}}',
				],
			]
		);

        $this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Action', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
            'ekit_twitter_feed_comments_box_header_hover_and_normal_action_tabs'
        );
        $this->start_controls_tab(
            'ekit_twitter_feed_comments_box_header_action_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_action_normal_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#657786',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-comments > a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-twitter-feed-comments > i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-twitter-feed-comments .ekit-twitter-feed-share' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_action_normal_font_size',
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
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-comments > a' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-twitter-feed-comments > i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-twitter-feed-comments .ekit-twitter-feed-share' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-twitter-feed-comments .ekit-twitter-feed-share a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_header_action_normal_margin_right',
			[
				'label' => esc_html__( 'Margin Right', 'elementskit' ),
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
					'size' => 24,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-comments > a' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_twitter_feed_comments_box_header_action_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_action_hover_comments_color_heading',
			[
				'label' => esc_html__( 'Comments', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_action_hover_comments_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1da1f2',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-comments .ekit-twitter-comments:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-twitter-feed-comments .ekit-twitter-comments:hover svg path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_action_hover_retweet_color_heading',
			[
				'label' => esc_html__( 'Retweet', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_action_hover_retweet_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#17bf63',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-comments .ekit-twitter-retweet:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-twitter-feed-comments .ekit-twitter-retweet:hover svg path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_action_hover_like_color_heading',
			[
				'label' => esc_html__( 'Like', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_header_action_hover_like_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e0245e',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-comments .ekit-twitter-like:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-twitter-feed-comments .ekit-twitter-like:hover svg path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_twitter_feed_share_box_header_action_hover_like_color_heading',
			[
				'label' => esc_html__( 'Share', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_twitter_feed_share_box_header_action_hover_like_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1DA1F2',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-feed-comments .ekit-twitter-feed-share:hover' => 'color: {{VALUE}}',
				],
			]
		);

        $this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_twitter_feed_comments_box_media_tab',
			[
				'label' => esc_html__( 'Media', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_twitter_feed_show_media' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_media_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-media > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_media_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-media' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_twitter_feed_comments_box_media_play_button',
			[
				'label' => esc_html__( 'Play Button', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs(
            'ekit_twitter_feed_comments_box_header_hover_and_normal_play_tabs'
        );
        $this->start_controls_tab(
            'ekit_twitter_feed_comments_box_header_play_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_media_play_button_circle_fill',
			[
				'label' => esc_html__( 'Circle Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1DA1F2',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-media .twitter_video_play_icon > circle' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_media_play_button_play_fill',
			[
				'label' => esc_html__( 'Play Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-media .twitter_video_play_icon > path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_media_play_button_play_opacity',
			[
				'label' => esc_html__( 'Opacity', 'elementskit' ),
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
					'size' => .9,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-media .twitter_video_play_icon' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_media_play_button_play_scale',
			[
				'label' => esc_html__( 'Scale', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => .5,
						'max' => 2,
						'step' => .1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => .9,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-media .twitter_video_play_icon' => 'transform : scale({{SIZE}});',
				],
			]
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_twitter_feed_comments_box_header_play_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_media_play_button_circle_fill_hover',
			[
				'label' => esc_html__( 'Circle Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1DA1F2',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-media:hover .twitter_video_play_icon > circle' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_media_play_button_play_fill_hover',
			[
				'label' => esc_html__( 'Play Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-media:hover .twitter_video_play_icon > path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_media_play_button_play_opacity_hover',
			[
				'label' => esc_html__( 'Opacity', 'elementskit' ),
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
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-media:hover .twitter_video_play_icon' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_twitter_feed_comments_box_media_play_button_play_scale_hover',
			[
				'label' => esc_html__( 'Scale', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => .5,
						'max' => 2,
						'step' => .1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-twitter-media:hover .twitter_video_play_icon' => 'transform : scale({{SIZE}});',
				],
			]
		);


        $this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->insert_pro_message();
	}

	public function str_check($textData = ''){
        $stringText = '';
        if(strlen($textData) > 5){
            $explodeText = explode(' ', trim($textData));
             for($st = 0 ; $st < count($explodeText) ; $st++){
                $pos = stripos(trim($explodeText[$st]), '#');
                $pos1 = stripos(trim($explodeText[$st]), '@');
				$poshttp = stripos(trim($explodeText[$st]), 'http');
				$poshttps = stripos(trim($explodeText[$st]), 'https');

                if($pos !== false){
                    $stringText .= '<a href="'. esc_url('https://twitter.com/hashtag/'.str_replace('#', '', $explodeText[$st]).'?src=hash') .'" target="_blank"> '.$explodeText[$st].' </a>';
                }else if($pos1 !== false){
                    $stringText .= '<a href="'. esc_url('https://twitter.com/'.$explodeText[$st].'/') .'" target="_blank"> '. $explodeText[$st].' </a>';
                }else if($poshttp !== false || $poshttps !== false){
                    $stringText .= '<a href="'.$explodeText[$st].'" target="_blank"> '. $explodeText[$st].' </a>';
                }else{
                    $stringText .= ' '.$explodeText[$st];
                }
            }
        }

        return $stringText;
	}

	protected function unit_converter($unit) {
		$convert_reaction = 0;
		$reaction_suffix = '';

		if ($unit >= 0 && $unit < 10000) {
			$convert_reaction = number_format($unit);
		}else if ($unit >= 10000 && $unit < 1000000) {
			$convert_reaction = round(floor($unit / 1000), 1);
			$reaction_suffix = 'K';
		}else if ($unit >= 1000000 && $unit < 100000000) {
			$convert_reaction = round(($unit / 1000000), 1);
			$reaction_suffix = 'M';
		}else if ($unit >= 100000000 && $unit < 1000000000) {
			$convert_reaction = round(floor($unit / 100000000), 1);
			$reaction_suffix = 'B';
		}else if($unit >= 1000000000){
			$convert_reaction = round(floor($unit / 1000000000), 1);
			$reaction_suffix = 'T';
		}

		return $convert_reaction.''.$reaction_suffix;
	}


	protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {
		   $settings = $this->get_settings_for_display();
		   extract($settings);

		   $config 			  = Handler::get_data();

		   $setting = new \Ekit_twitter_settings(  );
		   $setting->setup($config);
		   //$timeline = $setting->timeline_feed_user($ekit_twitter_feed_show_post);
			$timeline = $setting->_user_titmeline($ekit_twitter_feed_show_post);
			
		   $styleLayout = $ekit_twitter_feed_layout_style;
		   $columnFeed = 'ekit-col-12';
		   if(($styleLayout != 'ekit-layout-list') && ($styleLayout != 'ekit-layout-masonary')){
			   $columnFeed = $ekit_twitter_feed_column_grid;
		   }
		   $masnory_layout = '';
		   if (($styleLayout == 'ekit-layout-masonary')) {
				$masnory_layout = $ekit_twitter_feed_column_grid;
		   }
		   ?>
		   <?php
				if( isset($timeline['errors']) ){
				?>
					<p class="ekit-twitter-error"> <?php esc_html_e( isset($timeline['errors'][0]['message']) ? $timeline['errors'][0]['message'] : 'Invalid author', 'elementskit'); ?> </p>
				<?php
					return ;
				}
				if( isset($timeline['error']) ){
				?>
					<p class="ekit-twitter-error"> <?php esc_html_e( isset($timeline['error']) ? $timeline['error'] : 'Invalid author', 'elementskit'); ?> </p>
				<?php
					return ;
				}
			?>
				
		   <?php

			$profileInfo = $timeline[0]['user'];
			$profileName = $profileInfo['name'];
			$profileScName = $profileInfo['screen_name'];
			$statusUrlPrefix = esc_url( 'https://twitter.com/' . $profileScName . '/status/' );

		   if($ekit_twitter_feed_show_profile == 'yes'){
				$profileImage = str_replace( '_normal', '_400x400', $profileInfo['profile_image_url_https']);
				$backgroundImage = $profileInfo['profile_banner_url'];
				$tweet_count = $this->unit_converter($profileInfo['statuses_count']);
                $following_count = $this->unit_converter($profileInfo['friends_count']);
                $followers_count = $this->unit_converter($profileInfo['followers_count']);
                $fav_count = $this->unit_converter($profileInfo['favourites_count']);

			?>
			<div class="ekit-twitter-user-timeline">
				<?php if($ekit_twitter_feed_show_cover_photo == 'yes'): ?>
				<div class="ekit-twitter-feed-header-banner-container ">
					<img src="<?php echo esc_url($backgroundImage);?>" alt="<?php echo esc_html($profileName );?>">
				</div><!-- .ekit-twitter-feed-header-banner-container END -->
		   		<?php endif; ?>
				<div class="ekit-twitter-feed-profile-info-wraper">
					<div class="ekit-twitter-feed-header-user-info-container">
						<div class="ekit-twitter-feed-header-user">
							<div class="ekit-twitter-feed-header-user-image-container">
								<a class="<?php echo esc_attr($ekit_twitter_feed_author_style); ?>" href="<?php echo esc_url('https://twitter.com/'.$profileScName);?>" target="_blank">
									<img src="<?php echo esc_url($profileImage);?>" alt="<?php echo esc_html($profileScName);?>">
								</a>
							</div>
							<div class="ekit-twitter-feed-header-user-info">
								<a class="ekit-twitter-fullname" href="<?php echo esc_url('https://twitter.com/'.$profileScName);?>">
									<span class="name"><?php echo esc_html($profileName );?></span>
									<?php if($profileInfo['verified']): ?>
									<span class="twitter-verified-bdage">
										<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 268c0 17.9-4.3 34.5-12.9 49.7-8.6 15.2-20.1 27.1-34.6 35.4.4 2.7.6 6.9.6 12.6 0 27.1-9.1 50.1-27.1 69.1-18.1 19.1-39.9 28.6-65.4 28.6-11.4 0-22.3-2.1-32.6-6.3-8 16.4-19.5 29.6-34.6 39.7-15 10.2-31.5 15.2-49.4 15.2-18.3 0-34.9-4.9-49.7-14.9-14.9-9.9-26.3-23.2-34.3-40-10.3 4.2-21.1 6.3-32.6 6.3-25.5 0-47.4-9.5-65.7-28.6-18.3-19-27.4-42.1-27.4-69.1 0-3 .4-7.2 1.1-12.6-14.5-8.4-26-20.2-34.6-35.4C4.3 302.5 0 285.9 0 268c0-19 4.8-36.5 14.3-52.3 9.5-15.8 22.3-27.5 38.3-35.1-4.2-11.4-6.3-22.9-6.3-34.3 0-27 9.1-50.1 27.4-69.1 18.3-19 40.2-28.6 65.7-28.6 11.4 0 22.3 2.1 32.6 6.3 8-16.4 19.5-29.6 34.6-39.7C221.6 5.1 238.1 0 256 0s34.4 5.1 49.4 15.1c15 10.1 26.6 23.3 34.6 39.7 10.3-4.2 21.1-6.3 32.6-6.3 25.5 0 47.3 9.5 65.4 28.6 18.1 19.1 27.1 42.1 27.1 69.1 0 12.6-1.9 24-5.7 34.3 16 7.6 28.8 19.3 38.3 35.1C507.2 231.5 512 249 512 268zm-266.9 77.1l105.7-158.3c2.7-4.2 3.5-8.8 2.6-13.7-1-4.9-3.5-8.8-7.7-11.4-4.2-2.7-8.8-3.6-13.7-2.9-5 .8-9 3.2-12 7.4l-93.1 140-42.9-42.8c-3.8-3.8-8.2-5.6-13.1-5.4-5 .2-9.3 2-13.1 5.4-3.4 3.4-5.1 7.7-5.1 12.9 0 5.1 1.7 9.4 5.1 12.9l58.9 58.9 2.9 2.3c3.4 2.3 6.9 3.4 10.3 3.4 6.7-.1 11.8-2.9 15.2-8.7z" fill="#1da1f2"/></svg>
									</span>
									<?php endif;?>
								</a>
								<a class="ekit-twitter-screenname" href="<?php echo esc_url('https://twitter.com/'.$profileScName);?>">
									<span class="screen_name">@<?php echo esc_html($profileScName);?>
									</span>
								</a>
							</div>
						</div>
						<div class="ekit-twitter-feed-header-user-info-follow">
							<a rel="nofollow" href="<?php echo esc_url('https://twitter.com/intent/follow?screen_name='.$profileScName);?>">
								<?php \Elementor\Icons_Manager::render_icon( $settings['ekit_twitter_follow_btn_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								<span class="follow-button"><?php echo esc_html( $settings['ekit_twitter_follow_btn_text'] ); ?></span>
							</a>
						</div>
					</div>
					<?php if($ekit_twitter_feed_show_statics == 'yes') : ?>
					<div class="ekit-twitter-feed-header-statistics">
						<p class="ekit-twitter-tweet-count"><?php esc_html_e('Tweets', 'elementskit');?> <strong><?php echo esc_html($tweet_count);?></strong></p>
						<p class="ekit-twitter-tweet-count"> <?php esc_html_e('Following', 'elementskit');?><strong><?php echo esc_html($following_count);?></strong></p>
						<p class="ekit-twitter-tweet-count"><?php esc_html_e('Followers', 'elementskit');?> <strong><?php echo esc_html($followers_count);?></strong></p>
						<p class="ekit-twitter-tweet-count"><?php esc_html_e('Likes', 'elementskit');?> <strong><?php echo esc_html($fav_count);?></strong></p>
					</div>
		   			<?php endif; ?>
				</div><!-- .ekit-twitter-feed-profile-info-wraper END -->
			</div>
			<?php
			} ?>
				<div class="ekit-twitter-feed ">
					<div class="ekit-row <?php echo esc_attr($styleLayout .' '. $masnory_layout); ?>">
					<?php
					if(is_array($timeline) && sizeof($timeline) > 0):
						foreach($timeline AS $twitter):
							$user = isset($twitter['user']) ? $twitter['user'] : [];
							$entities = isset($twitter['entities']) ? $twitter['entities'] : [];
							$extended_entities = isset($twitter['extended_entities']) ? $twitter['extended_entities'] : [];

							$media = isset($entities['media'][0]['media_url_https']) ?  $entities['media'][0]['media_url_https'] : '';

							$tweet_url = 'https://twitter.com/i/web/status/' . $twitter['id'];
							?>
								<div class="ekit-twitter-feed-column <?php echo esc_attr($columnFeed); ?>">
									<div class="ekit-twitter-feed-content-wraper">
										<?php if($ekit_twitter_feed_show_author == 'yes'):?>
										<div class="ekit-twitter-feed-header">
											<!--Start author infomation -->
											<div class="ekit-twitter-feed-author">
												<!--Start author image -->
												<?php if( in_array($ekit_twitter_feed_author_type, ['both', 'only-profile']) ):?>
												<div class="ekit-twitter-profile-picture <?php echo esc_attr($ekit_twitter_feed_author_style); ?>">
													<img src="<?php echo esc_url(str_replace( '_normal', '_400x400', $user['profile_image_url_https']));?>" alt="<?php echo esc_attr($user['screen_name']);?>">
												</div>
												<?php endif; ?>
												<!--end author image -->
												<div class="ekit-twitter-feed-posts-item-user">
													<!--Start author name -->
													<?php if( in_array($ekit_twitter_feed_author_type, ['both', 'only-name']) ):?>
													<div class="profile-display-name">
														<a href="<?php echo esc_url('https://twitter.com/'.$user['screen_name'] );?>" class="fullname">
															<span><?php echo esc_html($user['name'] );?></span>
															<?php if($user['verified']): ?>
															<span class="twitter-verified-bdage">
																<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 268c0 17.9-4.3 34.5-12.9 49.7-8.6 15.2-20.1 27.1-34.6 35.4.4 2.7.6 6.9.6 12.6 0 27.1-9.1 50.1-27.1 69.1-18.1 19.1-39.9 28.6-65.4 28.6-11.4 0-22.3-2.1-32.6-6.3-8 16.4-19.5 29.6-34.6 39.7-15 10.2-31.5 15.2-49.4 15.2-18.3 0-34.9-4.9-49.7-14.9-14.9-9.9-26.3-23.2-34.3-40-10.3 4.2-21.1 6.3-32.6 6.3-25.5 0-47.4-9.5-65.7-28.6-18.3-19-27.4-42.1-27.4-69.1 0-3 .4-7.2 1.1-12.6-14.5-8.4-26-20.2-34.6-35.4C4.3 302.5 0 285.9 0 268c0-19 4.8-36.5 14.3-52.3 9.5-15.8 22.3-27.5 38.3-35.1-4.2-11.4-6.3-22.9-6.3-34.3 0-27 9.1-50.1 27.4-69.1 18.3-19 40.2-28.6 65.7-28.6 11.4 0 22.3 2.1 32.6 6.3 8-16.4 19.5-29.6 34.6-39.7C221.6 5.1 238.1 0 256 0s34.4 5.1 49.4 15.1c15 10.1 26.6 23.3 34.6 39.7 10.3-4.2 21.1-6.3 32.6-6.3 25.5 0 47.3 9.5 65.4 28.6 18.1 19.1 27.1 42.1 27.1 69.1 0 12.6-1.9 24-5.7 34.3 16 7.6 28.8 19.3 38.3 35.1C507.2 231.5 512 249 512 268zm-266.9 77.1l105.7-158.3c2.7-4.2 3.5-8.8 2.6-13.7-1-4.9-3.5-8.8-7.7-11.4-4.2-2.7-8.8-3.6-13.7-2.9-5 .8-9 3.2-12 7.4l-93.1 140-42.9-42.8c-3.8-3.8-8.2-5.6-13.1-5.4-5 .2-9.3 2-13.1 5.4-3.4 3.4-5.1 7.7-5.1 12.9 0 5.1 1.7 9.4 5.1 12.9l58.9 58.9 2.9 2.3c3.4 2.3 6.9 3.4 10.3 3.4 6.7-.1 11.8-2.9 15.2-8.7z" fill="#1da1f2"/></svg>
															</span>
															<?php endif;?>
														</a>
														<div class="ekit-twitter-feed-item-user-screen-name">
															<a href="<?php echo 'https://twitter.com/'.esc_html($user['screen_name'])  ;?>" class="screen_name">
																<span>@<?php echo esc_html($user['screen_name'] );?></span>
															</a>
															<!--start date -->
															<?php if($ekit_twitter_feed_show_post_date == 'yes'): ?>
															<span class="ekit-twitter-feed-item-user-date">
																<span><?php echo esc_html(date(get_option('date_format'), strtotime($twitter['created_at']) ));?></span>
															</span>
															<?php endif;?>
															<!--end date -->
														</div>
													</div>
													<?php endif; ?>
													<!--end author name -->
												</div>
											</div>
											<!--end author infomation -->
											<div class="ekit-twitter-logo">
												<a href="<?php echo $statusUrlPrefix . $twitter['id_str']; ?>" target="_blank"><i class="icon icon-twitter"></i></a>
											</div>
										</div>
										<?php endif;?>

										<!--Start test description-->
										<p class="feed-title"><?php echo \ElementsKit_Lite\Utils::render($this->str_check($twitter['text']));?> </p>
										<?php
										if($ekit_twitter_feed_show_media == 'yes'):
											if(!empty($media)):
												$type = isset($extended_entities['media'][0]['type']) ?  $extended_entities['media'][0]['type'] : 'photo';
											?>
											<div class="ekit-twitter-media">
												<a href="<?php echo  $statusUrlPrefix . $twitter['id_str']; ?>" target="_blank">
													<img src="<?php echo esc_url($media);?>" alt="<?php echo esc_html($user['name'] );?>" />
													<?php if($type == 'video') : ?>
														<!-- video content here -->
														<div class="video_content">
															<svg xmlns="https://www.w3.org/2000/svg" class="twitter_video_play_icon" viewBox="0 0 21.5 21.5"><circle cx="10.8" cy="10.8" r="10"/><path d="M14.8 10.3l-6-3.8c-.2-.1-.6-.1-.7.2-.1 0-.1.1-.1.2v7.6c0 .3.2.5.5.5.1 0 .2 0 .3-.1l6-3.8c.1-.1.2-.3.2-.4s-.1-.3-.2-.4z"/><path d="M10.8 21.5C4.8 21.5 0 16.7 0 10.8S4.8 0 10.8 0s10.8 4.8 10.8 10.8-4.9 10.7-10.8 10.7zm0-20c-5.1 0-9.3 4.2-9.3 9.3S5.7 20 10.8 20s9.3-4.1 9.3-9.3-4.2-9.2-9.3-9.2z"/></svg>
														</div>
														<!-- video content end -->
													<?php endif; ?>
												</a>
											</div>
											<?php
											endif;
										endif;
										?>
										<!--end test description-->


										<?php if($ekit_twitter_feed_show_hash == 'yes' && sizeof($entities['hashtags']) > 0):
											$hashtags = $entities['hashtags'];
											?>
											<!--Start hash tags-->
											<p class="feed-title-hash">
											<?php if(is_array($hashtags)){
												foreach($hashtags AS $tags):
											?>
												<a href="<?php echo esc_url('https://twitter.com/hashtag/'.$tags['text'].'?src=hash');?>" target="_blank"> <?php echo esc_html($tags['text']); ?> </a>
											<?php
												endforeach;
											} ?>
											</p>
											<!--End hash tags-->
										<?php endif;?>

										<!--Start read more-->

										<?php if($ekit_twitter_feed_show_read_more == 'yes'):?>
										<div class="read-more-button">
											<a href="<?php echo $statusUrlPrefix . $twitter['id_str']; ?>" target="_blank"> <?php esc_html_e('More', 'elementskit'); ?> </a>
										</div>
										<?php endif;?>
										<!--End read more-->

										<!--Start comments sections-->
										<?php if($ekit_twitter_feed_comments_box == 'yes'): ?>
											<div class="ekit-twitter-feed-comments">
												<a href="<?php echo esc_url('https://twitter.com/intent/tweet?in_reply_to=' . $twitter['id_str'] . '&related=' . $user['screen_name']);?>" title="Comments" class="ekit-twitter-comments"><i class="icon icon-comment1"></i></a>

												<a href="<?php echo esc_url('https://twitter.com/intent/retweet?tweet_id=' . $twitter['id_str'] . '&related=' . $user['screen_name']);?>" title="Retweet" class="ekit-twitter-retweet"><i class="icon icon-retweet"></i><strong><?php echo esc_attr($this->unit_converter($twitter['retweet_count']));?></strong> </a>

												<a href="<?php echo esc_url('https://twitter.com/intent/like?tweet_id=' . $twitter['id_str'] . '&related=' . $user['screen_name']);?>" title="Like" class="ekit-twitter-like"><i class="icon icon-heart-shape-outline"></i><strong><?php echo esc_attr($this->unit_converter($twitter['favorite_count']));?></strong> </a>

												<?php if($ekit_twitter_feed_share_show == 'yes'): ?>
													<div class="ekit-twitter-feed-share">
														<i class="icon icon-share-3"></i>
														<span class='medium-text'> <?php echo esc_html__( 'Share', 'elementskit') ?> </span>
														<div class="ekit-twitter-feed-share__menu">
															<a href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u=' . $tweet_url); ?>&display=popup&ref=plugin&src=post" title="Share" target="_blank" class="ekit-twitter-feed-share__with-fb">
																<i class="icon icon-facebook"></i>
																<span> <?php echo esc_html__( 'Share on Facebook', 'elementskit' ) ?> </span>
															</a>

															<?php 
															$twitter_params = 
															'?text=Share with twitter+-' .
															'&amp;url=' . urlencode($tweet_url) . 
															'&amp;counturl=' . urlencode($tweet_url) .
															'';
															?>
															<a href="<?php echo 'https://twitter.com/share' . $twitter_params; ?>" title="Share" target="_blank" class="ekit-twitter-feed-share__with-tw">
																<i class="icon icon-twitter"></i>
																<span><?php echo esc_html__( 'Share on Twitter', 'elementskit' ) ?></span>
															</a>
															<a href="<?php echo esc_url( 'https://pinterest.com/pin/create/button/?url='. $tweet_url .'& media='. $media .'& description=Share+With+Pinterest' ); ?>" title="Share" target="_blank" class="ekit-twitter-feed-share__with-pin">
																<i class="icon icon-pinterest"></i>
																<span> <?php echo esc_html__( 'Share on Pinterest', 'elementskit' ) ?> </span>
															</a>
														</div>
													</div>
												<?php endif;?>
											</div>
										<?php endif;?>
										<!--End comments sections-->
									</div>
								</div>
							<?php
						endforeach;
					endif;
					?>
				</div>
			</div>
		   <?php

	}
}
