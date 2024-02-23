<?php
namespace Jet_Engine\Modules\Maps_Listings;

/**
 * Get_Map_Point_Data endpoint
 */
class Get_Map_Point_Data extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-map-point-data';
	}

	/**
	 * API callback
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 */
	public function callback( $request ) {

		$params = $request->get_params();
		$lat    = $params['lat'];
		$lng    = $params['lng'];

		if ( ! $lat || ! $lng ) {
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

		$location = $geocode_provider->get_reverse_location_data( array( 'lat' => $lat, 'lng' => $lng ) );

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
	 * Check user access to current end-point.
	 *
	 * With nonce header this endpoint can be used on public.
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {

		$nonce = $request->get_header( 'nonce' );

		if ( $nonce && wp_verify_nonce( $nonce, 'jet-map-field' ) ) {
			return true;
		}

		return current_user_can( 'edit_posts' );
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array(
			'lat' => array(
				'default'  => '',
				'required' => true,
			),
			'lng' => array(
				'default'  => '',
				'required' => true,
			),
		);
	}

}