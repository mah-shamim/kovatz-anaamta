<?php
namespace Jet_Engine\Dashboard;

/**
 * Custom tabs managr class
 */
class Tab_Manager {

	private $_tabs = array();

	private $_assets = array();

	public static $instance;

	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->register_tabs();
		$this->register_manager_js();
	}

	public function register_tabs() {

		if ( ! class_exists( 'Jet_Engine\Dashboard\Base_Tab' ) ) {
			require_once jet_engine()->plugin_path( 'includes/dashboard/base-tab.php' );
		}

		$tabs = apply_filters( 'jet-engine/dashboard/register-tabs', array() );

		foreach ( $tabs as $tab ) {
			$this->register_tab( $tab );
		}
	}

	public function register_tab( Base_Tab $tab ) {

		if ( ! $tab->condition() ) {
			return;
		}

		$this->_tabs[ $tab->slug() ] = $tab;

		$assets = is_array( $tab->assets() ) ? $tab->assets() : array( $tab->assets() );

		$this->_assets = array_merge( $this->_assets, $assets );

		add_action( $tab->hook(), array( $tab, 'render_tab' ), 99 );
		add_action( 'jet-engine/dashboard/assets', array( $tab, 'render_assets' ) );
		add_filter( 'jet-engine/dashboard/config', function ( $config ) use ( $tab ) {

			$tab_config = $tab->load_config();

			if ( empty( $tab_config ) ) {
				return $config;
			}

			$config["_config__{$tab->slug()}"] = $tab_config;

			return $config;

		} );
	}

	public function register_manager_js() {
		
		if ( empty( $this->_assets ) ) {
			return;
		}

		add_action( 'jet-engine/dashboard/assets', function() {

			// Register default tab-manager bundle JS
			wp_register_script(
				'jet-engine-tab-manager',
				jet_engine()->plugin_url( 'assets/js/admin/dashboard/tab-manager.bundle.js' ),
				array( 'cx-vue-ui' ),
				jet_engine()->get_version(),
				true
			);

			foreach( array_unique( $this->_assets ) as $handle ) {
				wp_enqueue_script( $handle );
			}

		}, 999 );
	}

}
