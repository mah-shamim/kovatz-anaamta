<?php
namespace Jet_Engine\Modules\Profile_Builder\Blocks;

use Jet_Engine\Modules\Profile_Builder\Module;

class Profile_Content extends \Jet_Engine_Blocks_Views_Type_Base {

	/**
	 * Returns block name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return 'profile-content';
	}

	/**
	 * Return attributes array
	 *
	 * @return array
	 */
	public function get_attributes() {
		return array();
	}

	/**
	 * Render callback for the profile content widget
	 *
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	public function render_callback( $attributes = array() ) {

		if ( ! empty( $_GET['context'] ) && 'edit' === $_GET['context'] ) {
			return __( 'Profile content', 'jet-engine' );
		}

		ob_start();
		Module::instance()->frontend->render_page_content();
		return ob_get_clean();

	}

}
