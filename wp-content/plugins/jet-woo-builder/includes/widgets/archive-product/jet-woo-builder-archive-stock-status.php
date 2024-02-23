<?php
/**
 * Class: Jet_Woo_Builder_Archive_Stock_Status
 * Name: Archive Stock Status
 * Slug: jet-woo-builder-archive-stock-status
 */

namespace Elementor;

use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Archive_Stock_Status extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-archive-stock-status';
	}

	public function get_title() {
		return __( 'Archive Stock Status', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-archive-stock-status';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/woocommerce-jetwoobuilder-settings-how-to-create-and-set-a-custom-categories-archive-template/?utm_source=need-help&utm_medium=jet-woo-categories&utm_campaign=jetwoobuilder';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'archive' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-archive-stock-status/css-scheme',
			array(
				'stock'        => '.jet-woo-builder-archive-product-stock-status .stock',
				'in_stock'     => '.jet-woo-builder-archive-product-stock-status .in-stock',
				'out_of_stock' => '.jet-woo-builder-archive-product-stock-status .out-of-stock',
			)
		);

		$this->start_controls_section(
			'section_stock_style',
			[
				'label' => __( 'Stock Status', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'stock_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['stock'],
			]
		);

		$this->start_controls_tabs( 'stock_style_tabs' );

		$this->start_controls_tab(
			'in_stock_styles',
			array(
				'label' => esc_html__( 'In Stock', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'in_stock_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['in_stock'] => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'out_of_stock_styles',
			array(
				'label' => esc_html__( 'Out Of Stock', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'out_of_stock_color',
			[
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['out_of_stock'] => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'stock_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['stock'] => 'text-align: {{VALUE}};',
				),
				'separator' => 'before',
				'classes'   => 'elementor-control-align',
			)
		);

		$this->end_controls_section();

	}

	/**
	 * CSS selector.
	 *
	 * Returns CSS selector for nested element.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @param null $el Selector.
	 *
	 * @return string
	 */
	public function css_selector( $el = null ) {
		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	public static function render_callback() {

		echo '<div class="jet-woo-builder-archive-product-stock-status">';
		echo jet_woo_builder_template_functions()->get_product_stock_status();
		echo '</div>';

	}

	protected function render() {

		if ( jet_woo_builder_tools()->is_builder_content_save() ) {
			echo jet_woo_builder()->parser->get_macros_string( $this->get_name() );
		} else {
			echo self::render_callback();
		}

	}

}
