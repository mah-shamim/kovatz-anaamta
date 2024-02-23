<?php
namespace Jet_Engine\Modules\Maps_Listings\Source;

class Users extends Base {

	/**
	 * Returns source ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'users';
	}

	public function get_obj_by_id( $id ) {
		return get_user_by( 'ID', $id );
	}

	public function get_field_value( $obj, $field ) {
		return get_user_meta( $obj->ID, $field, true );
	}

	public function delete_field_value( $obj, $field ) {
		delete_user_meta( $obj->ID, $field );
	}

	public function update_field_value( $obj, $field, $value ) {
		
		$hash = md5( $field );

		update_user_meta( $obj->ID, $hash . '_hash', $value['key'] );
		update_user_meta( $obj->ID, $hash . '_lat', $value['coord']['lat'] );
		update_user_meta( $obj->ID, $hash . '_lng', $value['coord']['lng'] );

	}

	public function get_failure_key( $obj ) {
		return 'User #' . $obj->ID;
	}

	public function add_preload_hooks( $preload_fields ) {

		foreach ( $preload_fields as $field ) {
			$fields = explode( '+', $field );

			if ( 1 === count( $fields ) ) {
				$field = str_replace( 'user::', '', $field );
				add_action( 'jet-engine/user-meta/before-save/' . $field, array( $this, 'preload', ), 10, 3 );
			} else {
				$this->field_groups[] = array_map( function ( $item ) {
					return str_replace( 'user::', '', $item );
				}, $fields );
			}
		}

		if ( ! empty( $this->field_groups ) ) {
			add_action( 'jet-engine/user-meta/after-save', array( $this, 'preload_groups' ) );
		}
	}

	public function filtered_preload_fields( $field ) {
		return false !== strpos( $field, 'user::' );
	}

}
