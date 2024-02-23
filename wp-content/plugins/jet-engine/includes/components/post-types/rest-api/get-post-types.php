<?php
/**
 * Add/Update post type endpoint
 */

class Jet_Engine_CPT_Rest_Get_Post_Types extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-post-types';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$post_types = jet_engine()->cpt->data->get_items();
		$post_types = array_map( array( $this, 'prepare_post_type' ), $post_types );

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $post_types,
		) );

	}

	/**
	 * Prepare post type item to return
	 *
	 * @param  array $item Item data
	 * @return array
	 */
	public function prepare_post_type( $item ) {

		$item['labels']      = maybe_unserialize( $item['labels'] );
		$item['args']        = maybe_unserialize( $item['args'] );
		$item['meta_fields'] = maybe_unserialize( $item['meta_fields'] );

		return $item;
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'GET';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

}