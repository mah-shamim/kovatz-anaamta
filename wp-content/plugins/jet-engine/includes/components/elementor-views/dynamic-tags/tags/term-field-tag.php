<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_Term_Field_Tag extends Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'jet-term-field';
	}

	public function get_title() {
		return __( 'Term Field', 'jet-engine' );
	}

	public function get_group() {
		return Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
			Jet_Engine_Dynamic_Tags_Module::NUMBER_CATEGORY,
			Jet_Engine_Dynamic_Tags_Module::URL_CATEGORY,
			Jet_Engine_Dynamic_Tags_Module::POST_META_CATEGORY,
			Jet_Engine_Dynamic_Tags_Module::COLOR_CATEGORY
		);
	}

	public function is_settings_required() {
		return true;
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
			'term_field',
			array(
				'label'   => __( 'Field', 'jet-engine' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'name'        => __( 'Term name', 'jet-engine' ),
					'description' => __( 'Term description', 'jet-engine' ),
					'count'       => __( 'Posts count', 'jet-engine' ),
					'term_id'     => __( 'Term ID', 'jet-engine' ),
					'term_url'    => __( 'Term URL', 'jet-engine' ),
					'meta_field'  => __( 'Meta field', 'jet-engine' ),
				),
			)
		);

		$this->add_control(
			'meta_field',
			array(
				'label'     => __( 'Meta Field', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => array(
					'term_field' => array( 'meta_field' ),
				),
			)
		);

	}

	public function render() {

		$tax        = $this->get_settings( 'taxonomy' );
		$field      = $this->get_settings( 'term_field' );
		$meta_field = $this->get_settings( 'meta_field' );

		if ( empty( $tax ) ) {
			return;
		}

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! $current_object ) {
			$this->render_post_term( get_the_ID(), $tax, $field, $meta_field );
			return;
		}

		$class = get_class( $current_object );

		if ( 'WP_Term' === $class ) {
			$this->render_term_data( $current_object, $tax, $field, $meta_field );
		} else {
			$this->render_post_term( get_the_ID(), $tax, $field, $meta_field );
		}

	}

	public function render_post_term( $post_id, $tax, $field, $meta_field ) {

		if ( ! $post_id ) {
			return;
		}

		$post_terms = wp_get_post_terms( $post_id, $tax );

		if ( is_wp_error( $post_terms ) || empty( $post_terms ) ) {
			return;
		}

		$term = $post_terms[0];

		$this->render_term_data( $term, $tax, $field, $meta_field );

	}

	public function render_term_data( $term, $tax, $field, $meta_field ) {

		switch ( $field ) {

			case 'meta_field':

				if ( !  empty( $meta_field ) ) {
					$meta = get_term_meta( $term->term_id, $meta_field, true );

					if ( is_array( $meta ) ) {
						echo implode( ', ', $meta );
					} else {
						echo $meta;
					}

				}

				break;

			case 'term_url':

				$term_url = get_term_link( $term->term_id, $tax );

				if ( is_wp_error( $term_url ) ) {
					$term_url = '';
				}

				echo $term_url;
				break;

			default:

				if ( isset( $term->$field ) ) {
					echo $term->$field;
				}

				break;
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
