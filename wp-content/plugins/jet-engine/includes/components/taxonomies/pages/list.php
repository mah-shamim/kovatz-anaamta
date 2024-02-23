<?php
/**
 * Taxonomies list page
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Tax_Page_List' ) ) {

	/**
	 * Define Jet_Engine_CPT_Tax_Page_List class
	 */
	class Jet_Engine_CPT_Tax_Page_List extends Jet_Engine_CPT_Page_Base {

		public $is_default = true;
		public $engine_types = null;

		/**
		 * Class constructor
		 */
		public function __construct( $manager ) {

			parent::__construct( $manager );

			add_action( 'jet-engine/taxonomies/page/after-title', array( $this, 'add_new_btn' ) );
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
				'popup_title' => __( 'How to add a new Custom Taxonomy?', 'jet-engine' ),
				'embed' => 'https://www.youtube.com/embed/hxYkMiKNk-E',
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
			return esc_html__( 'Custom Taxonomies List', 'jet-engine' );
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
				jet_engine()->taxonomies->component_url( 'assets/js/delete-dialog.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch', ),
				jet_engine()->get_version(),
				true
			);

			wp_localize_script(
				'jet-engine-cpt-delete-dialog',
				'JetEngineCPTDeleteDialog',
				array(
					'taxonomies' => Jet_Engine_Tools::get_taxonomies_for_js(),
					'api_path'   => jet_engine()->api->get_route( 'delete-taxonomy' ),
					'redirect'   => $this->manager->get_page_link( 'list' ),
				)
			);

			wp_enqueue_script(
				'jet-engine-cpt-list',
				jet_engine()->taxonomies->component_url( 'assets/js/list.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch', 'jet-engine-cpt-delete-dialog' ),
				jet_engine()->get_version(),
				true
			);

			wp_localize_script(
				'jet-engine-cpt-list',
				'JetEngineCPTListConfig',
				array(
					'api_path'       => jet_engine()->api->get_route( 'get-taxonomies' ),
					'api_path_copy'  => jet_engine()->api->get_route( 'copy-taxonomy' ),
					'edit_link'      => $this->manager->get_edit_item_link( '%id%' ),
					'built_in_types' => $this->get_built_in_types(),
					'engine_types'   => array_values( $this->get_engine_types() ),
					'notices'        => array(
						'copied' => __( 'Copied!', 'jet-engine' ),
					),
				)
			);

			add_action( 'admin_footer', array( $this, 'add_page_template' ) );

		}

		/**
		 * Returns post types registered by JetEngine
		 *
		 * @return [type] [description]
		 */
		public function get_engine_types() {

			if ( null !== $this->engine_types ) {
				return $this->engine_types;
			}

			$this->engine_types = array();

			$items = $this->manager->data->get_items();

			if ( ! empty( $items ) ) {
				foreach ( $items as $item ) {

					$item['labels'] = maybe_unserialize( $item['labels'] );
					$item['args']   = maybe_unserialize( $item['args'] );

					$this->engine_types[ $item['slug'] ] = array(
						'slug'   => $item['slug'],
						'id'     => $item['id'],
						'labels' => array(
							'name' => $item['labels']['name'],
						),
					);

					if ( ! empty( $item['args']['rewrite'] ) && ! empty( $item['args']['rewrite_slug'] )
						&& $item['slug'] !== $item['args']['rewrite_slug']
					) {
						$this->engine_types[ $item['slug'] ]['rewrite_slug'] = $item['args']['rewrite_slug'];
					}
				}
			}

			return $this->engine_types;

		}

		/**
		 * Get built-in post types
		 *
		 * @return [type] [description]
		 */
		public function get_built_in_types() {

			$objects  = get_taxonomies( array(), 'objects' );
			$result   = array();
			$excluded = apply_filters( 'jet-engine/taxonomies/excluded-built-in-types', array(
				'link_category',
				'wp_theme',
				'post_format',
				'nav_menu',
			) );

			$engine_types = $this->get_engine_types();

			foreach ( $objects as $object ) {

				if ( in_array( $object->name, $excluded ) || isset( $engine_types[ $object->name ] ) ) {
					continue;
				}

				$tax_config = array(
					'slug'   => $object->name,
					'id'     => -1,
					'labels' => array(
						'name' => $object->label,
					),
				);

				if ( ! empty( $object->rewrite ) && ! empty( $object->rewrite['slug'] )
					&& $object->name !== $object->rewrite['slug']
				) {
					$tax_config['rewrite_slug'] = $object->rewrite['slug'];
				}

				$result[] = $tax_config;

			}

			return $result;

		}

		/**
		 * Print add/edit page template
		 */
		public function add_page_template() {

			ob_start();
			include jet_engine()->taxonomies->component_path( 'templates/list.php' );
			$content = ob_get_clean();
			printf( '<script type="text/x-template" id="jet-cpt-list">%s</script>', $content );

			ob_start();
			include jet_engine()->taxonomies->component_path( 'templates/delete-dialog.php' );
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