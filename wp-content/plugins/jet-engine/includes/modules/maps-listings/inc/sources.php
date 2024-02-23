<?php
namespace Jet_Engine\Modules\Maps_Listings;

class Sources {

	private $_sources = array();

	public function __construct() {
		add_action( 'init', array( $this, 'register_sources' ), 5 );
	}

	/**
	 * Register sources
	 *
	 * @return void
	 */
	public function register_sources() {
		$path = jet_engine()->modules->modules_path( 'maps-listings/inc/sources/' );

		require_once $path . 'base.php';
		require_once $path . 'posts.php';
		require_once $path . 'terms.php';
		require_once $path . 'users.php';
		require_once $path . 'repeater.php';
		require_once $path . 'sql.php';

		$this->register_source( new Source\Posts() );
		$this->register_source( new Source\Terms() );
		$this->register_source( new Source\Users() );
		$this->register_source( new Source\Repeater() );
		$this->register_source( new Source\SQL() );

		do_action( 'jet-engine/maps-listing/sources/register', $this );
	}

	/**
	 * Get registered sources
	 *
	 * @return array
	 */
	public function get_sources() {
		return $this->_sources;
	}

	/**
	 * Register source instance
	 *
	 * @return void
	 */
	public function register_source( $instance ) {
		$this->_sources[ $instance->get_id() ] = $instance;
	}

	/**
	 * Get source instance by ID
	 *
	 * @param  string $id
	 * @return object|false
	 */
	public function get_source( $id ) {
		return isset( $this->_sources[ $id ] ) ? $this->_sources[ $id ] : false;
	}

}
