<?php
namespace Jet_Engine\Modules\Maps_Listings\Compatibility;

class Jet_Form_Builder {

	public function __construct() {
		add_action( 'jet-form-builder/action/after-post-insert', array( $this, 'update_post_service_map_fields' ), 10, 2 );
		add_action( 'jet-form-builder/action/after-post-update', array( $this, 'update_post_service_map_fields' ), 10, 2 );

		add_filter( 'insert_custom_user_meta', array( $this, 'update_user_service_map_fields' ) );

		add_filter( 'pre_update_option', array( $this, 'update_option_service_map_fields' ), 10, 2 );

		add_filter( 'jet-engine/custom-content-types/item-to-update', array( $this, 'update_cct_service_map_fields' ), 10, 3 );
	}

	public function update_post_service_map_fields( $action, $handler ) {

		$service_values = $this->get_service_fields_values_from_request();

		if ( empty( $service_values ) ) {
			return;
		}

		$post_id = $handler->get_inserted_post_id( $action->_id );

		foreach ( $service_values as $field => $value ) {
			update_post_meta( $post_id, $field, $value );
		}
	}

	public function update_user_service_map_fields( $meta ) {

		if ( ! jet_fb_action_handler()->in_loop() ) {
			return $meta;
		}

		$action = jet_fb_action_handler()->get_current_action();

		if ( ! in_array( $action->get_id(), array( 'register_user', 'update_user' ) ) ) {
			return $meta;
		}

		$service_values = $this->get_service_fields_values_from_request();

		if ( empty( $service_values ) ) {
			return $meta;
		}

		$meta = array_merge( $meta, $service_values );

		return $meta;
	}

	public function update_option_service_map_fields( $value, $option ) {

		if ( ! jet_fb_action_handler()->in_loop() ) {
			return $value;
		}

		$action = jet_fb_action_handler()->get_current_action();

		if ( 'update_options' !== $action->get_id() ) {
			return $value;
		}

		$settings = $action->settings;

		if ( empty( $settings['options_page'] ) || $settings['options_page'] !== $option ) {
			return $value;
		}

		$service_values = $this->get_service_fields_values_from_request();

		if ( empty( $service_values ) ) {
			return $value;
		}

		$value = (array) $value;

		$value = array_merge( $value, $service_values );

		return $value;
	}

	public function update_cct_service_map_fields( $item, $fields, $cct ) {

		if ( ! jet_fb_action_handler()->in_loop() ) {
			return $item;
		}

		$action = jet_fb_action_handler()->get_current_action();

		if ( 'insert_custom_content_type' !== $action->get_id() ) {
			return $item;
		}

		$settings = $action->settings;
		$cct_slug = $cct->get_factory()->get_arg( 'slug' );

		if ( empty( $settings['type'] ) || $settings['type'] !== $cct_slug ) {
			return $item;
		}

		$service_values = $this->get_service_fields_values_from_request( false );

		$item = array_merge( $item, $service_values );

		return $item;
	}

	public function get_service_fields_values_from_request( $hash_prefix = true ) {

		$service_values = array();

		$action   = jet_fb_action_handler()->get_current_action();
		$settings = $action->settings;

		$fields_map_key = 'fields_map';

		if ( in_array( $action->get_id(), array( 'update_options', 'register_user' ) ) ) {
			$fields_map_key = 'meta_fields_map';
		}

		if ( empty( $settings[ $fields_map_key ] ) ) {
			return $service_values;
		}

		$request = jet_fb_action_handler()->request_data;

		foreach ( $settings[ $fields_map_key ] as $form_field => $post_field ) {
			$is_map_field = jet_fb_request_handler()->is_type( $form_field, 'map-field' );

			if ( ! $is_map_field ) {
				continue;
			}

			$prefix = $post_field;

			if ( $hash_prefix ) {
				$prefix = md5( $prefix );
			}

			$service_values[ $prefix . '_hash' ] = ! empty( $request[ $form_field ] ) ? md5( $request[ $form_field ] ) : false;
			$service_values[ $prefix . '_lat' ]  = ! empty( $request[ $form_field . '_lat' ] ) ? $request[ $form_field . '_lat' ] : false;
			$service_values[ $prefix . '_lng' ]  = ! empty( $request[ $form_field . '_lng' ] ) ? $request[ $form_field . '_lng' ] : false;
		}

		return $service_values;
	}

}
