<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Settings' ) ) {
	/**
	 * Define Jet_Smart_Filters_Settings class
	 */
	class Jet_Smart_Filters_Settings {

		public $key = 'jet-smart-filters-settings';

		public function get( $setting, $default = false ) {

			$current = get_option( $this->key, array() );

			return isset( $current[ $setting ] ) ? $current[ $setting ] : $default;
		}

		public function update( $setting, $value ) {

			$current = get_option( $this->key, array() );
			$current[$setting] = is_array( $value ) ? $value : esc_attr( $value );

			return update_option( $this->key, $current );
		}
	}
}
