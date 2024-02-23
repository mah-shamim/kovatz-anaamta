<?php
namespace Jet_Engine\Modules\Rest_API_Listings;

class Request {

	private $endpoint;
	private $url;
	private $error;
	private $error_details;

	public $is_sample_request = false;

	public function set_endpoint( $endpoint = array(), $is_sample_request = false ) {

		$this->endpoint          = $endpoint;
		$this->is_sample_request = $is_sample_request;
		$this->url               = ! empty( $this->endpoint['url'] ) ? $this->endpoint['url'] : false;
		$this->url               = do_shortcode( jet_engine()->listings->macros->do_macros( $this->url ) );

		return $this;
	}

	public function get_url() {
		return apply_filters( 'jet-engine/rest-api-listings/request/url', $this->url, $this );
	}

	public function get_endpoint() {
		return $this->endpoint;
	}

	public function get_error() {
		return $this->error;
	}

	public function set_error( $error ) {
		$this->error = $error;
	}

	public function get_error_details() {
		return $this->error_details;
	}

	public function set_error_details( $error_details ) {
		$this->error_details = $error_details;
	}

	public function send_request( $query_args = array(), $type = 'get' ) {

		do_action( 'jet-engine/rest-api-listings/request/before-send', $this );

		$args = isset( $this->endpoint['args'] ) ? $this->endpoint['args'] : array();

		if ( ! isset( $args['timeout'] ) ) {
			$args['timeout'] = 30;
		}

		$args       = apply_filters( 'jet-engine/rest-api-listings/request/args', $args, $this );
		$query_args = apply_filters( 'jet-engine/rest-api-listings/request/query-args', $query_args, $this );
		$type       = apply_filters( 'jet-engine/rest-api-listings/request/type', $type, $this );

		$url = $this->get_url();

		if ( ! empty( $query_args ) ) {
			if ( is_array( $query_args ) ) {
				$url = add_query_arg( $query_args, $url );
			} else {
				$url = trailingslashit( $url ) . $query_args;
			}
		}

		switch ( $type ) {
			case 'post':
				return wp_remote_post( $url, $args );

			default:
				return wp_remote_get( $url, $args );
		}

	}

	public function get_items( $query_args = array(), $force = false ) {

		$cached = false;

		if ( ! $force ) {
			$cached = $this->get_cached_items( $query_args );
		}

		if ( false !== $cached ) {
			return $cached;
		}

		$response = $this->send_request( $query_args );

		if ( is_wp_error( $response ) ) {
			$this->set_error( $response->get_error_message() );
			return false;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$this->set_error( wp_remote_retrieve_response_message( $response ) );
			$this->set_error_details( $response );
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );

		$body = apply_filters( 'jet-engine/rest-api-listings/response/body', $body, $this, $query_args, $response );

		if ( empty( $body ) ) {
			$this->set_error( __( 'Reponse body is empty', 'jet-engine' ) );
			return false;
		}

		$items = $this->recursive_find_items( $body );

		if ( false === $items ) {
			$this->set_error( __( 'Items not found in the request by given path', 'jet-engine' ) );
			return false;
		} else {
			$this->update_items_cache( $items, $query_args );
			return $items;
		}

	}

	public function is_cached() {

		$endpoint = $this->get_endpoint();

		if ( empty( $endpoint['cache'] ) ) {
			return false;
		} else {
			return true;
		}

	}

	public function get_cache_transient( $query = array() ) {

		$hash = $this->get_url();

		if ( ! empty( $query ) ) {

			$str = '';
			foreach ( $query as $key => $value ) {
				$str .= $key . $value;
			}

			$hash .= md5( $str );

		}

		return 'jet_rest_' . $hash;

	}

	public function get_cached_items( $query = array() ) {

		if ( ! $this->is_cached() ) {
			return false;
		}

		$data = get_transient( $this->get_cache_transient( $query ) );

		if ( ! is_array( $data ) ) {
			return false;
		} else {
			return $data;
		}

	}

	public function update_items_cache( $items = array(), $query = array() ) {

		if ( ! $this->is_cached() ) {
			return false;
		}

		$endpoint = $this->get_endpoint();
		$interval = isset( $endpoint['cache_value'] ) ? absint( $endpoint['cache_value'] ) : 1;
		$duration = 0;

		if ( ! $interval ) {
			$interval = 1;
		}

		$period = isset( $endpoint['cache_period'] ) ? $endpoint['cache_period'] : 'minutes';

		switch ( $period ) {
			case 'hours':
				$duration = $interval * HOUR_IN_SECONDS;
				break;

			case 'days':
				$duration = $interval * DAY_IN_SECONDS;
				break;

			default:
				$duration = $interval * 60;
				break;
		}

		return set_transient( $this->get_cache_transient( $query ), $items, $duration );

	}

	public function recursive_find_items( $body = array(), $key_index = false ) {

		if ( ! is_object( $body ) && ! is_array( $body ) ) {
			return false;
		}

		$key_data = $this->get_key_data();

		if ( false === $key_index ) {

			if ( empty( $key_data ) ) {
				return $body;
			} else {
				$key_index = 0;
			}

		}

		$key = $key_data[ $key_index ];

		if ( is_object( $body ) && ! isset( $body->$key ) ) {
			return false;
		}

		if ( is_array( $body ) && ! isset( $body[ $key ] ) ) {
			return false;
		}

		if ( is_object( $body ) ) {
			$res = $body->$key;
		} elseif ( is_array( $body ) ) {
			$res = $body[ $key ];
		}

		if ( $key_index === ( count( $key_data ) - 1 ) ) {
			return $res;
		} else {
			$new_index = $key_index + 1;
			return $this->recursive_find_items( $res, $new_index );
		}

	}

	public function get_key_data() {

		$path = ! empty( $this->endpoint['items_path'] ) ? $this->endpoint['items_path'] : '/';

		if ( '/' === $path ) {
			return array();
		}

		return explode( '/', trim( $path, '/' ) );

	}

}
