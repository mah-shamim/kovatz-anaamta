<?php
/**
 * Class: Jet_Woo_Builder_Checkout_Login_Form
 * Name: Checkout Login Form
 * Slug: jet-checkout-login-form
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Checkout_Login_Form extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-checkout-login-form';
	}

	public function get_title() {
		return __( 'Checkout Login Form', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-checkout-login-form';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-checkout-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'checkout' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-checkout-login-form/css-scheme',
			[
				'message' => '.woocommerce-info',
				'form'    => '.login.woocommerce-form-login',
				'button'  => '.login.woocommerce-form-login button.button',
				'label'   => '.login.woocommerce-form-login label',
				'field'   => '.login.woocommerce-form-login .form-row',
				'input'   => '.login.woocommerce-form-login input.input-text',
			]
		);

		$this->start_controls_section(
			'checkout_login_form_toggle_section',
			[
				'label' => __( 'Login Toggle', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->add_control(
			'checkout_login_form_modify_toggle',
			[
				'label' => __( 'Modify', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'checkout_login_form_heading_notice_text',
			[
				'label'       => __( 'Toggle', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Returning customer?', 'jet-woo-builder' ),
				'placeholder' => __( 'Returning customer?', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'checkout_login_form_modify_toggle' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_login_toggle_styles_section',
			[
				'label' => __( 'Toggle', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'checkout_login_message_typography',
				'label'    => esc_html__( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['message'] . ', {{WRAPPER}} ' . $css_scheme['message'] . ' a',
			)
		);

		$this->add_control(
			'checkout_login_message_text_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['message'] => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'checkout_login_message_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['message'] . ':before' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'checkout_login_message_background',
			[
				'label'     => __( 'Background Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['message'] => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'checkout_login_message_link_heading',
			[
				'label' => __( 'Links', 'jet-woo-builder' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs( 'checkout_login_message_link_styles_tabs' );

		$this->start_controls_tab(
			'checkout_login_message_link_styles_normal_tab',
			[
				'label' => __( 'Normal', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'checkout_login_message_link_color',
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
			'checkout_login_message_link_styles_hover_tab',
			[
				'label' => __( 'Hover', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'checkout_login_message_link_hover_color',
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
			array(
				'name'        => 'checkout_login_message_border',
				'label'       => esc_html__( 'Border', 'jet-woo-builder' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['message'],
			)
		);

		$this->add_responsive_control(
			'checkout_login_message_border_radius',
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
			'checkout_login_message_margin',
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
			'checkout_login_message_padding',
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
			'checkout_login_form_styles',
			array(
				'label' => esc_html__( 'Form', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_form_style_controls( $this, 'checkout_login', $css_scheme['form'], true );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_login_form_label_styles',
			array(
				'label' => esc_html__( 'Label', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_label_style_controls( $this, 'checkout_login_form', $css_scheme['label'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_login_form_input_styles',
			[
				'label' => __( 'Inputs', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'checkout_login_form_col_gap',
			[
				'label'      => __( 'Columns Gap', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [ 'px' => 0 ],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['field'] => 'padding-left: calc( {{SIZE}}{{UNIT}}/2 ); padding-right: calc( {{SIZE}}{{UNIT}}/2 ); margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->add_responsive_control(
			'checkout_login_form_row_gap',
			[
				'label'      => __( 'Rows Gap', 'jet-woo-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default'    => [ 'px' => 0 ],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['field'] => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		jet_woo_builder_common_controls()->register_input_style_controls( $this, 'checkout_login_form', $css_scheme['input'], false );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_login_form_button_styles',
			array(
				'label' => esc_html__( 'Button', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'checkout_login_form', $css_scheme['button'] );

		$this->end_controls_section();

	}

	protected function render() {

		// Add filters before displaying our Widget.
		// Change login form toggle message.
		add_filter( 'woocommerce_checkout_login_message', array( $this, 'modify_checkout_login_form_toggle_message' ) );

		$this->__open_wrap();

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			include jet_woo_builder()->get_template( 'editor/widgets/checkout/login-form.php' );
		} else {
			woocommerce_checkout_login_form();
		}

		$this->__close_wrap();

		// Remove filters after displaying our Widget.
		add_filter( 'woocommerce_checkout_login_message', array( $this, 'modify_checkout_login_form_toggle_message' ) );

	}

	/**
	 * Change checkout login form toggle message.
	 *
	 * @param $message
	 *
	 * @return string
	 */
	public function modify_checkout_login_form_toggle_message( $message ) {

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['checkout_login_form_modify_toggle'] ) && 'yes' === $settings['checkout_login_form_modify_toggle'] ) {
			$toggle = isset( $settings['checkout_login_form_heading_notice_text'] ) && ! empty( $settings['checkout_login_form_heading_notice_text'] ) ?
				$settings['checkout_login_form_heading_notice_text'] : 'Returning customer?';

			$message = esc_html__( $toggle, 'jet-woo-builder' );
		}

		return $message;

	}

}
