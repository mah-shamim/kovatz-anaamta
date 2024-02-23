<?php
namespace Jet_Engine\Relations\Legacy;

/**
 * Jet_Engine_Relations_Hierarchy manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Relations_Hierarchy class
 */
class Hierarchy {

	private $_hierarchy = array();
	private $_relations = array();

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_filter( 'jet-engine/listings/macros-list', array( $this, 'register_grand_parent_child_macros' ) );
		//add_action( 'wp_loaded', array( $this, 'test_relations' ) );
	}

	public function test_relations() {
		$this->get_grandparent( 'kingdom', 'entity' );
		$this->get_grandchild( 'entity', 'kingdom' );
	}

	/**
	 * Add %get_grandparent% and %get_grandchild%
	 * @return [type] [description]
	 */
	public function register_grand_parent_child_macros( $macros = array() ) {

		$macros['get_grandparent'] = array(
			'label' => esc_html__( 'Get grandparent (legacy)', 'jet-engine' ),
			'cb'    => array( $this, 'get_grandparent_macros' ),
			'args'  => array(
				'post_type' => array(
					'label'   => __( 'Post type', 'jet-engine' ),
					'type'    => 'select',
					'options' => jet_engine()->listings->get_post_types_for_options(),
				),
			),
		);

		$macros['get_grandchild'] = array(
			'label' => esc_html__( 'Get grandchild (legacy)', 'jet-engine' ),
			'cb'    => array( $this, 'get_grandchild_macros' ),
			'args'  => array(
				'post_type' => array(
					'label'   => __( 'Post type', 'jet-engine' ),
					'type'    => 'select',
					'options' => jet_engine()->listings->get_post_types_for_options(),
				),
			),
		);

		return $macros;

	}

	/**
	 * Has hierarchy
	 *
	 * @return boolean [description]
	 */
	public function has_hierarchy() {
		return ! empty( $this->_hierarchy );
	}

	/**
	 * Handler for %get_grandparent% macros
	 * @param  [type] $value          [description]
	 * @param  [type] $from_post_type [description]
	 * @return [type]                 [description]
	 */
	public function get_grandparent_macros( $value, $from_post_type ) {

		$ids = $this->get_grandparent( $from_post_type );

		if ( empty( $ids ) ) {
			$ids = array( 0 );
		}

		return implode( ',', $ids );
	}

	/**
	 * Handler for %get_grandchild% macros
	 * @param  [type] $value          [description]
	 * @param  [type] $from_post_type [description]
	 * @return [type]                 [description]
	 */
	public function get_grandchild_macros( $value, $from_post_type ) {

		$ids = $this->get_grandchild( $from_post_type );

		if ( empty( $ids ) ) {
			$ids = array( 0 );
		}

		return implode( ',', $ids );

	}

	/**
	 * Returns grandparent posts for current post type
	 *
	 * @param  [type] $value     [description]
	 * @param  [type] $post_type [description]
	 * @return [type]            [description]
	 */
	public function get_grandparent( $from_post_type = null, $current = null, $post_id = null ) {

		if ( ! $current ) {
			$current = get_post_type();
		}

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$grandparent = $from_post_type;

		if ( ! $current ) {
			return;
		}

		if ( empty( $this->_hierarchy ) ) {
			return;
		}

		$trail = $this->get_trail_data( $grandparent, $current );

		if ( empty( $trail['post_types'] ) ) {
			return;
		}

		if ( 1 === count( $trail['keys'] ) && 2 === count( $trail['post_types'] ) ) {
			$posts = jet_engine()->relations->legacy->get_related_posts( array(
				'post_type_1' => $from_post_type,
				'post_type_2' => $current,
				'from'        => $from_post_type,
			) );
		}

		$posts = $this->get_posts_by_trail( $post_id, $trail, 'up', $grandparent );

		if ( is_array( $posts ) ) {
			return $posts;
		} else {
			return array();
		}

	}

	/**
	 * Returns grandchild posts for current post type
	 *
	 * @param  [type] $value     [description]
	 * @param  [type] $post_type [description]
	 * @return [type]            [description]
	 */
	public function get_grandchild( $from_post_type = null, $current = null, $post_id = null ) {

		if ( ! $current ) {
			$current = get_post_type();
		}

		$grandchild = $from_post_type;

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $current ) {
			return;
		}

		if ( empty( $this->_hierarchy ) ) {
			return;
		}

		$trail = $this->get_trail_data( $current, $grandchild );

		if ( empty( $trail['post_types'] ) ) {
			return;
		}

		if ( 1 === count( $trail['keys'] ) && 2 === count( $trail['post_types'] ) ) {
			$posts = jet_engine()->relations->legacy->get_related_posts( array(
				'post_type_1' => $current,
				'post_type_2' => $from_post_type,
				'from'        => $from_post_type,
			) );
		}

		$posts = $this->get_posts_by_trail( $post_id, $trail, 'down', $grandchild );

		if ( is_array( $posts ) ) {
			return $posts;
		} else {
			return array();
		}

	}

	/**
	 * Returns posts by post types and meta keys trail
	 *
	 * @return [type] [description]
	 */
	public function get_posts_by_trail( $post_id = null, $trail = array(), $dir = 'down', $column = false ) {

		$post_types = $trail['post_types'];
		$keys       = $trail['keys'];
		$post_id    = absint( $post_id );

		if ( 'up' === $dir ) {
			$post_types = array_reverse( $post_types );
			$keys       = array_reverse( $keys );
		}

		$select = "SELECT ";
		$from   = "FROM ";
		$where  = "WHERE ";

		global $wpdb;

		$table = $wpdb->postmeta;

		if ( ! $column ) {
			$glue = ', ';
		} else {
			$glue = '';
		}

		foreach ( $post_types as $index => $post_type ) {

			$show = ( ! $column || ( $column && $post_type === $column ) ) ? true : false;

			if ( 0 === $index ) {
				if ( $show ) {
					$select .= "p1.post_id AS `{$post_type}`";
				}
			} else {
				if ( $show ) {
					$select .= "{$glue}p{$index}.meta_value AS `{$post_type}`";
				}
			}

		}

		foreach ( $keys as $index => $key ) {

			$tableindex = $index + 1;
			$key        = esc_attr( $key );

			if ( 0 === $index ) {
				$from  .= "{$table} AS p1";
				$where .= "p1.post_id = {$post_id} AND p1.meta_key = '{$key}'";
			} else {
				$from  .= " LEFT JOIN {$table} AS p{$tableindex} ON p{$tableindex}.post_id = p{$index}.meta_value";
				$where .= " AND p{$tableindex}.meta_key = '{$key}'";
			}
		}

		$query = $select . ' ' . $from . ' ' . $where . ';';

		if ( $column ) {
			$result = $wpdb->get_col( $query );
		} else {
			$result = $wpdb->get_results( $query );
		}

		return $result;

	}

	/**
	 * Returns trail from post type to post type
	 *
	 * @param  [type] $from [description]
	 * @param  [type] $to   [description]
	 * @return [type]       [description]
	 */
	public function get_trail_data( $from = null, $to = null ) {

		$result = array(
			'post_types' => array(),
			'keys'       => array(),
		);

		foreach ( $this->_hierarchy as $hierarchy ) {
			$post_types = $hierarchy['post_types'];
			$keys       = $hierarchy['trail'];
			$from_index = array_search( $from, $post_types );
			$to_index   = array_search( $to, $post_types );

			if ( false === $from_index || false === $to_index || $to_index < $from_index ) {
				continue;
			}

			$to_index_keys = $to_index - 1;

			$result['post_types'] = array_slice( $post_types, $from_index, ( $to_index - $from_index + 1 ) );
			$result['keys']       = array_slice( $keys, $from_index, ( $to_index_keys - $from_index + 1 ) );

			return $result;

		}

		return $result;
	}

	/**
	 * Create hierarchy description
	 *
	 * @param  [type] $relations [description]
	 * @return [type]            [description]
	 */
	public function create_hierarchy( $relations = array() ) {

		$this->_relations = $this->prepare_relations( $relations );

		foreach ( $this->_relations as $key => $relation ) {

			if ( empty( $relation['parent_relation'] ) ) {

				if ( ! empty( $relation['children'] ) ) {
					$this->search_for_children( $key, $relation, null );
				}

			}

		}

	}

	/**
	 * Prepare relations array.
	 *
	 * @param  array $relations
	 * @return array
	 */
	public function prepare_relations( $relations = array() ) {

		foreach ( $relations as $key => $relation ) {

			if ( ! empty( $relation['parent_relation'] ) && isset( $relations[ $relation['parent_relation'] ] ) ) {
				$relations[ $relation['parent_relation'] ]['children'][] = $key;
			}

		}

		return $relations;
	}

	/**
	 * Add relations to existing hierarchy or create new
	 *
	 * @param string $key
	 * @param array  $relation
	 * @param array  $_result
	 */
	public function search_for_children( $key = null, $relation = null, $_result = null ) {

		foreach ( $relation['children'] as $child_key ) {

			if ( empty( $_result ) ) {
				$result = array(
					'trail'      => array( $key ),
					'post_types' => array( $relation['post_type_1'], $relation['post_type_2'] ),
				);
			} else {
				$result = $_result;
			}

			$child_relation = $this->_relations[ $child_key ];

			$result['trail'][]      = $child_key;
			$result['post_types'][] = $child_relation['post_type_1'];
			$result['post_types'][] = $child_relation['post_type_2'];

			$result['post_types'] = array_values( array_unique(
				$result['post_types']
			) );

			if ( ! empty( $child_relation['children'] ) ) {
				$this->search_for_children( $child_key, $child_relation, $result );
			} else {
				$this->_hierarchy[] = $result;
			}
		}

	}

}
