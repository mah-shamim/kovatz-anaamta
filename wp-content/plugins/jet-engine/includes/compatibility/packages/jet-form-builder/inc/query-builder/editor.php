<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Form_Builder\Query_Builder;

use Jet_Engine\Compatibility\Packages\Jet_Form_Builder\Package;

class Query_Editor extends \Jet_Engine\Query_Builder\Query_Editor\Base_Query {

	/**
	 * Query type ID
	 */
	public function get_id() {
		return Manager::instance()->slug;
	}

	/**
	 * Query type name
	 */
	public function get_name() {
		return __( 'JetFormBuilder Records', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 *
	 * @return string
	 */
	public function editor_component_name() {
		return 'jet-form-builder-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return mixed|void
	 */
	public function editor_component_data() {

		return array(
			'forms' => \Jet_Engine_Tools::prepare_list_for_js( get_posts( 
				array(
					'post_type' => 'jet-form-builder',
					'posts_per_page' => -1 
				) 
			), 'ID', 'post_title' ),
		);

	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return false|string
	 */
	public function editor_component_template() {
		ob_start();
		include Package::instance()->package_path( 'templates/admin/query-editor.php' );
		return ob_get_clean();
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return string
	 */
	public function editor_component_file() {
		return Package::instance()->package_url( 'assets/js/admin/query-editor.js' );
	}

}
