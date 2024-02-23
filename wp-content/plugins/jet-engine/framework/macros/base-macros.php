<?php
/**
 * Base macros module
 *
 * Version: 1.0.0
 */
namespace Crocoblock;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( '\Crocoblock\Base_Macros' ) ) {

	/**
	 * Define Base_Macros
	 */
	abstract class Base_Macros {

		public $args = null;

		/**
		 * Returns macros tag
		 *
		 * @return string
		 */
		abstract public function macros_tag();

		/**
		 * Returns macros name
		 *
		 * @return string
		 */
		abstract public function macros_name();

		/**
		 * Callback function to return macros value
		 *
		 * @return string
		 */
		abstract public function macros_callback( $args = array() );

		/**
		 * Wrapper for callback function to explode arguments
		 *
		 * @param null $field_value
		 * @param null $raw_args
		 *
		 * @return string
		 */
		public function _macros_callback( $field_value = null, $raw_args = null ) {

			$custom_args = $this->get_macros_args();
			$args        = array();

			if ( ! empty( $custom_args ) ) {

				$raw_args = explode( '|', $raw_args );
				$i        = 0;

				foreach ( $custom_args as $key => $value ) {
					$default      = isset( $value['default'] ) ? $value['default'] : null;
					$args[ $key ] = isset( $raw_args[ $i ] ) ? $raw_args[ $i ] : $default;
					$i++;
				}

			}

			return call_user_func( array( $this, 'macros_callback' ), $args,  $field_value );
		}

		/**
		 * Optionally return custom macros attributes array
		 *
		 * @return array
		 */
		public function macros_args() {
			return [];
		}

		/**
		 * Returns registered macros arguments list
		 *
		 * @return array
		 */
		public function get_macros_args() {

			if ( null === $this->args ) {
				$this->args = $this->macros_args();
			}

			return $this->args;
		}

	}

}
