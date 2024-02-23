<?php
/**
 * Class: Jet_Woo_Builder_ThankYou_Order_Details
 * Name: Thank You Order Details
 * Slug: jet-thankyou-order-details
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_ThankYou_Order_Details extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-thankyou-order-details';
	}

	public function get_title() {
		return __( 'Thank You Order Details', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-thank-you-order-details';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-thank-you-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'thankyou' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-thankyou-order-details/css-scheme',
			array(
				'heading'       => '.elementor-jet-thankyou-order-details h2',
				'table_heading' => '.elementor-jet-thankyou-order-details .woocommerce-table.order_details tr th',
				'table_content' => '.elementor-jet-thankyou-order-details .woocommerce-table.shop_table.order_details tr td',
			)
		);

		$this->start_controls_section(
			'thankyou_details_heading_styles',
			array(
				'label' => esc_html__( 'Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'thankyou_details', $css_scheme['heading'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'thankyou_details_table_heading_styles',
			array(
				'label' => esc_html__( 'Table Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'thankyou_details_table_heading_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['table_heading'],
			)
		);

		$this->add_control(
			'thankyou_details_table_heading_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['table_heading'] => 'color: {{VALUE}}',
				),
			)
		);

		jet_woo_builder_common_controls()->register_table_cell_style_controls( $this, 'thankyou_details_table_heading', $css_scheme['table_heading'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'thankyou_details_cell_styles',
			array(
				'label' => esc_html__( 'Table Cells', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'thankyou_details_cell_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['table_content'],
			)
		);

		$this->add_control(
			'thankyou_details_cell_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['table_content']                        => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['table_content'] . ' .product-quantity' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'thankyou_details_cell_link_heading',
			[
				'label' => __( 'Links', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs( 'thankyou_details_cell_link_styles_tabs' );

		$this->start_controls_tab(
			'thankyou_details_cell_link_styles_normal_tab',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'thankyou_details_cell_link_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['table_content'] . ' a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'thankyou_details_cell_link_styles_hover_tab',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'thankyou_details_cell_link_hover_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['table_content'] . ' a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		jet_woo_builder_common_controls()->register_table_cell_style_controls( $this, 'thankyou_details_cell', $css_scheme['table_content'] );

		$this->end_controls_section();

	}

	protected function render() {

		$this->__open_wrap();

		include $this->get_template( 'thankyou/order-details.php' );

		$this->__close_wrap();

	}

}
