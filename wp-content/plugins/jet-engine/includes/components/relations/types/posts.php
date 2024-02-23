<?php
namespace Jet_Engine\Relations\Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Posts extends Base {

	/**
	 * Returns type name
	 * @return [type] [description]
	 */
	public function get_name() {
		return 'posts';
	}

	/**
	 * Returns type label
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'Posts', 'jet-engine' );
	}

	/**
	 * Returns subtypes list
	 * @return [type] [description]
	 */
	public function get_object_names() {

		$post_types = get_post_types( array(), 'objects' );
		$result     = array();

		foreach ( $post_types as $post_type ) {
			$result[ $post_type->name ] = array(
				'value'        => $post_type->name,
				'label'        => $post_type->label,
				'label_single' => $post_type->labels->singular_name,
			);
		}

		return $result;
	}

	/**
	 * Checkk type specific user capabilities
	 *
	 * @return [type] [description]
	 */
	public function current_user_can( $cap, $item_id, $object_name ) {

		switch ( $cap ) {
			case 'edit':
			case 'delete':
				return current_user_can( $cap . '_post', $item_id );

			default:
				return true;
		}

	}

	/**
	 * Returns type items
	 * @return [type] [description]
	 */
	public function get_items( $object_name, $relation ) {

		global $wpdb;
		$table = $wpdb->posts;

		$res = $wpdb->get_results(
			"SELECT ID AS value, post_title AS label FROM $table WHERE post_type='{$object_name}' AND post_status = 'publish' ORDER BY ID DESC",
			ARRAY_A
		);

		if ( empty( $res ) ) {
			return array();
		} else {

			$items = array_map( function( $item ) {

				if ( empty( $item['label'] ) ) {
					$item['label'] = '#' . $item['value'];
				}

				return $item;

			}, array_values( $res ) );

			return apply_filters( 'jet-engine/relations/types/posts/get-items', $items, $object_name );
		}
	}

	/**
	 * Returns type items
	 * @return [type] [description]
	 */
	public function get_type_item_title( $item_id, $object_name, $relation ) {
		return get_the_title( $item_id );
	}

	/**
	 * Returns item edit URL by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_type_item_edit_url( $item_id, $object_name, $relation ) {
		return get_edit_post_link( $item_id, 'url' );
	}

	/**
	 * Returns item view URL by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_type_item_view_url( $item_id, $object_name, $relation ) {
		return get_permalink( $item_id );
	}

	/**
	 * Trash given post
	 *
	 * @return [type] [description]
	 */
	public function delete_item( $item_id, $object_name ) {

		if ( ! $this->current_user_can( 'delete', $item_id, $object_name ) ) {
			return false;
		}

		return wp_trash_post( $item_id );

	}

	/**
	 * Returns fields list required to create item of given type
	 *
	 * @param  [type] $object_name [description]
	 * @return [type]       [description]
	 */
	public function get_create_control_fields( $object_name, $relation ) {

		return apply_filters( 'jet-engine/relations/types/posts/create-fields', array(
			array(
				'name'  => 'post_title',
				'title' => __( 'Title', 'jet-engine' ),
				'type'  => 'text',
			),
		), $object_name, $relation );

	}

	/**
	 * Create new item of given typer by given data
	 *
	 * @return [type] [description]
	 */
	public function create_item( $data, $object_name ) {

		if ( ! post_type_exists( $object_name ) ) {
			return false;
		}

		$allowed_data = array( 'post_title', 'post_excerpt', 'post_content' );
		$postarr      = array(
			'post_type'   => $object_name,
			'post_status' => 'publish',
		);

		foreach ( $allowed_data as $key ) {
			if ( isset( $data[ $key ] ) ) {
				$postarr[ $key ] = $data[ $key ];
			}
		}

		$post_id = wp_insert_post( $postarr );

		/**
		 * On create new related post
		 */
		do_action( 'jet-engine/relations/types/posts/on-create', $post_id, $data, $object_name, $postarr );

		if ( is_wp_error( $post_id ) ) {
			return false;
		}

		return $post_id;

	}

	/**
	 * Returns object of current type by item ID of this object
	 *
	 * @return [type] [description]
	 */
	public function get_object_by_id( $item_id, $object_name ) {
		return get_post( $item_id );
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

		if ( ! $class || 'WP_Post' !== $class ) {
			return false;
		}

		return ( $object_name === $object->post_type ) ? true : false;

	}

	/**
	 * Return JetSmartFilters-prepared query arguments array of given ids for given object type
	 *
	 * @return array()
	 */
	public function filtered_query_args( $ids = array(), $object_name = '' ) {
		return array( 'post__in' => $ids );
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

		add_action( 'delete_post', function( $pid, $post ) use ( $object_name, $callback, $type_name ) {

			if ( $post->post_type && $post->post_type === $object_name ) {
				call_user_func( $callback, $type_name, $pid );
			}

		}, 10, 2 );
	}

}
