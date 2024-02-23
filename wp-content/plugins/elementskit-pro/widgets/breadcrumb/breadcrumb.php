<?php

namespace Elementor;

defined('ABSPATH') || exit;

use Elementor\ElementsKit_Widget_Breadcrumb_Handler as Handler;


class ElementsKit_Widget_Breadcrumb extends Widget_Base {

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
		return 'https://wpmet.com/doc/breadcrumb/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'ekit_lite_section_content',
			[
				'label' => __('Settings', 'elementskit'),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'ekit_bresdcroum_home_icon',
			[
				'label' => esc_html__('Home Icon', 'elementskit'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'label_block' => false,
				'skin' => 'inline',
			]
		);

		$this->add_control(
			'ekit_bresdcroum_separate_icon_show',
			[
				'label'        => __('Show Separate Icon', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Show', 'elementskit'),
				'label_off'    => __('Hide', 'elementskit'),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);
		
		$this->add_control(
			'ekit_bresdcroum_separate_icon',
			[
				'label' => esc_html__('Separate Icon', 'elementskit'),
				'type' =>  Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'skin_settings' => [
					'inline' => [
						'none' => [
							'label' => esc_html__( 'Default', 'elementskit' ),
							'icon' => 'fas fa-angle-double-right',
						],
					],
				],
				'condition' => [
					'ekit_bresdcroum_separate_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekit_breadcrumb_len',
			[
				'label'   => __('Max Title word length', 'elementskit'),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 5,
				'max'     => 100,
				'step'    => 1,
				'default' => 15,
			]
		);

		$this->add_control(
			'ekit_breadcrumb_show_trail',
			[
				'label'        => __('Show category trail', 'elementskit'),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __('Show', 'elementskit'),
				'label_off'    => __('Hide', 'elementskit'),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_breadcrumbs_style',
			[
				'label' => esc_html__('Style', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_breadcrumb_text_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'alpha'     => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb > li:not(.brd_sep)' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_breadcrumb_link_color',
			[
				'label'     => esc_html__('Link Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'alpha'     => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb > li > a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-breadcrumb > li > span.ekit_home_icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-breadcrumb > li > span.ekit_home_icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_breadcrumb_link_hover_color',
			[
				'label'     => esc_html__('Link Hover Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'alpha'     => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb > li:hover > a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-breadcrumb > li:hover > span.ekit_home_icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-breadcrumb > li:hover > span.ekit_home_icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'ekit_breadcrumb_typography',
				'selector'       => '{{WRAPPER}} .ekit-breadcrumb',
				'exclude'		 => ['text_decoration', 'letter_spacing'],
				'fields_options' => [
					'typography'     => [
						'default' => 'custom',
					],
					'font_size'      => [
						'default'    => [
							'size' => '',
							'unit' => 'px'
						],
						'label'      => 'Font size (px)',
						'size_units' => ['px'],
					],
					'font_weight'    => [
						'default' => '',
					],
					'text_transform' => [
						'default' => '',
					],
					'line_height'    => [
						'default' => [
							'size' => '',
							'unit' => 'px'
						]
					],
					'letter_spacing' => [
						'default' => [
							'size' => '',
						]
					],
				],
			]
		);

		$this->add_responsive_control(
			'ekit_breadcrumbs_alignment',
			[
				'label'     => esc_html__('Alignment', 'elementskit'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'start'	=> [
						'title'		=> esc_html__('Left', 'elementskit'),
						'icon'		=> 'eicon-text-align-left',
					],
					'center'	=> [
						'title' 	=> esc_html__('Center', 'elementskit'),
						'icon'  	=> 'eicon-text-align-center',
					],
					'end'  => [
						'title' 	=> esc_html__('Right', 'elementskit'),
						'icon'  	=> 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb' => 'justify-content: {{VALUE}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_breadcrumb_style_section',
			[
				'label' => esc_html__('Breadcrumb', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_breadcrumb_space_between',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'default' => [
					'top' => '',
					'right' => '5',
					'bottom' => '',
					'left' => '',
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb > li:not(.brd_sep)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		
		
		$this->add_responsive_control(
			'ekit_breadcrumb_space_between_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb > li:not(.brd_sep)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_breadcrumb_box_shadow',
				'selector' => '{{WRAPPER}} .ekit-breadcrumb > li:not(.brd_sep)',
			]
		);
		
		$this->start_controls_tabs(
			'ekit_breadcrumb_tab_section'
		);

		$this->start_controls_tab(
			'ekit_breadcrumb_normal_tab',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_breadcrumb_space_background_normal',
				'types' => [ 'classic', 'gradient'],
				'exclude'   => ['image'],
				'fields_options'  => [
					'background' => [
						'label' => esc_html__('Item Background', 'elementskit'),
					]
				],
				'selector' => '{{WRAPPER}} .ekit-breadcrumb > li:not(.brd_sep, :last-of-type)',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_breadcrumb_space_background_active',
				'types' => [ 'classic', 'gradient'],
				'exclude'   => ['image'],
				'fields_options'  => [
					'background' => [
						'label' => esc_html__('Active Background', 'elementskit'),
					]
				],
				'selector' => '{{WRAPPER}} .ekit-breadcrumb > li:is(:last-of-type)',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_breadcrumb_hover_tab',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_breadcrumb_space_background_hover',
				'types' => [ 'classic', 'gradient'],
				'exclude'   => ['image'],
				'fields_options'  => [
					'background' => [
						'label' => esc_html__('Item Background', 'elementskit'),
					]
				],
				'selector' => '{{WRAPPER}} .ekit-breadcrumb > li:not(.brd_sep, :last-of-type):hover',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_breadcrumb_space_background_active_hover',
				'types' => [ 'classic', 'gradient'],
				'exclude'   => ['image'],
				'fields_options'  => [
					'background' => [
						'label' => esc_html__('Active Background', 'elementskit'),
					]
				],
				'selector' => '{{WRAPPER}} .ekit-breadcrumb > li:is(:last-of-type):hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'ekit_breadcrumb_border_options',
			[
				'label' => esc_html__('Border Options', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_breadcrumb_item_border',
				'fields_options'  => [
					'border' => [
						'label' => esc_html__('Item Border', 'elementskit'),
					]
				],
				'selector' => '{{WRAPPER}} .ekit-breadcrumb > li:not(.brd_sep)',
			]
		);

		$this->add_control(
			'ekit_breadcrumb_item_border_radius',
			[
				'label'      => esc_html__('Item Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb > li:not(.brd_sep)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_breadcrumbs_icon',
			[
				'label' => esc_html__('Icon', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_breadcrumb_home_icon_options_icon',
			[
				'label' => esc_html__('Home icon', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_bresdcroum_home_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'ekit_breadcrumb_home_icon_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'alpha'     => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb .ekit_breadcrumbs_start .ekit_home_icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-breadcrumb .ekit_breadcrumbs_start .ekit_home_icon svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'ekit_bresdcroum_home_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'ekit_breadcrumb_home_space_between',
			[
				'label'      => esc_html__('Space Between', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'custom' ],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb .ekit_breadcrumbs_start .ekit_home_icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_bresdcroum_home_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_breadcrumb_home_icon_size',
			[
				'label' => esc_html__( 'Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'condition' => [
					'ekit_bresdcroum_home_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb .ekit_breadcrumbs_start .ekit_home_icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => "after"
			]
		);

		$this->add_control(
			'ekit_breadcrumb_separator_icon',
			[
				'label' => esc_html__('Separator icon', 'elementskit'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'ekit_breadcrumb_space_icon',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb > li:is(.brd_sep)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_breadcrumb_icon_size',
			[
				'label' => esc_html__( 'Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb > li:is(.brd_sep) .separate_icon ' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_breadcrumb_box_shadow_icon',
				'selector' => '{{WRAPPER}} .ekit-breadcrumb > li:is(.brd_sep)',
			]
		);

		$this->start_controls_tabs(
			'ekit_breadcrumb_space_icon_tabs' 
		);

		$this->start_controls_tab(
			'ekit_breadcrumb_space_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_breadcrumb_icon_color_normal',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'alpha'     => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb > li:is(.brd_sep)' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-breadcrumb > li:is(.brd_sep) svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_breadcrumb_space_icon_background_normal',
				'types' => [ 'classic', 'gradient'],
				'exclude'   => ['image'],
				'fields_options'  => [
					'background' => [
						'label' => esc_html__('Background', 'elementskit'),
					]
				],
				'selector' => '{{WRAPPER}} .ekit-breadcrumb > li:is(.brd_sep)',
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'ekit_breadcrumb_space_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_breadcrumb_icon_color_hover',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'alpha'     => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb > li:is(.brd_sep):hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-breadcrumb > li:is(.brd_sep):hover svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_breadcrumb_space_icon_background_hover',
				'types' => [ 'classic', 'gradient'],
				'exclude'   => ['image'],
				'fields_options'  => [
					'background' => [
						'label' => esc_html__('Background', 'elementskit'),
					]
				],
				'selector' => '{{WRAPPER}} .ekit-breadcrumb > li:is(.brd_sep):hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_breadcrumb_icon_border',
				'fields_options'  => [
					'border' => [
						'label' => esc_html__('Border', 'elementskit'),
					]
				],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .ekit-breadcrumb > li:is(.brd_sep)',
			]
		);

		$this->add_control(
			'ekit_breadcrumb_icon_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-breadcrumb > li:is(.brd_sep)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$pid      = get_the_ID();
		$max_len  = empty($settings['ekit_breadcrumb_len']) ? 15 : intval($settings['ekit_breadcrumb_len']);
		$trail    = !empty($settings['ekit_breadcrumb_show_trail']);

		echo $this->get_crumb($settings, $pid, $max_len, $trail);

	}


	private function get_crumb($settings, $post_id, $max_len, $trail = false) {

		$ret = '<ol class="ekit-breadcrumb">';

		// separator breadcrumbs icon 
		$sep = '';
		if($settings['ekit_bresdcroum_separate_icon_show'] === 'yes') {
			$separate_icon = Icons_Manager::try_get_icon_html($settings['ekit_bresdcroum_separate_icon'],[ 'aria-hidden' => 'true' ]);
			$separate_icon_tag = '<span class="separate_icon">'. $separate_icon .'</span>';
			$sep = !empty( $settings['ekit_bresdcroum_separate_icon']['value']) ? ' <li class="brd_sep">' . $separate_icon_tag . '</li> ' : ' <li class="brd_sep"> &raquo; </li> ' ;
		}

		// home breadcrumb icon 
		$icon =  Icons_Manager::try_get_icon_html($settings['ekit_bresdcroum_home_icon'],[ 'aria-hidden' => 'true' ]);
		$icon_tag = !empty($settings['ekit_bresdcroum_home_icon']['value']) ? '<span class="ekit_home_icon"> '.$icon.' </span>' : '';
		$ret .= '<li class="ekit_breadcrumbs_start">' . $icon_tag . '<a href="' . get_home_url('/') .'">' . __('Home', 'elementskit') . '</a></li>';

		if(!is_home()) {

			if(is_category() || is_single() || is_archive()) {

				$category = get_the_category();

				if(!empty($category)) {

					$cat         = $category[0];
					$term_parent = $cat->parent;
					$taxonomy    = $cat->taxonomy;
					$p_trail     = '';

					if($trail === true) {

						if(0 !== $term_parent) {

							while($term_parent) {

								$term        = get_term($term_parent, $taxonomy);
								$term_parent = $term->parent;

								$p_trail = $sep . '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>' . $p_trail;
							}
						}
					}

					$ret .= $p_trail . $sep . '<li><a href="' . get_category_link($cat->term_id) . '">' . $cat->cat_name . '</a></li>';

				} else {

					$p_type    = get_post_type($post_id);
					$post_type = get_post_type_object($p_type);

					if(!empty($post_type->labels->singular_name) && !in_array($post_type->name, ['post', 'page'])) {

						$ret .= $sep . '<li><a href="' . get_post_type_archive_link($p_type) . '">' . $post_type->labels->singular_name . '</a></li>';

					}
				}

				if(is_single()) {
					$ret .= $this->render_parent_posts($post_id, $max_len, $sep);
				}

			} elseif(is_page()) {
				$ret .= $this->render_parent_posts($post_id, $max_len, $sep);	
			}
		} elseif(is_home()) {
			$page_for_posts = get_option('page_for_posts');
			$ret .= $page_for_posts ? $this->render_parent_posts($page_for_posts, $max_len, $sep) : sprintf('%1$s <li>%2$s</li>', $sep, esc_html__('Posts', 'elementskit'));
		}



		if(is_tag() ) {

			$ret .= '<li>' . single_tag_title() . '</li>';

		} elseif(is_day()) {

			$ret .= '<li>' . esc_html__('Blogs for', 'elementskit') . ' ' . get_the_time('F jS, Y', $post_id) . '</li>';

		} elseif(is_month()) {

			$ret .= '<li>' . esc_html__('Blogs for', 'elementskit') . ' ' . get_the_time('F, Y', $post_id) . '</li>';

		} elseif(is_year()) {

			$ret .= '<li>' . esc_html__('Blogs for', 'elementskit') . ' ' . get_the_time('Y', $post_id) . '</li>';

		} elseif(is_author()) {

			$ret .= $sep . '<li>' . esc_html__('Author Blogs', 'elementskit') . '</li>';

		} elseif(isset($_GET['paged']) && !empty($_GET['paged'])) {

			$ret .= $sep .'<li>' . esc_html__('Blogs', 'elementskit') . '</li>';

		} elseif(is_search()) {

			//the_search_query()

			$ret .= $sep . '<li>' . esc_html__('Search Result', 'elementskit') . '</li>';

		} elseif(is_404()) {

			$ret .= $sep . '<li>' . esc_html__('404 Not Found', 'elementskit') . '</li>';
		}

		$ret .= '</ol>';

		return $ret;
	}

	public function render_parent_posts($post_id, $max_len, $sep) {

		$page_items = [];
		$page_items[] = sprintf(
			'%1$s <li>%2$s</li>',
			$sep, 
			(is_home() && get_option('page_for_posts')) ? wp_trim_words(get_the_title(get_option('page_for_posts')), $max_len) : wp_trim_words(get_the_title(), $max_len)
		);
		$post = get_post($post_id);
		while( $post->post_parent ) {
			$page_items[] = $sep . '<li><a href="'.get_permalink($post->post_parent).'" title="'.get_the_title($post->post_parent).'">'.get_the_title($post->post_parent).'</a></li>';
			$post = get_post($post->post_parent);
		}

		$page_items = array_reverse($page_items);
		$page_list = implode('', $page_items);

		return $page_list;
	}
}
