<?php
namespace Jet_Engine\Relations;

/**
 * Relation object
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Arguments schema:
 *
 * 'parent_object'  => 'posts::page' - information about parent object in type::subtype format
 * 'child_object'   => 'posts::post' - information about child object in type::subtype format
 * 'parent_rel'     => null - ID of parent relation
 * 'type'           => 'one_to_one' - relation type, allowed values - 'one_to_one', 'one_to_many', 'many_to_many'
 * 'db_table'       => true - register or not separate DB table to store all related items
 * 'parent_control' => true - register or not control for related children on the parent object edit page
 * 'child_control'  => true - register or not control for related parents on the children objects edit page
 * 'parent_manager' => true - allow to create new objects of children type from parent object edit page
 * 'child_manager'  => true - allow to create new objects of parent type from children objects edit page
 * 'parent_allow_delete' => true - allow to delete objects of children type from parent object edit page
 * 'child_allow_delete'  => true - allow to delete objects of parent type from children objects edit page
 * 'parent_table' => array( 'image' => array( 'enable' => true, 'callback' => '', 'name' => 'Image' ) ) - additional table columns for parent object edit page control
 * 'child_table'  => array( 'image' => array( 'enable' => true, 'callback' => '', 'name' => 'Image' ) ) - additional table columns for child object edit page control
 * 'meta_fields'    => array() - list of meta fields for relation
 * 'id'             => $id - relation ID
 *
 */
class Relation {

	private $raw_args = array();
	private $rel_id = array();
	private $controls;
	private $rel_cache_group = 'jet_engine_rel';
	private $update_context  = null;
	private $control_context = null;

	public $db;
	public $meta_db;

	/**
	 * @param integer $rel_id relation ID
	 * @param array   $args   relation arguments
	 * @param boolean $silent is silent activation or not. if silent - only props will be filled, no hooks added
	 */
	public function __construct( $rel_id = 0, $args = array(), $silent = false ) {

		$this->raw_args = apply_filters( 'jet-engine/relations/raw-args', $args, $rel_id );
		$this->rel_id   = $rel_id;

		$this->setup_db();

		if ( $silent ) {
			return;
		}

		if ( is_admin() && $this->is_valid() ) {
			$this->setup_controls();
		}

		add_action( 'rest_api_init', array( $this, 'init_public_rest_api' ) );

		jet_engine()->relations->types_helper->register_cleanup_hook( $this->get_args( 'parent_object' ), array( $this, 'cleanup_relation' ) );
		jet_engine()->relations->types_helper->register_cleanup_hook( $this->get_args( 'child_object' ), array( $this, 'cleanup_relation' ) );

		// Context-related hooks
		add_filter( 'jet-engine/listings/allowed-context-list', array( $this, 'register_context' ) );
		add_filter( 'jet-engine/listings/data/object-by-context/' . $this->get_context_name(), array( $this, 'apply_context' ) );

		do_action( 'jet-engine/relations/init/' . $rel_id, $this );

	}

	public function init_public_rest_api() {
		

		$get  = $this->get_args( 'rest_get_enabled' );
		$edit = $this->get_args( 'rest_post_enabled' );

		if ( $get || $edit ) {

			if ( ! class_exists( '\Jet_Engine\Relations\Rest\Public_Controller' ) ) {
				require jet_engine()->relations->component_path( 'rest-api/public-controller.php' );
			}

			$rest_controller = new Rest\Public_Controller();

			$rest_controller->register_routes( array(
				'rel_id' => $this->rel_id,
				'get'    => $get,
				'edit'   => $edit,
			) );
		}

	}

	/**
	 * Check if this relations is can be correctly registered
	 *
	 * @return boolean [description]
	 */
	public function is_valid() {

		$parent_object = jet_engine()->relations->types_helper->type_parts_by_name( $this->get_args( 'parent_object' ) );
		$parent_type   = jet_engine()->relations->types_helper->get_instances( $parent_object[0] );
		$child_object  = jet_engine()->relations->types_helper->type_parts_by_name( $this->get_args( 'child_object' ) );
		$child_type    = jet_engine()->relations->types_helper->get_instances( $child_object[0] );

		if ( ! $parent_type || ! $child_type ) {
			return false;
		}

		return true;

	}

	/**
	 * Register context for current relation into allowed context list
	 *
	 * @return [type] [description]
	 */
	public function register_context( $context ) {
		$context[ $this->get_context_name() ] = __( 'Related Items From ', 'jet-engine' ) . $this->get_relation_name();
		return $context;
	}

	/**
	 * Return object for relation context
	 *
	 * @return [type] [description]
	 */
	public function apply_context() {

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! $current_object ) {
			return null;
		}

		$object_type   = jet_engine()->relations->types_helper->get_type_for_object( $current_object );
		$related_items = $this->get_related_items_for_object( null, $object_type );

		if ( empty( $related_items ) ) {
			return new \stdClass();
		}

		if ( $this->get_args( 'parent_object' ) === $object_type ) {
			$from_object_type = $this->get_args( 'child_object' );
		} else {
			$from_object_type = $this->get_args( 'parent_object' );
		}

		if ( 1 === count( $related_items ) ) {
			// If we have single realted item - use it as source for object context
			$object = jet_engine()->relations->sources->get_source_object_by_id( $from_object_type, $related_items[0] );
		} else {

			// If we have multiple results - exact context is undefined, so we'll try to retrieve approppriate object from the current stack
			$object = jet_engine()->relations->sources->get_object_from_stack( $from_object_type );

			if ( ! $object ) {
				// fallback
				$object = jet_engine()->relations->sources->get_source_object_by_id( $from_object_type, $related_items[0] );
			}

		}

		if ( is_array( $object ) ) {

			$tmp_object = new \stdClass();

			foreach ( $object as $key => $value ) {
				$tmp_object->$key = $value;
			}

			$object = $tmp_object;

		}

		return $object;

	}

	/**
	 * Returns context name
	 *
	 * @return [type] [description]
	 */
	public function get_context_name() {
		return 'rel_' . $this->get_id();
	}

	/**
	 * Return rel id
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return $this->rel_id;
	}

	/**
	 * Return raw arguments of relation intsance
	 *
	 * @return [type] [description]
	 */
	public function get_args( $key = false, $default = false ) {

		if ( ! $key ) {
			return $this->raw_args;
		}

		return isset( $this->raw_args[ $key ] ) ? $this->raw_args[ $key ] : $default;

	}

	/**
	 * Setup DB manager instance
	 *
	 * @return [type] [description]
	 */
	public function setup_db() {

		$db_table = $this->get_args( 'db_table' );

		if ( $db_table ) {
			$this->db      = jet_engine()->relations->storage->get_db_instance( $this->get_id(), jet_engine()->relations->storage->get_db_schema() );
			$this->meta_db = jet_engine()->relations->storage->get_db_instance( $this->get_id() . '_meta', jet_engine()->relations->storage->get_meta_db_schema() );
		} else {
			$this->db      = jet_engine()->relations->storage->get_default_db();
			$this->meta_db = jet_engine()->relations->storage->get_default_meta_db();
		}

	}

	/**
	 * Check if current relation can have only one child item
	 *
	 * @return boolean [description]
	 */
	public function is_single_child() {

		$type       = $this->get_args( 'type' );
		$type       = explode( '_to_', $type );
		$child_type = isset( $type[1] ) ? $type[1] : 'one';

		return ( 'one' === $child_type );
	}

	/**
	 * Check if current relation can have only one parent item
	 *
	 * @return boolean [description]
	 */
	public function is_single_parent() {

		$type       = $this->get_args( 'type' );
		$type       = explode( '_to_', $type );
		$parent_type = isset( $type[0] ) ? $type[0] : 'one';

		return ( 'one' === $parent_type );
	}

	/**
	 * Setup DB manager instance
	 *
	 * @return [type] [description]
	 */
	public function setup_controls() {

		$parent_object  = $this->get_args( 'parent_object' );
		$child_object   = $this->get_args( 'child_object' );
		$parent_control = $this->get_args( 'parent_control' );
		$child_control  = $this->get_args( 'child_control' );
		$type           = $this->get_args( 'type' );
		$type           = explode( '_to_', $type );
		$parent_type    = isset( $type[0] ) ? $type[0] : 'one';
		$child_type     = isset( $type[1] ) ? $type[1] : 'one';

		if ( $parent_control ) {
			$this->control_context = 'parent_control';
			$this->setup_object_controls( $parent_object, $child_type );
		}

		if ( $child_control ) {
			$this->control_context = 'child_control';
			$this->setup_object_controls( $child_object, $parent_type );
		}

	}

	/**
	 * retyurns list of the fields for the create item control
	 * @return [type] [description]
	 */
	public function get_create_control_fields( $for ) {
		$object = $this->get_args( $for );
		return jet_engine()->relations->types_helper->get_create_control_fields( $object, $this );
	}

	/**
	 * Returns new instance of controls class
	 *
	 * @param  [type] $class       [description]
	 * @param  array  $object_data [description]
	 * @param  string $type        [description]
	 * @param  string $label       [description]
	 * @return [type]              [description]
	 */
	public function init_controls_class( $class, $object_data = array() ) {

		if ( ! class_exists( '\Jet_Engine\Relations\Controls\Base' ) ) {

			require_once jet_engine()->relations->component_path( 'controls/base.php' );
			require_once jet_engine()->relations->component_path( 'controls/post-meta.php' );
			require_once jet_engine()->relations->component_path( 'controls/term-meta.php' );
			require_once jet_engine()->relations->component_path( 'controls/user-meta.php' );

			/**
			 * Allways include custom control classes on this hook (if you don't have autoloader implemented)
			 */
			do_action( 'jet-engine/relation/include-controls-class', $this );

		}

		new $class( array(
			'object_type' => $object_data[0],
			'object_name' => $object_data[1],
			'relation'    => $this,
			'context'     => $this->control_context,
		) );

	}

	/**
	 * Setup object controls
	 *
	 * @return [type] [description]
	 */
	public function setup_object_controls( $object, $type ) {

		$object_data = jet_engine()->relations->types_helper->type_parts_by_name( $object );

		if ( ! isset( $object_data[1] ) ) {
			$this->init_controls_class( '\Jet_Engine\Relations\Controls\Post_Meta', array( 'posts', $object ) );
		}

		add_action( 'jet-engine/relation/mix-object-controls/users', array( $this, 'init_users_controls_class' ), 10, 2 );

		switch ( $object_data[0] ) {
			case 'posts':
				$this->init_controls_class( '\Jet_Engine\Relations\Controls\Post_Meta', $object_data );
				break;

			case 'terms':
				$this->init_controls_class( '\Jet_Engine\Relations\Controls\Term_Meta', $object_data );
				break;

			case 'mix':
				do_action( 'jet-engine/relation/mix-object-controls/' . $object_data[1], $object_data, $this );
				break;

			default:
				do_action( 'jet-engine/relation/setup-object-controls/' . $object_data[0], $object_data, $this );
				break;
		}

		remove_action( 'jet-engine/relation/mix-object-controls/users', array( $this, 'init_users_controls_class' ), 10, 2 );

	}

	/**
	 * Initialize users control class
	 * @return [type] [description]
	 */
	public function init_users_controls_class( $object_data ) {
		$this->init_controls_class( '\Jet_Engine\Relations\Controls\User_Meta', $object_data );
	}

	/**
	 * Returns relation metafields
	 *
	 * @return [type] [description]
	 */
	public function get_meta_fields( $format = false, $filter = false, $return = ARRAY_N ) {

		$meta_fields = ! empty( $this->get_args( 'meta_fields' ) ) ? $this->get_args( 'meta_fields' ) : array();

		if ( ! is_array( $meta_fields ) ) {
			$meta_fields = array();
		}

		if ( $format ) {

			if ( ! class_exists( '\Jet_Engine_CPT_Meta' ) ) {
				require_once jet_engine()->plugin_path( 'includes/components/meta-boxes/post.php' );
			}

			$meta_manager = new \Jet_Engine_CPT_Meta( false, $meta_fields );
			$meta_manager->set_blocks_flag();

			$meta_fields = $meta_manager->prepare_meta_fields( $meta_fields );

			foreach ( $meta_fields as $key => $field_data ) {

				$field_data['name'] = $key;

				if ( ! empty( $field_data['description'] ) ) {
					$field_data['description'] = wp_strip_all_tags( $field_data['description'] );
				}

				if ( ! empty( $field_data['options_callback'] )
					&& is_callable( $field_data['options_callback'] ) 
				) {

					$field_data['options'] = \Jet_Engine_Tools::get_options_from_callback(
						$field_data['options_callback'], true
					);

					unset( $field_data['options_callback'] );
				}

				$meta_fields[ $key ] = $field_data;

			}

		}

		if ( ARRAY_N === $return ) {
			$meta_fields = array_values( $meta_fields );
		} elseif ( ! $format ) {
			foreach ( $meta_fields as $field_data ) {
				$meta_fields[ $field_data['name'] ] = $field_data;
			}
		}

		if ( $filter && is_callable( $filter ) ) {
			return array_map( $filter, $meta_fields );
		} else {
			return $meta_fields;
		}
	}

	/**
	 * Register new column for current relation
	 * @param [type] $key      [description]
	 * @param [type] $callback [description]
	 */
	public function add_table_column( $object, $key, $name, $callback ) {

		// if we adds columns for parent object of relation, table with items of this object will be shown on child object page,
		// so we need to modify a child table and vice versa
		$arg = ( 'parent_object' === $object ) ? 'child_table' : 'parent_table';

		$current = $this->get_args( $arg, array() );
		$current[ $key ] = array(
			'enabled'  => true,
			'name'     => $name,
			'callback' => $callback,
		);

		$this->raw_args[ $arg ] = $current;

	}

	/**
	 * Returns available columns list fro given object
	 *
	 * @return [type] [description]
	 */
	public function get_table_columns_for_object( $object ) {

		$defaults = array(
			'title' => __( 'Title', 'jet-engine' ),
		);

		$custom_cols = $this->get_object_column( $object );

		if ( ! empty( $custom_cols ) ) {
			foreach ( $custom_cols as $key => $data ) {
				if ( ! empty( $data['enabled'] ) ) {
					$defaults[ $key ] = $data['name'];
				}
			}
		}

		$meta_fields = $this->get_meta_fields();

		if ( ! empty( $meta_fields ) ) {
			$defaults['meta'] = __( 'Meta Data', 'jet-engine' );
		}

		$defaults['actions'] = __( 'Actions', 'jet-engine' );

		return $defaults;

	}

	/**
	 * Check if given object has given columns enabled for edit page table of this object
	 *
	 * @param  [type] $object [description]
	 * @param  [type] $column [description]
	 * @return [type]         [description]
	 */
	public function object_has_column( $object, $column ) {
		$col = $this->get_object_column( $object, $column );
		return ( ! empty( $col['enabled'] ) ) ? true : false;
	}

	/**
	 * Returns information about given columns for given object
	 *
	 * @param  [type] $object [description]
	 * @param  [type] $column [description]
	 * @return [type]         [description]
	 */
	public function get_object_column( $object, $column = null ) {

		$option_key = ( $object === $this->get_args( 'parent_object' ) ) ? 'child_table' : 'parent_table';
		$table_data = $this->get_args( $option_key, array() );
		$col        = array();

		if ( ! $column ) {
			return $table_data;
		}

		if ( ! empty( $table_data ) ) {
			$col = $table_data[ $column ];
		}

		return $col;

	}

	/**
	 * Returns relation name by objects
	 *
	 * @return [type] [description]
	 */
	public function get_relation_name() {

		$labels = $this->get_args( 'labels' );

		// Returns human-readable name if defined
		if ( ! empty( $labels['name'] ) ) {
			return $labels['name'];
		}

		// If not- generate name automatically
		$parent_object = $this->get_args( 'parent_object' );
		$child_object  = $this->get_args( 'child_object' );

		$parent_object = jet_engine()->relations->types_helper->type_parts_by_name( $parent_object );
		$child_object  = jet_engine()->relations->types_helper->type_parts_by_name( $child_object );

		return sprintf(
			__( '%1$s to %2$s' ),
			jet_engine()->relations->types_helper->get_type_label( 'plural', $parent_object[0], $parent_object[1] ),
			jet_engine()->relations->types_helper->get_type_label( 'plural', $child_object[0], $child_object[1] )
		);

	}

	/**
	 * Returns related items for given object
	 * Automatically detects - we need to get children or parent items by object.
	 *
	 * @param  [type] $object [description]
	 * @return [type]         [description]
	 */
	public function get_related_items_for_object( $object = null, $object_type = null ) {

		if ( ! $object_type ) {
			$object_type = jet_engine()->relations->types_helper->get_type_for_object( $object );
		}

		if ( $this->get_args( 'parent_object' ) === $object_type ) {
			return $this->get_children( jet_engine()->listings->data->get_current_object_id( $object ), 'ids' );
		} else {
			return $this->get_parents( jet_engine()->listings->data->get_current_object_id( $object ), 'ids' );
		}

		return array();

	}

	/**
	 * Returns related children id/ids
	 *
	 * @param  [type] $parent_id [description]
	 * @return [type]            [description]
	 */
	public function get_children( $parent_id, $fields = 'all' ) {

		$query_args = array( array(
			'field' => 'rel_id',
			'value' => $this->get_id(),
		) );

		if ( is_array( $parent_id ) ) {
			$query_args[] = array(
				'field'    => 'parent_object_id',
				'value'    => $parent_id,
				'operator' => 'IN',
			);
		} elseif ( $parent_id ) {
			$query_args[] = array(
				'field' => 'parent_object_id',
				'value' => $parent_id,
			);
		}

		$cache_key = $this->get_cache_key( $parent_id, '0' );
		$children  = wp_cache_get( $cache_key, $this->rel_cache_group );

		if ( ! $children ) {
			$children = $this->db->query( $query_args );
			wp_cache_set( $cache_key, $children, $this->rel_cache_group );
		}

		if ( 'all' !== $fields ) {
			$children = array_map( function( $item ) use ( $fields ) {
				return $item['child_object_id'];
			}, $children );
		}

		$result = ! empty( $children ) ? $children : array();

		return apply_filters( 'jet-engine/relations/get-children', $result, $parent_id, $fields, $this );

	}

	/**
	 * Returns related parent id/ids
	 *
	 * @param  [type] $parent_id [description]
	 * @return [type]            [description]
	 */
	public function get_parents( $child_id, $fields = 'all' ) {

		$query_args = array( array(
			'field' => 'rel_id',
			'value' => $this->get_id(),
		) );

		if ( is_array( $child_id ) ) {
			$query_args[] = array(
				'field'    => 'child_object_id',
				'value'    => $child_id,
				'operator' => 'IN',
			);
		} elseif ( $child_id ) {
			$query_args[] = array(
				'field' => 'child_object_id',
				'value' => $child_id,
			);
		}

		$cache_key = $this->get_cache_key( '0', $child_id );
		$parents   = wp_cache_get( $cache_key, $this->rel_cache_group );

		if ( ! $parents ) {
			$parents = $this->db->query( $query_args );
			wp_cache_set( $cache_key, $parents, $this->rel_cache_group );
		}

		if ( 'all' !== $fields ) {
			$parents = array_map( function( $item ) use ( $fields ) {
				return $item['parent_object_id'];
			}, $parents );
		}

		$result = ! empty( $parents ) ? $parents : array();

		return apply_filters( 'jet-engine/relations/get-parents', $result, $child_id, $fields, $this );

	}

	/**
	 * Returns related siblings list
	 *
	 * @param  [type] $object_id [description]
	 * @param  [type] $from      [description]
	 * @param  string $fields    [description]
	 * @return [type]            [description]
	 */
	public function get_siblings( $object_id = null, $from = 'child_object', $fields = 'all' ) {

		if ( ! $object_id ) {
			return array();
		}

		$query_args = array( array(
			'field' => 'rel_id',
			'value' => $this->get_id(),
		) );

		$from_object = $from . '_id';
		$rel_object  = ( 'parent_object_id' === $from_object ) ? 'child_object_id' : 'parent_object_id';

		if ( 'parent_object_id' === $rel_object ) {
			$rel_ids = $this->get_parents( $object_id, 'ids' );
		} else {
			$rel_ids = $this->get_children( $object_id, 'ids' );
		}

		if ( empty( $rel_ids ) ) {
			return array();
		}

		$query_args[] = array(
			'field'    => $rel_object,
			'value'    => $rel_ids,
			'operator' => 'IN',
		);

		if ( is_array( $object_id ) ) {
			$query_args[] = array(
				'field'    => $from_object,
				'value'    => $object_id,
				'operator' => 'NOT IN',
			);
		} elseif ( $object_id ) {
			$query_args[] = array(
				'field'    => $from_object,
				'value'    => $object_id,
				'operator' => '!=',
			);
		}

		$cache_key = $this->get_cache_key( $from_object, $object_id, array( 'siblings' ) );
		$result    = wp_cache_get( $cache_key, $this->rel_cache_group );

		if ( ! $result ) {
			$result = $this->db->query( $query_args );
		}

		if ( 'all' !== $fields ) {
			$result = array_map( function( $item ) use ( $fields, $from_object ) {
				return $item[ $from_object ];
			}, $result );
		}

		$result = ! empty( $result ) ? $result : array();

		return apply_filters( 'jet-engine/relations/get-siblings', $result, $object_id, $fields, $this );

	}

	/**
	 * Check if given object type and name combination is arent fro current relation
	 *
	 * @return boolean [description]
	 */
	public function is_parent( $type, $name ) {
		$parent_object = $this->get_args( 'parent_object' );
		return $parent_object === jet_engine()->relations->types_helper->type_name_by_parts( $type, $name );
	}

	/**
	 * Callback to remove related items on deletion of initial item from given object
	 *
	 * @param  [type] $object  [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function cleanup_relation( $object, $item_id ) {

		$parent_object = false;
		$child_object  = false;

		if ( $this->get_args( 'parent_object' ) === $object ) {
			$parent_object = $item_id;
		} else {
			$child_object = $item_id;
		}

		$this->delete_rows( $parent_object, $child_object, true );
		$this->db->reset_cache();
		$this->meta_db->reset_cache();

	}

	/**
	 * Delete parent - child pair for current relation.
	 *
	 * if $parent_object is empty - will be deleted all rows for current relations which has $child_object
	 * if $child_object is empty - will be deleted all rows for current relations which has $parent_object
	 * if both empty - will be deleted all rows for current relation
	 *
	 * @param  [type]  $parent_object [description]
	 * @param  [type]  $child_object  [description]
	 * @return [type]                 [description]
	 */
	public function delete_rows( $parent_object = false, $child_object = false, $clear_meta = true ) {

		$delete_where = array(
			'rel_id' => $this->get_id(),
		);

		if ( false !== $parent_object ) {
			$delete_where['parent_object_id'] = $parent_object;
		}

		if ( false !== $child_object ) {
			$delete_where['child_object_id'] = $child_object;
		}

		if ( $clear_meta && $this->meta_db->is_table_exists() ) {
			$this->meta_db->delete( $delete_where );
		}

		$this->db->delete( $delete_where );

		do_action( 'jet-engine/relation/delete/after', $parent_object, $child_object, $clear_meta, $this );

		$this->db->reset_cache();
		$this->meta_db->reset_cache();

	}

	/**
	 * Clean up meta fields which are exists in the DB
	 * @param  boolean $meta_fields [description]
	 * @return [type]               [description]
	 */
	public function cleanup_meta( $parent_object = null, $child_object = null, $meta_fields = false ) {

		if ( ! $meta_fields ) {
			$meta_fields = $this->get_meta_fields();
		}

		$allowed_keys = array();

		foreach ( $meta_fields as $field ) {
			$allowed_keys[] = $field['name'];
		}

		$all_meta = $this->get_all_meta( $parent_object, $child_object );

		if ( empty( $all_meta ) ) {
			return;
		}

		if ( $child_object ) {
			foreach ( $all_meta as $meta_key => $meta_value ) {
				if ( ! in_array( $meta_key, $allowed_keys ) ) {
					$this->delete_meta( $parent_object, $child_object, $meta_key );
				}
			}
		} else {
			foreach ( $all_meta as $child_id => $child_meta ) {
				foreach ( $child_meta as $meta_key => $meta_value ) {
					if ( ! in_array( $meta_key, $allowed_keys ) ) {
						$this->delete_meta( $parent_object, $child_id, $meta_key );
					}
				}
			}
		}

		$this->db->reset_cache();
		$this->meta_db->reset_cache();

	}

	/**
	 * Update relation data in the DB
	 *
	 * @param  [type]  $parent_object [description]
	 * @param  [type]  $child_object  [description]
	 * @return [type]                 [description]
	 */
	public function update_all_meta( $new_meta = array(), $parent_object = null, $child_object = null ) {

		if ( null === $parent_object || null === $child_object ) {
			return;
		}

		if ( ! $this->meta_db->is_table_exists() ) {
			$this->meta_db->create_table();
		}

		$allowed_meta = $this->get_meta_fields();

		$this->cleanup_meta( $parent_object, $child_object, $allowed_meta );

		foreach ( $allowed_meta as $field ) {

			$name = ! empty( $field['name'] ) ? $field['name'] : false;

			if ( ! $name ) {
				continue;
			}

			if ( isset( $new_meta[ $name ] ) ) {

				$value = $this->sanitize_meta( $new_meta[ $name ], $field );
				$this->update_meta( $parent_object, $child_object, $name, $value );

			} else {
				$this->delete_meta( $parent_object, $child_object, $name );
			}

		}

		do_action( 'jet-engine/relation/update-all-meta/after', $parent_object, $child_object, $new_meta, $this );

		$this->db->reset_cache();
		$this->meta_db->reset_cache();

	}

	/**
	 * Get formatted meta data output for editor
	 *
	 * @param  array  $meta [description]
	 * @return [type]       [description]
	 */
	public function format_meta( $meta = array() ) {

		if ( empty( $meta ) ) {
			return array();
		}

		$fields = $this->get_meta_fields( false, false, ARRAY_A );

		foreach ( $meta as $key => $value ) {

			$field = isset( $fields[ $key ] ) ? $fields[ $key ] : false;

			if ( ! $field ) {
				continue;
			}

			switch ( $field['type'] ) {

				case 'date':

					if ( ! empty( $field['is_timestamp'] ) && \Jet_Engine_Tools::is_valid_timestamp( $value ) ) {
						$value = date( 'Y-m-d', $value );
					}

					break;

				case 'datetime-local':

					if ( ! empty( $field['is_timestamp'] ) && \Jet_Engine_Tools::is_valid_timestamp( $value ) ) {
						$value = date( 'Y-m-d\TH:i', $value );
					}

					break;

			}

			$meta[ $key ] = $value;

		}

		return $meta;

	}

	/**
	 * Sanitize meta field by field data
	 *
	 * @param  [type] $input [description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function sanitize_meta( $input, $field ) {

		switch ( $field['type'] ) {

			case 'date':
			case 'datetime-local':

				if ( ! empty( $field['is_timestamp'] ) && ! \Jet_Engine_Tools::is_valid_timestamp( $input ) ) {
					$input = strtotime( $input );
				}

				break;

		}

		return $input;

	}

	/**
	 * Update meta for parent+child pair
	 *
	 * @param  [type] $parent_object [description]
	 * @param  [type] $child_object  [description]
	 * @param  [type] $meta_key      [description]
	 * @param  string $meta_value    [description]
	 * @return [type]                [description]
	 */
	public function update_meta( $parent_object, $child_object, $meta_key, $meta_value = '' ) {

		$query = array(
			'rel_id'           => $this->get_id(),
			'parent_object_id' => $parent_object,
			'child_object_id'  => $child_object,
			'meta_key'         => $meta_key,
		);

		$exists = $this->meta_db->query( $query );

		if ( ! empty( $exists ) ) {
			$this->meta_db->update( array( 'meta_value' => $meta_value ), $query );
		} else {
			$query['meta_value'] = $meta_value;
			$this->meta_db->insert( $query );
		}

		$cache_key = $this->get_cache_key( $parent_object, $child_object );

		wp_cache_delete( $cache_key, $this->rel_cache_group );

		$this->db->reset_cache();
		$this->meta_db->reset_cache();

	}

	/**
	 * Delete meta row
	 *
	 * @param  [type] $parent_object [description]
	 * @param  [type] $child_object  [description]
	 * @param  [type] $meta_key      [description]
	 * @return [type]                [description]
	 */
	public function delete_meta( $parent_object, $child_object = '', $meta_key = null ) {

		$query = array(
			'rel_id'           => $this->get_id(),
			'parent_object_id' => $parent_object,
		);

		if ( $child_object ) {
			$query['child_object_id'] = $child_object;
		}

		if ( $meta_key ) {
			$query['meta_key'] = $meta_key;
		}

		$this->meta_db->delete( array(
			'rel_id'           => $this->get_id(),
			'parent_object_id' => $parent_object,
			'child_object_id'  => $child_object,
			'meta_key'         => $meta_key,
		) );

		$cache_key = $this->get_cache_key( $parent_object, $child_object );
		wp_cache_delete( $cache_key, $this->rel_cache_group );

		$this->db->reset_cache();
		$this->meta_db->reset_cache();

	}

	/**
	 * Get meta value by key for parent+child pair
	 *
	 * @param  [type] $parent_object [description]
	 * @param  [type] $child_object  [description]
	 * @param  [type] $meta_key      [description]
	 * @return [type]                [description]
	 */
	public function get_meta( $parent_object, $child_object, $meta_key ) {
		$meta = $this->get_all_meta( $parent_object, $child_object );
		return isset( $meta[ $meta_key ] ) ? $meta[ $meta_key ] : false;
	}

	/**
	 * Get all existing meta for parent+child pair
	 *
	 * @param  [type] $parent_object [description]
	 * @param  [type] $child_object  [description]
	 * @return [type]                [description]
	 */
	public function get_all_meta( $parent_object, $child_object = '' ) {

		$cache_key = $this->get_cache_key( $parent_object, $child_object );
		$meta      = wp_cache_get( $cache_key, $this->rel_cache_group );

		if ( false === $meta ) {

			$query = array(
				'rel_id'           => $this->get_id(),
				'parent_object_id' => $parent_object,
			);

			if ( $child_object ) {
				$query['child_object_id'] = $child_object;
			}

			$meta = $this->meta_db->query( $query );

			if ( ! empty( $meta ) ) {

				$prepared_meta = array();

				foreach ( $meta as $meta_row ) {
					if ( $child_object ) {
						$prepared_meta[ $meta_row['meta_key'] ] = $meta_row['meta_value'];
					} else {

						$child_id = $meta_row['child_object_id'];

						if ( ! isset( $prepared_meta[ $child_id ] ) ) {
							$prepared_meta[ $child_id ] = array();
						}

						$prepared_meta[ $child_id ][ $meta_row['meta_key'] ] = $meta_row['meta_value'];

					}
				}

				$meta = $prepared_meta;

			}

			wp_cache_set( $cache_key, $meta, $this->rel_cache_group );

		}

		return $meta;

	}

	/**
	 * Returns meta for current object
	 *
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function get_current_meta( $key ) {

		$current_object_id = jet_engine()->listings->data->get_current_object_id();
		$current_object    = jet_engine()->listings->data->get_current_object();

		$key_data = explode( '::', $key );
		$key = $key_data[0];
		$suffix = isset( $key_data[1] ) ? $key_data[1] : false;



		if ( ! $current_object || ! $current_object_id ) {
			return false;
		}

		$res       = false;
		$parent_id = false;
		$child_id  = false;

		if ( $suffix ) {
			switch ( $suffix ) {
				case 'child':
					
					$child_id      = $current_object_id;
					$parent_object = jet_engine()->listings->objects_stack->get_parent_object_from_stack();

					if ( $parent_object ) {
						$parent_id  = jet_engine()->listings->data->get_current_object_id( $parent_object );
					}

					break;
				
				case 'parent':
					
					$parent_id      = $current_object_id;
					$child_object = jet_engine()->listings->objects_stack->get_parent_object_from_stack();

					if ( $child_object ) {
						$child_id  = jet_engine()->listings->data->get_current_object_id( $child_object );
					}

					break;
			}
		} else {

			$from_object = jet_engine()->relations->types_helper->get_type_for_object( $current_object );

			if ( $this->get_args( 'parent_object' ) === $from_object ) {

				$child_object = jet_engine()->relations->sources->get_object_from_stack( $this->get_args( 'child_object' ) );

				if ( ! $child_object ) {
					return false;
				}

				$parent_id = $current_object_id;
				$child_id  = jet_engine()->listings->data->get_current_object_id( $child_object );

			} else {

				$parent_object = jet_engine()->relations->sources->get_object_from_stack( $this->get_args( 'parent_object' ) );

				if ( ! $parent_object ) {
					return false;
				}

				$parent_id = jet_engine()->listings->data->get_current_object_id( $parent_object );
				$child_id  = $current_object_id;

			}

		}

		if ( $parent_id && $child_id ) {
			$res = $this->get_meta( $parent_id, $child_id, $key );
		}

		return $res;

	}

	/**
	 * Return WP cahce key for current relation parent+child pair
	 *
	 * @param  [type] $parent_object [description]
	 * @param  [type] $child_object  [description]
	 * @return [type]                [description]
	 */
	public function get_cache_key( $parent_object, $child_object, $custom_args = array() ) {

		if ( is_array( $parent_object ) ) {
			$parent_object = implode( '.', $parent_object );
		}

		if ( is_array( $child_object ) ) {
			$child_object = implode( '.', $child_object );
		}

		$custom = '';

		foreach ( $custom_args as $key => $value ) {
			$custom .= $key . '.' . $value;
		}

		return md5( $this->get_id() . $parent_object . $child_object . $custom );
	}

	/**
	 * Update relation data in the DB
	 *
	 * @param  [type]  $parent_object [description]
	 * @param  [type]  $child_object  [description]
	 * @return [type]                 [description]
	 */
	public function update( $parent_object, $child_object ) {

		// todo - update related meta

		if ( ! $this->db->is_table_exists() ) {
			$this->db->create_table();
		}

		$exists = $this->db->query( array(
			'rel_id'           => $this->get_id(),
			'parent_object_id' => $parent_object,
			'child_object_id'  => $child_object,
		) );

		if ( ! empty( $exists ) ) {
			return $exists[0];
		}

		do_action( 'jet-engine/relation/update/before', $parent_object, $child_object, $this );

		$update = false;
		$where  = array(
			'rel_id' => $this->get_id()
		);

		if ( $this->is_single_child() ) {

			$children = $this->get_children( $parent_object );

			if ( ! empty( $children ) ) {
				$update                    = true;
				$where['parent_object_id'] = $children[0]['parent_object_id'];
			}

		}

		$context = $this->get_update_context();

		if ( $this->is_single_parent() ) {

			if ( 'parent' === $context ) {
				/**
				 * If we updating parent items from children object,
				 * we need to update exiting parent item fro current child object
				 */
				$parents = $this->get_parents( $child_object );

				if ( ! empty( $parents ) ) {
					$update                   = true;
					$where['child_object_id'] = $parents[0]['child_object_id'];
				}

			} else {
				/**
				 * If we updating children items from parent object,
				 * we need to delete all other parent objects fromm updated child and than insert a new one
				 */
				$this->delete_rows( false, $child_object );
			}

		}

		if ( $update ) {

			$to_update = array(
				'rel_id'           => $this->get_id(),
				'parent_rel'       => $this->get_args( 'parent_rel' ),
				'parent_object_id' => $parent_object,
				'child_object_id'  => $child_object,
			);

			$updated = $this->db->update( $to_update, $where );

			if ( $updated ) {
				$item_id = $this->db->query( $to_update );

				if ( ! empty( $item_id ) ) {
					$item_id = $item_id[0];
				}

			}

		} else {

			$item_id = $this->db->insert( array(
				'rel_id'           => $this->get_id(),
				'parent_rel'       => $this->get_args( 'parent_rel' ),
				'parent_object_id' => $parent_object,
				'child_object_id'  => $child_object,
			) );

		}

		do_action( 'jet-engine/relation/update/after', $parent_object, $child_object, $item_id, $this );

		$this->db->reset_cache();
		$this->meta_db->reset_cache();
		$this->reset_update_context();

		if ( ! empty( $item_id ) && is_array( $item_id ) ) {
			return $item_id;
		} elseif ( $item_id ) {
			return $this->db->get_item( $item_id );
		} else {
			return false;
		}

	}

	/**
	 * Reset current update context.
	 * Should be called after each relation update to avoid contexts overlaping
	 * @return [type] [description]
	 */
	public function reset_update_context() {
		$this->set_update_context( null );
	}

	/**
	 * Set current update context.
	 * Should be called before relation update to specify in what context we updating it - add parents from child or vice versa
	 * parent - means we seeting up parent related items for the child object (update initiated from child object)
	 * child  - means we seeting up children related items for the parent object (update initiated from parent object)
	 *
	 * @return [type] [description]
	 */
	public function set_update_context( $context ) {
		$this->update_context = $context;
	}

	/**
	 * Get current update context
	 *
	 * @return [type] [description]
	 */
	public function get_update_context() {
		return $this->update_context;
	}

}
