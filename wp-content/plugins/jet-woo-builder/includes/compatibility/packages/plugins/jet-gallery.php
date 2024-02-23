<?php
/**
 * JetGallery compatibility package
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Gallery_Package' ) ) {


	class Jet_Woo_Builder_Gallery_Package {


		public function __construct() {
			add_filter( 'jet-woo-builder/compatibility/jet-popup/script-dependencies', [ $this, 'add_quick_view_gallery_script_dependencies' ] );
		}

		/**
		 * Add quick view gallery script dependencies.
		 *
		 * Returns list of extended JetGallery script dependencies for JetPopup product quick view.
		 *
		 * @since  2.1.1
		 * @access public
		 *
		 * @param array $deps List of dependencies.
		 *
		 * @return mixed
		 */
		public function add_quick_view_gallery_script_dependencies( $deps ) {

			jet_woo_product_gallery_assets()->enqueue_scripts();

			array_push( $deps, 'photoswipe', 'photoswipe-ui-default' );

			return $deps;

		}

	}

}

new Jet_Woo_Builder_Gallery_Package();