<?php
/**
 * Fullwidth block editor module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Fullwidth_Block_Editor' ) ) {

	/**
	 * Define Jet_Engine_Module_Fullwidth_Block_Editor class
	 */
	class Jet_Engine_Module_Fullwidth_Block_Editor extends Jet_Engine_Module_Base {

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'fullwidth-block-editor';
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>Make block editor area fullwidth. Not depending on theme styles.</p>';
		}

		public function get_video_embed() {
			return false;
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array();
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Fullwidth Block Editor', 'jet-engine' );
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {
			add_action( 'enqueue_block_editor_assets', array( $this, 'add_inline_styles' ) );
			
		}

		public function add_inline_styles() {
			wp_add_inline_style( 'wp-block-editor', '.wp-admin #wpwrap .editor-styles-wrapper .wp-block {margin-left: auto !important;margin-right: auto!important;max-width: 95% !important;} .wp-admin .editor-styles-wrapper .block-editor-block-list__layout {padding-left: 0;padding-right: 0;}' );
		}

		/**
		 * Is module supports elementor view
		 *
		 * @return [type] [description]
		 */
		public function support_elementor() {
			return false;
		}

	}

}
