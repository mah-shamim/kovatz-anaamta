<?php
namespace Jet_Engine\Modules\Maps_Listings\Geosearch\Controls;

class Terms extends Base {

	public function __construct() {
		$this->register_orderby_option( 'terms' );
		add_action( 'jet-engine/query-builder/terms/controls', array( $this, 'geosearch_controls' ) );
	}

}
