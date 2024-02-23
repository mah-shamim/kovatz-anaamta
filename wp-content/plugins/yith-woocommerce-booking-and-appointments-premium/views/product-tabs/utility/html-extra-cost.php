<?php
/**
 * Template options in WC Product Panel
 *
 * @var YITH_WCBK_Product_Extra_Cost $extra_cost       one product extra cost
 * @var int                          $extra_cost_id    the id of the extra cost
 * @var string                       $extra_cost_title the title of the extra cost
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$extra_cost_id     = $extra_cost_id ?? $extra_cost->get_id();
$extra_cost_title  = $extra_cost_title ?? $extra_cost->get_name();
$field_name_prefix = "_yith_booking_extra_costs[{$extra_cost_id}]";

yith_wcbk_product_metabox_form_field(
	array(
		'title'  => $extra_cost_title . ' (' . get_woocommerce_currency_symbol() . ')',
		'class'  => 'yith-wcbk-extra-cost',
		'fields' => array(
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
				'type'  => 'hidden',
				'value' => $extra_cost_id,
				'name'  => $field_name_prefix . '[id]',
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
		),
	)
);
