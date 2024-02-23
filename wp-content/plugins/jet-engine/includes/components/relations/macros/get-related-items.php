<?php
namespace Jet_Engine\Relations\Macros;

/**
 * Required methods:
 * macros_tag()  - here you need to set macros tag for JetEngine core
 * macros_name() - here you need to set human-readable macros name for different UIs where macros are available
 * macros_callback() - the main function of the macros. Returns the value
 * macros_args() - Optional, arguments list for the macros. Arguments format is the same ad for Elementor controls
 */
class Get_Related_Items extends \Jet_Engine_Base_Macros {

	/**
	 * Returns macros tag
	 *
	 * @return string
	 */
	public function macros_tag() {
		return 'rel_get_items';
	}

	/**
	 * Returns macros name
	 *
	 * @return string
	 */
	public function macros_name() {
		return __( 'Related Items', 'jet-engine' );
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

		if ( $rel_object_from ) {

			$object_id = jet_engine()->relations->sources->get_id_by_source( $rel_object_from, $rel_object_var );

			if ( ! $object_id ) {
				return false;
			}

		}

		$related_ids = array();

		switch ( $rel_object ) {
			case 'parent_object':
				$related_ids = $relation->get_parents( $object_id, 'ids' );
				break;

			default:
				$related_ids = $relation->get_children( $object_id, 'ids' );
				break;
		}

		$related_ids = ! empty( $related_ids ) ? $related_ids : array( PHP_INT_MAX );

		do_action( 'jet-engine/relations/macros/get-related', $relation, $related_ids, $this );

		return implode( ',', $related_ids );

	}

	/**
	 * Optionally return custom macros attributes array
	 *
	 * @return array
	 */
	public function macros_args() {

		return array(
			'rel_id' => array(
				'label'   => __( 'From Relation', 'jet-engine' ),
				'type'    => 'select',
				'options' => function() {
					return jet_engine()->relations->get_relations_for_js( true, __( 'Select...', 'jet-engine' ) );
				},
				'default' => '',
			),
			'rel_object' => array(
				'label'   => __( 'From Object (what to show)', 'jet-engine' ),
				'type'    => 'select',
				'options' => array(
					'parent_object' => __( 'Parent Object', 'jet-engine' ),
					'child_object'  => __( 'Child Object', 'jet-engine' ),
				),
				'default' => 'child_object',
			),
			'rel_object_from' => array(
				'label'   => __( 'Initial Object ID From (get initial ID here)', 'jet-engine' ),
				'type'    => 'select',
				'options' => array( jet_engine()->relations->sources, 'get_sources' ),
				'default' => 'current_object',
			),
			'rel_object_var' => array(
				'label'     => __( 'Variable Name', 'jet-engine' ),
				'type'      => 'text',
				'default'   => '',
				'condition' => array( 'rel_object_from' => array( 'query_var', 'object_var' ) ),
			),
		);
	}

}
