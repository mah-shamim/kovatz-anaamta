<?php
/**
 * Class: Jet_Woo_Builder_Archive_Product_Thumbnail
 * Name: Archive Thumbnail
 * Slug: jet-woo-builder-archive-product-thumbnail
 */

namespace Elementor;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class Jet_Woo_Builder_Archive_Product_Thumbnail extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-archive-product-thumbnail';
	}

	public function get_title() {
		return __( 'Archive Thumbnail', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-images';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/woocommerce-jetwoobuilder-settings-how-to-create-and-set-a-custom-categories-archive-template/?utm_source=need-help&utm_medium=jet-woo-categories&utm_campaign=jetwoobuilder';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'archive' );
	}

	protected function register_controls() {

		$enable_thumb_effect = filter_var( jet_woo_builder_settings()->get( 'enable_product_thumb_effect' ), FILTER_VALIDATE_BOOLEAN );

		$this->start_controls_section(
			'section_general',
			[
				'label' => __( 'Thumbnail', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'is_linked',
			[
				'label' => __( 'Enable Permalink', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'open_new_tab',
			[
				'label'     => __( 'Open in New Window', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'is_linked' => 'yes',
				],
			]
		);

		if ( $enable_thumb_effect ) {
			$this->add_control(
				'enable_thumbnail_effect',
				[
					'label'   => __( 'Enable Thumbnail Effect', 'jet-woo-builder' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);
		}

		$this->add_control(
			'archive_thumbnail_size',
			array(
				'type'    => 'select',
				'label'   => esc_html__( 'Thumbnail Size', 'jet-woo-builder' ),
				'default' => 'woocommerce_thumbnail',
				'options' => jet_woo_builder_tools()->get_image_sizes(),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_archive_thumbnail_style',
			[
				'label' => __( 'Thumbnail', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'archive_thumbnail_background_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector() => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'archive_thumbnail_border',
				'selector' => $this->css_selector(),
			]
		);

		$this->add_control(
			'archive_thumbnail_border_radius',
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
				'name'     => 'archive_thumbnail_box_shadow',
				'selector' => $this->css_selector(),
			]
		);

		$this->add_responsive_control(
			'archive_thumbnail_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					$this->css_selector( '__wrapper' ) => 'text-align: {{VALUE}};',
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

		$target_attr = $settings['open_new_tab'] ? 'target="_blank"' : '';
		$open_link   = '';
		$close_link  = '';

		if ( $settings['enable_permalink'] ) {
			$link       = jet_woo_builder_template_functions()->get_product_permalink();
			$open_link  = '<a href="' . $link . '" ' . $target_attr . '>';
			$close_link = '</a>';
		}

		echo '<div class="jet-woo-builder-archive-product-thumbnail__wrapper">';
		echo '<div class="jet-woo-builder-archive-product-thumbnail">';
		echo $open_link;
		echo jet_woo_builder_template_functions()->get_product_thumbnail(
			$settings['thumbnail_size'],
			$settings['thumbnail_effect'],
			[
				'class'        => 'jet-woo-builder-archive-product-image',
				'data-no-lazy' => '1',
			]
		);
		echo $close_link;
		echo '</div>';
		echo '</div>';

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$macros_settings = [
			'enable_permalink' => isset( $settings['is_linked'] ) ? filter_var( $settings['is_linked'], FILTER_VALIDATE_BOOLEAN ) : false,
			'open_new_tab'     => isset( $settings['open_new_tab'] ) ? filter_var( $settings['open_new_tab'], FILTER_VALIDATE_BOOLEAN ) : false,
			'thumbnail_size'   => $settings['archive_thumbnail_size'] ?? 'woocommerce_thumbnail',
			'thumbnail_effect' => isset( $settings['enable_thumbnail_effect'] ) ? filter_var( $settings['enable_thumbnail_effect'], FILTER_VALIDATE_BOOLEAN ) : false,
		];

		if ( jet_woo_builder_tools()->is_builder_content_save() ) {
			echo jet_woo_builder()->parser->get_macros_string( $this->get_name(), $macros_settings );
		} else {
			echo self::render_callback( $macros_settings );
		}

	}

}
