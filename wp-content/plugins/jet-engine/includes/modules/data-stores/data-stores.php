<?php
/**
 * Dynamic vistibility conditions module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Data_Stores' ) ) {

	/**
	 * Define Jet_Engine_Module_Data_Stores class
	 */
	class Jet_Engine_Module_Data_Stores extends Jet_Engine_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'data-stores';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Data Stores', 'jet-engine' );
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>After activation, the Data Stores tab will be added to the JetEngine Settings dashboard. Besides that, in the Source drop-down menu of the Content settings tab of the Dynamic Link widget a “Add to store” option will appear.</p>
				<p>This module adds a wishlist, favorites, likes, bookmarks, etc. functionality to your website.</p>';
		}

		public function get_video_embed() {
			return 'https://www.youtube.com/embed/aP_jHmX9hd4?start=679';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'Data Stores Module Overview',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-data-stores-module-overview/',
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
			require $jet_engine->modules->modules_path( 'data-stores/inc/module.php' );
			$this->instance = \Jet_Engine\Modules\Data_Stores\Module::instance();
		}

	}

}
