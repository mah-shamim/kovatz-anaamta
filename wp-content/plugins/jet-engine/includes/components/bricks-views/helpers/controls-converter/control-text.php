<?php

namespace Jet_Engine\Bricks_Views\Helpers\Controls_Converter;

class Control_Text extends Base {

	public function parse_callback_arguments( $args = [] ) {

		$required = [];
		$dynamic  = false;

		if ( array_key_exists( 'condition', $args ) ) {
			$required = $this->parse_callback_argument_condition( $args['condition'] );
			unset( $args['condition'] );
		}

		if ( isset( $args['dynamic'] ) && isset( $args['dynamic']['active'] ) ) {
			$dynamic = filter_var( $args['dynamic']['active'], FILTER_VALIDATE_BOOLEAN );
			unset( $args['dynamic'] );
		}

		return array_merge(
			[
				'tab'            => 'content',
				'hasDynamicData' => $dynamic,
			],
			$args,
			$required ? [ 'required' => $required ] : [],
		);
	}
}