<?php
namespace Jet_Engine\Macros;

/**
 * Returns values of passed mata key for current post/term.
 */
class Current_Meta extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'current_meta';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Current meta value', 'jet-engine' );
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

		return jet_engine()->listings->macros->get_current_meta( $field_value, $meta_key );
	}
}