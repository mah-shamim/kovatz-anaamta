<?php
/**
 * Compatibility manager class.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Compatibility' ) ) {

	class Jet_Woo_Builder_Compatibility {

		function __construct() {
			add_action( 'init', [ $this, 'load_plugin_compatibility_packages' ] );

			$this->load_theme_compatibility_packages();
		}

		/**
		 * Plugin compat packages.
		 *
		 * Load plugin compatibility packages.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @return void
		 */
		public function load_plugin_compatibility_packages() {

			$packages = [
				'jet-popup.php'         => [
					'cb'   => 'class_exists',
					'args' => 'Jet_Popup',
				],
				'jet-cw.php'            => [
					'cb'   => 'class_exists',
					'args' => 'Jet_CW',
				],
				'jet-engine.php'        => [
					'cb'   => 'class_exists',
					'args' => 'Jet_Engine',
				],
				'jet-gallery.php'       => [
					'cb'   => 'class_exists',
					'args' => 'Jet_Woo_Product_Gallery',
				],
				'jet-smart-filters.php' => [
					'cb'   => 'class_exists',
					'args' => 'Jet_Smart_Filters',
				],
				'polylang.php'          => [
					'cb'   => 'class_exists',
					'args' => 'Polylang',
				],
				'wpml.php'              => [
					'cb'   => 'defined',
					'args' => 'WPML_ST_VERSION',
				],
			];

			foreach ( $packages as $file => $condition ) {
				if ( true === call_user_func( $condition['cb'], $condition['args'] ) ) {
					require jet_woo_builder()->plugin_path( 'includes/compatibility/packages/plugins/' . $file );
				}
			}

		}

		/**
		 * Theme compat packages.
		 *
		 * Load theme compatibility packages.
		 *
		 * @since  1.13.0
		 * @access public
		 *
		 * @return void
		 */
		public function load_theme_compatibility_packages() {

			$template = get_template();
			$int_file = jet_woo_builder()->plugin_path( 'includes/compatibility/packages/themes/' . $template . '/functions.php' );

			if ( file_exists( $int_file ) ) {
				require $int_file;
			}

		}

	}

}