<?php
class Jet_Search_Rest_Get_Suggestions extends Jet_Search_Rest_Base_Route {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-suggestions';
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELETE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'GET';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		jet_search()->db->create_all_tables();

		$params = $request->get_params();

		$offset         = ! empty( $params['offset'] ) ? absint( $params['offset'] ) : 0;
		$per_page       = ! empty( $params['per_page'] ) ? absint( $params['per_page'] ) : 30;
		$sort           = ! empty( $params['sort'] ) ? json_decode( $params['sort'], true ) : array();
		$search_parent  = ! empty( $params['query'] ) ? trim( $params['query'] ) : '';
		$filter         = ! empty( $params['filter'] ) ? json_decode( $params['filter'], true ) : '';
		$ids            = ! empty( $params['ids'] ) ? $params['ids'] : '';
		$action         = ! empty( $params['action'] ) ? $params['action'] : '';
		$result         = array();

		if ( 'get_form_suggestions' === $action ) {
			$result = $this->get_form_suggestions_list( $params );
			return rest_ensure_response( $result );
		}

		global $wpdb;

		$prefix     = 'jet_';
		$table_name = $wpdb->prefix . $prefix . 'search_suggestions';
		$query      = "SELECT s1.*, MAX(s2.id) AS child FROM {$table_name} AS s1 LEFT JOIN {$table_name} AS s2 ON s1.id = s2.parent";

		if ( '' != $filter && ( isset ( $filter['search'] ) || isset( $filter['searchType'] ) ) ) {
			$result = $this->get_filtered_items( $query, $params );

			return rest_ensure_response( $result );
		}

		if ( '' != $search_parent || '' != $ids ) {
			$result = $this->get_options_list( $query, $search_parent, $ids );

			return rest_ensure_response( $result );
		}

		if ( ! empty( $sort ) && empty( $filter ) ) {
			$orderby = $sort['orderby'];
			$order   = ! empty( $sort['order'] ) ? $sort['order'] : 'desc';
			$order   = strtoupper( $order );
			$query  .= " GROUP BY s1.id";
			$query  .= " ORDER BY {$orderby} {$order}";
		} else {
			$query .= " GROUP BY s1.id";
		}

		$query       .= " LIMIT {$per_page} OFFSET {$offset}";
		$suggestions  = $wpdb->get_results( $query, ARRAY_A );
		$count        = jet_search()->db->count( 'search_suggestions' );
		$on_page      = count( $suggestions );
		$parents_list = array();
		$parents_ids  = array();

		if ( $suggestions ) {
			foreach ( $suggestions as $item ) {
				$parent = $item['parent'];
				if ( ! empty( $parent ) ) {
					$parents_ids[] = $parent;
				}
			}

			$parents_ids = array_unique( $parents_ids );

			foreach ( $suggestions as $item ) {
				if ( in_array( $item['id'], $parents_ids, true ) ) {
					$parents_list[] = array(
						'value' => (string) $item['id'],
						'label' => $item['name'],
					);
				}
			}

			$result = array(
				"success"      => true,
				"items_list"   => $suggestions,
				"parents_list" => $parents_list,
				"total"        => (int)$count,
				"on_page"      => $on_page
			);
		} else {
			$result = array(
				"success"      => false,
				"items_list"   => array(),
				"parents_list" => array(),
				"total"        => 0,
				"on_page"      => 0
			);
		}

		return rest_ensure_response( $result );
	}

	public function get_form_suggestions_list( $params ) {
		$list_type   = ! empty( $params['data']['list_type'] ) ? $params['data']['list_type'] : 'popular';
		$limit       = ! empty( $params['data']['limit'] ) ? $params['data']['limit'] : 5;
		$value       = ! empty( $params['data']['value'] ) ? $params['data']['value'] : '';
		$parents_ids = array();
		$suggestions = array();

		global $wpdb;

		$prefix      = 'jet_';
		$table_name  = $wpdb->prefix . $prefix . 'search_suggestions';
		$query       = "SELECT * FROM {$table_name}";

		if ( '' != $value ) {
			$query       .= " WHERE ( name LIKE '%{$value}%' AND parent = 0 )";
			$query       .= " ORDER BY WEIGHT DESC";
			$query       .= " LIMIT {$limit} OFFSET 0";
		} else {
			$query       .= " WHERE parent = 0";
			if ( 'latest' === $list_type ) {
				$query .= " ORDER BY ID DESC";
			} else if ( 'popular' === $list_type ) {
				$query .= " ORDER BY WEIGHT DESC";
			}

			$query .= " LIMIT {$limit} OFFSET 0";
		}

		$suggestions = $wpdb->get_results( $query, ARRAY_A );

		return $suggestions;
	}

	/**
	 * Returns a list of options by IDs or a list of options found by the given name
	 *
	 * @return array
	 */
	public function get_options_list( $query, $search, $ids ) {
		if ( '' != $search ) {
			global $wpdb;

			$result      = array();
			$search_rel  = ' WHERE';
			$query      .= "{$search_rel} s1.name LIKE '%{$search}%' AND s1.parent = 0";
			$query      .= " GROUP BY s1.id";

			$suggestions = $wpdb->get_results( $query, ARRAY_A );

			if ( $suggestions ) {
				foreach ( $suggestions as $suggestion ) {
					$result[] = array(
						'value' => (string) $suggestion['id'],
						'label' => $suggestion['name'],
					);
				}
			}

			return rest_ensure_response( $result );
		} else if ( ! empty( $ids ) ) {
			$parents_list = $this->get_parents_options_list( $ids );

			return rest_ensure_response( $parents_list );
		}
	}

	/**
	 * Returns filtered by name items
	 *
	 * @return array
	 */
	public function get_filtered_items( $query, $params ) {
		$offset      = ! empty( $params['offset'] ) ? absint( $params['offset'] ) : 0;
		$per_page    = ! empty( $params['per_page'] ) ? absint( $params['per_page'] ) : 30;
		$sort        = ! empty( $params['sort'] ) ? json_decode( $params['sort'], true ) : array();
		$filter      = json_decode( $params['filter'], true );
		$filter_name = ! empty( $filter['search'] ) ? $filter['search'] : '';
		$filter_type = ! empty( $filter['searchType'] ) ? $filter['searchType'] : '';
		$type_query  = '';

		global $wpdb;

		$search_rel  = ' WHERE';

		if ( '' != $filter_type ) {
			switch ($filter_type) {
				case 'parent':
					$type_query = "s2.id IS NOT NULL";
					break;
				case 'child':
					$type_query = "s1.parent != 0";
					break;
				case 'unassigned':
					$type_query = "( s1.parent = 0 AND s2.id IS NULL )";
					break;
			}
		}

		if ( '' != $filter_name && '' != $filter_type ) {

			$query .= "{$search_rel} ( s1.name LIKE '%{$filter['search']}%' AND {$type_query} )";

		} else if ( '' != $filter_name ) {

			$query .= "{$search_rel} ( s1.name LIKE '%{$filter['search']}%' )";

		} else if ( '' != $filter_type ) {

			$query .= "{$search_rel} {$type_query}";
		}

		if ( ! empty( $sort ) ) {
			$orderby = $sort['orderby'];
			$order   = ! empty( $sort['order'] ) ? $sort['order'] : 'desc';
			$order   = strtoupper( $order );
			$query  .= " GROUP BY s1.id";
			$query  .= " ORDER BY {$orderby} {$order}";
		} else {
			$query .= " GROUP BY s1.id";
		}

		$count_query = $query;

		$query       .= " LIMIT {$per_page} OFFSET {$offset}";
		$suggestions  = $wpdb->get_results( $query, ARRAY_A );
		$count        = count( $wpdb->get_results( $count_query, ARRAY_A ) );
		$on_page      = count( $suggestions );
		$parents_list = array();
		$parents_ids  = array();

		if ( $suggestions ) {
			foreach ( $suggestions as $item ) {
				$parent = $item['parent'];
				if ( ! empty( $parent ) ) {
					$parents_ids[] = $parent[0];
				}
			}

			$parents_ids = array_unique( $parents_ids );

			foreach ( $suggestions as $item ) {
				if ( in_array( $item['id'], $parents_ids, true ) ) {
					$parents_list[] = array(
						'value' => (string) $item['id'],
						'label' => $item['name'],
					);
				}
			}

			$result = array(
				"success"      => true,
				"items_list"   => $suggestions,
				"parents_list" => $parents_list,
				"total"        => (int)$count,
				"on_page"      => $on_page
			);
		} else {
			$result = array(
				"success"      => false,
				"items_list"   => array(),
				"parents_list" => array(),
				"total"        => 0,
				"on_page"      => 0
			);
		}

		return $result;
	}

	/**
	 * Returns parents list by ids
	 *
	 * @return array
	 */
	public function get_parents_options_list( $ids ) {
		$parents_list = array();

		global $wpdb;

		$prefix        = 'jet_';
		$table_name    = $wpdb->prefix . $prefix . 'search_suggestions';
		$parents_query = "SELECT * FROM {$table_name} WHERE id IN (" . $ids . ")";
		$suggestions   = $wpdb->get_results( $parents_query, ARRAY_A );

		foreach ( $suggestions as $item ) {
			$parents_list[] = array(
				'value' => (string) $item['id'],
				'label' => $item['name'],
			);
		}

		return $parents_list;
	}

	/**
	 * Returns unserialized item parents
	 *
	 * @return array
	 */
	public function parents_unserialize( $item ) {
		if( NULL === $item['parent'] ) {
			$item['parent'] = NULL;
		}

		$item['parent'] = maybe_unserialize( $item['parent'] );
		return $item;
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		if ( 'get_form_suggestions' === $request['action'] ) {
			return true;
		}

		return current_user_can( 'manage_options' );
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array(
			'offset' => array(
				'default'  => 0,
				'required' => false,
			),
			'per_page' => array(
				'default'  => 30,
				'required' => false,
			),
			'sort' => array(
				'default'  => array(),
				'required' => false,
			),
		);
	}

}
