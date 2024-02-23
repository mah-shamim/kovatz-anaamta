<?php
namespace Jet_Engine\Modules\Data_Stores\Stores;

class Session_Store extends Base_Store {

	/**
	 * Store type ID
	 */
	public function type_id() {
		return 'session';
	}

	/**
	 * Store type name
	 */
	public function type_name() {
		return __( 'Session', 'jet-engine' );
	}

	/**
	 * Maybe start session
	 */
	public function start_session() {

		if ( headers_sent() ) {
			return;
		}

		if ( ! session_id() || PHP_SESSION_ACTIVE !== session_status() ) {
			session_start();
		}
	}

	public function init_session( $wp ) {
		$this->start_session();
	}

	public function on_init() {
		add_action( 'parse_request', array( $this, 'init_session' ) );
	}

	public function on_unregister() {
		remove_action( 'parse_request', array( $this, 'init_session' ) );
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

		$this->set_store( $store_id, $store );

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

		$store = array_values( $store ); // added for reindex of array

		$count = count( $store );

		$this->set_store( $store_id, $store );

		return $count;

	}

	public function set_store( $store_id, $store ) {

		$this->start_session();

		$all_stores = isset( $_SESSION[ $this->prefix ] ) ? $_SESSION[ $this->prefix ] : array();
		$all_stores[ $store_id ] = $store;
		$_SESSION[ $this->prefix ] = $all_stores;

	}

	/**
	 * Get post IDs from store
	 */
	public function get( $store_id ) {

		$this->start_session();

		$all_stores = isset( $_SESSION[ $this->prefix ] ) ? $_SESSION[ $this->prefix ] : array();
		$store      = isset( $all_stores[ $store_id ] ) ? $all_stores[ $store_id ] : array();

		return apply_filters( 'jet-engine/data-stores/store/data', $store, $store_id );
	}

}
