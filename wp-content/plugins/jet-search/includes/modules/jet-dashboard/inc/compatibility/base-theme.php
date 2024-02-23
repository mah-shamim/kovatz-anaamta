<?php
namespace Jet_Dashboard\Compatibility;

use Jet_Dashboard\Dashboard as Dashboard;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Base_Theme {

	/**
	 * [get_page_slug description]
	 * @return [type] [description]
	 */
	abstract public function get_slug();

	/**
	 * [__construct description]
	 * @param array $args [description]
	 */
	public function __construct( $args = array() ) {

		$this->init();

		add_filter( 'jet-dashboard/data-manager/theme-info', array( $this, 'theme_info_data' ) );
	}

	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public function init() {}

	/**
	 * [theme_info_data description]
	 * @param  array  $theme_data [description]
	 * @return [type]             [description]
	 */
	public function theme_info_data( $theme_data = array() ) {
		return $theme_data;
	}

}
