<?php
namespace Jet_Engine\Modules\Data_Stores;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	public $slug = 'data-stores';
	public $data = null;
	public $settings = null;
	public $stores = null;
	public $elementor_integration = null;
	public $blocks_integration = null;
	public $render = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'jet-engine/listings/renderers/registered', array( $this, 'register_render_class' ) );
	}

	/**
	 * Init module components
	 *
	 * @return [type] [description]
	 */
	public function init() {

		if ( ! class_exists( '\Jet_Engine_Base_Data' ) ) {
			require_once jet_engine()->plugin_path( 'includes/base/base-data.php' );
		}

		require_once jet_engine()->modules->modules_path( 'data-stores/inc/data.php' );
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/settings.php' );
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/macros.php' );
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/elementor-integration.php' );
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/blocks-integration.php' );
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/query.php' );
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/render-links.php' );
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/stores/manager.php' );
		require_once jet_engine()->modules->modules_path( 'data-stores/inc/compatibility.php' );

		// Bricks Integration
		require jet_engine()->modules->modules_path( 'data-stores/inc/bricks-views/manager.php' );

		// Twig integration
		require jet_engine()->modules->modules_path( 'data-stores/inc/twig-views/manager.php' );

		$this->data                  = new Data( $this );
		$this->settings              = new Settings();
		$this->stores                = new Stores\Manager();
		$this->elementor_integration = new Elementor_Integration();
		$this->blocks_integration    = new Blocks_Integration();
		$this->render                = new Render_Links();

		new Bricks_Views\Manager();
		new Twig_Views\Manager();
		new Macros();
		new Query();
		new Compatibility();

	}

	/**
	 * Register render class.
	 *
	 * @param object $listings
	 */
	public function register_render_class( $listings ) {

		$listings->register_render_class(
			'data-store-button',
			array(
				'class_name' => 'Jet_Engine\Modules\Data_Stores\Render\Button',
				'path'       => jet_engine()->modules->modules_path( 'data-stores/inc/render/button.php' ),
			)
		);
	}

	/**
	 * Return path inside module
	 *
	 * @param  string $relative_path
	 * @return string
	 */
	public function module_path( $relative_path = '' ) {
		return jet_engine()->modules->modules_path( $this->slug . '/inc/' . $relative_path );
	}

	/**
	 * Return url inside module
	 *
	 * @param  string $relative_path
	 * @return string
	 */
	public function module_url( $relative_path = '' ) {
		return jet_engine()->modules->modules_url( $this->slug . '/inc/' . $relative_path );
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
