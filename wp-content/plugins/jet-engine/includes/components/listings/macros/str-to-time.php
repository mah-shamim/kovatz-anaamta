<?php
namespace Jet_Engine\Macros;

/**
 * Return timestamp by string.
 */
class Str_To_Time extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'str_to_time';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'String to timestamp', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {
		return array(
			'str' => array(
				'label'   => __( 'String to convert', 'jet-engine' ),
				'type'    => 'text',
				'default' => '',
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$string = ! empty( $args['str'] ) ? $args['str'] : false;

		return strtotime( $string );
	}
}