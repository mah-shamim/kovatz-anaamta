<?php
/**
 * Get options page endpoint
 */

class Jet_Engine_Rest_Get_Options_Pages extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-options-pages';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$items = jet_engine()->options_pages->data->get_items();
		$items = array_map( array( $this, 'prepare_item' ), $items );

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $items,
		) );

	}

	/**
	 * Prepare post type item to return
	 *
	 * @param  array $item Item data
	 * @return array
	 */
	public function prepare_item( $item ) {

		$item['labels'] = maybe_unserialize( $item['labels'] );
		$item['args']   = maybe_unserialize( $item['args'] );
		$item['fields'] = maybe_unserialize( $item['meta_fields'] );

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