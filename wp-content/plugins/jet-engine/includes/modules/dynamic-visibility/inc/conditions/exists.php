<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Exists extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'exists';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Exists', 'jet-engine' );
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type          = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$current_value = $this->get_current_value( $args );

		if ( ! empty( $current_value ) && ! is_array( $current_value ) && PHP_INT_MAX == $current_value ) {
			$current_value = array();
		}

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
	$manager->register_condition( new Exists() );
} );
