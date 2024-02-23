<?php
/**
 * Class: Jet_Woo_Builder_Archive_Document_Category
 * Name: Category Template
 * Slug: jet-woo-builder-category
 */

use Elementor\Controls_Manager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Woo_Builder_Archive_Document_Category extends Jet_Woo_Builder_Document_Base {

	public function get_name() {
		return 'jet-woo-builder-category';
	}

	public static function get_title() {
		return esc_html__( 'Jet Woo Category Template', 'jet-woo-builder' );
	}

	protected function register_controls() {

		$columns = jet_woo_builder_tools()->get_select_range( 12 );

		parent::register_controls();

		$this->start_controls_section(
			'section_template_category_settings',
			[
				'label'      => esc_html__( 'Template Settings', 'jet-woo-builder' ),
				'tab'        => Controls_Manager::TAB_SETTINGS,
				'show_label' => false,
			]
		);

		$this->add_control(
			'use_custom_template_category_columns',
			[
				'label' => __( 'Enable Custom Columns Count', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'equal_columns_height',
			[
				'label'       => __( 'Enable Equal Columns Height', 'jet-woo-builder' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Fits only top level sections of grid item.', 'jet-woo-builder' ),
				'condition'   => [
					'use_custom_template_category_columns' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'template_category_columns_count',
			[
				'label'              => __( 'Template Columns', 'jet-woo-builder' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 4,
				'options'            => $columns,
				'frontend_available' => true,
				'selectors'          => [
					'.woocommerce {{WRAPPER}}.products.jet-woo-builder-categories--columns' => '--columns: {{VALUE}}',
				],
				'condition'          => [
					'use_custom_template_category_columns' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'template_category_columns_horizontal_gutter',
			[
				'label'              => esc_html__( 'Template Columns Horizontal Gutter (px)', 'jet-woo-builder' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => [ 'px' ],
				'range'              => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'            => [
					'size' => 10,
					'unit' => 'px',
				],
				'frontend_available' => true,
				'selectors'          => [
					'.woocommerce {{WRAPPER}}.products.jet-woo-builder-categories--columns .product.product-category' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',
					'.woocommerce {{WRAPPER}}.products.jet-woo-builder-categories--columns'                           => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
				],
				'condition'          => [
					'use_custom_template_category_columns' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'template_category_columns_vertical_gutter',
			[
				'label'              => esc_html__( 'Template Columns Vertical Gutter (px)', 'jet-woo-builder' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => [ 'px' ],
				'range'              => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'            => [
					'size' => 10,
					'unit' => 'px',
				],
				'frontend_available' => true,
				'selectors'          => [
					'.woocommerce {{WRAPPER}}.products.jet-woo-builder-categories--columns .product.product-category' => 'margin-bottom: {{SIZE}}{{UNIT}}!important;',
				],
				'condition'          => [
					'use_custom_template_category_columns' => 'yes',
				],
			]
		);

		$this->add_control(
			'archive_link',
			[
				'label' => __( 'Make Archive Item Clickable', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'archive_link_open_in_new_window',
			[
				'label'     => __( 'Open in New Window', 'jet-engine' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'archive_link!' => '',
				],
			]
		);

		$this->end_controls_section();

	}

	public function save( $data = [] ) {
		return $this->save_archive_templates( $data );
	}

	public function get_wp_preview_url() {

		$main_post_id     = $this->get_main_id();
		$product_category = $this->query_first_category();

		return add_query_arg(
			[
				'preview_nonce'    => wp_create_nonce( 'post_preview_' . $main_post_id ),
				'jet_woo_template' => $main_post_id,
			],
			esc_url( get_category_link( $product_category ) )
		);

	}

	public function get_preview_as_query_args() {

		jet_woo_builder()->documents->set_current_type( $this->get_name() );

		$args             = [];
		$product_category = $this->query_first_category();

		if ( ! empty( $product_category ) ) {
			$args = [
				'posts_per_page' => -1,
				'post_type'      => 'product',
				'tax_query'      => [
					[
						'taxonomy' => 'product_cat',
						'field'    => 'id',
						'terms'    => $product_category,
					],
				],
			];
		}

		return $args;

	}

}