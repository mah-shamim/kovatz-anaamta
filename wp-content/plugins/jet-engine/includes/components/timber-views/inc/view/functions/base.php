<?php
/**
 * Base function class
 */
namespace Jet_Engine\Timber_Views\View\Functions;

abstract class Base {
	
	abstract public function get_name();

	abstract public function get_label();

	abstract public function get_result( $args );

	public function get_args() {
		return [];
	}

}
