<?php
namespace Jet_Engine\Macros;

/**
 * Return today timestamp.
 */
class Today extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'today';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Today', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {
		return strtotime( 'Today 00:00' );
	}
}