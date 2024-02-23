<?php
namespace Jet_Engine\Macros;

/**
 * Returns queried term.
 */
class Queried_Term extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'queried_term';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Queried term', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$current_object = $this->get_macros_object();

		if ( $current_object && 'WP_Term' === get_class( $current_object ) ) {
			return $current_object->term_id;
		}

		$queried_object = get_queried_object();

		if ( $queried_object && 'WP_Term' === get_class( $queried_object ) ) {
			return $queried_object->term_id;
		} else {
			return null;
		}
	}
}