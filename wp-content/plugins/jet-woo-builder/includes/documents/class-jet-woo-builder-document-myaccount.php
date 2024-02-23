<?php
/**
 * Class: Jet_Woo_Builder_MyAccount_Document
 * Name: My Account Page Template
 * Slug: jet-woo-builder-myaccount
 */

use Elementor\Controls_Manager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Woo_Builder_MyAccount_Document extends Jet_Woo_Builder_Document_Base {

	public function get_name() {
		return 'jet-woo-builder-myaccount';
	}

	public static function get_title() {
		return esc_html__( 'Jet Woo My Account Template', 'jet-woo-builder' );
	}

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['woo_builder_template_settings'] = true;

		return $properties;
	}

	public function get_preview_as_query_args() {
		jet_woo_builder()->documents->set_current_type( $this->get_name() );
	}

}