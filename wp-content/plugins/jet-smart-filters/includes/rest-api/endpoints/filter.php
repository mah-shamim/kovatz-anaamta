<?php
namespace Jet_Smart_Filters\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Filter extends Base {

	public function get_name() {

		return 'filter';
	}

	public function get_args() {

		return array(
			'id' => array(
				'required' => true,
			),
			'data' => array(
				'default'  => false,
				'required' => false,
			),
		);
	}

	public function callback( $request ) {

		global $wpdb;

		$args     = $request->get_params();
		$filterID = $args['id'];
		$new_data = $args['data'];
		$response = false;

		if ( ! $new_data ) {
			// Get filter
			$response = jet_smart_filters()->services->filter->get( $filterID );
		} else {
			// Update filter
			$response = jet_smart_filters()->services->filter->update( $filterID, $new_data );
		}

		return rest_ensure_response( $response );
	}
}
