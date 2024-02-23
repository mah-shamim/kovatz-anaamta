<?php
namespace Jet_Engine\Query_Builder\Conditions;

use Jet_Engine\Query_Builder\Manager;

class Has_Not_Items extends Has_Items {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'query-has-not-items';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Query Has Not Items', 'jet-engine' );
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {
		return ! parent::check( $args );
	}

}
