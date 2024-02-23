<?php
/**
 * Class YITH_WCBK_Integrations
 * handle plugin integrations
 *
 * @author  YITH
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Integrations
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.1
 */
class YITH_WCBK_Integrations {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Integrations list.
	 *
	 * @var array
	 */
	protected $integrations_list = array();

	/**
	 * Integrations object list.
	 *
	 * @var YITH_WCBK_Integration[]
	 */
	protected $integrations = array();

	/**
	 * YITH_WCBK_Integrations constructor.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_integrations' ), 15 );

		add_action( 'wp_loaded', array( $this, 'manage_actions' ) );
	}

	/**
	 * Load plugins
	 */
	public function load_integrations() {
		$this->integrations_list = require_once 'integrations-list.php';
		$this->load();

		add_action( 'yith_wcbk_integrations_tab_contents', array( $this, 'print_integrations' ) );
	}

	/**
	 * Load Integration classes
	 */
	private function load() {
		require_once YITH_WCBK_INCLUDES_PATH . '/integrations/class-yith-wcbk-integration.php';

		foreach ( $this->integrations_list as $key => $integration_data ) {
			$type      = $integration_data['type'] ?? 'plugin';
			$folder    = 'theme' === $type ? 'themes' : 'plugins';
			$path      = YITH_WCBK_INCLUDES_PATH . '/integrations/' . $folder . '/';
			$filename  = $path . 'class-yith-wcbk-' . $key . '-integration.php';
			$classname = $this->get_class_name_from_key( $key );
			$var       = str_replace( ' - ', '_', $key );

			if ( file_exists( $filename ) && ! class_exists( $classname ) ) {
				require_once $filename;
			}

			$integration_data['key'] = $key;

			if ( class_exists( $classname ) && method_exists( $classname, 'get_instance' ) ) {
				/**
				 * The integration.
				 *
				 * @var YITH_WCBK_Integration $integration
				 */
				$integration = $classname::get_instance();

			} else {
				$integration = new YITH_WCBK_Integration();
			}

			$integration->set_data( $integration_data );
			$integration->init_once();

			$this->$var                 = $integration;
			$this->integrations[ $key ] = $integration;
		}
	}

	/**
	 * Manage Integration actions
	 * Activate Deactivate
	 */
	public function manage_actions() {
		$action = wc_clean( wp_unslash( $_REQUEST['yith-wcbk-integration-action'] ?? '' ) );

		if ( $action ) {
			check_admin_referer( 'yith-wcbk-integration-status-change' );

			$allowed_actions = array( 'activate', 'deactivate' );
			$integration_key = wc_clean( wp_unslash( $_REQUEST['integration'] ?? '' ) );

			if ( current_user_can( 'manage_options' ) && in_array( $action, $allowed_actions, true ) && in_array( $integration_key, array_keys( $this->integrations_list ), true ) ) {
				$status = 'activate' === $action ? 'yes' : 'no';

				update_option( 'yith-wcbk-' . $integration_key . '-add-on-active', $status );

				do_action( 'yith_wcbk_' . $integration_key . '_add_on_active_status_change', $status );

				wp_safe_redirect( remove_query_arg( array( '_wpnonce', 'yith-wcbk-integration-action', 'integration' ) ) );
				exit;
			}
		}
	}

	/**
	 * Print integration list.
	 */
	public function print_integrations() {
		foreach ( $this->integrations as $key => $integration ) {
			if ( $integration->is_visible() ) {
				include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-single-integration.php';
			}
		}
	}

	/**
	 * Get the class name from key.
	 *
	 * @param string $key The integration key.
	 *
	 * @return string
	 */
	public function get_class_name_from_key( $key ) {
		$class_key = str_replace( '-', ' ', $key );
		$class_key = ucwords( $class_key );
		$class_key = str_replace( ' ', '_', $class_key );

		return 'YITH_WCBK_' . $class_key . '_Integration';
	}

	/**
	 * Check if user has the component (plugin/theme).
	 *
	 * @param string $key The integration key.
	 *
	 * @return bool
	 */
	public function has_component( string $key ): bool {
		$integration = $this->get_integration( $key );

		return ! ! $integration && $integration->is_component_active();
	}

	/**
	 * Retrieve a specific integration instance.
	 *
	 * @param string $key The integration key.
	 *
	 * @return YITH_WCBK_Integration|bool
	 */
	public function get_integration( string $key ) {
		if ( ! empty( $this->integrations[ $key ] ) ) {
			return $this->integrations[ $key ];
		}

		return false;
	}
}
