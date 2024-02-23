<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * JetSmartFilters apply tax auery query var
 */
class Jet_Smart_Filters_Plain_Query_Var extends Jet_Smart_Filters_Tax_Query_Var {

	public $prefix = '_plain_query::';
	public $type   = 'plain_query';

}
