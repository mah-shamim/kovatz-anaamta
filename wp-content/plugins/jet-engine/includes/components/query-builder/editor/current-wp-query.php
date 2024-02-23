<?php
namespace Jet_Engine\Query_Builder\Query_Editor;

use Jet_Engine\Query_Builder\Manager;
use Jet_Engine\Query_Builder\Helpers\Posts_Per_Page_Manager;

class Current_WP_Query extends Posts_Query {

	/**
	 * Qery type ID
	 */
	public function get_id() {
		return 'current-wp-query';
	}

	/**
	 * Qery type name
	 */
	public function get_name() {
		return __( 'Current WP Query', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_name() {
		return 'jet-wp-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * 
	 * @return [type] [description]
	 */
	public function editor_component_data() {
		return apply_filters( 'jet-engine/query-builder/types/current-wp-query/data', array(
			'page_types_options' => Posts_Per_Page_Manager::instance()->get_options(),
		) );
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_template() {
		ob_start();
		include Manager::instance()->component_path( 'templates/admin/types/current-wp-query.php' );
		return ob_get_clean();
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_file() {
		return Manager::instance()->component_url( 'assets/js/admin/types/current-wp-query.js' );
	}

}
