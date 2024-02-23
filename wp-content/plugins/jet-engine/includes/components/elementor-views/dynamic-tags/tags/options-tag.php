<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_Options_Tag extends Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'jet-options-page';
	}

	public function get_title() {
		return __( 'Option', 'jet-engine' );
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

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'plain' );

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

	public function render() {

		$option_field = $this->get_settings( 'option_field' );

		if ( empty( $option_field ) ) {
			return;
		}

		$value = jet_engine()->listings->data->get_option( $option_field );

		if ( is_array( $value ) ) {
			echo jet_engine_render_checkbox_values( $value );
			return $value;
		}

		echo wp_kses_post( $value );
	}

}
