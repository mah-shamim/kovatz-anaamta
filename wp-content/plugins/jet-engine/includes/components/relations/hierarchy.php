<?php
namespace Jet_Engine\Relations;

/**
 * JetEngine Relations hierarchy manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Hierarchy {

	public function __construct() {
		add_action( 'jet-engine/register-macros', array( $this, 'register_macros' ) );
	}

	/**
	 * Register relations related macros
	 *
	 * @return [type] [description]
	 */
	public function register_macros() {

		require_once jet_engine()->relations->component_path( 'macros/get-related-grandparents.php' );
		require_once jet_engine()->relations->component_path( 'macros/get-related-grandchildren.php' );

		new Macros\Get_Related_Grandparents();
		new Macros\Get_Related_Grandchildren();

	}

	/**
	 * Returns related grandchildren for given object_id, object_id should be cgrandparent object ID,
	 * $rel_id should be ID of grandchildren relation
	 *
	 * @param  [type] $relation_id [description]
	 * @param  [type] $object_id   [description]
	 * @return [type]              [description]
	 */
	public function get_grandchildren( $relation_id, $object_id ) {

		$object_id   = absint( $object_id );
		$relation_id = absint( $relation_id );
		$relation    = jet_engine()->relations->get_active_relations( $relation_id );

		if ( ! $relation ) {
			return;
		}

		$parent_rel_id = $relation->get_args( 'parent_rel' );

		if ( ! $parent_rel_id ) {
			return;
		}

		$parent_relation = jet_engine()->relations->get_active_relations( $parent_rel_id );
		$child_relation  = jet_engine()->relations->get_active_relations( $relation_id );

		if ( ! $parent_relation ) {
			return;
		}

		$current_table = $relation->db->table();
		$parent_table  = $parent_relation->db->table();
		$current_slug  = 'rel_' . $relation_id;
		$parent_slug   = 'rel_' . $parent_rel_id;

		// SQL changed in v3.0.4 to fix https://github.com/Crocoblock/issues-tracker/issues/809
		$result = $child_relation->db->raw_query( "SELECT {$current_slug}.child_object_id AS id FROM {$current_table} AS {$current_slug}
		INNER JOIN {$parent_table} AS {$parent_slug} ON {$parent_slug}.child_object_id = {$current_slug}.parent_object_id
		WHERE {$parent_slug}.parent_object_id = {$object_id} AND {$parent_slug}.rel_id = {$parent_rel_id} AND {$current_slug}.parent_rel = {$parent_rel_id} AND {$current_slug}.rel_id = {$relation_id}" );

		if ( empty( $result ) ) {
			return array( PHP_INT_MAX );
		} else {
			return array_values( array_map( function( $item ) {
				return $item->id;
			}, $result ) );
		}

	}

	/**
	 * Returns related grandparent for given object_id, object_id should be children object from
	 * @param  [type] $relation_id [description]
	 * @param  [type] $object_id   [description]
	 * @return [type]              [description]
	 */
	public function get_grandparents( $relation_id, $object_id ) {

		$object_id   = absint( $object_id );
		$relation_id = absint( $relation_id );
		$relation    = jet_engine()->relations->get_active_relations( $relation_id );

		if ( ! $relation ) {
			return;
		}

		$parent_rel_id = $relation->get_args( 'parent_rel' );

		if ( ! $parent_rel_id ) {
			return;
		}

		$parent_relation = jet_engine()->relations->get_active_relations( $parent_rel_id );

		if ( ! $parent_relation ) {
			return;
		}

		$current_table = $relation->db->table();
		$parent_table  = $parent_relation->db->table();
		$current_slug  = 'rel_' . $relation_id;
		$parent_slug   = 'rel_' . $parent_rel_id;

		// SQL changed in v3.0.4 to fix https://github.com/Crocoblock/issues-tracker/issues/809
		$result = $parent_relation->db->raw_query( "SELECT {$parent_slug}.parent_object_id AS id FROM {$parent_table} AS {$parent_slug}
		INNER JOIN {$current_table} AS {$current_slug} ON {$current_slug}.parent_object_id = {$parent_slug}.child_object_id
		WHERE {$current_slug}.child_object_id = {$object_id} AND {$current_slug}.rel_id = {$relation_id} AND {$parent_slug}.rel_id = {$parent_rel_id}" );

		if ( empty( $result ) ) {
			return array( 'not-found' );
		} else {
			return array_values( array_map( function( $item ) {
				return $item->id;
			}, $result ) );
		}

	}

}
