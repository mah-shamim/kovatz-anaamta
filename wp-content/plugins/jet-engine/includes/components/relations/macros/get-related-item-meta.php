<?php
namespace Jet_Engine\Relations\Macros;

/**
 * Required methods:
 * macros_tag()  - here you need to set macros tag for JetEngine core
 * macros_name() - here you need to set human-readable macros name for different UIs where macros are available
 * macros_callback() - the main function of the macros. Returns the value
 * macros_args() - Optional, arguments list for the macros. Arguments format is the same ad for Elementor controls
 */
class Get_Related_Item_Meta extends \Jet_Engine_Base_Macros {

	/**
	 * Returns macros tag
	 *
	 * @return string
	 */
	public function macros_tag() {
		return 'rel_get_item_meta';
	}

	/**
	 * Returns macros name
	 *
	 * @return string
	 */
	public function macros_name() {
		return __( 'Related Item Meta', 'jet-engine' );
	}

	/**
	 * Callback function to return macros value
	 *
	 * @return string
	 */
	public function macros_callback( $args = array() ) {

		$meta_field      = ! empty( $args['rel_meta_key'] ) ? $args['rel_meta_key'] : false;
		$rel_parent_from = ! empty( $args['rel_parent_from'] ) ? $args['rel_parent_from'] : 'current_object';
		$rel_parent_var  = ! empty( $args['rel_parent_var'] ) ? $args['rel_parent_var'] : false;
		$rel_child_from  = ! empty( $args['rel_child_from'] ) ? $args['rel_child_from'] : 'current_object';
		$rel_child_var   = ! empty( $args['rel_child_var'] ) ? $args['rel_child_var'] : false;

		if ( ! $meta_field ) {
			return '';
		}

		$meta_field = explode( '::', $meta_field );

		$rel_id = $meta_field[0];
		$key    = $meta_field[1];

		if ( ! $rel_id ) {
			return '';
		}

		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			return '';
		}

		$parent_id = false;
		$child_id  = false;

		if ( $rel_parent_from ) {

			$parent_id = jet_engine()->relations->sources->get_id_by_source( $rel_parent_from, $rel_parent_var );

			if ( ! $parent_id ) {
				return '';
			}

		}

		if ( $rel_child_from ) {

			$child_id = jet_engine()->relations->sources->get_id_by_source( $rel_child_from, $rel_child_var );

			if ( ! $child_id ) {
				return '';
			}

		}

		$meta = $relation->get_meta( $parent_id, $child_id, $key );

		return ! empty( $meta ) ? $meta : '';

	}

	/**
	 * Optionally return custom macros attributes array
	 *
	 * @return array
	 */
	public function macros_args() {

		return array(
			'rel_meta_key' => array(
				'label'   => __( 'Meta Field', 'jet-engine' ),
				'type'    => 'select',
				'options' => function() {

					$raw_fields  = jet_engine()->relations->get_active_relations_meta_fields();
					$meta_fields = array( '' => __( 'Select...', 'jet-engine' ) );

					if ( empty( $raw_fields ) ) {
						return $meta_fields;
					}

					foreach ( $raw_fields as $rel_id => $rel_data ) {

						foreach ( $rel_data['fields'] as $field ) {

							if ( ! empty( $type ) && ! in_array( $field['type'], $type ) ) {
								continue;
							}

							$key = $rel_id . '::' . $field['name'];
							$meta_fields[ $key ] = $field['title'];

						}

					}

					return $meta_fields;
				},
				'default' => '',
			),
			'rel_parent_from' => array(
				'label'   => __( 'Parent Object ID From', 'jet-engine' ),
				'type'    => 'select',
				'options' => array( jet_engine()->relations->sources, 'get_sources' ),
				'default' => 'current_object',
			),
			'rel_parent_var' => array(
				'label'     => __( 'Parent Object Variable Name', 'jet-engine' ),
				'type'      => 'text',
				'default'   => '',
				'condition' => array( 'rel_parent_from' => array( 'query_var', 'object_var' ) ),
			),
			'rel_child_from' => array(
				'label'   => __( 'Child Object ID From', 'jet-engine' ),
				'type'    => 'select',
				'options' => array( jet_engine()->relations->sources, 'get_sources' ),
				'default' => 'current_object',
			),
			'rel_child_var' => array(
				'label'     => __( 'Child Object Variable Name', 'jet-engine' ),
				'type'      => 'text',
				'default'   => '',
				'condition' => array( 'rel_child_from' => array( 'query_var', 'object_var' ) ),
			),
		);
	}

}
