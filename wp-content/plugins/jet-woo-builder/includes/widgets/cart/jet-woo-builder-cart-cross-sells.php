<?php
/**
 * Class: Jet_Woo_Builder_Cart_Cross_Sells
 * Name: Cart Cross Sells
 * Slug: jet-cart-cross-sells
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Cart_Cross_Sells extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-cart-cross-sells';
	}

	public function get_title() {
		return __( 'Cart Cross Sells', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-cart-cross-sells';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-cart-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'cart' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-cart-cross-sells/css-scheme',
			[
				'heading' => '.cross-sells > h2',
				'item'    => 'ul.products li.product',
				'thumb'   => 'ul.products li.product img',
				'title'   => 'ul.products li.product .woocommerce-loop-product__title',
				'rating'  => 'ul.products li.product .star-rating',
				'price'   => 'ul.products li.product .price',
				'button'  => 'ul.products li.product .button',
				'badge'   => 'ul.products li.product span.onsale',
			]
		);

		$this->start_controls_section(
			'cross_sell_section',
			[
				'label' => __( 'Cross Sells', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->add_control(
			'cross_sell_products_columns',
			[
				'label'   => __( 'Columns', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 2,
				'options' => jet_woo_builder_tools()->get_select_range( 12 ),
			]
		);

		$this->add_control(
			'cross_sell_products_orderby',
			[
				'label'   => __( 'Order by', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rand',
				'options' => [
					'date'       => __( 'Date', 'jet-woo-builder' ),
					'title'      => __( 'Title', 'jet-woo-builder' ),
					'price'      => __( 'Price', 'jet-woo-builder' ),
					'popularity' => __( 'Popularity', 'jet-woo-builder' ),
					'rating'     => __( 'Rating', 'jet-woo-builder' ),
					'rand'       => __( 'Random', 'jet-woo-builder' ),
					'modified'   => __( 'Modified Date', 'jet-woo-builder' ),
				],
			]
		);

		$this->add_control(
			'cross_sell_products_order',
			array(
				'label'   => esc_html__( 'Order', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => jet_woo_builder_tools()->order_arr(),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cross_sell_heading_section',
			[
				'label' => __( 'Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'cross_sell_products_show_heading',
			[
				'label'   => __( 'Show', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'cross_sell_products_edit_heading',
			[
				'label'     => __( 'Modify', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'cross_sell_products_show_heading' => 'yes',
				],
			]
		);

		$this->add_control(
			'cross_sell_products_edit_heading_area',
			[
				'label'       => __( 'Heading', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your heading', 'jet-woo-builder' ),
				'default'     => __( 'You may be interested in...', 'jet-woo-builder' ),
				'condition'   => [
					'cross_sell_products_show_heading' => 'yes',
					'cross_sell_products_edit_heading' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cross_sells_heading_styles',
			[
				'label' => __( 'Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'cross_sells', $css_scheme['heading'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'cross_sells_card_styles',
			[
				'label' => __( 'Item', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'cross_sells_card_style_tabs' );

		$this->start_controls_tab(
			'cross_sells_card_style_tab_normal',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'cross_sells_card_bg_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['item'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cross_sells_card_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['item'],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cross_sells_card_style_tab_hover',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'cross_sells_card_bg_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['item'] . ':hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cross_sells_card_border_color_hover',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['item'] . ':hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'cross_sells_card_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cross_sells_card_box_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['item'] . ':hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'cross_sells_card_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['item'],
			]
		);

		$this->add_responsive_control(
			'cross_sells_card_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['item'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cross_sells_card_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['item'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cross_sells_card_align',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['item'] => 'text-align: {{VALUE}}',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cross_sells_thumb_styles',
			array(
				'label' => esc_html__( 'Thumbnail', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'cross_sells_thumb_border',
				'label'    => __( 'Border', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['thumb'],
			]
		);

		$this->add_responsive_control(
			'cross_sells_thumb_border_radius',
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
			'cross_sells_thumb_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['thumb'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cross_sells_title_styles',
			array(
				'label' => esc_html__( 'Title', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cross_sells_title_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['title'],
			]
		);

		$this->start_controls_tabs( 'cross_sells_title_color_style_tabs' );

		$this->start_controls_tab(
			'cross_sells_title_normal_color_tab',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'cross_sells_title_normal_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['title'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cross_sells_title_hover_color_tab',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'cross_sells_title_hover_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['title'] . ':hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'cross_sells_title_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['title'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cross_sells_rating_styles',
			[
				'label' => __( 'Rating', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'cross_sells_rating_star_color',
			[
				'label'     => __( 'Star Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['rating'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cross_sells_rating_empty_star_color',
			[
				'label'     => __( 'Empty Star Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['rating'] . '::before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'cross_sells_rating_font_size',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Star Size', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em' ] ),
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
					'em' => [
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['rating'] => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cross_sells_rating_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['rating'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cross_sells_price_styles',
			[
				'label' => esc_html__( 'Price', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cross_sells_price_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['price'],
			)
		);

		$this->add_control(
			'cross_sells_price_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['price']              => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['price'] . ' .amount' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'cross_sells_price_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['price'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'cross_sells_price_styles_tabs' );

		$this->start_controls_tab(
			'cross_sells_price_regular_tab',
			array(
				'label' => esc_html__( 'Regular', 'jet-woo-builder' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cross_sells_price_regular_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['price'] . ' del, {{WRAPPER}} ' . $css_scheme['price'] . ' del .amount',
			]
		);

		$this->add_control(
			'cross_sells_price_regular_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['price'] . ' del'         => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['price'] . ' del .amount' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cross_sells_price_sale_tab',
			array(
				'label' => esc_html__( 'Sale', 'jet-woo-builder' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cross_sells_price_sale_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['price'] . ' ins, {{WRAPPER}} ' . $css_scheme['price'] . ' ins .amount',
			]
		);

		$this->add_control(
			'cross_sells_price_sale_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['price'] . ' ins'         => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['price'] . ' ins .amount' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'cross_sells_button_styles',
			array(
				'label' => esc_html__( 'Button', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'cross_sells', $css_scheme['button'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'cross_sells_badges_styles',
			[
				'label' => __( 'Sale Flash', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'cross_sells_badges_min_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Min Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['badge'] => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cross_sells_badges_min_height',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Min Height', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['badge'] => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cross_sells_badges_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['badge'],
			]
		);

		$this->add_control(
			'cross_sells_badges_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['badge'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'cross_sells_badges_background_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['badge'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'cross_sells_badges_border',
				'label'    => __( 'Border', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['badge'],
			]
		);

		$this->add_responsive_control(
			'cross_sells_badges_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['badge'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cross_sells_badges_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['badge'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$columns = isset( $settings['cross_sell_products_columns'] ) ? $settings['cross_sell_products_columns'] : 2;
		$orderby = isset( $settings['cross_sell_products_orderby'] ) ? $settings['cross_sell_products_orderby'] : 'rand';
		$order   = isset( $settings['cross_sell_products_order'] ) ? $settings['cross_sell_products_order'] : 'desc';

		// Add filters before displaying our Widget.
		// Change cross-sells products section heading.
		add_filter( 'woocommerce_product_cross_sells_products_heading', [ $this, 'modify_cart_cross_sells_products_heading' ] );

		$this->__open_wrap();

		woocommerce_cross_sell_display( -1, $columns, $orderby, $order );

		$this->__close_wrap();

		// Remove filters after displaying our Widget.
		remove_filter( 'woocommerce_product_cross_sells_products_heading', [ $this, 'modify_cart_cross_sells_products_heading' ] );

	}

	/**
	 * Change default cross sells area heading.
	 *
	 * @since 1.12.0
	 *
	 * @param $heading
	 *
	 * @return string
	 */
	public function modify_cart_cross_sells_products_heading( $heading ) {

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['cross_sell_products_show_heading'] ) && 'yes' !== $settings['cross_sell_products_show_heading'] ) {
			return '';
		}

		if ( isset( $settings['cross_sell_products_edit_heading'] ) && 'yes' === $settings['cross_sell_products_edit_heading'] ) {
			if ( isset( $settings['cross_sell_products_edit_heading_area'] ) && ! empty( $settings['cross_sell_products_edit_heading_area'] ) ) {
				$heading = $settings['cross_sell_products_edit_heading_area'];
			}
		}

		return $heading;

	}

}
