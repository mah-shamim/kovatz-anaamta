<?php
/**
 * Timber editor render class
 */
namespace Jet_Engine\Timber_Views\View;

use Jet_Engine\Timber_Views\Package;
use Timber\Timber;
use Timber\Post;
use Timber\Loader;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Render {

	private $rendered_css = [];
	private $twig = null;
	
	public function __construct() {
		add_filter( 'jet-engine/listing/content/twig', array( $this, 'get_listing_content' ), 10, 2 );
	}

	public function get_listing_content( $content, $listing_id ) {

		jet_engine()->listings->ensure_listing_doc_class();

		$html = \Jet_Engine_Listings_Document::get_listing_html_by_id( $listing_id );
		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! $this->twig ) {
			$dummy_loader = new Loader();
			$this->twig = $dummy_loader->get_twig();
		}

		return $this->get_listing_css( $listing_id ) . Package::instance()->render_html(
			$html,
			Package::instance()->get_context_for_object( $current_object ),
			$this->twig
		);

	}

	public function get_listing_css( $listing_id ) {
		
		if ( in_array( $listing_id, $this->rendered_css ) ) {
			return;
		}

		$this->rendered_css[] = $listing_id;

		return sprintf(
			'<style>%s</style>',
			str_replace( 'selector', '.jet-listing-grid--' . $listing_id, \Jet_Engine_Listings_Document::get_listing_css_by_id( $listing_id )
		) );

	}

}
