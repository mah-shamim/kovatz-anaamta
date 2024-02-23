<?php
/**
 * Class: Jet_Woo_Builder_Document_Not_Supported
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Woo_Builder_Document_Not_Supported extends Elementor\Modules\Library\Documents\Not_Supported {

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @access public
	 * @static
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {

		$properties = parent::get_properties();

		$properties['cpt'] = [ 'jet-woo-builder' ];

		return $properties;

	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @access public
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'jet-woo-builder-not-supported';
	}

	public function save_template_type() {
	}

}