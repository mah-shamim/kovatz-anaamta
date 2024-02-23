<?php
/**
 * Class: Jet_Woo_Builder_Archive_Category_Count
 * Name: Archive Category Count
 * Slug: jet-woo-builder-archive-category-count
 */

namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Archive_Category_Count extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-archive-category-count';
	}

	public function get_title() {
		return __( 'Archive Category Count', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-category-count';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/woocommerce-jetwoobuilder-settings-how-to-create-and-set-a-custom-categories-archive-template/?utm_source=need-help&utm_medium=jet-woo-categories&utm_campaign=jetwoobuilder';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'category' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			[
				'label' => __( 'Count', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'archive_category_count_before_text',
			[
				'label'   => __( 'Before Count', 'jet-woo-builder' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '(',
			]
		);

		$this->add_control(
			'archive_category_count_after_text',
			[
				'label'   => __( 'After Count', 'jet-woo-builder' ),
				'type'    => Controls_Manager::TEXT,
				'default' => ')',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_archive_category_count_style',
			[
				'label' => __( 'Count', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'archive_category_count_display',
			[
				'label'     => __( 'Display Type', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'block',
				'options'   => jet_woo_builder_tools()->get_available_display_types(),
				'selectors' => [
					$this->css_selector() => 'display: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'archive_category_count_typography',
				'selector' => $this->css_selector(),
			]
		);

		$this->add_control(
			'archive_category_count_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector() => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'archive_category_count_bg',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector() => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'archive_category_count_border',
				'label'    => __( 'Border', 'jet-woo-builder' ),
				'selector' => $this->css_selector(),
			]
		);

		$this->add_control(
			'archive_category_count_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					$this->css_selector() => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'archive_category_count_shadow',
				'selector' => $this->css_selector(),
			]
		);

		$this->add_responsive_control(
			'archive_category_count_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					$this->css_selector() => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'archive_category_count_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					$this->css_selector() => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'archive_category_count_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} '        => 'text-align: {{VALUE}};',
					$this->css_selector() => 'text-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * CSS selector.
	 *
	 * Returns CSS selector for nested element.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @param null $el Selector.
	 *
	 * @return string
	 */
	public function css_selector( $el = null ) {
		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	public static function render_callback( $settings = [], $args = [] ) {

		$category = ! empty( $args ) ? $args['category'] : get_queried_object();
		$before   = $settings['before_count'];
		$after    = $settings['after_count'];

		echo '<div class="jet-woo-builder-archive-category-count">';
		echo sprintf( '<span class="jet-woo-category-count">%2$s%1$s%3$s</span>', $category->count, $before, $after );
		echo '</div>';

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$before_count = isset( $settings['archive_category_count_before_text'] ) ? esc_html__( $settings['archive_category_count_before_text'], 'jet-woo-builder' ) : '';
		$after_count  = isset( $settings['archive_category_count_after_text'] ) ? esc_html__( $settings['archive_category_count_after_text'], 'jet-woo-builder' ) : '';

		$macros_settings = [
			'before_count' => ! is_rtl() ? $before_count : $after_count,
			'after_count'  => ! is_rtl() ? $after_count : $before_count,
		];

		if ( jet_woo_builder_tools()->is_builder_content_save() ) {
			echo jet_woo_builder()->parser->get_macros_string( $this->get_name(), $macros_settings );
		} else {
			echo self::render_callback( $macros_settings, jet_woo_builder()->woocommerce->get_current_args() );
		}

	}

}
