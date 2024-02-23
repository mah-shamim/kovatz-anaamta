<?php
namespace Jet_Engine\Macros;

/**
 * Get current page url
 */
class Current_Page_Url extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'current_page_url';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Current Page URL (global)', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array(), $field_value = null ) {
		global $wp;
		return home_url( $wp->request );
	}
}