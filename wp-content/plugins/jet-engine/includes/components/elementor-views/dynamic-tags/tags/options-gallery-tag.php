<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_Options_Gallery_Tag extends Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-options-gallery';
	}

	public function get_title() {
		return __( 'Gallery from options', 'jet-engine' );
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

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'gallery' );

			$this->add_control(
				'option_field',
				array(
					'label'  => __( 'Option', 'jet-engine' ),
					'type'   => Elementor\Controls_Manager::SELECT,
					'groups' => $options_pages_select,
				)
			);

		}

	}

	public function get_value( array $options = array() ) {

		$option_field = $this->get_settings( 'option_field' );

		if ( empty( $option_field ) ) {
			return array();
		}

		$value = jet_engine()->listings->data->get_option( $option_field );

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

}
