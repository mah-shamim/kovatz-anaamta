<?php
namespace Jet_Engine\Modules\Data_Stores\Stores;

class Local_Storage extends Base_Store {

	/**
	 * Store type ID
	 */
	public function type_id() {
		return 'local-storage';
	}

	/**
	 * Store type name
	 */
	public function type_name() {
		return __( 'Local Storage', 'jet-engine' );
	}

	/**
	 * Add to store callback
	 */
	public function add_to_store( $store_id, $post_id ) {

	}

	/**
	 * Add to store callback
	 */
	public function remove( $store_id, $post_id ) {

	}

	/**
	 * Get post IDs from store
	 */
	public function get( $store_id ) {

	}

	/**
	 * JS callback for add to store method
	 */
	public function js_add_to_store() {
		return "
		var store = window.localStorage.getItem( 'jet_engine_store_' + storeSlug );
		isOnViewStore = isOnViewStore || false;

		if ( store ) {
			store = store.split( ',' );
		} else {
			store = [];
		}

		postID = '' + postID;

		maxSize = parseInt( maxSize, 10 );

		if ( 0 <= store.indexOf( postID ) ) {
			return store.length;
		}

		if ( 0 < maxSize && store.length >= maxSize ) {
			
			if ( isOnViewStore ) {
				store.splice( 0, 1 );
			} else {
				alert( 'You can`t add more posts' );
				return false;
			}
		
		}

		store.push( postID );

		window.localStorage.setItem( 'jet_engine_store_' + storeSlug, store.join( ',' ) );

		return store.length;

		";
	}

	/**
	 * JS callback for remove from store method
	 */
	public function js_remove() {
		return "
		var store = window.localStorage.getItem( 'jet_engine_store_' + storeSlug ),
			index;

		if ( store ) {
			store = store.split( ',' );
		} else {
			store = [];
		}

		postID = '' + postID;

		index = store.indexOf( postID );

		if ( 0 > index ) {
			return store.length;
		} else {
			store.splice( index, 1 );
		}

		window.localStorage.setItem( 'jet_engine_store_' + storeSlug, store.join( ',' ) );

		return store.length;

		";
	}

	/**
	 * JS callback for is in store method
	 */
	public function js_in_store() {
		return "
		var store = window.localStorage.getItem( 'jet_engine_store_' + storeSlug ),
			index;

		postID = '' + postID;

		if ( store ) {
			store = store.split( ',' );
		} else {
			store = [];
		}

		index = store.indexOf( postID );

		return ( 0 <= index );

		";
	}

	/**
	 * JS callback for get store method
	 */
	public function js_get_store() {
		return "
		var store = window.localStorage.getItem( 'jet_engine_store_' + storeSlug ),
			index;

		if ( store ) {
			store = store.split( ',' );
		} else {
			store = [];
		}

		return store;

		";;
	}

	/**
	 * Check if this storeis processed on the front-end and should be served by JS
	 */
	public function is_front_store() {
		return true;
	}

}