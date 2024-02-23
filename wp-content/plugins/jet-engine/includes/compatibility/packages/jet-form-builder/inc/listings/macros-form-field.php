<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Form_Builder\Listings;

/**
 * Required methods:
 * macros_tag()  - here you need to set macros tag for JetEngine core
 * macros_name() - here you need to set human-readable macros name for different UIs where macros are available
 * macros_callback() - the main function of the macros. Returns the value
 * macros_args() - Optional, arguments list for the macros. Arguments format is the same ad for Elementor controls
 */
class Macros_Form_Field extends \Jet_Engine_Base_Macros {

	/**
	 * Returns macros tag
	 *
	 * @return string
	 */
	public function macros_tag() {
		return 'jfb_record_field';
	}

	/**
	 * Returns macros name
	 *
	 * @return string
	 */
	public function macros_name() {
		return __( 'JetFormBuilder Record Field', 'jet-engine' );
	}

	/**
	 * Callback function to return macros value
	 *
	 * @return string
	 */
	public function macros_callback( $args = array() ) {

		$field_name = isset( $args['jfb_field_name'] ) ? $args['jfb_field_name'] : '';
		$object     = $this->get_macros_object();

		if ( ! $field_name ) {
			return;
		}

		return Manager::instance()->get_form_record_field( $field_name, $object );

	}

	/**
	 * Optionally return custom macros attributes array
	 *
	 * @return array
	 */
	public function macros_args() {

		return array(
			'jfb_field_name' => array(
				'label'       => __( 'Field Name', 'jet-engine' ),
				'type'        => 'text',
				'description' => __( 'Please enter form field name to show recorded value of this field. If field contains array of values, please add path to exact value. For example filed_name/0, field_name/1/nested_key, field_name/nested_key etc.', 'jet-engine' ),
			),
		);
	}

}
