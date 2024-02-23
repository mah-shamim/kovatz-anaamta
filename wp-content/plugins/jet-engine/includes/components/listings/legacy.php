<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Listings_Legacy class
 */
class Jet_Engine_Listings_Legacy {

	private $option_key = 'jet_engine_disable_listing_legacy_settings';
	private $action_key = 'jet_engine_listing_switch_legacy';

	public function __construct() {
		add_action( 'admin_action_' . $this->action_key, array( $this, 'switch_legacy' ) );
	}

	public function switch_legacy() {
		
		if ( ! $this->can_disable() || ! wp_verify_nonce( $_GET['_nonce'], $this->action_key ) ) {
			wp_die( __( 'You not allowed to do this.', 'jet-engine' ), __( 'Error', 'jet-engine' ) );
		}

		$is_disabled = $this->is_disabled();
		$is_disabled = ! $is_disabled;
		update_option( $this->option_key, $is_disabled, true );

		wp_die( 
			sprintf( 
				__( 'Legacy option switched. Please reload the page where you clicked the link to apply changes. To switch legacy options back - %s', 'jet-engine' ),
				$this->get_legacy_switch_link( __( 'click here', 'jet-engine' ) )
			), 
			__( 'Legacy Options Switched', 'jet-engine' ) );
	}

	
	
	public function get_notice() {

		if ( $this->is_disabled() ) {
			return sprintf(
				__( '<b>Query options</b> marked as legacy and disabled. To enable these options (not recommended) - %s', 'jet-engine' ),
				$this->get_legacy_switch_link( __( 'click here', 'jet-engine' ), true )
			);
		} else {
			return sprintf(
				__( '<b>Query options</b> inside Listing Grid marked as <b>legacy</b>. We recommend to use <b>Query Builder</b> instead. You can disable these options to optimize performance a bit. To disable legacy options - %s', 'jet-engine' ),
				$this->get_legacy_switch_link( __( 'click here', 'jet-engine' ), true )
			);
		}

	}

	public function listing_has_query_notice( $listing ) {
		
		if ( ! $this->is_disabled() ) {
			return;
		}

		if ( ! $listing->listing_query_id ) {
			_e( 'Please set the <b>Query</b> for the listing. You can do this by choosing listing item with <b>Query source</b> or by adding query in <b>Custom Query</b> section', 'jet-engine' );
		}

	}

	public function get_legacy_switch_link( $text = '', $blank = false ) {
		return sprintf( 
			'<a href="%1$s" %2$s>%3$s</a>', 
			$this->get_legacy_switch_url(), 
			( $blank ? 'target="_blank"' : '' ), 
			$text 
		);
	}

	public function get_legacy_switch_url() {
		return add_query_arg(
			array(
				'action' => $this->action_key,
				'_nonce' => wp_create_nonce( $this->action_key ),
			),
			admin_url( 'admin.php' )
		);
	}

	public function can_disable() {
		return current_user_can( 'manage_options' );
	}

	public function is_disabled() {
		return get_option( $this->option_key, false );
	}

}
