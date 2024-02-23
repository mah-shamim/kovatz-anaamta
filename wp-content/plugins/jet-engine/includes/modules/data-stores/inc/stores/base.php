<?php
namespace Jet_Engine\Modules\Data_Stores\Stores;

abstract class Base_Store {

	public $prefix = 'je_data_store_';
	public $is_int = true;

	/**
	 * Store constructor
	 */
	public function __construct() {

		if ( $this->is_front_store() ) {
			add_action( 'jet-engine/listings/frontend-scripts', array( $this, 'register_store_js_object' ) );
		}

		//$this->on_init(); // moved to Factory class to prevent conflicts with cache plugins

	}

	public function set_store_as_string() {
		$this->is_int = false;
	}

	public function sanitize_store_item( $item ) {
		if ( $this->is_int && ! is_array( $item ) ) {
			return absint( $item );
		} elseif ( ! is_array( $item ) ) {
			return sanitize_text_field( ( string ) $item );
		} else {
			return json_encode( $item );
		}
	}

	/**
	 * Do something n post type unregister
	 *
	 * @return [type] [description]
	 */
	public function on_unregister() {}

	/**
	 * Store type ID
	 */
	abstract public function type_id();

	/**
	 * Store type name
	 */
	abstract public function type_name();

	/**
	 * Add to store callback
	 */
	abstract public function add_to_store( $store_id, $post_id );

	/**
	 * Add to store callback
	 */
	abstract public function remove( $store_id, $post_id );

	/**
	 * Get post IDs from store
	 */
	abstract public function get( $store_id );

	/**
	 * Check if this storeis processed on the front-end and should be served by JS
	 */
	public function is_front_store() {
		return false;
	}

	/**
	 * JS callback for add to store method
	 */
	public function js_add_to_store() {
		return '';
	}

	/**
	 * JS callback for remove from store method
	 */
	public function js_remove() {
		return '';
	}

	/**
	 * JS callback for is in store method
	 */
	public function js_in_store() {
		return '';
	}

	/**
	 * JS callback for get store method
	 */
	public function js_get_store() {
		return '';
	}

	/**
	 * Store-specific initialization actions
	 */
	public function on_init() {}

	/**
	 * Register object for current store with add, get, remove, in_store functions
	 */
	public function register_store_js_object() {

		$data = sprintf(
			'
			window.JetEngineStores = window.JetEngineStores || {};
			window.JetEngineStores[\'%1$s\'] = {
				addToStore: function( storeSlug, postID, maxSize, isOnViewStore ) {
					%2$s
				},
				remove: function( storeSlug, postID ) {
					%3$s
				},
				inStore: function( storeSlug, postID ) {
					%4$s
				},
				getStore: function( storeSlug ) {
					%5$s
				},
			};',
			$this->type_id(),
			$this->js_add_to_store(),
			$this->js_remove(),
			$this->js_in_store(),
			$this->js_get_store()
		);

		wp_add_inline_script( 'jet-engine-frontend', $data, 'before' );
	}

}
