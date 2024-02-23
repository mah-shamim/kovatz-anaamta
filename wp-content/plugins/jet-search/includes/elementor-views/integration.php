<?php
/**
 * Jet_Search_Integration class
 *
 * @package   jet-search
 * @author    Zemez
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Search_Integration' ) ) {

	/**
	 * Define Jet_Search_Integration class
	 */
	class Jet_Search_Integration {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   Jet_Search_Integration
		 */
		private static $instance = null;

		/**
		 * Initialize integration hooks
		 *
		 * @return void
		 */
		public function init() {
			add_action( 'elementor/frontend/after_enqueue_styles',   array( jet_search_assets(), 'enqueue_styles' ) );
			add_action( 'elementor/frontend/after_register_scripts', array( jet_search_assets(), 'register_scripts' ) );

			add_action( 'elementor/preview/enqueue_scripts', array( jet_search_assets(), 'enqueue_preview_scripts' ) );
			add_action( 'elementor/preview/enqueue_styles',  array( jet_search_assets(), 'enqueue_styles' ) );

			add_action( 'elementor/editor/before_enqueue_scripts', array( jet_search_assets(), 'editor_scripts' ) );
			add_action( 'elementor/editor/after_enqueue_styles',   array( jet_search_assets(), 'editor_styles' ) );

			add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );

			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ), 10 );
			} else {
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ), 10 );
			}

			add_action( 'elementor/controls/controls_registered',   array( $this, 'add_controls' ), 10 );
		}

		/**
		 * Register cherry category for elementor if not exists
		 *
		 * @return void
		 */
		public function register_category( $elements_manager ) {
			$cherry_cat       = 'cherry';

			$elements_manager->add_category(
				$cherry_cat,
				array(
					'title' => esc_html__( 'JetElements', 'jet-search' ),
					'icon'  => 'font',
				)
			);
		}

		/**
		 * Register plugin widgets.
		 *
		 * @param  object $widgets_manager Elementor widgets manager instance.
		 * @return void
		 */
		public function register_widgets( $widgets_manager ) {

			require jet_search()->plugin_path( 'includes/elementor-views/base/widget-base.php' );

			foreach ( glob( jet_search()->plugin_path( 'includes/elementor-views/widgets/' ) . '*.php' ) as $file ) {
				$this->register_widget( $file, $widgets_manager );
			}
		}

		/**
		 * Register widget by file name
		 *
		 * @param  string $file            File name.
		 * @param  object $widgets_manager Widgets manager instance.
		 * @return void
		 */
		public function register_widget( $file, $widgets_manager ) {

			$base  = basename( str_replace( '.php', '', $file ) );
			$class = ucwords( str_replace( '-', ' ', $base ) );
			$class = str_replace( ' ', '_', $class );
			$class = sprintf( 'Elementor\Jet_Search_%s_Widget', $class );

			require $file;

			if ( class_exists( $class ) ) {
				if ( method_exists( $widgets_manager, 'register' ) ) {
					$widgets_manager->register( new $class );
				} else {
					$widgets_manager->register_widget_type( new $class );
				}
			}
		}

		/**
		 * Add new controls.
		 *
		 * @param  object $controls_manager Controls manager instance.
		 * @return void
		 */
		public function add_controls( $controls_manager ) {

			$controls = array(
				'jet-search-query' => 'Jet_Search_Control_Query',
			);

			foreach ( $controls as $control_id => $class_name ) {
				if ( $this->include_control( $class_name ) ) {
					if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
						$controls_manager->register( new $class_name() );
					} else {
						$controls_manager->register_control( $control_id, new $class_name() );;
					}
				}
			}

		}

		/**
		 * Include control file by class name.
		 *
		 * @param  string $class_name Class name
		 * @param  bool   $grouped    Group control or not
		 * @return bool
		 */
		public function include_control( $class_name, $grouped = false ) {

			$filename = str_replace( 'Jet_Search_Control_', '', $class_name );
			$filename = str_replace( '_', '-', strtolower( $filename ) );

			$filepath = sprintf(
				'includes/elementor-views/controls/%2$s%1$s.php',
				$filename,
				( true === $grouped ? 'groups/' : '' )
			);

			if ( ! file_exists( jet_search()->plugin_path( $filepath ) ) ) {
				return false;
			}

			require jet_search()->plugin_path( $filepath );

			return true;
		}

		/**
		 * Check if we currently in Elementor mode
		 *
		 * @return bool
		 */
		public function in_elementor() {

			$result = false;

			if ( wp_doing_ajax() ) {
				$result = ( isset( $_REQUEST['action'] ) && 'elementor_ajax' === $_REQUEST['action'] );
			} elseif ( jet_search()->elementor()->editor->is_edit_mode() && 'wp_enqueue_scripts' === current_filter() ) {
				$result = true;
			} elseif ( jet_search()->elementor()->preview->is_preview_mode() && 'wp_enqueue_scripts' === current_filter() ) {
				$result = true;
			}

			/**
			 * Allow to filter result before return
			 *
			 * @var bool $result
			 */
			return apply_filters( 'jet-search/in-elementor', $result );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return Jet_Search_Integration
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}

/**
 * Returns instance of Jet_Search_Integration
 *
 * @return Jet_Search_Integration
 */
function jet_search_integration() {
	return Jet_Search_Integration::get_instance();
}
