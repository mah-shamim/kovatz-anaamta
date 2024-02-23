<?php
/**
 * Timber view class
 */
namespace Jet_Engine\Timber_Views;

use Timber\Timber;
use Timber\Loader;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Package {
	
	/**
	 * A reference to an instance of this class.
	 *
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	public $editor;
	public $registry;
	public $render;
	public $listing;

	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Initialize
	 * 
	 * @return [type] [description]
	 */
	public function init() {

		require_once $this->package_path( 'integration.php' );

		$integration = new Integration();

		if ( ! $integration->is_enabled() || ! $integration->has_timber() ) {
			return;
		}
		
		require_once $this->package_path( 'editor/render.php' );
		require_once $this->package_path( 'editor/listing.php' );
		require_once $this->package_path( 'view/registry.php' );
		require_once $this->package_path( 'view/render.php' );
		require_once $this->package_path( 'conditional-tags.php' );
		require_once $this->package_path( 'object-factory.php' );
		
		$this->editor   = new Editor\Render();
		$this->listing  = new Editor\Listing();
		$this->registry = new View\Registry();
		$this->render   = new View\Render();

		new Conditional_Tags();

		add_action( 'init', [ $this, 'after_init_hook' ], 999 );

	}

	public function after_init_hook() {
		do_action( 'jet-engine/twig-views/after-init', $this );
	}

	/**
	 * Return path inside package.
	 *
	 * @param string $relative_path
	 *
	 * @return string
	 */
	public function package_path( $relative_path = '' ) {
		return jet_engine()->plugin_path( 'includes/components/timber-views/inc/' . $relative_path );
	}

	/**
	 * Return url inside package.
	 *
	 * @param string $relative_path
	 *
	 * @return string
	 */
	public function package_url( $relative_path = '' ) {
		return jet_engine()->plugin_url( 'includes/components/timber-views/inc/' . $relative_path );
	}

	/**
	 * Sanitize HTML template including twig components
	 * 
	 * @param  [type] $html [description]
	 * @return [type]       [description]
	 */
	public function sanitize_html( $html ) {

		/**
		 * @todo - more advanced HTML sanitization. 
		 * The problem - wp_kses_post strips complex data from HTML attributes
		 */
		return wp_unslash( $html );
	}

	/**
	 * Sanitize listing CSS before save or render
	 * 
	 * @param  [type] $css [description]
	 * @return [type]      [description]
	 */
	public function sanitize_css( $css ) {
		return $css;
	}

	public function render_html( $html = '', $context = [], $twig = null ) {
		
		if ( ! $twig ) {
			$dummy_loader = new Loader();
			$twig = $dummy_loader->get_twig();
		}

		$template = $twig->createTemplate( $this->sanitize_html( do_shortcode( $html ) ) );
		return $template->render( $context );
	}

	public function get_context_for_object( $object ) {

		$context        = [];
		$object_factory = new Object_Factory();

		if ( is_object( $object ) && 'WP_Post' === get_class( $object ) ) {
			$context['post'] = $object_factory->get_post( $object, false );
		}

		if ( is_object( $object ) && 'WP_User' === get_class( $object ) ) {
			$context['user'] = $object_factory->get_user( $object, false );
		} elseif ( is_user_logged_in() ) {
			$context['user'] = $object_factory->get_user( wp_get_current_user(), false );
		}

		$object_factory->set_current( $object );

		return apply_filters( 'jet-engine/twig-views/current-context', $context, $object );

	}

	/**
	 * Slug for listing views
	 * 
	 * @return [type] [description]
	 */
	public function get_view_slug() {
		return 'twig';
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
