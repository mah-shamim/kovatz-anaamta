<?php

namespace Jet_Engine\Bricks_Views\Helpers\Controls_Converter;

class Base {

	public function parse_callback_arguments( $args = [] ) {
		return $args;
	}

	public function parse_callback_argument_condition( $args = [] ) {
		$result = [];

		foreach ( $args as $key => $value ) {
			$condition       = '=';
			$key_last_symbol = $key[ - 1 ];

			if ( $key_last_symbol === '!' ) {
				$condition = $key_last_symbol . $condition;
				$key       = rtrim( $key, $key_last_symbol );
			}

			if ( $value === 'yes' ) {
				$value = true;
			}

			$result[] = [ $key, $condition, $value ];
		}

		return $result;
	}

	public function parse_callback_argument_selectors( $args = [] ) {

		$result = [];

		foreach ( $args as $key => $value ) {

			$property = substr( $value, 0, strrpos( $value, ':' ) );
			$selector = substr( $key, strrpos( $key, '.' ) );

			$result[] = [
				'property' => $property,
				'selector' => $selector,
			];
		}

		return $result;
	}
}