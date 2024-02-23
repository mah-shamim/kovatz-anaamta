<?php


namespace Jet_Engine\Modules\Forms\Tabs;


class Tab_Manager {

	public static $instance;
	private $_tabs = array();

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	/**
	 * We register tabs on the server side to receive data
	 * on page load and hang the ajax handler
	 */
	public function register_tabs() {
		require_once jet_engine()->plugin_path( 'includes/modules/forms/tabs/base-form-tab.php' );
		require_once jet_engine()->plugin_path( 'includes/modules/forms/tabs/captcha.php' );
		require_once jet_engine()->plugin_path( 'includes/modules/forms/tabs/active-campaign.php' );
		require_once jet_engine()->plugin_path( 'includes/modules/forms/tabs/get-response.php' );
		require_once jet_engine()->plugin_path( 'includes/modules/forms/tabs/mailchimp.php' );

		$tabs = apply_filters( 'jet-engine/dashboard/form-tabs', array(
			new Captcha(),
			new Active_Campaign(),
			new Get_Response(),
			new Mailchimp(),
		) );

		foreach ( $tabs as $tab ) {
			if ( $tab instanceof Base_Form_Tab ) {
				$this->register_tab( $tab );
			}
		}
	}

	public function register_tab( $tab ) {
		$this->_tabs[ $tab->slug() ] = $tab;

		add_action( "wp_ajax_jet_engine_forms_save_tab__{$tab->slug()}", array( $tab, 'on_get_request' ) );
		add_action( 'jet-engine/dashboard/assets', array( $tab, 'render_assets' ), - 999 );

		add_filter( 'jet-engine/dashboard/forms-config', function ( $page_config ) use ( $tab ) {
			$page_config[ $tab->slug() ] = $tab->on_load();

			return $page_config;
		} );
	}

	public function tab( $slug ) {
		$this->isset_tab( $slug );

		return $this->_tabs[ $slug ];
	}

	public function options( $slug = '', $default = array() ) {
		$this->isset_tab( $slug );

		return $this->_tabs[ $slug ]->get_options( $default );
	}

	public function isset_tab( $slug ) {
		if ( ! isset( $this->_tabs[ $slug ] ) ) {
			_doing_it_wrong(
				static::class . '::' . __FUNCTION__,
				'Undefined tab: ' . var_export( $slug, true ),
				'2.7.8'
			);
		}
	}

	public function all( $default_tabs = array() ) {
		$response = array();

		foreach ( $this->_tabs as $slug => $tab ) {
			$default = array();
			if ( isset( $default_tabs[ $slug ] ) ) {
				$default = $default_tabs[ $slug ];
			}

			$response[ $slug ] = $tab->get_options( $default );
		}

		return $response;
	}

}