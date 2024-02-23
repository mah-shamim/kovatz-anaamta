<?php
/**
 * PRO Ajax actions used in by admin.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.2.1
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */

/**
 * Toggle entry stars from Entries table.
 *
 * @since 1.1.6
 */
function wpforms_entry_list_star() {

	// Run a security check.
	check_ajax_referer( 'wpforms-admin', 'nonce' );

	// Check for permissions.
	if ( ! wpforms_current_user_can() ) {
		wp_send_json_error();
	}

	if ( empty( $_POST['entry_id'] ) || empty( $_POST['task'] ) ) {
		wp_send_json_error();
	}

	$task       = $_POST['task'];
	$entry_id   = absint( $_POST['entry_id'] );
	$is_success = false;

	if ( 'star' === $task ) {
		$is_success = wpforms()->entry->update(
			$entry_id,
			array(
				'starred' => '1',
			)
		);
	} elseif ( 'unstar' === $task ) {
		$is_success = wpforms()->entry->update(
			$entry_id,
			array(
				'starred' => '0',
			)
		);
	}

	$is_success ? wp_send_json_success() : wp_send_json_error();
}

add_action( 'wp_ajax_wpforms_entry_list_star', 'wpforms_entry_list_star' );

/**
 * Toggle entry read state from Entries table.
 *
 * @since 1.1.6
 */
function wpforms_entry_list_read() {

	// Run a security check.
	check_ajax_referer( 'wpforms-admin', 'nonce' );

	// Check for permissions.
	if ( ! wpforms_current_user_can() ) {
		wp_send_json_error();
	}

	if ( empty( $_POST['entry_id'] ) || empty( $_POST['task'] ) ) {
		wp_send_json_error();
	}

	$task       = $_POST['task'];
	$entry_id   = absint( $_POST['entry_id'] );
	$is_success = false;

	if ( 'read' === $task ) {
		$is_success = wpforms()->entry->update(
			$entry_id,
			array(
				'viewed' => '1',
			)
		);
	} elseif ( 'unread' === $task ) {
		$is_success = wpforms()->entry->update(
			$entry_id,
			array(
				'viewed' => '0',
			)
		);
	}

	$is_success ? wp_send_json_success() : wp_send_json_error();
}

add_action( 'wp_ajax_wpforms_entry_list_read', 'wpforms_entry_list_read' );

/**
 * Deactivate license.
 *
 * @since 1.3.9
 */
function wpforms_deactivate_license() {

	// Run a security check.
	check_ajax_referer( 'wpforms-admin', 'nonce' );

	// Check for permissions.
	if ( ! wpforms_current_user_can() ) {
		wp_send_json_error();
	}

	wpforms()->license->deactivate_key( true );
}

add_action( 'wp_ajax_wpforms_deactivate_license', 'wpforms_deactivate_license' );

/**
 * Refresh license.
 *
 * @since 1.3.9
 */
function wpforms_refresh_license() {

	// Run a security check.
	check_ajax_referer( 'wpforms-admin', 'nonce' );

	// Check for permissions.
	if ( ! wpforms_current_user_can() ) {
		wp_send_json_error();
	}

	// Check for license key.
	if ( empty( $_POST['license'] ) ) {
		wp_send_json_error( esc_html__( 'Please enter a license key.', 'wpforms' ) );
	}

	wpforms()->license->validate_key( $_POST['license'], true, true );
}

add_action( 'wp_ajax_wpforms_refresh_license', 'wpforms_refresh_license' );

/**
 * Save a single notification state (opened or closed) for a form for a currently logged in user.
 *
 * @since 1.4.1
 * @deprecated 1.4.8
 */
function wpforms_builder_notification_state_save() {

	_deprecated_function( __FUNCTION__, '1.4.8 of WPForms plugin', 'wpforms_builder_settings_block_state_save()' );

	check_ajax_referer( 'wpforms-builder', 'nonce' );

	if ( empty( $_POST['block_type'] ) ) {
		$_POST['block_type'] = 'notification';
	}
	wpforms_builder_settings_block_state_save();
}

add_action( 'wp_ajax_wpforms_builder_notification_state_save', 'wpforms_builder_notification_state_save' );

/**
 * Save a single settings block state (opened or closed) for a form for a currently logged in user.
 *
 * @since 1.4.8
 */
function wpforms_builder_settings_block_state_save() {

	// Run a security check.
	check_ajax_referer( 'wpforms-builder', 'nonce' );

	if ( empty( $_POST ) || ! wpforms_current_user_can() ) {
		wp_send_json_error();
	}

	$form_id    = ! empty( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
	$block_id   = ! empty( $_POST['block_id'] ) ? absint( $_POST['block_id'] ) : 0;
	$block_type = ! empty( $_POST['block_type'] ) ? sanitize_key( $_POST['block_type'] ) : '';
	$state      = ! empty( $_POST['state'] ) ? sanitize_key( $_POST['state'] ) : '';

	if (
		empty( $form_id ) ||
		empty( $block_id ) ||
		empty( $block_type ) ||
		( empty( $state ) || ! in_array( $state, array( 'opened', 'closed' ), true ) )
	) {
		wp_send_json_error();
	}

	$all_states = (array) get_user_meta( get_current_user_id(), 'wpforms_builder_settings_collapsable_block_states', true );

	$all_states[ $form_id ][ $block_type ][ $block_id ] = $state;

	update_user_meta( get_current_user_id(), 'wpforms_builder_settings_collapsable_block_states', $all_states );

	wp_send_json_success();
}

add_action( 'wp_ajax_wpforms_builder_settings_block_state_save', 'wpforms_builder_settings_block_state_save' );

/**
 * Remove a single notification state (opened or closed) for a form for a currently logged in user.
 *
 * @since 1.4.1
 * @deprecated 1.4.8
 */
function wpforms_builder_notification_state_remove() {

	_deprecated_function( __FUNCTION__, '1.4.8 of WPForms plugin', 'wpforms_builder_settings_block_state_remove()' );

	check_ajax_referer( 'wpforms-builder', 'nonce' );

	if ( empty( $_POST['block_type'] ) ) {
		$_POST['block_type'] = 'notification';
	}
	wpforms_builder_settings_block_state_remove();
}

add_action( 'wp_ajax_wpforms_builder_notification_state_remove', 'wpforms_builder_notification_state_remove' );

/**
 * Remove a single settings block state (opened or closed) for a form for a currently logged in user.
 *
 * @since 1.4.8
 */
function wpforms_builder_settings_block_state_remove() {

	// Run a security check.
	check_ajax_referer( 'wpforms-builder', 'nonce' );

	if ( empty( $_POST ) || ! wpforms_current_user_can() ) {
		wp_send_json_error();
	}

	$form_id    = ! empty( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
	$block_id   = ! empty( $_POST['block_id'] ) ? absint( $_POST['block_id'] ) : 0;
	$block_type = ! empty( $_POST['block_type'] ) ? sanitize_key( $_POST['block_type'] ) : '';

	if ( empty( $form_id ) || empty( $block_id ) ) {
		wp_send_json_error();
	}

	$all_states = get_user_meta( get_current_user_id(), 'wpforms_builder_settings_collapsable_block_states', true );

	// Backward compatibility for notifications.
	if ( 'notification' === $block_type && empty( $all_states[ $form_id ][ $block_type ][ $block_id ] ) ) {
		$notification_states_meta = get_user_meta( get_current_user_id(), 'wpforms_builder_notification_states', true );
		$notification_states      = $notification_states_meta;
	}

	if (
		empty( $all_states[ $form_id ][ $block_type ][ $block_id ] ) &&
		empty( $notification_states[ $form_id ][ $block_id ] )
	) {
		wp_send_json_error();
	}

	// Backward compatibility for notifications.
	if ( 'notification' === $block_type && ! empty( $notification_states[ $form_id ][ $block_id ] ) ) {
		unset( $notification_states[ $form_id ][ $block_id ] );
	}
	if ( ! empty( $notification_states_meta ) && ! empty( $notification_states ) ) {
		update_user_meta( get_current_user_id(), 'wpforms_builder_notification_states', $notification_states );
	}
	if ( ! empty( $notification_states_meta ) && empty( $notification_states ) ) {
		delete_user_meta( get_current_user_id(), 'wpforms_builder_notification_states' );
	}

	if ( ! empty( $all_states[ $form_id ][ $block_type ][ $block_id ] ) ) {
		unset( $all_states[ $form_id ][ $block_type ][ $block_id ] );
	}

	update_user_meta( get_current_user_id(), 'wpforms_builder_settings_collapsable_block_states', $all_states );

	wp_send_json_success();
}

add_action( 'wp_ajax_wpforms_builder_settings_block_state_remove', 'wpforms_builder_settings_block_state_remove' );
