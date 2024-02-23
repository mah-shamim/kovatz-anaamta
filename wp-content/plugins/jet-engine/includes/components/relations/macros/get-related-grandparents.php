<?php
namespace Jet_Engine\Relations\Macros;

/**
 * Required methods:
 * macros_tag()  - here you need to set macros tag for JetEngine core
 * macros_name() - here you need to set human-readable macros name for different UIs where macros are available
 * macros_callback() - the main function of the macros. Returns the value
 * macros_args() - Optional, arguments list for the macros. Arguments format is the same ad for Elementor controls
 */
class Get_Related_Grandparents extends \Jet_Engine_Base_Macros {

	/**
	 * Returns macros tag
	 *
	 * @return string
	 */
	public function macros_tag() {
		return 'rel_get_grandparents';
	}

	/**
	 * Returns macros name
	 *
	 * @return string
	 */
	public function macros_name() {
		return __( 'Related Grandparents', 'jet-engine' );
	}

	/**
	 * Callback function to return macros value
	 *
	 * @return string
	 */
	public function macros_callback( $args = array() ) {

		$rel_id          = isset( $args['rel_id'] ) ? $args['rel_id'] : false;
		$rel_object_from = isset( $args['rel_object_from'] ) ? $args['rel_object_from'] : 'current_object';
		$rel_object_var  = isset( $args['rel_object_var'] ) ? $args['rel_object_var'] : '';

		if ( ! $rel_id || ! jet_engine()->relations->hierachy ) {
			return;
		}

		$object_id = false;

		if ( $rel_object_from ) {

			$object_id = jet_engine()->relations->sources->get_id_by_source( $rel_object_from, $rel_object_var );

			if ( ! $object_id ) {
				return false;
			}

		}

		$related_ids = $this->get_related_ids( $rel_id, $object_id );
		$related_ids = ! empty( $related_ids ) ? $related_ids : array( PHP_INT_MAX );

		return implode( ',', $related_ids );

	}

	/**
	 * Returns related IDs list
	 * @param  [type] $rel_id    [description]
	 * @param  [type] $object_id [description]
	 * @return [type]            [description]
	 */
	public function get_related_ids( $rel_id, $object_id ) {
		return jet_engine()->relations->hierachy->get_grandparents( $rel_id, $object_id );
	}

	/**
	 * Returns object option label
	 * @return [type] [description]
	 */
	public function object_option_label() {
		return __( 'Grandchild ID is', 'jet-engine' );
	}

	/**
	 * Optionally return custom macros attributes array
	 *
	 * @return array
	 */
	public function macros_args() {

		return array(
			'rel_id' => array(
				'label'   => __( 'Grandchild Relation', 'jet-engine' ),
				'type'    => 'select',
				'options' => function() {
					return jet_engine()->relations->get_relations_for_js( true, __( 'Select...', 'jet-engine' ) );
				},
				'default' => '',
			),
			'rel_object_from' => array(
				'label'   => $this->object_option_label(),
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
