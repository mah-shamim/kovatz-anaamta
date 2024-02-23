<?php
namespace Jet_Smart_Filters\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class TaxonomyTerms extends Base {

	public function get_name() {

		return 'taxonomy-terms';
	}

	public function get_args() {

		return array(
			'taxonomy' => array(
				'required' => true,
			)
		);
	}

	public function callback( $request ) {

		$args = $request->get_params();

		// Taxonomy
		$tax = $args['taxonomy'];

		$args = array(
			'taxonomy'   => $tax,
			'hide_empty' => $hide_empty
		);

		$terms = get_terms( $args );
		$response = wp_list_pluck( $terms, 'name', 'term_id' );

		return rest_ensure_response( $response );
	}
}
