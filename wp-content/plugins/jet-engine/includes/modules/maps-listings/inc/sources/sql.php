<?php
namespace Jet_Engine\Modules\Maps_Listings\Source;

class SQL extends Base {

	/**
	 * Returns source ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'sql';
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

		// TODO: setup filtered props

		$advanced_query = $query->get_advanced_query();

		if ( ! $advanced_query ) {
			$offset = ! empty( $query->final_query['offset'] ) ? absint( $query->final_query['offset'] ) : 0;

			$query->final_query['limit_per_page'] = 1;
			$query->final_query['offset'] = $offset + $item_index;

			$item_index = 0;
		}

		$items = $query->get_items();

		return isset( $items[ $item_index ] ) ? $items[ $item_index ] : false;
	}

	public function update_field_value( $obj, $field, $value ) {}

	public function get_field_value( $obj, $field ) {
		return isset( $obj->$field ) ? $obj->$field : false;
	}

	public function get_failure_key( $obj ) {
		return 'SQL Query Item #' . $obj->sql_query_item_id;
	}

	public function get_field_coordinates( $obj, $location_string = '', $field_name = null ) {

		if ( ! $field_name ) {
			$field_name = $this->lat_lng->meta_key;
		}

		$location_hash = $this->get_field_value( $obj, $field_name . '_hash' );

		if ( ! $location_hash ) {
			$field_hash    = md5( $field_name );
			$location_hash = $this->get_field_value( $obj, $field_hash . '_hash' );
		}

		// Try to get from transient
		if ( ! $location_hash ) {
			$coord = $this->lat_lng->get_from_transient( $location_string );

			return array(
				'key'   => md5( $location_string ),
				'coord' => $coord,
			);
		}

		return array(
			'key'   => $location_hash,
			'coord' => array(
				'lat' => $this->get_field_value( $obj, $field_name . '_lat' ),
				'lng' => $this->get_field_value( $obj, $field_name . '_lng' ),
			),
		);
	}

}
