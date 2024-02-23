<?php
namespace Jet_Engine\Macros;

/**
 * Returns comma-separated terms list of passed taxonomy associated with current post.
 */
class Current_Terms extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'current_terms';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Current terms', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {
		return array(
			'taxonomy' => array(
				'label'   => __( 'Taxonomy', 'jet-engine' ),
				'type'    => 'select',
				'options' => function() {
					return jet_engine()->listings->get_taxonomies_for_options();
				},
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array(), $field_value = null ) {

		$taxonomy = ! empty( $args['taxonomy'] ) ? $args['taxonomy'] : null;

		if ( ! $taxonomy && ! empty( $field_value ) ) {
			$taxonomy = $field_value;
		}

		if ( ! $taxonomy ) {
			return '';
		}

		$object = $this->get_macros_object();

		if ( ! $object || ! is_object( $object ) ) {
			return '';
		}

		$class  = get_class( $object );

		if ( 'WP_Post' !== $class ) {
			return '';
		}

		$terms = wp_get_post_terms( $object->ID, $taxonomy, array( 'fields' => 'ids' ) );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return '';
		}

		if ( is_array( $terms ) && 1 === count( $terms ) ) {
			return absint( $terms[0] );
		}

		return implode( ',', $terms );
	}
}
