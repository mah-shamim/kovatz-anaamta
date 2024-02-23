<?php
namespace Jet_Engine\Macros;

/**
 * Get current object ID
 */
class Current_Id extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'current_id';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Current ID', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array(), $field_value = null ) {

		$object = $this->get_macros_object();

		if ( ! $object ) {
			return $field_value;
		}

		$class  = get_class( $object );
		$result = '';

		switch ( $class ) {
	
			case 'WP_Post':
				$result = $object->ID;
				break;

			case 'WP_Term':
				$result = $object->term_id;
				break;

			default:
				$result = apply_filters(
					'jet-engine/listings/macros/current-id',
					jet_engine()->listings->data->get_current_object_id( $object ),
					$object
				);
				break;

		}

		return $result;
	}
}