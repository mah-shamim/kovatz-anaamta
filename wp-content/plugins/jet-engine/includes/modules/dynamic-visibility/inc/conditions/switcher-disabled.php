<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Switcher_Disabled extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'switcher-disabled';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Switcher disabled', 'jet-engine' );
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
		$current_value = filter_var( $current_value, FILTER_VALIDATE_BOOLEAN );
		$current_value = ! $current_value;

		if ( 'hide' === $type ) {
			return empty( $current_value );
		} else {
			return ! empty( $current_value );
		}

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
	$manager->register_condition( new Switcher_Disabled() );
} );
