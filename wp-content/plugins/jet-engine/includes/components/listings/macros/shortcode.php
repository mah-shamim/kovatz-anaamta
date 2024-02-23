<?php
namespace Jet_Engine\Macros;

/**
 * Return timestamp by string.
 */
class Shortcode extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'shortcode_result';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Shortcode result', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {
		return array(
			'shortcode' => array(
				'label'   => __( 'Shortcode', 'jet-engine' ),
				'type'    => 'textarea',
				'default' => '',
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {
		$shortcode = ! empty( $args['shortcode'] ) ? $args['shortcode'] : '';
		return do_shortcode( $shortcode );

	}

}
