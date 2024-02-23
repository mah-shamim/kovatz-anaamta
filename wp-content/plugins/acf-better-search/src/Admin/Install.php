<?php

namespace AcfBetterSearch\Admin;

use AcfBetterSearch\HookableInterface;
use AcfBetterSearch\Notice\ConverterPluginNotice;
use AcfBetterSearch\Notice\NoticeIntegration;
use AcfBetterSearch\Notice\ThanksNotice;
use AcfBetterSearch\PluginInfo;

/**
 * .
 */
class Install implements HookableInterface {

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
		register_activation_hook( $this->plugin_info->get_plugin_file(), [ $this, 'add_default_options' ] );
	}

	/**
	 * @return void
	 */
	public function add_default_options() {
		NoticeIntegration::set_default_value( ConverterPluginNotice::NOTICE_OPTION, ConverterPluginNotice::get_default_value() );
		NoticeIntegration::set_default_value( ThanksNotice::NOTICE_OPTION, ThanksNotice::get_default_value() );
	}
}
