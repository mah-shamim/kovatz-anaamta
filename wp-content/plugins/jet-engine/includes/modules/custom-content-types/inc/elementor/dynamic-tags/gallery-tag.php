<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Elementor\Dynamic_Tags;

use Jet_Engine\Modules\Custom_Content_Types\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Gallery_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-cct-gallery';
	}

	public function get_title() {
		return __( 'Custom Content Type Gallery', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::GALLERY_CATEGORY,
		);
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

	public function get_value( array $options = array() ) {

		$field = $this->get_settings( 'content_type_field' );

		if ( empty( $field ) ) {
			return;
		}

		$value = jet_engine()->listings->data->get_prop( $field );

		if ( empty( $value ) ) {
			return array();
		}

		if ( is_array( $value ) ) {
			$value = $value;
		} else {
			$value = explode( ',', $value );
		}

		return array_map( function( $item ) {
			return \Jet_Engine_Tools::get_attachment_image_data_array( $item, 'id' );
		}, $value );

	}
}
