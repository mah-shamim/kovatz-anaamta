<?php
namespace Jet_Engine\Modules\Performance;

/**
 * Main class
 */
class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	public $option_slug = 'jet-engine-performance-tweaks';

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), -1 );
	}

	/**
	 * Init module components
	 *
	 * @return [type] [description]
	 */
	public function init() {
		
		require $this->module_path( 'traits/prevent-wrap.php' );

		add_filter( 'jet-engine/dashboard/register-tabs', array( $this, 'register_dashboard_tab' ) );
		add_filter( 'body_class', array( $this, 'tweak_body_classes' ) );
		add_filter( 'admin_body_class', array( $this, 'tweak_body_classes' ) );

		add_action( 'wp_ajax_jet_engine_dashboard_save_tweaks', array( $this, 'save_tweaks' ) );

	}

	public function register_dashboard_tab( $tabs ) {

		require $this->module_path( 'dashboard-tab.php' );

		$tabs[] = new Dashboard_Tab();
		return $tabs;
	}

	/**
	 * Add ceurrently active tweak classes to body
	 * 
	 * @param  [type] $classes [description]
	 * @return [type]          [description]
	 */
	public function tweak_body_classes( $classes ) {

		if ( $this->is_tweak_active( 'optimized_dom' ) ) {
			if ( is_array( $classes ) ) {
				$classes[] = 'jet-engine-optimized-dom';
			} else {
				$classes .= ' jet-engine-optimized-dom';
			}
			
		}

		return $classes;
	}

	/**
	 * Check if given performance tweak already active
	 * 
	 * @param  [type]  $performance_tweak [description]
	 * @return boolean                    [description]
	 */
	public function is_tweak_active( $performance_tweak ) {
		$config = $this->get_tweaks_config();
		return isset( $config[ $performance_tweak ] ) ? $config[ $performance_tweak ] : false;
	}

	/**
	 * Get saved tweak config.
	 * 
	 * @return [type] [description]
	 */
	public function get_tweaks_config() {
		return wp_parse_args( get_option( $this->option_slug, array() ), $this->get_default_tweaks_config() );
	}

	/**
	 * Get default tweaks config
	 * 
	 * @return [type] [description]
	 */
	public function get_default_tweaks_config() {
		return apply_filters( 'jet-engine/modules/performance/default-tweaks', array(
			'optimized_dom'          => false,
			'enable_elementor_views' => true,
			'enable_blocks_views'    => true,
			'enable_bricks_views'    => true,
		) );
	}

	/**
	 * Ajax callback to save tweaks config
	 * 
	 * @return [type] [description]
	 */
	public function save_tweaks() {

		if ( empty( $_REQUEST['nonce'] ) 
			|| ! wp_verify_nonce( $_REQUEST['nonce'], jet_engine()->dashboard->get_nonce_action() ) 
		) {
			wp_send_json_error( array( 'message' => 'The link is expired. Please reload page and try again.' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'You don`t have access to this' ) );
		}

		$to_save = array();
		$tweaks  = $_REQUEST['tweaks'] ? $_REQUEST['tweaks'] : array();

		foreach ( $tweaks as $key => $value ) {
			$tweaks[ $key ] = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
		}

		$this->update_tweaks( $tweaks );

		wp_send_json_success( array( 'message' => __( 'Saved!', 'jet-engine' ) ) );

	}

	public function update_tweaks( $tweaks = [] ) {

		foreach ( $this->get_tweaks_config() as $tweak => $default ) {
			$to_save[ $tweak ] = isset( $tweaks[ $tweak ] ) ? $tweaks[ $tweak ] : $default;
		}

		update_option( $this->option_slug, $to_save, true );
		
	}

	/**
	 * Return path inside module
	 *
	 * @param  string $relative_path [description]
	 * @return [type]                [description]
	 */
	public function module_path( $relative_path = '' ) {
		return jet_engine()->modules->modules_path( 'performance/inc/' . $relative_path );
	}

	/**
	 * Return url inside module
	 *
	 * @param  string $relative_path [description]
	 * @return [type]                [description]
	 */
	public function module_url( $relative_path = '' ) {
		return jet_engine()->plugin_url( 'includes/modules/performance/inc/' . $relative_path );
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
