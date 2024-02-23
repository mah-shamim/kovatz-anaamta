<?php
/**
 * Services class
 */

class Jet_Smart_Filters_Services {
	// Services
	public $filters;
	public $filter;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		require jet_smart_filters()->plugin_path( 'includes/services/filters.php' );
		require jet_smart_filters()->plugin_path( 'includes/services/filter.php' );

		$this->filters = new Jet_Smart_Filters_Service_Filters();
		$this->filter  = new Jet_Smart_Filters_Service_Filter();
	}
}
