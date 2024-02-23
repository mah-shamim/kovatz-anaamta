<?php
namespace Jet_Engine\Relations\Macros;

/**
 * Required methods:
 * macros_tag()  - here you need to set macros tag for JetEngine core
 * macros_name() - here you need to set human-readable macros name for different UIs where macros are available
 * macros_callback() - the main function of the macros. Returns the value
 * macros_args() - Optional, arguments list for the macros. Arguments format is the same ad for Elementor controls
 */
class Get_Related_Siblings extends Get_Related_Items {

	/**
	 * Returns macros tag
	 *
	 * @return string
	 */
	public function macros_tag() {
		return 'rel_get_siblings';
	}

	/**
	 * Returns macros name
	 *
	 * @return string
	 */
	public function macros_name() {
		return __( 'Related Siblings', 'jet-engine' );
	}

	/**
	 * Callback function to return macros value
	 *
	 * @return string
	 */
	public function macros_callback( $args = array() ) {

		$rel_id          = isset( $args['rel_id'] ) ? $args['rel_id'] : false;
		$rel_object      = isset( $args['rel_object'] ) ? $args['rel_object'] : 'child_object';
		$rel_object_from = isset( $args['rel_object_from'] ) ? $args['rel_object_from'] : 'current_object';
		$rel_object_var  = isset( $args['rel_object_var'] ) ? $args['rel_object_var'] : '';

		if ( ! $rel_id ) {
			return;
		}

		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			return;
		}

		$object_id = false;

		if ( ! $rel_object_from ) {
			return false;
		}

		$object_id = jet_engine()->relations->sources->get_id_by_source( $rel_object_from, $rel_object_var );

		if ( ! $object_id ) {
			return false;
		}

		$ids = $relation->get_siblings( $object_id, $rel_object, 'ids' );
		$ids = ! empty( $ids ) ? $ids : array( 'not-found' );

		do_action( 'jet-engine/relations/macros/get-siblings', $relation, $ids, $this );

		return implode( ',', $ids );

	}

}
