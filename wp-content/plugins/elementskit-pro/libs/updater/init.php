<?php
namespace ElementsKit\Libs\Updater;
use ElementsKit\Libs\Framework\Classes\Utils;

defined( 'ABSPATH' ) || exit;

class Init{
    public function __construct(){
        $license_key = explode('-', trim( Utils::instance()->get_option('license_key') ));
        $license_key = !isset($license_key[0]) ? '' : $license_key[0];
        
        $plugin_dir_and_filename = \ElementsKit::plugin_dir() . 'elementskit.php';

        $active_plugins = get_option( 'active_plugins' );
        foreach ( $active_plugins as $active_plugin ) {
            if ( false !== strpos( $active_plugin, 'elementskit.php' ) ) {
                $plugin_dir_and_filename = $active_plugin;
                break;
            }
        }
        if ( ! isset( $plugin_dir_and_filename ) || empty( $plugin_dir_and_filename ) ) {
            throw( 'Plugin not found! Check the name of your plugin file in the if check above' );
        }

        new Edd_Warper(
            \Elementskit::account_url(),
            $plugin_dir_and_filename,
            [
                'version' => \Elementskit::version(), // current version number.
                'license' => $license_key, // license key (used get_option above to retrieve from DB).
                'item_id' => \Elementskit::product_id(), // id of this product in EDD.
                'author'  => \Elementskit::author_name(), // author of this plugin.
                'url'     => home_url(),
            ]
        );
    }
}