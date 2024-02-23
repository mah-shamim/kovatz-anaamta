<?php

/**
 * Class WPML_Integration_Jet_Smart_Filters_Sorting
 */
class WPML_Integration_Jet_Smart_Filters_Sorting extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {

		return 'sorting_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {

		return array( 'title' );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {

		switch( $field ) {
			case 'title':
				return esc_html__( 'JetSmartFilters: Sorting Item Text', 'jet-smart-filters' );
	
			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch( $field ) {
			case 'title':
				return 'LINE';

			default:
				return '';
		}
	}
}
