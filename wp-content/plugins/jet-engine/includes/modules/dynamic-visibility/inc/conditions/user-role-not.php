<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class User_Role_Not extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'user-role-not';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'User Role is not', 'jet-engine' );
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

		$type  = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$roles = ! empty( $args['user_role'] ) ? $args['user_role'] : array();

		if ( ! is_array( $roles ) ) {
			$roles = array( $roles );
		}

		if ( 'hide' === $type ) {
			if ( ! is_user_logged_in() ) {
				return false;
			} else {

				$user = wp_get_current_user();

				foreach ( $roles as $role ) {
					if ( in_array( $role, (array) $user->roles ) ) {
						return true;
					}
				}

				return false;
			}
		} else {

			if ( ! is_user_logged_in() ) {
				return true;
			} else {

				$user = wp_get_current_user();

				foreach ( $roles as $role ) {
					if ( in_array( $role, (array) $user->roles ) ) {
						return false;
					}
				}

				return true;

			}

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
	$manager->register_condition( new User_Role_Not() );
} );
