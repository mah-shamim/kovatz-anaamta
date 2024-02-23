<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Checkbox_Field extends Base_Convert_Field {

	/**
	 * @return array
	 */
	public function block_names() {
		return array(
			'checkbox' => 'jet-forms/checkbox-field',
			'radio'    => 'jet-forms/radio-field',
			'select'   => 'jet-forms/select-field',
		);
	}

	protected function manual_attrs() {
		return array_merge_recursive( parent::manual_attrs(), array(
			'options_from_glossary' => array(
				'name'     => 'field_options_from',
				'callable' => function ( $value ) {
					return $value ? 'glossary' : 'manual_input';
				}
			),
			'options'               => array(
				'name'     => 'field_options',
				'callable' => array( $this, 'prepare_options' )
			),
			'glossary_id'           => array(
				'name' => 'glossary_id'
			),
			'placeholder'           => array(
				'name' => 'placeholder'
			)
		) );
	}

	public function prepare_options( $options ) {
		return array_map( function ( $option ) {
			return array(
				'label' => $option['value'],
				'value' => $option['key']
			);
		}, $options );
	}

	public function custom_converting() {
		if ( ! $this->isset_attr( 'field_options_from' ) ) {
			$this->save_attr( 'field_options_from', 'manual_input' );
		}
	}

}