<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_Custom_Gallery_Tag extends Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-post-custom-gallery';
	}

	public function get_title() {
		return __( 'JetEngine Gallery', 'jet-engine' );
	}

	public function get_group() {
		return Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			Jet_Engine_Dynamic_Tags_Module::GALLERY_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'gallery_field',
			array(
				'label'  => __( 'Field', 'jet-engine' ),
				'type'   => Elementor\Controls_Manager::SELECT,
				'groups' => $this->get_meta_fields(),
			)
		);

	}

	public function get_value( array $options = array() ) {

		$meta_field = $this->get_settings( 'gallery_field' );

		if ( empty( $meta_field ) ) {
			return array();
		}

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! $current_object ) {
			return array();
		}

		$value = jet_engine()->listings->data->get_meta( $meta_field );

		if ( empty( $value ) ) {
			return array();
		}

		if ( is_array( $value ) ) {
			$value = $value;
		} else {
			$value = explode( ',', $value );
		}

		return array_map( function( $item ) {
			return Jet_Engine_Tools::get_attachment_image_data_array( $item, 'id' );
		}, $value );

	}

	private function get_meta_fields() {

		if ( jet_engine()->meta_boxes ) {
			return jet_engine()->meta_boxes->get_fields_for_select( 'gallery' );
		} else {
			return array();
		}

	}
}
