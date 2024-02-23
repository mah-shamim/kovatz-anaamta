<?php
namespace Jet_Engine\Relations\Storage;

/**
 * Database manager class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Base DB class
 */
class DB extends \Jet_Engine_Base_DB {

	public static $prefix = 'jet_rel_';
	public $relations_cache = [];

	/**
	 * Insert booking
	 *
	 * @param  array  $booking [description]
	 * @return [type]          [description]
	 */
	public function insert( $data = array() ) {

		if ( ! empty( $this->defaults ) ) {
			foreach ( $this->defaults as $default_key => $default_value ) {
				if ( ! isset( $data[ $default_key ] ) ) {
					$data[ $default_key ] = $default_value;
				}
			}
		}

		$time = current_time( 'mysql' );

		if ( empty( $data['created'] ) ) {
			$data['created'] = $time;
		}

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$value        = maybe_serialize( $value );
				$data[ $key ] = $value;
			}
		}

		$inserted = self::wpdb()->insert( $this->table(), $data );

		if ( $inserted ) {
			return self::wpdb()->insert_id;
		} else {
			return false;
		}
	}

	/**
	 * Update appointment info
	 *
	 * @param  array  $new_data [description]
	 * @param  array  $where    [description]
	 * @return [type]           [description]
	 */
	public function update( $new_data = array(), $where = array() ) {

		if ( ! empty( $this->defaults ) ) {
			foreach ( $this->defaults as $default_key => $default_value ) {
				if ( ! isset( $data[ $default_key ] ) ) {
					$data[ $default_key ] = $default_value;
				}
			}
		}

		foreach ( $new_data as $key => $value ) {
			if ( is_array( $value ) ) {
				$value            = maybe_serialize( $value );
				$new_data[ $key ] = $value;
			}
		}

		return self::wpdb()->update( $this->table(), $new_data, $where );

	}

	/**
	 * Returns table columns schema
	 *
	 * @return [type] [description]
	 */
	public function get_table_schema() {

		$charset_collate = $this->wpdb()->get_charset_collate();
		$table           = $this->table();
		$default_columns = array(
			'_ID'     => 'bigint(20) NOT NULL AUTO_INCREMENT',
			'created' => 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP',
		);

		$additional_columns = $this->schema;
		$columns_schema     = '';

		foreach ( $default_columns as $column => $desc ) {
			$columns_schema .= $column . ' ' . $desc . ',';
		}

		if ( is_array( $additional_columns ) && ! empty( $additional_columns ) ) {
			foreach ( $additional_columns as $column => $definition ) {

				if ( ! $definition ) {
					$definition = 'text';
				}

				$columns_schema .= $column . ' ' . $definition . ',';

			}
		}

		return "CREATE TABLE $table (
			$columns_schema
			PRIMARY KEY (_ID)
		) $charset_collate;";

	}

	/**
	 * Get unique string cache key for given relations request
	 * 
	 * @param  array   $args   [description]
	 * @param  integer $limit  [description]
	 * @param  integer $offset [description]
	 * @param  array   $order  [description]
	 * @param  string  $rel    [description]
	 * @return [type]          [description]
	 */
	public function get_cache_key( $args = array(), $limit = 0, $offset = 0, $order = array(), $rel = 'AND' ) {
		return apply_filters(
			'jet-engine/relations/db/cache-key',
			md5( json_encode( $args ) . json_encode( $order ) . $limit . $offset . $rel ),
			$args = array(), $limit = 0, $offset = 0, $order = array(), $rel = 'AND'
		);
	}

	/**
	 * Reset relations object cache
	 * @return [type] [description]
	 */
	public function reset_cache() {
		$this->relations_cache = [];
	}

	/**
	 * Query data from db table
	 *
	 * @return [type] [description]
	 */
	public function query( $args = array(), $limit = 0, $offset = 0, $order = array(), $filter = false, $rel = 'AND' ) {

		$cache_key = $this->get_cache_key( $args, $limit, $offset, $order, $rel );
		$raw       = isset( $this->relations_cache[ $cache_key ] ) ? $this->relations_cache[ $cache_key ] : false;

		if ( false === $raw ) {
			
			$table = $this->table();

			$query = "SELECT * FROM $table";

			if ( ! $rel ) {
				$rel = 'AND';
			}

			$search = ! empty( $args['_search'] ) ? $args['_search'] : false;

			if ( $search ) {
				unset( $args['_search'] );
			}

			$where  = $this->add_where_args( $args, $rel );
			$query .= $where;

			if ( $search ) {

				$search_str = array();
				$keyword    = $search['keyword'];
				$fields     = ! empty( $search['fields'] ) ? $search['fields'] : false;

				if ( ! $fields ) {
					$fields = array_keys( $this->schema );
				}

				if ( $fields ) {
					foreach ( $fields as $field ) {
						$search_str[] = sprintf( '`%1$s` LIKE \'%%%2$s%%\'', $field, $keyword );
					}

					$search_str = implode( ' OR ', $search_str );
				}

				if ( ! empty( $search_str ) ) {

					if ( $where ) {
						$query .= ' ' . $rel;
					} else {
						$query .= ' WHERE';
					}

					$query .= ' (' . $search_str . ')';

				}
			}

			if ( empty( $order ) ) {
				$order = array( array(
					'orderby' => '_ID',
					'order'   => 'desc',
				) );
			}

			$query .= $this->add_order_args( $order );

			if ( intval( $limit ) > 0 ) {
				$limit  = absint( $limit );
				$offset = absint( $offset );
				$query .= " LIMIT $offset, $limit";
			}

			$raw = self::wpdb()->get_results( $query, $this->get_format_flag() );
			$this->relations_cache[ $cache_key ] = $raw;

		}

		if ( $filter && is_callable( $filter ) ) {
			return array_map( $filter, $raw );
		} else {
			return array_map( function( $item ) {

				if ( is_array( $item ) ) {
					foreach ( $item as $key => $value ) {

						$value = maybe_unserialize( $value );

						if ( is_string( $value ) ) {
							$item[ $key ] = wp_unslash( $value );
						} else {
							$item[ $key ] = $value;
						}
					}

				} elseif ( is_object( $item ) ) {

					foreach ( get_object_vars( $item )  as $key => $value ) {

						$value = maybe_unserialize( $value );

						if ( is_string( $value ) ) {
							$item->$key = wp_unslash( $value );
						} else {
							$item->$key = $value;
						}

					}

				}

				return $item;

			}, $raw );
		}

	}

}
