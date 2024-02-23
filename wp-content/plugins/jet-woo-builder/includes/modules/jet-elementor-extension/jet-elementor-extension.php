<?php
/**
 * Jet Elementor Extension Module.
 *
 * Version: 1.0.6
 */

namespace Jet_Elementor_Extension;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Elementor_Extension\Module' ) ) {

	/**
	 * Class Jet_Elementor_Extension\Module.
	 *
	 * @since 1.0.0
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

		/**
		 * Module version.
		 *
		 * @var string
		 */
		protected $version = '1.0.6';

		/**
		 * Module directory path.
		 *
		 * @since 1.5.0
		 * @access protected
		 * @var srting.
		 */
		protected $path;

		/**
		 * Module directory URL.
		 *
		 * @since 1.5.0
		 * @access protected
		 * @var srting.
		 */
		protected $url;

		/**
		 * Constructor.
		 *
		 * @since  1.0.0
		 * @param  array $args
		 * @access public
		 * @return void
		 */
		public function __construct( array $args = array() ) {

			$this->path = $args['path'];
			$this->url  = $args['url'];

			$this->load_files();

			new Ajax_Handlers();

			$register_controls_action = 'elementor/controls/controls_registered';

			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				$register_controls_action = 'elementor/controls/register';
			}

			add_action( $register_controls_action, array( $this, 'register_controls' ) );
			add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
		}

		/**
		 * Load required files.
		 */
		public function load_files() {
			require trailingslashit( $this->path ) . 'inc/controls/query.php';
			require trailingslashit( $this->path ) . 'inc/controls/repeater.php';
			require trailingslashit( $this->path ) . 'inc/ajax-handlers.php';
		}

		/**
		 * Register new controls.
		 *
		 * @param  object $controls_manager Controls manager instance.
		 * @return void
		 */
		public function register_controls( $controls_manager ) {

			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				$controls_manager->register( new Query_Control() );
				$controls_manager->register( new Repeater_Control() );
			} else {
				$controls_manager->register_control( 'jet-query',    new Query_Control() );
				$controls_manager->register_control( 'jet-repeater', new Repeater_Control() );
			}
		}

		/**
		 * Enqueue editor scripts.
		 */
		public function enqueue_editor_scripts() {
			wp_enqueue_script(
				'jet-elementor-ext-editor',
				$this->url . 'assets/js/editor.js',
				array( 'jquery' ),
				$this->version,
				true
			);
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @param  array $args
		 * @access public
		 * @return object
		 */
		public static function get_instance( array $args = array() ) {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $args );
			}

			return self::$instance;
		}
	}
}
