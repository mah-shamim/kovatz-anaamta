<?php
namespace Jet_Engine\Bricks_Views\Helpers;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
class Preview {

	private $post_id    = null;
	private $listing_id = null;
	private $post_type  = null;
	private $listing    = null;
	private $preview    = null;

	public function __construct( $post_id = null ) {
		
		if ( ! $post_id ) {
			return;
		}

		if ( ! function_exists( 'jet_engine' ) ) {
			return;
		}

		$this->post_id = $post_id;

		$post_type = $this->get_post_type();

		if ( $post_type === jet_engine()->post_type->slug() ) {
			$this->listing_id = $post_id;
			$this->post_id    = jet_engine()->listings->data->get_current_object_id( $this->get_preview_object() );
		}
	}

	public function get_post_type() {
		
		if ( null === $this->post_type ) {
			$post = get_post( $this->get_post_id() );
			$this->post_type = $post->post_type;
		}

		return $this->post_type;
	}

	public function setup_preview_for_render( $render ) {
		$render->setup_listing( $this->get_listing(), $this->get_object_id(), true, $this->get_listing_id() );
	}

	public function get_listing_preview() {
		
		if ( ! $this->preview ) {
			$this->preview = new \Jet_Engine_Listings_Preview( [], $this->get_listing_id() );
		}

		return $this->preview;

	}

	public function get_listing() {
		
		if ( null === $this->listing ) {

			$post_type = $this->get_post_type();

			if ( $post_type === jet_engine()->post_type->slug() ) {
				$this->listing = $this->get_listing_preview()->get_preview_document()->get_settings();
			} else {
				$this->listing = [
					'listing_source'    => 'posts',
					'listing_post_type' => $post_type,
				];
			}
		}
		
		return $this->listing;

	}

	public function get_post_id() {
		return $this->post_id;
	}

	public function get_object_id() {
		
		$post_type = $this->get_post_type();

		if ( $post_type === jet_engine()->post_type->slug() ) {
			return jet_engine()->listings->data->get_current_object_id( $this->get_preview_object() );
		} else {
			return $this->get_post_id();
		}

	}

	public function get_listing_id() {
		return $this->listing_id;
	}

	public function get_preview_object() {
		
		$post_type = $this->get_post_type();

		if ( $post_type === jet_engine()->post_type->slug() ) {
			return $this->get_listing_preview()->get_preview_object();
		} else {
			return get_post( $this->get_post_id() );
		}
	}

}
