<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Simple_Field extends Base_Convert_Field {

	/**
	 * @inheritDoc
	 */
	public function block_names() {
		return array(
			'time'        => 'jet-forms/time-field',
			'wysiwyg'     => 'jet-forms/wysiwyg-field',
			'iconpicker'  => 'jet-forms/text-field',
			'colorpicker' => 'jet-forms/text-field',
		);
	}

}