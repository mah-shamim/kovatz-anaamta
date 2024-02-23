<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Engine_User_Image_Tag extends Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-user-custom-image';
	}

	public function get_title() {
		return __( 'User Image', 'jet-engine' );
	}

	public function get_group() {
		return Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			Jet_Engine_Dynamic_Tags_Module::IMAGE_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'img_field',
			array(
				'label'  => __( 'Field', 'jet-engine' ),
				'type'   => Elementor\Controls_Manager::SELECT,
				'groups' => $this->get_user_fields(),
			)
		);

		$this->add_control(
			'fallback',
			array(
				'label' => __( 'Fallback', 'jet-engine' ),
				'type'  => Elementor\Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'user_context',
			array(
				'label'   => __( 'Context', 'jet-engine' ),
				'type'    => Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => apply_filters( 'jet-engine/elementor/dynamic-tags/user-context-list', array(
					'wp_user'             => __( 'Current User (global)', 'jet-engine' ),
					'current_user'        => __( 'Current User (for current scope)', 'jet-engine' ),
					'queried_user'        => __( 'Queried User', 'jet-engine' ),
					'current_post_author' => __( 'Current Post Author', 'jet-engine' ),
				) ),
			)
		);

	}

	public function get_value( array $options = array() ) {

		$img_field = $this->get_settings( 'img_field' );
		$context   = $this->get_settings( 'user_context' );

		if ( ! $context ) {
			$context = 'current_user';
		}

		if ( empty( $img_field ) ) {
			return $this->get_settings( 'fallback' );
		}

		$value = false;

		switch ( $context ) {

			case 'wp_user':
				$user_object = wp_get_current_user();
				break;

			case 'current_user':
				$user_object = jet_engine()->listings->data->get_current_user_object();
				break;

			case 'current_post_author':
				$user_object = jet_engine()->listings->data->get_current_author_object();
				break;

			default:
				$user_object = jet_engine()->listings->data->get_queried_user_object();
				$user_object = apply_filters( 'jet-engine/elementor/dynamic-tags/user-context-object/' . $context, $user_object );
				break;
		}

		if ( ! $user_object ) {
			return;
		}

		$img_id = get_user_meta( $user_object->ID, $img_field, true );

		if ( $img_id ) {
			return Jet_Engine_Tools::get_attachment_image_data_array( $img_id );
		} else {
			return $this->get_settings( 'fallback' );
		}

	}

	private function get_user_fields() {

		if ( jet_engine()->meta_boxes ) {
			return jet_engine()->meta_boxes->get_fields_for_select( 'media', 'elementor', 'user' );
		} else {
			return array();
		}

	}

}
