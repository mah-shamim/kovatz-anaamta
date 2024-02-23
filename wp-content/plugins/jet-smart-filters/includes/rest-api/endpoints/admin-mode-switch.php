<?php
namespace Jet_Smart_Filters\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class AdminModeSwitch extends Base {

	public function get_name() {

		return 'admin-mode-switch';
	}

	public function get_args() {

		return array(
			'mode' => array(
				'required' => true,
			)
		);
	}

	public function callback( $request ) {

		$args = $request->get_params();

		// Mode
		$mode = $args['mode'];

		return jet_smart_filters()->settings->update( 'admin_mode', $mode );
	}

	/**
	 * Check user access to current end-popint
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}
}
