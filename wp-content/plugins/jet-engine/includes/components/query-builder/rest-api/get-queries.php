<?php
namespace Jet_Engine\Query_Builder\Rest;

use Jet_Engine\Query_Builder\Manager;

class Get_Queries extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-queries';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$items = Manager::instance()->data->get_items();
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

		$item['args']   = maybe_unserialize( $item['args'] );
		$item['labels'] = maybe_unserialize( $item['labels'] );

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
