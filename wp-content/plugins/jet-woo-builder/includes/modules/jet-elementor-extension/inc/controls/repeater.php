<?php

namespace Jet_Elementor_Extension;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Repeater_Control extends \Elementor\Control_Repeater {

	public function get_type() {
		return 'jet-repeater';
	}
}
