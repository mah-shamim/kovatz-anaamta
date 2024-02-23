<?php
/**
 * Template options in WC Product Panel
 *
 * @var YITH_WCBK_Product_Extra_Cost $extra_cost one product extra cost
 * @var string                       $index
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$extra_cost_id     = $extra_cost->get_identifier();
$extra_cost_id     = '_' !== $extra_cost_id ? $extra_cost_id : "_{$index}";
$field_name_prefix = "_yith_booking_extra_costs[{$index}]";

yith_wcbk_product_metabox_form_field(
	array(
		'class'  => 'yith-wcbk-extra-cost yith-wcbk-extra-cost--custom',
		'fields' => array(
			array(
				'yith-field'        => true,
				'type'              => 'text',
				'value'             => $extra_cost->get_name( 'edit' ),
				'name'              => $field_name_prefix . '[name]',
				'id'                => "_yith_booking_extra_cost_{$extra_cost_id}_name",
				'class'             => 'yith-wcbk-extra-cost__name',
				'custom_attributes' => array(
					'placeholder'  => esc_html__( 'Set the name...', 'yith-booking-for-woocommerce' ),
					'autocomplete' => 'off',
				),
			),
			array(
				'yith-field'        => true,
				'type'              => 'text',
				'value'             => $extra_cost->get_cost(),
				'name'              => $field_name_prefix . '[cost]',
				'id'                => "_yith_booking_extra_cost_{$extra_cost_id}_cost",
				'class'             => 'wc_input_price yith-wcbk-mini-field yith-wcbk-extra-cost__cost',
				'custom_attributes' => array(
					'placeholder'  => esc_html__( 'Set the cost...', 'yith-booking-for-woocommerce' ),
					'autocomplete' => 'off',
				),
			),
			array(
				'yith-field' => true,
				'type'       => 'hidden',
				'value'      => 0,
				'name'       => $field_name_prefix . '[id]',
			),
			array(
				'type'   => 'section',
				'class'  => 'yith-wcbk-settings-checkbox-container',
				'fields' => array(
					array(
						'type'  => 'checkbox',
						'value' => wc_bool_to_string( $extra_cost->get_multiply_by_number_of_people() ),
						'name'  => $field_name_prefix . '[multiply_by_number_of_people]',
						'id'    => "_yith_booking_extra_cost_{$extra_cost_id}_multiply_fixed_base_fee_by_number_of_people",
					),
					array(
						'type'  => 'label',
						'value' => __( 'Multiply by the number of people', 'yith-booking-for-woocommerce' ),
						'for'   => "_yith_booking_extra_cost_{$extra_cost_id}_multiply_fixed_base_fee_by_number_of_people",
					),
				),
			),
			array(
				'type'   => 'section',
				'class'  => 'yith-wcbk-settings-checkbox-container',
				'fields' => array(
					array(
						'type'  => 'checkbox',
						'value' => wc_bool_to_string( $extra_cost->get_multiply_by_duration() ),
						'name'  => $field_name_prefix . '[multiply_by_duration]',
						'id'    => "_yith_booking_extra_cost_{$extra_cost_id}_multiply_by_duration",
					),
					array(
						'type'  => 'label',
						'value' => __( 'Multiply by duration', 'yith-booking-for-woocommerce' ),
						'for'   => "_yith_booking_extra_cost_{$extra_cost_id}_multiply_by_duration",
					),
				),
			),
			array(
				'type'  => 'yith-icon',
				'icon'  => 'trash',
				'class' => 'yith-wcbk-extra-cost__delete',
			),
		),
	)
);
