<?php

namespace AcfBetterSearch\Settings;

use AcfBetterSearch\Helper\ViewLoader;
use AcfBetterSearch\HookableInterface;
use AcfBetterSearch\PluginInfo;

/**
 * .
 */
class Page implements HookableInterface {

	const PAGE_VIEW_PATH = 'views/settings.php';

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	/**
	 * @var Options
	 */
	private $options;

	public function __construct( PluginInfo $plugin_info, Options $options = null ) {
		$this->plugin_info = $plugin_info;
		$this->options     = $options ?: new Options();
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
	}

	/**
	 * @return void
	 */
	public function add_settings_page() {
		if ( is_network_admin() ) {
			return;
		}

		add_submenu_page(
			'options-general.php',
			'ACF: Better Search',
			'ACF: Better Search',
			'manage_options',
			'acfbs_admin_page',
			[ $this, 'show_settings_page' ]
		);
	}

	/**
	 * @return void
	 */
	public function show_settings_page() {
		( new Save() )->init_saving();

		$config = apply_filters( 'acfbs_config', [], true );
		( new ViewLoader( $this->plugin_info ) )->load_view(
			self::PAGE_VIEW_PATH,
			[
				'settings_url'      => sprintf(
					'%1$s&_wpnonce=%2$s',
					menu_page_url( 'acfbs_admin_page', false ),
					wp_create_nonce( 'acfbs-save' )
				),
				'submit_value'      => 'acfbs_save',
				'fields'            => $this->options->get_fields_settings(),
				'features_default'  => $this->options->get_features_default_settings( $config ),
				'features_advanced' => $this->options->get_features_advanced_settings( $config ),
				'config'            => $config,
			]
		);

		require_once $this->plugin_info->get_plugin_directory_path() . '/templates/views/settings.php';
	}
}
