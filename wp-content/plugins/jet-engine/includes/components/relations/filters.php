<?php
namespace Jet_Engine\Relations;

/**
 * JetSmartFilters compatibility class
 */
class Filters {

	public function __construct() {

		add_filter( 'jet-smart-filters/query/final-query', array( $this, 'add_relation_query' ), -10 );

		// Indexer common
		add_filter( 'jet-smart-filters/indexer/get-post-meta', array( $this, 'index_posts_relations' ), 10, 2 );
		add_filter( 'jet-smart-filters/indexer/get-user-meta', array( $this, 'index_users_relations' ), 10, 2 );
		add_filter( 'jet-smart-filters/indexer/single-item-data', array( $this, 'index_single_item' ), 10, 3 );

		// CCT + Indexer
		add_filter( 'jet-engine/custom-content-types/filters/indexer/skip-key', array( $this, 'prevent_default_indexing_cct_relations' ), 10, 2 );
		add_filter( 'jet-smart-filters/pre-get-indexed-data', array( $this, 'index_cct_relations' ), 11, 4 );

		// JSF before version 3.0.0
		add_action( 'jet-smart-filters/post-type/filter-notes-after', array( $this, 'helper_notes' ) );

		// after JSF version 3.0.0
		add_action( 'jet-smart-filters/admin/register-dynamic-query', array( $this, 'helper_dynamic_query' ) );

	}

	/**
	 * Printe helper notes for relations on filters edit page
	 *
	 * @return [type] [description]
	 */
	public function helper_notes() {
		?>
		<p><b><?php _e( 'JetEngine Relations:', 'jet-engine' ); ?></b></p>
		<ul>
		<?php
		foreach ( jet_engine()->relations->get_active_relations() as $relation ) {

			printf(
				'<li><b>related_children*%1$s</b>: %2$s: %3$s</li>',
				$relation->get_id(),
				$relation->get_relation_name(),
				__( 'filters children items list by parents IDs', 'jet-engine' )
			);

			printf(
				'<li><b>related_parents*%1$s</b>: %2$s: %3$s</li>',
				$relation->get_id(),
				$relation->get_relation_name(),
				__( 'filters parents items list by children IDs', 'jet-engine' )
			);

		}
		?>
		</ul>
		<?php
	}

	/**
	 * Admin dynamic query for JSF query variable
	 */
	public function helper_dynamic_query( $dynamic_query_manager ) {

		$relations = jet_engine()->relations->get_active_relations();

		if ( ! $relations ) {
			return;
		}

		$relations_list = array(
			'related_children' => __( 'filters children items list by parents IDs', 'jet-engine' ),
			'related_parents'  => __( 'filters parents items list by children IDs', 'jet-engine' )
		);

		$relations_options = array();
		foreach ( $relations as $relation_item ) {
			$relations_options[$relation_item->get_id()] = $relation_item->get_relation_name();
		}
		
		foreach ( $relations_list as $relation_key => $relation_label ) {
			$relation_dynamic_query_item = new class( $relation_key, $relation_label, $relations_options ) {
				public function __construct( $key, $label, $options ) {
					$this->key     = $key;
					$this->label   = $label;
					$this->options = $options;
				}

				public function get_name() {
					return $this->key;
				}

				public function get_label() {
					return 'JetEngine: ' . $this->label;
				}

				public function get_extra_args() {
					return array(
						'relation' => array(
							'type'        => 'select',
							'title'       => __( 'Relation', 'jet-engine' ),
							'placeholder' => __( 'Select relation...', 'jet-engine' ),
							'options'     => $this->options,
						),
					);
				}

				public function get_delimiter() {
					return '*';
				}
			};

			$dynamic_query_manager->register_item( $relation_dynamic_query_item );
		}
	}

	/**
	 * Check if relations was requested for indexing - tries to get posts for relations
	 *
	 * @param  [type] $result   [description]
	 * @param  [type] $metadata [description]
	 * @return [type]           [description]
	 */
	public function index_posts_relations( $result, $metadata ) {
		return array_merge( $result, $this->get_related_meta( $metadata, 'post' ) );
	}

	/**
	 * Check if relations was requested for indexing - tries to get users for relations
	 *
	 * @param  [type] $result   [description]
	 * @param  [type] $metadata [description]
	 * @return [type]           [description]
	 */
	public function index_users_relations( $result, $metadata ) {
		return array_merge( $result, $this->get_related_meta( $metadata, 'user' ) );
	}

	/**
	 * Re-index relations indexer data on each update of post or user
	 *
	 * @param  [type] $result      [description]
	 * @param  [type] $filter_data [description]
	 * @return [type]              [description]
	 */
	public function index_single_item( $result, $filters_data, $type ) {

		if ( empty( $filters_data['meta_query'] ) ) {
			return $result;
		}

		return array_merge( $result, $this->get_related_meta( $filters_data['meta_query'], $type, true ) );
	}

	/**
	 * Check if relation object mets indexer type requirements
	 *
	 * @return boolean [description]
	 */
	public function is_supported_type( $type, $object ) {

		switch ( $type ) {
			case 'post':
				return jet_engine()->relations->types_helper->object_is( $object, 'posts' );

			case 'user':
				return jet_engine()->relations->types_helper->object_is( $object, 'mix', 'users' );
		}

		return false;
	}

	/**
	 * Returns related items data for indexer
	 *
	 * @param  [type] $metadata [description]
	 * @param  [type] $type     [description]
	 * @return [type]           [description]
	 */
	public function get_related_meta( $metadata = array(), $type = 'post', $flush = false ) {

		$result = array();

		foreach ( array( 'normal', 'serialized', 'range' ) as $data_type ) {

			if ( empty( $metadata[ $data_type ] ) ) {
				continue;
			}

			foreach ( $metadata[ $data_type ] as $query_var => $data ) {

				if ( ! $this->is_relation_filter( $query_var ) ) {
					continue;
				}

				$query_var_data = explode( '*', $query_var );
				$rel_type       = $query_var_data[0];
				$rel_id         = ! empty( $query_var_data[1] ) ? $query_var_data[1] : false;

				if ( ! $rel_id ) {
					continue;
				}

				$relation = jet_engine()->relations->get_active_relations( $rel_id );

				if ( ! $relation ) {
					continue;
				}

				if ( $flush ) {
					global $wpdb;
					$wpdb->delete( \Jet_Smart_Filters_DB::get_table_full_name( 'indexer' ), array(
						'item_key' => $query_var,
					) );
				}

				if ( empty( $data ) ) {

					switch ( $rel_type ) {

						case 'related_children':
							if ( $this->is_supported_type( $type, $relation->get_args( 'child_object' ) ) ) {
								$data = $relation->get_parents( null, 'ids' );
							}
							break;

						case 'related_parents':
							if ( $this->is_supported_type( $type, $relation->get_args( 'parent_object' ) ) ) {
								$data = $relation->get_children( null, 'ids' );
							}
							break;

					}
				}

				$data = array_unique( $data );

				foreach ( $data as $value ) {

					$rel_ids = false;

					switch ( $rel_type ) {
						case 'related_children':
							if ( $this->is_supported_type( $type, $relation->get_args( 'child_object' ) ) ) {
								$rel_ids = $relation->get_children( $value, 'ids' );
							}
							break;

						case 'related_parents':
							if ( $this->is_supported_type( $type, $relation->get_args( 'parent_object' ) ) ) {
								$rel_ids = $relation->get_parents( $value, 'ids' );
							}
							break;

					}

					if ( ! empty( $rel_ids ) ) {

						foreach ( $rel_ids as $id ) {
							$result[] = array(
								'item_id'    => $id,
								'item_key'   => $query_var,
								'item_value' => $value,
								'item_query' => 'meta_query',
								'type'       => $type,
							);
						}

					}

				}

			}
		}

		return $result;
	}

	/**
	 * Add relation query arguments
	 *
	 * @param [type] $args [description]
	 */
	public function add_relation_query( $args ) {

		if ( empty( $args['meta_query'] ) ) {
			return $args;
		}

		$found = false;

		foreach ( $args['meta_query'] as $key => $data ) {
			if ( ! empty( $data['key'] ) && $this->is_relation_filter( $data['key'] ) ) {
				$args  = $this->add_relation_args( $args, $data );
				$found = true;
			}
		}

		if ( $found ) {
			foreach ( $args['meta_query'] as $key => $data ) {
				if ( ! empty( $data['key'] ) && $this->is_relation_filter( $data['key'] ) ) {
					unset( $args['meta_query'][ $key ] );
				}
			}
		}

		return $args;

	}

	/**
	 * Prevent relation meta keys from indexing with default CCT logic
	 *
	 * @param  [type] $result [description]
	 * @param  [type] $key    [description]
	 * @return [type]         [description]
	 */
	public function prevent_default_indexing_cct_relations( $result, $key ) {
		return $this->is_relation_filter( $key );
	}

	/**
	 * Index CCT relations meta
	 *
	 * @param  [type] $data       [description]
	 * @param  [type] $provider   [description]
	 * @param  [type] $query_args [description]
	 * @param  [type] $indexer    [description]
	 * @return [type]             [description]
	 */
	public function index_cct_relations( $data, $provider, $query_args, $indexer ) {

		if ( ! class_exists( '\Jet_Engine\Modules\Custom_Content_Types\Module' ) ) {
			return $data;
		}

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

		$content_type = \Jet_Engine\Modules\Custom_Content_Types\Module::instance()->manager->get_content_types( $props['query_meta']['content_type'] );

		if ( ! $content_type ) {
			return $data;
		}

		$indexing_data = $indexer->indexing_data[ $provider ];

		if ( empty( $indexing_data ) || empty( $indexing_data['meta_query'] ) ) {
			return $data;
		}

		foreach ( $indexing_data['meta_query'] as $key => $options ) {

			$key_data = explode( '|', $key );
			$suffix   = ! empty( $key_data[1] ) ? $key_data[1] : false;
			$key      = $key_data[0];

			if ( false !== strpos( $key, ',' ) ) {
				$key  = str_replace( ', ', ',', $key );
				$keys = explode( ',', $key );
			} else {
				$keys = array( $key );
			}

			foreach ( $keys as $key ) {

				if ( ! $this->is_relation_filter( $key ) ) {
					continue;
				}

				$rel_id = $this->get_relation_id_from_key( $key );

				if ( ! $rel_id || ! $relation = jet_engine()->relations->get_active_relations( $rel_id ) ) {
					continue;
				}

				$col        = $this->is_children_filter( $key ) ? 'parent_object_id' : 'child_object_id';
				$counts     = $relation->db->raw_query( "SELECT $col AS id, COUNT(*) AS count FROM %table% WHERE rel_id = $rel_id GROUP BY $col;" );
				$key_counts = array_fill_keys( $options, 0 );

				foreach ( $counts as $option ) {
					$option_id                = absint( $option->id );
					$key_counts[ $option_id ] = $option->count;
				}

				$data['meta_query'][ $key ] = $key_counts;

			}

		}

		return $data;

	}

	/**
	 * Extract relation ID from key
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function get_relation_id_from_key( $key ) {
		return str_replace( array( 'related_children*', 'related_parents*' ), '', $key );
	}

	/**
	 * Check if given filter key is relation-associated key
	 *
	 * @param  [type]  $key [description]
	 * @return boolean      [description]
	 */
	public function is_relation_filter( $key ) {
		return ( $this->is_children_filter( $key ) || $this->is_parents_filter( $key ) );
	}

	/**
	 * Chek if is children related items filter
	 *
	 * @param  [type]  $key [description]
	 * @return boolean      [description]
	 */
	public function is_children_filter( $key ) {
		return false !== strpos( $key, 'related_children*' );
	}

	/**
	 * Chek if is parents related items filter
	 *
	 * @param  [type]  $key [description]
	 * @return boolean      [description]
	 */
	public function is_parents_filter( $key ) {
		return false !== strpos( $key, 'related_parents*' );
	}

	/**
	 * Adds realtion query arguments to existing filter query args
	 *
	 * @param [type] $args [description]
	 * @param [type] $data [description]
	 */
	public function add_relation_args( $args, $data ) {

		$key    = $data['key'];
		$key    = explode( '*', $key );
		$type   = $key[0];
		$rel_id = ! empty( $key[1] ) ? $key[1] : false;

		if ( ! $rel_id ) {
			return $args;
		}

		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			return $args;
		}

		$rel_ids = false;
		$object  = false;

		switch ( $type ) {
			case 'related_children':
				$object  = $relation->get_args( 'child_object' );
				$rel_ids = $relation->get_children( $data['value'], 'ids' );
				break;

			case 'related_parents':
				$object  = $relation->get_args( 'parent_object' );
				$rel_ids = $relation->get_parents( $data['value'], 'ids' );
				break;

		}

		if ( false === $rel_ids || ! $object ) {
			return $args;
		}

		if ( empty( $rel_ids ) ) {
			$rel_ids = array( 'not-found' );
		}

		$new_args = jet_engine()->relations->types_helper->filtered_query_args( $object, $rel_ids );

		if ( ! empty( $args['post__in'] ) && ! empty( $new_args['post__in'] ) ) {
			$args['post__in'] = array_intersect( $args['post__in'], $new_args['post__in'] );

			// Not found posts.
			if ( empty( $args['post__in'] ) ) {
				$args['post__in'] = array( PHP_INT_MAX );
			}

			$result = $args;
		} else {
			$result = array_merge_recursive( $args, $new_args );
		}

		return $result;

	}

}
