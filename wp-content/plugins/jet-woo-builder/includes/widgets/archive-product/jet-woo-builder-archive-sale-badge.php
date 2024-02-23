<?php
/**
 * Class: Jet_Woo_Builder_Archive_Sale_Badge
 * Name: Archive Sale Badge
 * Slug: jet-woo-builder-archive-sale-badge
 */

namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Archive_Sale_Badge extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-archive-sale-badge';
	}

	public function get_title() {
		return __( 'Archive Sale Badge', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-sale-badge';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/woocommerce-jetwoobuilder-settings-how-to-create-and-set-a-custom-categories-archive-template/?utm_source=need-help&utm_medium=jet-woo-categories&utm_campaign=jetwoobuilder';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'archive' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-archive-sale-badge/css-scheme',
			array(
				'wrapper' => '.jet-woo-builder-archive-product-sale-badge',
				'badge'   => '.jet-woo-product-badge',
			)
		);

		$this->start_controls_section(
			'section_badge_content',
			[
				'label' => __( 'Sale Badge', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'archive_badge_text',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Label', 'jet-woo-builder' ),
				'default'     => __( 'Sale!', 'jet-woo-builder' ),
				'description' => __( 'Use %percentage_sale% and %numeric_sale% macros to display a withdrawal of discounts as a percentage or numeric of the initial price.', 'jet-woo-builder' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_archive_badge_style',
			[
				'label' => __( 'Sale Badge', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'archive_badge_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['badge'],
			]
		);

		$this->add_control(
			'archive_badge_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['badge'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'archive_badge_background',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['badge'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'archive_badge_border',
				'label'       => esc_html__( 'Border', 'jet-woo-builder' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['badge'],
			)
		);

		$this->add_responsive_control(
			'archive_badge_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['badge'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'archive_badge_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['badge'],
			)
		);

		$this->add_responsive_control(
			'archive_badge_content_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['badge'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'archive_badge_content_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['badge'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'archive_badge_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['wrapper'] => 'text-align: {{VALUE}};',
				),
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

	public static function render_callback( $settings = [] ) {

		$label = jet_woo_builder()->macros->do_macros( $settings['label'] );
		$badge = jet_woo_builder_template_functions()->get_product_sale_flash( $label, $settings );

		if ( ! $badge ) {
			return;
		}

		printf( '<div class="jet-woo-builder-archive-product-sale-badge">%s</div>', $badge );

	}


	protected function render() {

		$settings = $this->get_settings_for_display();

		$macros_settings = apply_filters( 'jet-woo-builder/jet-woo-builder-archive-sale-badge/macros-settings', [
			'label' => isset( $settings['archive_badge_text'] ) && ! empty( $settings['archive_badge_text'] ) ? esc_html__( $settings['archive_badge_text'], 'jet-woo-builder' ) : __( 'Sale!', 'jet-woo-builder' ),
		], $settings );

		if ( jet_woo_builder_tools()->is_builder_content_save() ) {
			echo jet_woo_builder()->parser->get_macros_string( $this->get_name(), $macros_settings );
		} else {
			echo self::render_callback( $macros_settings );
		}

	}

}
