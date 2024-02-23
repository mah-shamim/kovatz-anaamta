<?php
namespace Jet_Engine\Modules\Maps_Listings\Source;

use Jet_Engine\Modules\Maps_Listings\Module;

abstract class Base {

	public $lat_lng      = null;
	public $field_groups = array();

	public function __construct() {
		$this->lat_lng = Module::instance()->lat_lng;
	}

	/**
	 * Returns source ID
	 *
	 * @return string
	 */
	abstract public function get_id();

	abstract public function get_obj_by_id( $id );

	abstract public function get_field_value( $obj, $field );

	abstract public function update_field_value( $obj, $field, $value );

	abstract public function get_failure_key( $obj );

	/**
	 * Delete field value. Required to delete legacy values if exists.
	 * Could be removed in the future
	 * @param  [type]
	 * @param  [type]
	 * @return [type]
	 */
	public function delete_field_value( $obj, $field ) {
	}

	/**
	 * Returns coordinates data based on multiple fields (support preloaded values and map control)
	 * 
	 * @param  [type]
	 * @param  string
	 * @param  [type]
	 * @return [type]
	 */
	public function get_field_coordinates( $obj, $location_string = '', $field_name = null ) {

		if ( ! $field_name ) {
			$field_name = $this->lat_lng->meta_key;
		}

		$field_hash    = md5( $field_name );
		$location_hash = $this->get_field_value( $obj, $field_hash . '_hash' );

		// Try to get legacy preloaded data and update it
		if ( ! $location_hash ) {
			
			$legacy_data = $this->get_field_value( $obj, $this->lat_lng->meta_key );

			if ( ! empty( $legacy_data ) ) {
				$location_hash = $legacy_data['key'];
				$this->update_field_value( $obj, $field_name, $legacy_data );
				$this->delete_field_value( $obj, $this->lat_lng->meta_key );
			}
			
		}

		if ( ! $location_hash ) {
			return;
		}

		return array(
			'key'   => $location_hash,
			'coord' => array(
				'lat' => $this->get_field_value( $obj, $field_hash . '_lat' ),
				'lng' => $this->get_field_value( $obj, $field_hash . '_lng' ),
			),
		);
		
	}

	public function preload_hooks( $preload_fields ) {
		$fields = array_filter( $preload_fields, array( $this, 'filtered_preload_fields' ) );

		if ( empty( $fields ) ) {
			return;
		}

		$this->add_preload_hooks( $fields );
	}

	public function filtered_preload_fields( $field ) {
		return true;
	}

	public function add_preload_hooks( $preload_fields ) {}

	/**
	 * Preload field address
	 *
	 * @param  int    $obj_id
	 * @param  string $address
	 * @return void
	 */
	public function preload( $obj_id, $address, $field = '' ) {
		$this->lat_lng->set_current_source( $this->get_id() );
		$this->lat_lng->preload( $obj_id, $address, $field );
	}

	/**
	 * Preload fields groups
	 *
	 * @param  int  $obj_id
	 * @return void
	 */
	public function preload_groups( $obj_id ) {
		$this->lat_lng->set_current_source( $this->get_id() );
		$this->lat_lng->preload_groups( $obj_id );
	}

	public function get_field_groups() {
		return $this->field_groups;
	}

}
