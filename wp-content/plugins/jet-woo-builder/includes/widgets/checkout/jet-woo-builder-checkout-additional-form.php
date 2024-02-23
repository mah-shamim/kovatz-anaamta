<?php
/**
 * Class: Jet_Woo_Builder_Checkout_Additional_Form
 * Name: Checkout Additional Form
 * Slug: jet-checkout-additional-form
 */

namespace Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Checkout_Additional_Form extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-checkout-additional-form';
	}

	public function get_title() {
		return __( 'Checkout Additional Form', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-checkout-additional-form';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-checkout-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'checkout' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-checkout-additional-form/css-scheme',
			[
				'heading'  => '.woocommerce-additional-fields > h3',
				'label'    => '.woocommerce-additional-fields .form-row label',
				'field'    => '.woocommerce-additional-fields .form-row',
				'textarea' => '.woocommerce-additional-fields textarea',
				'input'    => '.woocommerce-additional-fields .form-row .woocommerce-input-wrapper > *:not(.woocommerce-password-strength):not(.woocommerce-password-hint):not(.show-password-input)',
			]
		);

		$this->start_controls_section(
			'checkout_additional_form_heading_section',
			[
				'label' => __( 'Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->add_control(
			'checkout_additional_form_heading_visibility',
			[
				'label'     => __( 'Heading', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
			]
		);

		$this->add_control(
			'checkout_additional_form_custom_labels',
			[
				'label'     => __( 'Modify', 'jet-woo-builder' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'checkout_additional_form_heading_visibility!' => '',
				],
			]
		);

		$this->add_control(
			'checkout_additional_form_title_text',
			[
				'label'       => __( 'Heading', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Additional information', 'jet-woo-builder' ),
				'placeholder' => __( 'Additional information', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'checkout_additional_form_heading_visibility!' => '',
					'checkout_additional_form_custom_labels!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_additional_heading_styles',
			[
				'label'     => __( 'Heading', 'jet-woo-builder' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'checkout_additional_form_heading_visibility' => 'yes',
				],
			]
		);

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'checkout_additional', $css_scheme['heading'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_additional_label_styles',
			array(
				'label' => esc_html__( 'Label', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_label_style_controls( $this, 'checkout_additional', $css_scheme['label'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_additional_textarea_styles',
			[
				'label' => __( 'Fields', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_responsive_control(
			'checkout_additional_form_col_gap',
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
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['field'] => 'padding-left: calc( {{SIZE}}{{UNIT}}/2 ); padding-right: calc( {{SIZE}}{{UNIT}}/2 ); margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->add_responsive_control(
			'checkout_additional_form_row_gap',
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
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['field'] => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'checkout_additional_input_min_height',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Textarea Height', 'jet-woo-builder' ),
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 300,
					],
				],
				'default'    => [
					'size' => 150,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['textarea'] => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		jet_woo_builder_common_controls()->register_input_style_controls( $this, 'checkout_additional', $css_scheme['input'], false );

		$this->end_controls_section();

	}

	protected function render() {

		$this->__open_wrap();

		include $this->get_template( 'checkout/additional-form.php' );

		$this->__close_wrap();

	}

}
