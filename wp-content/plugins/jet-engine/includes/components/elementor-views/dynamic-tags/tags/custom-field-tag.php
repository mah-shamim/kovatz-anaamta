<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_Custom_Field_Tag extends Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'jet-post-custom-field';
	}

	public function get_title() {
		return __( 'Custom Field', 'jet-engine' );
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
			'meta_field',
			array(
				'label'  => __( 'Field', 'jet-engine' ),
				'type'   => Elementor\Controls_Manager::SELECT,
				'groups' => $this->get_meta_fields(),
			)
		);
	}

	public function render() {

		$meta_field = $this->get_settings( 'meta_field' );

		if ( empty( $meta_field ) ) {
			return;
		}

		$value = jet_engine()->listings->data->get_meta( $meta_field );

		if ( is_array( $value ) ) {
			echo jet_engine_render_checkbox_values( $value );
			return $value;
		}

		echo wp_kses_post( $value );

	}

	private function get_meta_fields() {

		if ( jet_engine()->meta_boxes ) {
			return jet_engine()->meta_boxes->get_fields_for_select( 'plain' );
		} else {
			return array();
		}

	}
}
