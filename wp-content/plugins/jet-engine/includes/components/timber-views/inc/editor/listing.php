<?php
/**
 * Timber editor views for listing
 */
namespace Jet_Engine\Timber_Views\Editor;

use Jet_Engine\Timber_Views\Package;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Listing {

	public function __construct() {

		add_filter(
			'jet-engine/templates/listing-views',
			[ $this, 'add_view' ] 
		);

		add_filter(
			'jet-engine/templates/edit-url/' . Package::instance()->get_view_slug(),
			[ $this, 'edit_url' ], 10, 2
		);

		add_action(
			'jet-engine/templates/created/' . Package::instance()->get_view_slug(),
			[ $this, 'set_template_meta' ] 
		);

		add_filter(
			'get_edit_post_link',
			[ $this, 'change_default_edit_url' ], 10, 2
		);

	}

	public function change_default_edit_url( $url, $post_id ) {
		
		$listing_type = jet_engine()->listings->data->get_listing_type( $post_id );
		
		if ( Package::instance()->get_view_slug() === $listing_type ) {
			$url = $this->edit_url( $url, $post_id );
		}

		return $url;
	}

	public function set_template_meta( $post_id ) {
	}

	public function edit_url( $url, $post_id ) {
		return add_query_arg( [
			'post' => $post_id,
			'action' => Package::instance()->editor->get_editor_trigger() 
		], admin_url( 'post.php' ) );
	}

	public function add_view( $views ) {
		$views[ Package::instance()->get_view_slug() ] = __( 'Timber/Twig', 'jet-engine' );
		return $views;
	}

}
