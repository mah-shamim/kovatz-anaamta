<?php
namespace Jet_Engine\Meta_Boxes\Option_Sources;

class Manual_Bulk_Options extends Manual_Options {

	public $source_name = 'manual_bulk';

	/**
	 * Apply options of current source
	 * 
	 * @return [type] [description]
	 */
	public function apply_options( $options = [], $field = [] ) {

		if ( $this->is_field_of_current_source( $field ) ) {
			$options = $this->parse_options( $field );
		}

		return $options;

	}

	/**
	 * Check if given field belongs to current source
	 * 
	 * @param  array   $field [description]
	 * @return boolean        [description]
	 */
	public function is_field_of_current_source( $field = [] ) {

		if ( ! empty( $field['options_source'] ) && $this->source_name === $field['options_source'] ) {
			return true;
		}

		return false;

	}

	public function parse_options( $field ) {
		
		$raw = ! empty( $field['bulk_options'] ) ? $field['bulk_options'] : '';
		$result = [];

		$raw = explode( PHP_EOL, $raw );

		if ( empty( $raw ) ) {
			return $result;
		}

		foreach ( $raw as $value ) {
			$parsed_value = explode( '::', trim( $value ) );
			$result[] = array(
				'key'        => $parsed_value[0],
				'value'      => isset( $parsed_value[1] ) ? $parsed_value[1] : $parsed_value[0],
				'is_checked' => ( isset( $parsed_value[2] ) && 'checked' ) ? true : false,
			);
		}

		return $result;

	}

	/**
	 * Merge new custom value to field options
	 * 
	 * @param  [type] $field        [description]
	 * @param  [type] $custom_value [description]
	 * @return [type]               [description]
	 */
	public function merge_custom_values( $field, $custom_value ) {

		if ( $this->is_field_of_current_source( $field ) ) {
			$field['bulk_options'] .= PHP_EOL . $custom_value;
		}

		return $field;
	}

}
