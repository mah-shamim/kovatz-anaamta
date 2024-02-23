<?php
namespace Jet_Engine\Modules\Maps_Listings\Geosearch\Controls;

class Posts extends Base {

	public function __construct() {
		$this->register_orderby_option( 'posts' );
		add_action( 'jet-engine/query-builder/posts/controls', array( $this, 'geosearch_controls' ) );
	}

}
