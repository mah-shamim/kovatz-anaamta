<?php
namespace Jet_Engine\Modules\Maps_Listings\Geocode_Providers;

class OpenStreetMap extends Base {

	public function build_api_url( $location ) {
		return add_query_arg(
			array(
				'q'      => urlencode( $location ),
				'format' => 'json',
			),
			'https://nominatim.openstreetmap.org/search'
		);
	}

	/**
	 * Build Reverse geocoding API URL for given coordinates point
	 * @return [type] [description]
	 */
	public function build_reverse_api_url( $point = array() ) {
		return add_query_arg( array(
			'lat'    => $point['lat'],
			'lon'    => $point['lng'],
			'format' => 'json',
		), 'https://nominatim.openstreetmap.org/reverse' );
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
		return isset( $data['display_name'] ) ? $data['display_name'] : false;
	}

	/**
	 * Find coordinates in the response data and return it
	 *
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function extract_coordinates_from_response_data( $data = array() ) {

		$coord = isset( $data[0] )
			? array( 'lat' => $data[0]['lat'], 'lng' => $data[0]['lon'] )
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

		if ( empty( $data ) ) {
			return false;
		}

		$result = array();

		foreach ( $data as $prediction ) {
			$result[] = array(
				'address' => $prediction['display_name'],
				'lat'     => $prediction['lat'],
				'lng'     => $prediction['lon'],
			);
		}

		return $result;
	}

	/**
	 * Returns provider system slug
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'openstreetmap';
	}

	/**
	 * Returns provider human-readable name
	 *
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'OpenStreetMap', 'jet-engine' );
	}

	/**
	 * Provider-specific settings fields template
	 *
	 * @return [type] [description]
	 */
	public function settings_fields() {
		?>
		<template
			v-if="'openstreetmap' === settings.geocode_provider"
		>
			<cx-vui-component-wrapper
				label="<?php _e( 'Note:', 'jet-engine' ); ?>"
				description="<?php _e( 'Be aware that this service runs on donated servers and has a very limited capacity. So please avoid heavy uses (an absolute maximum of 1 request per second).', 'jet-engine' ); ?>"
			></cx-vui-component-wrapper>
		</template>
		<?php
	}

}
