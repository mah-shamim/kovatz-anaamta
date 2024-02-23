<?php
namespace Jet_Engine\Modules\Maps_Listings\Filters\Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define User_Geolocation class
 */
class User_Geolocation extends \Jet_Smart_Filters_Filter_Base {

	/**
	 * Get provider name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'User Geolocation', 'jet-engine' );
	}

	/**
	 * Get provider ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'user-geolocation';
	}

	/**
	 * Get icon URL
	 * 
	 * @return string
	 */
	public function get_icon_url() {

		return jet_engine()->plugin_url( 'assets/img/jet-smart-filters-types/user-geolocation.png' );
	}

	/**
	 * Get provider wrapper selector
	 *
	 * @return string
	 */
	public function get_scripts() {
		return array( 'jet-maps-listings-user-geolocation' );
	}

	public function get_template( $args = array() ) {
		return jet_engine()->modules->modules_path( 'maps-listings/inc/filters/types/user-geolocation-template.php' );
	}

	/**
	 * Prepare filter template argumnets
	 *
	 * @param  [type] $args [description]
	 *
	 * @return [type]       [description]
	 */
	public function prepare_args( $args ) {

		$filter_id            = $args['filter_id'];
		$content_provider     = isset( $args['content_provider'] ) ? $args['content_provider'] : false;
		$additional_providers = isset( $args['additional_providers'] ) ? $args['additional_providers'] : false;

		if ( ! $filter_id ) {
			return false;
		}

		return array(
			'options'              => false,
			'query_type'           => 'geo_query',
			'query_var'            => false,
			'query_var_suffix'     => jet_smart_filters()->filter_types->get_filter_query_var_suffix( $filter_id ),
			'content_provider'     => $content_provider,
			'additional_providers' => $additional_providers,
			'filter_id'            => $filter_id,
			'apply_type'           => 'ajax',
		);

	}

}
