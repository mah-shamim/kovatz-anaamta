<?php
/**
 * Base class for module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Base' ) ) {

	/**
	 * Define Jet_Engine_Module_Base class
	 */
	abstract class Jet_Engine_Module_Base {

		/**
		 * Module ID
		 *
		 * @return string
		 */
		abstract public function module_id();

		/**
		 * Module name
		 *
		 * @return string
		 */
		abstract public function module_name();

		/**
		 * Module init
		 *
		 * @return void
		 */
		abstract public function module_init();

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '';
		}

		/**
		 * Return video embed to showcase module in the admin area
		 */
		public function get_video_embed() {
			return '';
		}

		/**
		 * Returns array links to the module-related resources
		 *
		 * item format: array(
		* 'label'    => 'Link label',
		* 'url'      => 'https://link-url',
		* 'is_video' => true,
		 * )
		 *
		 * @return [type] [description]
		 */
		public function get_module_links() {
			return array();
		}

		/**
		 * Is module supports elementor view
		 *
		 * @return [type] [description]
		 */
		public function support_elementor() {
			return true;
		}

		/**
		 * Is module supports blocks view
		 *
		 * @return [type] [description]
		 */
		public function support_blocks() {
			return true;
		}

		/**
		 * Returns slug of the module to install it from the crocoblock.com
		 * 
		 * @return false or string
		 */
		public function external_slug() {
			return false;
		}

	}

}
