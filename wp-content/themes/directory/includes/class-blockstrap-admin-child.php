<?php

if ( class_exists( 'BlockStrap_Admin' ) ) {


	class BlockStrap_Admin_Child extends BlockStrap_Admin {

		public function __construct() {
			parent::__construct();
		}

		/**
		 * Get the theme title.
		 *
		 * @return string|null
		 */
		public function get_theme_title() {
			return __( 'Directory', 'directory' );
		}

		/**
		 * Get the required plugins details array.
		 *
		 * @return array
		 */
		public function get_required_plugins() {
			return array(
				'blockstrap-page-builder-blocks' => __( 'BlockStrap Builder', 'directory' ),
				'geodirectory'                   => __( 'GeoDirectory', 'directory' ),
			);
		}
	}

}
