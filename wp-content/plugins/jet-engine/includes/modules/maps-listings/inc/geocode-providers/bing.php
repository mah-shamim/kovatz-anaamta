<?php
namespace Jet_Engine\Modules\Maps_Listings\Geocode_Providers;

use Jet_Engine\Modules\Maps_Listings\Module;

class Bing extends Base {

	public function build_api_url( $location = '' ) {

		$api_url = 'https://dev.virtualearth.net/REST/v1/Locations/';
		$api_key = Module::instance()->settings->get( 'bing_key' );

		// Do nothing if api key not provided
		if ( ! $api_key ) {
			return false;
		}

		return add_query_arg(
			array(
				'query' => urlencode( $location ),
				'key'   => urlencode( $api_key )
			),
			$api_url
		);
	}

	/**
	 * Build Reverse geocoding API URL for given coordinates point
	 * @return [type] [description]
	 */
	public function build_reverse_api_url( $point = array() ) {
		
		$api_url = 'https://dev.virtualearth.net/REST/v1/Locations/';
		$api_key = Module::instance()->settings->get( 'bing_key' );

		// Do nothing if api key not provided
		if ( ! $api_key ) {
			return false;
		}

		return add_query_arg(
			array(
				'key' => urlencode( $api_key )
			),
			$api_url . implode( ',', $point )
		);
	}

	/**
	 * Build Autocomplete API URL for given place predictions
	 * @return mixed
	 */
	public function build_autocomplete_api_url( $query = '' ) {
		return $this->build_api_url( $query );
	}

	/**
	 * Find location name in the reverse geocoding response data and return it
	 *
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function extract_location_from_response_data( $data = array() ) {

		if ( empty( $data['resourceSets'][0]['resources'][0] ) ) {
			return false;
		}

		$data = $data['resourceSets'][0]['resources'][0];

		return isset( $data['name'] ) ? $data['name'] : false;
	}

	/**
	 * Find coordinates in the response data and return it
	 *
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function extract_coordinates_from_response_data( $data = array() ) {

		if ( empty( $data['resourceSets'][0]['resources'][0] ) ) {
			return false;
		}

		$data = $data['resourceSets'][0]['resources'][0];

		$coord = isset( $data['point'] )
			? array( 'lat' => $data['point']['coordinates'][0], 'lng' => $data['point']['coordinates'][1] )
			: false;

		if ( ! $coord ) {
			return false;
		}

		return $coord;

	}

	/**
	 * Find place predictions in the response data and return it
	 *
	 * @param  array $data
	 * @return array|false
	 */
	public function extract_autocomplete_data_from_response_data( $data = array() ) {

		if ( empty( $data['resourceSets'] ) ) {
			return false;
		}

		if ( empty( $data['resourceSets'][0]['resources'] ) ) {
			return false;
		}

		$resources = $data['resourceSets'][0]['resources'];

		$result = array();

		foreach ( $resources as $resource ) {

			$properties = isset( $resource['address'] ) ? $resource['address'] : false;

			if ( ! $properties ) {
				$address = $resource['name'];
			} else {
				$address = implode( ', ', array_filter( array(
					$resource['name'],
					$this->get_prop( $properties, 'adminDistrict2' ),
					$this->get_prop( $properties, 'adminDistrict' ),
				) ) );
			}

			$result[] = array(
				'address' => $address,
				'lat'     => $resource['point']['coordinates'][0],
				'lng'     => $resource['point']['coordinates'][1],
			);
		}

		return $result;
	}

	public function get_prop( $properties, $key ) {
		return isset( $properties[ $key ] ) ? $properties[ $key ] : null;
	}

	/**
	 * Returns provider system slug
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'bing';
	}

	/**
	 * Returns provider human-readable name
	 *
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'Bing', 'jet-engine' );
	}

	/**
	 * Provider-specific settings fields template
	 *
	 * @return [type] [description]
	 */
	public function settings_fields() {
		?>
		<template
			v-if="'bing' === settings.geocode_provider"
		>
			<cx-vui-input
				label="<?php _e( 'Bing API Key', 'jet-engine' ); ?>"
				description="<?php _e( 'API key instructions', 'jet-engine' ); ?> - <a href='https://www.microsoft.com/en-us/maps/create-a-bing-maps-key' target='_blank'>https://www.microsoft.com/en-us/maps/create-a-bing-maps-key</a>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				@on-input-change="updateSetting( $event.target.value, 'bing_key' )"
				:value="settings.bing_key"
			></cx-vui-input>
		</template>
		<?php
	}

}
