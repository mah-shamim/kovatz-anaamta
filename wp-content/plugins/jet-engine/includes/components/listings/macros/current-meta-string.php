<?php
namespace Jet_Engine\Macros;

/**
 * Returns current meta value. For arrays implode it to coma separated string.
 */
class Current_Meta_String extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'current_meta_string';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Current meta value as string', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {
		return array(
			'meta_key' => array(
				'label'   => __( 'Meta field', 'jet-engine' ),
				'type'    => 'text',
				'default' => '',
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array(), $field_value = null ) {

		$meta_key = ! empty( $args['meta_key'] ) ? $args['meta_key'] : null;
		$meta     = jet_engine()->listings->macros->get_current_meta( $field_value, $meta_key );

		return is_array( $meta ) ? implode( ', ', $meta ) : $meta;
	}
}