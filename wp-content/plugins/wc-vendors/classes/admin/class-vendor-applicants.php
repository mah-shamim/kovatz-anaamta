<?php

/**
 * WCV_Vendor_Applicants class
 */
class WCV_Vendor_Applicants {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_filter( 'user_row_actions', array( $this, 'user_row_actions' ), 10, 2 );
		add_filter( 'load-users.php', array( $this, 'user_row_actions_commit' ) );
		add_action( 'wcvendors_approve_vendor', array( $this, 'add_vendor_status' ), 10, 1 );
	}

	/**
	 * Add user action on user screen
	 *
	 * @param array  $actions The user actions.
	 * @param object $user_object The user object.
	 *
	 * @return array $actions The user actions.
	 *
	 * @version 2.4.8 - Added nonce check and modified approve and deny urls
	 */
	public function user_row_actions( $actions, $user_object ) {
		global $wpdb;
		$pending_vendors = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->usermeta WHERE meta_key = '{$wpdb->prefix}capabilities' AND meta_value LIKE '%pending_vendor%'" );
		if ( in_array( 'pending_vendor', $user_object->roles, true ) ) {
			$approve_url = wp_nonce_url( 'users.php?action=approve_vendor&user_id=' . $user_object->ID, 'vendor_approval' );
			$deny_url    = wp_nonce_url( 'users.php?action=deny_vendor&user_id=' . $user_object->ID, 'vendor_approval' );
			if ( 2 <= $pending_vendors ) {
				$approve_url = add_query_arg( 'role', 'pending_vendor', $approve_url );
				$deny_url    = add_query_arg( 'role', 'pending_vendor', $deny_url );
			}
			$actions['approve_vendor'] = "<a href='$approve_url'>" . __( 'Approve', 'wc-vendors' ) . '</a>';
			$actions['deny_vendor']    = "<a href='$deny_url'>" . __( 'Deny', 'wc-vendors' ) . '</a>';
		}

		return $actions;
	}


	/**
	 * Process the approve and deny actions for the user screen
	 *
	 * @since 1.0.1
	 * @version 2.1.10
	 * @version 2.4.8 - Added nonce check
	 */
	public function user_row_actions_commit() {

		if ( ! empty( $_GET['action'] ) && ! empty( $_GET['user_id'] ) && ! empty( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'vendor_approval' ) ) {

			$wp_user_object = new WP_User( (int) $_GET['user_id'] );

			switch ( $_GET['action'] ) { // phpcs:ignore
				case 'approve_vendor':
					// Remove the pending vendor role.
					$wp_user_object->remove_role( 'pending_vendor' );
					wcv_set_primary_vendor_role( $wp_user_object );
					add_action( 'admin_notices', array( $this, 'approved' ) );
					do_action( 'wcvendors_approve_vendor', $wp_user_object );
					break;

				case 'deny_vendor':
					$role = apply_filters( 'wcvendors_denied_vendor_role', get_option( 'default_role', 'subscriber' ) );
					$wp_user_object->remove_role( 'pending_vendor' );
					// Only add the default role if the user uas no other roles.
					if ( empty( $wp_user_object->roles ) ) {
						$wp_user_object->add_role( $role );
					}
					add_action( 'admin_notices', array( $this, 'denied' ) );
					do_action( 'wcvendors_deny_vendor', $wp_user_object );
					break;

				default:
					break;
			}
		}
	}


	/**
	 *  Denied vendor message
	 */
	public function denied() {

		echo '<div class="updated">';
		/* translators: %s: vendor name */
		echo '<p>' . sprintf( wp_kses_post( __( '%s has been <b>denied</b>.', 'wc-vendors' ) ), esc_html( wcv_get_vendor_name() ) ) . '</p>';
		echo '</div>';
	}


	/**
	 * Approved vendor message
	 */
	public function approved() {
		echo '<div class="updated">';
		/* translators: %s: vendor name */
		echo '<p>' . sprintf( wp_kses_post( __( '%s has been <b>approved</b>.', 'wc-vendors' ) ), esc_html( wcv_get_vendor_name() ) ) . '</p>';
		echo '</div>';
	}


	/**
	 *  Show vendor pending link
	 *
	 * @param array $values The values.
	 *
	 * @return array The parsed values.
	 */
	public function show_pending_vendors_link( $values ) {

		$values['pending_vendors'] = '<a href="?role=asd">' . __( 'Pending Vendors', 'wc-vendors' ) . ' <span class="count">(3)</span></a>';

		return $values;
	}

	/**
	 * Add vendor status
	 *
	 * @param \WP_User $wp_user_object The wp user object.
	 */
	public function add_vendor_status( $wp_user_object ) {
		$user_id = $wp_user_object->ID;
		update_user_meta( $user_id, '_wcv_vendor_status', 'active' );
	}
}
