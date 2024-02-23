<?php

namespace AcfBetterSearch\Admin;

use AcfBetterSearch\HookableInterface;
use AcfBetterSearch\Notice\ConverterPluginNotice;
use AcfBetterSearch\Notice\ThanksNotice;
use AcfBetterSearch\PluginInfo;

/**
 * .
 */
class Uninstall implements HookableInterface {

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
		register_uninstall_hook(
			$this->plugin_info->get_plugin_file(),
			[ 'AcfBetterSearch\Admin\Uninstall', 'remove_plugin_settings' ]
		);
	}

	/**
	 * @return void
	 */
	public static function remove_plugin_settings() {
		delete_option( 'acfbs_fields_types' );
		delete_option( 'acfbs_whole_phrases' );
		delete_option( 'acfbs_whole_words' );
		delete_option( 'acfbs_lite_mode' );
		delete_option( 'acfbs_selected_mode' );
		delete_option( ConverterPluginNotice::NOTICE_OPTION );
		delete_option( ThanksNotice::NOTICE_OPTION );
	}
}
