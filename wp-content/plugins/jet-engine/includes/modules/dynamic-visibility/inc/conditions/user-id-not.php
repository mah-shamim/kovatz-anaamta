<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class User_ID_Not extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'user-id-not';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'User ID is not', 'jet-engine' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'user';
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type       = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$user_ids   = $this->explode_string( $args['user_id'] );
		$current_id = get_current_user_id();

		if ( 'hide' === $type ) {
			return in_array( $current_id, $user_ids );
		} else {
			return ! in_array( $current_id, $user_ids );
		}
	}

	/**
	 * Check if is condition available for meta fields control
	 *
	 * @return boolean [description]
	 */
	public function is_for_fields() {
		return false;
	}

	/**
	 * Check if is condition available for meta value control
	 *
	 * @return boolean [description]
	 */
	public function need_value_detect() {
		return false;
	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new User_ID_Not() );
} );
