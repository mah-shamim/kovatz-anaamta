<?php
namespace Jet_Engine\Modules\Maps_Listings\Source;

class Posts extends Base {

	/**
	 * Returns source ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'posts';
	}

	public function get_obj_by_id( $id ) {
		return get_post( $id );
	}

	public function get_field_value( $obj, $field ) {
		return get_post_meta( $obj->ID, $field, true );
	}

	public function delete_field_value( $obj, $field ) {
		delete_post_meta( $obj->ID, $field );
	}

	public function update_field_value( $obj, $field, $value ) {
		
		$hash = md5( $field );

		update_post_meta( $obj->ID, $hash . '_hash', $value['key'] );
		update_post_meta( $obj->ID, $hash . '_lat', $value['coord']['lat'] );
		update_post_meta( $obj->ID, $hash . '_lng', $value['coord']['lng'] );

	}

	public function get_failure_key( $obj ) {
		return 'Post #' . $obj->ID;
	}

	public function add_preload_hooks( $preload_fields ) {

		foreach ( $preload_fields as $field ) {

			$fields = explode( '+', $field );

			if ( 1 === count( $fields ) ) {
				add_action( 'cx_post_meta/before_save_meta/' . $field, array( $this, 'preload' ), 10, 3 );
			} else {
				$this->field_groups[] = $fields;
			}
		}

		if ( ! empty( $this->field_groups ) ) {
			add_action( 'cx_post_meta/after_save', array( $this, 'preload_groups' ) );
		}
	}

	public function filtered_preload_fields( $field ) {
		return false === strpos( $field, '::' );
	}

}
