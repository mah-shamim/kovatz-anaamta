<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Text_Field extends Base_Convert_Field {

	/**
	 * @return array
	 */
	public function block_names() {
		return array(
			'text'     => 'jet-forms/text-field',
			'textarea' => 'jet-forms/textarea-field'
		);
	}

	protected function manual_attrs() {
		return array_merge_recursive( parent::manual_attrs(), array(
			'max_length' => array(
				'name'     => 'maxlength',
				'callable' => 'absint'
			),
		) );
	}


}