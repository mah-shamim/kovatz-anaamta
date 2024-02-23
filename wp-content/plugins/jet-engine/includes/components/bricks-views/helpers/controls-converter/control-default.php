<?php

namespace Jet_Engine\Bricks_Views\Helpers\Controls_Converter;

class Control_Default extends Base {

	public function parse_callback_arguments( $args = [] ) {

		$css      = [];
		$required = [];

		if ( array_key_exists( 'selectors', $args ) ) {
			$css = $this->parse_callback_argument_selectors( $args['selectors'] );
			unset( $args['selectors'] );
		}

		if ( array_key_exists( 'condition', $args ) ) {
			$required = $this->parse_callback_argument_condition( $args['condition'] );
			unset( $args['condition'] );
		}

		return array_merge(
			$args,
			$css ? [ 'css' => $css ] : [],
			$required ? [ 'required' => $required ] : [],
		);
	}
}