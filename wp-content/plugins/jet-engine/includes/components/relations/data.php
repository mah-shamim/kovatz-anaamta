<?php
namespace Jet_Engine\Relations;

/**
 * Relations data controller class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Relations Data class
 */
class Data extends \Jet_Engine_Base_Data {

	/**
	 * Edit slug
	 *
	 * @var string
	 */
	public $edit                 = 'edit-relation';
	public $option_name          = 'jet_engine_relations';
	public $replaced_option_name = 'jet_engine_relations_replaced';

	/**
	 * Table name
	 *
	 * @var string
	 */
	public $table = 'post_types';

	/**
	 * Query arguments
	 *
	 * @var array
	 */
	public $query_args = array(
		'status' => 'relation',
	);

	/**
	 * Sanitizr post type request
	 *
	 * @return void
	 */
	public function sanitize_item_request() {

		$valid = true;

		$this->request['slug']         = 1;
		$this->request['args']['slug'] = $this->request['slug'];

		return $valid;

	}

	/**
	 * Prepare post data from request to write into database
	 *
	 * @return array
	 */
	public function sanitize_item_from_request() {

		$request = $this->request;

		$result = array(
			'slug'        => '',
			'status'      => 'relation',
			'labels'      => array(),
			'args'        => array(),
			'meta_fields' => array(),
		);

		$args = array();

		$ensure_bool = array(
			'db_table',
			'parent_control',
			'child_control',
			'parent_manager',
			'child_manager',
			'parent_allow_delete',
			'child_allow_delete',
			'is_legacy',
			'rest_get_enabled',
			'rest_post_enabled',
		);

		$regular_args = array(
			'name'             => '',
			'parent_object'    => '',
			'child_object'     => '',
			'parent_rel'       => null,
			'type'             => '',
			'legacy_id'        => '',
			'rest_get_access'  => '',
			'rest_post_access' => '',
		);



		if ( ! empty( $request['is_legacy'] ) ) {

			$map_args = array(
				'post_type_1_control' => 'parent_control',
				'post_type_2_control' => 'child_control',
				'post_type_1'         => 'parent_object',
				'post_type_2'         => 'child_object',
				'parent_relation'     => 'parent_rel',
			);

			foreach ( $map_args as $legacy => $new ) {
				if ( ! isset( $request[ $new ] ) ) {
					$request[ $new ] = isset( $request[ $legacy ] ) ? $request[ $legacy ] : '';
				}
			}

		}

		foreach ( $ensure_bool as $key ) {
			$val = ! empty( $request[ $key ] ) ? $request[ $key ] : false;
			$args[ $key ] = filter_var( $val, FILTER_VALIDATE_BOOLEAN );
		}

		foreach ( $regular_args as $key => $default ) {
			$args[ $key ] = ! empty( $request[ $key ] ) ? sanitize_text_field( $request[ $key ] ) : $default;
		}

		$meta_fields = isset( $request['meta_fields'] ) ? $request['meta_fields'] : array();
		$meta_fields = $this->sanitize_meta_fields( $meta_fields );

		$args = jet_engine()->relations->types_helper->sanitize_relation_edit_args( $args, $request );

		$result['slug']        = '';
		$result['labels']      = ! empty( $request['labels'] ) ? $request['labels'] : array();
		$result['args']        = $args;
		$result['meta_fields'] = $meta_fields;

		return $result;

	}

	/**
	 * Find related posts for ppassed relation key and current post ID pair
	 *
	 * @param  [type] $meta_key [description]
	 * @param  [type] $post_id  [description]
	 * @return [type]           [description]
	 */
	public function find_related_posts( $meta_key, $post_id ) {

		global $wpdb;

		$related = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT post_id FROM $wpdb->postmeta WHERE `meta_key` = '%s' AND `meta_value` = %d;",
				$meta_key,
				$post_id
			)
		);

		return $related;

	}

	public function get_unique_name( $name = 'field', $initial = 'field', $list = array() ) {

		if ( ! in_array( $name, $list ) ) {
			return $name;
		} else {

			if ( $name === $initial ) {
				$name .= '_1';
			} else {

				$name = preg_replace_callback( '/_(\d)$/', function( $matches ) {

					if ( ! empty( $matches[1] ) ) {
						$i = intval( $matches[1] );
					}

					return '_' . $i;

				}, $name );

			}

			return $this->get_unique_name( $name, $initial, $list );
		}
	}

	/**
	 * Delete all related meta contains passed $post_id
	 *
	 * @param  [type] $meta_key [description]
	 * @param  [type] $post_id  [description]
	 * @return [type]           [description]
	 */
	public function delete_all_related_meta( $meta_key, $post_id ) {

		delete_post_meta( $post_id, $meta_key );
		$old_related = $this->find_related_posts( $meta_key, $post_id );

		if ( ! empty( $old_related ) ) {

			foreach ( $old_related as $related_post_id ) {
				delete_post_meta( $related_post_id, $meta_key, $post_id );
			}

		}

	}

	public function get_field_by_name( $field_name, $fields ) {

		foreach ( $fields as $index => $field ) {
			if ( $field['name'] === $field_name ) {
				$field['order'] = absint( $index );
				return $field;
			}
		}

		return false;

	}

	/**
	 * Sanitize meta fields
	 *
	 * @param  [type] $meta_fields [description]
	 * @return [type]              [description]
	 */
	public function sanitize_meta_fields( $meta_fields ) {

		$unique_names = array();

		if ( empty( $meta_fields ) ) {
			$meta_fields = array();
		}

		foreach ( $meta_fields as $index => $field ) {

			$name = ! empty( $field['name'] ) ? $field['name'] : 'field';
			$name = str_replace( '-', '_', sanitize_title( $name ) );
			$name = $this->get_unique_name( $name, $name, $unique_names );

			$meta_fields[ $index ]['name'] = $name;

			$unique_names[] = $name;

		}

		return $meta_fields;
	}

	/**
	 * Legacy. Not used for the new relation
	 *
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_item_by_id( $id ) {

		$item = $this->db->query(
			$this->table,
			array( 'id' => $id ),
			array( $this, 'filter_item_for_register' )
		);

		if ( ! empty( $item ) ) {
			return $item[0];
		} else {
			return false;
		}
	}

	/**
	 * Filter post type for edit
	 *
	 * @return array
	 */
	public function filter_item_for_edit( $item ) {

		$args        = maybe_unserialize( $item['args'] );
		$meta_fields = maybe_unserialize( $item['meta_fields'] );
		$labels      = $this->setup_labels( $item );

		if ( empty( $args ) ) {
			$args = array();
		}

		// For existing relations the `parent_allow_delete` and the `child_allow_delete` settings must be enabled.
		if ( ! empty( $args ) ) {

			if ( ! isset( $args['parent_allow_delete'] ) ) {
				$args['parent_allow_delete'] = true;
			}

			if ( ! isset( $args['child_allow_delete'] ) ) {
				$args['child_allow_delete'] = true;
			}

		}

		if ( empty( $meta_fields ) ) {
			$meta_fields = array();
		}

		$item['args'] = array_merge( $args, array( 'labels' => $labels, 'meta_fields' => $meta_fields ) );

		return $item;

	}

	/**
	 * Return blacklisted items names
	 *
	 * @return array
	 */
	public function items_blacklist() {
		return array();
	}

	/**
	 * Filter callback to apply legacy option
	 *
	 * @param  [type] $item [description]
	 * @return [type]       [description]
	 */
	public function filter_item_for_register( $item ) {

		$item = $this->filter_item_for_edit( $item );

		$is_legacy                 = isset( $item['args']['is_legacy'] ) ? $item['args']['is_legacy'] : true;
		$item['is_legacy']         = $is_legacy;
		$item['args']['is_legacy'] = $is_legacy;

		return $item;

	}

	/**
	 * Ensure labels array is corrctly set up
	 * @param  array  $item [description]
	 * @return [type]       [description]
	 */
	public function setup_labels( $item = array() ) {

		if ( ! empty( $item['labels'] ) ) {
			$labels = maybe_unserialize( $item['labels'] );
		} else {
			$labels = array();
		}

		if ( ! is_array( $labels ) ) {
			$labels = array();
		}

		if ( ! isset( $labels['name'] ) ) {

			if ( isset( $item['args'] ) && ! is_array( $item['args'] ) ) {
				$item['args'] = maybe_unserialize( $item['args'] );
			}

			$labels['name'] = isset( $item['args']['name'] ) ? $item['args']['name'] : '';
		}

		return $labels;
	}

	/**
	 * Returns post type in prepared for register format
	 *
	 * @return array
	 */
	public function get_item_for_register() {
		return array_map( array( $this, 'filter_item_for_register' ), $this->get_raw() );
	}

	/**
	 * Returns items by args without filtering
	 *
	 * @return array
	 */
	public function get_raw( $args = array() ) {

		if ( ! $this->raw ) {

			$option = get_option( $this->option_name, array() );

			if ( $option ) {
				$this->move_option_to_db( $option );
			}

			if ( ! empty( $this->query_args ) ) {
				$args = array_merge( $args, $this->query_args );
			}

			$this->raw = $this->db->query( $this->table, $args );
		}

		return $this->raw;
	}

	/**
	 * Move legacy relations from an option to DB
	 *
	 * @param  array  $relations [description]
	 * @return [type]            [description]
	 */
	public function move_option_to_db( $relations = array() ) {

		$replaced = get_option( $this->replaced_option_name );

		$existing = $this->db->query( $this->table, $this->query_args );

		if ( empty( $existing ) ) {
			$existing = array();
		}

		$existing = array_map( function( $item ) {
			$args = maybe_unserialize( $item['args'] );
			return isset( $args['legacy_id'] ) ? $args['legacy_id'] : false;
		}, $existing );

		if ( ! empty( $existing ) ) {
			$existing = array_filter( $existing );
		}

		$processed_legacy = array();
		$processed_new    = array();
		$parents          = array();

		foreach ( $relations as $id => $rel ) {

			if ( in_array( $id, $existing ) ) {
				continue;
			}

			$rel['is_legacy'] = true;
			$rel['legacy_id'] = $id;

			$this->set_request( $rel );
			$new_id = $this->create_item( false );

			if ( ! empty( $rel['parent_relation'] ) ) {
				$parents[ $new_id ] = $rel['parent_relation'];
			}

			$hash = $id;

			if ( $this->parent->legacy ) {
				$hash = $this->parent->legacy->get_relation_hash( $rel['post_type_1'], $rel['post_type_2'] );
			}

			$rel['id'] = $new_id;

			$processed_legacy[ $hash ] = $new_id;
			$processed_new[ $new_id ]  = $rel;

		}

		if ( ! empty( $parents ) ) {
			foreach ( $parents as $new_rel_id => $legacy_parent_id ) {
				$rel = $processed_new[ $new_rel_id ];
				$rel['parent_relation'] = $processed_legacy[ $legacy_parent_id ];
				$rel['parent_rel']      = $processed_legacy[ $legacy_parent_id ];
				$this->set_request( $rel );
				$this->edit_item( false );
			}
		}

		/**
		 * Uncomment this after 2.11.5+
		 * delete_option( $this->option_name );
		 */

		/**
		 * Remove this logic after delete_option will be uncommented
		 */
		if ( ! $replaced ) {
			update_option( $this->replaced_option_name, true, true );
		}

	}

	/**
	 * Query post types
	 *
	 * @return array
	 */
	public function get_items() {
		return array_map( array( $this, 'filter_item_for_register' ), $this->get_raw() );
	}

	/**
	 * Remove related data on relation deletion
	 *
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function before_item_delete( $item_id ) {
		do_action( 'jet-engine/relations/before-relation-delete', $item_id );
	}

}
