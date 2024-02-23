<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Not_In_List extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'not-in-list';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Not in the list', 'jet-engine' );
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

			if ( in_array( 'true', $current_value ) || in_array( 'false', $current_value ) ) {
				$current_value = $this->checkboxes_to_array( $current_value );
			}

			if ( empty( $current_value ) ) {
				if ( 'hide' === $type ) {
					return false;
				} else {
					return true;
				}
			}

			$found = false;

			foreach ( $current_value as $value ) {
				if ( in_array( $value, $values ) ) {
					$found = true;
				}
			}

			if ( 'hide' === $type ) {
				return $found;
			} else {
				return ! $found;
			}

		} else {
			if ( 'hide' === $type ) {
				return in_array( $current_value, $values );
			} else {
				return ! in_array( $current_value, $values );
			}
		}

	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Not_In_List() );
} );
