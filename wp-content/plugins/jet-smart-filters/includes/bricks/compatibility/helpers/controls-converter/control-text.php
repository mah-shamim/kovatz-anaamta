<?php

namespace Jet_Engine\Bricks_Views\Helpers\Controls_Converter;

class Control_Text extends Base {

	public function parse_callback_arguments( $args = [] ) {

		$required = [];

		if ( array_key_exists( 'condition', $args ) ) {
			$required = $this->parse_callback_argument_condition( $args['condition'] );
			unset( $args['condition'] );
		}

		return array_merge(
			[
				'tab'            => 'content',
				'hasDynamicData' => false,
			],
			$args,
			$required ? [ 'required' => $required ] : [],
		);
	}
}