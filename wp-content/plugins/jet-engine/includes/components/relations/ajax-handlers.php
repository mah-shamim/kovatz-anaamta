<?php
namespace Jet_Engine\Relations;

/**
 * Relations manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Relations Manager class
 */
class Ajax_Handlers {

	public function __construct() {

		$endpoints = array(
			'get_type_items',
			'get_related_items',
			'save_relation_meta',
			'get_related_item_meta',
			'update_relation_items',
			'disconnect_relation_items',
			'create_item_of_type',
		);

		foreach ( $endpoints as $endpoint ) {
			add_action( 'wp_ajax_jet_engine_relations_' . $endpoint, array( $this, $endpoint ) );
		}

	}

	/**
	 * Returns error message
	 *
	 * @return [type] [description]
	 */
	public function get_error_message() {

		$message = wp_cache_get( 'jet-engine-relations-error' );

		if ( $message ) {
			wp_cache_delete( 'jet-engine-relations-error' );
		}

		return $message;
	}

	/**
	 * Create new item of given type
	 *
	 * @return [type] [description]
	 */
	public function create_item_of_type() {

		// verify nonce and abort if not
		$this->check_nonce();

		// verify user access
		$this->check_user_access();

		$related_object_type = ! empty( $_REQUEST['relatedObjectType'] ) ? sanitize_text_field( $_REQUEST['relatedObjectType'] ) : false;
		$related_object_name = ! empty( $_REQUEST['relatedObjectName'] ) ? sanitize_text_field( $_REQUEST['relatedObjectName'] ) : false;
		$data                = ! empty( $_REQUEST['item'] ) ? $_REQUEST['item'] : array();

		if ( ! $related_object_type || ! $related_object_name ) {
			wp_send_json_error( 'Incomplete request' );
		}

		$item_id = jet_engine()->relations->types_helper->create_item(
			jet_engine()->relations->types_helper->type_name_by_parts( $related_object_type, $related_object_name ),
			$data
		);

		if ( $item_id ) {
			wp_send_json_success( array( 'itemID' => $item_id ) );
		} else {

			$message = $this->get_error_message();

			if ( ! $message ) {
				$message = __( 'Can`t create a new item', 'jet-engine' );
			}

			wp_send_json_error( $message );

		}

	}

	/**
	 * Get items callback
	 *
	 * @return [type] [description]
	 */
	public function get_type_items() {

		// verify nonce and abort if not
		$this->check_nonce();

		// verify user access
		$this->check_user_access();

		$type     = ! empty( $_GET['objectType'] ) ? sanitize_text_field( $_GET['objectType'] ) : false;
		$object   = ! empty( $_GET['object'] ) ? sanitize_text_field( $_GET['object'] ) : false;
		$existing = ! empty( $_REQUEST['existing'] ) ? $_REQUEST['existing'] : array();

		$relation = $this->get_relation_from_request();

		if ( ! $relation ) {
			wp_send_json_error( __( 'Relation ID not found in the reques', 'jet-engine' ) );
		}

		wp_send_json_success( jet_engine()->relations->types_helper->get_type_items( $type, $object, $relation, $existing ) );

	}

	/**
	 * Retrieve relation object from request data
	 *
	 * @return [type] [description]
	 */
	public function get_relation_from_request() {

		$rel_id = isset( $_REQUEST['relID'] ) ? sanitize_text_field( $_REQUEST['relID'] ) : false;

		if ( ! $rel_id ) {
			wp_send_json_error( __( 'Relation not found in the request', 'jet-engine' ) );
		}

		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			wp_send_json_error( __( 'Relation not found', 'jet-engine' ) );
		}

		return $relation;
	}

	/**
	 * Ajax callback to retrieve related items list
	 *
	 * @return [type] [description]
	 */
	public function get_related_items() {

		// verify nonce and abort if not
		$this->check_nonce();

		// verify user access
		$this->check_user_access();

		$related_object_type = ! empty( $_REQUEST['objectType'] ) ? sanitize_text_field( $_REQUEST['objectType'] ) : false;
		$related_object_name = ! empty( $_REQUEST['object'] ) ? sanitize_text_field( $_REQUEST['object'] ) : false;
		$current_object      = ! empty( $_REQUEST['currentObjectID'] ) ? sanitize_text_field( $_REQUEST['currentObjectID'] ) : false;

		if ( ! $related_object_type || ! $current_object || ! $related_object_name ) {
			wp_send_json_error( __( 'Incomplete request', 'jet-engine' ) );
		}

		$relation = $this->get_relation_from_request();

		if ( isset( $_REQUEST['isParentProcessed'] ) ) {
			$is_parent_processed = filter_var( $_REQUEST['isParentProcessed'], FILTER_VALIDATE_BOOLEAN );
		} else {
			$is_parent_processed = $relation->is_parent( $related_object_type, $related_object_name );
		}

		if ( ! $relation->db->is_table_exists() ) {
			$relation->db->create_table();
		}

		wp_send_json_success( $this->get_related_list( $current_object, $relation, $is_parent_processed ) );

	}

	/**
	 * Process disconnect item request
	 *
	 * @return [type] [description]
	 */
	public function disconnect_relation_items() {

		// verify nonce and abort if not
		$this->check_nonce();

		// verify user access
		$this->check_user_access();

		$data = $this->get_data_from_request();

		$relation            = $data['relation'];
		$parent_object_id    = $data['parent_object_id'];
		$child_object_id     = $data['child_object_id'];
		$is_parent_processed = $data['is_parent_processed'];
		$related_object_type = $data['related_object_type'];
		$related_object_name = $data['related_object_name'];
		$related_object      = $data['related_object'];
		$current_object      = $data['current_object'];
		$is_trash            = ! empty( $_POST['isTrash'] ) ? filter_var( $_POST['isTrash'], FILTER_VALIDATE_BOOLEAN ) : false;

		$relation->delete_rows( $parent_object_id, $child_object_id );

		$type = jet_engine()->relations->types_helper->type_name_by_parts( $related_object_type, $related_object_name );

		if ( $is_trash && jet_engine()->relations->types_helper->current_user_can( 'delete', $type, $related_object, $related_object_name ) ) {
			jet_engine()->relations->types_helper->delete_item( $type, $related_object );
		}

		wp_send_json_success( array(
			'related_list' => $this->get_related_list( $current_object, $relation, $is_parent_processed )
		) );

	}

	/**
	 * save_relation_meta callback
	 *
	 * @return [type] [description]
	 */
	public function save_relation_meta() {

		// verify nonce and abort if not
		$this->check_nonce();

		// verify user access
		$this->check_user_access();

		$data = $this->get_data_from_request();

		$relation            = $data['relation'];
		$parent_object_id    = $data['parent_object_id'];
		$child_object_id     = $data['child_object_id'];
		$is_parent_processed = $data['is_parent_processed'];

		$meta = isset( $_POST['meta'] ) ? $_POST['meta'] : array();

		$relation->update_all_meta( $meta, $parent_object_id, $child_object_id );

		wp_send_json_success();

	}

	public function get_related_item_meta() {

		// verify nonce and abort if not
		$this->check_nonce();

		// verify user access
		$this->check_user_access();

		$data = $this->get_data_from_request();

		$relation            = $data['relation'];
		$parent_object_id    = $data['parent_object_id'];
		$child_object_id     = $data['child_object_id'];
		$is_parent_processed = $data['is_parent_processed'];

		if ( ! $relation->meta_db->is_table_exists() ) {
			$relation->meta_db->create_table();
		}

		$meta = isset( $_POST['meta'] ) ? $_POST['meta'] : array();

		wp_send_json_success( $relation->format_meta( $relation->get_all_meta( $parent_object_id, $child_object_id ) ) );
	}

	/**
	 * Returns typical data required for most of callbacks
	 *
	 * @return [type] [description]
	 */
	public function get_data_from_request() {

		$related_object      = ! empty( $_REQUEST['relatedObjectID'] ) ? sanitize_text_field( $_REQUEST['relatedObjectID'] ) : false;
		$related_object_type = ! empty( $_REQUEST['relatedObjectType'] ) ? sanitize_text_field( $_REQUEST['relatedObjectType'] ) : false;
		$related_object_name = ! empty( $_REQUEST['relatedObjectName'] ) ? sanitize_text_field( $_REQUEST['relatedObjectName'] ) : false;
		$current_object      = ! empty( $_REQUEST['currentObjectID'] ) ? sanitize_text_field( $_REQUEST['currentObjectID'] ) : false;

		if ( ! $related_object || ! $related_object_type || ! $current_object || ! $related_object_name ) {
			wp_send_json_error( __( 'Incomplete request', 'jet-engine' ) );
		}

		$relation = $this->get_relation_from_request();

		if ( isset( $_REQUEST['isParentProcessed'] ) ) {
			$is_parent_processed = filter_var( $_REQUEST['isParentProcessed'], FILTER_VALIDATE_BOOLEAN );
		} else {
			$is_parent_processed = $relation->is_parent( $related_object_type, $related_object_name );
		}

		if ( $is_parent_processed ) {
			$parent_object_id = $related_object;
			$child_object_id  = $current_object;
			$current_type     = $relation->get_args( 'child_object' );
		} else {
			$parent_object_id = $current_object;
			$child_object_id  = $related_object;
			$current_type     = $relation->get_args( 'parent_object' );
		}

		$current_object_type = jet_engine()->relations->types_helper->type_parts_by_name( $current_type );

		if ( ! jet_engine()->relations->types_helper->current_user_can( 'edit', $current_type, $current_object, $current_object_type[1] ) ) {
			wp_send_json_error( __( 'You are not allowed to do this', 'jet-engine' ) );
		}

		return array(
			'relation'            => $relation,
			'is_parent_processed' => $is_parent_processed,
			'parent_object_id'    => $parent_object_id,
			'child_object_id'     => $child_object_id,
			'related_object'      => $related_object,
			'related_object_type' => $related_object_type,
			'related_object_name' => $related_object_name,
			'current_object'      => $current_object,
		);
	}

	/**
	 * Update items callback
	 *
	 * @return [type] [description]
	 */
	public function update_relation_items() {

		// verify nonce and abort if not
		$this->check_nonce();

		// verify user access
		$this->check_user_access();

		$data = $this->get_data_from_request();

		$relation            = $data['relation'];
		$parent_object_id    = $data['parent_object_id'];
		$child_object_id     = $data['child_object_id'];
		$is_parent_processed = $data['is_parent_processed'];
		$current_object      = $data['current_object'];

		// if we currently processing parent - this means update comes from child object page
		if ( $is_parent_processed ) {
			$relation->set_update_context( 'parent' );
		} else {
			$relation->set_update_context( 'child' );
		}

		$_id = $relation->update( $parent_object_id, $child_object_id );

		wp_send_json_success( array(
			'related_item' => $_id,
			'related_list' => $this->get_related_list( $current_object, $relation, $is_parent_processed )
		) );

	}

	/**
	 * Returns correctly fromatted related items list for editor
	 *
	 * @param  [type]  $current_object      [description]
	 * @param  [type]  $relation            [description]
	 * @param  boolean $is_parent_processed [description]
	 * @return [type]                       [description]
	 */
	public function get_related_list( $current_object, $relation, $is_parent_processed = false ) {

		$related_list = $is_parent_processed ? $relation->get_parents( $current_object ) : $relation->get_children( $current_object );

		return array_map( function( $item ) use ( $is_parent_processed, $relation ) {

			if ( $is_parent_processed ) {
				$item_id    = $item['parent_object_id'];
				$current_id = $item['child_object_id'];
				$type       = $relation->get_args( 'parent_object' );
				$allow_del  = $relation->get_args( 'child_allow_delete' );
			} else {
				$item_id    = $item['child_object_id'];
				$current_id = $item['parent_object_id'];
				$type       = $relation->get_args( 'child_object' );
				$allow_del  = $relation->get_args( 'parent_allow_delete' );
			}

			$columns   = $relation->get_table_columns_for_object( $type );
			$type_data = jet_engine()->relations->types_helper->type_parts_by_name( $type );
			unset( $columns['actions'] );

			if ( isset( $columns['meta'] ) ) {
				unset( $columns['meta'] );
			}

			$item['columns'] = $this->get_columns_contents( $columns, $type, $item_id, $relation, $current_id );
			$item['actions'] = array(
				'view'       => jet_engine()->relations->types_helper->get_type_item_view_url( $type, $item_id, $relation ),
				'disconnect' => true,
			);

			if ( jet_engine()->relations->types_helper->current_user_can( 'edit', $type, $item_id, $type_data[1] ) ) {
				$item['actions']['edit'] = jet_engine()->relations->types_helper->get_type_item_edit_url( $type, $item_id, $relation );
			}

			if ( $allow_del && jet_engine()->relations->types_helper->current_user_can( 'delete', $type, $item_id, $type_data[1] ) ) {
				$item['actions']['trash'] = true;
			}

			$item['related_id'] = $item_id;
			$item['current_id'] = $current_id;

			return $item;

		}, $related_list );
	}

	/**
	 * Returns array wiith content of table columns for given item
	 *
	 * @param  [type] $columns [description]
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_columns_contents( $columns, $type, $item_id, $relation, $current_id ) {

		$result = array();

		foreach ( $columns as $key => $label ) {

			switch ( $key ) {
				case 'title':
					$result[] = jet_engine()->relations->types_helper->get_type_item_title( $type, $item_id, $relation );
					break;

				default:

					$column = $relation->get_object_column( $type, $key );

					if ( $column && ! empty( $column['callback'] ) && is_callable( $column['callback'] ) ) {
						$result[] = call_user_func( $column['callback'], $item_id, $current_id, $relation, $type );
					} else {
						$result[] = apply_filters( 'jet-engine/relations/column-content/' . $key, '', $item_id, $type, $relation );
					}

					break;
			}
		}

		return $result;

	}

	/**
	 * Verify request nonce
	 *
	 * @return [type] [description]
	 */
	public function check_nonce() {

		$nonce = ! empty( $_REQUEST['_nonce'] ) ? $_REQUEST['_nonce'] : false;

		if ( ! wp_verify_nonce( $nonce, 'jet-engine-relations-control' ) ) {
			wp_send_json_error( __( 'The link is expired, please reload the page and try again', 'jet-engine' ) );
		}

	}

	/**
	 * Check user access
	 *
	 * @return [type] [description]
	 */
	public function check_user_access() {

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( __( 'Access denied', 'jet-engine' ) );
		}

	}
}
