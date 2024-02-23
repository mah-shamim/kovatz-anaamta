<?php

/**
 * Plugin Name: ACF: Better Search
 * Description: Adds to default WordPress search engine the ability to search by content from selected fields of Advanced Custom Fields plugin.
 * Version: 4.2.0
 * Author: Mateusz Gbiorczyk
 * Author URI: https://gbiorczyk.pl/
 * Text Domain: acf-better-search
 */

require_once __DIR__ . '/vendor/autoload.php';

new AcfBetterSearch\AcfBetterSearch(
	new AcfBetterSearch\PluginInfo( __FILE__, '4.2.0' )
);
