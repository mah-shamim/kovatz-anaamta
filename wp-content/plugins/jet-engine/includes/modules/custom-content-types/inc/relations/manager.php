<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Relations;

use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Manager class
 */
class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	public $slug = 'cct';

	/**
	 * register CCT relations
	 */
	public function __construct() {

		add_action( 'jet-engine/relation/setup-object-controls/' . $this->slug(), array( $this, 'setup_control' ), 10, 2 );
		add_action( 'jet-engine/relation/include-controls-class', array( $this, 'include_control_class' ) );

		add_filter( 'jet-engine/relations/types', array( $this, 'register_object_type' ) );

		if ( is_admin() ) {
			require_once Module::instance()->module_path( 'relations/edit.php' );
			new Edit();
		}

	}

	/**
	 * Register object types
	 *
	 * @param  [type] $types [description]
	 * @return [type]        [description]
	 */
	public function register_object_type( $types ) {

		require_once Module::instance()->module_path( 'relations/type.php' );
		$type = new Type();
		$types[ $type->get_name() ] = $type;

		return $types;

	}

	/**
	 * Returns slug of CCT relations object type
	 *
	 * @return [type] [description]
	 */
	public function slug() {
		return $this->slug;
	}

	/**
	 * Setup controls class
	 *
	 * @param  [type] $object_data [description]
	 * @param  [type] $relation    [description]
	 * @return [type]              [description]
	 */
	public function setup_control( $object_data, $relation ) {
		$relation->init_controls_class( '\Jet_Engine\Modules\Custom_Content_Types\Relations\Control', $object_data );
	}

	/**
	 * Include controls class for CCT relations
	 *
	 * @return [type] [description]
	 */
	public function include_control_class() {
		require_once Module::instance()->module_path( 'relations/control.php' );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

}
