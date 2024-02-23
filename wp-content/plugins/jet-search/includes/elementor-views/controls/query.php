<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Search_Control_Query extends Elementor\Control_Select2 {

	public function get_type() {
		return 'jet-search-query';
	}
}
