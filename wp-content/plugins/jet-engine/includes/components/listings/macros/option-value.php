<?php
namespace Jet_Engine\Macros;

/**
 * Returns option value.
 */
class Option_Value extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'option_value';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Option value', 'jet-engine' );
	}

	public function macros_args() {

		return array(
			'option' => array(
				'label'   => __( 'Option', 'jet-engine' ),
				'type'    => 'select',
				'groups'  => function() {
					$option_fields = jet_engine()->options_pages->get_options_for_select( 'plain' );
					unset( $option_fields[''] );
					return array_values( $option_fields );
				},
			),
			'custom_option' => array(
				'label'       => __( 'Custom option', 'jet-engine' ),
				'description' => __( 'Note: this field will override the Option value', 'jet-engine' ),
				'type'        => 'text',
				'default'     => '',
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array(), $field_value = null ) {

		$option        = ! empty( $args['option'] ) ? $args['option'] : false;
		$custom_option = ! empty( $args['custom_option'] ) ? $args['custom_option'] : false;

		if ( empty( $option ) && empty( $custom_option ) ) {
			return $field_value;
		}

		if ( ! empty( $custom_option ) ) {
			$value = get_option( $custom_option );
		} else {
			$value = jet_engine()->listings->data->get_option( $option );

			if ( is_array( $value ) ) {
				return jet_engine_render_checkbox_values( $value );
			}
		}

		return wp_kses_post( $value );
	}
}
