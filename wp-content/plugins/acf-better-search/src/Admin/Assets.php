<?php

namespace AcfBetterSearch\Admin;

use AcfBetterSearch\HookableInterface;
use AcfBetterSearch\PluginInfo;

/**
 * .
 */
class Assets implements HookableInterface {

	const CSS_FILE_PATH = 'assets/build/css/styles.css';
	const JS_FILE_PATH  = 'assets/build/js/scripts.js';

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	public function __construct( PluginInfo $plugin_info ) {
		$this->plugin_info = $plugin_info;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'admin_enqueue_scripts', [ $this, 'load_styles' ] );
		add_filter( 'admin_enqueue_scripts', [ $this, 'load_scripts' ] );
	}

	/**
	 * @return void
	 */
	public function load_styles() {
		wp_register_style(
			'acf-better-search',
			$this->plugin_info->get_plugin_directory_url() . self::CSS_FILE_PATH,
			[],
			$this->plugin_info->get_plugin_version()
		);
		wp_enqueue_style( 'acf-better-search' );
	}

	/**
	 * @return void
	 */
	public function load_scripts() {
		wp_register_script(
			'acf-better-search',
			$this->plugin_info->get_plugin_directory_url() . self::JS_FILE_PATH,
			[],
			$this->plugin_info->get_plugin_version(),
			true
		);
		wp_enqueue_script( 'acf-better-search' );
	}
}
