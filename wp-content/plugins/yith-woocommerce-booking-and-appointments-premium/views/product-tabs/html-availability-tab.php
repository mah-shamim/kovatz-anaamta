<?php
/**
 * Availability tab in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

?>
<div class="yith-wcbk-product-metabox-options-panel yith-plugin-ui options_group show_if_<?php echo esc_attr( $prod_type ); ?>">

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Availability', 'yith-booking-for-woocommerce' ); ?></h3>
			<span class="yith-wcbk-settings-section__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<div class="yith-wcbk-product-settings__availability__default-availability">
				<?php
				$default_availabilities = $booking_product ? $booking_product->get_default_availabilities( 'edit' ) : array();
				$default_availabilities = ! ! $default_availabilities ? $default_availabilities : array( new YITH_WCBK_Availability() );
				$field_name             = '_yith_booking_default_availabilities';
				ob_start();
				yith_wcbk_get_view( 'product-tabs/utility/html-default-availabilities.php', compact( 'default_availabilities', 'field_name' ) );
				$default_availabilities_html = ob_get_clean();

				yith_wcbk_product_metabox_form_field(
					array(
						'title'  => __( 'Set default availability', 'yith-booking-for-woocommerce' ),
						'desc'   => __( 'Set the default availability for this product. You can override these options by using the additional availability rules below.', 'yith-booking-for-woocommerce' ),
						'class'  => 'yith_booking_default_availabilities_wrapper',
						'fields' => array(
							'type'  => 'html',
							'value' => $default_availabilities_html,
						),
					)
				);

				?>
			</div>
			<div class="yith-wcbk-product-settings__availability__availability-rules">
				<div class="yith-wcbk-product-settings__availability__availability-rules__title">
					<h3><?php esc_html_e( 'Additional availability rules', 'yith-booking-for-woocommerce' ); ?></h3>
					<div class="yith-wcbk-availability-rules__expand-collapse">
						<span class="yith-wcbk-availability-rules__expand"><?php esc_html_e( 'Expand all', 'yith-booking-for-woocommerce' ); ?></span>
						<span class="yith-wcbk-availability-rules__collapse"><?php esc_html_e( 'Collapse all', 'yith-booking-for-woocommerce' ); ?></span>
					</div>
				</div>
				<div class="yith-wcbk-settings-section__description"><?php esc_html_e( 'You can create advanced rules to enable/disable booking availability for specific dates or months', 'yith-booking-for-woocommerce' ); ?></div>
				<?php
				$availability_rules = $booking_product ? $booking_product->get_availability_rules( 'edit' ) : array();
				$field_name         = '_yith_booking_availability_range';
				yith_wcbk_get_view( 'product-tabs/utility/html-availability-rules.php', compact( 'availability_rules', 'field_name' ) );
				?>
			</div>
		</div>
	</div>

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Booking terms', 'yith-booking-for-woocommerce' ); ?></h3>
			<span class="yith-wcbk-settings-section__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php
			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_available_max_per_block_field',
					'title'  => __( 'Max bookings per unit', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Select the maximum number of bookings allowed for each unit. Set 0 (zero) for unlimited.', 'yith-booking-for-woocommerce' ),
					'fields' =>
						array(
							'yith-field'        => true,
							'type'              => 'number',
							'value'             => $booking_product ? $booking_product->get_max_bookings_per_unit( 'edit' ) : 1,
							'id'                => '_yith_booking_max_per_block',
							'name'              => '_yith_booking_max_per_block',
							'class'             => 'yith-wcbk-mini-field',
							'custom_attributes' => array(
								'step' => 1,
								'min'  => 0,
							),
						),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_minimum_duration_field bk_show_if_customer_chooses_blocks yith_booking_multi_fields',
					'title'  => __( 'Minimum booking duration', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Set the minimum booking duration that customers can select.', 'yith-booking-for-woocommerce' ),
					'fields' =>
						array(
							array(
								'yith-field'        => true,
								'type'              => 'number',
								'value'             => $booking_product ? $booking_product->get_minimum_duration( 'edit' ) : 1,
								'id'                => '_yith_booking_minimum_duration',
								'name'              => '_yith_booking_minimum_duration',
								'class'             => 'yith-wcbk-mini-field',
								'custom_attributes' => array(
									'step' => 1,
									'min'  => 1,
								),
							),
							array(
								'type'  => 'html',
								'value' => yith_wcbk_product_metabox_dynamic_duration_qty(),
							),
						),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_maximum_duration_field bk_show_if_customer_chooses_blocks yith_booking_multi_fields',
					'title'  => __( 'Maximum booking duration', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Set the maximum booking duration that customers can select.', 'yith-booking-for-woocommerce' ),
					'fields' =>
						array(
							array(
								'yith-field'        => true,
								'type'              => 'number',
								'value'             => $booking_product ? $booking_product->get_maximum_duration( 'edit' ) : 0,
								'id'                => '_yith_booking_maximum_duration',
								'name'              => '_yith_booking_maximum_duration',
								'class'             => 'yith-wcbk-mini-field',
								'custom_attributes' => array(
									'step' => 1,
									'min'  => 0,
								),
							),
							array(
								'type'  => 'html',
								'value' => yith_wcbk_product_metabox_dynamic_duration_qty(),
							),
						),
				)
			);


			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_request_confirmation_field',
					'title'  => __( 'Require confirmation', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Enable if the admin has to confirm a booking before accepting it.', 'yith-booking-for-woocommerce' ),
					'fields' =>
						array(
							'yith-field' => true,
							'type'       => 'onoff',
							'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_confirmation_required( 'edit' ) : false ),
							'id'         => '_yith_booking_request_confirmation',
							'name'       => '_yith_booking_request_confirmation',
						),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_can_be_cancelled_field',
					'title'  => __( 'Allow cancellation', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Enable if the customer can cancel the booking.', 'yith-booking-for-woocommerce' ),
					'fields' =>
						array(
							'yith-field' => true,
							'type'       => 'onoff',
							'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_cancellation_available( 'edit' ) : false ),
							'id'         => '_yith_booking_can_be_cancelled',
							'name'       => '_yith_booking_can_be_cancelled',
						),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_cancelled_time_field bk_show_if_can_be_cancelled yith_booking_multi_fields',
					'title'  => __( 'The booking can be cancelled up to', 'yith-booking-for-woocommerce' ),
					'fields' =>
						array(
							array(
								'yith-field'        => true,
								'type'              => 'number',
								'value'             => $booking_product ? $booking_product->get_cancellation_available_up_to( 'edit' ) : '0',
								'id'                => '_yith_booking_cancelled_duration',
								'name'              => '_yith_booking_cancelled_duration',
								'class'             => 'yith-wcbk-mini-field',
								'custom_attributes' => array(
									'step' => 1,
									'min'  => 0,
								),
							),
							array(
								'yith-field' => true,
								'type'       => 'select',
								'value'      => $booking_product ? $booking_product->get_cancellation_available_up_to_unit( 'edit' ) : 'day',
								'id'         => '_yith_booking_cancelled_unit',
								'name'       => '_yith_booking_cancelled_unit',
								'class'      => 'wc-enhanced-select',
								'options'    => yith_wcbk_get_cancel_duration_units(),
							),
							array(
								'yith-field' => true,
								'type'       => 'html',
								'html'       => __( 'before the booking start date', 'yith-booking-for-woocommerce' ),
							),
						),
				)
			);


			?>
		</div>
	</div>

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Booking preferences', 'yith-booking-for-woocommerce' ); ?></h3>
			<span class="yith-wcbk-settings-section__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_allowed_start_days_field',
					'title'  => __( 'Allowed Start Days', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Select on which days the booking can start. Leave empty if it can start without any limit on any day of the week.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'yith-field'        => true,
						'type'              => 'select',
						'class'             => 'wc-enhanced-select select short',
						'multiple'          => true,
						'name'              => '_yith_booking_allowed_start_days',
						'options'           => yith_wcbk_get_days_array(),
						'value'             => $booking_product ? $booking_product->get_allowed_start_days( 'edit' ) : array(),
						'custom_attributes' => array(
							'style' => 'width:400px',
						),

					),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_buffer_field yith_booking_multi_fields',
					'title'  => __( 'Buffer time', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Time for preparation or cleanup between two bookings.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'yith-field'        => true,
							'type'              => 'number',
							'value'             => $booking_product ? $booking_product->get_buffer( 'edit' ) : 0,
							'id'                => '_yith_booking_buffer',
							'name'              => '_yith_booking_buffer',
							'custom_attributes' => apply_filters( 'yith_wcbk_buffer_field_custom_attributes', 'step="1" min="0"' ),
							'class'             => 'yith-wcbk-mini-field',
						),
						array(
							'yith-field' => true,
							'type'       => 'html',
							'html'       => yith_wcbk_product_metabox_dynamic_duration_unit(),
						),
					),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_available_checkin_field',
					'title'  => __( 'Check-in time', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Insert check-in time for your customers', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'yith-field' => true,
						'type'       => 'text',
						'value'      => $booking_product ? $booking_product->get_check_in( 'edit' ) : '',
						'id'         => '_yith_booking_checkin',
						'name'       => '_yith_booking_checkin',
						'class'      => 'yith-wcbk-mini-field',
					),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_available_checkout_field',
					'title'  => __( 'Check-out time', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Insert check-out time for your customers', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'yith-field' => true,
						'type'       => 'text',
						'value'      => $booking_product ? $booking_product->get_check_out( 'edit' ) : '',
						'id'         => '_yith_booking_checkout',
						'name'       => '_yith_booking_checkout',
						'class'      => 'yith-wcbk-mini-field',
					),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_time_increment_based_on_duration bk_show_if_time',
					'title'  => __( 'Time increment based on duration', 'yith-booking-for-woocommerce' ),
					'desc'   => __( "Select if the time increment of your booking is based on booking duration. By default the time increment is 1 hour for hourly bookings and 15 minutes for per-minute bookings. Example: if enabled and your booking duration is 3 hours, the time increment will be 3 hours, so you'll see the following time slots: 8:00 - 11:00 - 14:00 - 17:00", 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'type'  => 'checkbox',
						'value' => wc_bool_to_string( $booking_product ? $booking_product->get_time_increment_based_on_duration( 'edit' ) : false ),
						'id'    => '_yith_booking_time_increment_based_on_duration',
						'name'  => '_yith_booking_time_increment_based_on_duration',
					),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_time_increment_including_buffer bk_show_if_fixed_and_time',
					'title'  => __( 'Time increment including buffer', 'yith-booking-for-woocommerce' ),
					'desc'   => __( "Select if you want to include buffer time to the time increment. Example: if enabled and the booking duration is 1 hour and you set a buffer of 1 hour, the time increment will be 1 hour + 1 hour, so you'll see the following time slots: 8:00 - 10:00 - 12:00 - 14:00", 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'type'  => 'checkbox',
						'value' => wc_bool_to_string( $booking_product ? $booking_product->get_time_increment_including_buffer( 'edit' ) : false ),
						'id'    => '_yith_booking_time_increment_including_buffer',
						'name'  => '_yith_booking_time_increment_including_buffer',
					),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_available_allow_after_field yith_booking_multi_fields',
					'title'  => __( 'Minimum advance reservation', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Insert the minimum advance reservation for the booking. For example: if you set it to 10 days, customers can book now a booking that will start in 10 days', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'yith-field'        => true,
							'type'              => 'number',
							'value'             => $booking_product ? $booking_product->get_minimum_advance_reservation( 'edit' ) : 0,
							'id'                => '_yith_booking_allow_after',
							'name'              => '_yith_booking_allow_after',
							'class'             => 'yith-wcbk-mini-field',
							'custom_attributes' => array(
								'step' => 1,
								'min'  => 0,
							),
						),
						array(
							'yith-field' => true,
							'type'       => 'select',
							'value'      => $booking_product ? $booking_product->get_minimum_advance_reservation_unit( 'edit' ) : 'day',
							'id'         => '_yith_booking_allow_after_unit',
							'name'       => '_yith_booking_allow_after_unit',
							'class'      => 'wc-enhanced-select',
							'options'    => array(
								'month' => __( 'Month(s)', 'yith-booking-for-woocommerce' ),
								'day'   => __( 'Day(s)', 'yith-booking-for-woocommerce' ),
								'hour'  => __( 'Hour(s)', 'yith-booking-for-woocommerce' ),
							),
						),
					),
				)
			);

			yith_wcbk_product_metabox_form_field(
				array(
					'class'  => '_yith_booking_available_allow_until_field yith_booking_multi_fields',
					'title'  => __( 'Maximum advance reservation', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Insert the maximum advance reservation for the booking. For example: if you set it to 6 months, customers can only book within 6 months.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'yith-field'        => true,
							'type'              => 'number',
							'value'             => $booking_product ? $booking_product->get_maximum_advance_reservation( 'edit' ) : 1,
							'id'                => '_yith_booking_allow_until',
							'name'              => '_yith_booking_allow_until',
							'class'             => 'yith-wcbk-mini-field',
							'custom_attributes' => array(
								'step' => 1,
								'min'  => 1,
							),
						),
						array(
							'yith-field' => true,
							'type'       => 'select',
							'value'      => $booking_product ? $booking_product->get_maximum_advance_reservation_unit( 'edit' ) : 'year',
							'id'         => '_yith_booking_allow_until_unit',
							'name'       => '_yith_booking_allow_until_unit',
							'class'      => 'wc-enhanced-select',
							'options'    => array(
								'year'  => __( 'Year(s)', 'yith-booking-for-woocommerce' ),
								'month' => __( 'Month(s)', 'yith-booking-for-woocommerce' ),
								'day'   => __( 'Day(s)', 'yith-booking-for-woocommerce' ),
							),
						),
					),
				)
			);
			?>

		</div>
	</div>
</div>
