<?php
/**
 * Class: Jet_Woo_Builder_MyAccount_Login_Form
 * Name: Account Login Form
 * Slug: jet-myaccount-login-form
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_MyAccount_Login_Form extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-myaccount-login-form';
	}

	public function get_title() {
		return __( 'Account Login Form', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-my-account-login-form';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-my-account-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'myaccount' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-woo-builder-myaccount-login-form/css-scheme',
			[
				'form'  => 'form.woocommerce-form-login',
				'label' => 'form.woocommerce-form-login .form-row label',
				'input' => 'form.woocommerce-form-login input.input-text',
			]
		);

		$this->start_controls_section(
			'myaccount_login_form_heading_styles',
			array(
				'label' => esc_html__( 'Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'myaccount_login_form', 'h2' );

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_login_form_styles',
			array(
				'label' => esc_html__( 'Form', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_form_style_controls( $this, 'myaccount_login', $css_scheme['form'], true );

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_login_form_label_styles',
			array(
				'label' => esc_html__( 'Label', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_label_style_controls( $this, 'myaccount_login_form', $css_scheme['label'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_login_form_input_styles',
			[
				'label' => __( 'Inputs', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'myaccount_login_form_row_gap',
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
					'{{WRAPPER}} form.woocommerce-form-login .form-row' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		jet_woo_builder_common_controls()->register_input_style_controls( $this, 'myaccount_login_form', $css_scheme['input'], false );

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_login_form_button_styles',
			array(
				'label' => esc_html__( 'Button', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'myaccount_login_form', '.button' );

		$this->end_controls_section();

	}

	protected function render() {

		$this->__open_wrap();

		include $this->get_template( 'myaccount/login-form.php' );

		$this->__close_wrap();

	}
}
