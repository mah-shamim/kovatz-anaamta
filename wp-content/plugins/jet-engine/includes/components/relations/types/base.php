<?php
namespace Jet_Engine\Relations\Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Base {

	/**
	 * Returns type name
	 * @return [type] [description]
	 */
	abstract public function get_name();

	/**
	 * Returns type label
	 * @return [type] [description]
	 */
	abstract public function get_label();

	/**
	 * Returns subtypes list
	 * @return [type] [description]
	 */
	abstract public function get_object_names();

	/**
	 * Returns type items
	 * @return [type] [description]
	 */
	abstract public function get_items( $object_name, $relation );

	/**
	 * Returns type items
	 * @return [type] [description]
	 */
	abstract public function get_type_item_title( $item_id, $object_name, $relation );

	/**
	 * Returns item edit URL by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	abstract public function get_type_item_edit_url( $item_id, $object_name, $relation );

	/**
	 * Returns item view URL by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	abstract public function get_type_item_view_url( $item_id, $object_name, $relation );

	/**
	 * Delete given item.
	 * By default not allowed, should be set for each type individually with appropriate capability check
	 *
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function delete_item( $item_id, $object_name ) {
		return false;
	}

	/**
	 * Checkk type specific user capabilities
	 *
	 * @return [type] [description]
	 */
	public function current_user_can( $cap, $item_id, $object_name ) {
		return true;
	}

	/**
	 * Returns fields list required to create item of given type
	 *
	 * @param  [type] $object_name [description]
	 * @return [type]       [description]
	 */
	public function get_create_control_fields( $object_name, $relation ) {
		return array();
	}

	/**
	 * Create new item of given typer by given data
	 *
	 * @return [type] [description]
	 */
	public function create_item( $data, $object_name ) {
		return false;
	}

	/**
	 * Check if $object is belongs to current type
	 *
	 * @param  [type]  $object      [description]
	 * @param  [type]  $object_name [description]
	 * @return boolean              [description]
	 */
	public function is_object_of_type( $object, $object_name ) {
		return false;
	}

	/**
	 * Returns object of current type by item ID of this object
	 *
	 * @return [type] [description]
	 */
	public function get_object_by_id( $item_id, $object_name ) {
		return false;
	}

	/**
	 * Sanitize type-specific arguments of relation on edit.
	 * Is placeholder method, by default returs input data without changes.
	 * Rewrite this method in the child class if you pass any additional controls into relation.
	 *
	 * @param  array  $final_args   [description]
	 * @param  array  $request_data [description]
	 * @return [type]               [description]
	 */
	public function sanitize_relation_edit_args( $final_args = array(), $request_data = array() ) {
		return $final_args;
	}

	/**
	 * Return JetSmartFilters-prepared query arguments array of given ids for given object type
	 *
	 * @return array()
	 */
	public function filtered_query_args( $ids = array(), $object_name = '' ) {
		return array();
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
		return false;
	}

}
