<?php

namespace Jet_Engine\Bricks_Views\Helpers\Controls_Converter;

class Control_Checkbox extends Base {
	public function parse_callback_arguments( $args = [] ) {
		$required = [];

		if ( array_key_exists( 'condition', $args ) ) {
			$required = $this->parse_callback_argument_condition( $args['condition'] );
		}

		$args = wp_parse_args( $args, [
			'label'   => '',
			'default' => false,
		] );

		return array_merge(
			[
				'tab'     => 'content',
				'label'   => esc_html__( $args['label'], 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => filter_var( $args['default'], FILTER_VALIDATE_BOOLEAN ),
			],
			$required ? [ 'required' => $required ] : [],
		);
	}
}