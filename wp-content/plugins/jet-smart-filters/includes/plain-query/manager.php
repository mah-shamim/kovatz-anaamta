<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * JetSmartFilters tax query manager
 */
class Jet_Smart_Filters_Plain_Query_Manager extends Jet_Smart_Filters_Tax_Query_Manager {

	public function register_query_var() {
		require jet_smart_filters()->plugin_path( 'includes/plain-query/query-var.php' );
		new Jet_Smart_Filters_Plain_Query_Var();
	}

	public function register_dynamic_var( $dynamic_query_manager ) {
		require jet_smart_filters()->plugin_path( 'includes/plain-query/dynamic-var.php' );
		$dynamic_query_manager->register_item( new Jet_Smart_Filters_Plain_Query_Dynamic_Var() );
	}

}
