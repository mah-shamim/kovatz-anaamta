<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Query_Builder;

use Jet_Engine\Modules\Maps_Listings\Geosearch\Controls\Base;

class Geosearch_Controls extends Base {

	public function __construct() {
		$this->register_orderby_option( 'cct' );
		
		add_action( 'jet-engine/custom-content-types/query-builder-controls', array( $this, 'geosearch_controls_list' ) );
		add_action( 'jet-engine/query-builder/editor/after-enqueue-scripts', array( $this, 'geosearch_controls_inline_css' ) );
	}

	public function geosearch_controls_inline_css() {
		?>
		<style>
			.cx-vui-panel .cx-vui-component--geosearch {
				padding: 20px;
			}
		</style>
		<?php
	}

}
