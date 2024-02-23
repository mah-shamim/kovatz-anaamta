<?php
/**
 * Class: Jet_Woo_Builder_Cart_Empty_Message
 * Name: Cart Empty Message
 * Slug: jet-cart-empty-message
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Cart_Empty_Message extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-cart-empty-message';
	}

	public function get_title() {
		return __( 'Cart Empty Message', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-cart-empty-message';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-cart-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'cart' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-cart-empty-message/css-scheme',
			[
				'message' => '.cart-empty.woocommerce-info',
			]
		);

		$this->start_controls_section(
			'cart_empty_message_section',
			[
				'label' => __( 'Message', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->add_control(
			'cart_empty_enable_custom_message',
			[
				'label' => __( 'Modify', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'cart_empty_message_text',
			[
				'label'       => __( 'Message', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter your message here.', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'cart_empty_enable_custom_message' => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_empty_message_hide_icon',
			[
				'label'                => __( 'Hide Icon', 'jet-woo-builder' ),
				'type'                 => Controls_Manager::SWITCHER,
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
				'selectors_dictionary' => [
					'yes' => '--cart-empty-message-icon-display: none;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_empty_message_styles_section',
			[
				'label' => __( 'Message', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'empty_message_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['message'],
			)
		);

		$this->add_control(
			'empty_message_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['message'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'empty_message_background',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['message'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'empty_message_border',
				'label'    => __( 'Border', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['message'],
			]
		);

		$this->add_responsive_control(
			'empty_message_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['message'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'empty_message_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['message'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'empty_message_align',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types( true ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['message'] => 'text-align: {{VALUE}}',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_empty_message_icon_styles_section',
			[
				'label'     => __( 'Icon', 'jet-woo-builder' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'cart_empty_message_hide_icon' => '',
				],
			]
		);

		$this->add_control(
			'empty_message_icon_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['message'] . ':before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'empty_message_icon_size',
			[
				'label'      => __( 'Size', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em' ] ),
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
					'em' => [
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['message'] . ':before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'empty_message_icon_indent',
			[
				'label'      => __( 'Indent', 'elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em' ] ),
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'em' => [
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['message'] . ':before' => ! is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		// Add filters before displaying our Widget.
		// Change empty cart message.
		add_filter( 'wc_empty_cart_message', [ $this, 'modify_empty_cart_message' ] );
		add_filter( 'jet-woo-builder/cart/empty-message', '__return_false' );

		$this->__open_wrap();

		do_action( 'woocommerce_cart_is_empty' );

		$this->__close_wrap();

		// Remove filters after displaying our Widget.
		remove_filter( 'wc_empty_cart_message', [ $this, 'modify_empty_cart_message' ] );

	}

	/**
	 * Modify empty cart message.
	 *
	 * Change default empty cart message.
	 *
	 * @access public
	 *
	 * @param $message
	 *
	 * @return mixed
	 */
	public function modify_empty_cart_message( $message ) {

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['cart_empty_enable_custom_message'] ) && filter_var( $settings['cart_empty_enable_custom_message'], FILTER_VALIDATE_BOOLEAN ) ) {
			$empty_message = $settings['cart_empty_message_text'] ?? '';

			if ( ! empty( $empty_message ) ) {
				$message = __( $empty_message, 'jet_woo_builder' );
			}
		}

		return $message;

	}

}
