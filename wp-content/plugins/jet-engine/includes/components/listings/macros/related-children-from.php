<?php
namespace Jet_Engine\Macros;

/**
 * Returns related children post IDs.
 */
class Related_Children_From extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'related_children_from';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Related children from (legacy)', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {
		return array(
			'post_type' => array(
				'label'   => __( 'Post type', 'jet-engine' ),
				'type'    => 'select',
				'options' => function() {
					return jet_engine()->listings->get_post_types_for_options();
				}
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$post_type = ! empty( $args['post_type'] ) ? $args['post_type'] : false;

		$posts = jet_engine()->relations->legacy->get_related_posts( array(
			'post_type_1' => get_post_type(),
			'post_type_2' => $post_type,
			'from'        => $post_type,
		) );

		if ( empty( $posts ) ) {
			return 'not-found';
		}

		if ( is_array( $posts ) ) {
			return implode( ',', $posts );
		} else {
			return $posts;
		}
	}
}
