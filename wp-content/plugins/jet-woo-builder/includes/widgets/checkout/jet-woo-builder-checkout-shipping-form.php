<?php
/**
 * Class: Jet_Woo_Builder_Checkout_Shipping_Form
 * Name: Checkout Shipping Form
 * Slug: jet-checkout-shipping-form
 */

namespace Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Checkout_Shipping_Form extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-checkout-shipping-form';
	}

	public function get_title() {
		return __( 'Checkout Shipping Form', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-checkout-shipping-form';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-a-checkout-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'checkout' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-checkout-shipping-form/css-scheme',
			[
				'heading' => '.woocommerce-shipping-fields #ship-to-different-address',
				'label'   => '.woocommerce-shipping-fields .form-row label',
				'field'   => '.woocommerce-shipping-fields .form-row',
				'input'   => '.woocommerce-shipping-fields .form-row .woocommerce-input-wrapper > *:not(.woocommerce-password-strength):not(.woocommerce-password-hint):not(.show-password-input)',
			]
		);

		$this->start_controls_section(
			'checkout_shipping_form_general_section',
			[
				'label' => __( 'Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->add_control(
			'checkout_shipping_form_enable_custom_title',
			[
				'label' => __( 'Modify', 'jet-woo-builder' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'checkout_shipping_form_title_text',
			[
				'label'       => __( 'Heading', 'jet-woo-builder' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Ship to a different address?', 'jet-woo-builder' ),
				'placeholder' => __( 'Ship to a different address?', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'checkout_shipping_form_enable_custom_title' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_shipping_manage_fields_section',
			[
				'label' => __( 'Fields', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_checkout_forms_manage_fields_controls( $this, 'shipping', $css_scheme );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_shipping_heading_styles',
			[
				'label' => __( 'Heading', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'checkout_shipping_heading_typography',
				'label'    => __( 'Typography', 'jet-woo-builder' ),
				'selector' => '{{WRAPPER}} ' . $css_scheme['heading'] . ',{{WRAPPER}} ' . $css_scheme['heading'] . ' label',
			]
		);

		$this->add_control(
			'checkout_shipping_heading_color',
			[
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['heading'] . ' label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'checkout_shipping_heading_margin',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'jet-woo-builder' ),
				'size_units' => $this->set_custom_size_unit( [ 'px', 'em', '%' ] ),
				'selectors'  => [
					'{{WRAPPER}} ' . $css_scheme['heading'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'checkout_shipping_heading_align',
			[
				'label'     => __( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types(),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['heading'] => 'text-align: {{VALUE}}',
				],
				'classes'   => 'elementor-control-align',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_shipping_label_styles',
			array(
				'label' => esc_html__( 'Label', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		jet_woo_builder_common_controls()->register_label_style_controls( $this, 'checkout_shipping', $css_scheme['label'] );

		$this->end_controls_section();

		$this->start_controls_section(
			'checkout_shipping_input_styles',
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
					'{{WRAPPER}} ' . $css_scheme['field'] => 'padding-left: calc( {{SIZE}}{{UNIT}}/2 ); padding-right: calc( {{SIZE}}{{UNIT}}/2 ); margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
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

		jet_woo_builder_common_controls()->register_input_style_controls( $this, 'checkout_shipping', $css_scheme['input'], false );

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['modify_field'] ) && filter_var( $settings['modify_field'], FILTER_VALIDATE_BOOLEAN ) ) {
			$items = [];

			if ( isset( $settings['field_list'] ) ) {
				$priority = 10;

				foreach ( $settings['field_list'] as $key => $field ) {
					$field_key     = 'shipping_' . $field['field_key'];
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

			if ( ! empty( get_option( 'jet_woo_builder_wc_fields_shipping' ) ) || get_option( 'jet_woo_builder_wc_fields_shipping' ) ) {
				update_option( 'jet_woo_builder_wc_fields_shipping', $items );
			} else {
				add_option( 'jet_woo_builder_wc_fields_shipping', $items );
			}
		} else {
			delete_option( 'jet_woo_builder_wc_fields_shipping' );
		}

		$checkout = wc()->checkout();

		// Add & Remove filters & actions before displaying our Widget.
		// Show shipping form in editor.
		add_filter( 'woocommerce_cart_needs_shipping_address', [ $this, 'maybe_cart_needs_shipping_address' ] );
		remove_action( 'woocommerce_checkout_shipping', [ $checkout, 'checkout_form_shipping' ] );

		$this->__open_wrap();

		if ( sizeof( $checkout->checkout_fields ) > 0 ) {
			do_action( 'woocommerce_checkout_shipping' );

			include $this->get_template( 'checkout/shipping-form.php' );
		}

		$this->__close_wrap();

		// Add & Remove filters & actions after displaying our Widget.
		remove_filter( 'woocommerce_cart_needs_shipping_address', [ $this, 'maybe_cart_needs_shipping_address' ] );
		add_action( 'woocommerce_checkout_shipping', [ $checkout, 'checkout_form_shipping' ] );

	}

	/**
	 * Show shipping form in editor.
	 *
	 * @since 1.12.0
	 *
	 * @param $required
	 *
	 * @return bool|mixed
	 */
	public function maybe_cart_needs_shipping_address( $required ) {

		if ( jet_woo_builder()->elementor_views->in_elementor() ) {
			return true;
		}

		return $required;

	}

}
