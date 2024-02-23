<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_Dynamic_Function_Tag extends Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'jet-dynamic-function';
	}

	public function get_title() {
		return __( 'Dynamic Function', 'jet-engine' );
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
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
		jet_engine()->dynamic_functions->register_custom_settings( $this );

		$this->add_control(
			'dynamic_function_notice',
			array(
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => '<a target="_blank" href="https://www.youtube.com/watch?v=giyHGJNckoM">' . esc_html__( 'What is Dynamic functions and how it works?', 'jet-engine' ) . '</a>'
			)
		);
	}

	public function render() {

		$function_name = $this->get_settings( 'function_name' );
		$source   = $this->get_settings( 'data_source' );
		$field_name    = $this->get_settings( 'field_name' );

		if ( ! $source ) {
			$source = 'post_meta';
		}

		$data_source = array( 'source' => $source );

		if ( empty( $function_name ) ) {
			return;
		}

		if ( 'post_meta' === $source ) {
			$data_context = $this->get_settings( 'data_context' );
			$data_context_tax = $this->get_settings( 'data_context_tax' );

			$data_source['context']          = $data_context ? $data_context : 'all_posts';
			$data_source['context_tax']      = $data_context_tax;
			$data_source['context_tax_term'] = $this->get_settings( 'data_context_tax_term' );
			$data_source['context_user_id']  = $this->get_settings( 'data_context_user_id' );
			$data_source['context_relation'] = $this->get_settings( 'data_context_relation' );
			$data_source['post_status']      = $this->get_settings( 'data_post_status' );
			$data_source['post_types']       = $this->get_settings( 'data_post_types' );
		}

		$custom_settings = jet_engine()->dynamic_functions->get_custom_settings( $function_name, $this );

		echo jet_engine()->dynamic_functions->call_function( $function_name, $data_source, $field_name, $custom_settings );
	}

}
