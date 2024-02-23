<?php
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
class Jet_Engine_Base_DB {

	public $_found_items = array();

	/**
	 * Check if booking DB table already exists
	 *
	 * @var bool
	 */
	public $table_exists = null;

	/**
	 * Return format flag;
	 *
	 * @var null
	 */
	public $format_flag = null;

	/**
	 * Stores latest queried result to use it
	 *
	 * @var null
	 */
	public $latest_result = null;

	/**
	 * Default values for table cols
	 * @var array
	 */
	public $defaults = array();

	/**
	 * Table name
	 * @var null
	 */
	public $table = null;

	/**
	 * Table schema
	 * @var array
	 */
	public $schema = array();

	/**
	 * Latest queried item ID
	 * @var boolean
	 */
	public $queried_item_id = false;

	/**
	 * Latest queried item ID
	 * @var boolean
	 */
	public $queried_item = false;

	/**
	 * Internal prefix
	 * @var boolean
	 */
	public static $prefix = '';

	/**
	 * Constructor for the class
	 */
	public function __construct( $table = null, $schema = array() ) {
		$this->table  = $table;
		$this->schema = $schema;
	}

	/**
	 * Return s Custom content types tables prfix
	 *
	 * @return [type] [description]
	 */
	public static function table_prefix() {
		return self::wpdb()->prefix . static::$prefix;
	}

	/**
	 * Returns table name
	 * @return [type] [description]
	 */
	public function table() {
		return static::table_prefix() . $this->table;
	}

	public function get_queried_item_id() {
		return $this->queried_item_id;
	}

	public function set_queried_item_id( $item_id = false ) {
		$this->queried_item_id = $item_id;
	}

	public function get_queried_item() {

		if ( ! $this->queried_item && $this->get_queried_item_id() ) {
			$this->queried_item = $this->get_item( $this->get_queried_item_id() );
		}

		return $this->queried_item;
	}

	public function set_queried_item( $item = false ) {

		if ( is_integer( $item ) ) {
			$this->set_queried_item_id( $item );
			$this->get_queried_item();
		} else {
			$this->queried_item = $item;
		}

	}

	/**
	 * Insert booking
	 *
	 * @param  array  $booking [description]
	 * @return [type]          [description]
	 */
	public function insert( $data = array() ) {
	}

	/**
	 * Update appointment info
	 *
	 * @param  array  $new_data [description]
	 * @param  array  $where    [description]
	 * @return [type]           [description]
	 */
	public function update( $new_data = array(), $where = array() ) {
	}

	/**
	 * Delete column
	 * @return [type] [description]
	 */
	public function delete( $where = array() ) {
		self::wpdb()->delete( $this->table(), $where );
	}

	/**
	 * Returns WPDB error thrown
	 *
	 * @return [type] [description]
	 */
	public function get_errors() {
		return self::wpdb()->last_error;
	}

	/**
	 * Check if current instance table alredy exists
	 *
	 * @return boolean [description]
	 */
	public function is_table_exists() {

		if ( null !== $this->table_exists ) {
			return $this->table_exists;
		}

		$table = $this->table();

		if ( strtolower( $table ) === strtolower( self::wpdb()->get_var( "SHOW TABLES LIKE '$table'" ) ) ) {
			$this->table_exists = true;
		} else {
			$this->table_exists = false;
		}

		return $this->table_exists;

	}

	/**
	 * Check if requested table already exists
	 *
	 * @return boolean [description]
	 */
	public static function custom_table_exists( $table = null ) {

		if ( ! $table ) {
			return false;
		}

		$table = static::table_prefix() . $table;

		if ( $table === self::wpdb()->get_var( "SHOW TABLES LIKE '$table'" ) ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Try to recreate DB table by request
	 *
	 * @return void
	 */
	public function install_table() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->create_table();

	}

	/**
	 * Returns WPDB instance
	 * @return [type] [description]
	 */
	public static function wpdb() {
		global $wpdb;
		return $wpdb;
	}

	/**
	 * Create DB table for apartment units
	 *
	 * @return [type] [description]
	 */
	public function create_table( $delete_if_exists = false ) {

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		}

		if ( $delete_if_exists && $this->is_table_exists() ) {
			$this->drop_table();
		}

		$sql = $this->get_table_schema();

		dbDelta( $sql );

	}

	/**
	 * Drop current table from DB
	 *
	 * @return [type] [description]
	 */
	public function drop_table() {
		$table = $this->table();
		self::wpdb()->query( "DROP TABLE $table;" );
	}

	/**
	 * Returns table columns schema
	 *
	 * @return [type] [description]
	 */
	public function get_table_schema() {
	}

	/**
	 * Check if content type has given column
	 *
	 * @param  [type]  $column_name [description]
	 * @return boolean              [description]
	 */
	public function has_col( $column_name ) {
		return isset( $this->schema[ $column_name ] );

	}

	/**
	 * Insert new columns into existing bookings table
	 *
	 * @param  [type] $columns [description]
	 * @return [type]          [description]
	 */
	public function insert_table_columns( $columns = array() ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$table          = $this->table();
		$columns_schema = '';

		foreach ( $columns as $column => $desc ) {
			if ( ! $desc ) {
				$desc = 'text';
			}
			$columns_schema .= 'ADD ' . $column . ' ' . $desc . ',';
		}

		$columns_schema = rtrim( $columns_schema, ',' );

		$sql = "ALTER TABLE $table $columns_schema;";

		self::wpdb()->query( $sql );

	}

	/**
	 * Returns array of table column names
	 *
	 * @return [type] [description]
	 */
	public function get_columns_list() {

		$table = $this->table();
		$sql   = "SHOW COLUMNS FROM `$table` WHERE field NOT LIKE '_ID'";

		$exclude_fields = apply_filters( 'jet-engine/base-db/exclude-fields', array(), $this );

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

	/**
	 * Adjust existing DB columns to defined columns schema
	 *
	 * @return [type] [description]
	 */
	public function adjust_fields_to_schema() {

		$existing_columns = $this->get_columns_list();
		$columns_schema   = $this->schema;
		$existing_keys    = array_keys( $existing_columns );
		$schema_keys      = array_keys( $columns_schema );

		$to_add    = array_diff( $schema_keys, $existing_keys );
		$to_remove = array_diff( $existing_keys, $schema_keys );

		if ( ! empty( $to_add ) ) {

			$to_add_columns = array();

			foreach ( $columns_schema as $key => $desc ) {
				if ( in_array( $key, $to_add ) ) {
					$to_add_columns[ $key ] = $desc;
				}
			}

			$this->insert_table_columns( $to_add_columns );

		}

		$this->maybe_transfer_data( $to_remove, $to_add );

		if ( ! empty( $to_remove ) ) {
			$this->delete_table_columns( $to_remove );
		}

	}

	/**
	 * Check if we can transfer data into new columns before removing
	 *
	 * Rewrite this method in the childrent where it supported
	 */
	public function maybe_transfer_data( $old_columns = array(), $new_columns = array() ) {
	}

	/**
	 * Check if booking DB column is exists
	 *
	 * @return [type] [description]
	 */
	public function column_exists( $column ) {

		$table = $this->table();
		return self::wpdb()->query( "SHOW COLUMNS FROM `$table` LIKE '$column'" );

	}

	/**
	 * Delete columns into existing bookings table
	 *
	 * @param  [type] $columns [description]
	 * @return [type]          [description]
	 */
	public function delete_table_columns( $columns ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$table          = $this->table();
		$columns_schema = '';

		foreach ( $columns as $column ) {
			$columns_schema .= ' DROP ' . $column . ',';
		}

		$columns_schema = rtrim( $columns_schema, ',' );

		$sql = "ALTER TABLE $table $columns_schema;";

		self::wpdb()->query( $sql );

	}

	/**
	 * Add nested query arguments
	 *
	 * @param  [type]  $key    [description]
	 * @param  [type]  $value  [description]
	 * @param  boolean $format [description]
	 * @return [type]          [description]
	 */
	public function get_sub_query( $key = null, $value = null, $format = false ) {

		$query = '';
		$glue  = '';

		if ( ! $format ) {

			if ( false !== strpos( $key, '!' ) ) {
				$format = '`%1$s` != \'%2$s\'';
				$key    = ltrim( $key, '!' );
			} else {
				$format = '`%1$s` = \'%2$s\'';
			}

		}

		foreach ( $value as $child ) {
			$query .= $glue;
			$query .= sprintf( $format, esc_sql( $key ), esc_sql( $child ) );
			$glue   = ' OR ';
		}

		return $query;

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

		$query .= $this->add_where_args( $args, $rel );

		return self::wpdb()->get_var( $query );

	}

	/**
	 * Add where arguments to query
	 *
	 * @param array  $args [description]
	 * @param string $rel  [description]
	 */
	public function add_where_args( $args = array(), $rel = 'AND', $add_where_string = true ) {

		$query      = '';
		$multi_args = false;

		if ( ! empty( $args ) ) {

			if ( $add_where_string ) {
				$query .= ' WHERE ';
			}

			$glue = '';

			if ( count( $args ) > 1 ) {
				$multi_args = true;
			}

			foreach ( $args as $key => $arg ) {

				$clause = false;

				if ( isset( $arg['relation'] ) ) {
					$sub_rel = $arg['relation'];
					unset( $arg['relation'] );
					$clause = '(' . $this->add_where_args( $arg, $sub_rel, false ) . ')';
				} else {
					if ( is_array( $arg ) && isset( $arg['field'] ) ) {
						$field    = ! empty( $arg['field'] ) ? $arg['field'] : false;
						$operator = ! empty( $arg['operator'] ) ? $arg['operator'] : '=';
						$value    = isset( $arg['value'] ) ? $arg['value'] : '';
						$type     = ! empty( $arg['type'] ) ? $arg['type'] : false;
					} else {
						$field    = $key;
						$operator = '=';
						$value    = $arg;
						$type     = false;
					}

					$clause = $this->prepare_where_clause( $field, $operator, $value, $type );
				}

				if ( $clause ) {
					$query .= $glue;
					$query .= $clause;
					$glue   = ' ' . $rel . ' ';
				}

			}

		}

		return $query;

	}

	/**
	 * Adjust SQL value string according queried argument type
	 *
	 * @param  string  $value [description]
	 * @param  boolean $type  [description]
	 * @return [type]         [description]
	 */
	public function adjust_value_by_type( $value = '', $type = false ) {

		if ( is_array( $value ) ) {
			return false;
		}

		$type = strtolower( $type );

		switch ( $type ) {
			case 'integer':
				$value = absint( $value );
				break;
			case 'float':
				$value = floatval( $value );
				break;
			case 'timestamp':
				if ( ! \Jet_Engine_Tools::is_valid_timestamp( $value ) ) {
					$value = strtotime( $value );
				}
				$value = absint( $value );
				break;
			case 'date':
				$value = strtotime( $value );
				$value = sprintf( "'%s'", date( 'Y-m-d H:i:s', $value ) );
				break;
			default:
				$value = sprintf( "'%s'", esc_sql( $value ) );
				break;
		}

		return $value;

	}

	/**
	 * Perform a raw query by current table
	 *
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function raw_query( $query ) {

		$query = preg_replace_callback( '/\%.*?\%/', function( $matches ) {

			$result = '';
			$match  = $matches[0];
			$match  = trim( $match, '%' );

			$data = explode( '::', $match );

			if ( empty( $data[1] ) ) {
				$result = $this->table();
			} else {
				$result = self::wpdb()->prefix . $data[1];
			}

			return $result;

		}, $query );

		return self::wpdb()->get_results( $query );
	}

	/**
	 * Check if $haystack start from $needle
	 *
	 * @param  [type] $haystack [description]
	 * @param  [type] $needle   [description]
	 * @return [type]           [description]
	 */
	public function starts_with( $haystack, $needle ) {
		$length = strlen( $needle );
		return substr( $haystack, 0, $length ) === $needle;
	}

	/**
	 * Check if $haystack ends with $needle
	 *
	 * @param  [type] $haystack [description]
	 * @param  [type] $needle   [description]
	 * @return [type]           [description]
	 */
	public function ends_with( $haystack, $needle ) {

		$length = strlen( $needle );

		if ( ! $length ) {
			return true;
		}

		return substr( $haystack, -$length ) === $needle;
	}

	/**
	 * Prepare string to use in like or not like operator
	 *
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function prepare_value_for_like_operator( $value ) {
		if ( $this->starts_with( $value, '%' ) || $this->ends_with( $value, '%' ) ) {
			return sprintf( "'%s'", $value );
		} else {
			return sprintf( "'%%%s%%'", esc_sql( $value ) );
		}
	}

	/**
	 * Prepare single query clause by arguments
	 *
	 * @return [type] [description]
	 */
	public function prepare_where_clause( $field = false, $operator = '=', $value = '', $type = false ) {

		if ( ! $field ) {
			return '';
		}

		$format          = '`%1$s` %3$s %2$s';
		$array_operators = array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' );

		switch ( $type ) {
			case 'integer':
			case 'float':
				$format = 'CAST( `%1$s` AS DECIMAL ) %3$s %2$s';
				break;

			default:

				if ( false !== strpos( $type, 'DECIMAL' ) ) {
					$format = 'CAST( `%1$s` AS ' . $type . ' ) %3$s %2$s';
				}

				break;

		}

		if ( 'EXISTS' === $operator ) {
			$format = '`%1$s` IS NOT NULL';
		} elseif ( 'NOT EXISTS' === $operator ) {
			$format = '`%1$s` IS NULL';
		}

		if ( ! is_array( $value ) && in_array( $operator, $array_operators ) && false !== strpos( $value, ',' ) ) {
			$value = explode( ',', $value );
			$value = array_map( 'trim', $value );
		}

		if ( is_array( $value ) ) {
			switch ( $operator ) {

				case 'IN':
				case 'NOT IN':

					array_walk( $value, function( &$item ) use ( $type ) {
						$item = $this->adjust_value_by_type( $item, $type );
					} );

					$value = sprintf( '( %s )', implode( ', ', $value ) );

					break;

				case 'BETWEEN':
				case 'NOT BETWEEN':

					$from = isset( $value[0] ) ? $value[0] : 0;
					$to   = isset( $value[1] ) ? $value[1] : $from;

					$from = $this->adjust_value_by_type( $from, $type );
					$to   = $this->adjust_value_by_type( $to, $type );

					$value = sprintf( '%1$s AND %2$s', $from, $to );

					break;

				default:
					$format = '(%2$s)';
					$args   = array();

					foreach ( $value as $val ) {
						$args[] = array(
							'field'    => $field,
							'operator' => $operator,
							'type'     => $type,
							'value'    => $val,
						);
					}

					$value = $this->add_where_args( $args, 'OR', false );
					break;

			}
		} else {

			if ( in_array( $operator, array( 'LIKE', 'NOT LIKE' ) ) ) {
				$value = $this->prepare_value_for_like_operator( $value );
			} else {
				$value = $this->adjust_value_by_type( $value, $type );
			}

			if ( in_array( $operator, array( 'IN', 'BETWEEN' ) ) ) {
				$operator = '=';
			} elseif ( in_array( $operator, array( 'NOT IN', 'NOT BETWEEN' ) ) ) {
				$operator = '!=';
			}

		}

		$result = sprintf( $format, esc_sql( $field ), $value, $operator );

		return $result;

	}

	/**
	 * Add order arguments to query
	 *
	 * @param array $args [description]
	 */
	public function add_order_args( $order = array() ) {

		$query = '';
		$glue  = '';

		foreach ( $order as $order_clause ) {
			if ( ! empty( $order_clause['orderby'] ) ) {

				// Sanitize SQL `column name` string to prevent SQL injection.
				// See: https://github.com/Crocoblock/issues-tracker/issues/5251
				$orderby = \Jet_Engine_Tools::sanitize_sql_orderby( $order_clause['orderby'] );
				$order   = ! empty( $order_clause['order'] ) ? strtoupper( $order_clause['order'] ) : 'DESC';
				$order   = in_array( $order, array( 'ASC', 'DESC' ) ) ? $order : 'DESC';
				$type    = ! empty( $order_clause['type'] ) ? $order_clause['type'] : false;

				if ( ! $orderby ) {
					continue;
				}

				switch ( $type ) {
					case 'integer':
					case 'float':
					case 'timestamp':
					case 'NUMERIC':
					case 'DECIMAL':
					case 'TIMESTAMP':
						$orderby = sprintf( 'CAST( %s AS DECIMAL )', $orderby );
						break;
					case 'date':
						$orderby = sprintf( 'CAST( %s AS DATE )', $orderby );
						break;

					case 'DATE':
					case 'DATETIME':
					case 'TIME':
					case 'BINARY':
					case 'SIGNED':
					case 'UNSIGNED':
						$orderby = sprintf( 'CAST( %1$s AS %2$s )', $orderby, $type );
						break;
				}

				if ( 'random_order' === $orderby ) {
					$orderby = sprintf( 'RAND(%s)', $this->get_random_seed() );
				}

				$query .= $glue . $orderby . ' ' . $order;
				$glue   = ', ';
			}
		}

		if ( $query ) {
			$query  = " ORDER BY " . $query;
		}

		return $query;

	}

	public function get_random_seed() {

		$transient_key  = 'jet_random_seed';
		$transient_time = 3 * MINUTE_IN_SECONDS;

		$seed = get_transient( $transient_key );

		if ( empty( $seed ) ) {
			$seed = rand();
			set_transient( $transient_key, $seed, $transient_time );
		}

		return apply_filters( 'jet-engine/base-db/random-seed', $seed, $transient_key, $transient_time );
	}

	/**
	 * Clear table data
	 * @return [type] [description]
	 */
	public function clear() {
		$table = $this->table();
		self::wpdb()->query( "TRUNCATE `$table`;" );
	}

	/**
	 * Set current format flag
	 *
	 * @param [type] $flag [description]
	 */
	public function set_format_flag( $flag = null ) {
		$this->format_flag = $flag;
	}

	/**
	 * Get current format flag
	 *
	 */
	public function get_format_flag() {
		if ( $this->format_flag ) {
			return $this->format_flag;
		} else {
			return ARRAY_A;
		}
	}

	/**
	 * Get content type item by ID
	 *
	 * @param  boolean $item_id [description]
	 * @return [type]           [description]
	 */
	public function get_item( $item_id = false, $by = '_ID' ) {

		if ( ! $item_id ) {
			return false;
		}

		if ( ! $by ) {
			$by = '_ID';
		}

		if ( isset( $this->_found_items[ $item_id . $by ] ) ) {
			return $this->_found_items[ $item_id . $by ];
		}

		$result = $this->query( array( $by => $item_id ) );

		if ( empty( $result ) ) {
			return false;
		} else {

			$item = $result[0];

			$this->_found_items[ $item_id . $by ] = $item;

			return $item;
		}

	}

	/**
	 * Query data from db table
	 *
	 * @return [type] [description]
	 */
	public function query( $args = array(), $limit = 0, $offset = 0, $order = array(), $rel = 'AND' ) {
	}

}
