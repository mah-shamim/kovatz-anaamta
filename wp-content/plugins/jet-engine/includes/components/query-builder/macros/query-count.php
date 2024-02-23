<?php
namespace Jet_Engine\Query_Builder\Macros;

use Jet_Engine\Query_Builder\Manager;

class Query_Count_Macro extends \Jet_Engine_Base_Macros {

	use \Jet_Engine\Query_Builder\Traits\Query_Count_Trait;

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'query_count';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return $this->get_title();
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {
		return$this->get_args();
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {
		return $this->get_result( $args );
	}
}