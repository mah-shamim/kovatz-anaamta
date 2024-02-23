<?php
/**
 * CPTs list page
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Options_Page_List' ) ) {

	/**
	 * Define Jet_Engine_Options_Page_List class
	 */
	class Jet_Engine_Options_Page_List extends Jet_Engine_CPT_Page_Base {

		public $is_default = true;

		/**
		 * Class constructor
		 */
		public function __construct( $manager ) {

			parent::__construct( $manager );

			add_action( 'jet-engine/options-pages/page/after-title', array( $this, 'add_new_btn' ) );
		}

		/**
		 * Add new  post type button
		 */
		public function add_new_btn( $page ) {

			if ( $page->get_slug() !== $this->get_slug() ) {
				return;
			}

			?>
			<a class="page-title-action" href="<?php echo $this->manager->get_page_link( 'add' ); ?>"><?php
				_e( 'Add New', 'jet-engine' );
			?></a>
			<?php

			jet_engine()->get_video_help_popup( array(
				'popup_title' => __( 'JetEngine options pages overview', 'jet-engine' ),
				'embed' => 'https://www.youtube.com/embed/MqcuHrT1a_c',
			) )->wp_page_popup();
			

		}

		/**
		 * Page slug
		 *
		 * @return string
		 */
		public function get_slug() {
			return 'list';
		}

		/**
		 * Page name
		 *
		 * @return string
		 */
		public function get_name() {
			return esc_html__( 'Options Pages', 'jet-engine' );
		}

		/**
		 * Register add controls
		 * @return [type] [description]
		 */
		public function page_specific_assets() {

			$module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );

			$ui = new CX_Vue_UI( $module_data );

			$ui->enqueue_assets();

			wp_register_script(
				'jet-engine-cpt-delete-dialog',
				jet_engine()->options_pages->component_url( 'assets/js/delete-dialog.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch', ),
				jet_engine()->get_version(),
				true
			);

			wp_localize_script(
				'jet-engine-cpt-delete-dialog',
				'JetEngineDeleteDialog',
				array(
					'types'    => Jet_Engine_Tools::get_post_types_for_js(),
					'api_path' => jet_engine()->api->get_route( 'delete-options-page' ),
					'redirect' => $this->manager->get_page_link( 'list' ),
				)
			);

			wp_enqueue_script(
				'jet-engine-cpt-list',
				jet_engine()->options_pages->component_url( 'assets/js/list.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch', 'jet-engine-cpt-delete-dialog' ),
				jet_engine()->get_version(),
				true
			);

			wp_localize_script(
				'jet-engine-cpt-list',
				'JetEngineCPTListConfig',
				array(
					'api_path'      => jet_engine()->api->get_route( 'get-options-pages' ),
					'add_api_path'  => jet_engine()->api->get_route( 'add-options-page' ),
					'edit_link'     => $this->manager->get_edit_item_link( '%id%' ),
					'notices'       => array(
						'copied' => __( 'Copied!', 'jet-engine' ),
					),
				)
			);

			add_action( 'admin_footer', array( $this, 'add_page_template' ) );

		}

		/**
		 * Print add/edit page template
		 */
		public function add_page_template() {

			ob_start();
			include jet_engine()->options_pages->component_path( 'templates/list.php' );
			$content = ob_get_clean();
			printf( '<script type="text/x-template" id="jet-cpt-list">%s</script>', $content );

			ob_start();
			include jet_engine()->options_pages->component_path( 'templates/delete-dialog.php' );
			$content = ob_get_clean();
			printf( '<script type="text/x-template" id="jet-cpt-delete-dialog">%s</script>', $content );

		}

		/**
		 * Renderer callback
		 *
		 * @return void
		 */
		public function render_page() {

			?>
			<br>
			<div id="jet_cpt_list"></div>
			<?php

		}

	}

}