<?php


namespace Jet_Engine\Modules\Rest_API_Listings;

use Jet_Engine\Modules\Rest_API_Listings\Module;
use Jet_Form_Builder\Actions\Action_Handler;
use Jet_Form_Builder\Actions\Types\Base;
use Jet_Form_Builder\Exceptions\Action_Exception;

class Jet_Action extends Base {

	/**
	 * @return mixed
	 */
	public function get_id() {
		return 'rest_api_request';
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return __( 'REST API Request', 'jet-engine' );
	}

	public function action_data() {
		$auth_types = Module::instance()->auth_types->get_types_for_js();

		return array(
			'auth_types' => $auth_types,
		);
	}

	/**
	 * @return mixed
	 */
	public function visible_attributes_for_gateway_editor() {
		return array();
	}

	/**
	 * @return mixed
	 */
	public function self_script_name() {
		return 'JetEngineRestApi';
	}

	/**
	 * @return mixed
	 */
	public function editor_labels() {
		return array(
			'url'                 => __( 'REST API URL:', 'jet-engine' ),
			'body'                => __( 'Custom Body:', 'jet-engine' ),
			'authorization'       => __( 'Authorization:', 'jet-engine' ),
			'auth_type'           => __( 'Authorization type:', 'jet-engine' ),
			'rapidapi_key'        => __( 'RapidAPI Key:', 'jet-engine' ),
			'rapidapi_host'       => __( 'RapidAPI Host:', 'jet-engine' ),
			'application_pass'    => __( 'User:password string:', 'jet-engine' ),
			'bearer_token'        => __( 'Bearer token:', 'jet-engine' ),
			'custom_header_name'  => __( 'Header name', 'jet-engine' ),
			'custom_header_value' => __( 'Header value', 'jet-engine' ),
		);
	}

	public function editor_labels_help() {
		return array(
			'url'                => __(
				'You can use these macros as dynamic part of the URL: %field_name%',
				'jet-engine'
			),
			'body'               => __(
				'By default API request will use all form data as body. Here you can set custom body 
				of your API request in the JSON format. 
				<a href="https://www.w3dnetwork.com/json-formatter.html" target="_blank">Online editing tool</a> 
				- swith to the <b><i>Tree View</i></b>, edit object as you need, than swith to 
				<b><i>Plain Text</i></b> and copy/paste result here. 
				You can use the same macros as for the URL.',
				'jet-engine'
			),
			'application_pass'   => __( 'Set application user and password separated with `:`', 'jet-engine' ),
			'rapidapi_key'       => __( 'X-RapidAPI-Key from endpoint settings at the rapidapi.com', 'jet-engine' ),
			'rapidapi_host'      => __( 'X-RapidAPI-Host from endpoint settings at the rapidapi.com', 'jet-engine' ),
			'bearer_token'       => __( 'Set token for Bearer Authorization type', 'jet-engine' ),
			'custom_header_name' => __( 'Set authorization header name. Could be found in your API docs', 'jet-engine' ),
			'custom_header_value' => __( 'Set authorization header value. Could be found in your API docs or you user profile related to this API', 'jet-engine' ),
		);
	}

	/**
	 * @param array $request
	 * @param Action_Handler $handler
	 *
	 * @return void
	 * @throws Action_Exception
	 */
	public function do_action( array $request, Action_Handler $handler ) {
		$endpoint = ! empty( $this->settings ) ? $this->settings : array();

		if ( ! empty( $endpoint['body'] ) ) {
			$body = Module::instance()->form->prepare_body( $endpoint['body'], $request );
			unset( $endpoint['body'] );
		} else {
			$body = $request;
		}

		$endpoint['args'] = array(
			'body' => $body,
		);

		$endpoint['url'] = Module::instance()->form->prepate_url( $endpoint['url'], $request );

		/**
		 * Allow to filter endpoint data before sending the request
		 *
		 * @var array
		 */
		$endpoint = apply_filters( 'jet-engine/rest-api-listings/form-notification/endpoint-data', $endpoint, $handler );

		Module::instance()->request->set_endpoint( $endpoint );
		$response = Module::instance()->request->send_request( array(), 'post' );

		$error_prefix = __( 'REST API error: ', 'jet-engine' );

		if ( is_wp_error( $response ) ) {
			throw ( new Action_Exception(
				$error_prefix . $response->get_error_message()
			) )->dynamic_error();
		}

		$code = (int) wp_remote_retrieve_response_code( $response );

		if ( 400 <= $code ) {
			throw ( new Action_Exception(
				$error_prefix . wp_remote_retrieve_response_message( $response )
			) )->dynamic_error();
		}

	}

}
