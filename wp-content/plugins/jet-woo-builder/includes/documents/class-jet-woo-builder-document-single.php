<?php
/**
 * Class: Jet_Woo_Builder_Document
 * Name: Single Product Template
 * Slug: jet-woo-builder
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


class Jet_Woo_Builder_Document extends Jet_Woo_Builder_Document_Base {

	public function get_name() {
		return 'jet-woo-builder';
	}

	public static function get_title() {
		return esc_html__( 'Jet Woo Single Template', 'jet-woo-builder' );
	}

	public static function get_properties() {

		$properties = parent::get_properties();

		$properties['woo_builder_template_settings'] = true;

		return $properties;

	}

	public function get_wp_preview_url() {

		$main_post_id   = $this->get_main_id();
		$sample_product = get_post_meta( $main_post_id, '_sample_product', true );

		if ( ! $sample_product ) {
			$sample_product = $this->query_first_product();
		}

		$product_id = $sample_product;

		return add_query_arg(
			[
				'preview_nonce'    => wp_create_nonce( 'post_preview_' . $main_post_id ),
				'jet_woo_template' => $main_post_id,
			],
			get_permalink( $product_id )
		);

	}

	public function get_preview_as_query_args() {

		jet_woo_builder()->documents->set_current_type( $this->get_name() );

		$args    = [];
		$product = $this->query_first_product();

		if ( ! empty( $product ) ) {
			$args = [
				'post_type' => 'product',
				'p'         => $product,
			];
		}

		return $args;

	}

}
