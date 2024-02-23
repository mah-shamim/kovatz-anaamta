<?php
/**
 * User profile builder module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Profile_Builder' ) ) {

	/**
	 * Define Jet_Engine_Module_Profile_Builder class
	 */
	class Jet_Engine_Module_Profile_Builder extends Jet_Engine_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'profile-builder';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Profile Builder', 'jet-engine' );
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>After activation, the Profile Builder tab will be added to the JetEngine submenu.</p>
				<p>This module allows you to create either a personal user’s account page on your website or public profiles of the users similar to the social media profiles.</p>';
		}

		public function get_video_embed() {
			return 'https://www.youtube.com/embed/Q1lVe_kpTO0';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'User Profile Builder Overview',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-user-profile-builder-overview/',
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
			require $jet_engine->modules->modules_path( 'profile-builder/inc/module.php' );
			$this->instance = \Jet_Engine\Modules\Profile_Builder\Module::instance();
		}

	}

}
