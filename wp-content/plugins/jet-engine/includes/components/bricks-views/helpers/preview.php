<?php
namespace Jet_Engine\Bricks_Views\Helpers;

use Bricks\Helpers;
use Bricks\Query;

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

		$this->post_id = $post_id;

		$post_type                    = $this->get_post_type();
		$je_post_type                 = $post_type === jet_engine()->post_type->slug();
		$is_query_loop_render_element = bricks_is_rest_call() && Query::is_looping();

		if ( $je_post_type || $is_query_loop_render_element ) {
			$this->listing_id = $post_id;
			$this->post_id    = jet_engine()->listings->data->get_current_object_id( $this->get_preview_object() );
		}

		/**
		 * Setup post obj if post or page direct edit with Bricks (#861m48kv4)
		 *
		 * If bricks_is_builder_call(), shouldn't setup post obj if looping.
		 *
		 * @since 1.8
		 */
		$setup_preview_post = Helpers::is_bricks_template( $post_id ) || ( bricks_is_builder_call() && ! Query::is_looping() );
		$is_bricks_template_slug = $post_type === BRICKS_DB_TEMPLATE_SLUG;

		if ( $setup_preview_post && $is_bricks_template_slug ) {
			$this->listing_id = $post_id;

			// STEP: Set post ID to template preview ID if direct edit or single template preview
			$template_settings     = Helpers::get_template_settings( $post_id );
			$template_preview_type = Helpers::get_template_setting( 'templatePreviewType', $post_id );

			// @since 1.8 - Set preview type if direct edit page or post with Bricks (#861m48kv4)
			if ( bricks_is_builder_call() && empty( $template_settings ) && ! Helpers::is_bricks_template( $post_id ) ) {
				$template_preview_type = 'direct-edit';
			}

			if ( in_array( $template_preview_type, [ 'direct-edit', 'single' ] ) ) {
				// @since 1.8 - If direct edit page or post with Bricks, use the $post_id (#861m48kv4)
				$template_preview_post_id = ( $template_preview_type === 'direct-edit' ) ? $post_id : Helpers::get_template_setting( 'templatePreviewPostId', $post_id );

				if ( $template_preview_post_id ) {
					$this->post_id = $template_preview_post_id;
				}
			}
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

		if ( bricks_is_rest_call() && Query::is_looping() ) {
			return Query::get_loop_object();
		} elseif ( $post_type === jet_engine()->post_type->slug() ) {
			return $this->get_listing_preview()->get_preview_object();
		} else {
			return get_post( $this->get_post_id() );
		}
	}

}
