<?php
/**
 * Register options field for Rest API
 */

if ( ! class_exists( 'Jet_Engine_Rest_Post_Meta' ) ) {
	require jet_engine()->meta_boxes->component_path( 'rest-api/fields/post-meta.php' );
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Rest_Settings' ) ) {

	/**
	 * Define Jet_Engine_Rest_Settings class
	 */
	class Jet_Engine_Rest_Settings extends Jet_Engine_Rest_Post_Meta {

		public $page = null;

		public function __construct( $field = array(), $post_type = null, $page = null ) {

			$this->field          = $field;
			$this->object_subtype = $post_type;
			$this->page           = $page;

			$this->prepare_object();
			$this->register_field();

			add_filter( 'rest_pre_get_setting',    array( $this, 'get_setting' ), 10, 3 );
			add_filter( 'rest_pre_update_setting', array( $this, 'update_setting' ), 10, 3 );

		}

		public function get_setting( $value, $setting, $args ) {

			$field_name = $this->field['name'];

			if ( 'separate' === $this->page->storage_type ) {
				$field_name = $this->page->get_separate_option_name( $field_name );
			}

			if ( $setting === $field_name ) {
				
				$value = jet_engine()->listings->data->get_option( $this->object_subtype . '::' . $this->field['name'] );

				if ( 'repeater' === $this->field['type'] ) {
				
					if ( ! $value ) {
						$value = array();
					} else {
						$value = is_array( $value ) ? array_values( $value ) : array();
					}

					foreach ( $value as $index => $val ) {

						foreach ( $val as $item_key => $item_val ) {

							if ( ! isset( $this->field['fields'][ $item_key ] ) ) {
								continue;
							}

							$value[ $index ][ $item_key ] = apply_filters( 'jet-engine/options-pages/rest-api/fields/value', $item_val, $this->field['fields'][ $item_key ] );
						}

					}
				}

				$value = apply_filters( 'jet-engine/options-pages/rest-api/fields/value', $value, $this->field );

			}

			return $value;
		}

		public function update_setting( $result, $setting, $value ) {

			$field_name = $this->field['name'];

			if ( 'separate' === $this->page->storage_type ) {
				$field_name = $this->page->get_separate_option_name( $field_name );
			}

			if ( $setting === $field_name ) {

				$data = array(
					$this->field['name'] => $value,
				);

				$this->page->update_options( $data, false, false );

				return true;
			}

			return $result;
		}

		public function prepare_object() {
			// Not used for settings		
		}

		public function get_rest_schema( $field_type, $field = null ) {

			$result = true;

			if ( ! $field ) {
				$field = $this->field;
				$is_repeater_field = false;
			} else {
				$is_repeater_field = true;
			}

			switch ( $field_type ) {
				
				case 'array':
					
					$result = $this->get_settings_array_schema( $field );
					break;

				case 'object':
					
					$result = $this->get_settings_object_schema( $field );
					break;
				
				default:
					
					$result = true;
					break;
			}

			$result = apply_filters( 'jet-engine/meta-boxes/rest-api/fields/schema', $result, $field_type, $field );

			if ( is_array( $result ) && isset( $result['type'] ) && ! $is_repeater_field ) {
				$result = array( 'schema' => $result );
			}

			if ( ! is_array( $result ) ) {
				$result = array(
					'type' => array( 'string', 'integer', 'float' ),
				);
			}

			return $result;

		}

		public function get_settings_object_schema( $field ) {
		
			$properties = array();

			if ( 'media' === $field['type'] ) {
				
				$properties = array(
					'id' => array(
						'type' => 'integer',
					),
					'url' => array(
						'type' => 'string',
					),
				);

			} elseif ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
				foreach ( $field['options'] as $key => $value ) {
					$properties[ $key ] = array(
						'type'     => array( 'string', 'integer' ),
						'required' => false,
					);
				}
			}

			return array( 'schema' => array(
				'type'       => 'object',
				'properties' => $properties,
			) );

		}

		public function get_settings_array_schema( $field ) {
		
			if ( 'media' === $field['type'] && isset( $field['value_format'] ) && 'both' === $field['value_format'] ) {
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
			} elseif ( 'repeater' === $field['type']  ) {
				
				$properties = array();

				if ( ! empty( $field['fields'] ) ) {
					foreach ( $field['fields'] as $repeater_field => $data ) {
						$properties[ $repeater_field ] = $this->get_rest_schema( $data['type'], $data );
					}
				}

				$result = array( 'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'object',
						'properties' => $properties,
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

			return $result;

		}

		public function register_field() {

			$field_name   = $this->field['name'];
			$field_type   = $this->get_field_type();
			$option_group = $this->object_subtype;

			if ( 'repeater' === $this->field['type'] ) {
				$field_type = 'array';
			}

			if ( 'separate' === $this->page->storage_type ) {
				$field_name = $this->page->get_separate_option_name( $field_name );
			}

			register_setting( $option_group, $field_name, array(
				'type' => $field_type,
				'show_in_rest' => $this->get_rest_schema( $field_type ),
			) );

		}

	}

}
