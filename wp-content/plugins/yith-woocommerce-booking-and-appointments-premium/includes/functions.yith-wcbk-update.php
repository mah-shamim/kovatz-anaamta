<?php
/**
 * Update functions
 *
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Update booking lookup tables.
 */
function yith_wcbk_update_300_booking_lookup_tables() {
	// Don't need to use the 'truncate' parameter, since the look-up table doesn't exist before 3.0.
	yith_wcbk_update_booking_lookup_tables();
}

/**
 * Clear scheduled hooks, since the 3.0.0 version uses the WooCommerce Action Scheduler instead.
 */
function yith_wcbk_update_300_clear_scheduled_hooks() {
	wp_clear_scheduled_hook( 'yith_wcbk_check_reject_pending_confirmation_bookings' );
	wp_clear_scheduled_hook( 'yith_wcbk_check_complete_paid_bookings' );
}

/**
 * Update options
 */
function yith_wcbk_update_300_options() {
	$categories         = get_option( 'yith-wcbk-booking-categories', array() );
	$categories_to_show = ! ! $categories ? 'specific' : 'all';
	update_option( 'yith-wcbk-booking-categories-to-show', $categories_to_show );

	$reject_bookings_after = get_option( 'yith-wcbk-reject-pending-confirmation-bookings-after', 0 );
	$reject_enabled        = ! ! $reject_bookings_after ? 'yes' : 'no';
	update_option( 'yith-wcbk-reject-pending-confirmation-booking-enabled', $reject_enabled );

	$complete_bookings_after = get_option( 'yith-wcbk-complete-paid-bookings-after', '' );
	$complete_enabled        = '' !== $complete_bookings_after ? 'yes' : 'no';
	update_option( 'yith-wcbk-complete-paid-bookings-enabled', $complete_enabled );

	$show_to_logged_users_only = get_option( 'yith-wcbk-show-booking-form-to-logged-users-only', 'no' ) === 'yes';
	$show_to                   = ! ! $show_to_logged_users_only ? 'logged-in' : 'all';
	update_option( 'yith-wcbk-show-booking-form-to', $show_to );

	$unique_range_picker_enabled = get_option( 'yith-wcbk-unique-calendar-range-picker-enabled', 'yes' ) === 'yes';
	$date_range_picker_layout    = ! ! $unique_range_picker_enabled ? 'unique' : 'different';
	update_option( 'yith-wcbk-date-range-picker-layout', $date_range_picker_layout );

	$date_picker_inline = get_option( 'yith-wcbk-display-date-picker-inline', 'no' ) === 'yes';
	$calendar_style     = ! ! $date_picker_inline ? 'inline' : 'dropdown';
	update_option( 'yith-wcbk-calendar-style', $calendar_style );

}

/**
 * Update Search Forms
 *
 * @return bool False to stop the execution, true otherwise.
 */
function yith_wcbk_update_300_search_forms() {

	$forms = get_posts(
		array(
			'limit'        => 10,
			'post_type'    => 'ywcbk-search-form',
			'fields'       => 'ids',
			'meta_key'     => '_background-color', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_compare' => 'EXISTS',
		)
	);

	if ( ! $forms ) {
		// Stop the execution, since there are no more forms to update.
		return false;
	}

	foreach ( $forms as $form_id ) {
		$background_color   = get_post_meta( $form_id, '_background-color', true );
		$text_color         = get_post_meta( $form_id, '_text-color', true );
		$search_bg_color    = get_post_meta( $form_id, '_search-background-color', true );
		$search_text_color  = get_post_meta( $form_id, '_search-text-color', true );
		$search_hover_color = get_post_meta( $form_id, '_search-hover-color', true );

		// defaults.
		$background_color   = ! ! $background_color ? $background_color : 'transparent';
		$text_color         = ! ! $text_color ? $text_color : '#333333';
		$search_bg_color    = ! ! $search_bg_color ? $search_bg_color : '#3b4b56';
		$search_text_color  = ! ! $search_text_color ? $search_text_color : '#ffffff';
		$search_hover_color = ! ! $search_hover_color ? $search_hover_color : '#2e627c';

		$colors = array(
			'background' => $background_color,
			'text'       => $text_color,
		);

		$search_button_colors = array(
			'background'       => $search_bg_color,
			'text'             => $search_text_color,
			'background-hover' => $search_hover_color,
			'text-hover'       => $search_text_color,
		);

		update_post_meta( $form_id, '_colors', $colors );
		update_post_meta( $form_id, '_search-button-colors', $search_button_colors );

		delete_post_meta( $form_id, '_background-color' );
		delete_post_meta( $form_id, '_text-color' );
		delete_post_meta( $form_id, '_search-background-color' );
		delete_post_meta( $form_id, '_search-text-color' );
		delete_post_meta( $form_id, '_search-hover-color' );

	}

	// Next execution!
	return true;
}

/**
 * Remove daily start time from booking products by converting it into a default availability.
 *
 * @return bool False to stop the execution, true otherwise.
 */
function yith_wcbk_update_300_daily_start_time() {

	$products = get_posts(
		array(
			'limit'        => 10,
			'post_type'    => 'product',
			'fields'       => 'ids',
			'meta_key'     => '_yith_booking_daily_start_time', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_compare' => 'EXISTS',
		)
	);

	if ( ! $products ) {
		// Stop the execution, since there are no more products to update.
		return false;
	}

	foreach ( $products as $product_id ) {
		$daily_start_date = get_post_meta( $product_id, '_yith_booking_daily_start_time', true );
		$daily_start_date = yith_wcbk_time_slot_validate( $daily_start_date ) ? $daily_start_date : false;

		if ( $daily_start_date && '00:00' !== $daily_start_date && yith_wcbk_is_booking_product( $product_id ) ) {
			$default_availabilities = array(
				array(
					'day'        => 'all',
					'bookable'   => 'yes',
					'time_slots' => array(
						array(
							'from' => $daily_start_date,
							'to'   => '00:00',
						),
					),
				),
			);

			update_post_meta( $product_id, '_yith_booking_default_availabilities', $default_availabilities, true );
		}

		delete_post_meta( $product_id, '_yith_booking_daily_start_time' );
	}

	// Next execution!
	return true;
}

/**
 * Enable legacy menu.
 */
function yith_wcbk_update_300_enable_legacy_menu() {
	update_option( 'yith-wcbk-legacy-show-bookings-menu-in-wp-menu', 'yes' );
}

/**
 * Update DB Version.
 */
function yith_wcbk_update_300_db_version() {
	YITH_WCBK_Install::update_db_version( '3.0.0' );
}
