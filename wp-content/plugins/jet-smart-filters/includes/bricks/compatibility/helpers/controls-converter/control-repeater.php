<?php

namespace Jet_Engine\Bricks_Views\Helpers\Controls_Converter;

class Control_Repeater extends Base {

	public function parse_callback_arguments( $args = [] ) {
		$required = [];

		if ( array_key_exists( 'fields', $args ) && is_array( $args['fields'] ) ) {
			$args['fields'] = $this->parse_callback_argument_repeater_fields( $args['fields'] );
		}

		if ( array_key_exists( 'condition', $args ) ) {
			$required = $this->parse_callback_argument_condition( $args['condition'] );
			unset( $args['condition'] );
		}

		return array_merge(
			[ 'tab' => 'content' ],
			$args,
			$required ? [ 'required' => $required ] : [],
		);
	}

	public function parse_callback_argument_repeater_fields( $fields = [] ) {

		$prepared_fields = [];

		foreach ( $fields as $field_name => $field_data ) {
			$prepared_fields[ $field_name ] = \Jet_Engine\Bricks_Views\Helpers\Options_Converter::convert( $field_data );
		}

		return $prepared_fields;
	}
}