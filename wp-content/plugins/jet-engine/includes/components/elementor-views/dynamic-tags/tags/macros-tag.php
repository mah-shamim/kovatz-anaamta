<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_Macros_Tag extends Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-macros';
	}

	public function get_title() {
		return __( 'Macros', 'jet-engine' );
	}

	public function get_group() {
		return Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {

		$this->add_control(
			'macros',
			array(
				'label'   => __( 'Macros', 'jet-engine' ),
				'type'    => Elementor\Controls_Manager::SELECT,
				'options' => jet_engine()->listings->macros->get_macros_list_for_options(),
			)
		);

		$this->register_additional_controls();
	}

	public function register_additional_controls() {
		$all      = jet_engine()->listings->macros->get_all( false, true );
		$controls = array();

		foreach ( $all as $macros => $data ) {

			if ( empty( $data['args'] ) ) {
				continue;
			}

			foreach ( $data['args'] as $control_id => $control_data ) {

				if ( empty( $controls[ $control_id ] ) ) {

					$control_data['condition']['macros'] = array( $macros );

					$controls[ $control_id ] = $control_data;
				} else {
					$controls[ $control_id ]['condition']['macros'][] = $macros;
				}

			}
		}

		if ( ! empty( $controls ) ) {
			foreach ( $controls as $control_id => $control_data ) {
				$this->add_control( $control_id, $control_data );
			}
		}

	}

	public function get_macros_args( $macros ) {

		$all    = jet_engine()->listings->macros->get_all();
		$result = array();

		if ( empty( $all[ $macros ]['args'] ) ) {
			return false;
		}

		$keys = array_keys( $all[ $macros ]['args'] );

		foreach ( $keys as $key ) {
			$value = $this->get_settings( $key );

			if ( ! empty( $value ) ) {
				$result[] = $value;
			}
		}

		return $result;
	}

	public function get_value( array $options = array() ) {

		$macros = $this->get_settings( 'macros' );

		if ( empty( $macros ) ) {
			return '';
		}

		$macros_args = $this->get_macros_args( $macros );

		if ( ! empty( $macros_args ) ) {
			$macros = $macros . '|' . join( '|', $macros_args );
		}

		return '%' . $macros . '%';
	}

}
