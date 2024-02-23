<?php
/**
 * Class: Jet_Woo_Builder_Checkout_Order_Review
 * Name: Checkout Order Review
 * Slug: jet-checkout-order-review
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Checkout_Order_Review extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-checkout-order-review';
	}

	public function get_title() {
		return __( 'Checkout Order Review', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-checkout-order-review';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-checkout-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'checkout' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-checkout-order-review/css-scheme',
			[
				'heading'            => '#order_review_heading:not(.elementor-widget-woocommerce-checkout-page #order_review_heading)',
				'product_price'      => '.woocommerce-checkout-review-order-table td.product-total .amount',
				'product_price_sign' => '.woocommerce-checkout-review-order-table td.product-total .amount .woocommerce-Price-currencySymbol',
				'total_price'        => '.woocommerce-checkout-review-order-table tfoot .woocommerce-Price-amount.amount',
				'total_price_sign'   => '.woocommerce-checkout-review-order-table tfoot .woocommerce-Price-amount.amount .woocommerce-Price-currencySymbol',
				'table_heading'      => '#order_review .woocommerce-checkout-review-order-table th',
				'cell'               => '#order_review .woocommerce-checkout-review-order-table td',
			]
		);

		$this->start_controls_section(
			'checkout_order_review_heading_styles',
			array(
				'label' => esc_html__( 'Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'checkout_order_review', $css_scheme['heading'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_order_review_table_heading_styles',
			array(
				'label' => esc_html__( 'Table Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'checkout_order_review_table_heading_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['table_heading'],
			)
		);

		$this->add_control(
			'checkout_order_review_table_heading_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['table_heading'] => 'color: {{VALUE}}',
				),
			)
		);

		jet_woo_builder_common_controls()->register_table_cell_style_controls( $this, 'checkout_order_review_table_heading', $css_scheme['table_heading'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_order_review_cell_styles',
			[
				'label' => __( 'Table Cell', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'checkout_order_review_cell_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['cell'],
			)
		);

		$this->add_control(
			'checkout_order_review_cell_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['cell'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'checkout_order_review_cell_link_heading',
			[
				'label' => __( 'Links', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs( 'checkout_order_review_cell_link_styles_tabs' );

		$this->start_controls_tab(
			'checkout_order_review_cell_link_styles_normal_tab',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'checkout_order_review_cell_link_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['cell'] . ' a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'checkout_order_review_cell_link_styles_hover_tab',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'checkout_order_review_cell_link_hover_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['cell'] . ' a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		jet_woo_builder_common_controls()->register_table_cell_style_controls( $this, 'checkout_order_review_cell', $css_scheme['cell'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_order_review_price_styles',
			[
				'label' => __( 'Price', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'checkout_order_review_price_styles_tabs' );

		$this->start_controls_tab(
			'checkout_order_review_price_styles_subtotal_tab',
			[
				'label' => __( 'Subtotal', 'jet-woo-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'checkout_order_review_price_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['product_price'],
			]
		);

		$this->add_control(
			'checkout_order_review_product_price_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['product_price'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'checkout_order_review_price_currency_sign_heading',
			[
				'type'  => Controls_Manager::HEADING,
				'label' => __( 'Currency Sign', 'jet-woo-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'checkout_order_review_product_price_sign_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['product_price_sign'],
			]
		);

		$this->add_control(
			'checkout_order_review_product_price_sign_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['product_price_sign'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'checkout_order_review_price_styles_total_tab',
			[
				'label' => __( 'Total', 'jet-woo-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'checkout_order_review_total_price_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['total_price'],
			]
		);

		$this->add_control(
			'checkout_order_review_total_price_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['total_price'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'checkout_order_review_price_total_currency_sign_heading',
			[
				'label' => __( 'Currency Sing', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'checkout_order_review_total_price_sign_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['total_price_sign'],
			]
		);

		$this->add_control(
			'checkout_order_review_total_price_sign_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['total_price_sign'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function render() {

		// Remove actions before displaying our Widget.
		remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

		$this->__open_wrap();

		include $this->get_template( 'checkout/order-review.php' );

		$this->__close_wrap();

		// Add actions after displaying our Widget.
		add_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

	}

}
