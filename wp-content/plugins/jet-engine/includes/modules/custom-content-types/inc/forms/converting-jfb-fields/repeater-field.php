<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Repeater_Field extends Base_Convert_Field {

	public function block_names() {
		return array( 'repeater' => 'jet-forms/repeater-field' );
	}

	public function custom_converting() {
		if ( empty( $this->raw_field['repeater-fields'] ) ) {
			return;
		}
		$this->prepared_field['innerBlocks'] = Convert_Manager::instance()->prepare_fields(
			$this->raw_field['repeater-fields']
		);
	}
}