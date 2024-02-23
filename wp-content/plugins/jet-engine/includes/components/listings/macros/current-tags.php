<?php
namespace Jet_Engine\Macros;

/**
 * Returns comma-separated tags list associated with current post.
 */
class Current_Tags extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'current_tags';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Current tags', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$object = $this->get_macros_object();
		$class  = get_class( $object );

		if ( 'WP_Post' !== $class ) {
			return '';
		}

		$tags = wp_get_post_tags( $object->ID, array( 'fields' => 'ids' ) );

		if ( empty( $tags ) ) {
			return '';
		}

		return implode( ',', $tags );
	}
}