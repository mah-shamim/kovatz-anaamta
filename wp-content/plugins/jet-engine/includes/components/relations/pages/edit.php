<?php
/**
 * Relation edit page
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Relations_Page_Edit' ) ) {

	/**
	 * Define Jet_Engine_Relations_Page_Edit class
	 */
	class Jet_Engine_Relations_Page_Edit extends Jet_Engine_CPT_Page_Base {

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
				return esc_html__( 'Edit Relation', 'jet-engine' );
			} else {
				return esc_html__( 'Add Relation', 'jet-engine' );
			}
		}

		/**
		 * Returns currently requested items ID.
		 * If this funciton returns an empty result - this is add new item page
		 *
		 * @return [type] [description]
		 */
		public function item_id() {
			return isset( $_GET['id'] ) ? esc_attr( $_GET['id'] ) : false;
		}

		/**
		 * Register add controls
		 * @return [type] [description]
		 */
		public function page_specific_assets() {

			$module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );

			$ui = new CX_Vue_UI( $module_data );

			$ui->enqueue_assets();

			do_action( 'jet-engine/relations/edit/before-enqueue-assets' );

			if ( ! class_exists( '\Jet_Engine_Meta_Boxes_Page_Edit' ) ) {

				require_once jet_engine()->plugin_path( 'includes/components/meta-boxes/pages/edit.php' );

				\Jet_Engine_Meta_Boxes_Page_Edit::enqueue_meta_fields( array(
					'title'         => __( 'Meta Fields', 'jet-engine' ),
					'button'        => __( 'New Field', 'jet-engine' ),
					'allowed_types' => array( 'text', 'select', 'radio', 'checkbox', 'textarea', 'media', 'date', 'time', 'textarea', 'datetime-local' ),
					'disabled'      => array(
						'max_length',
						'object_type',
						'allow_custom',
						'save_custom',
						'is_array',
						'conditional_logic',
						'quick_editable',
						'width',
						'default_val',
						'revision_support',
					)
				) );
			}

			wp_enqueue_style(
				'jet-engine-relations',
				jet_engine()->plugin_url( 'includes/components/relations/assets/css/relations.css' ),
				array(),
				jet_engine()->get_version()
			);

			wp_register_script(
				'jet-engine-relation-delete-dialog',
				jet_engine()->plugin_url( 'includes/components/relations/assets/js/delete-dialog.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch', ),
				jet_engine()->get_version(),
				true
			);

			wp_register_script(
				'jet-engine-relation-component',
				jet_engine()->plugin_url( 'includes/components/relations/assets/js/edit-relation.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch', 'wp-util', 'lodash' ),
				jet_engine()->get_version(),
				true
			);

			wp_localize_script(
				'jet-engine-relation-delete-dialog',
				'JetEngineRelationDeleteDialog',
				array(
					'api_path' => jet_engine()->api->get_route( 'delete-relation' ),
					'redirect' => $this->manager->get_page_link( 'list' ),
				)
			);

			wp_enqueue_script(
				'jet-engine-relation-edit',
				jet_engine()->plugin_url( 'includes/components/relations/assets/js/edit.js' ),
				array( 'cx-vue-ui', 'wp-util', 'wp-api-fetch', 'jet-engine-relation-delete-dialog', 'jet-engine-relation-component' ),
				jet_engine()->get_version(),
				true
			);

			$id = $this->item_id();

			if ( $id ) {
				$button_label = __( 'Update Relation', 'jet-engine' );
				$redirect     = false;
			} else {
				$button_label = __( 'Add Relation', 'jet-engine' );
				$redirect     = $this->manager->get_edit_item_link( '%id%' );
			}

			wp_localize_script(
				'jet-engine-relation-edit',
				'JetEngineRelationConfig',
				$this->manager->get_admin_page_config( array(
					'api_path_edit'      => jet_engine()->api->get_route( $this->get_slug() . '-relation' ),
					'item_id'            => $id,
					'edit_button_label'  => $button_label,
					'redirect'           => $redirect,
					'existing_relations' => $this->get_existing_relations( $id, false ),
					'legacy_relations'   => $this->get_existing_relations( $id, true ),
					'post_types'         => \Jet_Engine_Tools::get_post_types_for_js(),
					'object_types'       => jet_engine()->relations->types_helper->get_types_for_js(),
					'rest_base'         => rest_url( '/jet-rel/' ),
					'help_links'         => array(
						array(
							'url'   => 'https://crocoblock.com/knowledge-base/articles/how-to-choose-the-needed-post-relations-and-set-them-with-jetengine-plugin/?utm_source=jetengine&utm_medium=relations-page&utm_campaign=need-help',
							'label' => __( 'How to choose the needed post relations and set them with JetEngine', 'jet-engine' ),
						),
						array(
							'url'   => 'https://crocoblock.com/knowledge-base/articles/how-to-establish-posts-relations-with-jetengine-creating-one-to-one-posts-relation/?utm_source=jetengine&utm_medium=relations-page&utm_campaign=need-help',
							'label' => __( 'How to establish posts relations with JetEngine. Creating “one-to-one” posts relation', 'jet-engine' ),
						),
						array(
							'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-post-relations-how-to-display-related-posts-using-dynamic-field-widget/?utm_source=jetengine&utm_medium=relations-page&utm_campaign=need-help',
							'label' => __( 'How to display related posts using Dynamic Field widget', 'jet-engine' ),
						),
						array(
							'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-post-relations-how-to-display-the-related-child-posts-in-the-listing-grid/?utm_source=jetengine&utm_medium=relations-page&utm_campaign=need-help',
							'label' => __( 'How to display the related child posts in the Listing Grid', 'jet-engine' ),
						),
						array(
							'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-post-relations-how-to-display-the-related-parent-posts-in-the-listing-grid/?utm_source=jetengine&utm_medium=relations-page&utm_campaign=need-help',
							'label' => __( 'How to display the related parent posts in the Listing Grid', 'jet-engine' ),
						),
					),
				) )
			);

			add_action( 'admin_footer', array( $this, 'add_page_template' ) );

		}

		/**
		 * Print add/edit page template
		 */
		public function add_page_template() {

			ob_start();
			include jet_engine()->relations->component_path( 'templates/relation.php' );
			$content = ob_get_clean();
			printf( '<script type="text/x-template" id="jet-edit-relation">%s</script>', $content );

			ob_start();
			include jet_engine()->relations->component_path( 'templates/edit.php' );
			$content = ob_get_clean();
			printf( '<script type="text/x-template" id="jet-cpt-form">%s</script>', $content );

			ob_start();
			include jet_engine()->relations->component_path( 'templates/delete-dialog.php' );
			$content = ob_get_clean();
			printf( '<script type="text/x-template" id="jet-cpt-delete-dialog">%s</script>', $content );

		}

		/**
		 * Returns existing relations list except requested
		 * @return [type] [description]
		 */
		public function get_existing_relations( $id = false, $is_legacy = false ) {

			$result = array();

			if ( $is_legacy ) {
				$items = jet_engine()->relations->legacy->get_active_relations();
			} else {
				$items = jet_engine()->relations->get_active_relations();
			}

			foreach ( $items as $item ) {

				if ( $is_legacy ) {

					if ( $id && $id == $item['id'] ) {
						continue;
					}

					$result[ $item['id'] ] = $item['name'];

				} else {

					if ( $id && $id === $item->get_id() ) {
						continue;
					}

					$result[ $item->get_id() ] = $item->get_relation_name();

				}

			}

			return $result;

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
