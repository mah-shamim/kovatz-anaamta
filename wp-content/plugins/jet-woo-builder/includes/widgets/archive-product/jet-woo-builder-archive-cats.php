<?php
/**
 * Class: Jet_Woo_Builder_Archive_Cats
 * Name: Archive Categories
 * Slug: jet-woo-builder-archive-cats
 */

namespace Elementor;

use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Archive_Cats extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-archive-cats';
	}

	public function get_title() {
		return __( 'Archive Categories', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-archive-categories';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/woocommerce-jetwoobuilder-settings-how-to-create-and-set-a-custom-categories-archive-template/?utm_source=need-help&utm_medium=jet-woo-categories&utm_campaign=jetwoobuilder';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'archive' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_archive_cats_content',
			[
				'label' => __( 'Categories', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'categories_count',
			[
				'label'       => __( 'Count', 'jet-woo-builder' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set 0 to show full list of categories.', 'jet-woo-builder' ),
				'min'         => 0,
				'default'     => 0,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_archive_cats_style',
			[
				'label' => __( 'Categories', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'archive_typography',
				'selector' => $this->css_selector( ' a' ),
			]
		);

		$this->start_controls_tabs( 'tabs_archive_cats_color' );

		$this->start_controls_tab(
			'tab_archive_cats_color_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'archive_cats_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector()       => 'color: {{VALUE}}',
					$this->css_selector( ' a' ) => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_archive_cats_color_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'archive_cats_color_hover',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' a:hover' ) => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'archive_cats_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					$this->css_selector() => 'text-align: {{VALUE}};',
				],
				'separator' => 'before',
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

		echo '<div class="jet-woo-builder-archive-cats"><ul>';
		echo jet_woo_builder_template_functions()->get_product_terms_list( 'product_cat', $settings['count'] );
		echo '</ul></div>';

	}

	protected function render() {

		$this->__open_wrap();

		$settings = $this->get_settings_for_display();

		$macros_settings = [
			'count' => isset( $settings['categories_count'] ) ? $settings['categories_count'] : 0,
		];

		if ( jet_woo_builder_tools()->is_builder_content_save() ) {
			echo jet_woo_builder()->parser->get_macros_string( $this->get_name(), $macros_settings );
		} else {
			echo self::render_callback( $macros_settings );
		}

		$this->__close_wrap();

	}

}
