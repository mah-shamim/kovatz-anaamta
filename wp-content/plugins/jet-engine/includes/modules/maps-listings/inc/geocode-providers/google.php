<?php
namespace Jet_Engine\Modules\Maps_Listings\Geocode_Providers;

use Jet_Engine\Modules\Maps_Listings\Module;

class Google extends Base {

	public function base_api_url() {

		$api_url           = 'https://maps.googleapis.com/maps/api/geocode/json';
		$api_key           = Module::instance()->settings->get( 'api_key' );
		$use_geocoding_key = Module::instance()->settings->get( 'use_geocoding_key' );
		$geocoding_key     = Module::instance()->settings->get( 'geocoding_key' );

		// from 3.0.0 map could have different providers so we need to reset some data if provider is not Google maps
		if ( 'google' !== Module::instance()->settings->get( 'map_provider' ) ) {
			$use_geocoding_key = true;
			$api_key           = false;
		}

		if ( $use_geocoding_key && $geocoding_key ) {
			$api_key = $geocoding_key;
		}

		// Do nothing if api key not provided
		if ( ! $api_key ) {
			return false;
		}

		return add_query_arg(
			array(
				'key'      => urlencode( $api_key ),
				'language' => substr( get_bloginfo( 'language' ), 0, 2 ),
			),
			$api_url
		);
	}

	public function build_api_url( $location = '' ) {
		return add_query_arg( array(
			'address' => urlencode( $location ),
		), $this->base_api_url() );
	}

	/**
	 * Build Reverse geocoding API URL for given coordinates point
	 * @return [type] [description]
	 */
	public function build_reverse_api_url( $point = array() ) {
		return add_query_arg( array(
			'latlng' => implode( ',', $point ),
		), $this->base_api_url() );
	}

	/**
	 * Build Autocomplete API URL for given place predictions
	 * @return mixed
	 */
	public function build_autocomplete_api_url( $query = '' ) {

		$api_url           = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';
		$api_key           = Module::instance()->settings->get( 'api_key' );
		$use_geocoding_key = Module::instance()->settings->get( 'use_geocoding_key' );
		$geocoding_key     = Module::instance()->settings->get( 'geocoding_key' );

		if ( $use_geocoding_key && $geocoding_key ) {
			$api_key = $geocoding_key;
		}

		if ( ! $api_key ) {
			return false;
		}

		return add_query_arg(
			array(
				'input'    => urlencode( $query ),
				'key'      => urlencode( $api_key ),
				'language' => substr( get_bloginfo( 'language' ), 0, 2 ),
				//'sessiontoken' => '', // todo - add sessiontoken to optimize request.
			),
			$api_url
		);
	}

	/**
	 * Find location name in the reverse geocoding response data and return it
	 *
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function extract_location_from_response_data( $data = array() ) {
		return isset( $data['results'][0]['formatted_address'] ) ? $data['results'][0]['formatted_address'] : false;
	}

	/**
	 * Find coordinates in the response data and return it
	 *
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function extract_coordinates_from_response_data( $data = array() ) {

		$coord = isset( $data['results'][0]['geometry']['location'] )
			? $data['results'][0]['geometry']['location']
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

		$predictions = isset( $data['predictions'] ) ? $data['predictions'] : false;

		if ( ! $predictions ) {
			return false;
		}

		$result = array();

		foreach ( $predictions as $prediction ) {
			$result[] = array(
				'address' => $prediction['description']
			);
		}

		return $result;
	}

	/**
	 * Settings assets
	 *
	 * @return [type] [description]
	 */
	public function settings_assets() {

		wp_enqueue_script(
			'jet-engine-maps-settings-google',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/js/admin/settings-google.js' ),
			array( 'cx-vue-ui' ),
			jet_engine()->get_version(),
			true
		);

	}

	/**
	 * Returns provider system slug
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'google';
	}

	/**
	 * Returns provider human-readable name
	 *
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'Google', 'jet-engine' );
	}

	/**
	 * Provider-specific settings fields template
	 *
	 * @return [type] [description]
	 */
	public function settings_fields() {
		?>
		<template
			v-if="'google' === settings.geocode_provider"
		>
			<template v-if="'google' === settings.map_provider">
				<cx-vui-switcher
					label="<?php _e( 'Separate Geocoding API key', 'jet-engine' ); ?>"
						description="<?php _e( 'Use separate key for Geocoding API. This allows you to set more accurate restrictions for your API key.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					@input="updateSetting( $event, 'use_geocoding_key' )"
					:value="settings.use_geocoding_key"
				></cx-vui-switcher>
				<cx-vui-input
					label="<?php _e( 'Geocoding API Key', 'jet-engine' ); ?>"
					description="<?php _e( 'Google maps API key with Geocoding API enabled. For this key <b>Application restrictions</b> should be set to <b>None</b> or <b>IP addresses</b> and in the <b>API restrictions</b> you need to select <b>Don\'t restrict key</b> or enable <b>Geocoding API</b>', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					@on-input-change="updateSetting( $event.target.value, 'geocoding_key' )"
					:value="settings.geocoding_key"
					v-if="settings.use_geocoding_key"
				></cx-vui-input>
			</template>
			<template v-else>
				<cx-vui-input
					label="<?php _e( 'Geocoding API Key', 'jet-engine' ); ?>"
					description="<?php _e( 'Google maps API key with Geocoding API enabled. For this key <b>Application restrictions</b> should be set to <b>None</b> or <b>IP addresses</b> and in the <b>API restrictions</b> you need to select <b>Don\'t restrict key</b> or enable <b>Geocoding API</b>', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					@on-input-change="updateSetting( $event.target.value, 'geocoding_key' )"
					:value="settings.geocoding_key"
				></cx-vui-input>
			</template>
			<jet-engine-maps-google-validate-api-key
				:settings="settings"
			/>
		</template>
		<?php
	}

}
