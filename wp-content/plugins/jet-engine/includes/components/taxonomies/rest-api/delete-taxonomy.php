<?php
/**
 * Delete tax endpoint
 */

class Jet_Engine_CPT_Rest_Delete_Taxonomy extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'delete-taxonomy';
	}

	/**
	 * API callback
	 *
	 * @return void|WP_Error|WP_REST_Response
	 */
	public function callback( $request ) {

		$params = $request->get_params();

		$action = $params['action'];
		$id     = $params['id'];

		if ( ! $id ) {

			jet_engine()->taxonomies->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->taxonomies->get_notices(),
			) );

		}

		$tax_data = jet_engine()->taxonomies->data->get_item_for_edit( $id );

		if ( ! $tax_data || ! isset( $tax_data['general_settings']['slug'] ) ) {

			jet_engine()->taxonomies->add_notice(
				'error',
				__( 'Item data not found', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->taxonomies->get_notices(),
			) );

		}

		$from_tax = $tax_data['general_settings']['slug'];

		if ( 'delete' === $action ) {
			$this->delete_terms( $from_tax );
		}

		jet_engine()->taxonomies->data->set_request( array( 'id' => $id ) );

		if ( jet_engine()->taxonomies->data->delete_item( false ) ) {

			do_action( 'jet-engine/taxonomies/deleted-taxonomy', $from_tax );

			return rest_ensure_response( array(
				'success' => true,
			) );
		} else {
			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->taxonomies->get_notices(),
			) );
		}

	}

	/**
	 * Delete posts
	 *
	 * @return [type] [description]
	 */
	public function delete_terms( $from_tax ) {

		$terms = get_terms( array(
			'taxonomy'   => $from_tax,
			'hide_empty' => false,
			'fields'     => 'ids',
		) );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}

		foreach ( $terms as $term_id ) {
			wp_delete_term( $term_id, $from_tax );
		}

	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELETE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'DELETE';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get query param. Regex with query parameters
	 *
	 * @return string
	 */
	public function get_query_params() {
		return '(?P<id>[\d]+)';
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array(
			'action' => array(
				'default'  => 'none',
				'required' => true,
			),
		);
	}

}