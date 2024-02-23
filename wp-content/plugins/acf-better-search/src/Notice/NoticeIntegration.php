<?php

namespace AcfBetterSearch\Notice;

use AcfBetterSearch\HookableInterface;
use AcfBetterSearch\PluginInfo;
use AcfBetterSearch\Helper\ViewLoader;

/**
 * Supports ability to display notice and its management.
 */
class NoticeIntegration implements HookableInterface {

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	/**
	 * @var NoticeInterface
	 */
	private $notice;

	public function __construct( PluginInfo $plugin_info, NoticeInterface $notice ) {
		$this->plugin_info = $plugin_info;
		$this->notice      = $notice;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'admin_init', [ $this, 'init_notice_hooks' ] );

		if ( $ajax_action = $this->notice->get_ajax_action_to_disable() ) {
			add_action( 'wp_ajax_' . $ajax_action, [ $this, 'set_disable_value' ] );
		}
	}

	/**
	 * Initializes displaying notice in administration panel.
	 *
	 * @return void
	 * @internal
	 */
	public function init_notice_hooks() {
		if ( ! $this->notice->is_available() || ! $this->notice->is_active() ) {
			return;
		}

		if ( ! is_multisite() ) {
			add_action( 'admin_notices', [ $this, 'load_notice' ], 0 );
		} else {
			add_action( 'network_admin_notices', [ $this, 'load_notice' ], 0 );
		}
	}

	/**
	 * Loads view template for notice.
	 *
	 * @return void
	 * @internal
	 */
	public function load_notice() {
		$view_vars = $this->notice->get_vars_for_view();
		if ( $view_vars === null ) {
			return;
		}

		( new ViewLoader( $this->plugin_info ) )->load_view(
			$this->notice->get_output_path(),
			$view_vars
		);
	}

	/**
	 * Sets value for option that specifies whether to display notice.
	 *
	 * @param string      $notice_name   .
	 * @param string|null $default_value .
	 *
	 * @return void
	 */
	public static function set_default_value( string $notice_name, string $default_value = null ) {
		if ( ( $default_value === null ) || ( get_option( $notice_name, false ) !== false ) ) {
			return;
		}

		add_option( $notice_name, $default_value );
	}

	/**
	 * Sets options to disable notice.
	 *
	 * @return void
	 */
	public function set_disable_value() {
		update_option( $this->notice->get_option_name(), $this->notice->get_disable_value() );
	}
}
