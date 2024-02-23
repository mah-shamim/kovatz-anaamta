<?php
namespace Jet_Engine\Macros;

/**
 * Returns related children post IDs.
 */
class Related_Children_Between extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'related_children_between';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Related children between (legacy)', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {
		return array(
			'post_type_1' => array(
				'label'   => __( 'Post type 1', 'jet-engine' ),
				'type'    => 'select',
				'options' => function() {
					return jet_engine()->listings->get_post_types_for_options();
				},
			),
			'post_type_2' => array(
				'label'   => __( 'Post type 2', 'jet-engine' ),
				'type'    => 'select',
				'options' => function() {
					return jet_engine()->listings->get_post_types_for_options();
				},
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$post_type_1 = ! empty( $args['post_type_1'] ) ? $args['post_type_1'] : false;
		$post_type_2 = ! empty( $args['post_type_2'] ) ? $args['post_type_2'] : false;

		$posts = jet_engine()->relations->legacy->get_related_posts( array(
			'post_type_1' => $post_type_1,
			'post_type_2' => $post_type_2,
			'from'        => $post_type_2,
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
