<?php
/**
 * Jet_Search_Bricks_Integration class
 *
 * @package   jet-search
 * @author    Zemez
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Integration class
 */
class Jet_Search_Bricks_Integration {

	/**
	 * Frontend instance
	 *
	 * @var null
	 */
	public $frontend = null;

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 3.1.0
	 * @var   Jet_Search_Bricks_Integration
	 */
	private static $instance = null;

	/**
	 * Constructor for the class
	 */
	function init() {

		if ( ! $this->has_bricks() ) {
			return;
		}

		add_action( 'init', array( $this, 'register_elements' ), 10 );

		add_filter( 'bricks/builder/i18n', function( $i18n ) {
			$i18n['jetsearch'] = esc_html__( 'JetSearch', 'jet-search' );

			return $i18n;
		} );

		//Add JetSearch icons
		add_action( 'wp_enqueue_scripts', function() {
			if ( bricks_is_builder() ) {
				wp_enqueue_style(
					'jet-search-font',
					jet_search()->plugin_url( 'assets/css/jet-search-icons.css' ),
					array(),
					jet_search()->get_version()
				);
			}
		} );
	}

	public function component_path( $relative_path = '' ) {
		return Jet_Search()->plugin_path( 'includes/bricks-views/' . $relative_path );
	}

	public function register_elements() {

		require $this->component_path( 'elements/base.php' );

		$element_files = array(
			$this->component_path( 'elements/ajax-search.php' ),
			$this->component_path( 'elements/search-suggestions.php' ),
		);

		foreach ( $element_files as $file ) {
			\Bricks\Elements::register_element( $file );
		}

		do_action( 'jet-search/bricks-views/register-elements' );

	}

	public function has_bricks() {
		return defined( 'BRICKS_VERSION' );
	}

	/**
	 * Check if is Bricks editor render request
	 *
	 * @return boolean [description]
	 */
	public function is_bricks_editor() {

		// is API request
		$bricks_request_str = 'wp-json/bricks/v1/render_element';
		$is_api = ( ! empty( $_SERVER['REQUEST_URI'] ) && false !== strpos( $_SERVER['REQUEST_URI'], $bricks_request_str ) );

		// is AJAX request
		$is_ajax = ( ! empty( $_REQUEST['action'] ) && 'bricks_render_element' === $_REQUEST['action'] );

		// Is editor iframe
		$is_editor = ( ! empty( $_REQUEST['bricks'] ) && 'run' === $_REQUEST['bricks'] );

		return $is_api || $is_ajax || $is_editor;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  3.1.0
	 * @return Jet_Search_Bricks_Integration
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

/**
 * Returns instance of Jet_Search_Bricks_Integration
 *
 * @return Jet_Search_Bricks_Integration
 */
function jet_search_bricks_integration() {
	return Jet_Search_Bricks_Integration::get_instance();
}
