<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Form_Builder\Query_Builder;

use Jet_Engine\Compatibility\Packages\Jet_Form_Builder\Package;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @access private
	 * @var    object
	 */
	public static $instance = null;

	public $slug = 'jet-form-builder-query';

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'jet-engine/query-builder/query-editor/register', array( $this, 'register_editor_component' ) );
		add_action( 'jet-engine/query-builder/queries/register', array( $this, 'register_query' ) );
	}

	/**
	 * Register editor component for the query builder
	 *
	 * @param  $manager
	 *
	 * @return void
	 */
	public function register_editor_component( $manager ) {
		require_once Package::instance()->package_path( 'query-builder/editor.php' );
		$manager->register_type( new Query_Editor() );
	}

	/**
	 * Register query class
	 *
	 * @param  $manager
	 *
	 * @return void
	 */
	public function register_query( $manager ) {

		require_once Package::instance()->package_path( 'query-builder/query.php' );
		
		$type  = $this->slug;
		$class = __NAMESPACE__ . '\Form_Query';

		$manager::register_query( $type, $class );

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
