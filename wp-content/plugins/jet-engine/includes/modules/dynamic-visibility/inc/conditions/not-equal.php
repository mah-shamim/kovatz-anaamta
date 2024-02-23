<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Not_Equal extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'not-equal';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Not Equal', 'jet-engine' );
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type          = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$current_value = $this->get_current_value( $args );

		if ( 'hide' === $type ) {
			return $current_value == $args['value'];
		} else {
			return $current_value != $args['value'];
		}

	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Not_Equal() );
} );
