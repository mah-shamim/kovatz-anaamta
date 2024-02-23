<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Is_Mobile extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'wp-is-mobile';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Is mobile device', 'jet-engine' );
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

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type = ! empty( $args['type'] ) ? $args['type'] : 'show';

		if ( 'hide' === $type ) {
			return ! wp_is_mobile();
		} else {
			return wp_is_mobile();
		}

	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Is_Mobile() );
} );
