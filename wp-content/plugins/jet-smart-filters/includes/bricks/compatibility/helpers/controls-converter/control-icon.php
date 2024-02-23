<?php

namespace Jet_Engine\Bricks_Views\Helpers\Controls_Converter;

class Control_Icon extends Control_Default {

	public function parse_callback_arguments( $args = [] ) {
		$args['type'] = 'icon';
		return parent::parse_callback_arguments( $args );
	}
}