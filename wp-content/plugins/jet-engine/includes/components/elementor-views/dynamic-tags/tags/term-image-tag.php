<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_Term_Image_Tag extends Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-term-image';
	}

	public function get_title() {
		return __( 'Term Image', 'jet-engine' );
	}

	public function get_group() {
		return Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			Jet_Engine_Dynamic_Tags_Module::IMAGE_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'taxonomy',
			array(
				'label'   => __( 'Taxonomy', 'jet-engine' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_taxonomies_list(),
			)
		);

		$this->add_control(
			'meta_field',
			array(
				'label' => __( 'Meta Field', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'fallback',
			array(
				'label' => __( 'Fallback', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

	}

	public function get_value( array $options = array() ) {

		$tax        = $this->get_settings( 'taxonomy' );
		$meta_field = $this->get_settings( 'meta_field' );

		if ( empty( $tax ) || empty( $meta_field ) ) {
			return $this->get_settings( 'fallback' );
		}

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! $current_object ) {
			return $this->get_post_term_data( get_the_ID(), $tax, $meta_field );
		}

		$class = get_class( $current_object );

		if ( 'WP_Term' === $class ) {
			return $this->get_term_data( $current_object, $meta_field );
		} else {
			return $this->get_post_term_data( get_the_ID(), $tax, $meta_field );
		}

	}

	public function get_post_term_data( $post_id, $tax, $meta_field ) {

		if ( ! $post_id ) {
			return $this->get_settings( 'fallback' );
		}

		$post_terms = wp_get_post_terms( $post_id, $tax );

		if ( is_wp_error( $post_terms ) || empty( $post_terms ) ) {
			return $this->get_settings( 'fallback' );
		}

		$term = $post_terms[0];

		return $this->get_term_data( $term, $meta_field );

	}

	public function get_term_data( $term, $meta_field ) {

		if ( ! empty( $meta_field ) ) {

			$meta = get_term_meta( $term->term_id, $meta_field, true );

			if ( $meta ) {
				return Jet_Engine_Tools::get_attachment_image_data_array( $meta );
			} else {
				return $this->get_settings( 'fallback' );
			}

		} else {
			return $this->get_settings( 'fallback' );
		}

	}

	private function get_taxonomies_list() {

		$taxonomies = Jet_Engine_Tools::get_taxonomies_for_js();
		$result     = array();

		foreach ( $taxonomies as $tax ) {
			$result[ $tax['value'] ] = $tax['label'];
		}

		return $result;

	}

}
