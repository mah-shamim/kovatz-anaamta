<?php

namespace Jet_Elementor_Extension;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Query_Control extends \Elementor\Control_Select2 {

	public function get_type() {
		return 'jet-query';
	}

	public function enqueue() {
		// Styles
		$inline_css = '.jet-query-edit-btn-wrap{margin-top:10px}.jet-query-edit-btn.elementor-button{border:none}';
		printf( '<style>%s</style>', $inline_css );
	}

	/**
	 * @param string|array $value
	 * @param array $config
	 *
	 * @return string|array
	 */
	public function before_save( $value, array $config ) {
		if ( ! is_array( $value ) ) {
			if ( ! empty( $value ) ) {
				$value = absint( $value );
			}
		} else {
			$value = array_map( 'absint', $value );
		}
		
		return $value;
	}

	protected function get_default_settings() {
		return array_merge(
			parent::get_default_settings(), array(
				'query_type'      => 'post',
				'query'           => array(),
				'prevent_looping' => false,
				'edit_button' => array(
					'active' => false,
					'label'  => 'Edit Template',
				),
			)
		);
	}
}
