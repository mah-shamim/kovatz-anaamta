<?php
namespace Jet_Engine\Modules\Rest_API_Listings;

if ( ! trait_exists( '\Jet_Engine_Notices_Trait' ) ) {
	require_once jet_engine()->plugin_path( 'includes/traits/notices.php' );
}

/**
 * @property Auth_Types\Manager auth_types
 * @property Forms form
 *
 * Class Module
 * @package Jet_Engine\Modules\Rest_API_Listings
 */
class Module {

	use \Jet_Engine_Notices_Trait;

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	public $slug = 'rest-api-listings';
	public $data;
	public $request;
	public $settings;
	public $listings;
	public $auth_types;
	public $form;

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Init module components
	 *
	 * @return [type] [description]
	 */
	public function init() {

		require_once $this->module_path( 'auth-types/manager.php' );
		require_once $this->module_path( 'listings/manager.php' );
		require_once $this->module_path( 'data.php' );
		require_once $this->module_path( 'request.php' );
		require_once $this->module_path( 'settings.php' );
		require_once $this->module_path( 'forms.php' );

		$this->data       = new Data( $this );
		$this->request    = new Request();
		$this->settings   = new Settings();
		$this->listings   = new Listings\Manager();
		$this->auth_types = new Auth_Types\Manager();
		$this->form       = new Forms();

		require_once $this->module_path( 'query-builder/manager.php' );
		Query_Builder\Manager::instance();

		require_once $this->module_path( 'action-manager.php' );
		new Action_Manager();

	}

	/**
	 * Return path inside module
	 *
	 * @param  string $relative_path [description]
	 * @return [type]                [description]
	 */
	public function module_path( $relative_path = '' ) {
		return jet_engine()->modules->modules_path( $this->slug . '/inc/' . $relative_path );
	}

	/**
	 * Return url inside module
	 *
	 * @param  string $relative_path [description]
	 * @return [type]                [description]
	 */
	public function module_url( $relative_path = '' ) {
		return jet_engine()->plugin_url( 'includes/modules/' . $this->slug . '/inc/' . $relative_path );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
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
