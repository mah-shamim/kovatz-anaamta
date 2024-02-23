<?php
/**
 * Class: Jet_Woo_Builder_Products_Result_Count
 * Name: Products Result Count
 * Slug: jet-woo-builder-products-result-count
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Products_Result_Count extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-products-result-count';
	}

	public function get_title() {
		return esc_html__( 'Products Result Count', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-shop-result-count';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-shop-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'shop' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/products-result-count/css-scheme',
			array(
				'result_count' => '.elementor-jet-woo-builder-products-result-count .woocommerce-result-count',
			)
		);

		$this->start_controls_section(
			'section_result_count_style',
			array(
				'label' => __( 'Result Count', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'result_count_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['result_count'],
			]
		);

		$this->add_control(
			'result_count_text_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['result_count'] => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'result_count_align',
			array(
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['result_count'] => 'text-align: {{VALUE}};',
				),
				'classes'   => 'elementor-control-align',
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		$this->__open_wrap();

		woocommerce_result_count();

		$this->__close_wrap();

	}

}
