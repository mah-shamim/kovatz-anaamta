<?php
/**
 * AI requests handler class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Engine_AI_Handler {

	private $action          = 'jet_engine_ai_request';
	private $url             = 'https://account.crocoblock.com/';
	private $matched_license = null;
	private $is_lifetime     = null;
	private $limit           = 0;

	public function __construct() {
		add_action( 'wp_ajax_' . $this->get_action(), [ $this, 'process_request' ] );
	}

	/**
	 * Check if given AI source allowed or not
	 * @param  string  $mode [description]
	 * @return boolean       [description]
	 */
	public function is_ai_allowed( $source = 'sql' ) {
		return apply_filters( 'jet-engine/ai/' . $source, true );
	}

	public function get_matched_license() {

		if ( null !== $this->matched_license ) {
			return $this->matched_license;
		}

		$license     = false;
		$licenses    = \Jet_Dashboard\Utils::get_license_list();
		$is_lifetime = false;
		$limit       = 0;

		foreach ( $licenses as $license_key => $license_data ) {
			
			if ( 'active' === $license_data['licenseStatus']
				&& 'crocoblock' === $license_data['licenseDetails']['type']
				&& 'lifetime' === $license_data['licenseDetails']['product_category']
			) {
				$license     = $license_key;
				$is_lifetime = true;
				$limit       = 30;
			} else {

				$license = \Jet_Dashboard\Utils::get_plugin_license_key( 'jet-engine/jet-engine.php' );

				if ( $license ) {
					$is_lifetime = false;
					$limit       = 5;
				}

			}

		}

		$this->matched_license = $license;
		$this->is_lifetime     = $is_lifetime;
		$this->limit           = $limit;

		return $this->matched_license;

	}

	public function get_is_lifetime() {

		// Ensure all props is set
		$this->get_matched_license();

		return $this->is_lifetime;
	}

	public function get_ai_limit() {

		// Ensure all props is set
		$this->get_matched_license();

		return $this->limit;
	}

	public function process_request() {

		// available ony for admins so far
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'You not have permissions to make AI requests' );
		}

		if ( empty( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], $this->get_action() ) ) {
			wp_send_json_error( 'Incorrect request' );
		}

		$prompt = ! empty( $_REQUEST['prompt'] ) ? wp_kses_post( $_REQUEST['prompt'] ) : '';

		if ( ! $prompt ) {
			wp_send_json_error( 'Empty prompt' );
		}

		$max_length = 400;

		if ( $max_length < strlen( $prompt ) ) {
			wp_send_json_error( 'Prompt is too long. Limit is ' . $max_length . ' chars' );
		}

		$source  = ! empty( $_REQUEST['source'] ) ? esc_attr( $_REQUEST['source'] ) : 'sql';
		$license = $this->get_matched_license();

		if ( ! $license ) {
			wp_send_json_error( 'You do not have activated appropriate Crocoblock license' );
		}

		$response = wp_remote_get( add_query_arg( [
			'ai_api'  => $source,
			'prompt'  => $prompt,
			'license' => $license
		], $this->url ), [
			'timeout' => 60,
		] );

		$body = wp_remote_retrieve_body( $response );

		if ( ! $body ) {
			wp_send_json_error( 'Empty response' );
		}

		$body = json_decode( $body, true );

		if ( empty( $body ) ) {
			wp_send_json_error( 'Incorrect response format' );
		}

		if ( empty( $body['success'] ) ) {
			wp_send_json_error( $body['data'] );
		}

		$completion = $this->prepare_completion( $body['data']['completion'] );

		wp_send_json_success( [
			'completion'     => $completion,
			'requests_used'  => $body['data']['usage'],
			'requests_limit' => $body['data']['limit'],
			'rows'           => substr_count( $completion, "\n" ),
		] );

	}

	public function prepare_completion( $completion, $source = 'sql' ) {
		
		$completion = ltrim( $completion, "\n" );

		switch ( $source ) {
			case 'sql':
				
				$completion = str_replace( 
					[ 'wp_', 'META_KEY', 'META_VALUE' ],
					[ '{prefix}', 'meta_key', 'meta_value' ],
					$completion 
				);

				break;
		}

		return $completion;
	}

	public function get_action() {
		return $this->action;
	}

	public function get_nonce() {
		return wp_create_nonce( $this->get_action() );
	}

}
