<?php
namespace Jet_Engine\Relations\Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Mix extends Base {

	/**
	 * Returns type name
	 * @return [type] [description]
	 */
	public function get_name() {
		return 'mix';
	}

	/**
	 * Returns type label
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'Mix', 'jet-engine' );
	}

	/**
	 * Returns subtypes list
	 * @return [type] [description]
	 */
	public function get_object_names() {

		return apply_filters( 'jet-engine/relations/types/mix', array(
			'users' => array(
				'value'        => 'users',
				'label'        => __( 'Users', 'jet-engine' ),
				'label_single' => __( 'User', 'jet-engine' ),
			),
		) );

	}

	/**
	 * Checkk type specific user capabilities
	 *
	 * @return [type] [description]
	 */
	public function current_user_can( $cap, $item_id, $object_name ) {

		if ( 'users' === $object_name ) {
			switch ( $cap ) {
				case 'edit':
					return current_user_can( 'edit_users' );

				case 'delete':
					return false;

				default:
					return true;
			}
		} else {
			return apply_filters( 'jet-engine/relations/types/mix/check-cap/' . $object_name, false, $cap, $item_id );
		}

	}

	/**
	 * Returns type items
	 * @return [type] [description]
	 */
	public function get_items( $object_name, $relation ) {

		switch ( $object_name ) {

			case 'users':

				global $wpdb;

				$table = $wpdb->users;
				$res   = $wpdb->get_results( "SELECT ID AS value, CONCAT( user_login, ' (', user_email, ')' ) AS label FROM $table", ARRAY_A );

				return ! empty( $res ) ? $res : array();

			default:
				return apply_filters( 'jet-engine/relations/types/mix/items/' . $object_name, array(), $relation );
		}

	}

	/**
	 * Returns type items
	 * @return [type] [description]
	 */
	public function get_type_item_title( $item_id, $object_name, $relation ) {

		$result = '#' . $item_id;

		switch ( $object_name ) {

			case 'users':

				$user = get_user_by( 'ID', $item_id );

				if ( $user ) {
					$result = $user->user_login . ' (' . $user->user_email . ')';
				}

				return $result;

			default:
				return apply_filters( 'jet-engine/relations/types/mix/item-title/' . $object_name, $result, $item_id, $relation );
		}

	}

	/**
	 * Returns item edit URL by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_type_item_edit_url( $item_id, $object_name, $relation ) {

		switch ( $object_name ) {

			case 'users':
				return get_edit_user_link( $item_id );

			default:
				return apply_filters( 'jet-engine/relations/types/mix/item-edit-url/' . $object_name, false, $item_id, $relation );
		}

	}

	/**
	 * Returns item view URL by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_type_item_view_url( $item_id, $object_name, $relation ) {

		switch ( $object_name ) {
			case 'users':
				return get_author_posts_url( $item_id );
			default:
				return apply_filters( 'jet-engine/relations/types/mix/item-view-url/' . $object_name, false, $item_id, $relation );
		}
	}

	/**
	 * Trash given post
	 *
	 * @return [type] [description]
	 */
	public function delete_item( $item_id, $object_name ) {

		switch ( $object_name ) {
			case 'users':
				return false;
			default:
				return apply_filters( 'jet-engine/relations/types/mix/delete-item/' . $object_name, false, $item_id );
		}

	}

	/**
	 * Returns fields list required to create item of given type
	 *
	 * @param  [type] $object_name [description]
	 * @return [type]       [description]
	 */
	public function get_create_control_fields( $object_name, $relation ) {

		switch ( $object_name ) {

			case 'users':
				return apply_filters( 'jet-engine/relations/types/mix/create-fields/users', array(
					array(
						'name'  => 'user_login',
						'title' => __( 'Login', 'jet-engine' ),
						'type'  => 'text',
					),
					array(
						'name'  => 'user_email',
						'title' => __( 'Email', 'jet-engine' ),
						'type'  => 'text',
					),
					array(
						'name'  => 'user_pass',
						'title' => __( 'Password', 'jet-engine' ),
						'type'  => 'text',
					),
					array(
						'name'    => 'role',
						'title'   => __( 'Role', 'jet-engine' ),
						'type'    => 'select',
						'options' => $this->get_user_roles(),
					),
				), $object_name, $relation );

			default:
				return apply_filters( 'jet-engine/relations/types/mix/create-fields/' . $object_name, array(), $object_name, $relation );
		}

	}

	/**
	 * Returns available user roles list to use as options
	 *
	 * @return array
	 */
	public function get_user_roles() {

		$roles = wp_roles();

		// todo - replace with default user role from options
		$result = array( array(
			'value' => 'subscriber',
			'label' => __( 'Subscriber', 'jet-engine' ),
		) );

		foreach ( $roles->roles as $slug => $data ) {
			if ( 'subscriber' !== $slug ) {
				$result[] = array(
					'value' => $slug,
					'label' => $data['name'],
				);
			}
		}

		return $result;

	}

	/**
	 * Create new item of given typer by given data
	 *
	 * @return [type] [description]
	 */
	public function create_item( $data, $object_name ) {

		$result = false;

		switch ( $object_name ) {

			case 'users':

				// additional capability check just in case
				if ( ! current_user_can( 'create_users' ) ) {
					wp_cache_set( 'jet-engine-relations-error', __( 'You are not allowed to create new users', 'jet-engine' ) );
					return false;
				}

				$user_login = ! empty( $data['user_login'] ) ? sanitize_user( $data['user_login'] ) : false;
				$user_email = ! empty( $data['user_email'] ) ? $data['user_email'] : false;

				if ( ! $user_login ) {
					wp_cache_set( 'jet-engine-relations-error', __( 'Please set user login', 'jet-engine' ) );
					return false;
				}

				if ( ! $user_email || ! is_email( $user_email ) ) {
					wp_cache_set( 'jet-engine-relations-error', __( 'Please set valid user e-mail', 'jet-engine' ) );
					return false;
				}

				$user_pass = ! empty( $data['user_pass'] ) ? $data['user_pass'] : wp_generate_password();
				$user_role = ! empty( $data['user_role'] ) ? $data['user_role'] : get_option( 'default_role', 'subscriber' );

				$userdata = array(
					'user_login' => $user_login,
					'user_email' => $user_email,
					'user_pass'  => $user_pass,
					'role'       => $user_role,
				);

				$user_id = wp_insert_user( $userdata );

				if ( is_wp_error( $user_id ) ) {
					wp_cache_set( 'jet-engine-relations-error', $user_id->get_error_message() );
					return false;
				} else {
					$result = $user_id;
				}

				break;

			default:
				$result = apply_filters( 'jet-engine/relations/types/mix/create-item/' . $object_name, false, $data );
				break;
		}

		do_action( 'jet-engine/relations/types/mix/on-create/' . $object_name, $result, $data, $object_name );

		return $result;

	}

	/**
	 * Returns object of current type by item ID of this object
	 *
	 * @return [type] [description]
	 */
	public function get_object_by_id( $item_id, $object_name ) {
		switch ( $object_name ) {

			case 'users':
				return get_user_by( 'ID', $item_id );

			default:
				return apply_filters( 'jet-engine/relations/types/mix/get-object-by-id/' . $object_name, false, $item_id );

		}
	}

	/**
	 * Check if $object is belongs to current type
	 *
	 * @param  [type]  $object      [description]
	 * @param  [type]  $object_name [description]
	 * @return boolean              [description]
	 */
	public function is_object_of_type( $object, $object_name ) {

		if ( ! $object || ! is_object( $object ) ) {
			return false;
		}

		$class = get_class( $object );

		if ( ! $class ) {
			return false;
		}

		switch ( $object_name ) {

			case 'users':
				return ( 'WP_User' === $class ) ? true : false;

			default:
				return apply_filters( 'jet-engine/relations/types/mix/is-object-of-type/' . $object_name, false, $object );

		}

	}

	/**
	 * Return JetSmartFilters-prepared query arguments array of given ids for given object type
	 *
	 * @return array()
	 */
	public function filtered_query_args( $ids = array(), $object_name = '' ) {

		switch ( $object_name ) {

			case 'users':
				return array( 'include' => $ids );

			default:
				return apply_filters( 'jet-engine/relations/types/mix/filtered-query-args/' . $object_name, array(), $ids );

		}
	}

	/**
	 * Register appropriate cleanup hook for current type items.
	 * This hook should be called on deletion of item of current type and call clean up method from relation
	 * See the default types for examples.
	 *
	 * @param  string $object_name [description]
	 * @param  [type] $callback    [description]
	 * @return [type]              [description]
	 */
	public function register_cleanup_hook( $object_name = '', $callback = null, $type_name = '' ) {

		switch ( $object_name ) {

			case 'users':

				add_action( 'delete_user', function( $user_id ) use ( $callback, $type_name ) {
					call_user_func( $callback, $type_name, $user_id );
				}, 10, 3 );

				break;

			default:
				do_action( 'jet-engine/relations/types/mix/cleanup-hook/' . $object_name, $callback, $type_name );
				break;

		}

	}

}
