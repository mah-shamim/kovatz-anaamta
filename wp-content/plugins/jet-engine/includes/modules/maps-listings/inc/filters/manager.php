<?php
namespace Jet_Engine\Modules\Maps_Listings\Filters;

use Jet_Engine\Modules\Maps_Listings\Module;

class Manager {

	public function __construct() {

		require jet_engine()->modules->modules_path( 'maps-listings/inc/filters/types/location-distance-render.php' );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_geolocation_assets' ) );

		add_filter( 'jet-smart-filters/query/final-query', array( $this, 'adjust_geo_query' ) );

		add_action( 'jet-smart-filters/providers/register', array( $this, 'register_filters_provider' ) );
		add_action( 'jet-smart-filters/filter-types/register', array( $this, 'register_filter_types' ) );
		add_filter( 'jet-smart-filters/admin/settings-data', array( $this, 'adjust_editor_settings' ) );

		add_action( 'jet-engine/elementor-views/widgets/register', array( $this, 'register_widgets' ), 20, 2 );

		add_action( 'enqueue_block_editor_assets', array( $this, 'register_blocks_assets' ), 9 );
		add_action( 'init', array( $this, 'register_blocks_types' ), 999 );
		add_action( 'jet-smart-filters/blocks/localized-data', array( $this, 'modify_filters_localized_data' ) );

		add_action( 'init', array( $this, 'register_bricks_types' ), 999 );

		// general query var
		add_filter( 'jet-smart-filters/query/vars', array( $this, 'register_query_var' ) );
		// query var for permalink parser
		add_filter( 'jet-smart-filters/render/query-vars', array( $this, 'register_query_var' ) );
		// parse geo query variable before storing to request
		add_filter( 'jet-smart-filters/render/set-query-var', array( $this, 'parse_query_var' ), 10, 2 );

		add_filter( 'jet-smart-filter/utils/merge-query-args/merged-keys', array( $this, 'add_merged_query_key' ) );

	}

	public function parse_query_var( $value, $var ) {
		
		if ( 'geo_query' === $var && $value ) {
			
			$parsed_value = array();
			
			foreach ( explode( ';', $value ) as $row ) {
				$row_data = explode( ':', $row, 2 );
				$parsed_value[ $row_data[0] ] = $row_data[1];
			}

			$value = $parsed_value;

		}

		return $value;
	}

	public function adjust_editor_settings( $settings ) {

		if ( ! empty( $settings['settings']['_query_var']['conditions']['_filter_type!'] ) ) {
			$settings['settings']['_query_var']['conditions']['_filter_type!'][] = 'location-distance';
			$settings['settings']['_query_var']['conditions']['_filter_type!'][] = 'user-geolocation';
		}

		return $settings;
	}

	public function adjust_geo_query( $query ) {

		// Prepare `geo_query` on Page Reload
		if ( ! empty( $query['geo_query'] ) && is_string( $query['geo_query'] ) ) {
			$raw_geo_query = explode( ';', $query['geo_query'] );
			$geo_query     = array();

			foreach ( $raw_geo_query as $data ) {
				$key_value = explode( ':', $data, 2 );

				if ( ! isset( $key_value[0] ) || ! isset( $key_value[1] ) ) {
					continue;
				}

				$geo_query[ $key_value[0] ] = $key_value[1];
			}

			$query['geo_query'] = $geo_query;
		}

		if ( 
			! empty( $query['geo_query'] ) 
			&& ( empty( $query['geo_query']['latitude'] ) && empty( $query['geo_query']['longitude'] ) )
			&& ( ! empty( $query['geo_query']['address'] ) )
		) {
			
			$address_data = Module::instance()->lat_lng->get_from_transient( $query['geo_query']['address'] );

			if ( ! empty( $address_data ) ) {
				$query['geo_query']['latitude']  = $address_data['lat'];
				$query['geo_query']['longitude'] = $address_data['lng'];
			}

		}

		return $query;
	}

	public function register_blocks_types() {
		
		require jet_engine()->modules->modules_path( 'maps-listings/inc/filters/blocks/user-geolocation.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/filters/blocks/location-distance.php' );

		new Blocks\User_Geolocation();
		new Blocks\Location_Distance();
	}

	public function register_blocks_assets() {

		$this->register_geolocation_assets();

		wp_enqueue_script(
			'jet-maps-listings-geolocation-blocks',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/js/admin/blocks.js' ),
			array( 'wp-blocks','wp-editor', 'wp-components', 'wp-i18n' ),
			jet_engine()->get_version(),
			true
		);

	}

	public function register_bricks_types() {

		if ( ! $this->has_bricks() || ! class_exists( '\Jet_Smart_Filters\Bricks_Views\Elements\Jet_Smart_Filters_Bricks_Base' ) ) {
			return;
		}

		$element_files = array(
			jet_engine()->modules->modules_path( 'maps-listings/inc/filters/bricks-views/user-geolocation.php' ),
			jet_engine()->modules->modules_path( 'maps-listings/inc/filters/bricks-views/location-distance.php' ),
		);

		foreach ( $element_files as $file ) {
			\Bricks\Elements::register_element( $file );
		}
	}

	public function register_query_var( $vars ) {
		$vars[] = 'geo_query';
		return $vars;
	}

	public function register_geolocation_assets() {
		
		wp_register_script(
			'jet-maps-listings-user-geolocation',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/js/public/user-geolocation.js' ),
			array( 'jquery' ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script( 'jet-maps-listings-user-geolocation', 'JetMapListingGeolocationFilterData', array(
			'initEvent' => version_compare( jet_smart_filters()->get_version(), '3.0.0', '>' ) ? 'jet-smart-filters/before-init' : 'DOMContentLoaded',
		) );

		wp_register_script(
			'jet-maps-listings-location-distance',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/js/public/location-distance.js' ),
			array( 'jquery' ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script( 'jet-maps-listings-location-distance', 'JetMapListingLocationDistanceFilterData', array(
			'apiAutocomplete' => jet_engine()->api->get_route( 'get-map-autocomplete-data', true ),
		) );

	}

	public function register_widgets( $widgets_manager, $elementor_views ) {

		$filters_path = jet_engine()->modules->modules_path( 'maps-listings/inc/filters/elementor-widgets/' );

		$elementor_views->register_widget(
			$filters_path . 'user-geolocation.php',
			$widgets_manager,
			__NAMESPACE__ . '\Elementor_Widgets\User_Geolocation'
		);

		$elementor_views->register_widget(
			$filters_path . 'location-distance.php',
			$widgets_manager,
			__NAMESPACE__ . '\Elementor_Widgets\Location_Distance'
		);

	}

	public function register_filter_types( $types_manager ) {

		$filters_path = jet_engine()->modules->modules_path( 'maps-listings/inc/filters/types/' );

		$types_manager->register_filter_type(
			'\Jet_Engine\Modules\Maps_Listings\Filters\Types\User_Geolocation',
			$filters_path . 'user-geolocation.php'
		);

		$types_manager->register_filter_type(
			'\Jet_Engine\Modules\Maps_Listings\Filters\Types\Location_Distance',
			$filters_path . 'location-distance.php'
		);

	}

	/**
	 * Register custom provider for SmartFilters
	 *
	 * @return [type] [description]
	 */
	public function register_filters_provider( $providers_manager ) {
		$providers_manager->register_provider(
			'\Jet_Engine\Modules\Maps_Listings\Filters\Provider',
			jet_engine()->modules->modules_path( 'maps-listings/inc/filters/provider.php' )
		);
	}

	public function modify_filters_localized_data( $data ) {
		$data['providers']['jet-engine-maps'] = __( 'Map Listing', 'jet-engine' );
		return $data;
	}

	public function has_bricks() {
		return defined( 'BRICKS_VERSION' );
	}

	public function add_merged_query_key( $keys ) {
		$keys[] = 'geo_query';
		return $keys;
	}
}
