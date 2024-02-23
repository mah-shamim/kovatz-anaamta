<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Between extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'between';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Between', 'jet-engine' );
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

		$result = false;

		if ( isset( $values[0] ) && isset( $values[1] ) ) {

			if ( $values[1] > $values[0] ) {
				$result = $values[0] <= $current_value && $values[1] >= $current_value ;
			} else {
				$result = $values[1] <= $current_value && $values[0] >= $current_value ;
			}

		}

		if ( 'hide' === $type ) {
			return ! $result;
		} else {
			return $result;
		}
	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Between() );
} );
