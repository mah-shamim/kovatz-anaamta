<?php
/**
 * Class: Jet_Woo_Builder_Archive_Add_To_Cart
 * Name: Archive Add To Cart
 * Slug: jet-woo-builder-archive-add-to-cart
 */

namespace Elementor;

use Elementor\Group_Control_Border;
use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Archive_Add_To_Cart extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-archive-add-to-cart';
	}

	public function get_title() {
		return __( 'Archive Add to Cart', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-add-to-cart';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/woocommerce-jetwoobuilder-settings-how-to-create-and-set-a-custom-categories-archive-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'archive' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_archive_add_to_cart_content',
			[
				'label' => __( 'Add to Cart', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_quantity',
			[
				'label'              => __( 'Enable Quantity Input', 'jet-woo-builder' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_archive_add_to_cart_style',
			[
				'label' => __( 'Button', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_display',
			[
				'label'     => __( 'Display Type', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => jet_woo_builder_tools()->get_available_display_types(),
				'default'   => 'inline-block',
				'selectors' => [
					$this->css_selector( ' .button' ) => 'display: {{VALUE}}; --display-type: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 50,
						'max' => 1000,
					],
				],
				'selectors'  => [
					$this->css_selector( ' .button' ) => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'archive_add_to_cart_typography',
				'selector' => $this->css_selector( ' .button' ),
			]
		);

		$this->start_controls_tabs( 'tabs_archive_add_to_cart_style' );

		$this->start_controls_tab(
			'tab_archive_add_to_cart_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'archive_add_to_cart_text_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .button' ) => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'archive_add_to_cart_background_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .button' ) => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'archive_add_to_cart_box_shadow',
				'selector' => $this->css_selector( ' .button' ),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_archive_add_to_cart_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'archive_add_to_cart_hover_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .button:hover' ) => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'archive_add_to_cart_background_hover_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .button:hover' ) => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'archive_add_to_cart_hover_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .button:hover' ) => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'archive_add_to_cart_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'archive_add_to_cart_hover_box_shadow',
				'selector' => $this->css_selector( ' .button:hover' ),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_archive_add_to_cart_added',
			array(
				'label' => esc_html__( 'Added', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'archive_add_to_cart_disabled_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .button.added' ) => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'archive_add_to_cart_background_disabled_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .button.added' ) => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'archive_add_to_cart_added_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .button.added' ) => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'archive_add_to_cart_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'archive_add_to_cart_added_box_shadow',
				'selector' => $this->css_selector( ' .button.added' ),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_archive_add_to_cart_loading',
			array(
				'label' => esc_html__( 'Loading', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'archive_add_to_cart_loading_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .button.loading' ) => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'archive_add_to_cart_background_loading_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .button.loading' ) => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'archive_add_to_cart_loading_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .button.loading' ) => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'archive_add_to_cart_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'archive_add_to_cart_loading_box_shadow',
				'selector' => $this->css_selector( ' .button.loading' ),
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'archive_add_to_cart_border',
				'selector'  => $this->css_selector( ' .button' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'archive_add_to_cart_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					$this->css_selector( ' .button' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'archive_add_to_cart_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					$this->css_selector( ' .button' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'archive_add_to_cart_alignment',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					$this->css_selector() => 'text-align: {{VALUE}};',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'quantity_input_style_section',
			[
				'label'     => __( 'Quantity Input', 'jet-woo-builder' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_quantity' => 'yes',
				],
			]
		);

		$this->add_control(
			'qty_display',
			[
				'label'     => __( 'Display Type', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => jet_woo_builder_tools()->get_available_display_types(),
				'default'   => 'inline-block',
				'selectors' => [
					$this->css_selector( ' .quantity' ) => 'display: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'qty_input_width',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Width', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', '%' ] ),
				'range'      => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 50,
						'max' => 1000,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 70,
				],
				'selectors'  => [
					$this->css_selector( ' .quantity' ) => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'qty_typography',
				'selector' => $this->css_selector( ' .qty' ),
			]
		);

		$this->start_controls_tabs( 'tabs_qty_style' );

		$this->start_controls_tab(
			'tab_qty_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'qty_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .qty' ) => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'qty_background_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .qty' ) => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_qty_focus',
			array(
				'label' => esc_html__( 'Focus', 'jet-woo-builder' ),
			)
		);

		$this->add_control(
			'qty_focus_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .qty:focus' ) => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'qty_background_focus_color',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .qty:focus' ) => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'qty_focus_border_color',
			[
				'label'     => __( 'Border Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$this->css_selector( ' .qty:focus' ) => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'qty_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'qty_border',
				'selector'  => $this->css_selector( ' .qty' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'qty_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					$this->css_selector( ' .qty' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'qty_box_shadow',
				'selector' => $this->css_selector( ' .qty' ),
			]
		);

		$this->add_responsive_control(
			'qty_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					$this->css_selector( ' .quantity' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'qty_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					$this->css_selector( ' .qty' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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

		$attributes = apply_filters( 'jet-woo-builder/jet-woo-archive-add-to-cart/widget-attributes', '', $settings );

		echo '<div class="jet-woo-builder-archive-add-to-cart"' . $attributes . '>';

		jet_woo_builder_template_functions()->get_product_add_to_cart_button( [], $settings['quantity'] );

		echo '</div>';

	}

	protected function render() {

		$this->__open_wrap();

		$settings = apply_filters( 'jet-woo-builder/jet-woo-archive-add-to-cart/settings', $this->get_settings_for_display(), $this );

		$macros_settings = [
			'quantity' => isset( $settings['show_quantity'] ) ? filter_var( $settings['show_quantity'], FILTER_VALIDATE_BOOLEAN ) : false,
		];

		if ( class_exists( 'Jet_Popup' ) ) {
			$macros_settings['jet_woo_builder_cart_popup']          = isset( $settings['jet_woo_builder_cart_popup'] ) ? filter_var( $settings['jet_woo_builder_cart_popup'], FILTER_VALIDATE_BOOLEAN ) : false;
			$macros_settings['jet_woo_builder_cart_popup_template'] = isset( $settings['jet_woo_builder_cart_popup_template'] ) && ! empty( $settings['jet_woo_builder_cart_popup_template'] ) ? $settings['jet_woo_builder_cart_popup_template'] : '';
		}

		if ( jet_woo_builder_tools()->is_builder_content_save() ) {
			echo jet_woo_builder()->parser->get_macros_string( $this->get_name(), $macros_settings );
		} else {
			echo self::render_callback( $macros_settings );
		}

		$this->__close_wrap();

	}

}
