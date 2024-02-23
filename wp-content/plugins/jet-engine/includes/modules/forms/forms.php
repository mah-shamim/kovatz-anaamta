<?php
/**
 * Booking form module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Booking_Forms' ) ) {

	/**
	 * Define Jet_Engine_Module_Booking_Forms class
	 */
	class Jet_Engine_Module_Booking_Forms extends Jet_Engine_Module_Base {

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'booking-forms';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Forms (Legacy)', 'jet-engine' );
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p class="info-notice">This is a legacy form builder functionality. It would be updated only for critical bug fixes. Proceed to the <a href="' . admin_url( 'plugin-install.php?s=jetformbuilder&tab=search&type=term' ) . '" target="_blank">JetFormBuilder</a> plugin to get the latest updates with the new form features.</p>
			<p>After activation, the Forms tab will be added to the JetEngine submenu. Under this submenu you can create new forms and edit existings ones. This module also adds a new widget to the Elementor to display previously created forms.</p>';
		}

		public function get_video_embed() {
			return 'https://www.youtube.com/embed/2Fzt_90Yjco';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'How to Create a Form Layout',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/how-to-create-a-booking-form-layout/',
				),
				array(
					'label' => 'How to Update Posts via Front-end Form Submission Option',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-update-posts-via-front-end-form-submission-option/',
				),
				array(
					'label' => 'How to Update WordPress Users via Front-end Form Submission Option',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-update-wordpress-users-via-front-end-form-submission-option/',
				),
				array(
					'label' => 'More forms-related articles from the knowledge base',
					'url'   => 'https://crocoblock.com/knowledge-base/article-category/booking-form/',
				),
				array(
					'label'    => 'How to customize front-end post submission',
					'url'      => 'https://www.youtube.com/watch?v=Q1lVe_kpTO0',
					'is_video' => true,
				),
				array(
					'label'    => 'How to automate email notifications with Crocoblock and Zapier',
					'url'      => 'https://www.youtube.com/watch?v=KpvD7yatoKA',
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
			add_action( 'jet-engine/init', array( $this, 'create_instances' ) );
		}

		/**
		 * Create required instances
		 *
		 * @param  [type] $jet_engine [description]
		 * @return [type]             [description]
		 */
		public function create_instances( $jet_engine ) {

			require $jet_engine->modules->modules_path( 'forms/manager.php' );
			$jet_engine->forms = new Jet_Engine_Booking_Forms();

			// For backward compatibility
			$jet_engine->forms->booking = $jet_engine->forms;

		}

	}

}
