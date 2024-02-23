<?php
/**
 * REST API listings module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Rest_Api_Listings' ) ) {

	/**
	 * Define Jet_Engine_Module_Rest_Api_Listings class
	 */
	class Jet_Engine_Module_Rest_Api_Listings extends Jet_Engine_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'rest-api-listings';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Rest API Listings', 'jet-engine' );
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>This module allows you to display information from third-party REST API using the Listing Grid widget.</p>
				<p>After activation, the Rest API Endpoints tab will be added to the JetEngine Settings dashboard. Besides that, a “REST API Endpoint” option will be available in the Listing creation window.</p>
				<p>Start with adding a new endpoint to the REST API Endpoints field. After that, add a new listing template that will have that new endpoint as a Source.</p>
				<p>Also this module adds <b>“REST API Request” notification</b> to the JetEngine forms. This notification works pretty similar like “Call a Webhook” but has more extended options</p>';
		}

		public function get_video_embed() {
			return '';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'How to Display Custom Content Type Items Using REST API',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-display-custom-content-type-items-using-rest-api/',
				),
				array(
					'label' => 'How to Add and Edit CCT Items Remotely Using REST API',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-add-and-edit-cct-items-remotely-using-rest-api/',
				),
			);
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {
			add_action( 'jet-engine/init', array( $this, 'create_instance' ) );
		}

		/**
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance( $jet_engine ) {
			require $jet_engine->modules->modules_path( 'rest-api-listings/inc/module.php' );
			$this->instance = \Jet_Engine\Modules\Rest_API_Listings\Module::instance();
		}

	}

}
