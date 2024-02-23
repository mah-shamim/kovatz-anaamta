<?php
/**
 * User profile builder module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Maps_Listings' ) ) {

	/**
	 * Define Jet_Engine_Module_Maps_Listings class
	 */
	class Jet_Engine_Module_Maps_Listings extends Jet_Engine_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'maps-listings';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Maps Listings', 'jet-engine' );
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>After activation, the Maps Settings tab will be added to the JetEngine Settings dashboard. Besides that, a Map Listing widget will appear in the Elementor widget menu.</p>
				<p>This widget allows you to display any Custom Post Type items as pop-up markers on a map. Start with gettings an API key and paste it into a corresponding Maps Settings tab field.</p>';
		}

		public function get_video_embed() {
			return 'https://www.youtube.com/embed/Dc2VCbzXWqY';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'Map Listing Overview',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-maps-listing-overview/',
				),
				array(
					'label' => 'How to Create Google Maps API Key',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/how-to-create-google-map-api-key-to-use-advanced-map-widget-for-elementor/',
				),
				array(
					'label'    => 'Creating Google Maps API key',
					'url'      => 'https://www.youtube.com/watch?v=t2O2a2YiLJA&t',
					'is_video' => true,
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
			require $jet_engine->modules->modules_path( 'maps-listings/inc/module.php' );
			$this->instance = \Jet_Engine\Modules\Maps_Listings\Module::instance();
		}

	}

}
