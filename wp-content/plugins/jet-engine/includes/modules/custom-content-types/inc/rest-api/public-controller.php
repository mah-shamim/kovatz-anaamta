<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Rest;

use Jet_Engine\Modules\Custom_Content_Types\Module;

class Public_Controller {

	public $slug = 'jet-cct';

	public function base_url() {
		return rest_url( '/' . $this->slug . '/' );
	}

	public function register_routes( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'get'    => true,
			'create' => false,
			'edit'   => false,
			'delete' => false,
			'slug'   => false,
		) );

		if ( ! $args['slug'] ) {
			return;
		}

		$namespace = $this->slug;
		$base      = $args['slug'];

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

		if ( $args['create'] ) {
			$raw_routes[] = array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'                => $this->prepare_post_args( $base ),
			);
		}

		if ( $args['edit'] ) {
			$ids_routes[] = array(
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'                => $this->prepare_post_args( $base ),
			);
		}

		if ( $args['delete'] ) {
			$ids_routes[] = array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'                => array(),
			);
		}

		register_rest_route( $namespace, '/' . $base, $raw_routes );
		register_rest_route( $namespace, '/' . $base . '/(?P<_ID>[\d]+)', $ids_routes );

	}

	public function get_content_type_from_request( $request ) {

		$route = $request->get_route();
		$parts = explode( '/', ltrim( $route, '/' ) );

		if ( empty( $parts ) || empty( $parts[1] ) ) {
			return false;
		}

		return Module::instance()->manager->get_content_types( $parts[1] );

	}

	public function prepare_get_args( $type_slug ) {

		$content_type = Module::instance()->manager->get_content_types( $type_slug );
		$fields       = $content_type->get_formatted_fields();

		foreach ( $fields as $field ) {
			$args[ $field['name'] ] = array(
				'type'        => 'string',
				'description' => $field['title'],
			);
		}

		foreach ( $this->get_common_args() as $key => $data ) {
			$args[ $key ] = $data;
		}

		return $args;

	}

	public function get_common_args() {

		$args = array();

		$args['_cct_search'] = array(
			'type'        => 'string',
			'description' => __( 'Search string', 'jet-engine' ),
		);

		$args['_cct_search_by'] = array(
			'type'        => 'string',
			'description' => __( 'Comma separated fields names list to search only by this fields', 'jet-engine' ),
		);

		$args['_limit'] = array(
			'type'        => 'number',
			'description' => __( 'Items limit', 'jet-engine' ),
		);

		$args['_offset'] = array(
			'type'        => 'number',
			'description' => __( 'Items offset', 'jet-engine' ),
		);

		$args['_orderby'] = array(
			'type'        => 'string',
			'description' => __( 'Order items by field', 'jet-engine' ),
		);

		$args['_order'] = array(
			'type'        => 'string',
			'description' => __( 'Order - asc or desc', 'jet-engine' ),
		);

		$args['_ordertype'] = array(
			'type'        => 'string',
			'description' => __( 'Order value type - integer, float, timestamp, date, string', 'jet-engine' ),
		);

		$args['_filters'] = array(
			'type'        => 'string',
			'description' => __( 'JSON-encoded filters string', 'jet-engine' ),
		);

		return $args;

	}

	public function prepare_post_args( $type_slug ) {

		$content_type = Module::instance()->manager->get_content_types( $type_slug );
		$fields = $content_type->get_formatted_fields();

		foreach ( $fields as $field ) {

			$type = 'string';

			if ( in_array( $field['type'], array( 'checkbox', 'repeater' ) ) ) {
				$type = 'array';
			}

			if ( in_array( $field['type'], array( 'select', 'posts' ) ) && ! empty( $field['is_multiple'] )
				 && filter_var( $field['is_multiple'], FILTER_VALIDATE_BOOLEAN )
			) {
				$type = 'array';
			}

			$args[ $field['name'] ] = array(
				'type'        => $type,
				'description' => $field['title'],
			);
		}

		return $args;

	}

	public function get_items( $request ) {

		$content_type = $this->get_content_type_from_request( $request );

		if ( ! $content_type ) {
			return new \WP_Error( 'content_type_not_fount', __( 'Content type not found', 'jet-engine' ) );
		}

		$params = $request->get_params();
		$limit  = 0;
		$offset = 0;
		$order  = array();
		$fields = $content_type->get_formatted_fields();
		$query  = array();

		if ( ! empty( $params['_filters'] ) ) {

			$query = json_decode( $params['_filters'], true );
			if ( isset( $query['args'] ) ) {
				$query = $query['args'];
			}

			unset( $params['_filters'] );
		}

		if ( ! empty( $params['_limit'] ) ) {
			$limit = absint( $params['_limit'] );
			unset( $params['_limit'] );
		}

		if ( ! empty( $params['_offset'] ) ) {
			$offset = absint( $params['_offset'] );
			unset( $params['_offset'] );
		}

		if ( ! empty( $params['_orderby'] ) && isset( $fields[ $params['_orderby'] ] ) ) {

			$order_clause = array();
			$order_clause['orderby'] = $params['_orderby'];

			unset( $params['_orderby'] );

			if ( ! empty( $params['_order'] ) ) {
				$order_clause['order'] = $params['_order'];
				unset( $params['_order'] );
			}

			if ( ! empty( $params['_ordertype'] ) ) {
				$order_clause['type'] = $params['_ordertype'];
				unset( $params['_ordertype'] );
			}

			$order[] = $order_clause;

		}

		$search    = false;
		$search_by = false;

		if ( ! empty( $params['_cct_search'] ) ) {
			$search = $params['_cct_search'];
			unset( $params['_cct_search'] );
		}

		if ( ! empty( $params['_cct_search_by'] ) ) {
			$search_by = explode( ',', str_replace( ' ', '', $params['_cct_search_by'] ) );
			unset( $params['_cct_search_by'] );
		}

		if ( ! empty( $params ) ) {

			foreach ( $params as $key => $value ) {
				if ( isset( $fields[ $key ] ) ) {
					$query[] = array(
						'field' => $key,
						'value' => $value,
					);
				}
			}

		}

		if ( $search ) {
			$search_data = array( 'keyword' => $search );

			if ( ! empty( $search_by ) ) {
				$search_data['fields'] = $search_by;
			}

			$query['_cct_search'] = $search_data;
		}

		$query = apply_filters( 
			'jet-engine/custom-content-types/rest-api/' . $content_type->get_arg( 'slug' ) . '/get-items/query',
			$query,
			$content_type,
			$request,
			$this
		);

		$limit = apply_filters( 
			'jet-engine/custom-content-types/rest-api/' . $content_type->get_arg( 'slug' ) . '/get-items/limit',
			$limit,
			$content_type,
			$request,
			$this
		);

		$offset = apply_filters( 
			'jet-engine/custom-content-types/rest-api/' . $content_type->get_arg( 'slug' ) . '/get-items/offset',
			$offset,
			$content_type,
			$request,
			$this
		);

		$order = apply_filters( 
			'jet-engine/custom-content-types/rest-api/' . $content_type->get_arg( 'slug' ) . '/get-items/order',
			$order,
			$content_type,
			$request,
			$this
		);

		$data = $content_type->db->query( $query, $limit, $offset, $order );
		$data = $this->filter_data( $data, $content_type, false );

		return new \WP_REST_Response( $data, 200 );

	}

	public function filter_data( $data, $content_type, $single ) {

		if ( empty( $data ) ) {
			return $data;
		}

		/**
		 * Allow to add filtering callback function for the Rest API response fields
		 * example here - https://gist.github.com/MjHead/f3e883c19cd0f754761719b5101a1f62
		 */
		$filters = apply_filters( 'jet-engine/custom-content-types/rest-api/filters/' . $content_type->get_arg( 'slug' ), array(), $content_type, $this );

		if ( $single ) {
			$data = array( $data );
		}

		if ( ! empty( $filters ) ) {

			$data = array_map( function( $item ) use ( $filters ) {

				foreach ( $filters as $field => $callback ) {

					if ( ! is_callable( $callback ) ) {
						continue;
					}

					if ( isset( $item[ $field ] ) ) {
						$item[ $field ] = call_user_func( $callback, $item[ $field ] );
					} else {
						$item[ $field ] = call_user_func( $callback, $item );
					}

				}

				return $item;

			}, $data );

		}

		if ( $single ) {
			return $data[0];
		} else {
			return $data;
		}
	}

	public function check_user_permissions( $request, $context ) {

		$content_type = $this->get_content_type_from_request( $request );

		if ( ! $content_type ) {
			return false;
		}

		$cap = $content_type->get_arg( $context );

		if ( ! $cap || 'public' === $cap ) {
			return true;
		} else {
			return current_user_can( $cap );
		}

	}

	public function get_items_permissions_check( $request ) {
		return $this->check_user_permissions( $request, 'rest_get_access' );
	}

	public function create_item( $request ) {

		$content_type = $this->get_content_type_from_request( $request );

		if ( ! $content_type ) {
			return new \WP_Error( 'content_type_not_fount', __( 'Content type not found', 'jet-engine' ) );
		}

		$params = $request->get_params();

		if ( ! empty( $params['_ID'] ) ) {
			return new \WP_Error( 'incorrect_request_params', __( 'You can\'t send item IDs with create Rest API endpoint. Please use Upate endpoint to update existing data', 'jet-engine' ) );
		}

		$handler = $content_type->get_item_handler();
		$item_id = $handler->update_item( $params );

		if ( ! $item_id ) {
			return new \WP_Error( 'cant_create_item', __( 'We can not create new content type item', 'jet-engine' ) );
		} else {
			return new \WP_REST_Response(
				array(
					'success' => true,
					'item_id' => $item_id,
				),
				200
			);
		}

	}

	public function create_item_permissions_check( $request ) {
		return $this->check_user_permissions( $request, 'rest_put_access' );
	}

	public function get_item( $request ) {

		$content_type = $this->get_content_type_from_request( $request );

		if ( ! $content_type ) {
			return new \WP_Error( 'content_type_not_fount', __( 'Content type not found', 'jet-engine' ) );
		}

		$id   = $request->get_param( '_ID' );
		$data = $content_type->db->get_item( $id );
		$data = $this->filter_data( $data, $content_type, true );

		return new \WP_REST_Response( $data, 200 );

	}

	public function get_item_permissions_check( $request ) {
		return $this->check_user_permissions( $request, 'rest_get_access' );
	}

	public function update_item( $request ) {

		$content_type = $this->get_content_type_from_request( $request );

		if ( ! $content_type ) {
			return new \WP_Error( 'content_type_not_fount', __( 'Content type not found', 'jet-engine' ) );
		}

		$handler = $content_type->get_item_handler();
		$item_id = $handler->update_item( $request->get_params() );

		if ( ! $item_id ) {
			return new \WP_Error( 'cant_update_item', __( 'We can not update content type item', 'jet-engine' ) );
		} else {
			return new \WP_REST_Response(
				array(
					'success' => true,
					'item_id' => $item_id,
				),
				200
			);
		}

	}

	public function update_item_permissions_check( $request ) {
		return $this->check_user_permissions( $request, 'rest_post_access' );
	}

	public function delete_item( $request ) {

		$content_type = $this->get_content_type_from_request( $request );

		if ( ! $content_type ) {
			return new \WP_Error( 'content_type_not_fount', __( 'Content type not found', 'jet-engine' ) );
		}

		$id      = $request->get_param( '_ID' );
		$handler = $content_type->get_item_handler();

		$handler->delete_item( $id );

		return new \WP_REST_Response( array( 'success' => true ), 200 );

	}

	public function delete_item_permissions_check( $request ) {
		return $this->check_user_permissions( $request, 'rest_delete_access' );
	}

}
