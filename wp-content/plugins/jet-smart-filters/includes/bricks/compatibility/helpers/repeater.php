<?php
namespace Jet_Engine\Bricks_Views\Helpers;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
class Repeater {

	private $controls = [];

	public function add_control( $name, $data ) {
		$this->controls[ $name ] = $data;
	}

	public function get_controls() {
		return $this->controls;
	}

}
