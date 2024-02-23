<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Query_Builder;

use Jet_Engine\Modules\Rest_API_Listings\Module;

class REST_API_Query_Editor extends \Jet_Engine\Query_Builder\Query_Editor\Base_Query {

	/**
	 * Qery type ID
	 */
	public function get_id() {
		return Manager::instance()->slug;
	}

	/**
	 * Qery type name
	 */
	public function get_name() {
		return __( 'REST API Query', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_name() {
		return 'jet-rest-api-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return [type] [description]
	 */
	public function editor_component_data() {

		$endpoints = array(
			array(
				'value' => '',
				'label' =>  __( 'Select endpoint...', 'jet-engine' ),
			)
		);

		foreach ( Module::instance()->settings->get() as $endpoint ) {
			$endpoints[] = array(
				'value' => $endpoint['id'],
				'label' => $endpoint['name'],
			);
		}

		return array(
			'endpoints' => $endpoints,
		);

	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_template() {
		ob_start();
		include Module::instance()->module_path( 'templates/admin/query-editor.php' );
		return ob_get_clean();
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_file() {
		return Module::instance()->module_url( 'assets/js/admin/query-editor.js' );
	}

}
