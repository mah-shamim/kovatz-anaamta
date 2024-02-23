<?php
namespace Jet_Engine\Modules\Maps_Listings\Source;

class Repeater extends Base {

	/**
	 * Returns source ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'repeater';
	}

	public function get_obj_by_id( $id ) {

		$id_data = explode( '-', $id );

		if ( 3 === count( $id_data ) ) {
			$query_id   = $id_data[0];
			$object_id  = $id_data[1];
			$item_index = $id_data[2];
		} else {
			$query_id   = $id_data[0];
			$object_id  = false;
			$item_index = $id_data[1];
		}

		if ( ! $query_id ) {
			return false;
		}

		$query = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $query_id );

		if ( ! $query ) {
			return false;
		}

		$query->setup_query();

		if ( $object_id ) {
			$query->final_query['object_id'] = $object_id;
		}

		$items = $query->get_items();

		return isset( $items[ $item_index ] ) ? $items[ $item_index ] : false;
	}

	public function get_field_value( $obj, $field ) {
		return isset( $obj->$field ) ? $obj->$field : false;
	}

	public function delete_field_value( $obj, $field ) {}

	public function update_field_value( $obj, $field, $value ) {}

	public function get_failure_key( $obj ) {
		return 'Repeater Item #' . $obj->get_id();
	}

	public function get_field_coordinates( $obj, $location_string = '', $field_name = null ) {

		if ( ! $field_name ) {
			$field_name = $this->lat_lng->meta_key;
		}

		$location_hash = $this->get_field_value( $obj, $field_name . '_hash' );

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
