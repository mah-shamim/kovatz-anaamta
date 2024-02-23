<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Not_Contains extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'not-contains';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Doesn\'t contain', 'jet-engine' );
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type          = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$values        = $this->explode_string( $args['value'] );
		$current_value = $this->get_current_value( $args );

		if ( is_array( $current_value ) ) {
			$current_value = json_encode( $current_value );
		}

		$found = false;

		foreach ( $values as $value ) {
			if ( false !== strpos( $current_value, $value ) ) {
				$found = true;
			}
		}

		if ( 'hide' === $type ) {
			return $found;
		} else {
			return ! $found;
		}

	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Not_Contains() );
} );
