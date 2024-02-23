<?php
/**
 * Register term meta field for Rest API
 */

if ( ! class_exists( 'Jet_Engine_Rest_Post_Meta' ) ) {
	require jet_engine()->meta_boxes->component_path( 'rest-api/fields/post-meta.php' );
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Rest_Term_Meta' ) ) {

	/**
	 * Define Jet_Engine_Rest_Term_Meta class
	 */
	class Jet_Engine_Rest_Term_Meta extends Jet_Engine_Rest_Post_Meta {

		public function get_object_type() {
			return 'term';
		}

		public function prepare_object() {
			// Not used for terms		
		}

	}

}
