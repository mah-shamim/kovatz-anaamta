<?php
namespace Jet_Smart_Filters\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Posts class
 */
class Plugin_Settings extends Base {
	/**
	 * Returns route name
	 */
	public function get_name() {

		return 'plugin-settings';
	}

	public function callback( $request ) {

		$data = array_map(
			function( $setting ) {
				return is_array( $setting ) ? $setting : esc_attr( $setting );
			},
			$request->get_params()
		);

		update_option( jet_smart_filters()->settings->key, $data );

		return rest_ensure_response( [
			'status'  => 'success',
			'message' => __( 'Settings have been saved', 'jet-smart-filters' ),
		] );
	}

	/**
	 * Check user access to current end-popint
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}
}
