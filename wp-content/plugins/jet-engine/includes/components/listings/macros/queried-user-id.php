<?php
namespace Jet_Engine\Macros;

/**
 * Returns ID of the queried user.
 */
class Queried_User_Id extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'queried_user_id';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Queried user ID', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$user = jet_engine()->listings->data->get_queried_user_object();

		if ( ! $user ) {
			return false;
		} else {
			return $user->ID;
		}
	}
}