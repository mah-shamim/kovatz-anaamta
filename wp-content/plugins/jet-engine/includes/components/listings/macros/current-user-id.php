<?php
namespace Jet_Engine\Macros;

/**
 * Returns ID of current user.
 */
class Current_User_Id extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'current_user_id';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Current user ID', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {
		return get_current_user_id();
	}
}