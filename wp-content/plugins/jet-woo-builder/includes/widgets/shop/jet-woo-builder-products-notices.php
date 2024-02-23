<?php
/**
 * Class: Jet_Woo_Builder_Products_Notices
 * Name: Products Notices
 * Slug: jet-woo-builder-products-notices
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Products_Notices extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-woo-builder-products-notices';
	}

	public function get_title() {
		return __( 'Products Notices', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-shop-notice';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-shop-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'shop' );
	}

	protected function render() {

		$this->__open_wrap();

		if ( ! jet_woo_builder()->elementor_views->in_elementor() ) {
			wc_print_notices();
		}

		$this->__close_wrap();

	}
}
