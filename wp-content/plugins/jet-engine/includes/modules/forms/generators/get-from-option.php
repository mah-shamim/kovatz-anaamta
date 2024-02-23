<?php
namespace Jet_Engine\Forms\Generators;

class Get_From_Option extends Base {

	/**
	 * Returns generator ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'get_from_option';
	}

	/**
	 * Returns generator name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Get values list from JetEngine Option Page field', 'jet-engine' );
	}

	/**
	 * Returns generated options list
	 *
	 * @return array
	 */
	public function generate( $field ) {

		$all_pages   = jet_engine()->options_pages->registered_pages;
		$found_field = null;
		$result      = array();
		$parse_field = explode( '|', $field );
		$field       = $parse_field[0];
		$sub_field   = isset( $parse_field[1] ) ? $parse_field[1] : false;

		foreach ( $all_pages as $page ) {

			if ( empty( $page->meta_box ) ) {
				continue;
			}

			$fields = $page->meta_box;

			foreach ( $fields as $field_data ) {
				if ( ! empty( $field_data['name'] ) && $field === $field_data['name'] ) {
					$found_field = $field_data;
				}
			}
		}

		if ( ! empty( $sub_field ) && ! empty( $found_field['repeater-fields'] ) ) {
			foreach ( $found_field['repeater-fields'] as $repeater_field_data ) {
				if ( ! empty( $repeater_field_data['name'] ) && $sub_field === $repeater_field_data['name'] ) {
					$found_field = $repeater_field_data;
				}
			}
		}

		if ( empty( $found_field['options'] ) ) {
			return $result;
		}

		foreach ( $found_field['options'] as $option ) {
			$result[] = array(
				'value' => $option['key'],
				'label' => $option['value'],
			);
		}

		return $result;

	}

}
