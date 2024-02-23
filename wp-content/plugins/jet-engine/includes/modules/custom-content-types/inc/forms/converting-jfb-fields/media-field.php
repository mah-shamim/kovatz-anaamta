<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Media_Field extends Base_Convert_Field {

	/**
	 * @return array
	 */
	public function block_names() {
		return array(
			'media'   => 'jet-forms/media-field',
			'gallery' => 'jet-forms/media-field',
		);
	}

	protected function manual_attrs() {
		return array_merge_recursive( parent::manual_attrs(), array(
			'value_format' => array(
				'name' => 'value_format',
			),
		) );
	}

	public function custom_converting() {
		if ( $this->is_type( 'gallery' ) ) {
			$this->save_attr( 'max_files', 10 );
		}
	}

}