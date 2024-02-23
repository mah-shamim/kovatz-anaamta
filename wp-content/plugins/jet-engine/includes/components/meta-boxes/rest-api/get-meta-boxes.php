<?php
/**
 * Add/Update post type endpoint
 */

class Jet_Engine_Meta_Boxes_Rest_Get_All extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-meta-boxes';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$meta_boxes = jet_engine()->meta_boxes->data->get_items();
		$meta_boxes = array_map( array( $this, 'prepare_item' ), $meta_boxes );

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $meta_boxes,
		) );

	}

	/**
	 * Prepare post type item to return
	 *
	 * @param  array $item Item data
	 * @return array
	 */
	public function prepare_item( $item ) {

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