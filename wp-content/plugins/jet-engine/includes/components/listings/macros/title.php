<?php
namespace Jet_Engine\Macros;

/**
 * Get current object title
 */
class Title extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'title';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Title', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$object = $this->get_macros_object();

		if ( ! $object ) {
			return '';
		}

		return jet_engine()->listings->data->get_current_object_title( $object );

	}
}