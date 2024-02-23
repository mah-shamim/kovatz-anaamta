<?php
namespace Jet_Engine\Bricks_Views\Helpers;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Make callback for Elementor widgets hooks compatible with Bricks
 */
class Controls_Hook_Bridge {

	protected $element;
	protected $exclude;

	public function __construct( $el, $exclude = [] ) {
		$this->element = $el;
		$this->exclude = $exclude;
	}

	public function do_action( $tag ) {
		do_action( $tag, $this );
	}

	public function add_control( $name, $args = [] ) {

		if ( ! in_array( $name, $this->exclude ) ) {
			$args = Options_Converter::convert( $args );
			$this->element->register_jet_control( $name, $args );
		}

	}

}
