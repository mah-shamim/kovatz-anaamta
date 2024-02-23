<?php
namespace Jet_Engine\Modules\Maps_Listings;

/**
 * Get_Map_Autocomplete_Data endpoint
 */
class Get_Map_Autocomplete_Data extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-map-autocomplete-data';
	}

	/**
	 * API callback
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 */
	public function callback( $request ) {

		$params = $request->get_params();
		$query  = $params['query'];

		if ( ! $query ) {
			return rest_ensure_response( array(
				'success' => false,
				'html'    => __( 'Required parameters is not found in request', 'jet-engine' ),
				'data'    => [],
			) );
		}

		$provider_id      = Module::instance()->settings->get( 'geocode_provider' );
		$geocode_provider = Module::instance()->providers->get_providers( 'geocode', $provider_id );

		if ( ! $geocode_provider ) {
			return rest_ensure_response( array(
				'success' => false,
				'html'    => __( 'Required parameters is not found in request', 'jet-engine' ),
				'data'    => [],
			) );
		}

		$data = $geocode_provider->get_autocomplete_data( $query );

		if ( preg_match( '/^\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)\s*$/', $query, $matches ) ) {

			if ( empty( $data ) ) {
				$data = array();
			}
			
			$lat = trim( $matches[1] );
			$lng = trim( $matches[2] );
			
			if ( absint( $lat ) <= 90 && absint( $lng ) <= 180 ) {
				array_unshift( $data, array(
					'address' => $query,
					'lat'     => $lat,
					'lng'     => $lng,
				) );	
			}

		}

		if ( empty( $data ) ) {
			return rest_ensure_response( array(
				'success' => false,
				'html'    => __( 'No Results Found', 'jet-engine' ),
				'data'    => [],
			) );
		}

		$result = array(
			'success' => true,
			'data'    => $data,
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
		return true;
		//return current_user_can( 'edit_posts' );
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array(
			'query' => array(
				'default'  => '',
				'required' => true,
			),
		);
	}

}
