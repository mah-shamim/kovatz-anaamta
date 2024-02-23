<?php
/**
 * Kadence Block compatibility package
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Kadence_Block_Package' ) ) {

	class Jet_Engine_Kadence_Block_Package {

		public function __construct() {
			add_filter( 'jet-engine/blocks-views/render/listing-content', array( $this, 'replace_selectors_in_listing' ) );
		}

		public function replace_selectors_in_listing( $content ) {

			if ( false === strpos( $content, 'kadence' ) ) {
				return $content;
			}

			$current_obj_id = jet_engine()->listings->data->get_current_object_id();

			// Replace the `row layout` selector.
			$content = str_replace( 'kt-layout-id_', 'kt-layout-id_' .$current_obj_id . '_', $content );

			// Replace the `column layout` selector.
			$content = str_replace( 'kadence-column_', 'kadence-column_' . $current_obj_id . '_', $content );

			return $content;
		}
	}

}

new Jet_Engine_Kadence_Block_Package();
