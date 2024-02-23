<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Elementor\Dynamic_Tags;

use Jet_Engine\Modules\Custom_Content_Types\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Field_Tag extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'jet-cct-field';
	}

	public function get_title() {
		return __( 'Custom Content Type Field', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::NUMBER_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::URL_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::POST_META_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::COLOR_CATEGORY
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {

		$groups = array();

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

			$fields = $instance->get_fields_list();
			$prefixed_fields = array();

			foreach ( $fields as $key => $label ) {
				$prefixed_fields[ $type . '__' . $key ] = $label;
			}

			$groups[] = array(
				'label'   => __( 'Content Type:', 'jet-engine' ) . ' ' . $instance->get_arg( 'name' ),
				'options' => $prefixed_fields,
			);

		}

		$this->add_control(
			'content_type_field',
			array(
				'label'  => __( 'Field', 'jet-engine' ),
				'type'   => \Elementor\Controls_Manager::SELECT,
				'groups' => $groups,
			)
		);

	}

	public function render() {

		$field = $this->get_settings( 'content_type_field' );

		if ( empty( $field ) ) {
			return;
		}

		$value = jet_engine()->listings->data->get_prop( $field );

		if ( is_array( $value ) ) {
			echo jet_engine_render_checkbox_values( $value );
			return $value;
		}

		echo wp_kses_post( $value );

	}

}
