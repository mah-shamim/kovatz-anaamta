<?php

namespace AcfBetterSearch;

use AcfBetterSearch\Admin;
use AcfBetterSearch\Search;
use AcfBetterSearch\Settings;

/**
 * .
 */
class AcfBetterSearch {

	public function __construct( PluginInfo $plugin_info ) {
		( new Admin\Assets( $plugin_info ) )->init_hooks();
		( new Admin\Install( $plugin_info ) )->init_hooks();
		( new Admin\Plugin( $plugin_info ) )->init_hooks();
		( new Admin\Uninstall( $plugin_info ) )->init_hooks();
		( new Notice\NoticeIntegration( $plugin_info, new Notice\AcfRequiredNotice() ) )->init_hooks();
		( new Notice\NoticeIntegration( $plugin_info, new Notice\ConverterPluginNotice() ) )->init_hooks();
		( new Notice\NoticeIntegration( $plugin_info, new Notice\ThanksNotice() ) )->init_hooks();
		( new Search\Init() )->init_hooks();
		( new Settings\Acf() )->init_hooks();
		( new Settings\Config() )->init_hooks();
		( new Settings\Page( $plugin_info ) )->init_hooks();
	}
}
