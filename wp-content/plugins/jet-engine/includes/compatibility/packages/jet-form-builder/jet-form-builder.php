<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Form_Builder;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Define Package class
 */
class Package {

	/**
	 * A reference to an instance of this class.
	 *
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		require_once $this->package_path( 'query-builder/manager.php' );
		require_once $this->package_path( 'listings/manager.php' );
		
		Query_Builder\Manager::instance();
		Listings\Manager::instance();

	}

	/**
	 * Return path inside package.
	 *
	 * @param string $relative_path
	 *
	 * @return string
	 */
	public function package_path( $relative_path = '' ) {
		return jet_engine()->plugin_path( 'includes/compatibility/packages/jet-form-builder/inc/' . $relative_path );
	}

	/**
	 * Return URL inside package.
	 *
	 * @param string $relative_path
	 *
	 * @return string
	 */
	public function package_url( $relative_path = '' ) {
		return jet_engine()->plugin_url( 'includes/compatibility/packages/jet-form-builder/inc/' . $relative_path );
	}

	/**
	 * Returns the instance.
	 *
	 * @access public
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

}

Package::instance();
