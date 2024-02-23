<?php
/**
 * Class: Jet_Woo_Builder_Archive_Product_Excerpt
 * Name: Archive Excerpt
 * Slug: jet-woo-builder-archive-product-excerpt
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Archive_Product_Excerpt extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-archive-product-excerpt';
	}

	public function get_title() {
		return __( 'Archive Excerpt', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-excerpt';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/woocommerce-jetwoobuilder-settings-how-to-create-and-set-a-custom-categories-archive-template/?utm_source=need-help&utm_medium=jet-woo-categories&utm_campaign=jetwoobuilder';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'archive' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_archive_excerpt_content',
			[
				'label' => __( 'Excerpt', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'archive_excerpt_length',
			[
				'label'       => __( 'Length', 'jet-woo-builder' ),
				'description' => __( 'Set -1 to show full excerpt and 0 to hide it.', 'jet-woo-builder' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => -1,
				'default'     => 10,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_archive_excerpt_style',
			[
				'label' => __( 'Excerpt', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'archive_excerpt_typography',
				'selector' => $this->css_selector(),
			]
		);

		$this->add_control(
			'archive_excerpt_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector() => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'archive_excerpt_text_shadow',
				'selector' => $this->css_selector(),
			]
		);

		$this->add_responsive_control(
			'archive_excerpt_align',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types( true ),
				'selectors' => [
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

		$excerpt = jet_woo_builder_tools()->trim_text(
			jet_woo_builder_template_functions()->get_product_excerpt(),
			$settings['length'],
			'word',
			'...'
		);

		printf( '<div class="jet-woo-builder-archive-product-excerpt">%s</div>', $excerpt );

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$macros_settings = [
			'length' => isset( $settings['archive_excerpt_length'] ) && ! empty( $settings['archive_excerpt_length'] ) ? $settings['archive_excerpt_length'] : 10,
		];

		if ( jet_woo_builder_tools()->is_builder_content_save() ) {
			echo jet_woo_builder()->parser->get_macros_string( $this->get_name(), $macros_settings );
		} else {
			echo self::render_callback( $macros_settings );
		}

	}

}
