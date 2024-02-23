<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

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

	public $defaults = array(
		'cct_status' => 'publish',
	);

	public static $prefix = 'jet_cct_';

	public $query_object = null;

	/**
	 * Stores map removed fields to new on DB schema update to try keep the data
	 * @var array
	 */
	public $adjust_fields_map = array();

	/**
	 * Constructor for the class
	 */
	public function __construct( $table = null, $schema = array() ) {

		$this->table  = $table;
		$this->schema = apply_filters( 'jet-engine/custom-content-types/table-schema', $schema, $this );

		if ( ! empty( $_GET['jet_cct_install_tables'] ) ) {
			add_action( 'init', array( $this, 'install_table' ) );
		}

	}

	public function set_query_object( $query_object ) {
		$this->query_object = $query_object;
	}

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

		if ( empty( $data['cct_created'] ) ) {
			$data['cct_created'] = $time;
		}

		if ( empty( $data['cct_modified'] ) ) {
			$item['cct_modified'] = $time;
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

		if ( empty( $data['cct_modified'] ) ) {
			$item['cct_modified'] = current_time( 'mysql' );
		}

		self::wpdb()->update( $this->table(), $new_data, $where );

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
			'_ID'        => 'bigint(20) NOT NULL AUTO_INCREMENT',
			'cct_status' => 'text'
		);

		$additional_columns = $this->schema;
		$columns_schema     = '';

		foreach ( $default_columns as $column => $desc ) {
			$columns_schema .= $column . ' ' . $desc . ',';
		}

		if ( is_array( $additional_columns ) && ! empty( $additional_columns ) ) {

			foreach ( $additional_columns as $column => $definition ) {

				if ( isset( $default_columns[ $column ] ) ) {
					continue;
				}

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

	public function adjusted_fields_map( $old_fields = array(), $new_fields = array() ) {

		if ( empty( $old_fields ) || empty( $new_fields ) ) {
			return;
		}

		$old_fields_by_id = $this->get_fields_grouped_by_id( $old_fields );
		$new_fields_by_id = $this->get_fields_grouped_by_id( $new_fields );

		foreach ( $old_fields_by_id as $id => $field ) {
			if ( isset( $new_fields_by_id[ $id ] ) ) {
				$this->adjust_fields_map[ $field ] = $new_fields_by_id[ $id ];
			}
		}

	}

	public function adjusted_fields_types( $old_schema = array(), $old_fields = array(), $new_fields = array() ) {

		if ( empty( $old_schema ) || empty( $this->schema ) ) {
			return;
		}

		$old_fields_types_by_name = wp_list_pluck( $old_fields, 'type', 'name' );
		$new_fields_types_by_name = wp_list_pluck( $new_fields, 'type', 'name' );

		foreach ( $old_schema as $col => $type ) {

			$new_col = isset( $this->adjust_fields_map[ $col ] ) ? $this->adjust_fields_map[ $col ] : $col;

			if ( isset( $this->schema[ $new_col ] ) && $type !== $this->schema[ $new_col ] ) {

				$new_type = $this->schema[ $new_col ];
				$table    = $this->table();

				$old_field_type = $old_fields_types_by_name[ $col ];
				$new_field_type = $new_fields_types_by_name[ $new_col ];

				// Convert datetime string to timestamp string
				if ( in_array( $old_field_type, array( 'date', 'datetime', 'datetime-local' ) )
					 && in_array( $new_field_type, array( 'date', 'datetime', 'datetime-local' ) )
					 && 'TEXT' === $type && 'BIGINT' === $new_type
				) {
					self::wpdb()->query( "UPDATE $table SET $col = UNIX_TIMESTAMP( CONVERT_TZ( $col, '+00:00', @@global.time_zone ) ) WHERE $col IS NOT NULL;" );
				}

				// Change column datatype
				self::wpdb()->query( "ALTER TABLE $table MODIFY COLUMN $col $new_type;" );

				// Convert timestamp string to datetime string
				if ( in_array( $old_field_type, array( 'date', 'datetime', 'datetime-local' ) )
					 && in_array( $new_field_type, array( 'date', 'datetime', 'datetime-local' ) )
					 && 'BIGINT' === $type && 'TEXT' === $new_type
				) {

					switch ( $new_field_type ) {
						case 'date':
							self::wpdb()->query( "UPDATE $table SET $col = FROM_UNIXTIME( $col, '%Y-%m-%d' ) WHERE $col IS NOT NULL;" );
							break;

						case 'datetime':
						case 'datetime-local':
							self::wpdb()->query( "UPDATE $table SET $col = DATE_FORMAT( CONVERT_TZ( FROM_UNIXTIME( $col ), @@global.time_zone, '+00:00' ), '%Y-%m-%dT%H:%i' ) WHERE $col IS NOT NULL;" );
							break;
					}
				}
			}
		}
	}

	public function get_fields_grouped_by_id( $fields_list = array() ) {
		
		$result = array();

		foreach ( $fields_list as $index => $field ) {
			$index = isset( $field['id'] ) ? $field['id'] : $index;
			$result[ $index ] = $field['name'];
		}

		return $result;

	}

	/**
	 * Check if we can transfer data into new columns before removing
	 *
	 * Rewrite this method in the childrent where it supported
	 */
	public function maybe_transfer_data( $old_columns = array(), $new_columns = array() ) {

		if ( empty( $old_columns ) || empty( $new_columns ) ) {
			return;
		}

		foreach ( $old_columns as $index => $col ) {
			
			$new_col = isset( $this->adjust_fields_map[ $col ] ) ? $this->adjust_fields_map[ $col ] : false;

			if ( $new_col ) {
				$table = $this->table();
				$sql   = "UPDATE $table SET $new_col = $col WHERE $col IS NOT NULL;";
				self::wpdb()->query( $sql );
			}

		}

	}

	/**
	 * Returns array of table column names
	 *
	 * @return [type] [description]
	 */
	public function get_columns_list() {

		$table = $this->table();
		$sql   = "SHOW COLUMNS FROM `$table` WHERE field NOT LIKE '_ID'";

		$exclude_fields = apply_filters( 'jet-engine/custom-content-types/db/exclude-fields', array() );

		if ( ! empty( $exclude_fields ) ) {
			foreach ( $exclude_fields as $exclude_field ) {
				$sql .= " AND field NOT LIKE '$exclude_field'";
			}
		}

		$columns = self::wpdb()->get_results( $sql );
		$result  = array();


		if ( ! empty( $columns ) ) {

			foreach ( $columns as $column ) {
				$result[ $column->Field ] = $column->Type;
			}

		}

		return $result;

	}

	public function get_random_seed() {

		$transient_key  = 'jet_cct_random_seed';
		$transient_time = 3 * MINUTE_IN_SECONDS;

		$seed = get_transient( $transient_key );

		if ( empty( $seed ) ) {
			$seed = rand();
			set_transient( $transient_key, $seed, $transient_time );
		}

		return apply_filters( 'jet-engine/custom-content-types/db/random-seed', $seed, $transient_key, $transient_time );
	}

	/**
	 * Return count of queried items
	 *
	 * @return [type] [description]
	 */
	public function count( $args = array(), $rel = 'AND' ) {

		$table = $this->table();

		$query = "SELECT count(*) FROM $table";

		if ( ! $rel ) {
			$rel = 'AND';
		}

		$where = $this->add_where_args( $args, $rel );

		if ( ! $where ) {
			$where = " WHERE 1=1 ";
		}

		$query .= apply_filters( 'jet-engine/custom-content-types/sql-count-query', $where, $table, $args, $this );

		return self::wpdb()->get_var( $query );

	}

	/**
	 * Query data from db table
	 *
	 * @return [type] [description]
	 */
	public function query( $args = array(), $limit = 0, $offset = 0, $order = array(), $rel = 'AND' ) {

		$table = $this->table();
		$query = array();
		
		$query['select'] = "SELECT * FROM $table";

		if ( ! $rel ) {
			$rel = 'AND';
		}

		$search = ! empty( $args['_cct_search'] ) ? $args['_cct_search'] : false;

		if ( $search ) {
			unset( $args['_cct_search'] );
		}

		$where = $this->add_where_args( $args, $rel );
		
		$query['where'] = ! empty( $where ) ? $where : " WHERE 1=1";

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

				$search_sql = ' ' . $rel;
				$query['search'] = $search_sql . ' (' . $search_str . ')';

			}
		}

		if ( empty( $order ) ) {
			$order = array( array(
				'orderby' => '_ID',
				'order'   => 'desc',
			) );
		}

		$query['order'] = $this->add_order_args( $order );

		if ( intval( $limit ) > 0 ) {
			$limit          = absint( $limit );
			$offset         = absint( $offset );
			$query['limit'] = " LIMIT $offset, $limit";
		}

		$query = apply_filters( 'jet-engine/custom-content-types/sql-query-parts', $query, $table, $args, $this );
		$query = implode( '', $query );

		$raw = self::wpdb()->get_results( $query, $this->get_format_flag() );

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

				$item['cct_slug'] = $this->table;

			} elseif ( is_object( $item ) ) {

				foreach ( get_object_vars( $item )  as $key => $value ) {

					$value = maybe_unserialize( $value );

					if ( is_string( $value ) ) {
						$item->$key = wp_unslash( $value );
					} else {
						$item->$key = $value;
					}

				}

				$item->cct_slug = $this->table;
			}

			return $item;
		}, $raw );

	}

}
