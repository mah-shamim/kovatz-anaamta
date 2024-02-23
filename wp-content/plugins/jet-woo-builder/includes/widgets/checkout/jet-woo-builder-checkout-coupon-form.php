<?php
/**
 * Class: Jet_Woo_Builder_Checkout_Coupon_Form
 * Name: Checkout Coupon Form
 * Slug: jet-checkout-coupon-form
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Checkout_Coupon_Form extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-checkout-coupon-form';
	}

	public function get_title() {
		return __( 'Checkout Coupon Form', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-checkout-coupon-form';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-checkout-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'checkout' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-checkout-coupon-form/css-scheme',
			array(
				'message' => '.woocommerce-form-coupon-toggle .woocommerce-info',
				'form'    => '.checkout_coupon.woocommerce-form-coupon',
				'input'   => '.checkout_coupon.woocommerce-form-coupon input.input-text',
				'button'  => '.checkout_coupon.woocommerce-form-coupon button.button',
			)
		);

		$this->start_controls_section(
			'checkout_coupon_form_toggle',
			[
				'label' => __( 'Coupon Toggle', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->add_control(
			'checkout_coupon_form_modify_toggle',
			[
				'label' => __( 'Modify', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'checkout_coupon_form_heading_notice_text',
			[
				'label'       => __( 'Toggle', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Have a coupon?', 'jet-woo-builder' ),
				'placeholder' => __( 'Have a coupon?', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'checkout_coupon_form_modify_toggle' => 'yes',
				],
			]
		);


		$this->add_control(
			'checkout_coupon_form_heading_link_text',
			[
				'label'       => __( 'Toggle Link', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Click here to enter your code', 'jet-woo-builder' ),
				'placeholder' => __( 'Click here to enter your code', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'checkout_coupon_form_modify_toggle' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_coupon_message_styles',
			[
				'label' => __( 'Toggle', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'checkout_coupon_message_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['message'] . ', {{WRAPPER}} ' . $css_scheme['message'] . ' a',
			)
		);

		$this->add_control(
			'checkout_coupon_message_text_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['message'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'checkout_coupon_message_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['message'] . ':before' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'checkout_coupon_message_background',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['message'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'checkout_coupon_message_link_heading',
			[
				'label' => __( 'Links', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs( 'checkout_coupon_message_link_styles_tabs' );

		$this->start_controls_tab(
			'checkout_coupon_message_link_styles_normal_tab',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'checkout_coupon_message_link_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['message'] . ' a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'checkout_coupon_message_link_styles_hover_tab',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'checkout_coupon_message_link_hover_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['message'] . ' a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'checkout_coupon_message_border',
				'label'    => __( 'Border', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['message'],
			]
		);

		$this->add_responsive_control(
			'checkout_coupon_message_border_radius',
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
			'checkout_coupon_message_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['message'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'checkout_coupon_message_padding',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['message'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_coupon_form_styles',
			array(
				'label' => esc_html__( 'Form', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_form_style_controls( $this, 'checkout_coupon', $css_scheme['form'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_coupon_form_input_styles',
			array(
				'label' => esc_html__( 'Input', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'checkout_coupon_form_input_width',
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
						'max' => 500,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => '50',
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .form-row-first' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		jet_woo_builder_common_controls()->register_input_style_controls( $this, 'checkout_coupon_form', $css_scheme['input'], false );

		$this->add_responsive_control(
			'checkout_coupon_form_input_margin',
			[
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .form-row.form-row-first' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_coupon_form_button_styles',
			array(
				'label' => esc_html__( 'Button', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'checkout_coupon_form_button_width',
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
						'max' => 500,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => '50',
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .form-row-last' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'checkout_coupon_form', $css_scheme['button'] );

		$this->add_responsive_control(
			'checkout_coupon_form_button_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .form-row.form-row-last' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		// Add filters before displaying our Widget.
		// Change coupon form toggle message.
		add_filter( 'woocommerce_checkout_coupon_message', [ $this, 'modify_checkout_coupon_form_toggle_message' ] );

		$this->__open_wrap();

		woocommerce_checkout_coupon_form();

		$this->__close_wrap();

		// Remove filters after displaying our Widget.
		remove_filter( 'woocommerce_checkout_coupon_message', [ $this, 'modify_checkout_coupon_form_toggle_message' ] );

	}

	/**
	 * Change checkout coupon form toggle message.
	 *
	 * @param $message
	 *
	 * @return string
	 */
	public function modify_checkout_coupon_form_toggle_message( $message ) {

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['checkout_coupon_form_modify_toggle'] ) && 'yes' === $settings['checkout_coupon_form_modify_toggle'] ) {
			$toggle      = ! empty( $settings['checkout_coupon_form_heading_notice_text'] ) ? $settings['checkout_coupon_form_heading_notice_text'] : 'Have a coupon?';
			$toggle_link = ! empty( $settings['checkout_coupon_form_heading_link_text'] ) ? $settings['checkout_coupon_form_heading_link_text'] : 'Click here to enter your code';

			$message = sprintf(
				'%s <a href="#" class="showcoupon"> %s </a>',
				esc_html__( $toggle, 'jet-woo-builder' ),
				esc_html__( $toggle_link, 'jet-woo-builder' )
			);
		}


		return $message;

	}
}
