<?php
namespace Jet_Engine\Relations\Rest;

use Jet_Engine\Relations\Forms\Manager as Forms_Manager;

class Public_Controller {

	public $slug = 'jet-rel';

	public function base_url() {
		return rest_url( '/' . $this->slug . '/' );
	}

	public function register_routes( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'get'    => true,
			'edit'   => false,
			'rel_id' => false,
		) );

		if ( ! $args['rel_id'] ) {
			return;
		}

		$namespace = $this->slug;
		$base      = $args['rel_id'];

		$raw_routes = array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => $this->prepare_get_args( $base ),
			),
		);

		$ids_routes = array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(),
			),
		);

		if ( $args['edit'] ) {
			$raw_routes[] = array(
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'                => $this->prepare_post_args( $base ),
			);
		}

		register_rest_route( $namespace, '/' . $base, $raw_routes );
		register_rest_route( $namespace, '/' . $base . '/(?P<context>[\w]+)/(?P<_ID>[\d]+)', $ids_routes );

	}

	public function get_rel_from_request( $request ) {

		$route = $request->get_route();
		$parts = explode( '/', ltrim( $route, '/' ) );

		if ( empty( $parts ) || empty( $parts[1] ) ) {
			return false;
		}

		return jet_engine()->relations->get_active_relations( $parts[1] );

	}

	public function prepare_get_args( $type_slug ) {

		$args = array();

		return $args;

	}

	public function prepare_post_args( $type_slug ) {

		$args = array(
			'parent_id' => array(
				'default'  => '',
				'required' => true,
			),
			'child_id' => array(
				'default'  => '',
				'required' => true,
			),
			'context' => array(
				'default' => 'child',
			),
			'store_items_type' => array(
				'default' => 'replace',
			),
			'meta' => array(
				'type' => 'object',
			),
		);

		return $args;

	}

	public function get_items( $request ) {

		$params   = $request->get_params();
		$relation = $this->get_rel_from_request( $request );

		if ( ! $relation ) {
			return new \WP_Error( 'relation_not_fount', __( 'Relation not found', 'jet-engine' ) );
		}

		$meta       = $relation->get_meta_fields();
		$table      = $relation->db->table();
		$meta_table = $relation->meta_db->table();
		$rel_id     = $relation->get_id();
		
		if ( ! $meta ) {
			$data     = array();
			$raw_data = $relation->db->raw_query( "SELECT parent_object_id, child_object_id FROM $table WHERE rel_id ='$rel_id' ORDER BY parent_object_id ASC;" );

			foreach ( $raw_data as $row ) {
				
				if ( empty( $data[ $row->parent_object_id ] ) ) {
					$data[ $row->parent_object_id ] = array();
				}

				if ( empty( $data[ $row->parent_object_id ][ $row->child_object_id ] ) ) {
					$data[ $row->parent_object_id ][ $row->child_object_id ] = array(
						'child_object_id' => $row->child_object_id,
					);
				}

			}

		} else {
			
			$data     = array();
			$raw_data = $relation->db->raw_query( "SELECT r.parent_object_id AS parent_object_id, r.child_object_id AS child_object_id, rm.meta_key AS meta_key, rm.meta_value AS meta_value FROM $table AS r LEFT JOIN $meta_table AS rm ON rm.rel_id = r.rel_id AND rm.parent_object_id = r.parent_object_id AND rm.child_object_id = r.child_object_id WHERE r.rel_id ='$rel_id' ORDER BY r.parent_object_id ASC;" );

			$fields = array();

			foreach ( $meta as $field_data ) {
				$fields[] = $field_data['name'];
			}

			foreach ( $raw_data as $row ) {
				
				if ( empty( $data[ $row->parent_object_id ] ) ) {
					$data[ $row->parent_object_id ] = array();
				}

				if ( empty( $data[ $row->parent_object_id ][ $row->child_object_id ] ) ) {
					$data[ $row->parent_object_id ][ $row->child_object_id ] = array(
						'child_object_id' => $row->child_object_id,
					);
				}

				if ( $row->meta_key && in_array( $row->meta_key, $fields ) ) {

					if ( ! isset( $data[ $row->parent_object_id ][ $row->child_object_id ]['meta'] ) ) {
						$data[ $row->parent_object_id ][ $row->child_object_id ]['meta'] = array();
					}

					$data[ $row->parent_object_id ][ $row->child_object_id ]['meta'][ $row->meta_key ] = $row->meta_value;
				}

			}

		}

		foreach ( $data as $parent_object_id => $children ) {
			$data[ $parent_object_id ] = array_values( $children );
		}

		return new \WP_REST_Response( $data, 200 );

	}

	public function check_user_permissions( $request, $context ) {

		$relation = $this->get_rel_from_request( $request );

		if ( ! $relation ) {
			return false;
		}

		$cap = $relation->get_args( $context );

		if ( ! $cap || 'public' === $cap ) {
			return true;
		} else {
			return current_user_can( $cap );
		}

	}

	public function get_items_permissions_check( $request ) {
		return $this->check_user_permissions( $request, 'rest_get_access' );
	}

	public function get_item( $request ) {

		$relation = $this->get_rel_from_request( $request );

		if ( ! $relation ) {
			return new \WP_Error( 'relation_not_fount', __( 'Relation not found', 'jet-engine' ) );
		}

		$id       = absint( $request->get_param( '_ID' ) );
		$context  = $request->get_param( 'context' );
		$data     = array();
		$raw_data = array();
		$meta     = $relation->get_meta_fields();

		switch ( $context ) {
			case 'children':
				$raw_data   = $relation->get_children( $id, 'ids' );
				$object_key = 'child_object_id';
				break;
			
			case 'parents':
				$raw_data   = $relation->get_parents( $id, 'ids' );
				$object_key = 'parent_object_id';
				break;
		}

		if ( ! empty( $raw_data ) ) {
			foreach ( $raw_data as $rel_item_id ) {

				$data_row[ $object_key ] = $rel_item_id;

				if ( ! empty( $meta ) ) {
					
					switch ( $context ) {
						case 'children':
							$parent_id = $id;
							$child_id  = $rel_item_id;
							break;
						
						case 'parents':
							$parent_id = $rel_item_id;
							$child_id  = $id;
							break;
					}

					if ( ! empty( $meta ) ) {

						$fields = array();

						foreach ( $meta as $field_data ) {
							$fields[] = $field_data['name'];
						}

						$all_meta     = $relation->get_all_meta( $parent_id, $child_id );
						$allowed_meta = array();

						if ( ! empty( $all_meta ) ) {
							foreach ( $all_meta as $meta_key => $meta_value ) {
								if ( in_array( $meta_key, $fields ) ) {
									$allowed_meta[ $meta_key ] = $meta_value;
								}
							}
						}

						if ( ! empty( $allowed_meta ) ) {
							$data_row['meta'] = $allowed_meta;
						}
						
					}

				}

				$data[] = $data_row;
			}
		}

		return new \WP_REST_Response( $data, 200 );

	}

	public function get_item_permissions_check( $request ) {
		return $this->check_user_permissions( $request, 'rest_get_access' );
	}

	public function update_item( $request ) {

		$relation = $this->get_rel_from_request( $request );

		if ( ! $relation ) {
			return new \WP_Error( 'relation_not_fount', __( 'Relation not found', 'jet-engine' ) );
		}

		$res = Forms_Manager::instance()->update_related_items( array(
			'relation'         => $relation->get_id(),
			'parent_id'        => $request->get_param( 'parent_id' ),
			'child_id'         => $request->get_param( 'child_id' ),
			'context'          => $request->get_param( 'context' ),
			'store_items_type' => $request->get_param( 'store_items_type' ),
		) );

		$meta = $request->get_param( 'meta' );

		if ( ! empty( $meta ) ) {
			$relation->update_all_meta( $meta, $request->get_param( 'parent_id' ), $request->get_param( 'child_id' ) );
		}
		

		if ( is_wp_error( $res ) ) {
			return $res;
		} else {
			return new \WP_REST_Response( array( 'success' => true ), 200 );
		}

	}

	public function update_item_permissions_check( $request ) {
		return $this->check_user_permissions( $request, 'rest_post_access' );
	}

}
