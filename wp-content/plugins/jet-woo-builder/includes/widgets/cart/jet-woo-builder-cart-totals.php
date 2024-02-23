<?php
/**
 * Class: Jet_Woo_Builder_Cart_Totals
 * Name: Cart Totals
 * Slug: jet-cart-totals
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Cart_Totals extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-cart-totals';
	}

	public function get_title() {
		return esc_html__( 'Cart Totals', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-cart-totals';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-cart-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'cart' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-cart-totals/css-scheme',
			[
				'heading'              => '.cart_totals h2',
				'table_heading'        => '.cart_totals .shop_table tr th',
				'table_heading_mobile' => '.cart_totals .shop_table_responsive tr td::before',
				'cell'                 => '.cart_totals .shop_table tr td',
				'subtotal_price'       => '.cart_totals .shop_table tr.cart-subtotal td .amount',
				'subtotal_price_sign'  => '.cart_totals .shop_table tr.cart-subtotal td .amount .woocommerce-Price-currencySymbol',
				'total_price'          => '.cart_totals .shop_table tr.order-total td .amount',
				'total_price_sign'     => '.cart_totals .shop_table tr.order-total td .amount .woocommerce-Price-currencySymbol',
				'button'               => '.wc-proceed-to-checkout .button.checkout-button',
				'input'                => '.shipping-calculator-form .form-row .input-text',
				'form_button'          => '.shipping-calculator-form .button',
			]
		);

		$this->start_controls_section(
			'cart_total_heading_section',
			[
				'label' => __( 'Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->add_control(
			'cart_total_heading_visibility',
			[
				'label'                => __( 'Hide', 'jet-woo-builder' ),
				'type'                 => Controls_Manager::SWITCHER,
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
				'selectors_dictionary' => [
					'yes' => '--cart-totals-heading-display: none;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_total_heading_styles_section',
			[
				'label'     => __( 'Heading', 'jet-woo-builder' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'cart_total_heading_visibility' => '',
				],
			]
		);

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'cart_total', $css_scheme['heading'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'total_table_heading_styles',
			array(
				'label' => esc_html__( 'Table Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'total_table_heading_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['table_heading'] => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'total_table_heading_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['table_heading'] . ',{{WRAPPER}} ' . $css_scheme['table_heading_mobile'],
			)
		);

		$this->add_control(
			'total_table_heading_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['table_heading']        => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['table_heading_mobile'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'total_table_heading_background_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .shop_table tbody th' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'total_table_heading_border',
				'label'    => esc_html__( 'Border', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['table_heading'],
			)
		);

		$this->add_responsive_control(
			'total_table_heading_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['table_heading'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'total_table_heading_align',
			[
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['table_heading'] => 'text-align: {{VALUE}}',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'total_table_cell_styles',
			array(
				'label' => esc_html__( 'Table Cell', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'total_table_cell_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['cell'] => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'total_table_cell_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['cell'],
			]
		);

		$this->add_control(
			'total_table_cell_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['cell'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'total_table_cell_link_heading',
			[
				'label' => __( 'Links', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs( 'total_table_cell_link_styles_tabs' );

		$this->start_controls_tab(
			'total_table_cell_link_styles_normal_tab',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'total_table_cell_link_color',
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
			'total_table_cell_link_styles_hover_tab',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'total_table_cell_link_hover_color',
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

		jet_woo_builder_common_controls()->register_table_cell_style_controls( $this, 'total_table_cell', $css_scheme['cell'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_table_price_styles',
			[
				'label' => __( 'Price', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'cart_table_price_styles_tabs' );

		$this->start_controls_tab(
			'cart_table_price_styles_subtotal_tab',
			[
				'label' => __( 'Subtotal', 'jet-woo-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'total_subtotal_price_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['subtotal_price'],
			]
		);

		$this->add_control(
			'total_subtotal_price_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['subtotal_price'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'total_subtotal_currency_sign_heading',
			[
				'label' => __( 'Currency Sing', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'total_subtotal_price_sign_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['subtotal_price_sign'],
			]
		);

		$this->add_control(
			'total_subtotal_price_sign_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['subtotal_price_sign'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_table_price_styles_total_tab',
			[
				'label' => __( 'Total', 'jet-woo-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'total_order_total_price_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['total_price'],
			]
		);

		$this->add_control(
			'total_order_total_price_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['total_price'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'total_total_currency_sign_heading',
			[
				'label' => __( 'Currency Sing', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'total_order_total_price_sign_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['total_price_sign'],
			]
		);

		$this->add_control(
			'total_order_total_price_sign_color',
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

		$this->start_controls_section(
			'total_cart_form_styles_section',
			[
				'label' => __( 'Form', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'total_cart_form_fields_heading',
			[
				'label' => __( 'Fields', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'total_cart_form_input_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['input'] . ', {{WRAPPER}} .select2-container .select2-selection .select2-selection__rendered',
			]
		);

		$this->add_control(
			'total_cart_form_input_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['input']                                            => 'color: {{VALUE}}',
					'{{WRAPPER}} .select2-container .select2-selection .select2-selection__rendered' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'total_cart_form_input_background',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['input']                       => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .select2-container .select2-selection--single' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'total_cart_form_input_border',
				'label'    => __( 'Border', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['input'] . ', {{WRAPPER}} .select2-container .select2-selection--single',
			]
		);

		$this->add_responsive_control(
			'total_cart_form_input_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['input']                       => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .select2-container .select2-selection--single' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'total_cart_form_input_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} .shipping-calculator-form .form-row' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'total_cart_form_input_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['input']                                                             => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered' => 'line-height: calc( ({{TOP}}{{UNIT}}*2) + 16px ); padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__arrow'    => 'height: calc( ({{TOP}}{{UNIT}}*2) + 16px ); right: {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .select2-container--default .select2-selection--single'                              => 'height: auto;',
				],
			]
		);

		$this->add_control(
			'total_cart_form_button_heading',
			[
				'label'     => __( 'Button', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'total_cart_form', $css_scheme['form_button'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'total_cart_button_styles',
			array(
				'label' => esc_html__( 'Button', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'total_cart_button',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['button'] => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'total_cart', $css_scheme['button'] );

		$this->add_responsive_control(
			'total_cart_button_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} .cart_totals .wc-proceed-to-checkout' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'total_cart_button_align',
			[
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout' => 'text-align: {{VALUE}}',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$this->__open_wrap();

		woocommerce_cart_totals();

		$this->__close_wrap();

	}

}
