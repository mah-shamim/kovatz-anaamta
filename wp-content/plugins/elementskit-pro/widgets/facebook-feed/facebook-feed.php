<?php

namespace Elementor;

use \Elementor\ElementsKit_Widget_Facebook_Feed_Handler as Handler;

defined('ABSPATH') || exit;


/**
 * Class ElementsKit_Widget_Facebook_Feed
 *
 * This facebook feed need to must have user_posts permission to work properly
 *
 *
 * @package Elementor
 */
class ElementsKit_Widget_Facebook_Feed extends Widget_Base {
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
        return 'https://wpmet.com/doc/facebook-feed-api/';
    }

	protected function register_controls() {

		$setting = new \Ekit_facebook_settings();


		/*Layout Settings*/
		$this->start_controls_section(
			'ekit_facebook_feed_layout_settings', [
				'label' => esc_html__('Layout Settings ', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_facebook_feed_layout_style',
			[
				'label'   => esc_html__('Grid Style', 'elementskit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ekit-layout-grid',
				'options' => [
					'ekit-layout-list'     => esc_html__('List', 'elementskit'),
					'ekit-layout-grid'     => esc_html__('Grid', 'elementskit'),
					'ekit-layout-masonary' => esc_html__('Masonary', 'elementskit'),
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_column_grid',
			[
				'label'     => esc_html__('Columns Grid', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'frontend_available' => true,
				'default'   => 'ekit-fb-col-4',
				'tablet_default'   => 'ekit-fb-col-6',
				'mobile_default'   => 'ekit-fb-col-12',
				'options'   => [
					'ekit-fb-col-12' => esc_html__('1 Columns', 'elementskit'),
					'ekit-fb-col-6' => esc_html__('2 Columns', 'elementskit'),
					'ekit-fb-col-4' => esc_html__('3 Columns', 'elementskit'),
					'ekit-fb-col-3' => esc_html__('4 Columns', 'elementskit'),
					'ekit-fb-col-2' => esc_html__('6 Columns', 'elementskit'),
				],
				'condition' => ['ekit_facebook_feed_layout_style!' => 'ekit-layout-list'],
			]
		);

		$this->add_control(
			'ekit_facebook_feed_style_choose',
			[
				'label'     => esc_html__('Feed Style', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'classic',
				'options'   => [
					'classic' => esc_html__('Classic', 'elementskit'),
					'photos'  => esc_html__('Photos', 'elementskit'),
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'ekit_facebook_feed_show_post',
			[
				'label'   => esc_html__('Show Post', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'default' => 9,
				'min'     => 1,
				'max'     => 100,
				'step'    => 1,

			]
		);

		$this->end_controls_section();
		/* End layout settings*/

		/*Display Settings*/
		$this->start_controls_section(
			'ekit_facebook_feed_display_settings', [
				'label'     => esc_html__('Display Settings ', 'elementskit'),
				'condition' => [
					'ekit_facebook_feed_style_choose' => 'classic',
				],
			]
		);

		$this->add_control(
			'ekit_facebook_feed_show_author',
			[
				'label'        => esc_html__('Show Author', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Show', 'elementskit'),
				'label_off'    => esc_html__('Hide', 'elementskit'),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'ekit_facebook_feed_author_type',
			[
				'label'     => esc_html__('Author Settings', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'both',
				'options'   => [
					'only-profile' => esc_html__('Only Profile Image', 'elementskit'),
					'only-name'    => esc_html__('Only Name', 'elementskit'),
					'both'         => esc_html__('Both', 'elementskit'),
				],
				'condition' => [
					'ekit_facebook_feed_show_author' => 'yes',
				],
			]
		);
		$this->add_control(
			'ekit_facebook_feed_author_style',
			[
				'label'     => esc_html__('Author Style', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'circle',
				'options'   => [
					'circle' => esc_html__('Circle', 'elementskit'),
					'square' => esc_html__('Square', 'elementskit'),
				],
				'condition' => [
					'ekit_facebook_feed_author_type!' => 'only-name',
				],
			]
		);
		$this->add_control(
			'ekit_facebook_feed_show_post_date',
			[
				'label'        => esc_html__('Show Date', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Show', 'elementskit'),
				'label_off'    => esc_html__('Hide', 'elementskit'),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control( 'image_position', [
			'label'     => esc_html__('Image position', 'elementskit'),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'center',
			'options'   => [
				'center' => esc_html__('Center', 'elementskit'),
				'top' => esc_html__('Top', 'elementskit'),
				'left' => esc_html__('Left', 'elementskit'),
				'background' => esc_html__('Backgound', 'elementskit')
			]
		]);

		$this->add_control( 'image_bg_style',[
			'label'     => esc_html__('Background Style', 'elementskit'),
			'type'      => Controls_Manager::SELECT,
			'default'   => 's1',
			'options'   => [
				's1' => esc_html__('Style 1', 'elementskit'),
				's2' => esc_html__('Style 2', 'elementskit')
			],
			'condition' => [
				'image_position' => 'background'
			]
		]);

		$this->add_control(
			'header_menu_type',
			[
				'label'     => esc_html__('Header Menu', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => [
					'none' => esc_html__('None', 'elementskit'),
					'arrow' => esc_html__('Arrow', 'elementskit'),
					'dot' => esc_html__('Three Dot', 'elementskit')
				]
			]
		);

		$this->add_control( 'footer_heading', [
			'label'        => esc_html__('Footer', 'elementskit'),
			'type'         => Controls_Manager::HEADING,
			'separator'		=> 'before'
		]);

		$this->add_control( 'show_reaction', [
			'label'        => esc_html__('Like and Comment', 'elementskit'),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'no',
		]);

		$this->add_control( 'reaction_style', [
			'label'     => esc_html__('Like and Comment Style', 'elementskit'),
			'type'      => Controls_Manager::SELECT,
			'label_block' => true,
			'default'   => 's1',
			'options'   => [
				's1' => esc_html__('Style 01', 'elementskit'),
				's2' => esc_html__('Style 02', 'elementskit'),
			],
			'condition' => [
				'show_reaction' => 'yes'
			]
		]);

		$this->add_control(
			'show_share_and_view',
			[
				'label'        => esc_html__('Share and View', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes'
			]
		);

		// $this->add_control(
		// 	'ekit_facebook_feed_show_comments',
		// 	[
		// 		'label'        => esc_html__('Show Comments', 'elementskit'),
		// 		'type'         => Controls_Manager::SWITCHER,
		// 		'label_on'     => esc_html__('Show', 'elementskit'),
		// 		'label_off'    => esc_html__('Hide', 'elementskit'),
		// 		'return_value' => 'yes',
		// 		'default'      => 'no',
		// 	]
		// );

		$this->end_controls_section();
		/* End display settings*/

		/* Start style settings */
		$this->start_controls_section(
			'ekit_facebook_feed_layout_style_tab',
			[
				'label' => esc_html__('Layout', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_layout_gutter',
			[
				'label'      => esc_html__('Gutter', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px',],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-single-fb-feed-holder' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ekit-layout-grid '          => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'ekit_facebook_feed_layout_style!' => 'ekit-layout-masonary',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_layout_gutter_masnory',
			[
				'label'      => esc_html__('Gutter', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px',],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-layout-masonary' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'ekit_facebook_feed_layout_style' => 'ekit-layout-masonary',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_layout_margin_bottom',
			[
				'label'      => esc_html__('Margin Bottom', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-single-fb-feed' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control( 'widget_padding', [
			'label'      => esc_html__('Padding', 'elementskit'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .ekit-facebook-feed' => 
					'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			]
		]);

		$this->end_controls_section();

		/** start container style */
		$this->start_controls_section(
			'ekit_facebook_feed_container_style_tab',
			[
				'label' => esc_html__('Container', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'ekit_facebook_feed_container_border',
				'label'    => esc_html__('Border', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-single-fb-feed',
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_container_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'default'	=> [
					'top'      => '30', 'right'    => '30',
					'bottom'   => '30', 'left'     => '30',
					'unit'     => 'px', 'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-single-fb-feed' => 'padding-bottom:0;',
					'{{WRAPPER}} .ekit-single-fb-feed__bottom-padding' => 'padding-bottom: calc({{BOTTOM}}{{UNIT}} / 2);',
					'{{WRAPPER}} .ekit-single-fb-feed:not(.ekit_fb_photo_gallery)' => 'padding-left:0;padding-right:0;padding-top:0;',
					'{{WRAPPER}} .ekit-fb-feed-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-fb-feed-header-menu' => 'top:{{TOP}}{{UNIT}};right:{{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-fb-fotter-section, {{WRAPPER}} .ekit-fb-feed-share, {{WRAPPER}} .ekit-fb-feed-status, {{WRAPPER}} .comments-show' => 
						'padding-left:{{LEFT}}{{UNIT}};padding-right:{{RIGHT}}{{UNIT}};',
				],
				'condition'  => [
					'ekit_facebook_feed_style_choose' => 'classic',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_container_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-single-fb-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('ekit_facebook_feed_container_normal_and_hover_tabs');

		$this->start_controls_tab(
			'ekit_facebook_feed_container_normal_tab',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ekit_facebook_feed_container_normal_background',
				'label'     => esc_html__('Background', 'elementskit'),
				'types'     => ['classic',],
				'selector'  => '{{WRAPPER}} .ekit-single-fb-feed',
				'exclude'   => [
					'image',
				],
				'default'   => '#FFFFFF',
				'condition' => [
					'ekit_facebook_feed_style_choose' => 'classic',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_facebook_feed_container_normal_box_shadow',
				'label'    => esc_html__('Box Shadow', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-single-fb-feed',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_facebook_feed_container_hover_tab',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ekit_facebook_feed_container_hover_background',
				'label'     => esc_html__('Background', 'elementskit'),
				'types'     => ['classic',],
				'selector'  => '{{WRAPPER}} .ekit-single-fb-feed:hover',
				'exclude'   => [
					'image',
				],
				'default'   => '#FFFFFF',
				'condition' => [
					'ekit_facebook_feed_style_choose' => 'classic',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_facebook_feed_container_hover_box_shadow',
				'label'    => esc_html__('Box Shadow', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-single-fb-feed:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'overlay_heading', [
			'label'     => esc_html__('Overlay', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
				'image_position' => 'background',
			]
		]);

		$this->start_controls_tabs('overlay_tabs', [
			'condition' => [
				'image_position' => 'background',
			]
		]);

		$this->start_controls_tab( 'overlay_tab', [
			'label' => esc_html__('Normal', 'elementskit'),
		]);

		$this->add_responsive_control( 'overlay_background', [
			'label'     => esc_html__('Overlay Background', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .ekit-wid-con .ekit-single-fb-feed.image-in-background::before' => 
					'background-image:linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, {{VALUE}} 100%);',
				'{{WRAPPER}} .ekit-wid-con .ekit-single-fb-feed.image-in-background::after' => 
					'background-image:linear-gradient(180deg, {{VALUE}} 0%, {{VALUE}} 100%);',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab( 'overlay_tab_hover', [
			'label' => esc_html__('Hover', 'elementskit'),
		]);

		$this->add_responsive_control( 'overlay_background_hover', [
			'label'     => esc_html__('Overlay Background', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .ekit-wid-con .ekit-single-fb-feed.image-in-background::before' => 
					'background-image:linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, {{VALUE}} 100%);',
				'{{WRAPPER}} .ekit-wid-con .ekit-single-fb-feed.image-in-background::after' => 
					'background-image:linear-gradient(180deg, {{VALUE}} 0%, {{VALUE}} 100%);',
			],
		]);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
		/** end container */

		/** start header */
		$this->start_controls_section(
			'ekit_facebook_feed_header_style_tab',
			[
				'label'     => esc_html__('Header', 'elementskit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_facebook_feed_style_choose' => 'classic',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_margin',
			[
				'label'      => esc_html__('Header Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-fb-feed-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_padding',
			[
				'label'      => esc_html__('Header Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-fb-feed-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_normal_color',
			[
				'label'     => esc_html__('User Name', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#14223B',
				'selectors' => [
					'{{WRAPPER}} .ekit-fb-feed-profile-info .user-name' => 'color: {{VALUE}}',
				],
				'condition' => [
					'image_position!' => 'background'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_normal_color_img_in_bg',
			[
				'label'     => esc_html__('User Name', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'white',
				'selectors' => [
					'{{WRAPPER}} .facebook-feed-card.image-in-background .ekit-fb-feed-profile-info .user-name' => 'color: {{VALUE}}',
				],
				'condition' => [
					'image_position' => 'background'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_date_color',
			[
				'label'     => esc_html__('Date Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#868B94',
				'selectors' => [
					'{{WRAPPER}} .ekit-fb-feed-header .ekit-fb-post-publish-date' => 'color: {{VALUE}}',
				],
				'condition' => [
					'image_position!' => 'background'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_date_color_img_in_bg',
			[
				'label'     => esc_html__('Date Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'white',
				'selectors' => [
					'{{WRAPPER}} .facebook-feed-card.image-in-background .ekit-fb-feed-header .ekit-fb-post-publish-date' => 'color: {{VALUE}}',
				],
				'condition' => [
					'image_position' => 'background'
				]
			]
		);

		// $this->start_controls_tabs('ekit_facebook_feed_header_normal_and_hover_tabs');

		// $this->start_controls_tab(
		// 	'ekit_facebook_feed_header_normal_tab',
		// 	[
		// 		'label' => esc_html__('Normal', 'elementskit'),
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'ekit_facebook_feed_header_normal_color',
		// 	[
		// 		'label'     => esc_html__('User Name', 'elementskit'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'default'   => '#14223B',
		// 		'selectors' => [
		// 			'{{WRAPPER}} .ekit-fb-feed-profile-info .user-name' => 'color: {{VALUE}}',
		// 		],
		// 	]
		// );

		// $this->end_controls_tab();

		// $this->start_controls_tab(
		// 	'ekit_facebook_feed_header_hover_tab',
		// 	[
		// 		'label' => esc_html__('Hover', 'elementskit'),
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'ekit_facebook_feed_header_hover_color',
		// 	[
		// 		'label'     => esc_html__('User Name', 'elementskit'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'default'   => '#365899',
		// 		'selectors' => [
		// 			'{{WRAPPER}} .ekit-fb-feed-profile-info .user-name:hover' => 'color: {{VALUE}}',
		// 		],
		// 	]
		// );

		// $this->end_controls_tab();
		// $this->end_controls_tabs();

		$this->add_control(
			'ekit_facebook_feed_header_heading',
			[
				'label'     => esc_html__('Content', 'elementskit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_content_color',
			[
				'label'     => esc_html__('Content Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#70757E',
				'selectors' => [
					'{{WRAPPER}} .ekit-fb-feed-status' => 'color: {{VALUE}}',
				],
				'condition' => [
					'image_position!' => 'background'
				]
			]
		);
		$this->add_responsive_control(
			'ekit_facebook_feed_header_content_color_img_in_bg',
			[
				'label'     => esc_html__('Content Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'white',
				'selectors' => [
					'{{WRAPPER}} .facebook-feed-card.image-in-background .ekit-fb-feed-status' => 'color: {{VALUE}}',
				],
				'condition' => [
					'image_position' => 'background'
				]
			]
		);

		$this->end_controls_section();
		/** end header style */

		/**
		 * Start User Profile Thumbnail style
		 */

		$this->start_controls_section(
			'profile_thumbnail_section',
			[
				'label' => esc_html__('Profile Thumbnail', 'elementskit'),
			]
		);

		$this->add_responsive_control( 'profile_thumbnail_size', [
			'label'      => esc_html__('Size', 'elementskit'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['px'],
			'range'      => [
				'px' => [ 'min'  => 24, 'max'  => 96, 'step' => 1 ],
			],
			'default'    => [ 'unit' => 'px', 'size' => 40 ],
			'selectors'  => [
				'{{WRAPPER}} .ekit-wid-con .ekit-fb-feed-profile-thumb > a' => 
					'height:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				'profile_thumbnail_show_outline!' => 'yes'
			],
		]);

		$this->add_responsive_control( 'profile_thumbnail_size_has_outline', [
			'label'      => esc_html__('Size', 'elementskit'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['px'],
			'range'      => [
				'px' => [ 'min'  => 24, 'max'  => 96, 'step' => 1 ],
			],
			'default'    => [ 'unit' => 'px', 'size' => 50 ],
			'selectors'  => [
				'{{WRAPPER}} .ekit-wid-con .ekit-fb-feed-profile-thumb > a' => 
					'height:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				'profile_thumbnail_show_outline' => 'yes'
			],
		]);

		$this->add_control(
			'profile_thumbnail_outline_heading',
			[
				'label'        => esc_html__('Outline', 'elementskit'),
				'type'         => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'profile_thumbnail_show_outline',
			[
				'label'        => esc_html__('Show Outline', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();
		// End User Profile Thumbnail

		/**
		 * Post Image
		 */
		$this->start_controls_section( 'image_style_section', [
			'label' => esc_html__('Image', 'elementskit'),
			'tab' => Controls_Manager::TAB_STYLE
		]);

		$this->add_responsive_control( 'image_padding', [
			'label'      => esc_html__('Padding', 'elementskit'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .ekit-fb-feed-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			]
		]);

		$this->end_controls_section();
		// End post image

		/**
		 * Footer settings
		 */

		$this->start_controls_section( 'footer_section', [
			'label' => esc_html__('Icons', 'elementskit')
		]);

		$this->add_control( 'reactions_heading', [
			'label'		=> esc_html__('Reactions', 'elementskit'),
			'type'		=> Controls_Manager::HEADING,
			'condition'  => [
				'show_reaction' => 'yes'
			]
		]);

		$this->add_control( 'like_icons', [
			'label' => esc_html__( 'Like Icon', 'elementskit' ),
			'label_block' => true,
			'type' => Controls_Manager::ICONS,
			'fa4compatibility' => 'like_icon',
			'default' => [
				'value' => 'fas fa-thumbs-up',
				'library' => 'fa-solid',
			],
			'condition'  => [
				'show_reaction' => 'yes'
			]
		]);
	
		$this->add_control( 'love_icons', [
			'label' => esc_html__( 'Love Icon', 'elementskit' ),
			'label_block' => true,
			'type' => Controls_Manager::ICONS,
			'fa4compatibility' => 'love_icon',
			'default' => [
				'value' => 'fas fa-heart',
				'library' => 'fa-solid',
			],
			'condition' => [
				'show_reaction' => 'yes',
				'reaction_style' => 's1'
			]
		]);

		$this->add_control( 'comment_icons', [
			'label' => esc_html__( 'Comment Icon', 'elementskit' ),
			'label_block' => true,
			'type' => Controls_Manager::ICONS,
			'fa4compatibility' => 'comment_icon',
			'default' => [
				'value' => 'far fa-comment',
				'library' => 'fa-solid',
			],
			'condition' => [
				'reaction_style' => 's2'
			]
		]);

		$this->end_controls_section();
		// End Footer settings

		/**
		 * Start User Profile Thumbnail style
		 */

		$this->start_controls_section(
			'profile_thumbnail_style_section',
			[
				'label' => esc_html__('Profile Thumbnail', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						['name' => 'ekit_facebook_feed_author_style', 'operator' => '===', 'value' => 'square'],
						['name' => 'profile_thumbnail_show_outline', 'operator' => '===', 'value' => 'yes'],
					]
				]
			]
		);

		$this->add_responsive_control( 'profile_thumbnail_border_radius', [
			'label'      => esc_html__('Border Radius', 'elementskit'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .ekit-wid-con .ekit-fb-feed-profile-thumb > a, .ekit-wid-con .ekit-fb-feed-profile-thumb > a img' => 
					'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition' => [
				'ekit_facebook_feed_author_style' => 'square'
			]
		]);

		$this->add_control( 'profile_thumbnail_outline_style_heading', [
			'label'		=> esc_html__('Outline', 'elementskit'),
			'type'		=> Controls_Manager::HEADING,
			'condition'  => [
				'profile_thumbnail_show_outline' => 'yes'
			]
		]);

		$this->add_control( 'profile_thumbnail_outline_color', [
			'label'     => esc_html__('Color', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1261EB',
			'selectors' => [
				'{{WRAPPER}} .ekit-wid-con .ekit-fb-feed-profile-thumb > a.has-outline' => 
				'border-color: {{VALUE}}',
			],
			'condition'  => [
				'profile_thumbnail_show_outline' => 'yes'
			]
		]);

		$this->add_responsive_control( 'profile_thumbnail_outline_width', [
			'label'      => esc_html__('Width', 'elementskit'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['px'],
			'range'      => [
				'px' => [ 'min'  => 1, 'max'  => 8, 'step' => 1 ],
			],
			'default'    => [ 'unit' => 'px', 'size' => 1 ],
			'selectors'  => [
				'{{WRAPPER}} .ekit-wid-con .ekit-fb-feed-profile-thumb > a.has-outline' => 
					'border-width:{{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				'profile_thumbnail_show_outline' => 'yes'
			]
		]);

		$this->add_responsive_control( 'profile_thumbnail_outline_padding', [
			'label'      => esc_html__('Padding', 'elementskit'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'default'	=> [ 'size' => 4, 'unit' => 'px'],
			'selectors'  => [
				'{{WRAPPER}} .ekit-wid-con .ekit-fb-feed-profile-thumb > a.has-outline' => 
					'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition'  => [
				'profile_thumbnail_show_outline' => 'yes'
			],
		]);

		$this->end_controls_section();
		// End User Profile Thumbnail style


		/** style media */
		$this->start_controls_section(
			'ekit_facebook_feed_media_style_tab',
			[
				'label' => esc_html__('Media', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'ekit_facebook_feed_style_choose' => 'photos',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_media_image_height',
			[
				'label'      => esc_html__('Height', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px',],
				'range'      => [
					'px' => [
						'min'  => .5,
						'max'  => 2.5,
						'step' => .01,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => .6,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-layout-grid .ekit_fb_photo_gallery .ekit_fb_photo_link' => 'padding-bottom: calc(100% * {{SIZE}});',
				],
				'condition'  => [
					'ekit_facebook_feed_style_choose' => 'photos',
					'ekit_facebook_feed_layout_style' => 'ekit-layout-grid',
				],
			]
		);

		$this->add_control(
			'ekit_facebook_feed_media_image_show_overlay',
			[
				'label'        => esc_html__('Overlay', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Show', 'elementskit'),
				'label_off'    => esc_html__('Hide', 'elementskit'),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'ekit_facebook_feed_style_choose' => 'photos',
				],
			]
		);

		$this->start_controls_tabs(
			'ekit_facebook_feed_header_image_gallery_and_hover_tabs',
			[
				'condition' => [
					'ekit_facebook_feed_style_choose' => 'photos',
				],
			]
		);

		$this->start_controls_tab(
			'ekit_facebook_feed_header_image_gallery_tab',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_image_gallery_scale',
			[
				'label'      => esc_html__('Scale', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px',],
				'range'      => [
					'px' => [
						'min'  => .5,
						'max'  => 1.5,
						'step' => .01,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit_fb_photo_gallery .ekit_fb_photo_link .ekit_fb_photo' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_image_gallery_opaicty',
			[
				'label'      => esc_html__('Opacity', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px',],
				'range'      => [
					'px' => [
						'min'  => .5,
						'max'  => 1,
						'step' => .01,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit_fb_photo_gallery .ekit_fb_photo_link .ekit_fb_photo' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_facebook_feed_header_photo_gallery_tab',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_hover_photo_gallery_scale',
			[
				'label'      => esc_html__('Scale', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px',],
				'range'      => [
					'px' => [
						'min'  => .5,
						'max'  => 1.5,
						'step' => .01,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1.02,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit_fb_photo_gallery .ekit_fb_photo_link:hover .ekit_fb_photo' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_hover_photo_gallery_opacity',
			[
				'label'      => esc_html__('Opacity', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px',],
				'range'      => [
					'px' => [
						'min'  => .5,
						'max'  => 1,
						'step' => .01,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => .9,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit_fb_photo_gallery .ekit_fb_photo_link:hover .ekit_fb_photo' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_facebook_feed_media_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-single-fb-feed:not(.ekit_fb_photo_gallery) .ekit-fb-feed-media' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'ekit_facebook_feed_style_choose' => 'classic',
				],
			]
		);

		$this->start_controls_tabs(
			'ekit_facebook_feed_header_content_play_and_hover_tabs',
			[
				'condition' => [
					'ekit_facebook_feed_style_choose' => 'classic',
				],
			]
		);

		$this->start_controls_tab(
			'ekit_facebook_feed_header_content_play_tab',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_content_play_color',
			[
				'label'     => esc_html__('Play Icon', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-fb-video-play-button svg path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_content_play_scale',
			[
				'label'      => esc_html__('Scale', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px',],
				'range'      => [
					'px' => [
						'min'  => .9,
						'max'  => 2,
						'step' => .1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => .9,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-fb-video-play-button svg' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_facebook_feed_header_play_hover_tab',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_header_play_hover_color',
			[
				'label'     => esc_html__('Play Icon', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#365899',
				'selectors' => [
					'{{WRAPPER}} .ekit-fb-video-post:hover .ekit-fb-video-play-button svg path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_hover_play_scale',
			[
				'label'      => esc_html__('Scale', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px',],
				'range'      => [
					'px' => [
						'min'  => .9,
						'max'  => 2,
						'step' => .1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-fb-video-post:hover .ekit-fb-video-play-button svg' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
		/** end media */

		// $this->start_controls_section(
		// 	'ekit_facebook_feed_link_style_tab',
		// 	[
		// 		'label'     => esc_html__('Link', 'elementskit'),
		// 		'tab'       => Controls_Manager::TAB_STYLE,
		// 		'condition' => [
		// 			'ekit_facebook_feed_style_choose' => 'classic',
		// 		],
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'ekit_facebook_feed_link_spacing',
		// 	[
		// 		'label'      => esc_html__('Padding', 'elementskit'),
		// 		'type'       => Controls_Manager::DIMENSIONS,
		// 		'size_units' => ['px', '%', 'em'],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} .ekit-fb-link-type-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 		],
		// 	]
		// );

		// $this->add_group_control(
		// 	Group_Control_Background::get_type(),
		// 	[
		// 		'name'     => 'ekit_facebook_feed_link_background',
		// 		'label'    => esc_html__('Background', 'elementskit'),
		// 		'types'    => ['classic',],
		// 		'default'  => '#f2f3f5',
		// 		'selector' => '{{WRAPPER}} .ekit-fb-link-type-footer',
		// 		'exclude'  => [
		// 			'image',
		// 		],
		// 	]
		// );

		// $this->add_control(
		// 	'ekit_facebook_feed_link_hr',
		// 	[
		// 		'type' => Controls_Manager::DIVIDER,
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'ekit_facebook_feed_link_source_name',
		// 	[
		// 		'label'     => esc_html__('Source Color', 'elementskit'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'default'   => '#606770',
		// 		'selectors' => [
		// 			'{{WRAPPER}} .ekit-fb-link-type-footer .ekit-fb-source-name' => 'color: {{VALUE}}',
		// 		],
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'ekit_facebook_feed_link_caption_name',
		// 	[
		// 		'label'     => esc_html__('Caption Name Color', 'elementskit'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'default'   => '#1d2129',
		// 		'selectors' => [
		// 			'{{WRAPPER}} .ekit-fb-link-type-footer .ekit-fb-caption-name' => 'color: {{VALUE}}',
		// 		],
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'ekit_facebook_feed_link_caption',
		// 	[
		// 		'label'     => esc_html__('Caption Color', 'elementskit'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'default'   => '#606770',
		// 		'selectors' => [
		// 			'{{WRAPPER}} .ekit-fb-link-type-footer .ekit-fb-caption' => 'color: {{VALUE}}',
		// 		],
		// 	]
		// );

		// $this->end_controls_section();

		$this->start_controls_section(
			'status_style_section',
			[
				'label' => esc_html__('Status', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'status_max_height',
			[
				'label'      => esc_html__('Max Height', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 100,
						'max'  => 1080,
						'step' => 4,
					]
				],
				'default'    => [
					'unit' => 'px',
					'size' => 186,
				],
				'selectors'  => [
					'{{WRAPPER}} .facebook-feed-card .ekit-fb-feed-status' => 
					'max-height: {{SIZE}}{{UNIT}};',
					
				],
				'condition'  => [
					'image_position' => 'left',
				],
			]
		);

		$this->add_responsive_control(
			'status_max_height_img_in_bg',
			[
				'label'      => esc_html__('Max Height', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 100,
						'max'  => 1080,
						'step' => 4,
					]
				],
				'default'    => [
					'unit' => 'px',
					'size' => 212,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-single-fb-feed.image-in-background:hover .ekit-fb-feed-status' => 
					'max-height: {{SIZE}}{{UNIT}};',
					
				],
				'condition'  => [
					'image_position' => 'background',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'share_and_view_style_section',
			[
				'label' => esc_html__('Share and View', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('share_and_view_color_tabs');

		$this->start_controls_tab(
			'share_and_view_color_tab',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_responsive_control(
			'share_and_view_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#70757e',
				'selectors' => [
					'{{WRAPPER}} .ekit-fb-feed-share > a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-fb-feed-share > span' => 'color: {{VALUE}}'
				],
				'condition' => [
					'image_position!' => 'background'
				]
			]
		);
		$this->add_responsive_control(
			'share_and_view_color_img_in_bg',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'white',
				'selectors' => [
					'{{WRAPPER}} .facebook-feed-card.image-in-background .ekit-fb-feed-share > a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .facebook-feed-card.image-in-background .ekit-fb-feed-share > span' => 'color: {{VALUE}}'
				],
				'condition' => [
					'image_position!' => 'background'
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'share_and_view_color_tab_hover',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_responsive_control(
			'share_and_view_color_hover',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1261eb',
				'selectors' => [
					'{{WRAPPER}} .ekit-fb-feed-share > a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-fb-feed-share > span:hover' => 'color: {{VALUE}}'
				],
			]
		);
		

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();

		/** start footer style */
		$this->start_controls_section(
			'ekit_facebook_feed_footer_style_tab',
			[
				'label'     => esc_html__('Footer', 'elementskit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_facebook_feed_style_choose' => 'classic',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_footer_text_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#606770',
				'selectors' => [
					'{{WRAPPER}} .ekit-fb-fotter-section .ekit-facebook-like'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-fb-fotter-section .ekit-facebook-comments' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-fb-fotter-section .ekit-facebook-retweet'  => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_facebook_feed_footer_text_color_hover',
			[
				'label'     => esc_html__('Color Hover', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#365899',
				'selectors' => [
					'{{WRAPPER}} .ekit-fb-fotter-section .ekit-facebook-like:hover'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-fb-fotter-section .ekit-facebook-comments:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-fb-fotter-section .ekit-facebook-retweet:hover'  => 'color: {{VALUE}}',
				],
			]
		);

		// Reactions heading
		$this->add_control( 'reactions_style_heading', [
			'label'     => esc_html__('Reactions', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_responsive_control( 'reactions_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => 'vertical',
				'default'	=> [
					'top'      => '11', 'bottom'   => '11',
					'unit'     => 'px', 'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-fb-fotter-section' => 
						'padding-top:{{TOP}}{{UNIT}};padding-bottom:{{BOTTOM}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();
		/** end footer style */
		/* End style settings */

		$this->insert_pro_message();
	}


	public function str_check($textData = '') {
		$stringText = '';
		if(strlen($textData) > 5) {
			$explodeText = explode(' ', trim($textData));
			for($st = 0; $st < count($explodeText); $st++) {
				$pos      = stripos(trim($explodeText[$st]), '#');
				$pos1     = stripos(trim($explodeText[$st]), '@');
				$poshttp  = stripos(trim($explodeText[$st]), 'http');
				$poshttps = stripos(trim($explodeText[$st]), 'https');

				if($pos !== false) {
					$stringText .= '<a href="https://facebook.com/hashtag/' . str_replace('#', '', $explodeText[$st]) . '?source=feed_text" target="_blank"> ' . $explodeText[$st] . ' </a>';
				} elseif($pos1 !== false) {
					$stringText .= '<a href="https://facebook.com/' . $explodeText[$st] . '/" target="_blank"> ' . $explodeText[$st] . ' </a>';
				} elseif($poshttp !== false || $poshttps !== false) {
					$stringText .= '<a href="' . $explodeText[$st] . '" target="_blank"> ' . $explodeText[$st] . ' </a>';
				} else {
					$stringText .= ' ' . $explodeText[$st];
				}
			}
		}

		return $stringText;
	}


	protected function unit_converter($unit) {
		$convert_reaction = 0;
		$reaction_suffix  = '';

		if($unit >= 0 && $unit < 10000) {
			$convert_reaction = number_format($unit);
		} elseif($unit >= 10000 && $unit < 1000000) {
			$convert_reaction = round(floor($unit / 1000), 1);
			$reaction_suffix  = 'K';
		} elseif($unit >= 1000000 && $unit < 100000000) {
			$convert_reaction = round(($unit / 1000000), 1);
			$reaction_suffix  = 'M';
		} elseif($unit >= 100000000 && $unit < 1000000000) {
			$convert_reaction = round(floor($unit / 100000000), 1);
			$reaction_suffix  = 'B';
		} elseif($unit >= 1000000000) {
			$convert_reaction = round(floor($unit / 1000000000), 1);
			$reaction_suffix  = 'T';
		}

		return $convert_reaction . '' . $reaction_suffix;
	}


	protected function render() {
		echo '<div class="ekit-wid-con" >';
		$this->render_raw();
		echo '</div>';
	}


	protected function test_view() {
		$settings = $this->get_settings();
		extract($settings);


		$config = Handler::get_data();

		$setting = new \Ekit_facebook_settings();
		$setting->setup($config);
	}

	public function replace_symbols($s) {
		$s = preg_replace('/&/', '%26', $s);
		$s = preg_replace('/#/', '%23', $s);
		$s = preg_replace('/\+/', '%2B', $s);
		$s = preg_replace('/@/', '%40', $s);
		$s = preg_replace('/:/', '%3A', $s);
		return $s;
	}

	public function encode_pinterest_url($url) {
		$unescaped = array(
			'%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', '%7E'=>'~',
			'%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'
		);
		$reserved = array(
			'%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
			'%40'=>'@','%26'=>'&','%3D'=>'=','%2B'=>'+','%24'=>'$'
		);
		$score = array(
			'%23'=>'#'
		);
		return strtr(rawurlencode($url), array_merge($reserved,$unescaped,$score));
	}

	/**
	 * Encoding the url for Pinterest
	 */
	public function get_pinterest_url($url) {
		$url = $this->encode_pinterest_url($url);
		$url = $this->replace_symbols($url);
		return str_replace("%23038;","", $url);
	}

	protected function render_raw() {
		$settings = $this->get_settings();
		extract($settings);

		/**
		 * Config must contain access token and page id
		 */
		$config = Handler::get_data();

		if(empty($config['pg_tok']) || empty($config['pg_id'])) {

			echo esc_html__( 'Page access token  and page id is required!', 'elementskit' );

			return '';
		}

		$setting = new \Ekit_facebook_settings();
		$setting->setup($config);
		

		/**
		 * Get page name and id info
		 *
		 */
		$page = $setting->verify_fb_page_and_token();

		if(!empty($page['param']['pg_id'])):

			$load = $setting->get_fb_page_posts($ekit_facebook_feed_show_post);

			if(!empty($load['success'])) {

			    $max_post = $ekit_facebook_feed_show_post;


				$response    = $load['param'];
				$styleLayout = $ekit_facebook_feed_layout_style;
				$columnFeed  = 'ekit-fb-col-12';

				if($styleLayout != 'ekit-layout-list' && $styleLayout != 'ekit-layout-masonary') {
					$columnFeed = $ekit_facebook_feed_column_grid;
					if(isset($ekit_facebook_feed_column_grid_tablet)){
						$columnFeed .= $this->format_column('tablet', $ekit_facebook_feed_column_grid_tablet);
					}
					if(isset($ekit_facebook_feed_column_grid_mobile)){
						$columnFeed .= $this->format_column('mobile', $ekit_facebook_feed_column_grid_mobile);
					}
				}

				$masnory_column = '';

				if($styleLayout == 'ekit-layout-masonary') {
					$masnory_column = $ekit_facebook_feed_column_grid;
					if(isset($ekit_facebook_feed_column_grid_tablet)){
						$masnory_column .= $this->format_column('tablet', $ekit_facebook_feed_column_grid_tablet);
					}
					if(isset($ekit_facebook_feed_column_grid_mobile)){
						$masnory_column .= $this->format_column('mobile', $ekit_facebook_feed_column_grid_mobile);
					}
				}



				if(isset($response->error)) {
					echo esc_html($response->error->message);

					return '';
				}

				if(empty($load['param']->data)) {
					echo esc_html__( '0 posts returned!', 'elementskit' );
					return '';
				}

				$show_header_menu = isset($header_menu_type) && $header_menu_type != 'none';
				$image_position = isset($image_position) ? $image_position : 'center';
				$show_thumbnail_outline = isset($profile_thumbnail_show_outline) && $profile_thumbnail_show_outline == 'yes' ? true : false;
				$reaction_style = isset($reaction_style) ? $reaction_style : 's1';
				// Bottom share and view section
				$show_share_and_view = isset($show_share_and_view) ? $show_share_and_view : 'yes';
				$show_share_and_view = $show_share_and_view === 'yes' ? true : false;
				// Bottom reaction section
				$show_reaction = isset($show_reaction) ? $show_reaction : 'yes';
				$show_reaction = false; // todo - we are removing this feature

				$ekit_facebook_feed_show_comments = 'no'

				?>
                <div class="ekit-facebook-feed">
                    <div class="ekit-fb-row <?php echo esc_attr($masnory_column . ' ' . $styleLayout); ?>">
						<?php

						$default_pic_placeholder = Handler::get_url() . 'assets/images/profile-placeholder.jpg';

						$page_link = 'https://www.facebook.com/' . $page['param']['pg_id'];

                        $cnt = 0;

						foreach($response->data as $fbjson):

                            if($max_post < 1) continue;

							$cnt++;

							$fb_message       = empty($fbjson->message) ? '' : $fbjson->message;
							$fb_id            = isset($fbjson->id) ? $fbjson->id : '';
							$fb_message_tags  = isset($fbjson->message_tags) ? $fbjson->message_tags : [];
							$fb_type          = isset($fbjson->type) ? $fbjson->type : 'status';
							$fb_status_type   = isset($fbjson->status_type) ? $fbjson->status_type : 'status';
							$fb_created_time  = isset($fbjson->created_time) ? $fbjson->created_time : '';
							$updated_time     = isset($fbjson->updated_time) ? $fbjson->updated_time : '';
							$fb_picture       = isset($fbjson->picture) ? $fbjson->picture : '';
							$fb_full_picture  = isset($fbjson->full_picture) ? $fbjson->full_picture : '';
							$fb_permalink_url = isset($fbjson->permalink_url) ? $fbjson->permalink_url : '';

							// from user
							$fb_from_user = isset($fbjson->from) ? $fbjson->from : [];
							$fb_to_user   = isset($fbjson->to->data) ? $fbjson->to->data : [];

                            $feed_image =  $this->get_feed_img_url($fbjson);
                            $per_post_image_position = !empty($feed_image) ? $image_position : 'center';
                            $per_post_show_share_and_view = $image_position != 'background' ? $show_share_and_view : false;

							/**
							 * todo - AR likes are not correct
							 * With reaction & likes filed we can not pull the count other than the peoples who has roles in the app.
							 * So ....
							 */
							$fb_reactions     = empty($fbjson->likes->data) ? 0 : count($fbjson->likes->data);
							$convert_reaction = $this->unit_converter($fb_reactions);

							/**
							 *
							 * We are getting the correct number of approved comments
							 *
							 */
							$fb_comments     = empty($fbjson->comments->data) ? 0 : count($fbjson->comments->data);
							$convert_comment = $this->unit_converter($fb_comments);
							$cmt_link        = $fb_comments > 0 ? $fbjson->comments->data[0]->permalink_url : '#';

							/**
							 * Share count is coming correct now with permission -
							 *
							 */
							$fb_shares      = empty($fbjson->shares->count) ? 0 : intval($fbjson->shares->count);
							$convert_shares = $this->unit_converter($fb_shares);;


							// link share data
							$fb_link        = isset($fbjson->link) ? $fbjson->link : '';
							$fb_name        = isset($fbjson->name) ? $fbjson->name : '';
							$fb_caption     = isset($fbjson->caption) ? $fbjson->caption : '';
							$fb_description = isset($fbjson->description) ? $fbjson->description : '';
							$fb_icon        = isset($fbjson->icon) ? $fbjson->icon : '';

							$userName = '';

							$profileName = 'Annonymous';
							if(isset($fbjson->from)) {
								$userName     = $fb_from_user->id;
								$profileName  = $fb_from_user->name;
								$profileImage = \Ekit_facebook_settings::get_fb_page_profile_pic_url($fbjson->from->id);
							}

							$pic_created_time = date("F j, Y", strtotime($fb_created_time . " +1 days"));

							$photos_class = '';

							if($ekit_facebook_feed_style_choose == 'photos') {
								$photos_class = 'ekit_fb_photo_gallery';
							}


							if($ekit_facebook_feed_style_choose == 'classic') : ?>

                                <div class="ekit-single-fb-feed-holder <?php echo esc_attr($columnFeed); ?>">
									<div 
										class="facebook-feed-card ekit-single-fb-feed<?php 
											echo esc_attr( in_array($per_post_image_position, ['left', 'background']) ? ' image-in-' . $image_position : '' ); 
											echo esc_attr( $image_bg_style == 's2' ? ' bg-img-style-2' : '' )
										?>"
										style="<?php echo ($per_post_image_position == 'background' && !empty($feed_image)) ? 'background-image:url('. $feed_image .')' : '' ?>"
									>
										<?php if($per_post_image_position == 'background'): ?>
											<div class='facebook-feed-card__inner'>
												<?php if($image_bg_style == 's1'): ?>
													<div class='header-container'>
												<?php endif; ?>
										<?php endif; ?>

										<?php if( $per_post_image_position == 'background' && $image_bg_style == 's2' ): ?>
											<div class="ekit-fb-feed-header-menu">
												<div class="ekit-fb-feed-header-menu__toggler">
													<?php if($header_menu_type == 'arrow'): ?>
														<i class='icon icon-down-arrow1 rotate-on-hover'></i>
													<?php elseif($header_menu_type == 'dot'): ?>
														<span class='three-dot-icon'>
															<span></span>
															<span></span>
															<span></span>
														</span>
													<?php endif; ?>
													<div class="ekit-fb-feed-header-menu__content">
														<a href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u=' . $fb_permalink_url); ?>&display=popup&ref=plugin&src=post" target='_blank'>View on Facebook</a>
													</div>
												</div>
											</div>
										<?php endif; ?>

										<?php if($per_post_image_position == 'left'): ?>
											<?php $this->render_feed_image($fbjson); ?>
											<div class='right'>
										<?php endif; ?>

										<?php if($per_post_image_position == 'top'):
											$this->render_feed_image($fbjson);
										endif;

										
										if($ekit_facebook_feed_show_author == 'yes'): ?>
                                            <!--Start Profile header -->
                                            <div class="ekit-fb-feed-header">
                                                <div class="ekit-fb-feed-profile-thumb">
													<?php

													if(in_array($ekit_facebook_feed_author_type, [ 'both', 'only-profile' ])): ?>
                                                        <a 
                                                           	target="_blank"
															href="https://www.facebook.com/<?php echo esc_attr($userName); ?>"
                                                           	class="<?php echo esc_attr($ekit_facebook_feed_author_style); echo esc_attr( $show_thumbnail_outline ) ? ' has-outline' : '' ?>">
                                                            	<img 
																	src="<?php echo esc_url( $profileImage ); ?>"
                                                                 	alt="<?php echo esc_attr($userName); ?>"
																/>
                                                        </a>
														<?php
													endif; ?>

                                                </div>

                                                <div class="ekit-fb-feed-profile-info">
													<?php

													if(in_array($ekit_facebook_feed_author_type, [ 'both', 'only-name' ])): ?>
                                                        <a href="https://www.facebook.com/<?php echo esc_attr($userName); ?>"
                                                           target="_blank" class="user-name">
															<?php echo esc_html( $profileName ); ?>
                                                        </a>
													<?php endif;

													if($ekit_facebook_feed_show_post_date == 'yes'): ?>
                                                        <span class="ekit-fb-post-publish-date">
															<?php echo esc_html($pic_created_time); ?>
														</span>
														<?php
													endif; ?>

                                                </div>

												<?php if($show_header_menu && !($per_post_image_position == 'background' && $image_bg_style == 's2')): ?>
													<div class="ekit-fb-feed-header-menu">
														<div class="ekit-fb-feed-header-menu__toggler">
															<?php if($header_menu_type == 'arrow'): ?>
																<i class='icon icon-down-arrow1 rotate-on-hover'></i>
															<?php elseif($header_menu_type == 'dot'): ?>
																<!-- <i class='icon icon-dot'></i> -->
																<span class='three-dot-icon'>
																	<span></span>
																	<span></span>
																	<span></span>
																</span>
															<?php endif; ?>
															<div class="ekit-fb-feed-header-menu__content">
																<a href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u=' . $fb_permalink_url); ?>&display=popup&ref=plugin&src=post" target='_blank'>View on Facebook</a>
															</div>
														</div>
													</div>
												<?php endif; ?>

                                            </div>
                                            <!--End Profile header -->
											<?php

										endif; ?>

                                        <!--Start Body -->
                                        <p class="ekit-fb-feed-status">
											<?php echo \ElementsKit\Utils::kses($this->str_check($fb_message)); ?>
										</p>
										
										<?php if($per_post_image_position == 'background' && $image_bg_style == 's1' ): ?>
											</div>
										<?php endif; ?>
										
										<?php if($per_post_image_position == 'center'): ?>
											<?php $this->render_feed_image($fbjson); ?>
										<?php endif; ?>

										<?php if( empty($image_bg_style) || $image_bg_style == 's1' ): ?>
											<div class='ekit-fb-feed-bottom'>
										<?php endif; ?>
                                        <!--End Body -->
										
											<?php if($show_reaction): ?>
												<div class="ekit-fb-fotter-section<?php echo esc_attr( $reaction_style == 's2' ? ' style-02' : '' ) ?>">
													<?php if($reaction_style == 's1'): ?>
														<div class="ekit-fb-reaction-left">
															<a href="<?php echo esc_url($fb_permalink_url); ?>" target="_blank"
															title="Like" class="ekit-facebook-like"
															>
																<?php $this->render_icon($settings, 'like_icon', 'like'); ?>
																<?php $this->render_icon($settings, 'love_icon', 'love'); ?>
																<strong class='count'> <?php echo esc_html( $convert_reaction ) ?> </strong>
															</a>
														</div>
														<div>
															<a href="<?php echo esc_url($cmt_link); ?>"
															target="_blank"
															title="Comments"
															class="ekit-facebook-comments"
															>
																<strong class='count'><?php echo esc_html($convert_comment . ' Comments'); ?></strong>
															</a>
														</div>
													<?php elseif($reaction_style == 's2'): ?>
														<div class="ekit-fb-reaction-left">
															<a href="<?php echo esc_url($fb_permalink_url); ?>" target="_blank"
															title="Like" class="ekit-facebook-like"
															>
																<?php $this->render_icon($settings, 'like_icon', 'like'); ?>
																<strong class='count'>
																	<?php echo esc_html($convert_reaction . ' Likes') ?>
																</strong>
															</a>
														</div>
														<div>
															<a href="<?php echo esc_url($cmt_link); ?>"
																target="_blank"
																title="Comments"
																class="ekit-facebook-comments"
															>
																<?php $this->render_icon($settings, 'comment_icon', 'comment'); ?>
																<strong class='count'>
																	<?php echo esc_html($convert_comment . ' Comments'); ?>
																</strong>
															</a>
														</div>
													<?php endif; ?>
												</div>
											<?php endif; ?>

											<?php if($per_post_show_share_and_view): ?>
												<hr class='ekit-fb-feed-share__divider' />
												<div class='ekit-fb-feed-share'>
													<span class='ekit-fb-feed-share__share'>
														<i class='icon icon-share-3'></i>
														<span class='medium-text'> <?php echo esc_html__( 'Share', 'elementskit') ?> </span>

														<div class='ekit-fb-feed-share__menu'>
															<a href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u=' . $fb_permalink_url); ?>&display=popup&ref=plugin&src=post" title="Share" target="_blank" class='ekit-fb-feed-share__with-fb'>
																<i class="icon icon-facebook"></i>
																<span> <?php echo esc_html__( 'Share on Facebook', 'elementskit' ) ?> </span>
															</a>
															<?php 

															$twitter_params = 
																'?text=Share with twitter+-' .
																'&amp;url=' . urlencode($fb_permalink_url) . 
																'&amp;counturl=' . urlencode($fb_permalink_url) .
																'';
															?>
															<a href="<?php echo 'https://twitter.com/share' . $twitter_params; ?>"  title="Share" target="_blank"  class='ekit-fb-feed-share__with-tw'>
																<i class="icon icon-twitter"></i>
																<span> <?php echo esc_html__( 'Share on Twitter', 'elementskit' ) ?></span>
															</a>
															
															<?php

															// pinterst share url with media
															$pinterest_params = 
																'?url=' . $this->get_pinterest_url($fb_permalink_url)  . 
																'&media=' . $this->get_pinterest_url($this->get_feed_img_url($fbjson)) . 
																'&description=Share+With+Pinterest';												
															?>
															<a 
																href="<?php echo esc_url('https://pinterest.com/pin/create/button/'. $pinterest_params); ?>" 
																target='_blank' 
																class='ekit-fb-feed-share__with-pin'
																>
																<i class="icon icon-pinterest"></i>
																<span> <?php echo esc_html__( 'Share on Pinterest', 'elementskit' ) ?></span>
															</a>		
														</div>
													</span>
													<a href="<?php echo esc_url($fbjson->permalink_url) ;?>"  class='ekit-fb-feed-share__view' target="_blank">
														<span class='medium-text'> <?php echo esc_html__( 'View', 'elementskit' ) ?> </span>
														<i class='icon icon-arrow-right'></i>
													</a>
												</div>
											<?php endif; ?>


											<?php if($ekit_facebook_feed_show_comments == 'yes'): ?>

												<div class="comments-show facebook-feed-card__comments">
													<?php if(!empty($fbjson->comments->data)):
														foreach($fbjson->comments->data as $commentsData):?>
															<p><?php echo esc_html($commentsData->message) ?></p><?php
														endforeach;
													endif; ?>
												</div>

											<?php endif; ?>

										<?php if( empty($image_bg_style) || $image_bg_style == 's1' ): ?>
											</div>
										<?php endif; ?>
                                        <!--End Body -->

										<div class='ekit-single-fb-feed__bottom-padding'></div>

										<?php if(in_array($per_post_image_position, ['left', 'background'])): ?>
											</div>
										<?php endif; ?>
                                    </div>
                                </div>
								<?php

								$max_post--;

							else :

								if(!empty($fb_full_picture)) : ?>
                                    <div class="ekit-single-fb-feed-holder <?php echo esc_attr($columnFeed); ?>">
                                        <div class="ekit-single-fb-feed <?php echo \ElementsKit\Utils::render($photos_class); ?>">
                                            <div class="ekit-fb-feed-media">
                                                <a class="ekit_fb_photo_link"
                                                   href="<?php echo esc_url($fb_permalink_url); ?>"
                                                   target="_blank">
                                                    <img class="ekit_fb_photo"
                                                         src="<?php echo esc_url($fb_full_picture); ?>"
                                                         alt="<?php echo esc_attr($fb_name); ?>"/>
                                                </a>
                                            </div>
                                            <!--End Media -->
                                        </div>
                                    </div>
									<?php

									$max_post--;
								endif;
							endif;
						endforeach;
						?>
                    </div>
                </div>
				<?php

			} else {

				echo esc_html__( 'Fetching page posts failed! <br/>', 'elementskit' );
				echo esc_html__( 'Error msg : ', 'elementskit' ) . $load['msg'] . esc_html( '<br/>');

			}

		else:

			echo esc_html__( 'Page could not be verified!<br/>', 'elementskit' );
			echo esc_html__( 'Most probably your page access token is wrong or expired, please refresh the page token in the user setting data', 'elementskit' );

		endif;
	}

	protected function format_column( $devices, $value ){
		$splitted = explode('ekit-fb-col-', $value);
		return ' ekit-fb-col-'. $devices . '-'. $splitted[1];
	}

	protected function get_feed_img_url($post){

		if(empty($post->attachments) || empty($post->attachments->data)){ return ''; }

		$image_types = ['photo', 'album', 'share', 'profile_media', 'cover_photo'];
		$video_types = ['video', 'video_autoplay', 'video-inline'];
        $data = $post->attachments->data[0];
        
		if(in_array($data->type, $image_types)){
			return esc_url($data->media->image->src);
        }
		elseif(substr($data->type, 0, 5) == 'video' || in_array($data->type, $video_types)){
			return !empty($post->full_picture) ? esc_url($post->full_picture) : '';
		}
		return '';
	}

	protected function render_icon($settings, $control_key, $classes ) {
		$migrated = isset( $settings['__fa4_migrated'][$control_key . 's'] );
		$is_new = empty($settings[$control_key]);
		if ( $is_new || $migrated ) :
			\Elementor\Icons_Manager::render_icon( $settings[$control_key . 's'], [ 'aria-hidden' => 'true', 'class' => $classes ] );
		else : ?>
			<i class="<?php echo esc_attr( $classes .' '. $settings[$control_key] ); ?>" aria-hidden="true"></i>
		<?php endif;
	}

    protected function render_feed_image( $fbjson ) { 
        
        if(empty($fbjson->attachments)){ return ;}

        $has_media = false;
        foreach( $fbjson->attachments->data as $post ):
            if(isset($post->type)){
                $has_media = true;
                break;
            }
        endforeach;

        if(!$has_media){ return ;}

        ?>
		<div class='ekit-fb-feed-image'>
			<?php if(!empty($fbjson->attachments->data)):
				$objs = $fbjson->attachments->data;
				foreach( $objs as $obj ):
					if($obj->type == 'photo' || $obj->type == 'album') : ?>
						<img src="<?php echo esc_url( $obj->media->image->src ); ?>" alt="Post Image"/>
					<?php elseif( 
						$obj->type == 'share' || 
						$obj->type == 'profile_media' || 
						$obj->type == 'cover_photo' 
					): ?>
						<img 
							src="<?php echo esc_url( $obj->media->image->src ); ?>"
							title="<?php echo esc_attr( $obj->title ) ?>"
							alt="<?php echo esc_attr( $obj->title ) ?>" 
						/>
					<?php elseif(
						substr($obj->type, 0, 5) == 'video' || 
						$obj->type == 'video_autoplay' || 
						$obj->type == 'video'
                    ):
                    $fb_name          = isset($fbjson->name) ? $fbjson->name : '';
					$fb_full_picture  = isset($fbjson->full_picture) ? $fbjson->full_picture : '';
					?>
						<div class='video-container' data-src="<?php echo esc_url($obj->media->source); ?>">
                            <img src="<?php echo esc_url($fb_full_picture); ?>" alt="<?php echo esc_html($fb_name); ?>"/>
							<div class="ekit-fb-hover-content">
								<div class="ekit-fb-video-play-button">
									<svg xmlns="https://www.w3.org/2000/svg" style="display: initial;" viewBox="0 0 512 512">
										<path d="M354.2 247.4L219.1 155c-4.2-3.1-15.4-3.1-16.3 8.6v184.8c1 11.7 12.4 11.9 16.3 8.6l135.1-92.4c3.5-2.1 8.3-10.7 0-17.2zm-130.5 81.3V183.3L329.8 256l-106.1 72.7z"/>
										<path d="M256 11C120.9 11 11 120.9 11 256s109.9 245 245 245 245-109.9 245-245S391.1 11 256 11zm0 469.1C132.4 480.1 31.9 379.6 31.9 256S132.4 31.9 256 31.9 480.1 132.4 480.1 256 379.6 480.1 256 480.1z"/>
									</svg>
								</div>
							</div>
						</div>
					<?php endif;
				endforeach;
			endif; ?>
		</div>
	<?php }
}
