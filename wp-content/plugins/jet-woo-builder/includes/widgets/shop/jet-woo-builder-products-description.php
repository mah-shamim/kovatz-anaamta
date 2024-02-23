<?php
/**
 * Class: Jet_Woo_Builder_Products_Description
 * Name: Products Description
 * Slug: jet-woo-builder-products-description
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Products_Description extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-products-description';
	}

	public function get_title() {
		return __( 'Products Description', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-shop-description';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-shop-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'shop' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/products-description/css-scheme',
			[
				'term_description'    => '.elementor-jet-woo-builder-products-description .term-description',
				'archive_description' => '.elementor-jet-woo-builder-products-description .page-description',
			]
		);

		$this->start_controls_section(
			'section_products_description_style',
			[
				'label' => __( 'Products Description', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'products_description_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['term_description'] . ',' . '{{WRAPPER}} ' . $css_scheme['archive_description'],
			]
		);

		$this->add_control(
			'products_description_text_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['term_description']    => 'color: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['archive_description'] => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'products_description_text_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['term_description'] . ',' . '{{WRAPPER}} ' . $css_scheme['archive_description'],
			]
		);

		$this->add_responsive_control(
			'products_description_align',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types( true ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['term_description']    => 'text-align: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['archive_description'] => 'text-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

	}

	protected function render() {

		$this->__open_wrap();

		if ( jet_woo_builder()->elementor_views->in_elementor() ) {
			$description = __( 'The description is not prominent by default; however, some themes may show it.', 'jet-woo-builder' );

			printf( '<div class="page-description"><p>%s</p></div>', $description );
		} else {
			woocommerce_taxonomy_archive_description();
			woocommerce_product_archive_description();
		}

		$this->__close_wrap();

	}

}
