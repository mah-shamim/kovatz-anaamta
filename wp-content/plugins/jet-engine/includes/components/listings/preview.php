<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Listings_Legacy class
 */
class Jet_Engine_Listings_Preview {

	private $args = false;
	private $settings = array();
	private $listing_id = null;
	private $document = null;
	private $is_set = false;

	public static $is_preview = false;

	public function __construct( $settings = [], $listing_id = null ) {
		
		$this->settings   = $settings;
		$this->listing_id = $listing_id;

		$this->setup_preview();

	}

	public function get_settings( $key = false ) {
		return $this->get_preview_document()->get_settings( $key );
	}

	public function get_preview_document() {
		
		if ( ! $this->document ) {
			$this->document = jet_engine()->listings->get_new_doc( $this->settings, $this->listing_id );
		}

		return $this->document;
	}

	public function setup_preview() {

		self::$is_preview = true;

		if ( $this->is_set ) {
			return;
		}

		$document = $this->get_preview_document();

		jet_engine()->listings->data->set_listing( $document );

		$source = $document->get_settings( 'listing_source' );

		switch ( $source ) {

			case 'posts':
			case 'repeater':

				$post_type = $document->get_settings( 'listing_post_type' );

				$post = get_posts( apply_filters( 'jet-engine/listings/document/post-preview-query-args', [
					'post_type'        => $post_type,
					'numberposts'      => 1,
					'orderby'          => 'date',
					'order'            => 'DESC',
					'suppress_filters' => false,
				], $document ) );

				if ( ! empty( $post ) ) {

					jet_engine()->listings->data->set_current_object( $post[0] );

					$this->args = apply_filters( 'jet-engine/listings/document/preview-args', [
						'post_type' => $post_type,
						'p'         => $post[0]->ID,
					], $document );

				}

				break;

			case 'terms':

				$tax = $document->get_settings( 'listing_tax' );

				$terms = get_terms( array(
					'taxonomy'   => $tax,
					'hide_empty' => false,
				) );

				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

					jet_engine()->listings->data->set_current_object( $terms[0] );

					$this->args = apply_filters( 'jet-engine/listings/document/preview-args', [
						'tax_query' => [
							[
								'taxonomy' => $tax,
								'field'    => 'slug',
								'terms'    => $terms[0]->slug,
							],
						],
					], $document );

				}

				break;

			case 'users':

				jet_engine()->listings->data->set_current_object( wp_get_current_user() );

				break;

			default:

				do_action( 'jet-engine/listings/document/get-preview/' . $source, $document, $this );

				break;

		}

		$this->is_set = true;

	}

	public function get_preview_object() {
		$this->setup_preview();
		return jet_engine()->listings->data->get_current_object();
	}

	public function get_preview_args() {
		return $this->args;
	}

}
