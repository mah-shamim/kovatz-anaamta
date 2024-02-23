<?php
namespace Jet_Engine\Modules\Maps_Listings\Filters\Blocks;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define User_Geolocation class
 */
class User_Geolocation extends \Jet_Smart_Filters_Block_Base {

	/**
	 * Returns block name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'user-geolocation';
	}

	public function set_css_scheme(){
		$this->css_scheme = array();
	}

	public function add_style_manager_options() {}
}
