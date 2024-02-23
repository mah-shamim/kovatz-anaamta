<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Number_Field extends Base_Convert_Field {

	/**
	 * @return array
	 */
	public function block_names() {
		return array( 'number' => 'jet-forms/number-field' );
	}

	protected function manual_attrs() {
		return array_merge_recursive( parent::manual_attrs(), array(
			'min_value' => array(
				'name'     => 'min',
				'callable' => 'absint'
			),
			'max_value' => array(
				'name'     => 'max',
				'callable' => 'absint'
			),
			'step_value' => array(
				'name'     => 'step',
				'callable' => 'absint'
			),
		) );
	}


}