<?php
namespace Jet_Engine\Meta_Boxes\Option_Sources;

class Manual_Options {

	public $source_name = 'manual';

	public function __construct() {

		add_filter( 'jet-engine/meta-fields/field-options', array( $this, 'apply_options' ), 10, 2 );

		// Optional part of init. May be various for different sources
		$this->init();

	}

	/**
	 * Apply options of current source
	 * 
	 * @return [type] [description]
	 */
	public function apply_options( $options = [], $field = [] ) {

		if ( $this->is_field_of_current_source( $field ) ) {
			$options = ! empty( $field['options'] ) ? $field['options'] : [];
		}

		return $options;

	}

	/**
	 * Init optional part of the source
	 * 
	 * @return [type] [description]
	 */
	public function init() {
		add_filter( 
			'jet-engine/meta-boxes/option-sources/get-merged-options', 
			[ $this, 'merge_custom_values' ],
			10, 2 
		);
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

		// handle legacy manual options
		if ( empty( $field['options_source'] ) 
			&& ( empty( $field['options_from_glossary'] ) || empty( $field['glossary_id'] ) ) 
		) {
			return true;
		} elseif ( 
			empty( $field['options_source'] ) 
			&& ( ! empty( $field['options_from_glossary'] ) && ! empty( $field['glossary_id'] ) )
		) {
			return false;
		}

		return false;

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
			$field['options'][] = array(
				'key'        => esc_attr( $custom_value ),
				'value'      => esc_attr( $custom_value ),
				'is_checked' => '',
			);
		}

		return $field;
	}

}
