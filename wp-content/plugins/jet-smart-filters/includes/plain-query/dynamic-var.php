<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * JetSmartFilters dynamic var
 */
class Jet_Smart_Filters_Plain_Query_Dynamic_Var extends Jet_Smart_Filters_Tax_Query_Dynamic_Var {
	
	public function get_name() {
		return '_plain_query';
	}

	public function get_label() {
		return __( 'Plain Query Variable', 'jet-smart-filters' );
	}

	public function get_delimiter() {
		return '::';
	}

	// return additional arguments if needed
	public function get_extra_args() {
		return array(
			'query_var' => array(
				'type'        => 'text',
				'title'       => __( 'Variable Name', 'jet-engine' ),
				'description' => __( 'This variable will be stored', 'jet-engine' ),
			),
		);
	}
	
}
