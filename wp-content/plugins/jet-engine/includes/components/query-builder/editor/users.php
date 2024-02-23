<?php
namespace Jet_Engine\Query_Builder\Query_Editor;

use Jet_Engine\Query_Builder\Manager;

class Users_Query extends Base_Query {

	/**
	 * Qery type ID
	 */
	public function get_id() {
		return 'users';
	}

	/**
	 * Qery type name
	 */
	public function get_name() {
		return __( 'Users Query', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_name() {
		return 'jet-users-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_template() {
		ob_start();
		include Manager::instance()->component_path( 'templates/admin/types/users.php' );
		return ob_get_clean();
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_data() {
		return apply_filters( 'jet-engine/query-builder/types/users-query/data', array(
			'roles' => \Jet_Engine_Tools::get_user_roles_for_js(),
		) );
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 * I
	 * @return [type] [description]
	 */
	public function editor_component_file() {
		return Manager::instance()->component_url( 'assets/js/admin/types/users.js' );
	}

}
