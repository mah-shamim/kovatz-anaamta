<?php

namespace Elementor;

use \Elementor\ElementsKit_Widget_Instagram_Feed_Handler as Handler;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Instagram_Feed extends Widget_Base {

	private $ekit_ins_follow = 'https://www.instagram.com/xpeeder/';

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
		return 'https://wpmet.com/doc/instagram-feed-api/';
	}

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
		wp_register_script('ekit-instagram-feed', Handler::get_url() . 'assets/js/script.js', ['elementor-frontend'], null, true);
	}

	public function get_script_depends() {
		return ['ekit-instagram-feed'];
	}

	protected function register_controls() {

		/*Layout Settings*/
		$this->start_controls_section(
			'ekit_instagram_feed_layout_settings',
			[
				'label' => esc_html__('Layout Settings ', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_instagram_feed_layout_style',
			[
				'label'              => esc_html__('Grid Style', 'elementskit'),
				'type'               => Controls_Manager::SELECT,
				'frontend_available' => true,
				'default'            => 'layout-masonary',
				'options'            => [
					'layout-list'     => esc_html__('List', 'elementskit'),
					'layout-grid'     => esc_html__('Grid', 'elementskit'),
					'layout-masonary' => esc_html__('Masonary', 'elementskit'),
				],
			]
		);

		$this->add_control(
			'ekit_instagram_feed_column_grid',
			[
				'label'              => esc_html__('Columns Grid', 'elementskit'),
				'type'               => Controls_Manager::SELECT,
				'frontend_available' => true,
				'default'            => 'ekit-insta-col-4',
				'options'            => [
					'ekit-insta-col-auto' => esc_html__('1 Columns', 'elementskit'),
					'ekit-insta-col-6'    => esc_html__('2 Columns', 'elementskit'),
					'ekit-insta-col-4'    => esc_html__('3 Columns', 'elementskit'),
					'ekit-insta-col-3'    => esc_html__('4 Columns', 'elementskit'),
				],
				'condition'          => ['ekit_instagram_feed_layout_style!' => 'layout-list'],
				'separator'          => 'after',
			]
		);

		$this->add_control(
			'ekit_instagram_feed_style',
			[
				'label'              => esc_html__('Feed Style', 'elementskit'),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'label_on'           => esc_html__('Show', 'elementskit'),
				'label_off'          => esc_html__('Hide', 'elementskit'),
				'return_value'       => 'yes',
				'default'            => 'yes',
			]
		);

		$this->add_control(
			'ekit_instagram_feed_ins_limit',
			[
				'label'              => esc_html__('Show Post', 'elementskit'),
				'type'               => Controls_Manager::NUMBER,
				'frontend_available' => true,
				'default'            => esc_html__(9, 'elementskit'),
				'min'                => 1,
				'max'                => 100,
				'step'               => 1,
			]
		);


		$this->end_controls_section();
		/* End layout settings*/


		$this->start_controls_section(
			'ekit_instagram_feed_display_settings',
			[
				'label' => esc_html__('Display Settings ', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_instagram_feed_show_profile_header',
			[
				'label'              => esc_html__('Profile Header', 'elementskit'),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'label_on'           => esc_html__('Show', 'elementskit'),
				'label_off'          => esc_html__('Hide', 'elementskit'),
				'return_value'       => 'yes',
				'default'            => 'no',
				'condition'          => [
					'ekit_instagram_feed_style!' => 'yes',
				],
			]
		);
		$this->add_control(
			'ekit_instagram_feed_profile_settings',
			[
				'label'              => esc_html__('Profile Settings', 'elementskit'),
				'type'               => Controls_Manager::SELECT,
				'frontend_available' => true,
				'default'            => 'both',
				'options'            => [
					'only-profile' => esc_html__('Only Profile Image', 'elementskit'),
					'only-name'    => esc_html__('Only Name', 'elementskit'),
					'both'         => esc_html__('Both', 'elementskit'),
				],
				'condition'          => [
					'ekit_instagram_feed_show_profile_header' => 'yes',
					'ekit_instagram_feed_style!'              => 'yes',
				],
			]
		);
		$this->add_control(
			'ekit_instagram_feed_show_profile_style',
			[
				'label'              => esc_html__('Profile Style', 'elementskit'),
				'type'               => Controls_Manager::SELECT,
				'frontend_available' => true,
				'default'            => 'circle',
				'options'            => [
					'circle' => esc_html__('Circle', 'elementskit'),
					'square' => esc_html__('Square', 'elementskit'),
				],
				'condition'          => [
					'ekit_instagram_feed_profile_settings!'   => 'only-name',
					'ekit_instagram_feed_show_profile_header' => 'yes',
					'ekit_instagram_feed_style!'              => 'yes',
				],

			]
		);

		$this->add_control(
			'ekit_instagram_feed_show_post_date',
			[
				'label'              => esc_html__('Show Date', 'elementskit'),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'label_on'           => esc_html__('Show', 'elementskit'),
				'label_off'          => esc_html__('Hide', 'elementskit'),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'condition'          => [
					'ekit_instagram_feed_show_profile_header' => 'yes',
					'ekit_instagram_feed_style!'              => 'yes',
				],
			]
		);
		$this->add_control(
			'ekit_instagram_feed_show_ins_view',
			[
				'label'              => esc_html__('Show Icon', 'elementskit'),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'label_on'           => esc_html__('Show', 'elementskit'),
				'label_off'          => esc_html__('Hide', 'elementskit'),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'separator'          => 'after',
				'condition'          => [
					'ekit_instagram_feed_show_profile_header' => 'yes',
					'ekit_instagram_feed_style!'              => 'yes',
				],
			]
		);

		$this->add_control(
			'ekit_instagram_feed_show_caption_box',
			[
				'label'              => esc_html__('Show Caption', 'elementskit'),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'label_on'           => esc_html__('Show', 'elementskit'),
				'label_off'          => esc_html__('Hide', 'elementskit'),
				'return_value'       => 'yes',
				'default'            => 'no',
			]
		);


		$this->add_control(
			'ekit_instagram_feed_enable_follow',
			[
				'label'              => esc_html__('Enable Follow Button', 'elementskit'),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'label_on'           => esc_html__('Show', 'elementskit'),
				'label_off'          => esc_html__('Hide', 'elementskit'),
				'return_value'       => 'yes',
				'default'            => 'no',
				'separator'          => 'before',
			]
		);
		$this->add_control(
			'ekit_instagram_feed_ins_follow_text',
			[
				'label'              => esc_html__('Follow Button Text', 'elementskit'),
				'type'               => Controls_Manager::TEXT,
				'frontend_available' => true,
				'default'            => esc_html__('Follow On Instagram', 'elementskit'),
				'placeholder'        => esc_html__('Follow Text', 'elementskit'),
				'label_block'        => true,
				'condition'          => ['ekit_instagram_feed_enable_follow' => 'yes'],
				'dynamic'            => ['active' => true],
			]
		);
		$this->add_control(
			'ekit_instagram_feed_ins_follow_link',
			[
				'label'              => esc_html__('Follow link', 'elementskit'),
				'type'               => Controls_Manager::TEXT,
				'frontend_available' => true,
				'default'            => esc_html__($this->ekit_ins_follow, 'elementskit'),
				'placeholder'        => esc_html__('Follow Link', 'elementskit'),
				'label_block'        => true,
				'condition'          => ['ekit_instagram_feed_enable_follow' => 'yes'],
				'dynamic'            => ['active' => true],
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_btn_alignment',
			[
				'label'              => esc_html__('Alignment', 'elementskit'),
				'type'               => Controls_Manager::CHOOSE,
				'frontend_available' => true,
				'options'            => [
					'left'   => [
						'title' => esc_html__('Left', 'elementskit'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'elementskit'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'elementskit'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'            => 'center',
				'toggle'             => true,
				'condition'          => ['ekit_instagram_feed_enable_follow' => 'yes'],
				'selectors'          => [
					'{{WRAPPER}} .insta-follow-btn-area' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_instagram_feed_conainer_tab_style',
			[
				'label' => esc_html__('Container', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_conainer_margin_bottom',
			[
				'label'      => esc_html__('Container margin Bottom', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px',],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-instagram-area .ekit-insta-row' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ekit_instagram_feed_conainer_background',
				'label'     => esc_html__('Background', 'elementskit'),
				'types'     => ['classic', 'gradient',],
				'default'   => '#FFFFFF',
				'exclude'   => [
					'image',
				],
				'selector'  => '{{WRAPPER}} .ekit-insta-content-holder.ekit-insta-style-classic',
				'condition' => [
					'ekit_instagram_feed_style!' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'ekit_instagram_feed_conainer_border',
				'label'     => esc_html__('Border', 'elementskit'),
				'selector'  => '{{WRAPPER}} .ekit-insta-content-holder.ekit-insta-style-classic',
				'condition' => [
					'ekit_instagram_feed_style!' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_instagram_feed_conainer_box_shadow',
				'label'    => esc_html__('Box Shadow', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-insta-content-holder',
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_conainer_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-insta-content-holder' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_main_conainer_margin_bottom',
			[
				'label'      => esc_html__('Margin Bottom', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
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
					'{{WRAPPER}} .ekit-insta-content-holder' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_conainer_lay_out_grid_gutter',
			[
				'label'      => esc_html__('Gutter', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors'  => [
					'{{WRAPPER}} .layout-grid .ekit-ins-feed' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .layout-grid.ekit-insta-row' => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'ekit_instagram_feed_layout_style' => 'layout-grid',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_conainer_lay_out_list_gutter',
			[
				'label'      => esc_html__('Gutter', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-instagram-area' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'ekit_instagram_feed_layout_style' => 'layout-list',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_conainer_lay_out_grid_container_spacing',
			[
				'label'      => esc_html__('Container Spacing', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-instagram-area' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'ekit_instagram_feed_layout_style' => 'layout-grid',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_conainer_lay_out_masnory_gutter',
			[
				'label'      => esc_html__('Gutter', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-instagram-area .layout-masonary' => 'column-gap: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'ekit_instagram_feed_layout_style' => 'layout-masonary',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_instagram_feed_header_style_tab',
			[
				'label' => esc_html__('Header', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_header_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-nsta-user-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'ekit_instagram_feed_header_username_and_date_normal_and_hover_tabs'
		);
		$this->start_controls_tab(
			'ekit_instagram_feed_header_username_and_date_normal_tab',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_header_username_headeing_normal',
			[
				'label'     => esc_html__('Name', 'elementskit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_header_username_normal_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .ekit-insta-user-details' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_header_date_headeing_normal',
			[
				'label'     => esc_html__('Date', 'elementskit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_header_date_normal_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.6)',
				'selectors' => [
					'{{WRAPPER}} .ekit-insta-user-details .ekit-insta-dataandtime' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_header_icon_headeing_normal',
			[
				'label'     => esc_html__('Icon', 'elementskit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_header_icon_normal_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .ekit-instagram-feed-item-source-icon svg path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_instagram_feed_header_username_and_date_hover_tab',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_instagram_feed_header_username_headeing_hover',
			[
				'label'     => esc_html__('Name', 'elementskit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_header_username_hover_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e1306c',
				'selectors' => [
					'{{WRAPPER}} .ekit-insta-user-details:hover .ekit-insta-user-name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_instagram_feed_header_date_headeing_hover',
			[
				'label'     => esc_html__('Date', 'elementskit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_header_date_hover_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#833ab4',
				'selectors' => [
					'{{WRAPPER}} .ekit-insta-user-details:hover .ekit-insta-dataandtime' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_instagram_feed_header_icon_headeing_hover',
			[
				'label'     => esc_html__('Icon', 'elementskit'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_header_icon_hover_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f56040',
				'selectors' => [
					'{{WRAPPER}} .ekit-instagram-feed-item-source-icon:hover svg path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_instagram_feed_footer_style_tab',
			[
				'label' => esc_html__('Caption', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_instagram_feed_show_caption_box' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekit_instagram_feed_header_style_classic_content_normal_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .ekit-insta-style-classic .ekit-insta-captions' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_instagram_feed_style!' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekit_instagram_feed_header_style_tyles_content_normal_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-insta-style-tiles .ekit-insta-captions' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_instagram_feed_style' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_instagram_feed_header_content_normal_content_typography',
				'label'    => esc_html__('Typography', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-insta-captions',
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_footer_style_padding',
			[
				'label'      => esc_html__('Padding (px)', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-instagram-feed-posts-item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_instagram_feed_media_style_tab',
			[
				'label'     => esc_html__('Media', 'elementskit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_instagram_feed_style' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'ekit_instagram_feed_media_overlay_background',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ekit-insta-content-holder:hover .ekit-insta-hover-overlay',
				'exclude'  => [
					'image',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_media_scale',
			[
				'label'      => esc_html__('Scale', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 3,
						'step' => .1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1.1,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-insta-content-holder.ekit-insta-style-tiles:hover .insta-media .photo-thumb' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_instagram_feed_media_grayscale',
			[
				'label'      => esc_html__('Grayscale', 'elementskit'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-insta-content-holder.ekit-insta-style-tiles:hover .insta-media .photo-thumb' => 'filter: grayscale({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_instagram_feed_button_style_tab',
			[
				'label'     => esc_html__('Button', 'elementskit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_instagram_feed_enable_follow' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_text_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .insta-follow-btn-area .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .insta-follow-btn-area .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_btn_typography',
				'label'    => esc_html__('Typography', 'elementskit'),
				'selector' => '{{WRAPPER}} .insta-follow-btn-area .btn',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'ekit_btn_shadow',
				'selector' => '{{WRAPPER}} .insta-follow-btn-area .btn',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'ekit_btn_shadow_border',
				'label'    => esc_html__('Border', 'elementskit'),
				'selector' => '{{WRAPPER}} .insta-follow-btn-area .btn',
			]
		);

		$this->start_controls_tabs('ekit_btn_tabs_style');

		$this->start_controls_tab(
			'ekit_btn_tabnormal',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_responsive_control(
			'ekit_btn_text_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .insta-follow-btn-area .btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_text_background_color',
			[
				'label'     => esc_html__('Background Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f56040',
				'selectors' => [
					'{{WRAPPER}} .insta-follow-btn-area .btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_btn_text_box_shadow',
				'label'    => esc_html__('Box Shadow', 'elementskit'),
				'selector' => '{{WRAPPER}} .insta-follow-btn-area .btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_btn_tab_button_hover',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_responsive_control(
			'ekit_btn_hover_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .insta-follow-btn-area .btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_text_background_color_hover',
			[
				'label'     => esc_html__('Background Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .insta-follow-btn-area .btn:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_btn_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .insta-follow-btn-area .btn:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_btn_text_box_shadow_hover',
				'label'    => esc_html__('Box Shadow', 'elementskit'),
				'selector' => '{{WRAPPER}} .insta-follow-btn-area .btn:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		// $this->insert_pro_message();
	}

	protected function unit_converter($unit) {
		$convert_reaction = 0;
		$reaction_suffix = '';

		if($unit >= 0 && $unit < 10000) {
			$convert_reaction = number_format($unit);
		} else {
			if($unit >= 10000 && $unit < 1000000) {
				$convert_reaction = round(floor($unit / 1000), 1);
				$reaction_suffix = 'K';
			} else {
				if($unit >= 1000000 && $unit < 100000000) {
					$convert_reaction = round(($unit / 1000000), 1);
					$reaction_suffix = 'M';
				} else {
					if($unit >= 100000000 && $unit < 1000000000) {
						$convert_reaction = round(floor($unit / 100000000), 1);
						$reaction_suffix = 'B';
					} else {
						if($unit >= 1000000000) {
							$convert_reaction = round(floor($unit / 1000000000), 1);
							$reaction_suffix = 'T';
						}
					}
				}
			}
		}

		return $convert_reaction . '' . $reaction_suffix;
	}

	protected function render() {

	    echo '<div class="ekit-wid-con" >';

		$this->render_raw();

		echo '</div>';
	}


	protected function render_raw() {

		$data = Handler::get_instagram_feed_from_API();
		if(is_string($data)) {
			echo esc_html($data);
			return;
		}
		$userCache = Handler::get_user_info();
		if(is_string($userCache)) {
			echo esc_html($userCache);
			return;
		}

		$settings = $this->get_settings_for_display();
		extract($settings);

		$limit = $ekit_instagram_feed_ins_limit;

		$ins_user_link = strlen($ekit_instagram_feed_ins_follow_link) > 2 ? $ekit_instagram_feed_ins_follow_link : $this->ekit_ins_follow;

		$styleLayout = $ekit_instagram_feed_layout_style;
		$columnFeed = 'ekit-insta-col-12';

		if($styleLayout != 'layout-list' && $styleLayout != 'layout-masonary') {
			$columnFeed = $ekit_instagram_feed_column_grid;
		}

		$masnory_column = '';
		if($styleLayout == 'layout-masonary') {
			$masnory_column = $ekit_instagram_feed_column_grid;
		}

		$row_class = '';
		if($styleLayout != 'layout-list' && $styleLayout != 'layout-masonary' && $ekit_instagram_feed_column_grid == 'ekit-insta-col-auto') {
			$row_class = 'ekit-no-wrap ekit-justify-content-between';
		}
		?>

		<div class="ekit-instagram-area">
			<div class="ekit-insta-row <?php echo esc_attr($masnory_column . ' ' . $styleLayout . ' ' . $row_class); ?>">
				<?php
				$data = !empty($limit) ? array_slice($data, 0, $limit) : $data;

				if(isset($data) && is_array($data)):
				foreach($data as $no => $ins_post) {

					$pic_link = $ins_post->media_url;

					$userInfo = !empty($userCache->username) ? $userCache->username : '';
					$pic_created_time = date('F d, Y', strtotime($ins_post->timestamp));

					?>
					<div class="ekit-ins-feed <?php echo esc_attr($columnFeed); ?>">
						<div class="ekit-insta-content-holder <?php if($ekit_instagram_feed_style == 'yes') : ?>ekit-insta-style-tiles<?php endif; ?> <?php if($ekit_instagram_feed_style != 'yes') : ?>ekit-insta-style-classic<?php endif; ?>">
							<?php if(($ekit_instagram_feed_show_profile_header == 'yes') && ($ekit_instagram_feed_style != 'yes')) : ?>
                                <div class="ekit-nsta-user-info">
                                    <a class="ekit-insta-user-details"
                                       href="https://www.instagram.com/<?php echo esc_html($userInfo); ?>">

                                        <div class="ekit-insta-username-and-time">
											<?php if(in_array($ekit_instagram_feed_profile_settings, ['both', 'only-name'])) : ?>
                                                <span class="ekit-insta-user-name"> <?php echo esc_html($userInfo); ?> </span>
											<?php endif;
											if($ekit_instagram_feed_show_post_date == 'yes') : ?>
                                                <span class="ekit-insta-dataandtime"><?php echo esc_html($pic_created_time); ?></span>
											<?php endif; ?>
                                        </div>
                                    </a>
									<?php if($ekit_instagram_feed_show_ins_view == 'yes') : ?>
                                        <a class="ekit-instagram-feed-item-source-icon"
                                           href="<?php echo esc_url($pic_link); ?>">
                                            <svg viewBox="0 0 24 24" width="24" height="24">
                                                <path d="M17.1,1H6.9C3.7,1,1,3.7,1,6.9v10.1C1,20.3,3.7,23,6.9,23h10.1c3.3,0,5.9-2.7,5.9-5.9V6.9C23,3.7,20.3,1,17.1,1zM21.5,17.1c0,2.4-2,4.4-4.4,4.4H6.9c-2.4,0-4.4-2-4.4-4.4V6.9c0-2.4,2-4.4,4.4-4.4h10.3c2.4,0,4.4,2,4.4,4.4V17.1z"></path>
                                                <path d="M16.9,11.2c-0.2-1.1-0.6-2-1.4-2.8c-0.8-0.8-1.7-1.2-2.8-1.4c-0.5-0.1-1-0.1-1.4,0C10,7.3,8.9,8,8.1,9S7,11.4,7.2,12.7C7.4,14,8,15.1,9.1,15.9c0.9,0.6,1.9,1,2.9,1c0.2,0,0.5,0,0.7-0.1c1.3-0.2,2.5-0.9,3.2-1.9C16.8,13.8,17.1,12.5,16.9,11.2zM12.6,15.4c-0.9,0.1-1.8-0.1-2.6-0.6c-0.7-0.6-1.2-1.4-1.4-2.3c-0.1-0.9,0.1-1.8,0.6-2.6c0.6-0.7,1.4-1.2,2.3-1.4c0.2,0,0.3,0,0.5,0s0.3,0,0.5,0c1.5,0.2,2.7,1.4,2.9,2.9C15.8,13.3,14.5,15.1,12.6,15.4z"></path>
                                                <path d="M18.4,5.6c-0.2-0.2-0.4-0.3-0.6-0.3s-0.5,0.1-0.6,0.3c-0.2,0.2-0.3,0.4-0.3,0.6s0.1,0.5,0.3,0.6c0.2,0.2,0.4,0.3,0.6,0.3s0.5-0.1,0.6-0.3c0.2-0.2,0.3-0.4,0.3-0.6C18.7,5.9,18.6,5.7,18.4,5.6z"></path>
                                            </svg>
                                        </a>
									<?php endif; ?>
                                </div>
							<?php endif; ?>
                            <div class="insta-media">
                                <a href="<?php echo esc_url($pic_link); ?>" target="_blank">
									<?php if($ins_post->media_type == 'VIDEO'): ?>
                                        <video class="img-responsive photo-thumb"
                                               src="<?php echo esc_url($pic_link); ?>"
                                        ></video>
									<?php else: ?>
                                        <img class="img-responsive photo-thumb" src="<?php echo esc_url($pic_link); ?>"
                                             alt="<?php echo esc_html($userInfo); ?>">
									<?php endif; ?>
                                </a>
                            </div>
							<?php if( $ekit_instagram_feed_show_caption_box == 'yes' && isset($ins_post->caption)) : ?>
                                <div class="ekit-instagram-feed-posts-item-content">
									<div class="ekit-insta-captions">
										<?php echo esc_html($ins_post->caption) ?>
									</div>
                                </div>
							<?php endif; ?>
							<?php if($ekit_instagram_feed_style == 'yes') : ?>
                                <div class="ekit-insta-hover-overlay"></div>
							<?php endif; ?>
						</div>
					</div>
					<?php
				}
				endif;
				?>

			</div>
			<?php if($ekit_instagram_feed_enable_follow == 'yes') : ?>
                <div class="insta-follow-btn-area">
                    <a class="btn btn-primary" href="<?php echo esc_url($ins_user_link); ?>" target="_blank">
						<?php echo esc_html($ekit_instagram_feed_ins_follow_text); ?>
                    </a>
                </div>
			<?php endif; ?>
		</div>
		<?php

	}
}
