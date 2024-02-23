<?php
namespace Jet_Engine\Modules\Maps_Listings;

class Providers_Manager {

	private $_map_providers     = array();
	private $_geocode_providers = array();

	public function __construct() {

		require jet_engine()->modules->modules_path( 'maps-listings/inc/base-provider.php' );

		$this->register_geocode_providers();
		$this->register_map_providers();

	}

	public function get_active_map_provider() {
		$provider_id = Module::instance()->settings->get( 'map_provider' );
		return $this->get_providers( 'map', $provider_id );
	}

	/**
	 * Register all geocoding providers
	 *
	 * @return [type] [description]
	 */
	public function register_geocode_providers() {

		require jet_engine()->modules->modules_path( 'maps-listings/inc/geocode-providers/base.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/geocode-providers/google.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/geocode-providers/openstreetmap.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/geocode-providers/photon.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/geocode-providers/bing.php' );

		$this->register_provider( new Geocode_Providers\Google(), 'geocode' );
		$this->register_provider( new Geocode_Providers\OpenStreetMap(), 'geocode' );
		$this->register_provider( new Geocode_Providers\Photon(), 'geocode' );
		$this->register_provider( new Geocode_Providers\Bing(), 'geocode' );

		do_action( 'jet-engine/maps-listing/register-geocode-providers', $this );

	}

	/**
	 * Register all map providers
	 *
	 * @return [type] [description]
	 */
	public function register_map_providers() {

		require jet_engine()->modules->modules_path( 'maps-listings/inc/map-providers/base.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/map-providers/google.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/map-providers/leaflet.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/map-providers/mapbox.php' );

		$this->register_provider( new Providers\Google(), 'map' );
		$this->register_provider( new Providers\Leaflet(), 'map' );
		$this->register_provider( new Providers\Mapbox(), 'map' );

		do_action( 'jet-engine/maps-listing/register-map-providers', $this );

	}

	/**
	 * Register new provider instance
	 *
	 * @return [type] [description]
	 */
	public function register_provider( $provider, $source = 'map' ) {

		if ( 'geocode' === $source ) {
			$this->_geocode_providers[ $provider->get_id() ] = $provider;
		} else {
			$this->_map_providers[ $provider->get_id() ] = $provider;
		}

	}

	/**
	 * Returns all providers list or provider object by ID
	 *
	 * @param  [type] $provider_id [description]
	 * @return [type]              [description]
	 */
	public function get_providers( $source = 'map', $provider_id = null ) {

		$providers = ( 'geocode' === $source ) ? $this->_geocode_providers : $this->_map_providers;

		if ( ! $provider_id ) {
			return $providers;
		} else {
			return isset( $providers[ $provider_id ] ) ? $providers[ $provider_id ] : false;
		}

	}

	/**
	 * Get providers list for JS
	 *
	 * @return [type] [description]
	 */
	public function get_providers_for_js( $source = 'map' ) {

		$result = array();

		foreach ( $this->get_providers( $source ) as $provider ) {
			$result[] = array(
				'value' => $provider->get_id(),
				'label' => $provider->get_label(),
			);
		}

		return $result;

	}

}
