<?php
namespace Jet_Engine\Modules\Data_Stores\Stores;

class Cookies_Store extends Base_Store {

	/**
	 * Store type ID
	 */
	public function type_id() {
		return 'cookies';
	}

	/**
	 * Store type name
	 */
	public function type_name() {
		return __( 'Cookies', 'jet-engine' );
	}

	/**
	 * Add to store callback
	 */
	public function add_to_store( $store_id, $post_id ) {
		
		$store = $this->get( $store_id );

		if ( ! in_array( $post_id, $store ) ) {
			$store[] = $this->sanitize_store_item( $post_id );
		}

		$count = count( $store );

		$this->set_cookie( $store_id, $store );

		return $count;

	}

	/**
	 * Add to store callback
	 */
	public function remove( $store_id, $post_id ) {

		$store = $this->get( $store_id );

		if ( false !== ( $index = array_search( $post_id, $store ) ) ) {
			unset( $store[ $index ] );
		}

		$count = count( $store );

		$this->set_cookie( $store_id, $store );

		return $count;

	}

	public function set_cookie( $store_id, $store ) {

		$cookie_name = $this->prefix . $store_id;
		$cookie_val  = implode( ',', $store );
		$expire      = time() + YEAR_IN_SECONDS;
		$secure      = ( false !== strstr( get_option( 'home' ), 'https:' ) && is_ssl() );

		setcookie(
			$cookie_name, 
			$cookie_val, 
			$expire, 
			COOKIEPATH ? COOKIEPATH : '/', 
			COOKIE_DOMAIN, 
			$secure, 
			true
		);

		$_COOKIE[ $cookie_name ] = $cookie_val;

	}

	/**
	 * Get post IDs from store
	 */
	public function get( $store_id ) {
		
		$cookie_name = $this->prefix . $store_id;
		$cookie_val  = isset( $_COOKIE[ $cookie_name ] ) ? $_COOKIE[ $cookie_name ] : '';

		if ( $cookie_val ) {
			$store = explode( ',', $cookie_val );
		} else {
			$store = array();
		}

		return apply_filters( 'jet-engine/data-stores/store/data', $store, $store_id );
	}

}
