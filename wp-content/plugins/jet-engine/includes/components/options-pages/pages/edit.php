<?php
/**
 * options edit page
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Options_Page_Edit' ) ) {

	/**
	 * Define Jet_Engine_Options_Page_Edit class
	 */
	class Jet_Engine_Options_Page_Edit extends Jet_Engine_CPT_Page_Base {

		/**
		 * Page slug
		 *
		 * @return string
		 */
		public function get_slug() {
			if ( $this->item_id() ) {
				return 'edit';
			} else {
				return 'add';
			}
		}

		/**
		 * Page name
		 *
		 * @return string
		 */
		public function get_name() {
			if ( $this->item_id() ) {
				return esc_html__( 'Edit Options Page', 'jet-engine' );
			} else {
				return esc_html__( 'Add New Options Page', 'jet-engine' );
			}
		}

		/**
		 * Returns currently requested items ID.
		 * If this funciton returns an empty result - this is add new item page
		 *
		 * @return [type] [description]
		 */
		public function item_id() {
			return isset( $_GET['id'] ) ? absint( $_GET['id'] ) : false;
		}

		/**
		 * Register add controls
		 * @return [type] [description]
		 */
		public function page_specific_assets() {

			$module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );

			$ui = new CX_Vue_UI( $module_data );

			$ui->enqueue_assets();

			if ( ! class_exists( 'Jet_Engine_Meta_Boxes_Page_Edit' ) ) {
				require_once jet_engine()->plugin_path( 'includes/components/meta-boxes/pages/edit.php' );
				Jet_Engine_Meta_Boxes_Page_Edit::enqueue_meta_fields( array(
					'title'    => __( 'Fields', 'jet-engine' ),
					'button'   => __( 'New Field', 'jet-engine' ),
					'disabled' => array(),
				) );
			}

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
				'jet-engine-cpt-edit',
				jet_engine()->options_pages->component_url( 'assets/js/edit.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch', 'jet-engine-cpt-delete-dialog' ),
				jet_engine()->get_version(),
				true
			);

			$id = $this->item_id();

			if ( $id ) {
				$button_label = __( 'Update Page', 'jet-engine' );
				$redirect     = false;
			} else {
				$button_label = __( 'Add Page', 'jet-engine' );
				$redirect     = $this->manager->get_edit_item_link( '%id%' );
			}

			wp_localize_script(
				'jet-engine-cpt-edit',
				'JetEnginePageConfig',
				$this->manager->get_admin_page_config( array(
					'api_path_edit'     => jet_engine()->api->get_route( $this->get_slug() . '-options-page' ),
					'item_id'           => $id,
					'edit_button_label' => $button_label,
					'redirect'          => $redirect,
					'parents'           => $this->get_available_menu_parents(),
					'capabilities'      => $this->get_available_caps(),
					'positions'         => $this->get_positions(),
					'default_position'  => Jet_Engine_Tools::get_default_menu_position(),
				) )
			);

			add_action( 'admin_footer', array( $this, 'add_page_template' ) );

		}

		/**
		 * Returns available capabilies
		 *
		 * @return [type] [description]
		 */
		public function get_available_caps() {

			return apply_filters( 'jet-engine/options-pages/available-capabilities', array(
				array(
					'value' => 'manage_options',
					'label' => __( 'Manage options', 'jet-engine' ),
				),
				array(
					'value' => 'activate_plugins',
					'label' => __( 'Activate plugins', 'jet-engine' ),
				),
				array(
					'value' => 'create_users',
					'label' => __( 'Create users', 'jet-engine' ),
				),
				array(
					'value' => 'switch_themes',
					'label' => __( 'Switch themes', 'jet-engine' ),
				),
				array(
					'value' => 'edit_pages',
					'label' => __( 'Edit pages', 'jet-engine' ),
				),
				array(
					'value' => 'edit_posts',
					'label' => __( 'Edit posts', 'jet-engine' ),
				),
				array(
					'value' => 'upload_files',
					'label' => __( 'Upload files', 'jet-engine' ),
				),
			) );

		}

		/**
		 * Returns available positions list
		 *
		 * @return [type] [description]
		 */
		public function get_positions() {
			return apply_filters(
				'jet-engine/options-pages/available-positions',
				Jet_Engine_Tools::get_available_menu_positions()
			);
		}

		/**
		 * Returns availbale parent pages for menu
		 *
		 * @return [type] [description]
		 */
		public function get_available_menu_parents() {

			global $menu;

			$pages = array();

			foreach ( $menu as $page ) {

				if ( ! empty( $page['0'] ) && ! empty( $page['2'] ) ) {
					$pages[] = array(
						'value' => $page['2'],
						'label' => $this->strip_tags_content( $page['0'] ),
					);
				}

			}

			return $pages;

		}

		/**
		 * Strip tags with content inside tags
		 *
		 * @param  [type]  $text   [description]
		 * @return [type]          [description]
		 */
		public function strip_tags_content( $text ) {
			return preg_replace( '@(<span[^>]*?>.*?<\/span><\/span>)|(<img.*?>)@si', '', $text );
		}

		/**
		 * Print add/edit page template
		 */
		public function add_page_template() {

			ob_start();
			include jet_engine()->options_pages->component_path( 'templates/edit.php' );
			$content = ob_get_clean();
			printf( '<script type="text/x-template" id="jet-cpt-form">%s</script>', $content );

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
			<div id="jet_cpt_form"></div>
			<?php
		}

	}

}
