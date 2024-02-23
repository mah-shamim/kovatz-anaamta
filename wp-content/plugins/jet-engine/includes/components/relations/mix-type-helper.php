<?php
namespace Jet_Engine\Relations;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Mix_Types_Helper {

	public function __construct() {

		add_filter( 'jet-engine/relations/types/mix', array( $this, 'add_object_data' ) );
		add_filter( 'jet-engine/relations/types/mix/items/' . $this->get_object_name(), array( $this, 'get_object_items' ) );
		add_filter( 'jet-engine/relations/types/mix/check-cap/' . $this->get_object_name(), array( $this, 'check_capability' ), 10, 3 );
		add_filter( 'jet-engine/relations/types/mix/item-title/' . $this->get_object_name(), array( $this, 'get_item_title' ), 10, 2 );
		add_filter( 'jet-engine/relations/types/mix/item-edit-url/' . $this->get_object_name(), array( $this, 'get_item_edit_url' ), 10, 2 );
		add_filter( 'jet-engine/relations/types/mix/item-view-url/' . $this->get_object_name(), array( $this, 'get_item_view_url' ), 10, 2 );
		add_filter( 'jet-engine/relations/types/mix/delete-item/' . $this->get_object_name(), array( $this, 'delete_item' ), 10, 2 );
		add_filter( 'jet-engine/relations/types/mix/create-fields/' . $this->get_object_name(), array( $this, 'get_create_control_fields' ) );
		add_filter( 'jet-engine/relations/types/mix/create-item/' . $this->get_object_name(), array( $this, 'create_item' ), 10, 2 );
		add_filter( 'jet-engine/relations/types/mix/get-object-by-id/' . $this->get_object_name(), array( $this, 'get_object_by_id' ), 10, 2 );
		add_filter( 'jet-engine/relations/types/mix/is-object-of-type/' . $this->get_object_name(), array( $this, 'is_object_of_type' ), 10, 2 );
		add_filter( 'jet-engine/relations/types/mix/filtered-query-args' . $this->get_object_name(), array( $this, 'filtered_query_args' ), 10, 2 );
		add_filter( 'jet-engine/relations/types/mix/cleanup-hook/' . $this->get_object_name(), array( $this, 'cleanup_hook' ), 10, 2 );

	}

	/**
	 * Returns object type name
	 *
	 * @return string
	 */
	abstract public function get_object_name();

	/**
	 * Returns object type label
	 *
	 * @return string
	 */
	abstract public function get_object_label();

	/**
	 * Returns object type label singular
	 *
	 * @return string
	 */
	abstract public function get_object_label_single();

	/**
	 * Returns object items list
	 *
	 * @return string
	 */
	abstract public function get_object_items();

	/**
	 * Check given user capability for custom type
	 *
	 * @param  $cap - can be 'edit' or 'delete'. You nned to check current user agains approprited capabilities related for your post type.
	 *                Or maybe agains global capability - for example current_user_can( 'manage_options' ) means only admin-level users will be able
	 *                to edit related items of this type.
	 *
	 * @return string
	 */
	abstract public function check_capability( $res, $cap, $item_id );

	/**
	 * Returns current item ID
	 *
	 * @param  [type] $default [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_item_title( $default, $item_id ) {
		return $default;
	}

	/**
	 * Returns current item edit URL
	 *
	 * @return [type] [description]
	 */
	public function get_item_edit_url( $default, $item_id ) {
		return $default;
	}

	/**
	 * Returns view URL for current item
	 *
	 * @param  [type] $default [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_item_view_url( $default, $item_id ) {
		return $default;
	}

	/**
	 * Delete current item
	 *
	 * @param  [type] $default [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function delete_item( $default, $item_id ) {
		return $default;
	}

	/**
	 * Returns controls list required to create relation item
	 *
	 * @param  [type] $default [description]
	 * @return [type]          [description]
	 */
	public function get_create_control_fields( $default ) {
		return $default;
	}

	/**
	 * Create new item and returns created item ID
	 *
	 * @return [type] [description]
	 */
	public function create_item( $result, $data = array() ) {
		return $result;
	}

	/**
	 * Check if given object belongs current type
	 *
	 * @return boolean [description]
	 */
	public function is_object_of_type( $result, $object ) {
		return false;
	}

	/**
	 * Returns object of current type by item ID of this object
	 *
	 * @return [type] [description]
	 */
	public function get_object_by_id( $result, $item_id ) {
		return false;
	}

	/**
	 * Return JetSmartFilters-prepared query arguments array of given ids for given object type
	 *
	 * @return array()
	 */
	public function filtered_query_args( $result, $ids ) {
		return array();
	}

	/**
	 * Add object data
	 *
	 * @param [type] $objects [description]
	 */
	public function add_object_data( $objects ) {

		$objects[ $this->get_object_name() ] = array(
			'value'        => $this->get_object_name(),
			'label'        => $this->get_object_label(),
			'label_single' => $this->get_object_label_single(),
		);

		return $objects;
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
	public function cleanup_hook( $callback, $type_name ) {
		// Run hook there
	}

}
