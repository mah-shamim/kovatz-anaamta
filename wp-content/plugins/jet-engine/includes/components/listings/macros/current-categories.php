<?php
namespace Jet_Engine\Macros;

/**
 * Returns comma-separated categories list associated with current post.
 */
class Current_Categories extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'current_categories';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Current categories', 'jet-engine' );
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

		$cats = wp_get_post_categories( $object->ID, array( 'fields' => 'ids' ) );

		if ( empty( $cats ) ) {
			return '';
		}

		return implode( ',', $cats );
	}
}