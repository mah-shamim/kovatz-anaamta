<?php
/**
 * Base class for custom macros registration
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Base_Macros
 */
abstract class Jet_Engine_Base_Macros extends \Crocoblock\Base_Macros {

	public $args = null;

	/**
	 * Register macros
	 */
	public function __construct() {
		add_filter( 'jet-engine/listings/macros-list', array( $this, 'register_macros' ) );
	}

	/**
	 * Register macros callback
	 *
	 * @return array
	 */
	public function register_macros( $macros_list ) {

		$macros_data = array(
			'label' => $this->macros_name(),
			'cb'    => array( $this, '_macros_callback' ),
		);

		$args = $this->get_macros_args();

		if ( ! empty( $args ) ) {
			$macros_data['args'] = $args;
		}

		$macros_list[ $this->macros_tag() ] = $macros_data;

		return $macros_list;
	}

	/**
	 * Return current macros object
	 *
	 * @return object|null
	 */
	public function get_macros_object() {
		return jet_engine()->listings->macros->get_macros_object();
	}

}
