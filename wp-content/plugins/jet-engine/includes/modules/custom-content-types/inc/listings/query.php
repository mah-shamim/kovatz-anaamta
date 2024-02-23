<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Listings;

use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Query {

	public $source;
	public $items = array();

	/**
	 * Constructor for the class
	 */
	public function __construct( $source ) {

		$this->source = $source;

		add_filter(
			'jet-engine/listing/grid/query/' . $this->source,
			array( $this, 'query_items' ), 10, 3
		);

		add_filter(
			'jet-engine/listings/data/object-vars',
			array( $this, 'prepare_object_vars' ), 10
		);

		add_action( 'jet-engine/listings/frontend/reset-data', function( $data ) {
			if ( $this->source === $data->get_listing_source() ) {
				wp_reset_postdata();
			}
		} );

		add_filter( 'jet-smart-filters/pre-get-indexed-data', array( $this, 'get_indexed_data' ), 10, 4 );

		

		add_action( 'jet-engine/custom-content-types/after-register-instances', function( $manager ) {

			$post_types = $manager->get_post_types_map();

			if ( empty( $post_types ) ) {
				return;
			}

			add_action(
				'the_post',
				array( $this, 'maybe_add_item_to_post' )
			);

		} );

	}

	public function format_filter_args( $query_args = array() ) {
    
    	$args = array();

		if ( ! empty( $query_args['meta_query'] ) ) {
			
			$result = array();

			foreach ( $query_args['meta_query'] as $row ) {
				$result = $this->add_filter_row( $row, $result );
			}

			$args = $result;

		} elseif ( isset( $query_args['args'] ) ) {
			$args = $query_args['args'];
		}

		return $args;

	}

	/**
	 * Returns indexed data for CCT query
	 *
	 * @return [type] [description]
	 */
	public function get_indexed_data( $data, $provider, $query_args, $indexer ) {

		$props_args    = explode( '/', $provider );
		$provider_name = $props_args[0];
		$provider_id   = $props_args[1];
		$props         = jet_smart_filters()->query->get_query_props( $provider_name, $provider_id );

		if ( empty( $props ) || empty( $props['query_type'] ) || 'custom-content-type' !== $props['query_type'] ) {
			return $data;
		}

		if ( empty( $props['query_meta'] ) || empty( $props['query_meta']['content_type'] ) ) {
			return $data;
		}

		$content_type = Module::instance()->manager->get_content_types( $props['query_meta']['content_type'] );

		if ( ! $content_type ) {
			return $data;
		}

		$indexing_data = $indexer->indexing_data[ $provider ];

		if ( empty( $indexing_data ) || empty( $indexing_data['meta_query'] ) ) {
			return $data;
		}

		$query_id = ! empty( $props['query_id'] ) ? $props['query_id'] : false;

		if ( $query_id ) {
			$query_object = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $query_id );
		} else {
			$query_object = false;
		}

		if ( $query_object ) {
			$query_object->setup_query();
		}

		$result = array(
			'meta_query' => array(),
		);

		if ( ! empty( $query_args ) && ! empty( $query_args[ $provider_name ] ) && ! empty( $query_args[ $provider_name ][ $provider_id ] ) ) {
			// setup front-end query args
			$query_args = $query_args[ $provider_name ][ $provider_id ];
		} elseif ( ! empty( $query_args['meta_query'] ) && $query_object ) {
			// setup AJAX query args
			$query_object->set_filtered_prop( 'meta_query', $query_args['meta_query'] );
			$query_args['args'] = $query_object->final_query['args'];
			unset($query_args['meta_query'] );
		}

		$args  = $content_type->prepare_query_args( $this->format_filter_args( $query_args ) );
		$where = $content_type->db->add_where_args( $args, 'AND', false );
		$table = $content_type->db->table();

		/*

		This is 100% working logic but it much slower on a lot of filters

		foreach ( $indexing_data['meta_query'] as $key => $options ) {
			$query  = "SELECT $key AS $key, COUNT($key) AS items_num FROM $table $where GROUP BY $key";
			$counts = $content_type->db::wpdb()->get_results( $query );
			if ( ! empty( $counts ) ) {
				$result['meta_query'][ $key ] = array();
				foreach ( $counts as $count ) {
					$result['meta_query'][ $key ][ strtolower( $count->$key ) ] = $count->items_num;
				}
			}
		}
		*/

		$selects       = array();
		$groups        = array();
		$range_keys    = array();
		$multiple_keys = array();

		foreach ( $indexing_data['meta_query'] as $key => $options ) {

			$key_data = explode( '|', $key );
			$suffix   = ! empty( $key_data[1] ) ? $key_data[1] : false;
			$key      = $key_data[0];

			if ( false !== strpos( $key, ',' ) ) {

				$multiple_key = array(
					'key' => $key,
				);

				$key  = str_replace( ', ', ',', $key );
				$keys = explode( ',', $key );

				// Store info about filters with multiple keys to process later
				$multiple_key['chunks'] = $keys;
				$multiple_keys[] = $multiple_key;

			} else {
				$keys = array( $key );
			}

			foreach ( $keys as $key ) {

				if ( apply_filters( 'jet-engine/custom-content-types/filters/indexer/skip-key', false, $key ) ) {
					continue;
				}

				switch ( $suffix ) {

					case 'range':

						if ( ! empty( $where ) ) {
							$glue = 'AND';
						} else {
							$glue = '';
						}

						foreach ( $options as $option_key => $option_data ) {

							$range_key = 'range_' . $key . '_' . $option_key;
							$min       = $option_data['min'];
							$max       = $option_data['max'];
							$selects[] = "( SELECT COUNT(*) FROM $table WHERE $where $glue ( $key BETWEEN $min AND $max ) ) AS $range_key";

							$range_keys[ $range_key ] = array(
								'meta_key' => $key,
								'option_key' => $option_key,
							);

						}

						break;

					default:
						$selects[] = "$key AS $key";
						$selects[] = "COUNT( $key ) AS __count_$key";
						$groups[]  = $key;
						break;

				}

				$result['meta_query'][ $key ] = array();

			}

		}

		if ( empty( $selects ) ) {
			return $result;
		}

		$select   = implode( ', ', $selects );
		$group    = implode( ', ', $groups );
		$group_by = '';

		if ( $where ) {
			$where = 'WHERE 1=1 AND ' . $where;
		} else {
			$where = 'WHERE 1=1 ';
		}

		if ( $group ) {
			$group_by = "GROUP BY $group";
		}

		if ( $query_object ) {
			$query_object->setup_query();
			$content_type->db->set_query_object( $query_object );
		}

		$sql_query = apply_filters( 'jet-engine/custom-content-types/sql-query-parts', array(
			'select' => "SELECT $select",
			'from'   => "FROM $table",
			'where'  => $where,
			'group'  => $group_by,
		), $table, $args, $content_type->db );

		$sql_query = implode( ' ', $sql_query );
		$counts    = $content_type->db::wpdb()->get_results( $sql_query, ARRAY_A );

		if ( ! empty( $counts ) ) {

			/**
			 * Calculate counts for rangle filters
			 */
			if ( ! empty( $range_keys ) ) {
				foreach ( $range_keys as $key => $key_data ) {

					if ( empty( $result['meta_query'][ $key_data['meta_key'] ] ) ) {
						$result['meta_query'][ $key_data['meta_key'] ] = array();
					}

					if ( isset( $counts[0][ $key ] ) ) {
						$result['meta_query'][ $key_data['meta_key'] ][ $key_data['option_key'] ] = $counts[0][ $key ];
					}

				}
			}

			foreach ( $counts as $chunk ) {
				foreach ( $chunk as $key => $value ) {
					if ( isset( $result['meta_query'][ $key ] ) ) {

						if ( ! isset( $result['meta_query'][ $key ][ strtolower( $value ) ] ) ) {
							$result['meta_query'][ $key ][ strtolower( $value ) ] = 0;
						}

						$result['meta_query'][ $key ][ strtolower( $value ) ] += $chunk[ '__count_' . $key ];

					}
				}
			}
		}

		/**
		 * Calculate counts for filters with multiple keys
		 */
		if ( ! empty( $multiple_keys ) ) {
			foreach ( $multiple_keys as $multiple_key ) {

				$result['meta_query'][ $multiple_key['key'] ] = array();

				foreach ( $multiple_key['chunks'] as $chunk ) {
					if ( isset( $result['meta_query'][ $chunk ] ) ) {
						foreach ( $result['meta_query'][ $chunk ] as $option => $count ) {

							if ( ! isset( $result['meta_query'][ $multiple_key['key'] ][ $option ] ) ) {
								$result['meta_query'][ $multiple_key['key'] ][ $option ] = 0;
							}

							$result['meta_query'][ $multiple_key['key'] ][ $option ] += $count;

						}
					}
				}

			}
		}

		return $result;

	}

	/**
	 * Returns currentlyu request CCT item for given post ID
	 *
	 * @param  [type] $post_id   [description]
	 * @param  [type] $post_type [description]
	 * @return [type]            [description]
	 */
	public function get_current_item( $post_id = null, $post_type = null ) {

		if ( ! isset( $this->items[ $post_id ] ) ) {

			$content_type = Module::instance()->manager->get_content_type_for_post_type( $post_type );

			if ( ! $content_type ) {
				return false;
			}

			$slug = $content_type->get_arg( 'slug' );
			$item = Module::instance()->manager->get_item_for_post( $post_id, $content_type );

			if ( ! $item ) {
				return false;
			}

			$prepared_item = array();

			foreach ( $item as $key => $value ) {

				if ( 'cct_slug' !== $key ) {
					$prop = $slug . '__' . $key;
				} else {
					$prop = $key;
				}

				$prepared_item[ $prop ] = $value;
			}

			$this->items[ $post_id ] = $prepared_item;

		}

		return $this->items[ $post_id ];
	}

	/**
	 * Prepare CCT variables inside current listing object
	 */
	public function prepare_object_vars( $vars ) {

		if ( isset( $vars['cct_slug'] ) ) {

			$new_vars = array();

			foreach ( $vars as $key => $value ) {
				$new_vars[ $vars['cct_slug'] . '__' . $key ] = $value;
			}

			$vars = array_merge( $vars, $new_vars );

		} elseif ( ! empty( $vars['ID'] ) && ! empty( $vars['post_type'] ) ) {

			$post_id   = $vars['ID'];
			$post_type = $vars['post_type'];
			$item      = $this->get_current_item( $post_id, $post_type );

			if ( ! $item ) {
				return $vars;
			}

			$vars = array_merge( $vars, $item );

		}

		return $vars;

	}

	/**
	 * Attach CCT item fields for related post object
	 *
	 * @param  [type] &$post [description]
	 * @return [type]        [description]
	 */
	public function maybe_add_item_to_post( $post ) {

		$post_id   = $post->ID;
		$post_type = $post->post_type;
		$item      = $this->get_current_item( $post_id, $post_type );

		if ( ! $item ) {
			return $post;
		}

		foreach ( $item as $prop => $value ) {
			$post->$prop = $value;
		}

		return $post;

	}

	/**
	 * Check if JSF reuest is currentlly processed
	 *
	 * @return boolean [description]
	 */
	public function is_filters_request() {

		if ( ! empty( $_REQUEST['action'] ) && 'jet_smart_filters' === $_REQUEST['action'] ) {
			return true;
		}

		if ( ! empty( $_REQUEST['jsf'] ) && false !== strpos( $_REQUEST['jsf'], 'jet-engine' ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Query CCT items by given arguments
	 *
	 * @param  [type] $query    [description]
	 * @param  [type] $settings [description]
	 * @param  [type] $widget   [description]
	 * @return [type]           [description]
	 */
	public function query_items( $query, $settings, $widget ) {

		$widget->query_vars['page']    = 1;
		$widget->query_vars['pages']   = 1;
		$widget->query_vars['request'] = false;

		$type = jet_engine()->listings->data->get_listing_post_type();

		if ( ! $type ) {
			return $query;
		}

		$content_type = Module::instance()->manager->get_content_types( $type );

		if ( ! $content_type ) {
			return $query;
		}

		$page  = 1;
		$query = isset( $settings['jet_cct_query'] ) ? $settings['jet_cct_query'] : '{}';
		$query = json_decode( wp_unslash( $query ), true );

		if ( ! empty( $_REQUEST['action'] ) && 'jet_engine_ajax' === $_REQUEST['action'] && isset( $_REQUEST['query'] ) ) {
			$query = $_REQUEST['query'];
			$page  = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
		}

		if ( $this->is_filters_request() ) {
			if ( ! empty( $_REQUEST['pagenum'] ) ) {
				$page = absint( $_REQUEST['pagenum'] );
			} else {
				$page = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
			}
		}

		$order  = ! empty( $query['order'] ) ? $query['order'] : array();
		$args   = ! empty( $query['args'] ) ? $query['args'] : array();
		$offset = ! empty( $query['offset'] ) ? absint( $query['offset'] ) : 0;
		$status = ! empty( $query['status'] ) ? $query['status'] : '';
		$limit  = $widget->get_posts_num( $settings );

		$flag = \OBJECT;
		$content_type->db->set_format_flag( $flag );

		$filtered_query = apply_filters(
			'jet-engine/listing/grid/posts-query-args',
			array(),
			$widget,
			$settings
		);

		$args = $this->do_macros_in_args( $args );

		if ( $status ) {
			$args[] = array(
				'field'    => 'cct_status',
				'operator' => '=',
				'value'    => $status,
			);
		}

		if ( ! empty( $filtered_query['jet_smart_filters'] ) && ! empty( $filtered_query['meta_query'] ) ) {
			foreach ( $filtered_query['meta_query'] as $row ) {
				$args = $this->add_filter_row( $row, $args );
			}
		}

		if ( ! empty( $filtered_query['jet_smart_filters'] )
			&& ! empty( $filtered_query['orderby'] )
			&& ! empty( $filtered_query['meta_key'] ) ) {

			if ( 'meta_value_num' === $filtered_query['orderby'] ) {
				$type = 'float';
			} else {
				$type = 'string';
			}

			$order = array(
				array(
					'order'   => ! empty( $filtered_query['order'] ) ? $filtered_query['order'] : 'asc',
					'orderby' => $filtered_query['meta_key'],
					'type'    => $type,
				),
			);
		}

		$query_args = apply_filters(
			'jet-engine/custom-content-types/listing/query-args',
			$content_type->prepare_query_args( $args ),
			$settings
		);

		if ( false === $query_args ) {
			return array();
		}

		if ( 0 < $limit ) {
			$total = $content_type->db->count( $query_args );
			$widget->query_vars['pages'] = ceil( $total / $limit );

			if ( function_exists( 'jet_smart_filters' ) ) {

				$query_id = ! empty( $settings['_element_id'] ) ? $settings['_element_id'] : false;

				jet_smart_filters()->query->set_props(
					'jet-engine',
					array(
						'found_posts'   => $total,
						'max_num_pages' => $widget->query_vars['pages'],
						'page'          => $page,
						'query_type'    => 'custom-content-type',
						'query_meta'    => array(
							'content_type' => $type,
						),
					),
					$query_id
				);

			}
		}

		$widget->query_vars['request'] = array(
			'order'  => $order,
			'args'   => $query_args,
			'offset' => $offset,
		);

		if ( 1 < $page ) {
			$offset = $offset + ( $page - 1 ) * $limit;
		}

		return $content_type->db->query( $query_args, $limit, $offset, $order );

	}

	/**
	 * Do macros in CCT query arguments
	 *
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function do_macros_in_args( $args = array() ) {

		$prepared_args = array();

		foreach ( $args as $arg ) {

			if ( ! empty( $arg['value'] ) ) {
				$arg['value'] = jet_engine()->listings->macros->do_macros( $arg['value'] );
			}

			$prepared_args[] = $arg;
		}

		return $prepared_args;
	}

	/**
	 * Add filter request arguments into CCT query
	 *
	 * @param [type] $row   [description]
	 * @param [type] $query [description]
	 */
	public function add_filter_row( $row, $query ) {

		if ( ! empty( $row['relation'] ) ) {

			$query[] = $this->prepare_multi_relation_row( $row );

		} else {

			$row['field']    = $row['key'];
			$row['operator'] = $row['compare'];
			$found           = false;

			if ( '_cct_search' === $row['field'] || 'cct_search' === $row['field'] ) {

				$query['_cct_search'] = array(
					'keyword' => $row['value'],
				);

				return $query;

			}

			unset( $row['key'] );
			unset( $row['compare'] );

			foreach ( $query as $index => $query_row ) {
				if ( $row['field'] === $query_row['field'] ) {
					$query[ $index ] = $row;
					$found = true;
				}
			}

			if ( ! $found ) {
				$query[] = $row;
			}
		}

		return $query;

	}

	public function prepare_multi_relation_row( $row ) {

		if ( ! empty( $row['relation'] ) ) {

			$new_row = array(
				'relation' => $row['relation'],
			);

			unset( $row['relation'] );

			foreach ( $row as $inner_row ) {
				$new_row[] = $this->prepare_multi_relation_row( $inner_row );
			}

		} else {
			$new_row = array(
				'field'    => ! empty( $row['key'] ) ? $row['key'] : false,
				'operator' => ! empty( $row['compare'] ) ? $row['compare'] : '=',
				'value'    => ! empty( $row['value'] ) ? $row['value'] : '',
				'type'     => ! empty( $row['type'] ) ? $row['type'] : false,
			);
		}

		return $new_row;
	}

}
