<?php
namespace Jet_Engine\Relations;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Sources manager.
 * Keep information about all sources where we can get initial IDs for relation objects
 */
class Sources {

	/**
	 * Add available sources list
	 * @return [type] [description]
	 */
	public function get_sources() {
		return apply_filters( 'jet-engine/relations/sources-list', array(
			'current_object' => __( 'Current Object ID', 'jet-engine' ),
			'current_user'   => __( 'Current User ID', 'jet-engine' ),
			'queried_user'   => __( 'Queried User ID', 'jet-engine' ),
			'query_var'      => __( 'Query Variable', 'jet-engine' ),
			'object_var'     => __( 'Current Object Variable', 'jet-engine' ),
			'wp_object'      => __( 'Default WordPress Object (for current page)', 'jet-engine' ),
		) );
	}

	/**
	 * Get object ID by source
	 *
	 * @param  string $source [description]
	 * @param  string $var    [description]
	 * @return [type]         [description]
	 */
	public function get_id_by_source( $source = '', $var = '' ) {

		$object_id = false;

		if ( ! $source ) {
			$source = 'current_object';
		}

		switch ( $source ) {

			case 'current_object':

				$object_id = jet_engine()->listings->data->get_current_object_id();
				break;

			case 'current_user':
				$object_id = get_current_user_id();
				break;

			case 'queried_user':

				$user = jet_engine()->listings->data->get_queried_user_object();

				if ( $user ) {
					$object_id = $user->ID;
				}

				break;

			case 'query_var':

				if ( $var ) {
					if ( ! empty( $_REQUEST[ $var ] ) ) {
						$object_id = $_REQUEST[ $var ];
					} else {
						$object_id = get_query_var( $var );
					}
				}

				break;

			case 'object_var':

				$object = jet_engine()->listings->data->get_current_object();

				if ( $object && isset( $object->$var ) ) {
					$object_id = $object->$var;
				}

				break;

			case 'wp_object':
				$object_id = jet_engine()->listings->data->get_current_object_id(
					jet_engine()->listings->objects_stack->get_root_object()
				);
				break;

			default:
			
				$object_id = apply_filters( 'jet-engine/relations/object-id-by-source/' . $source, false, $var );
				break;
		}

		return $object_id;
	}

	/**
	 * Returns object of given type from stack
	 *
	 * @param  [type] $object_type [description]
	 * @return [type]              [description]
	 */
	public function get_object_from_stack( $type ) {

		$type_data     = jet_engine()->relations->types_helper->type_parts_by_name( $type );
		$type_instance = jet_engine()->relations->types_helper->get_instances( $type_data[0] );

		if ( ! $type_instance ) {
			return false;
		}

		$stack = jet_engine()->listings->objects_stack->get_full_stack();

		if ( empty( $stack ) ) {
			return false;
		}

		foreach ( array_reverse( $stack ) as $object ) {
			if ( $type_instance->is_object_of_type( $object, $type_data[1] ) ) {
				return $object;
			}
		}

		return false;

	}

	/**
	 * Returns obhject of given type by item ID of this object
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_source_object_by_id( $type, $item_id ) {

		$type_data     = jet_engine()->relations->types_helper->type_parts_by_name( $type );
		$type_instance = jet_engine()->relations->types_helper->get_instances( $type_data[0] );

		if ( ! $type_instance ) {
			return false;
		}

		return $type_instance->get_object_by_id( $item_id, $type_data[1] );

	}

}
