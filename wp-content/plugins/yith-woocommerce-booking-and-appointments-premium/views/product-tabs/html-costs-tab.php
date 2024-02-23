<?php
/**
 * Costs Tab in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.
?>
<div class="yith-wcbk-product-metabox-options-panel yith-plugin-ui options_group show_if_<?php echo esc_attr( $prod_type ); ?>">
	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Standard Prices', 'yith-booking-for-woocommerce' ); ?></h3>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php
			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_costs_block_cost_field',
					// translators: %s is the duration; ex: Base price for 1 day.
					'title'  => sprintf( __( 'Base price for %s', 'yith-booking-for-woocommerce' ), yith_wcbk_product_metabox_dynamic_duration() ),
					'desc'   => __( 'Set your booking price here.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'yith-field'        => true,
							'type'              => 'text',
							'value'             => wc_format_localized_price( $booking_product ? $booking_product->get_base_price( 'edit' ) : '' ),
							'id'                => '_yith_booking_block_cost',
							'name'              => '_yith_booking_block_cost',
							'class'             => 'wc_input_price yith-wcbk-mini-field',
							'custom_attributes' => array(
								'autocomplete' => 'off',
							),
						),
						array(
							'type'   => 'section',
							'class'  => '_yith_booking_multiply_base_price_by_number_of_people_field yith-wcbk-settings-checkbox-container',
							'fields' => array(
								array(
									'type'  => 'checkbox',
									'value' => wc_bool_to_string( $booking_product ? $booking_product->get_multiply_base_price_by_number_of_people( 'edit' ) : false ),
									'id'    => '_yith_booking_multiply_base_price_by_number_of_people',
									'name'  => '_yith_booking_multiply_base_price_by_number_of_people',
								),
								array(
									'type'  => 'label',
									'value' => __( 'Multiply by the number of people', 'yith-booking-for-woocommerce' ),
									'for'   => '_yith_booking_multiply_base_price_by_number_of_people',
								),
							),
						),
					),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_extra_price_per_person_field yith-wcbk-show-conditional',
					'data'   => array(
						'field-id' => '_yith_booking_multiply_base_price_by_number_of_people',
						'value'    => 'no',
					),
					'title'  => __( 'Extra price of', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Here you can set an extra cost for each person added to the specified number.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'yith-field'        => true,
							'type'              => 'text',
							'value'             => wc_format_localized_price( $booking_product ? $booking_product->get_extra_price_per_person( 'edit' ) : '' ),
							'id'                => '_yith_booking_extra_price_per_person',
							'name'              => '_yith_booking_extra_price_per_person',
							'class'             => 'wc_input_price yith-wcbk-mini-field',
							'custom_attributes' => array(
								'autocomplete' => 'off',
							),
						),
						array(
							'yith-field'        => true,
							'type'              => 'number',
							'title'             => __( 'for every person added to', 'yith-booking-for-woocommerce' ),
							'value'             => $booking_product ? $booking_product->get_extra_price_per_person_greater_than( 'edit' ) : 0,
							'id'                => '_yith_booking_extra_price_per_person_greater_than',
							'name'              => '_yith_booking_extra_price_per_person_greater_than',
							'class'             => 'mini',
							'custom_attributes' => array(
								'step' => 1,
								'min'  => 0,
							),
						),
					),
				)
			);
			?>
		</div>
	</div>

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Discounts', 'yith-booking-for-woocommerce' ); ?></h3>
			<span class="yith-wcbk-settings-section__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php
			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_costs_weekly_discount bk_show_if_customer_one_day',
					'title'  => __( 'Weekly discount', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Encourage users to book longer stays by offering weekly discounts.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'type'              => 'percentage',
							'value'             => $booking_product && $booking_product->get_weekly_discount( 'edit' ) ? $booking_product->get_weekly_discount( 'edit' ) : '',
							'id'                => '_yith_booking_weekly_discount',
							'class'             => 'yith-wcbk-mini-field',
							'custom_attributes' => array(
								'step' => 'any',
								'min'  => 0,
								'max'  => 100,
							),
						),
					),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_costs_monthly_discount bk_show_if_customer_one_day',
					'title'  => __( 'Monthly discount', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Encourage users to book longer stays by offering monthly discounts.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'type'              => 'percentage',
							'value'             => $booking_product && $booking_product->get_monthly_discount( 'edit' ) ? $booking_product->get_monthly_discount( 'edit' ) : '',
							'id'                => '_yith_booking_monthly_discount',
							'class'             => 'yith-wcbk-mini-field',
							'custom_attributes' => array(
								'step' => 'any',
								'min'  => 0,
								'max'  => 100,
							),
						),
					),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_costs_last_minute_discounts',
					'title'  => __( 'Last minute discount', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Encourage users to book by offering last minute discounts.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'type'              => 'percentage',
							'value'             => $booking_product && $booking_product->get_last_minute_discount( 'edit' ) ? $booking_product->get_last_minute_discount( 'edit' ) : '',
							'id'                => '_yith_booking_last_minute_discount',
							'class'             => 'yith-wcbk-mini-field',
							'custom_attributes' => array(
								'step' => 'any',
								'min'  => 0,
								'max'  => 100,
							),
						),
						array(
							'type'              => 'number',
							'title'             => __( 'Days before arrival', 'yith-booking-for-woocommerce' ),
							'value'             => $booking_product ? $booking_product->get_last_minute_discount_days_before_arrival( 'edit' ) : 0,
							'id'                => '_yith_booking_last_minute_discount_days_before_arrival',
							'class'             => 'mini',
							'custom_attributes' => array(
								'step' => 1,
								'min'  => 0,
							),
						),
					),
				)
			);
			?>
		</div>
	</div>


	<div class="yith-wcbk-extra-costs yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Extra Costs', 'yith-booking-for-woocommerce' ); ?></h3>
			<span class="yith-wcbk-settings-section__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<div class="yith-wcbk-settings-section__description"><?php esc_html_e( "Leave empty if you don't need to charge extra costs", 'yith-booking-for-woocommerce' ); ?></div>
			<?php
			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_costs_base_cost_field',
					'title'  => __( 'Fixed base fee', 'yith-booking-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
					'fields' => array(
						array(
							'yith-field'        => true,
							'type'              => 'text',
							'value'             => wc_format_localized_price( $booking_product ? $booking_product->get_fixed_base_fee( 'edit' ) : '' ),
							'id'                => '_yith_booking_base_cost',
							'name'              => '_yith_booking_base_cost',
							'class'             => 'wc_input_price yith-wcbk-mini-field yith-wcbk-extra-cost__cost',
							'custom_attributes' => array(
								'placeholder'  => esc_html__( 'Set the cost...', 'yith-booking-for-woocommerce' ),
								'autocomplete' => 'off',
							),
						),
						array(
							'type'   => 'section',
							'class'  => '_yith_booking_multiply_fixed_base_fee_by_number_of_people_field yith-wcbk-settings-checkbox-container',
							'fields' => array(
								array(
									'type'  => 'checkbox',
									'value' => wc_bool_to_string( $booking_product ? $booking_product->get_multiply_fixed_base_fee_by_number_of_people( 'edit' ) : false ),
									'id'    => '_yith_booking_multiply_fixed_base_fee_by_number_of_people',
									'name'  => '_yith_booking_multiply_fixed_base_fee_by_number_of_people',
								),
								array(
									'type'  => 'label',
									'value' => __( 'Multiply by the number of people', 'yith-booking-for-woocommerce' ),
									'for'   => '_yith_booking_multiply_fixed_base_fee_by_number_of_people',
								),
							),
						),
					),
				)
			);

			$extra_costs = $booking_product ? $booking_product->get_extra_costs( 'edit' ) : array();
			yith_wcbk_get_view( 'product-tabs/utility/html-extra-costs.php', compact( 'extra_costs' ) );

			?>
		</div>
	</div>


	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Advanced Price Rules', 'yith-booking-for-woocommerce' ); ?></h3>
			<div class="yith-wcbk-price-rules__expand-collapse">
				<span class="yith-wcbk-price-rules__expand"><?php esc_html_e( 'Expand all', 'yith-booking-for-woocommerce' ); ?></span>
				<span class="yith-wcbk-price-rules__collapse"><?php esc_html_e( 'Collapse all', 'yith-booking-for-woocommerce' ); ?></span>
			</div>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<div class="yith-wcbk-settings-section__description"><?php esc_html_e( 'You can create advanced rules to set different prices for specific conditions (dates, months, durations).', 'yith-booking-for-woocommerce' ); ?></div>
			<?php
			$price_rules = $booking_product ? $booking_product->get_price_rules() : array();
			$field_name  = '_yith_booking_costs_range';
			yith_wcbk_get_view( 'product-tabs/utility/html-price-rules.php', compact( 'price_rules', 'field_name' ) );
			?>
		</div>
	</div>
</div>
