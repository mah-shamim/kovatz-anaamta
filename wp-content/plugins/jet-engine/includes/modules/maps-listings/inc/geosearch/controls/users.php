<?php
namespace Jet_Engine\Modules\Maps_Listings\Geosearch\Controls;

class Users extends Base {

	public function __construct() {
		$this->register_orderby_option( 'users' );
		add_action( 'jet-engine/query-builder/users/controls', array( $this, 'geosearch_controls' ) );
	}

}
