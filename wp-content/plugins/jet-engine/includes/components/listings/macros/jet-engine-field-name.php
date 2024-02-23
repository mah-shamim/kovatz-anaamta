<?php
namespace Jet_Engine\Macros;

/**
 * Returns field name passed as second argument.
 * This macros is need to select JetEngine meta fields visually for the macros editors (dynamic tags, Query builder etc.)
 */
class Jet_Engine_Field_Name extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'jet_engine_field_name';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'JetEngine meta field', 'jet-engine' );
	}

	public function macros_args() {

		return array(
			'meta_field' => array(
				'label'   => __( 'Field', 'jet-engine' ),
				'type'    => 'select',
				'groups'  => function() {
					$meta_fields = jet_engine()->meta_boxes->get_fields_for_select( 'plain' );
					unset( $meta_fields[''] );
					return array_values( $meta_fields );
				},
			),
			'return' => array(
				'label'   => __( 'Return', 'jet-engine' ),
				'type'    => 'select',
				'options' => array(
					'field_name'  => __( 'Field name/key/ID', 'jet-engine' ),
					'field_value' => __( 'Field value', 'jet-engine' ),
				),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$field_name = ! empty( $args['meta_field'] ) ? $args['meta_field'] : null;
		$return     = ! empty( $args['return'] ) ? $args['return'] : 'field_name';

		if ( 'field_value' === $return ) {
			return jet_engine()->listings->macros->get_current_meta( $field_name, $field_name );
		} else {
			return $field_name;
		}
	}
}
