<?php
namespace Jet_Engine\Modules\Data_Stores\Stores;

use Jet_Engine\Modules\Data_Stores\Module;

class Manager {

	private $store_types = array();
	private $stores      = array();

	public function __construct() {

		$this->register_store_types();
		$this->register_stores();

	}

	/**
	 * Register store types
	 */
	public function register_store_types() {

		require jet_engine()->modules->modules_path( 'data-stores/inc/stores/base.php' );
		require jet_engine()->modules->modules_path( 'data-stores/inc/stores/cookies.php' );
		require jet_engine()->modules->modules_path( 'data-stores/inc/stores/session.php' );
		require jet_engine()->modules->modules_path( 'data-stores/inc/stores/user-meta.php' );
		require jet_engine()->modules->modules_path( 'data-stores/inc/stores/local-storage.php' );

		$this->register_store_type( new Cookies_Store() );
		$this->register_store_type( new Session_Store() );
		$this->register_store_type( new User_Meta_Store() );
		$this->register_store_type( new Local_Storage() );

		do_action( 'jet-engine/data-stores/register-store-types', $this );

	}

	/**
	 * Register store instances
	 */
	public function register_stores() {

		$stores = Module::instance()->settings->get();

		if ( ! empty( $stores ) ) {

			require jet_engine()->modules->modules_path( 'data-stores/inc/stores/factory.php' );

			foreach ( $stores as $store ) {
				$this->register_store( $store );
			}

			require_once jet_engine()->modules->modules_path( 'data-stores/inc/stores/on-view.php' );

			new On_View( $this );

		}

		do_action( 'jet-engine/data-stores/register-stores', $this );

	}

	/**
	 * Regsiter new store instance
	 * $store should be array with next data
	 * array(
	 *    'slug' => 'store-slug',
	 *    'name' => 'store-name',
	 *    'type' => 'store-type', - one of the reigistered types
	 *    'size' => 0, - stores max size, set 0 to unlimited size
	 * )
	 */
	public function register_store( $store = array() ) {

		if ( empty( $store['slug'] ) || empty( $store['type'] ) ) {
			return;
		}

		$store_type = ! empty( $this->store_types[ $store['type'] ] ) ? $this->store_types[ $store['type'] ] : false;

		if ( ! $store_type ) {
			return;
		}

		$this->stores[ $store['slug'] ] = new Factory( $store, $store_type );

	}

	/**
	 * Register store type
	 */
	public function register_store_type( $type_instance ) {
		$this->store_types[ $type_instance->type_id() ] = $type_instance;
	}

	/**
	 * Deregister store type
	 *
	 * @param  [type] $type_id [description]
	 * @return [type]          [description]
	 */
	public function unregister_store_type( $type_id ) {
		if ( isset( $this->store_types[ $type_id ] ) ) {
			$type_instance = $this->store_types[ $type_id ];
			$type_instance->on_unregister();
			unset( $this->store_types[ $type_id ] );
		}
	}

	/**
	 * Get registered store instances list
	 */
	public function get_stores() {
		return $this->stores;
	}

	/**
	 * Get registered store instances list
	 */
	public function get_store_types() {
		return $this->store_types;
	}

	/**
	 * Get store types list for JS
	 */
	public function get_types_for_js() {

		$result =array();

		foreach ( $this->store_types as $type ) {
			$result[] = array(
				'value' => $type->type_id(),
				'label' => $type->type_name(),
			);
		}

		return $result;

	}

	/**
	 * Return store instance by store slug
	 */
	public function get_store( $store ) {
		return isset( $this->stores[ $store ] ) ? $this->stores[ $store ] : false;
	}

}
