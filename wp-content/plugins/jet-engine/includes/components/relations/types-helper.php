<?php
namespace Jet_Engine\Relations;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Types_Helper {

	public $types = null;
	public $types_instances = array();

	/**
	 * Returns delimiter for type name parts
	 *
	 * @return [type] [description]
	 */
	public function type_delimiter() {
		return '::';
	}

	/**
	 * Get full type name by type parts
	 *
	 * @param  [type] $type   [description]
	 * @param  [type] $object [description]
	 * @return [type]         [description]
	 */
	public function type_name_by_parts( $type, $object ) {
		return $type . $this->type_delimiter() . $object;
	}

	/**
	 * Check if given object are belongs to the given type
	 *
	 * @param  [type]  $object [description]
	 * @param  [type]  $type   [description]
	 * @return boolean         [description]
	 */
	public function is_of_type( $object = null, $type = null ) {
		return ;
	}

	/**
	 * Get full type name by type parts
	 *
	 * @param  [type] $type   [description]
	 * @param  [type] $object [description]
	 * @return [type]         [description]
	 */
	public function type_parts_by_name( $type_name ) {
		return explode( $this->type_delimiter(), $type_name );
	}

	/**
	 * Check if given object string is object of given type
	 *
	 * @param  [type] $object      [description]
	 * @param  [type] $type        [description]
	 * @param  [type] $object_name [description]
	 * @return [type]              [description]
	 */
	public function object_is( $object, $type, $object_name = null ) {

		if ( $object_name ) {
			return $object === $this->type_name_by_parts( $type, $object_name );
		} else {
			return false !== strpos( $object, $type . $this->type_delimiter() );
		}

	}

	/**
	 * Returns istems list for given type and object (subtype)
	 * @param  [type] $type   [description]
	 * @param  [type] $object [description]
	 * @return [type]         [description]
	 */
	public function get_type_items( $type, $object = null, $relation = false, $existing = array() ) {

		$type_instance = $this->get_instances( $type );

		if ( ! $type_instance ) {
			return array();
		}

		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		return array_values( array_filter( $type_instance->get_items( $object, $relation ), function( $item ) use ( $existing ) {
			return ! in_array( $item['value'], $existing );
		} ) );

	}

	/**
	 * Returns types instances list
	 *
	 * @param  [type] $slug [description]
	 * @return [type]       [description]
	 */
	public function get_instances( $slug = null ) {

		if ( empty( $this->types_instances ) ) {

			require_once jet_engine()->relations->component_path( 'types/base.php' );
			require_once jet_engine()->relations->component_path( 'types/posts.php' );
			require_once jet_engine()->relations->component_path( 'types/terms.php' );
			require_once jet_engine()->relations->component_path( 'types/mix.php' );

			$posts = new Types\Posts();
			$terms = new Types\Terms();
			$mix   = new Types\Mix();

			$this->types_instances = apply_filters( 'jet-engine/relations/types', array(
				$posts->get_name() => $posts,
				$terms->get_name() => $terms,
				$mix->get_name()   => $mix,
			) );
		}

		if ( ! empty( $slug ) ) {
			return isset( $this->types_instances[ $slug ] ) ? $this->types_instances[ $slug ] : false;
		}

		return $this->types_instances;

	}

	/**
	 * Get registered types list
	 *
	 * @return [type] [description]
	 */
	public function get_types() {

		if ( null === $this->types) {

			foreach ( $this->get_instances() as $type ) {
				$this->types[ $type->get_name() ] = $type->get_object_names();
			}
		}

		return $this->types;

	}

	/**
	 * returns types list for JS
	 *
	 * @return [type] [description]
	 */
	public function get_types_for_js() {

		$result = array();

		foreach ( $this->get_instances() as $type ) {
			$result[] = array(
				'label'   => $type->get_label(),
				'name'    => $type->get_name(),
				'options' => array_values( array_map( function( $item ) use ( $type ) {
					$item['value'] = $this->type_name_by_parts( $type->get_name(), $item['value'] );
					unset( $item['label_single'] );
					return $item;
				}, $type->get_object_names() ) )
			);
		}

		return $result;

	}

	/**
	 * Returns label or relation type
	 *
	 * @param  string $context     [description]
	 * @param  [type] $object_type [description]
	 * @param  [type] $object      [description]
	 * @return [type]              [description]
	 */
	public function get_type_label( $context = 'plural', $object_type = null, $object = null ) {

		$defaults = array(
			'single' => __( 'Item', 'jet-engine' ),
			'plural' => __( 'Items', 'jet-engine' ),
		);

		if ( ! isset( $defaults[ $context ] ) ) {
			$context = 'plural';
		}

		$types = $this->get_types();

		if ( ! isset( $types[ $object_type ] ) || ! isset( $types[ $object_type ][ $object ] ) ) {
			return $defaults[ $context ];
		}

		if ( 'single' === $context ) {
			return isset( $types[ $object_type ][ $object ]['label_single'] ) ? $types[ $object_type ][ $object ]['label_single'] : $defaults['single'];
		} else {
			return $types[ $object_type ][ $object ]['label'];
		}

	}

	/**
	 * Returns label for control of given object in the given relation
	 *
	 * @param  array  $relation [description]
	 * @param  string $object   [description]
	 * @return [type]           [description]
	 */
	public function get_relation_label( $relation, $object_type = '', $object_name = '', $prefix = '', $is_parent_processed = null ) {

		if ( null === $is_parent_processed ) {
			$is_parent_processed = ( $relation->get_args( 'parent_object' ) !== $this->type_name_by_parts( $object_type, $object_name ) );
		}

		$from_object = ( $is_parent_processed ) ? $relation->get_args( 'parent_object' ) : $relation->get_args( 'child_object' );
		$types       = $this->get_types();
		$object_data = $this->type_parts_by_name( $from_object );
		$type        = isset( $types[ $object_data[0] ] ) ? $types[ $object_data[0] ] : array();
		$type_data   = isset( $type[ $object_data[1] ] ) ? $type[ $object_data[1] ] : array();
		$prefix      = ( $is_parent_processed ) ? __( 'Parent ', 'jet-engine' ) : __( 'Children', 'jet-engine' );

		return isset( $type_data['label'] ) ? $prefix . ' ' . $type_data['label'] : '';

	}

	/**
	 * Returns verbosed relation objects string
	 *
	 * @param  [type] $parent_object [description]
	 * @param  [type] $child_object  [description]
	 * @return [type]                [description]
	 */
	public function relation_verbose( $parent_object, $child_object, $delimiter = '->' ) {

		$parent_data = $this->type_parts_by_name( $parent_object );
		$child_data  = $this->type_parts_by_name( $child_object );
		$types       = $this->get_types();

		$parent_type      = isset( $types[ $parent_data[0] ] ) ? $types[ $parent_data[0] ] : array();
		$parent_type_data = isset( $parent_type[ $parent_data[1] ] ) ? $parent_type[ $parent_data[1] ] : array();
		$parent_label     = isset( $parent_type_data['label'] ) ? $parent_type_data['label'] : $parent_data[1];
		$parent_instance  = $this->get_instances( $parent_data[0] );
		$child_type       = isset( $types[ $child_data[0] ] ) ? $types[ $child_data[0] ] : array();
		$child_type_data  = isset( $child_type[ $child_data[1] ] ) ? $child_type[ $child_data[1] ] : array();
		$child_label      = isset( $child_type_data['label'] ) ? $child_type_data['label'] : $child_data[1];
		$child_instance   = $this->get_instances( $child_data[0] );

		return sprintf( '%1$s: %2$s %5$s %3$s: %4$s', $parent_instance->get_label(), $parent_label, $child_instance->get_label(), $child_label, $delimiter );

	}

	/**
	 * Returns item title by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_type_item_title( $type, $item_id, $relation ) {

		$type_data     = $this->type_parts_by_name( $type );
		$type_instance = $this->get_instances( $type_data[0] );

		if ( ! $type_instance ) {
			return '#' . $item_id;
		}

		return $type_instance->get_type_item_title( $item_id, $type_data[1], $relation );

	}

	/**
	 * Returns item edit URL by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_type_item_edit_url( $type, $item_id, $relation ) {

		$type_data     = $this->type_parts_by_name( $type );
		$type_instance = $this->get_instances( $type_data[0] );

		if ( ! $type_instance ) {
			return false;
		}

		return $type_instance->get_type_item_edit_url( $item_id, $type_data[1], $relation );

	}

	/**
	 * Check given capability fro current user + object type + item ID combination
	 *
	 * @param  [type] $cap     [description]
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function current_user_can( $cap, $type, $item_id, $object_name = null ) {

		$type_data     = $this->type_parts_by_name( $type );
		$type_instance = $this->get_instances( $type_data[0] );

		if ( ! $object_name ) {
			$object_name = $type_data[1];
		}

		if ( ! $type_instance ) {
			return false;
		}

		return $type_instance->current_user_can( $cap, $item_id, $object_name );

	}

	/**
	 * Returns fields list required to create item of given type
	 *
	 * @param  [type] $type [description]
	 * @return [type]       [description]
	 */
	public function get_create_control_fields( $type, $relation ) {

		$type_data     = $this->type_parts_by_name( $type );
		$type_instance = $this->get_instances( $type_data[0] );

		if ( ! $type_instance ) {
			return false;
		}

		return $type_instance->get_create_control_fields( $type_data[1], $relation );
	}

	/**
	 * Delete given item of given type
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function delete_item( $type, $item_id ) {

		$type_data     = $this->type_parts_by_name( $type );
		$type_instance = $this->get_instances( $type_data[0] );

		if ( ! $type_instance ) {
			return false;
		}

		// apropriate capability must be checked inside each Type callback
		return $type_instance->delete_item( $item_id, $type_data[1] );
	}

	/**
	 * Returns item view URL by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_type_item_view_url( $type, $item_id, $relation ) {

		$type_data     = $this->type_parts_by_name( $type );
		$type_instance = $this->get_instances( $type_data[0] );

		if ( ! $type_instance ) {
			return false;
		}

		return $type_instance->get_type_item_view_url( $item_id, $type_data[1], $relation );

	}

	/**
	 * Create new item of given typer by given data
	 *
	 * @return [type] [description]
	 */
	public function create_item( $type, $data = array() ) {

		$type_data     = $this->type_parts_by_name( $type );
		$type_instance = $this->get_instances( $type_data[0] );

		if ( ! $type_instance ) {
			return false;
		}

		return $type_instance->create_item( $data, $type_data[1] );

	}

	/**
	 * Find exact type of given object
	 *
	 * @param  [type] $object [description]
	 * @return [type]         [description]
	 */
	public function get_type_for_object( $object ) {

		foreach ( $this->get_types() as $type => $type_data ) {
			$type_instance = $this->get_instances( $type );
			foreach ( $type_data as $object_name => $data ) {
				if ( $type_instance->is_object_of_type( $object, $object_name ) ) {
					return $this->type_name_by_parts( $type, $object_name );
				}
			}
		}

		return false;
	}

	/**
	 * Sanitize type-specific arguments of relation on edit
	 *
	 * @param  array  $final_args   [description]
	 * @param  array  $request_data [description]
	 * @return [type]               [description]
	 */
	public function sanitize_relation_edit_args( $final_args = array(), $request_data = array() ) {

		foreach ( $this->get_types() as $type => $type_data ) {
			$type_instance = $this->get_instances( $type );
			$args = $type_instance->sanitize_relation_edit_args( $final_args, $request_data );
		}

		return $args;

	}

	/**
	 * Return JetSmartFilters-prepared query arguments array of given ids for given object type
	 *
	 * @return array()
	 */
	public function filtered_query_args( $type, $ids = array() ) {

		$type_data     = $this->type_parts_by_name( $type );
		$type_instance = $this->get_instances( $type_data[0] );

		if ( ! $type_instance ) {
			return array();
		} else {
			return $type_instance->filtered_query_args( $ids, $type_data[1] );
		}

	}

	/**
	 * register callback to clean relations data on removing items of selected type
	 *
	 * @param  [type] $type [description]
	 * @return [type]       [description]
	 */
	public function register_cleanup_hook( $type, $callback ) {

		$type_data     = $this->type_parts_by_name( $type );
		$type_instance = $this->get_instances( $type_data[0] );

		if ( ! $type_instance ) {
			return;
		}

		$type_instance->register_cleanup_hook( $type_data[1], $callback, $type );

	}

	/**
	 * Returns list of related object names by IDs and object type
	 *
	 * @param  [type] $from_object [description]
	 * @param  array  $related_ids [description]
	 * @return [type]              [description]
	 */
	public function verbose_related_objects( $from_object = null, $related_ids = array(), $relation = null ) {

		if ( empty( $related_ids ) ) {
			return $related_ids;
		}

		$result = array();

		foreach ( $related_ids as $id ) {

			$edit_url = $this->get_type_item_edit_url( $from_object, $id, $relation );

			if ( $this->current_user_can( 'edit', $from_object, $id ) && $edit_url ) {
				$result[] = sprintf( '<a href="%2$s" target="_blank">%1$s</a>', $this->get_type_item_title( $from_object, $id, $relation ), $edit_url );
			} else {
				$result[] = $this->get_type_item_title( $from_object, $id, $relation );
			}
		}

		return implode( ', ', $result );

	}

}
