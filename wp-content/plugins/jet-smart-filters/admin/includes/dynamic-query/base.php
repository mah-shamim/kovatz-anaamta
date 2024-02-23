<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

if ( ! class_exists( 'Jet_Smart_Filters_Admin_Dynamic_Query_Base' ) ) {
	/**
	 * Jet Smart Filters Admin Dynamic Query Base class
	 */
	abstract class Jet_Smart_Filters_Admin_Dynamic_Query_Base {
		
		// returns query id
		abstract public function get_name();
		
		// returns query label for UI control
		abstract public function get_label();
		
		// return additional arguments if needed
		public function get_extra_args() {

			return false;
		}
		
		// return main and additiona arguments delimiter if needed
		public function get_delimiter() {

			return false;
		}
	}
}