<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor section library document.
 *
 * Elementor section library document handler class is responsible for
 * handling a document of a section type.
 *
 */
class Jet_Engine_Not_Supported extends \Elementor\Modules\Library\Documents\Not_Supported {

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

		$properties['cpt'] = array(
			jet_engine()->listings->post_type->slug(),
		);

		$properties['is_editable'] = true;

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
		return 'jet-engine-not-supported';
	}

	public function save_template_type() {
		// Do nothing.
	}
}