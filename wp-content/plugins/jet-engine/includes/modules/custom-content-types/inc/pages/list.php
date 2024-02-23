<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Pages;

use Jet_Engine\Modules\Custom_Content_Types\Module;
use Jet_Engine\Modules\Custom_Content_Types\DB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Types_List extends \Jet_Engine_CPT_Page_Base {

	public $is_default = true;

	/**
	 * Class constructor
	 */
	public function __construct( $manager ) {

		parent::__construct( $manager );

		add_action( 'jet-engine/cct/page/after-title', array( $this, 'add_new_btn' ) );
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
			'popup_title' => __( 'How to work with Custom Content Types', 'jet-engine' ),
			'embed' => 'https://www.youtube.com/embed/m9lfFsm1NbE',
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
		return esc_html__( 'Custom Content Types List', 'jet-engine' );
	}

	/**
	 * Register add controls
	 * @return [type] [description]
	 */
	public function page_specific_assets() {

		$module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );

		$ui = new \CX_Vue_UI( $module_data );

		$ui->enqueue_assets();

		wp_register_script(
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

		wp_register_script(
			'jet-engine-cct-copy-dialog',
			Module::instance()->module_url( 'assets/js/admin/copy-dialog.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch', ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script(
			'jet-engine-cct-copy-dialog',
			'JetEngineCCTCopyDialog',
			array(
				'api_path' => jet_engine()->api->get_route( 'add-content-type' ),
				'notices'  => array(
					'copied'     => __( 'Copied!', 'jet-engine' ),
					'slug_error' => __( 'Please, set unique slug!', 'jet-engine' ),
				),
			)
		);

		wp_enqueue_script(
			'jet-engine-cct-list',
			Module::instance()->module_url( 'assets/js/admin/list.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch', 'jet-engine-cct-delete-dialog', 'jet-engine-cct-copy-dialog' ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script(
			'jet-engine-cct-list',
			'JetEngineCCTListConfig',
			array(
				'api_path'  => jet_engine()->api->get_route( 'get-content-types' ),
				'edit_link' => $this->manager->get_edit_item_link( '%id%' ),
				'db_prefix' => DB::table_prefix(),
			)
		);

		add_action( 'admin_footer', array( $this, 'add_page_template' ) );

	}

	/**
	 * Print add/edit page template
	 */
	public function add_page_template() {

		ob_start();
		include Module::instance()->module_path( 'templates/admin/list.php' );
		$content = ob_get_clean();
		printf( '<script type="text/x-template" id="jet-cct-list">%s</script>', $content );

		ob_start();
		include Module::instance()->module_path( 'templates/admin/delete-dialog.php' );
		$content = ob_get_clean();
		printf( '<script type="text/x-template" id="jet-cct-delete-dialog">%s</script>', $content );

		ob_start();
		include Module::instance()->module_path( 'templates/admin/copy-dialog.php' );
		$content = ob_get_clean();
		printf( '<script type="text/x-template" id="jet-cct-copy-dialog">%s</script>', $content );

	}

	/**
	 * Renderer callback
	 *
	 * @return void
	 */
	public function render_page() {

		?>
		<br>
		<style type="text/css">
			.list-table-heading__cell,
			.list-table-item__cell {
				flex: 0 0 25%;
				max-width: 25%;
			}
		</style>
		<div id="jet_cct_list"></div>
		<?php

	}

}
