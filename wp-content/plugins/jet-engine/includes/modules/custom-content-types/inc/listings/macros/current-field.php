<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Listings\Macros;

use Jet_Engine\Modules\Custom_Content_Types\Module;

/**
 * Returns requested CCT field value from current CCT object.
 */
class Current_Field extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'current_field';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Current CCT field', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {

		$groups = array();

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

			$fields = $instance->get_fields_list();
			$prefixed_fields = array();

			foreach ( $fields as $key => $label ) {
				$prefixed_fields[ $type . '__' . $key ] = $label;
			}

			$groups[] = array(
				'label'   => __( 'Content Type:', 'jet-engine' ) . ' ' . $instance->get_arg( 'name' ),
				'options' => $prefixed_fields,
			);

		}

		return array(
			'field_name' => array(
				'label'  => __( 'Field', 'jet-engine' ),
				'type'   => 'select',
				'groups' => $groups,
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$field_name     = ! empty( $args['field_name'] ) ? $args['field_name'] : '_ID';
		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! isset( $current_object->cct_slug ) ) {
			return null;
		}

		if ( ! $field_name ) {
			$field_name = '_ID';
		}

		return jet_engine()->listings->data->get_prop( $field_name );
	}
}