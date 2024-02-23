<?php
/**
 * Register post meta field for Rest API
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Rest_Post_Meta' ) ) {

	/**
	 * Define Jet_Engine_Rest_Post_Meta class
	 */
	class Jet_Engine_Rest_Post_Meta {

		public $field          = array();
		public $object_subtype = null;
		
		public function __construct( $field = array(), $post_type = null ) {
			
			$this->field     = $field;
			$this->object_subtype = $post_type;

			$this->prepare_object();
			$this->register_field();
		}

		public function get_object_type() {
			return 'post';
		}

		public function prepare_object() {
			
			$support_custom_fields = post_type_supports( $this->object_subtype, 'custom-fields' );

			if ( ! $support_custom_fields ) {
				add_post_type_support( $this->object_subtype, 'custom-fields' );
			}
			
		}

		public function get_field_type() {

			$field_type = 'string';

			switch ( $this->field['type'] ) {

				case 'text':

					if ( ! empty( $this->field['input_type'] ) && in_array( $this->field['input_type'], array( 'date', 'datetime-local' ) ) && ! empty( $this->field['is_timestamp'] ) ) {
						$field_type = 'integer';
					}

					break;
				
				case 'checkbox':
					
					if ( ! empty( $this->field['is_array'] ) ) {
						$field_type = 'array';
					} else {
						$field_type = 'object';
					}
					
					break;

				case 'repeater':
					$field_type = 'object';
					break;

				case 'posts':
					if ( ! empty( $this->field['multiple'] ) ) {
						$field_type = 'array';
					}

					break;

				case 'select':
					
					if ( ! empty( $this->field['multiple'] ) ) {
						$field_type = 'array';
					} else {
						$field_type = 'string';
					}
					
					break;

				case 'media':

					if ( ! empty( $this->field['multi_upload'] ) ) {
						$field_type = 'array';
					} else {
						if ( ! empty( $this->field['value_format'] ) && 'both' === $this->field['value_format'] ) {
							$field_type = 'object';
						} else {
							$field_type = 'string';
						}
					}
					
					break;
			}

			return apply_filters( 'jet-engine/meta-boxes/rest-api/fields/field-type', $field_type, $this->field );

		}

		public function get_rest_schema( $field_type ) {

			$result = true;

			switch ( $field_type ) {
				
				case 'array':

					if ( 'media' === $this->field['type'] && isset( $this->field['value_format'] ) && 'both' === $this->field['value_format'] ) {
						$result = array( 'schema' => array(
							'type'  => 'array',
							'items' => array(
								'type' => 'object',
								'properties' => array(
									'id' => array(
										'type' => 'integer',
									),
									'url'  => array(
										'type' => 'string',
									),
								),
							),
						) );
					} else {
						$result = array( 'schema' => array(
							'type'  => 'array',
							'items' => array(
								'type' => array( 'string', 'integer' ),
							),
						) );
					}

					break;

				case 'object':
					
					$result = array( 
						'type'             => 'object',
						'schema'           => array( 'additionalProperties' => true ),
						'prepare_callback' => array( $this, 'prepare_object_value' ) 
					);

					break;
				
				default:
					
					$result = true;
					break;
			}

			return apply_filters( 'jet-engine/meta-boxes/rest-api/fields/schema', $result, $field_type, $this->field );

		}

		public function register_field() {

			$field_type = $this->get_field_type();

			$args = array(
				'object_subtype' => $this->object_subtype,
				'type' => $field_type,
				'single' => true,
				'show_in_rest' => $this->get_rest_schema( $field_type ),
			);

			$result = register_meta( $this->get_object_type(), $this->field['name'], $args );

		}

		public function prepare_object_value( $value, $request, $args ) {
			return maybe_unserialize( $value );
		}

	}

}
