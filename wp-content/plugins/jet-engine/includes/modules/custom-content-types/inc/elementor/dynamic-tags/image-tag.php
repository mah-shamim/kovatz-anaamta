<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Elementor\Dynamic_Tags;

use Jet_Engine\Modules\Custom_Content_Types\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Image_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-cct-image';
	}

	public function get_title() {
		return __( 'Custom Content Type Image', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::IMAGE_CATEGORY,
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {

		$groups = array();

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

			$fields = $instance->get_fields_list( 'media' );
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

		$this->add_control(
			'fallback',
			array(
				'label' => __( 'Fallback', 'jet-engine' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

	}

	/**
	 * [get_value description]
	 *
	 * @param  array  $options [description]
	 * @return [type]          [description]
	 */
	public function get_value( array $options = array() ) {

		$field = $this->get_settings( 'content_type_field' );

		if ( empty( $field ) ) {
			return $this->get_settings( 'fallback' );
		}

		$img = jet_engine()->listings->data->get_prop( $field );

		if ( ! empty( $img ) ) {
			return \Jet_Engine_Tools::get_attachment_image_data_array( $img );
		}

		return $this->get_settings( 'fallback' );
	}

}
