<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Less_Or_Equal extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'less-or-equal';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Less or Equal', 'jet-engine' );
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type          = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$data_type     = ! empty( $args['data_type'] ) ? $args['data_type'] : 'chars';
		$current_value = $this->get_current_value( $args );
		$value         = $args['value'];
		$values        = $this->adjust_values_type( $current_value, $value, $data_type );

		if ( 'hide' === $type ) {
			return $values['current'] > $values['compare'];
		} else {
			return $values['current'] <= $values['compare'];
		}
	}

	/**
	 * This condition is required data type detection
	 *
	 * @return boolean [description]
	 */
	public function need_type_detect() {
		return true;
	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Less_Or_Equal() );
} );
