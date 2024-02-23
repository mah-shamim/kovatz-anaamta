<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Select_Posts_Field extends Base_Convert_Field {

	/**
	 * @return array
	 */
	public function block_names() {
		return array( 'posts' => 'jet-forms/select-field' );
	}

	protected function manual_attrs() {
		return array_merge_recursive( parent::manual_attrs(), array(
			'search_post_type' => array(
				'name'     => 'field_options_post_type',
				'callable' => function ( $types ) {
					return isset( $types[0] ) ? $types[0] : 'post';
				}
			),
		) );
	}

	public function custom_converting() {
		$this->save_attr( 'field_options_from', 'posts' );
	}


}