<?php

namespace AcfBetterSearch\Admin;

use AcfBetterSearch\HookableInterface;
use AcfBetterSearch\PluginInfo;

/**
 * .
 */
class Plugin implements HookableInterface {

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
		add_filter(
			'network_admin_plugin_action_links_' . $this->plugin_info->get_plugin_basename(),
			[ $this, 'add_plugin_links' ]
		);
		add_filter( 'plugin_action_links_' . $this->plugin_info->get_plugin_basename(), [ $this, 'add_plugin_links' ] );
	}

	/**
	 * @param string[] $links .
	 *
	 * @return string[]
	 */
	public function add_plugin_links( array $links ): array {
		array_unshift(
			$links,
			sprintf(
			/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
				__( '%1$sSettings%2$s', 'acf-better-search' ),
				'<a href="' . menu_page_url( 'acfbs_admin_page', false ) . '">',
				'</a>'
			)
		);
		$links[] = sprintf(
		/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
			__( '%1$sProvide us a coffee%2$s', 'acf-better-search' ),
			'<a href="https://ko-fi.com/gbiorczyk/?utm_source=acf-better-search&utm_medium=plugin-links" target="_blank">',
			'</a>'
		);
		return $links;
	}
}
