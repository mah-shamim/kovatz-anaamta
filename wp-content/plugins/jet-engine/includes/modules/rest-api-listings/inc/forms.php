<?php
namespace Jet_Engine\Modules\Rest_API_Listings;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Forms compatibility class
 */
class Forms {

	public $slug = 'rest_api_request';

	public function __construct() {

		add_action(
			'jet-engine/forms/editor/before-assets',
			array( $this, 'assets' )
		);

		add_filter(
			'jet-engine/forms/booking/notification-types',
			array( $this, 'register_notification' )
		);

		add_action(
			'jet-engine/forms/booking/notifications/fields-after',
			array( $this, 'notification_fields' )
		);

		add_filter(
			'jet-engine/forms/booking/notification/' . $this->slug,
			array( $this, 'handle_notification' ),
			1, 2
		);

	}

	/**
	 * Register notification assets
	 * @return [type] [description]
	 */
	public function assets() {

		wp_enqueue_script(
			'jet-engine-rest-notification',
			Module::instance()->module_url( 'assets/js/admin/form-notification.js' ),
			array(),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script(
			'jet-engine-rest-notification',
			'JetEngineRestData',
			array(
				'auth_types'  => array_merge(
					array(
						array(
							'value' => '',
							'label' => __( 'Select type...', 'jet-engine' ),
						),
					),
					Module::instance()->auth_types->get_types_for_js()
				),
			)
		);

		wp_enqueue_style(
			'jet-engine-rest-notification',
			Module::instance()->module_url( 'assets/css/form-notification.css' ),
			array(),
			jet_engine()->get_version()
		);

		add_action( 'admin_footer', array( $this, 'notification_component_template' ) );

	}

	/**
	 * Print notification component template
	 *
	 * @return [type] [description]
	 */
	public function notification_component_template() {

		ob_start();
		include Module::instance()->module_path( 'templates/admin/notification-component.php' );
		$content = ob_get_clean();

		printf(
			'<script type="text/x-template" id="jet-rest-notification">%s</script>',
			$content
		);

	}

	/**
	 * Register new notification type
	 *
	 * @return [type] [description]
	 */
	public function register_notification( $notifications ) {
		$notifications[ $this->slug ] = __( 'REST API Request', 'jet-engine' );
		return $notifications;
	}

	/**
	 * Render additional notification fields
	 *
	 * @return [type] [description]
	 */
	public function notification_fields() {
		$action_slug = $this->slug;
		include Module::instance()->module_path( 'templates/admin/notification-fields.php' );
	}

	public function get_macros_regex() {
		return '/%(.*?)(\|([a-zA-Z0-9\(\)_-]+))?%/';
	}

	/**
	 * Prepare request body
	 * @param  [type] $raw_body [description]
	 * @param  [type] $data     [description]
	 * @return [type]           [description]
	 */
	public function prepare_body( $raw_body, $data ) {

		$body = preg_replace_callback( $this->get_macros_regex(), function( $match ) use ( $data ) {

			if ( isset( $data[ $match[1] ] ) ) {

				if ( jet_engine()->listings && ! empty( $match[3] ) ) {
					return jet_engine()->listings->filters->apply_filters(
						$data[ $match[1] ], $match[3]
					);
				} else {
					if ( is_array( $data[ $match[1] ] ) ) {
						return json_encode( $data[ $match[1] ] );
					} else {
						return $data[ $match[1] ];
					}
				}
			} else {
				return $match[0];
			}
		}, $raw_body );

		$body = wp_specialchars_decode( sanitize_text_field( $body ), ENT_COMPAT );

		return json_decode( $body, true );

	}

	/**
	 * Prepare request body
	 * @param  [type] $raw_body [description]
	 * @param  [type] $data     [description]
	 * @return [type]           [description]
	 */
	public function prepate_url( $url, $data ) {

		if ( ! $url ) {
			return $url;
		}

		return preg_replace_callback( $this->get_macros_regex(), function( $match ) use ( $data ) {

			if ( isset( $data[ $match[1] ] ) ) {

				if ( jet_engine()->listings && ! empty( $match[3] ) ) {
					return jet_engine()->listings->filters->apply_filters(
						$data[ $match[1] ], $match[3]
					);
				} else {
					if ( is_array( $data[ $match[1] ] ) ) {
						return $match[0];
					} else {
						return $data[ $match[1] ];
					}
				}
			} else {
				return $match[0];
			}
		}, $url );

	}

	/**
	 * Handle form notification
	 *
	 * @return [type] [description]
	 */
	public function handle_notification( $args, $notifications ) {

		$endpoint = ! empty( $args['rest_api'] ) ? $args['rest_api'] : array();

		if ( ! empty( $endpoint['body'] ) ) {
			$body = $this->prepare_body( $endpoint['body'], $notifications->data );
			unset( $endpoint['body'] );
		} else {
			$body = $notifications->data;
		}

		$endpoint['args'] = array(
			'body' => $body,
		);

		$endpoint['url'] = $this->prepate_url( $endpoint['url'], $notifications->data );

		/**
		 * Allow to filter endpoint data before sending the request
		 * @var array
		 */
		$endpoint = apply_filters( 'jet-engine/rest-api-listings/form-notification/endpoint-data', $endpoint, $args, $notifications );

		Module::instance()->request->set_endpoint( $endpoint );
		$response = Module::instance()->request->send_request( array(), 'post' );

		$error_prefix = __( 'REST API error: ', 'jet-engine' );

		if ( is_wp_error( $response ) ) {
			$notifications->log[] = $notifications->set_specific_status( $error_prefix . $response->get_error_message() );
			return false;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {

			$notifications->log[] = $notifications->set_specific_status(
				$error_prefix . wp_remote_retrieve_response_message( $response )
			);

			return false;
		}

		$notifications->log[] = true;
		return true;

	}

}
