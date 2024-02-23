<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * JetSmartFilters dynamic var
 */
class Jet_Smart_Filters_Tax_Query_Dynamic_Var extends Jet_Smart_Filters_Admin_Dynamic_Query_Base {
	
	public function get_name() {
		return '_tax_query';
	}

	public function get_label() {
		return __( 'Taxonomy', 'jet-smart-filters' );
	}

	public function get_delimiter() {
		return '::';
	}

	// return additional arguments if needed
	public function get_extra_args() {
		return array(
			'relation' => array(
				'type'        => 'select',
				'title'       => __( 'Taxonomy', 'jet-engine' ),
				'placeholder' => __( 'Select taxonomy...', 'jet-engine' ),
				'options'     => jet_smart_filters()->data->get_taxonomies_for_options(),
			),
		);
	}
	
}
