<?php
/**
 * Class: Jet_Woo_Builder_Checkout_Billing
 * Name: Checkout Billing Form
 * Slug: jet-checkout-billing
 */

namespace Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Checkout_Billing extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-checkout-billing';
	}

	public function get_title() {
		return __( 'Checkout Billing Form', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-checkout-billing-form';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-checkout-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'checkout' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-checkout-billing/css-scheme',
			[
				'heading' => '.woocommerce-billing-fields > h3',
				'label'   => '.elementor-jet-checkout-billing .form-row label',
				'field'   => '.elementor-jet-checkout-billing .form-row',
				'input'   => '.elementor-jet-checkout-billing .form-row .woocommerce-input-wrapper > *:not(.woocommerce-password-strength):not(.woocommerce-password-hint):not(.show-password-input)',
			]
		);

		$this->start_controls_section(
			'checkout_billing_manage_fields_section',
			[
				'label' => __( 'Fields', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		jet_woo_builder_common_controls()->register_checkout_forms_manage_fields_controls( $this, 'billing', $css_scheme );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_billing_heading_styles',
			array(
				'label' => esc_html__( 'Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_heading_style_controls( $this, 'checkout_billing', $css_scheme['heading'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_billing_label_styles',
			[
				'label' => __( 'Labels', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		jet_woo_builder_common_controls()->register_label_style_controls( $this, 'checkout_billing', $css_scheme['label'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_billing_input_styles',
			[
				'label' => __( 'Fields', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'checkout_billing_form_col_gap',
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
					'{{WRAPPER}} ' . $css_scheme['field']                    => 'padding-left: calc( {{SIZE}}{{UNIT}}/2 ); padding-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .woocommerce-billing-fields__field-wrapper' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->add_responsive_control(
			'checkout_billing_form_row_gap',
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

		jet_woo_builder_common_controls()->register_input_style_controls( $this, 'checkout_billing', $css_scheme['input'], false );

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['modify_field'] ) && filter_var( $settings['modify_field'], FILTER_VALIDATE_BOOLEAN ) ) {
			$items = [];

			if ( isset( $settings['field_list'] ) ) {
				$priority = 10;

				foreach ( $settings['field_list'] as $key => $field ) {
					$field_key     = 'billing_' . $field['field_key'];
					$field_classes = is_array( $field['field_class'] ) ? $field['field_class'] : explode( ' ', $field['field_class'] );

					$field_classes[] = 'elementor-repeater-item-' . $field['_id'];

					$items[ $field_key ] = [
						'label'       => $field['field_label'],
						'required'    => filter_var( $field['field_required'], FILTER_VALIDATE_BOOLEAN ),
						'class'       => $field_classes,
						'default'     => $field['field_default_value'],
						'placeholder' => $field['field_placeholder'],
						'validate'    => $field['field_validation'],
						'priority'    => $priority + 10,
					];

					$priority += 10;
				}
			}

			if ( ! empty( get_option( 'jet_woo_builder_wc_fields_billing' ) ) || get_option( 'jet_woo_builder_wc_fields_billing' ) ) {
				update_option( 'jet_woo_builder_wc_fields_billing', $items );
			} else {
				add_option( 'jet_woo_builder_wc_fields_billing', $items );
			}
		} else {
			delete_option( 'jet_woo_builder_wc_fields_billing' );
		}

		$checkout = wc()->checkout();

		$this->__open_wrap();

		if ( sizeof( $checkout->checkout_fields ) > 0 ) {
			do_action( 'woocommerce_checkout_before_customer_details' );

			do_action( 'woocommerce_checkout_billing' );

			do_action( 'woocommerce_checkout_after_customer_details' );
		}

		$this->__close_wrap();

	}

}
