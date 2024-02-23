<?php


namespace Jet_Engine\Modules\Forms\Tabs;


abstract class Base_Form_Tab {

	public $prefix = 'jet_engine_form_settings__';

	abstract public function slug();

	abstract public function on_get_request();

	abstract public function on_load();

	public function get_options( $default = array() ) {
		$response = get_option( $this->prefix . $this->slug(), false );

		$response = $response
			? json_decode( $response, true )
			: array();

		return array_merge( $default, $response );
	}

	public function update_options( $options ) {
		$options = json_encode( $options );

		return update_option( $this->prefix . $this->slug(), $options );
	}

	public function render_assets() {
	}

	public function verify_request() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['_nonce'] ) ? $_REQUEST['_nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, jet_engine()->dashboard->get_nonce_action() ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Nonce validation failed', 'jet-engine' ) ) );
		}

	}

}