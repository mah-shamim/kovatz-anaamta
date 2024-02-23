<?php
/**
 * Class: Jet_Woo_Categories
 * Name: Categories Grid
 * Slug: jet-woo-categories
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Categories extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-categories';
	}

	public function get_title() {
		return __( 'Categories Grid', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-categories-grid';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-categories-grid-widget-overview/';
	}

	public function get_jet_style_depends() {
		return [ 'jet-woo-builder', 'elementor-icons-fa-solid' ];
	}

	public function __shortcode() {
		return jet_woo_builder_shortcodes()->get_shortcode( $this->get_name() );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			[
				'label' => __( 'Categories Grid', 'jet-woo-builder' ),
			]
		);

		if ( $this->__shortcode() ) {
			$attributes = $this->__shortcode()->get_atts();

			foreach ( $attributes as $attr => $settings ) {
				if ( empty( $settings['type'] ) ) {
					continue;
				}

				if ( ! empty( $settings['responsive'] ) ) {
					$this->add_responsive_control( $attr, $settings );
				} else {
					$this->add_control( $attr, $settings );
				}
			}
		}

		$this->end_controls_section();

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-woo-categories/css-scheme',
			array(
				'wrap'          => '.jet-woo-categories',
				'column'        => '.jet-woo-categories .jet-woo-categories__item',
				'inner-box'     => '.jet-woo-categories .jet-woo-categories__inner-box',
				'thumb'         => '.jet-woo-categories .jet-woo-category-thumbnail',
				'content'       => '.jet-woo-categories .jet-woo-categories-content',
				'title-wrap'    => '.jet-woo-categories .jet-woo-categories-title__wrap',
				'title'         => '.jet-woo-categories .jet-woo-category-title',
				'count-wrap'    => '.jet-woo-categories .jet-woo-category-count__wrap',
				'count'         => '.jet-woo-categories .jet-woo-category-count',
				'excerpt'       => '.jet-woo-categories .jet-woo-category-excerpt',
				'overlay'       => '.jet-woo-categories .jet-woo-category-img-overlay',
				'overlay-hover' => '.jet-woo-categories .jet-woo-category-img-overlay__hover',
			)
		);

		$this->start_controls_section(
			'section_carousel',
			[
				'label' => __( 'Carousel', 'jet-woo-builder' ),
			]
		);

		jet_woo_builder_common_controls()->register_carousel_controls( $this );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_column_style',
			[
				'label' => __( 'Column', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_padding',
			[
				'label'       => __( 'Padding', 'jet-woo-builder' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px' ],
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} ' . $css_scheme['column']                         => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['wrap'] . ':not(.swiper-wrapper)' => 'margin-right: -{{RIGHT}}{{UNIT}}; margin-left: -{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_box_style',
			[
				'label' => __( 'Grid Item', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'box_style_tabs' );

		$this->start_controls_tab(
			'box_normal_styles',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'box_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'inner_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['inner-box'],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_hover_styles',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'inner_box_hover_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'inner_box_hover_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'box_border',
				'label'     => __( 'Border', 'jet-woo-builder' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['inner-box'],
			]
		);

		$this->add_responsive_control(
			'box_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['inner-box'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['inner-box'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_thumb_style',
			[
				'label' => __( 'Thumbnail', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_thumb_style' );

		$this->start_controls_tab(
			'tab_thumb_normal',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'thumb_background',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['thumb'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumb_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['thumb'],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumb_hover',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'thumb_background_hover',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .jet-woo-category-thumbnail' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumb_box_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .jet-woo-category-thumbnail',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'thumb_border',
				'label'     => __( 'Border', 'jet-woo-builder' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['thumb'],
			]
		);

		$this->add_responsive_control(
			'thumb_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['thumb'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['thumb'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['thumb'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'box_content_style_tabs' );

		$this->start_controls_tab(
			'box_content_normal_styles',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'content_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['content'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_content_hover_styles',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'box_hover_content_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .jet-woo-categories-content' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'content_hover_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .jet-woo-categories-content' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'content_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'content_border',
				'label'     => __( 'Border', 'jet-woo-builder' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['content'],
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['content'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'content_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['content'],
			)
		);

		$this->add_responsive_control(
			'content_margin',
			[
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['content'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['content'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label'     => __( 'Title', 'jet-woo-builder' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['title'] . ', {{WRAPPER}} ' . $css_scheme['title'] . ' a',
			]
		);

		$this->add_control(
			'title_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->start_controls_tabs( 'box_content_title_style_tabs' );

		$this->start_controls_tab(
			'box_content_title_normal_styles',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['title'] . ' a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_content_title_hover_styles',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'box_hover_title_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .jet-woo-category-title' . ' a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_hover_title_decoration',
			array(
				'label'     => esc_html__( 'Text Decoration', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => jet_woo_builder_tools()->get_available_text_decoration_types(),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .jet-woo-category-title' . ' a' => 'text-decoration: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'title_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['title'],
			]
		);

		$this->add_responsive_control(
			'title_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['title'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['title'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title']      => 'text-align: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['title-wrap'] => 'text-align: {{VALUE}};',
				),
				'classes'   => 'elementor-control-align',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_count_style',
			[
				'label'     => __( 'Count', 'jet-woo-builder' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_count' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'count_min_width',
			[
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'min-width: {{SIZE}}{{UNIT}}; text-align: center;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'count_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['count'],
			]
		);

		$this->start_controls_tabs( 'box_content_count_style_tabs' );

		$this->start_controls_tab(
			'box_content_count_normal_styles',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'count_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'count_bg',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_content_count_hover_styles',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'box_hover_count_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .jet-woo-category-count' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_hover_count_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .jet-woo-category-count' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'count_hover_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .jet-woo-category-count' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'count_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'count_border',
				'label'     => __( 'Border', 'jet-woo-builder' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['count'],
			]
		);

		$this->add_responsive_control(
			'count_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'inner_count_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['count'],
			)
		);

		$this->add_responsive_control(
			'count_margin',
			[
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'count_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['count'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'count_vertical_position',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Vertical Position', 'jet-woo-builder' ),
				'default'   => 'top',
				'options'   => [
					'top'    => __( 'Top', 'jet-woo-builder' ),
					'bottom' => __( 'Bottom', 'jet-woo-builder' ),
				],
				'condition' => [
					'presets' => 'preset-1',
				],
			]
		);

		$this->add_responsive_control(
			'count_top_indent',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Top Indent', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['count-wrap'] => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
				],
				'condition'  => [
					'count_vertical_position' => 'top',
					'presets'                 => 'preset-1',
				],
			]
		);

		$this->add_responsive_control(
			'count_bottom_indent',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Bottom Indent', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'range'      => jet_woo_builder_tools()->get_available_units_ranges(),
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['count-wrap'] => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
				],
				'condition'  => [
					'count_vertical_position' => 'bottom',
					'presets'                 => 'preset-1',
				],
			]
		);

		$this->add_responsive_control(
			'count_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['count-wrap'] => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'presets!' => 'preset-3',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_excerpt_style',
			[
				'label' => __( 'Excerpt', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['excerpt'],
			]
		);

		$this->start_controls_tabs( 'box_content-excerpt_style_tabs' );

		$this->start_controls_tab(
			'box_content-excerpt_normal_styles',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_content-excerpt_hover_styles',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'box_hover_excerpt_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .jet-woo-category-excerpt' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'excerpt_bg',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => jet_woo_builder_tools()->get_available_h_align_types( true ),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'text-align: {{VALUE}};',
				),
				'classes'   => 'elementor-control-align',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_overlay_style',
			[
				'label' => __( 'Overlay', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_overlay_style' );

		$this->start_controls_tab(
			'tab_overlay_normal',
			[
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_bg',
				'fields_options' => [
					'image' => [
						'dynamic' => [
							'active' => false,
						],
					],
				],
				'selector'       => '{{WRAPPER}} ' . $css_scheme['overlay'],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_overlay_hover',
			[
				'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_bg_hover',
				'fields_options' => [
					'image' => [
						'dynamic' => [
							'active' => false,
						],
					],
				],
				'selector'       => '{{WRAPPER}} ' . $css_scheme['column'] . ':hover .jet-woo-category-img-overlay__hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_arrows_style', [
				'label'     => __( 'Carousel Navigation', 'jet-woo-builder' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'carousel_enabled' => 'yes',
					'arrows'           => 'yes',
				],
			]
		);

		jet_woo_builder_common_controls()->register_carousel_navigation_style_controls( $this );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dots_style',
			[
				'label'     => __( 'Carousel Pagination', 'jet-woo-builder' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'carousel_enabled' => 'yes',
					'dots!'            => '',
				],
			]
		);

		jet_woo_builder_common_controls()->register_carousel_pagination_style_controls( $this );

		$this->end_controls_section();

	}

	protected function render() {

		$attributes    = [];
		$settings      = $this->get_settings_for_display();
		$shortcode_obj = $this->__shortcode();

		if ( isset( $settings['selected_prev_arrow'] ) || isset( $settings['prev_arrow'] ) ) {
			$settings['prev_arrow'] = htmlspecialchars( $this->__render_icon( 'prev_arrow', '%s', '', false ) );
		}

		if ( isset( $settings['selected_next_arrow'] ) || isset( $settings['next_arrow'] ) ) {
			$settings['next_arrow'] = htmlspecialchars( $this->__render_icon( 'next_arrow', '%s', '', false ) );
		}

		$shortcode_obj->set_settings( $settings );

		foreach ( $shortcode_obj->get_atts() as $attr => $data ) {
			$attr_val            = $settings[ $attr ];
			$attr_val            = ! is_array( $attr_val ) ? $attr_val : implode( ',', $attr_val );
			$attributes[ $attr ] = $attr_val;
		}

		$this->__open_wrap();

		echo jet_woo_builder_tools()->get_carousel_wrapper_atts( $shortcode_obj->do_shortcode( $attributes ), $settings );

		$this->__close_wrap();

	}

}
