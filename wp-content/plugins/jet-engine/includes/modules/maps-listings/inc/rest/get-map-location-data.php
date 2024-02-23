<?php
namespace Jet_Engine\Modules\Maps_Listings;

/**
 * Get_Map_Location_Data endpoint
 */
class Get_Map_Location_Data extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-map-location-data';
	}

	/**
	 * API callback
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 */
	public function callback( $request ) {

		$params  = $request->get_params();
		$address = $params['address'];

		if ( ! $address ) {
			return rest_ensure_response( array(
				'success' => false,
				'html'    => __( 'Required parameters is not found in request', 'jet-engine' ),
			) );
		}

		$provider_id      = Module::instance()->settings->get( 'geocode_provider' );
		$geocode_provider = Module::instance()->providers->get_providers( 'geocode', $provider_id );

		if ( ! $geocode_provider ) {
			return rest_ensure_response( array(
				'success' => false,
				'html'    => __( 'Required parameters is not found in request', 'jet-engine' ),
			) );
		}

		$location = $geocode_provider->get_location_data( $address );

		if ( empty( $location ) ) {
			return rest_ensure_response( array(
				'success' => false,
				'html'    => __( 'Location not found.', 'jet-engine' ),
			) );
		}

		$result = array(
			'success' => true,
			'data'    => $location,
		);

		return rest_ensure_response( $result );

	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELETE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'GET';
	}

	/**
	 * Check user access to current end-point
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array(
			'address' => array(
				'default'  => '',
				'required' => true,
			),
		);
	}

}