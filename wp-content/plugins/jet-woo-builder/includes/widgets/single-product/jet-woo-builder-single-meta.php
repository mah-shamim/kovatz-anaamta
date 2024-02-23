<?php
/**
 * Class: Jet_Woo_Builder_Single_Meta
 * Name: Single Meta
 * Slug: jet-single-meta
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Single_Meta extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-single-meta';
	}

	public function get_title() {
		return __( 'Single Meta', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-single-meta';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-single-product-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'single' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-single-meta/css-scheme',
			[
				'meta' => '.elementor-jet-single-meta .product_meta',
			]
		);

		$this->start_controls_section(
			'section_product_meta_style',
			[
				'label' => __( 'Style', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'product_meta_display_type',
			[
				'label'     => __( 'Display', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'column',
				'options'   => jet_woo_builder_tools()->get_available_flex_directions_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['meta'] => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_meta_space_between',
			[
				'label'     => __( 'Space Between', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['meta'] => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'product_meta_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['meta'],
			]
		);

		$this->add_control(
			'product_meta_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['meta'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'product_meta_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['meta'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_meta_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['meta'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_meta__inline_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_flex_h_align_types( true ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['meta'] => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'product_meta_display_type' => 'row',
				],
			]
		);

		$this->add_responsive_control(
			'product_meta__block_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_flex_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['meta'] => 'align-items: {{VALUE}};',
				],
				'condition' => [
					'product_meta_display_type' => 'column',
				],
			]
		);

		$this->add_control(
			'heading_product_meta_value_styles',
			[
				'label'     => __( 'Value', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'product_meta_value_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['meta'] . ' a, {{WRAPPER}} ' . $css_scheme['meta'] . ' > span span',
			]
		);

		$this->add_control(
			'product_meta__value_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['meta'] . ' > span span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_product_meta_value_link_styles',
			[
				'label' => __( 'Link', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs( 'tabs_product_meta_value_link_style' );

		$this->start_controls_tab(
			'tab_product_meta_value_link_normal',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'product_meta__value_link_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['meta'] . ' a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_product_meta_value_link_hover',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'product_meta_value_link_hover_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['meta'] . ' a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_meta_value_link_hover_decoration',
			[
				'label'     => __( 'Text Decoration', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => jet_woo_builder_tools()->get_available_text_decoration_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['meta'] . ' a:hover' => 'text-decoration: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function render() {

		global $product;

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		if ( $this->__set_editor_product() ) {
			$this->__open_wrap();

			woocommerce_template_single_meta();

			$this->__close_wrap();

			if ( jet_woo_builder()->elementor_views->in_elementor() ) {
				$this->__reset_editor_product();
			}
		}

	}

}
