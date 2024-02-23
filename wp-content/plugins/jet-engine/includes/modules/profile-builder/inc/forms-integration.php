<?php
namespace Jet_Engine\Modules\Profile_Builder;

class Forms_Integration {

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_filter( 'jet-engine/forms/insert-post/pre-check', array( $this, 'check_posts_limit' ), 10, 4 );
	}

	/**
	 * Check posts limit
	 */
	public function check_posts_limit( $res, $postarr, $args, $notifications ) {

		// Apply restrictions only for post inserting, not the update
		if ( ! empty( $postarr['ID'] ) ) {
			return $res;
		}
		
		$restrictions = Module::instance()->get_restrictions_handler();
		$check = $restrictions->current_user_can_submit_posts( $postarr['post_type'] );

		if ( ! $check ) {
			$notifications->set_specific_status( $restrictions->get_latest_message() );
			return false;
		} else {
			return $res;
		}

	}

}
