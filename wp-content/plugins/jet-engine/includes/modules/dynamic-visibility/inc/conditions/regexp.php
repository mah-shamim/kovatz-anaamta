<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Regexp extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'regexp';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Regexp', 'jet-engine' );
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type          = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$pattern       = $args['value'];
		$pattern       = trim( $pattern, '/' );
		$pattern       = '/' . $pattern . '/';
		$current_value = $this->get_current_value( $args );

		$result = preg_match( $pattern, $current_value );

		if ( 'hide' === $type ) {
			return empty( $result );
		} else {
			return ! empty( $result );
		}
	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Regexp() );
} );
