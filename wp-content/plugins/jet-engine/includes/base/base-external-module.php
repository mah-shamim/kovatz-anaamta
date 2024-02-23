<?php
/**
 * Base class for module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_External_Module_Base' ) ) {

	/**
	 * Define Jet_Engine_Module_Base class
	 */
	abstract class Jet_Engine_External_Module_Base extends Jet_Engine_Module_Base {

		/**
		 * Returns module external slug which should be the same as module ID
		 *
		 * @return [type] [description]
		 */
		public function external_slug() {
			return $this->module_id();
		}

		/**
		 * Returns detailed info about module
		 *
		 * @return [type] [description]
		 */
		public function get_module_details() {
			$data = $this->get_related_plugin_data();
			return '<h3>' . __( 'Related plugin:', 'jet-engine' ) . ' ' . $data['name'] . '</h3>' . $this->get_module_description();
		}

		/**
		 * Returns text description for the module
		 *
		 * @return [type] [description]
		 */
		public function get_module_description() {
			return '';
		}

		/**
		 * Check if related external plugin is already active
		 *
		 * @return boolean [description]
		 */
		public function is_related_plugin_active() {
			return false;
		}

		/**
		 * Returns information about the related plugin for current module
		 *
		 * @return [type] [description]
		 */
		public function get_related_plugin_data() {
			return array(
				'file' => '',
				'name' => '',
			);
		}

		/**
		 * Returns actions allowed after plugin installation
		 *
		 * @return [type] [description]
		 */
		public function get_installed_actions() {
			return array();
		}

		/**
		 * Returns custom message about successfull module installation
		 *
		 * @return [type] [description]
		 */
		public function get_installed_message() {
			return false;
		}

	}

}
