<?php
/**
 * Class: Jet_Woo_Builder_Cart_Table
 * Name: Cart Table
 * Slug: jet-cart-table
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Cart_Table extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-cart-table';
	}

	public function get_title() {
		return esc_html__( 'Cart Table', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-cart-table';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-cart-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'cart' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-cart-table/css-scheme',
			[
				'heading'            => '.shop_table.cart thead th',
				'cell'               => '.shop_table.cart td',
				'image'              => '.shop_table.cart tr.cart_item td.product-thumbnail img',
				'title'              => '.shop_table.cart tr.cart_item td.product-name',
				'product_price'      => '.shop_table.cart tr.cart_item td.product-price .amount',
				'product_price_sign' => '.shop_table.cart tr.cart_item td.product-price .amount .woocommerce-Price-currencySymbol',
				'total_price'        => '.shop_table.cart tr.cart_item td.product-subtotal .amount',
				'total_price_sign'   => '.shop_table.cart tr.cart_item td.product-subtotal .amount .woocommerce-Price-currencySymbol',
				'update_button'      => '.shop_table.cart tr td.actions .button[name="update_cart"]',
				'coupon_button'      => '.shop_table.cart td.actions .coupon .button',
				'remove_button'      => '.shop_table.cart td.product-remove .remove',
				'input'              => '.shop_table.cart td.actions .coupon input.input-text',
				'product_count'      => '.shop_table.cart td.product-quantity .quantity input.input-text',
				'coupon_form'        => '.shop_table.cart td.actions .coupon',
			]
		);

		$this->start_controls_section(
			'cart_table_content_section',
			[
				'label' => __( 'Table', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$repeater = new Repeater();

		$repeater->add_control(
			'cart_table_items',
			[
				'label'   => __( 'Table Item', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'remove',
				'options' => [
					'remove'       => __( 'Remove', 'jet-woo-builder' ),
					'thumbnail'    => __( 'Thumbnail', 'jet-woo-builder' ),
					'name'         => __( 'Product Title', 'jet-woo-builder' ),
					'price'        => __( 'Price', 'jet-woo-builder' ),
					'quantity'     => __( 'Quantity', 'jet-woo-builder' ),
					'subtotal'     => __( 'Total', 'jet-woo-builder' ),
					'custom_field' => __( 'Custom Field', 'jet-woo-builder' ),
				],
			]
		);

		$repeater->add_control(
			'cart_table_heading_title',
			[
				'label' => __( 'Heading', 'jet-woo-builder' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'cart_table_thumbnail_size',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Thumbnail Size', 'jet-woo-builder' ),
				'default'   => 'woocommerce_thumbnail',
				'options'   => jet_woo_builder_tools()->get_image_sizes(),
				'condition' => [
					'cart_table_items' => 'thumbnail',
				],
			]
		);

		$this->__add_advanced_icon_control(
			'cart_table_remove_icon',
			[
				'label'       => __( 'Icon', 'jet-woo-builder' ),
				'type'        => Controls_Manager::ICON,
				'file'        => '',
				'default'     => 'fa fa-times',
				'fa5_default' => [
					'value'   => 'fas fa-times',
					'library' => 'fa-solid',
				],
				'condition'   => [
					'cart_table_items' => 'remove',
				],
			],
			$repeater
		);

		$repeater->add_control(
			'cart_table_custom_field',
			array(
				'label'     => esc_html__( 'Meta Field Key', 'jet-woo-builder' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'cart_table_items' => 'custom_field',
				),
			)
		);

		$repeater->add_control(
			'cart_table_custom_field_fallback',
			array(
				'label'       => esc_html__( 'Fallback', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'Show this if field value is empty', 'jet-woo-builder' ),
				'condition'   => array(
					'cart_table_items' => 'custom_field',
				),
			)
		);

		$repeater->add_responsive_control(
			'cart_table_cell_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Column Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
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
				'selectors'  => [
					'{{WRAPPER}} .shop_table.shop_table_responsive.cart tr {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cart_table_items_list',
			[
				'label'       => __( 'Items', 'jet-woo-builder' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'cart_table_items'         => 'remove',
						'cart_table_heading_title' => __( 'Remove', 'jet-woo-builder' ),
					],
					[
						'cart_table_items'         => 'thumbnail',
						'cart_table_heading_title' => __( 'Thumbnail', 'jet-woo-builder' ),
					],
					[
						'cart_table_items'         => 'name',
						'cart_table_heading_title' => __( 'Product Title', 'jet-woo-builder' ),
					],
					[
						'cart_table_items'         => 'price',
						'cart_table_heading_title' => __( 'Price', 'jet-woo-builder' ),
					],
					[
						'cart_table_items'         => 'quantity',
						'cart_table_heading_title' => __( 'Quantity', 'jet-woo-builder' ),
					],
					[
						'cart_table_items'         => 'subtotal',
						'cart_table_heading_title' => __( 'Total', 'jet-woo-builder' ),
					],
				],
				'title_field' => '{{{ cart_table_heading_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_table_action_controls',
			array(
				'label' => esc_html__( 'Action Controls', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'cart_update_automatically',
			[
				'label'                => __( 'Update Cart Automatically', 'jet-woo-builder' ),
				'type'                 => Controls_Manager::SWITCHER,
				'description'          => __( 'Changes to the cart will update automatically.', 'jet-woo-builder' ),
				'frontend_available'   => true,
				'render_type'          => 'template',
				'selectors_dictionary' => [
					'yes' => '--cart-update-automatically-display: none;',
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_table_update_button_heading',
			[
				'label'     => __( 'Update Button', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'cart_update_automatically!' => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_table_update_button_text',
			[
				'label'       => __( 'Label', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Update Cart', 'jet-woo-builder' ),
				'placeholder' => __( 'Update Cart', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'cart_update_automatically!' => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_table_coupon_form_heading',
			[
				'label'     => __( 'Coupon Form', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cart_table_show_coupon_form',
			[
				'label'   => __( 'Show', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'cart_table_coupon_form_button_text',
			[
				'label'       => __( 'Button Label', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Apply Coupon', 'jet-woo-builder' ),
				'placeholder' => __( 'Apply Coupon', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'cart_table_show_coupon_form' => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_table_coupon_form_placeholder_text',
			[
				'label'       => __( 'Placeholder Text', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Coupon Code', 'jet-woo-builder' ),
				'placeholder' => __( 'Coupon Code', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'cart_table_show_coupon_form' => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_table_coupon_float',
			[
				'label'     => __( 'Coupon Form Float', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HIDDEN,
				'default'   => 'none',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['coupon_form'] => 'float: {{VALUE}} !important;',
				],
				'condition' => [
					'cart_update_automatically' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_table_heading_styles',
			[
				'label' => __( 'Table Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cart_table_heading_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['heading'],
			)
		);

		$this->add_control(
			'cart_table_heading_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['heading'] => 'color: {{VALUE}}',
				),
			)
		);

		jet_woo_builder_common_controls()->register_table_cell_style_controls( $this, 'cart_table_heading', $css_scheme['heading'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_table_cell_styles',
			[
				'label' => __( 'Table Cell', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cart_table_cell_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['cell'],
			)
		);

		$this->add_control(
			'cart_table_cell_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['cell'] => 'color: {{VALUE}}',
				),
			)
		);

		jet_woo_builder_common_controls()->register_table_cell_style_controls( $this, 'cart_table_cell', $css_scheme['cell'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_product_image_styles',
			[
				'label' => __( 'Thumbnail', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'cart_product_image_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 500,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['image'] => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'cart_product_image_border',
				'selector' => '{{WRAPPER}} ' . $css_scheme['image'],
			]
		);

		$this->add_responsive_control(
			'cart_product_image_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['image'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_product_image_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['image'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_table_product_title_styles',
			array(
				'label' => esc_html__( 'Product Title', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cart_table_product_title_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['title'],
			)
		);

		$this->start_controls_tabs( 'cart_table_product_title_color_style_tabs' );

		$this->start_controls_tab(
			'cart_table_product_title_color_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'cart_table_product_title_normal_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title']        => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['title'] . ' a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_table_product_title_color_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'cart_table_product_title_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] . ':hover'   => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['title'] . ' a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_table_price_styles',
			[
				'label' => esc_html__( 'Price', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'cart_table_price_styles_tabs' );

		$this->start_controls_tab(
			'cart_table_price_styles_tab',
			[
				'label' => __( 'Price', 'jet-woo-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cart_table_product_price_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['product_price'],
			]
		);

		$this->add_control(
			'cart_table_product_price_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['product_price'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cart_table_price_currency_sign_heading',
			[
				'label' => __( 'Currency Sing', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cart_table_product_price_sign_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['product_price_sign'],
			]
		);

		$this->add_control(
			'cart_table_product_price_sign_color',
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
			'cart_table_total_price_styles_tab',
			[
				'label' => __( 'Total', 'jet-woo-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cart_table_total_price_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['total_price'],
			]
		);

		$this->add_control(
			'cart_table_total_price_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['total_price'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cart_table_total_price_currency_sign_heading',
			[
				'label' => __( 'Currency Sing', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cart_table_total_price_sign_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['total_price_sign'],
			]
		);

		$this->add_control(
			'cart_table_total_price_sign_color',
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
			'cart_table_product_count_styles',
			[
				'label' => __( 'Quantity Input', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'cart_table_product_count_input_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'px' => [
						'min' => 40,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['product_count'] => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		jet_woo_builder_common_controls()->register_input_style_controls( $this, 'cart_table_product_count', $css_scheme['product_count'], false );

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_table_remove_button_styles',
			array(
				'label' => esc_html__( 'Remove Button', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'cart_table_remove_button_icon_size',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Icon Size', 'jet-woo-builder' ),
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
					'{{WRAPPER}} ' . $css_scheme['remove_button'] => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'cart_table_remove_button_style_tabs' );

		$this->start_controls_tab(
			'cart_table_remove_button_normal_styles',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'cart_table_remove_button_normal_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['remove_button'] . ' i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['remove_button'] . ' svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_table_remove_button_background',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['remove_button'] => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_table_remove_button_hover_styles',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'cart_table_remove_button_hover_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['remove_button'] . ':hover i, {{WRAPPER}} ' . $css_scheme['remove_button'] . ':focus i'     => 'color: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['remove_button'] . ':hover svg, {{WRAPPER}} ' . $css_scheme['remove_button'] . ':focus svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_table_remove_button_background_hover',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['remove_button'] . ':hover, {{WRAPPER}} ' . $css_scheme['remove_button'] . ':focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_table_remove_button_border_color_hover',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['remove_button'] . ':hover, {{WRAPPER}} ' . $css_scheme['remove_button'] . ':focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'cart_table_remove_button_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'cart_table_remove_button_border',
				'label'     => __( 'Border', 'jet-woo-builder' ),
				'selector'  => '{{WRAPPER}} ' . $css_scheme['remove_button'],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'cart_table_remove_button_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['remove_button'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_table_remove_button_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['remove_button'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_table_update_button_styles',
			[
				'label'     => __( 'Update Button', 'jet-woo-builder' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'cart_update_automatically!' => 'yes',
				],
			]
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'cart_table_update', $css_scheme['update_button'] );

		$this->add_responsive_control(
			'cart_table_update_button_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['update_button'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_table_apply_coupon_styles',
			[
				'label'     => __( 'Coupon Form', 'jet-woo-builder' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'cart_table_show_coupon_form' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'cart_table_apply_coupon_display_type',
			[
				'label'     => __( 'Display', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'row',
				'options'   => jet_woo_builder_tools()->get_available_flex_directions_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['coupon_form'] => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_table_apply_coupon_button_title',
			[
				'label'     => __( 'Button', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'cart_table_apply_coupon_button_width',
			[
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['coupon_button'] => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'cart_table_apply_coupon', $css_scheme['coupon_button'] );

		$this->add_responsive_control(
			'cart_table_apply_coupon_button_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['coupon_button'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cart_table_apply_coupon_input_title',
			[
				'label'     => __( 'Input', 'jet-woo-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'cart_table_apply_coupon_input_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['input'] => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		jet_woo_builder_common_controls()->register_input_style_controls( $this, 'cart_table_apply_coupon', $css_scheme['input'] );

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$table_settings = [
			'items'      => $settings['cart_table_items_list'] ?? [],
			'components' => [
				'update-automatically'     => isset( $settings['cart_update_automatically'] ) ? filter_var( $settings['cart_update_automatically'], FILTER_VALIDATE_BOOLEAN ) : false,
				'update-button-label'      => $settings['cart_table_update_button_text'] ?? 'Update cart',
				'coupon-form-placeholder'  => $settings['cart_table_coupon_form_placeholder_text'] ?? 'Coupon code',
				'coupon-form-button-label' => $settings['cart_table_coupon_form_button_text'] ?? 'Apply coupon',
			],
		];

		// Add & Remove actions & filters before displaying our Widget.
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
		// Hide coupon form.
		add_filter( 'woocommerce_coupons_enabled', [ $this, 'cart_table_coupon_form_enable' ] );

		$this->__open_wrap();

		Jet_Woo_Builder_Shortcode_Cart::output( $atts = [], $table_settings, $this );

		$this->__close_wrap();

		// Add & Remove actions & filters after displaying our Widget.
		add_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
		remove_filter( 'woocommerce_coupons_enabled', [ $this, 'cart_table_coupon_form_enable' ] );

	}

	/**
	 * Control visibility of cart table coupon form.
	 *
	 * @since 1.12.0
	 *
	 * @param $enable_coupon
	 *
	 * @return mixed
	 */
	public function cart_table_coupon_form_enable( $enable_coupon ) {

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['cart_table_show_coupon_form'] ) && '' === $settings['cart_table_show_coupon_form'] ) {
			$enable_coupon = filter_var( $settings['cart_table_show_coupon_form'], FILTER_VALIDATE_BOOLEAN );
		}

		return $enable_coupon;

	}

}

/**
 * Class Jet_Woo_Builder_Shortcode_Cart
 *
 * Used on the cart page, the cart shortcode displays the cart contents and interface for coupon codes and other cart
 * bits and pieces.
 *
 * @package WooCommerce/Shortcodes/Cart
 * @version 2.3.0
 */
class Jet_Woo_Builder_Shortcode_Cart extends \WC_Shortcode_Cart {

	public static function output( $atts = [], $table_settings = [], $widget = null ) {

		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );

		$nonce_value = wc_get_var( $_REQUEST['woocommerce-shipping-calculator-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) );

		if ( ! empty( $_POST['calc_shipping'] ) && ( wp_verify_nonce( $nonce_value, 'woocommerce-shipping-calculator' ) || wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) ) {
			self::calculate_shipping();

			WC()->cart->calculate_totals();
		}

		do_action( 'woocommerce_check_cart_items' );

		WC()->cart->calculate_totals();

		if ( WC()->cart->is_empty() && ! jet_woo_builder()->elementor_views->in_elementor() ) {
			wc_get_template( 'cart/cart-empty.php' );
		} else {
			$template = jet_woo_builder()->get_template( 'jet-cart-table/global/index.php' );

			if ( ! $template ) {
				$template = jet_woo_builder()->get_template( 'widgets/cart/cart-table.php' );
			}

			include $template;
		}

	}

}
