<?php
/**
 * Class: Jet_Woo_Builder_Shop_Document
 * Name: Shop Template
 * Slug: jet-woo-builder-shop
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Woo_Builder_Shop_Document extends Jet_Woo_Builder_Document_Base {

	public function get_name() {
		return 'jet-woo-builder-shop';
	}

	public static function get_title() {
		return esc_html__( 'Jet Woo Shop Template', 'jet-woo-builder' );
	}

	public static function get_properties() {

		$properties = parent::get_properties();

		$properties['woo_builder_template_settings'] = true;

		return $properties;

	}

	public function get_preview_as_query_args() {

		jet_woo_builder()->documents->set_current_type( $this->get_name() );

		$args = [];

		$products = get_posts(
			[
				'post_type'      => 'product',
				'posts_per_page' => 5,
			]
		);

		if ( ! empty( $products ) ) {
			$args = [
				'post_type' => 'product',
				'p'         => $products,
			];
		}

		wc_set_loop_prop( 'total', 20 );
		wc_set_loop_prop( 'total_pages', 3 );

		return $args;

	}

}