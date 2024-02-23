<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Date_Field extends Base_Convert_Field {

	/**
	 * @return array
	 */
	public function block_names() {
		return array(
			'date'           => 'jet-forms/date-field',
			'datetime-local' => 'jet-forms/datetime-field',
		);
	}

	protected function manual_attrs() {
		return array_merge_recursive( parent::manual_attrs(), array(
			'is_timestamp' => array(
				'name' => 'is_timestamp',
			),
		) );
	}

}