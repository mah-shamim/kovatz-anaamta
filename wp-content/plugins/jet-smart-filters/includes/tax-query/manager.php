<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * JetSmartFilters tax query manager
 */
class Jet_Smart_Filters_Tax_Query_Manager {
	
	public function __construct() {
		
		$this->register_query_var();
		add_action( 'jet-smart-filters/admin/register-dynamic-query', array( $this, 'register_dynamic_var' ), 0 );

	}

	public function register_query_var() {
		require jet_smart_filters()->plugin_path( 'includes/tax-query/query-var.php' );
		new Jet_Smart_Filters_Tax_Query_Var();
	}

	public function register_dynamic_var( $dynamic_query_manager ) {
		require jet_smart_filters()->plugin_path( 'includes/tax-query/dynamic-var.php' );
		$dynamic_query_manager->register_item( new Jet_Smart_Filters_Tax_Query_Dynamic_Var() );
	}

}
