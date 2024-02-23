<?php
/**
 * Dynamic vistibility conditions module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Dynamic_Visibility' ) ) {

	/**
	 * Define Jet_Engine_Module_Dynamic_Visibility class
	 */
	class Jet_Engine_Module_Dynamic_Visibility extends Jet_Engine_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'dynamic-visibility';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Dynamic Visibility', 'jet-engine' );
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>After activation, in the Advanced settings tab of all Elementor widgets and sections a Dynamic Visibility section will be added.</p>
				<p>This module allows you to set the visibility of widgets, columns, and sections depending on the values of the meta fields, the role of the user, and other conditions.</p>';
		}

		public function get_video_embed() {
			return 'https://www.youtube.com/embed/aP_jHmX9hd4?start=532';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'Dynamic Visibility Module Options Overview',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-dynamic-visibility-module-options-overview/',
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
			require $jet_engine->modules->modules_path( 'dynamic-visibility/inc/module.php' );
			$this->instance = \Jet_Engine\Modules\Dynamic_Visibility\Module::instance();
		}

	}

}
