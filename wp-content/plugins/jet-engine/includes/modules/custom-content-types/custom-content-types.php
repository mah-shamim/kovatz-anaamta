<?php
/**
 * Custom content types module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Custom_Content_Types' ) ) {

	/**
	 * Define Jet_Engine_Module_Custom_Content_Types class
	 */
	class Jet_Engine_Module_Custom_Content_Types extends Jet_Engine_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'custom-content-types';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Custom Content Types', 'jet-engine' );
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>After activation, the Custom Content Types tab will be added to the JetEngine submenu.</p>
				<p>This module allows you to create custom tables in your website database, fill them with data manually or using front-end forms and then export that information or show it on the website pages with the help of the Listing Grid widget.</p>';
		}

		public function get_video_embed() {
			return 'https://www.youtube.com/embed/m9lfFsm1NbE';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'How to Create a Custom Content Type',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-create-a-custom-content-type/',
				),
				array(
					'label'    => 'Live Q&A | Setting Custom Content Types in JetEngine | JetTalks',
					'url'      => 'https://www.youtube.com/watch?v=YPfk1TEMsnk',
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
			require $jet_engine->modules->modules_path( 'custom-content-types/inc/module.php' );
			$this->instance = \Jet_Engine\Modules\Custom_Content_Types\Module::instance();
		}

	}

}
