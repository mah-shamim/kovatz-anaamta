<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_Object_Property_Tag extends Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-object-property';
	}

	public function get_title() {
		return __( 'Current Object Field', 'jet-engine' );
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
			Jet_Engine_Dynamic_Tags_Module::COLOR_CATEGORY,
			Jet_Engine_Dynamic_Tags_Module::IMAGE_CATEGORY,
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
		
		$this->add_control(
			'object_prop',
			array(
				'label'  => __( 'Field', 'jet-engine' ),
				'type'   => \Elementor\Controls_Manager::SELECT,
				'groups' => $this->get_object_fields(),
			)
		);

		$this->add_control(
			'object_context',
			array(
				'label'     => __( 'Context', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'default_object',
				'options'   => jet_engine()->listings->allowed_context_list(),
			)
		);
		
	}

	protected function register_advanced_section() {
		$this->start_controls_section(
			'advanced',
			array(
				'label' => esc_html__( 'Advanced', 'jet-engine' ),
			)
		);

		$this->add_control(
			'notice',
			array(
				'type' => Elementor\Controls_Manager::RAW_HTML,
				'raw'  => '<div style="font-weight: bold; font-style: italic;">' . esc_html__( 'The following settings only work for string values.', 'jet-engine' ) . '</div>',
			)
		);

		$this->add_control(
			'before',
			array(
				'label' => esc_html__( 'Before', 'jet-engine' ),
			)
		);

		$this->add_control(
			'after',
			array(
				'label' => esc_html__( 'After', 'jet-engine' ),
			)
		);

		$this->add_control(
			'fallback',
			array(
				'label' => esc_html__( 'Fallback', 'jet-engine' ),
			)
		);

		$this->end_controls_section();
	}

	public function get_object_fields() {
		return jet_engine()->listings->data->get_object_fields();
	}

	public function get_value( array $options = array() ) {
	
		$object_prop    = $this->get_settings( 'object_prop' );
		$object_context = $this->get_settings( 'object_context' );

		if ( empty( $object_prop ) ) {
			return '';
		}

		$value = jet_engine()->listings->data->get_prop(
			$object_prop,
			jet_engine()->listings->data->get_object_by_context( $object_context )
		);

		if ( is_array( $value ) ) {
			return $value;
		} else {
			$settings = $this->get_settings();
			$value    = wp_kses_post( $value );

			if ( ! Jet_Engine_Tools::is_empty( $value ) ) {

				if ( ! Jet_Engine_Tools::is_empty( $settings, 'before' ) ) {
					$value = wp_kses_post( $settings['before'] ) . $value;
				}

				if ( ! Jet_Engine_Tools::is_empty( $settings, 'after' ) ) {
					$value .= wp_kses_post( $settings['after'] );
				}

			} elseif ( ! Jet_Engine_Tools::is_empty( $settings, 'fallback' ) ) {
				$value = $settings['fallback'];
			}

			return wp_kses_post( $value );
		}

	}

}
