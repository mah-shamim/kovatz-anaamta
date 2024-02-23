<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Switcher_Field extends Base_Convert_Field {

	/**
	 * @inheritDoc
	 */
	public function block_names() {
		return array( 'switcher' => 'jet-forms/select-field' );
	}

	public function custom_converting() {
		$this->save_attr( 'field_options', array(
			array(
				'value' => 'true',
				'label' => __( 'On', 'jet-engine' ),
			),
			array(
				'value' => 'false',
				'label' => __( 'Off', 'jet-engine' ),
			),
		) );
	}
}