<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Pages;

use Jet_Engine\Modules\Custom_Content_Types\Module;
use Jet_Engine\Modules\Custom_Content_Types\DB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Edit extends \Jet_Engine_CPT_Page_Base {

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
			return esc_html__( 'Edit Content Type', 'jet-engine' );
		} else {
			return esc_html__( 'Add New Content Type', 'jet-engine' );
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

		$ui = new \CX_Vue_UI( $module_data );

		\CX_Vue_UI::$templates_path = Module::instance()->module_path( 'templates/admin/rewrite/' );

		$ui->enqueue_assets();

		if ( ! class_exists( '\Jet_Engine_Meta_Boxes_Page_Edit' ) ) {
			require_once jet_engine()->plugin_path( 'includes/components/meta-boxes/pages/edit.php' );
			\Jet_Engine_Meta_Boxes_Page_Edit::enqueue_meta_fields( array(
				'title'    => __( 'Fields', 'jet-engine' ),
				'button'   => __( 'New Field', 'jet-engine' ),
				'disabled' => array(),
			) );
		}

		wp_enqueue_script(
			'jet-engine-cct-delete-dialog',
			Module::instance()->module_url( 'assets/js/admin/delete-dialog.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch', ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script(
			'jet-engine-cct-delete-dialog',
			'JetEngineCCTDeleteDialog',
			array(
				'api_path' => jet_engine()->api->get_route( 'delete-content-type' ),
				'redirect' => $this->manager->get_page_link( 'list' ),
			)
		);

		wp_enqueue_script(
			'jet-engine-cct-edit',
			Module::instance()->module_url( 'assets/js/admin/edit.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch' ),
			jet_engine()->get_version(),
			true
		);

		$id = $this->item_id();

		if ( $id ) {
			$button_label = __( 'Update Content Type', 'jet-engine' );
			$redirect     = false;
		} else {
			$button_label = __( 'Add Content Type', 'jet-engine' );
			$redirect     = $this->manager->get_edit_item_link( '%id%' );
		}

		wp_localize_script(
			'jet-engine-cct-edit',
			'JetEngineCCTConfig',
			$this->manager->get_admin_page_config( array(
				'api_path_edit'     => jet_engine()->api->get_route( $this->get_slug() . '-content-type' ),
				'item_id'           => $id,
				'edit_button_label' => $button_label,
				'redirect'          => $redirect,
				'post_types'        => \Jet_Engine_Tools::get_post_types_for_js(),
				'db_prefix'         => DB::table_prefix(),
				'positions'         => $this->get_positions(),
				'default_position'  => \Jet_Engine_Tools::get_default_menu_position(),
				'rest_base'         => rest_url( '/jet-cct/' ),
				'service_fields'    => Module::instance()->manager->get_service_fields( array(
					'add_id_field' => true,
					'has_single'   => true,
				) ),
				'common_api_args'   => Module::instance()->rest_controller->get_common_args(),
				'help_links'        => array(
					array(
						'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-create-a-custom-content-type/?utm_source=jetengine&utm_medium=custom-content-type&utm_campaign=need-help',
						'label' => __( 'How to Create a Custom Content Type', 'jet-engine' ),
					),
					array(
						'url'   => 'https://crocoblock.com/wp-content/uploads/2020/11/Croco-blog-post-760x690-1-1024x930.png',
						'label' => __( 'How to choose: Custom Content Type vs Custom Post Type', 'jet-engine' ),
					),
				),
			) )
		);

		wp_add_inline_style( 'common', 'input.cx-vui-input[disabled="disabled"] {opacity:.5;}' );

		add_action( 'admin_footer', array( $this, 'add_page_template' ) );

	}

	/**
	 * Returns available positions list
	 *
	 * @return [type] [description]
	 */
	public function get_positions() {
		return apply_filters(
			'jet-engine/options-pages/available-positions',
			\Jet_Engine_Tools::get_available_menu_positions()
		);
	}

	/**
	 * Print add/edit page template
	 */
	public function add_page_template() {

		ob_start();
		include Module::instance()->module_path( 'templates/admin/edit.php' );
		$content = ob_get_clean();

		printf( '<script type="text/x-template" id="jet-cct-form">%s</script>', $content );

		ob_start();
		include Module::instance()->module_path( 'templates/admin/delete-dialog.php' );
		$content = ob_get_clean();
		printf( '<script type="text/x-template" id="jet-cct-delete-dialog">%s</script>', $content );

	}

	/**
	 * Adds template for meta fields component
	 */
	public static function add_meta_fields_template() {

		ob_start();
		include jet_engine()->get_template( 'admin/pages/meta-boxes/fields.php' );
		$content = ob_get_clean();

		printf( '<script type="text/x-template" id="jet-meta-fields">%s</script>', $content );

	}

	/**
	 * Renderer callback
	 *
	 * @return void
	 */
	public function render_page() {
		?>
		<br>
		<div id="jet_cct_form"></div>
		<?php
	}

}
