<?php
/**
 * Class: Jet_Woo_Builder_Single_Rating
 * Name: Single Rating
 * Slug: jet-single-rating
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Single_Rating extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-single-rating';
	}

	public function get_title() {
		return __( 'Single Rating', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-rating';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-single-product-page-template/';
	}

	public function get_jet_style_depends() {
		return [ 'jet-woo-builder', 'jet-woo-builder-frontend-font' ];
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'single' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-single-rating/css-scheme',
			[
				'rating_wrapper' => '.elementor-jet-single-rating .woocommerce-product-rating',
				'stars'          => '.elementor-jet-single-rating .product-star-rating',
				'reviews_link'   => '.elementor-jet-single-rating .woocommerce-review-link',
			]
		);
		$this->start_controls_section(
			'section_rating_content',
			[
				'label' => __( 'Product Rating', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_single_empty_rating',
			[
				'label'     => __( 'Empty Rating', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'edit_single_rating_link',
			[
				'label'   => __( 'Edit Link', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'single_rating_reviews_link_url',
			[
				'label'     => __( 'URL', 'jet-woo-builder' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '#reviews',
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'edit_single_rating_link!' => '',
				],
			]
		);

		$this->add_control(
			'single_rating_reviews_link_caption_single',
			[
				'label'       => __( 'Singular Caption', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( ' customer review', 'jet-woo-builder' ),
				'placeholder' => __( 'customer review', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'edit_single_rating_link!' => '',
				],
			]
		);

		$this->add_control(
			'single_rating_reviews_link_caption_plural',
			[
				'label'       => __( 'Plural Caption', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( ' customer reviews', 'jet-woo-builder' ),
				'placeholder' => __( 'customer reviews', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'edit_single_rating_link!' => '',
				],
			]
		);

		$this->add_control(
			'single_rating_reviews_link_before_caption',
			[
				'label'     => __( 'Before Caption', 'jet-woo-builder' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '(',
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'edit_single_rating_link!' => '',
				],
			]
		);

		$this->add_control(
			'single_rating_reviews_link_after_caption',
			[
				'label'     => __( 'After Caption', 'jet-woo-builder' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => ')',
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'edit_single_rating_link!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_rating_styles',
			[
				'label' => __( 'Product Rating', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'rating_icon',
			[
				'label'   => __( 'Stars Type', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'jetwoo-front-icon-rating-1',
				'options' => jet_woo_builder_tools()->get_available_rating_icons_list(),
			]
		);

		$this->add_control(
			'rating_direction',
			[
				'label'     => __( 'Display Type', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'row',
				'options'   => jet_woo_builder_tools()->get_available_flex_directions_types( true ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['rating_wrapper'] => 'flex-direction: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'rating_alignment_horizontal',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => jet_woo_builder_tools()->get_available_flex_h_align_types( true ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['rating_wrapper'] => 'justify-content: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
				'condition' => [
					'rating_direction' => [ 'row', 'row-reverse' ],
				],
			]
		);

		$this->add_responsive_control(
			'rating_alignment_vertical',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => jet_woo_builder_tools()->get_available_flex_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['rating_wrapper'] => 'align-items: {{VALUE}};',
				],
				'condition' => [
					'rating_direction' => [ 'column', 'column-reverse' ],
				],
			]
		);

		$this->add_control(
			'heading_stars_styles',
			array(
				'label'     => esc_html__( 'Stars', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'stars_font_size',
			[
				'label'      => __( 'Size', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['stars'] . ' .product-rating__icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'stars_space_between',
			[
				'label'      => __( 'Space Between', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 2,
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['stars'] . ' .product-rating__icon + .product-rating__icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_stars_styles' );

		$this->start_controls_tab(
			'tab_stars_all',
			array(
				'label' => esc_html__( 'All', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'stars_color_all',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e8e8',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['stars'] . ' .product-rating__icon' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_stars_rated',
			array(
				'label' => esc_html__( 'Rated', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'stars_color_rated',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fdbc32',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['stars'] . ' .product-rating__icon.active' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_stars_empty',
			[
				'label'     => __( 'Empty', 'jet-woo-builder' ),
				'condition' => [
					'show_single_empty_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'stars_color_empty',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .empty .product-star-rating .product-rating__icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'stars_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['stars'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_reviews_link_styles',
			array(
				'label'     => esc_html__( 'Reviews Link', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'reviews_link_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['reviews_link'],
			)
		);

		$this->start_controls_tabs( 'tabs_reviews_link_styles' );

		$this->start_controls_tab(
			'tab_reviews_link_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'reviews_link_color_normal',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['reviews_link'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_reviews_link_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'reviews_link_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['reviews_link'] . ':hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'reviews_link_decoration',
			array(
				'label'     => esc_html__( 'Text Decoration', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => jet_woo_builder_tools()->get_available_text_decoration_types(),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['reviews_link'] . ':hover' => 'text-decoration: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'reviews_link_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['reviews_link'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		if ( $this->__set_editor_product() ) {
			$this->__open_wrap();

			include $this->get_template( 'single-product/rating.php' );

			$this->__close_wrap();

			if ( jet_woo_builder()->elementor_views->in_elementor() ) {
				$this->__reset_editor_product();
			}
		}
	}

}
