<?php
/**
 * Class: Jet_Woo_Builder_MyAccount_Registration_Form
 * Name: Account Registration Form
 * Slug: jet-myaccount-registration-form
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_MyAccount_Registration_Form extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-myaccount-registration-form';
	}

	public function get_title() {
		return __( 'Account Registration Form', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-my-account-registration-form';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-my-account-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'myaccount' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-woo-builder-myaccount-registration-form/css-scheme',
			[
				'form'  => 'form.woocommerce-form-register',
				'label' => 'form.woocommerce-form-register .form-row label',
				'input' => 'form.woocommerce-form-register input.input-text',
			]
		);

		$this->start_controls_section(
			'myaccount_registration_heading_styles',
			array(
				'label' => esc_html__( 'Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'myaccount_registration', 'h2' );

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_registration_form_styles',
			array(
				'label' => esc_html__( 'Form', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_form_style_controls( $this, 'myaccount_registration', $css_scheme['form'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_registration_label_styles',
			array(
				'label' => esc_html__( 'Label', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_label_style_controls( $this, 'myaccount_registration', $css_scheme['label'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_registration_input_styles',
			array(
				'label' => esc_html__( 'Input', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_input_style_controls( $this, 'myaccount_registration', $css_scheme['input'], false );

		$this->end_controls_section();

		$this->start_controls_section(
			'myaccount_registration_button_styles',
			array(
				'label' => esc_html__( 'Button', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'myaccount_registration', '.button' );

		$this->end_controls_section();

	}

	protected function render() {

		$this->__open_wrap();

		include $this->get_template( 'myaccount/registration-form.php' );

		$this->__close_wrap();

	}
}
