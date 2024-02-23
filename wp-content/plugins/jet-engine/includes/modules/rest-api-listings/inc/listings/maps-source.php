<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Listings;

class Rest_API_Maps_Source extends \Jet_Engine\Modules\Maps_Listings\Source\SQL {

	/**
	 * Returns source ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'rest_api';
	}

	public function get_obj_by_id( $id ) {

		$id_data    = explode( '-', $id );
		$query_id   = isset( $id_data[0] ) ? $id_data[0] : false;
		$item_index = isset( $id_data[1] ) ? $id_data[1] : false;

		if ( false === $query_id || false === $item_index ) {
			return false;
		}

		$query = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $query_id );

		if ( ! $query ) {
			return false;
		}

		$query->setup_query();

		$items = $query->get_items();

		return isset( $items[ $item_index ] ) ? $items[ $item_index ] : false;
	}

	public function get_failure_key( $obj ) {
		return 'Rest API Item #' . $obj->_rest_api_item_id;
	}

}
