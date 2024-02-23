<?php


/**
 * Empty class of old settings systems.
 *
 * This is only to allow 3rd party extensions a notification that the system has changed. This will be completely removed in a future version.
 *
 * @since 2.0.0
 */
class SF_Settings_API {

	/**
	 * Get option
	 *
	 * @param string  $name The option name.
	 * @param boolean $default_value The default value.
	 * @return array|mixed|null
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	public function get_option( $name, $default_value = false ) {

		$mappings = wcv_get_settings_mapping();

		if ( array_key_exists( $name, $mappings ) ) {
			WC_Vendors::log( __FUNCTION__ . ' has been deprecated please replace WC_Vendors::$pv_options->get_option(\'' . $name . '\') with get_option(\'' . $mappings[ $name ] . '\')' );

		}

		return ( array_key_exists( $name, $mappings ) ) ? get_option( $mappings[ $name ], $default_value ) : null;
	}
}
