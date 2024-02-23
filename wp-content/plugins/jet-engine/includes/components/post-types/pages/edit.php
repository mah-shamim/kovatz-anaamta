<?php
/**
 * CPTs edit page
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Page_Edit' ) ) {

	/**
	 * Define Jet_Engine_CPT_Page_Edit class
	 */
	class Jet_Engine_CPT_Page_Edit extends Jet_Engine_CPT_Page_Base {

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
				return esc_html__( 'Edit Post Type', 'jet-engine' );
			} else {
				return esc_html__( 'Add New Post Type', 'jet-engine' );
			}
		}

		/**
		 * Returns currently requested items ID.
		 * If this funciton returns an empty result - this is add new item page
		 *
		 * @return [type] [description]
		 */
		public function item_id() {

			if ( ! empty( $_GET['edit-type'] ) && ! empty( $_GET['post-type'] ) ) {
				return -1;
			} else {
				return isset( $_GET['id'] ) ? absint( $_GET['id'] ) : false;
			}

		}

		/**
		 * Returns full data about existsing taxonomies
		 *
		 * @return [type] [description]
		 */
		public function get_taxonomies_full_data() {

			$taxonomies = get_taxonomies( array(), 'objects' );
			$result     = array();

			foreach ( $taxonomies as $tax ) {

				$result[] = array(
					'slug'         => $tax->name,
					'name'         => ! empty( $tax->label ) ? $tax->label : $tax->name,
					'objects'      => array_values( $tax->object_type ),
					'hierarchical' => $tax->hierarchical,
				);

			}

			return $result;

		}

		/**
		 * Register add controls
		 * @return [type] [description]
		 */
		public function page_specific_assets() {

			$module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );

			$ui = new CX_Vue_UI( $module_data );

			$ui->enqueue_assets();

			do_action( 'jet-engine/post-type/edit/before-enqueue-assets' );

			wp_register_script(
				'jet-engine-cpt-delete-dialog',
				jet_engine()->cpt->component_url( 'assets/js/delete-dialog.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch', ),
				jet_engine()->get_version(),
				true
			);

			wp_localize_script(
				'jet-engine-cpt-delete-dialog',
				'JetEngineCPTDeleteDialog',
				array(
					'types'    => Jet_Engine_Tools::get_post_types_for_js(),
					'api_path' => jet_engine()->api->get_route( 'delete-post-type' ),
					'redirect' => $this->manager->get_page_link( 'list' ),
				)
			);

			wp_enqueue_script(
				'jet-engine-cpt-edit',
				jet_engine()->cpt->component_url( 'assets/js/edit.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch', 'jet-engine-cpt-delete-dialog' ),
				jet_engine()->get_version(),
				true
			);

			$id            = $this->item_id();
			$api_path_edit = jet_engine()->api->get_route( $this->get_slug() . '-post-type' );
			$api_path_get  = jet_engine()->api->get_route( 'get-post-type' );
			$is_built_in   = false;

			if ( $id ) {
				$button_label = __( 'Update Post Type', 'jet-engine' );
				$redirect     = false;

				if ( $id < 0 ) {
					$api_path_edit = jet_engine()->api->get_route( 'edit-built-in-post-type' );
					$api_path_get  = jet_engine()->api->get_route( 'get-built-in-post-type' );
					$api_path_get .= esc_attr( $_GET['post-type'] );
					$is_built_in   = true;
				}

			} else {
				$button_label = __( 'Add Post Type', 'jet-engine' );
				$redirect     = $this->manager->get_edit_item_link( '%id%' );
			}

			wp_localize_script(
				'jet-engine-cpt-edit',
				'JetEngineCPTConfig',
				$this->manager->get_admin_page_config( array(
					'is_post_types_editor' => true,
					'api_path_edit'        => $api_path_edit,
					'api_path_get'         => $api_path_get,
					'api_path_reset'       => jet_engine()->api->get_route( 'reset-built-in-post-type' ),
					'taxonomies_full_data' => $this->get_taxonomies_full_data(),
					'all_fields'           => jet_engine()->meta_boxes->get_fields_for_context( 'post_type' ),
					'glossaries_list'      => jet_engine()->glossaries->get_glossaries_for_js(),
					'admin_filters_types'  => $this->manager->admin_filters_types(),
					'item_id'              => $id,
					'edit_button_label'    => $button_label,
					'redirect'             => $redirect,
					'is_built_in'          => $is_built_in,
					'admin_columns_cb'     => $this->get_allowed_admin_columns_cb(),
					'positions'            => Jet_Engine_Tools::get_available_menu_positions(),
					'default_position'     => Jet_Engine_Tools::get_default_menu_position(),
					'slug_error'           => __( 'Maximum 20 characters length', 'jet-engine' ),
					'help_links'           => array(
						array(
							'url'   => 'https://crocoblock.com/knowledge-base/articles/how-to-create-a-custom-post-type-based-on-jetengine-plugin/?utm_source=jetengine&utm_medium=post-type-page&utm_campaign=need-help',
							'label' => __( 'Creating custom post type with JetEngine', 'jet-engine' ),
						),
						array(
							'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-display-date-in-admin-column/?utm_source=jetengine&utm_medium=post-type-page&utm_campaign=need-help',
							'label' => __( 'How to display date in an admin column', 'jet-engine' ),
						),
						array(
							'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-use-custom-callback-to-display-needed-information-in-admin-columns/?utm_source=jetengine&utm_medium=post-type-page&utm_campaign=need-help',
							'label' => __( 'How to use custom callback to display needed information in admin columns', 'jet-engine' ),
						),
						array(
							'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-set-the-visibility-of-meta-fields-using-conditional-logic/?utm_source=jetengine&utm_medium=post-type-page&utm_campaign=need-help',
							'label' => __( 'How to set the visibility of meta fields using conditional logic', 'jet-engine' ),
						),
					),
				) )
			);

			add_action( 'admin_footer', array( $this, 'add_page_template' ) );

		}

		/**
		 * Returns predefined admin columns callbacks list
		 *
		 * @return array
		 */
		public function get_allowed_admin_columns_cb() {
			return apply_filters( 'jet-engine/post-type/predifined-columns-cb-for-js', array(
				'jet_engine_custom_cb_date' => array(
					'description' => __( 'Format date (from timestamp)', 'jet-engine' ),
					'args'        => array(
						'field' => array(
							'label'       => __( 'Set field', 'jet-engine' ),
							'description' => __( 'Meta field to get date from', 'jet-engine' ),
							'value'       => '',
						),
						'format' => array(
							'label'       => __( 'Set format', 'jet-engine' ),
							'description' => '<a href="https://wordpress.org/support/article/formatting-date-and-time/">' . __( 'Documentation on date and time formatting', 'jet-engine' ) . '</a>',
							'value'       => get_option( 'date_format' ),
						),
					),
				),
				'jet_engine_custom_cb_pretty_post_link' => array(
					'description' => __( 'Get linked post title by post ID', 'jet-engine' ),
					'args'        => array(
						'field' => array(
							'label'       => __( 'Set field', 'jet-engine' ),
							'description' => __( 'Meta field to get post ID from', 'jet-engine' ),
							'value'       => '',
						),
					),
				),
				'jet_engine_custom_cb_related_posts' => array(
					'description' => __( 'Get linked post title by post ID (for legacy relations)', 'jet-engine' ),
					'args'        => array(
						'field' => array(
							'label'       => __( 'Set field', 'jet-engine' ),
							'description' => __( 'Meta field to get post ID from', 'jet-engine' ),
							'value'       => '',
						),
					),
				),
				'jet_engine_custom_cb_related_items' => array(
					'description' => __( 'Get related items', 'jet-engine' ),
					'args'        => array(
						'rel_id' => array(
							'type'        => 'select',
							'label'       => __( 'Relation', 'jet-engine' ),
							'description' => __( 'Select relation to get items from', 'jet-engine' ),
							'options'     => jet_engine()->relations->get_relations_for_js( false, __( 'Select...', 'jet-engine' ) ),
							'value'       => '',
						),
					),
				),
				'jet_engine_custom_cb_render_switcher' => array(
					'description' => __( 'Render human-readable value from switcher field', 'jet-engine' ),
					'args'        => array(
						'field' => array(
							'label'       => __( 'Set field', 'jet-engine' ),
							'description' => __( 'Meta field to get value from', 'jet-engine' ),
							'value'       => '',
						),
						'true_label' => array(
							'label'       => __( 'Label if enabled', 'jet-engine' ),
							'description' => __( 'Show this if switcher is enabled', 'jet-engine' ),
							'value'       => 'On',
						),
						'false_label' => array(
							'label'       => __( 'Label if disabled', 'jet-engine' ),
							'description' => __( 'Show this if switcher is disabled', 'jet-engine' ),
							'value'       => 'Off',
						),
					),
				),
				'jet_engine_custom_cb_render_checkbox' => array(
					'description' => __( 'Render human-readable value from checkbox field', 'jet-engine' ),
					'args'        => array(
						'field' => array(
							'label'       => __( 'Set field', 'jet-engine' ),
							'description' => __( 'Meta field to get value from', 'jet-engine' ),
							'value'       => '',
						),
						'delimiter' => array(
							'label'       => __( 'Delimiter', 'jet-engine' ),
							'description' => __( 'If multiple values checked - them will be separated with this', 'jet-engine' ),
							'value'       => ', ',
						),
					),
				),
				'jet_engine_custom_cb_render_image' => array(
					'description' => __( 'Render image tag by post thumbnail or image from meta', 'jet-engine' ),
					'args'        => array(
						'field' => array(
							'label'       => __( 'Set field', 'jet-engine' ),
							'description' => __( 'Meta field to get value from', 'jet-engine' ),
							'value'       => 'thumbnail',
						),
						'size' => array(
							'label'       => __( 'Size', 'jet-engine' ),
							'description' => __( 'Image size (numeric value)', 'jet-engine' ),
							'value'       => 100,
						),
					),
				),
				'jet_engine_custom_cb_render_gallery' => array(
					'description' => __( 'Render images tags from gallery field', 'jet-engine' ),
					'args'        => array(
						'field' => array(
							'label'       => __( 'Set field', 'jet-engine' ),
							'description' => __( 'Meta field to get value from', 'jet-engine' ),
							'value'       => '',
						),
						'size' => array(
							'label'       => __( 'Size', 'jet-engine' ),
							'description' => __( 'Image size (numeric value)', 'jet-engine' ),
							'value'       => 100,
						),
					),
				),
				'jet_engine_custom_cb_menu_order' => array(
					'description' => __( 'Get menu order value', 'jet-engine' ),
					'args'        => false,
				),
				'jet_engine_custom_cb_render_select' => array(
					'description' => __( 'Render human-readable value from select field or radio field', 'jet-engine' ),
					'args'        => array(
						'field' => array(
							'label'       => __( 'Set field', 'jet-engine' ),
							'description' => __( 'Meta field to get value from', 'jet-engine' ),
							'value'       => '',
						),
						'delimiter' => array(
							'label'       => __( 'Delimiter', 'jet-engine' ),
							'description' => __( 'If multiple values checked - them will be separated with this', 'jet-engine' ),
							'value'       => ', ',
						),
					),
				),
			) );
		}

		/**
		 * Print add/edit page template
		 */
		public function add_page_template() {

			ob_start();
			include jet_engine()->cpt->component_path( 'templates/edit.php' );
			$content = ob_get_clean();
			printf( '<script type="text/x-template" id="jet-cpt-form">%s</script>', $content );

			ob_start();
			include jet_engine()->cpt->component_path( 'templates/filters.php' );
			$content = ob_get_clean();
			printf( '<script type="text/x-template" id="jet-engine-admin-filters">%s</script>', $content );

			ob_start();
			include jet_engine()->cpt->component_path( 'templates/delete-dialog.php' );
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
