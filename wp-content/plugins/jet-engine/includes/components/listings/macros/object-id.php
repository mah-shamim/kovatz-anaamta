<?php
namespace Jet_Engine\Macros;

/**
 * Returns current object ID for current context
 */
class Object_Id extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'object_id';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Object ID (for current context)', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {
		return jet_engine()->listings->data->get_current_object_id( $this->get_macros_object() );
	}
}