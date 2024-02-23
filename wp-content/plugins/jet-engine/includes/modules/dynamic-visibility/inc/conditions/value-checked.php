<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Value_Checked extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'value-checked';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Value is checked', 'jet-engine' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'jet-engine';
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type          = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$current_value = $this->get_current_value( $args );

		if ( ! is_array( $current_value ) ) {
			$current_value = explode( ', ', $current_value );
		}

		if ( empty( $current_value ) ) {
			$current_value = array();
		}

		if ( in_array( 'true', $current_value ) || in_array( 'false', $current_value ) ) {
			$current_value = $this->checkboxes_to_array( $current_value );
		}

		if ( 'hide' === $type ) {
			return ! in_array( $args['value'], $current_value );
		} else {
			return in_array( $args['value'], $current_value );
		}

	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Value_Checked() );
} );
