<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_Object_Property_Image_Tag extends Jet_Engine_Object_Property_Tag {

	public function get_name() {
		return 'jet-object-property-image';
	}

	public function get_title() {
		return __( 'Current Object Image Field', 'jet-engine' );
	}

	public function get_categories() {
		return array(
			Jet_Engine_Dynamic_Tags_Module::IMAGE_CATEGORY,
		);
	}

	protected function register_controls() {
		parent::register_controls();

		$this->add_control(
			'_fallback',
			array(
				'label' => __( 'Fallback', 'jet-engine' ),
				'type'  => Elementor\Controls_Manager::MEDIA,
			)
		);
	}

	public function get_object_fields() {
		// Object fields without Post, Term, User, Comment instance fields.
		return apply_filters( 'jet-engine/listing/data/object-fields-groups', array() );
	}

	protected function register_advanced_section() {}

	public function get_value( array $options = array() ) {

		$value = parent::get_value( $options );

		if ( empty( $value ) ) {
			return $this->get_settings( '_fallback' );
		}

		$value = Jet_Engine_Tools::get_attachment_image_data_array( $value );

		if ( empty( $value ) || empty( $value['url'] ) ) {
			return $this->get_settings( '_fallback' );
		}

		return $value;
	}

}
